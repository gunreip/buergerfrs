<?php

// app/Console/Commands/SyncTranslationAudits.php

namespace App\Console\Commands;

use App\Models\TranslationKey;
use App\Models\TranslationUsage;
use App\Models\TranslationValue;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

#[Signature('translations:sync-audits')]
#[Description('Sync translation audit JSON files into translation management tables.')]
class SyncTranslationAudits extends Command
{
    private const DEFAULT_LOCALES = ['de', 'en'];

    /**
     * @var array<int, string>
     */
    private array $seenUsageFingerprints = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();

        $langValues = $this->loadLangValues();
        $locales = $this->loadLocales($langValues);

        $seenFingerprints = [];
        $this->seenUsageFingerprints = [];

        $counters = [
            'ok' => 0,
            'missing' => 0,
            'obsolete' => 0,
            'native' => 0,
            'dynamic' => 0,
            'invalid' => 0,
            'values' => 0,
            'usages' => 0,
        ];

        foreach ($this->readList('compare', 'ok') as $entry) {
            $key = $this->syncKeyEntry(
                fingerprint: $this->fingerprint('key:' . $entry['full_key']),
                key: $entry['full_key'],
                status: 'ok',
                classification: 'key',
                suggestedKey: $this->suggestKeyForExistingKey($entry['full_key'] ?? null),
                nativeText: null,
                now: $now,
            );

            $seenFingerprints[] = $key->fingerprint;
            $counters['ok']++;

            foreach ($entry['usages'] ?? [] as $usage) {
                $this->syncUsage($key, $usage, 'key');
                $counters['usages']++;
            }

            foreach ($locales as $locale) {
                $this->syncValue(
                    translationKey: $key,
                    locale: $locale,
                    value: $langValues[$entry['full_key']][$locale] ?? null,
                    status: array_key_exists($locale, $langValues[$entry['full_key']] ?? []) ? 'ok' : 'missing',
                );

                $counters['values']++;
            }
        }

        foreach ($this->readList('compare', 'missing') as $entry) {
            $key = $this->syncKeyEntry(
                fingerprint: $this->fingerprint('key:' . $entry['full_key']),
                key: $entry['full_key'],
                status: 'missing',
                classification: 'key',
                suggestedKey: $this->suggestKeyForExistingKey($entry['full_key'] ?? null),
                nativeText: null,
                now: $now,
            );

            $seenFingerprints[] = $key->fingerprint;
            $counters['missing']++;

            foreach ($entry['usages'] ?? [] as $usage) {
                $this->syncUsage($key, $usage, 'key');
                $counters['usages']++;
            }

            foreach ($locales as $locale) {
                $this->syncValue(
                    translationKey: $key,
                    locale: $locale,
                    value: $langValues[$entry['full_key']][$locale] ?? null,
                    status: array_key_exists($locale, $langValues[$entry['full_key']] ?? []) ? 'ok' : 'missing',
                );

                $counters['values']++;
            }
        }

        foreach ($this->readList('compare', 'obsolete') as $entry) {
            $key = $this->syncKeyEntry(
                fingerprint: $this->fingerprint('key:' . $entry['full_key']),
                key: $entry['full_key'],
                status: 'obsolete',
                classification: 'key',
                suggestedKey: $this->suggestKeyForExistingKey($entry['full_key'] ?? null),
                nativeText: null,
                now: $now,
            );

            $seenFingerprints[] = $key->fingerprint;
            $counters['obsolete']++;

            foreach ($locales as $locale) {
                $this->syncValue(
                    translationKey: $key,
                    locale: $locale,
                    value: $langValues[$entry['full_key']][$locale] ?? null,
                    status: array_key_exists($locale, $langValues[$entry['full_key']] ?? []) ? 'obsolete' : 'missing',
                );

                $counters['values']++;
            }
        }

        foreach ($this->readList('compare', 'native') as $entry) {
            $fingerprint = $this->fingerprint(implode('|', [
                'native',
                $entry['file'] ?? '',
                $entry['line'] ?? '',
                $entry['function'] ?? '',
                $entry['value'] ?? '',
            ]));

            $key = $this->syncKeyEntry(
                fingerprint: $fingerprint,
                key: null,
                status: 'native',
                classification: 'native',
                suggestedKey: $entry['suggested_key'] ?? null,
                nativeText: $entry['value'] ?? null,
                now: $now,
            );

            $seenFingerprints[] = $key->fingerprint;
            $counters['native']++;

            $this->syncUsage($key, $entry, 'native');
            $counters['usages']++;
        }

        foreach ($this->readList('compare', 'dynamic') as $entry) {
            $fingerprint = $this->fingerprint(implode('|', [
                'dynamic',
                $entry['file'] ?? '',
                $entry['line'] ?? '',
                $entry['function'] ?? '',
                $entry['raw'] ?? '',
            ]));

            $key = $this->syncKeyEntry(
                fingerprint: $fingerprint,
                key: null,
                status: 'dynamic',
                classification: 'dynamic',
                suggestedKey: $entry['suggested_key'] ?? null,
                nativeText: $entry['value'] ?? null,
                now: $now,
            );

            $seenFingerprints[] = $key->fingerprint;
            $counters['dynamic']++;

            $this->syncUsage($key, $entry, 'dynamic');
            $counters['usages']++;
        }

        foreach ($this->readInvalidEntries() as $entry) {
            $fingerprint = $this->fingerprint(implode('|', [
                'invalid',
                $entry['scope'] ?? '',
                $entry['file'] ?? '',
                $entry['line'] ?? '',
                $entry['raw'] ?? '',
                $entry['reason'] ?? '',
            ]));

            $key = $this->syncKeyEntry(
                fingerprint: $fingerprint,
                key: $entry['full_key'] ?? null,
                status: 'invalid',
                classification: 'invalid',
                suggestedKey: $this->suggestKeyForExistingKey($entry['full_key'] ?? null),
                nativeText: $entry['value'] ?? null,
                now: $now,
            );

            $seenFingerprints[] = $key->fingerprint;
            $counters['invalid']++;

            if (($entry['file'] ?? null) !== null) {
                $this->syncUsage($key, $entry, 'invalid');
                $counters['usages']++;
            }
        }

        $staleUsageCount = $this->markStaleAuditUsages($this->seenUsageFingerprints, $now);
        $staleCount = $this->markStaleAuditKeys($seenFingerprints, $now);

        $this->components->info('Translation audits synced.');

        $this->table(
            ['Status', 'Count'],
            [
                ['OK keys', $counters['ok']],
                ['Missing keys', $counters['missing']],
                ['Obsolete keys', $counters['obsolete']],
                ['Native entries', $counters['native']],
                ['Dynamic entries', $counters['dynamic']],
                ['Invalid entries', $counters['invalid']],
                ['Values synced', $counters['values']],
                ['Usages synced', $counters['usages']],
                ['Stale audit usages marked', $staleUsageCount],
                ['Stale audit keys marked', $staleCount],
            ],
        );

        return self::SUCCESS;
    }

    private function syncKeyEntry(
        string $fingerprint,
        ?string $key,
        string $status,
        string $classification,
        ?string $suggestedKey,
        ?string $nativeText,
        mixed $now,
    ): TranslationKey {
        $translationKey = TranslationKey::query()->firstOrNew([
            'fingerprint' => $fingerprint,
        ]);

        $wasExisting = $translationKey->exists;
        $oldStatus = $translationKey->status;
        $oldClassification = $translationKey->classification;
        $oldNativeText = $translationKey->native_text;
        $oldObsoleteAt = $translationKey->obsolete_at;

        if (! $translationKey->exists) {
            $translationKey->first_seen_at = $now;
        }

        $resolvedClassification = $this->resolveClassificationForSync(
            currentClassification: $oldClassification,
            incomingClassification: $classification,
            currentNativeText: $oldNativeText,
            incomingNativeText: $nativeText,
        );

        $attributes = [
            'key' => $key,
            'namespace' => $this->namespaceFromKey($key ?? $suggestedKey),
            'group' => $this->groupFromKey($key ?? $suggestedKey),
            'status' => $status,
            'classification' => $resolvedClassification,
            'source' => 'audit',
            'suggested_key' => $suggestedKey,
            'last_seen_at' => $now,
            'obsolete_at' => $status === 'obsolete' ? $now : null,
        ];

        if ($nativeText !== null) {
            $attributes['native_text'] = $nativeText;
        }

        $translationKey->fill($attributes);

        $translationKey->save();

        if ($wasExisting && $oldStatus === 'obsolete' && $status !== 'obsolete') {
            $this->createAuditEvent([
                'translation_key_id' => $translationKey->id,
                'entity_type' => 'translation_key',
                'event_type' => 'reactivated',
                'old_fingerprint' => $translationKey->fingerprint,
                'new_fingerprint' => $translationKey->fingerprint,
                'old_key' => $translationKey->getOriginal('key'),
                'new_key' => $translationKey->key,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'reason' => 'audit_key_seen_again_in_latest_sync',
                'context' => [
                    'old_obsolete_at' => $oldObsoleteAt ? (string) $oldObsoleteAt : null,
                ],
            ]);
        }

        if ($wasExisting && $nativeText !== null && $oldNativeText !== $nativeText) {
            $this->createAuditEvent([
                'translation_key_id' => $translationKey->id,
                'entity_type' => 'translation_key',
                'event_type' => $oldNativeText === null ? 'native_text_filled' : 'native_text_changed',
                'old_fingerprint' => $translationKey->fingerprint,
                'new_fingerprint' => $translationKey->fingerprint,
                'old_key' => $translationKey->key,
                'new_key' => $translationKey->key,
                'old_value' => $oldNativeText,
                'new_value' => $nativeText,
                'old_status' => $oldStatus,
                'new_status' => $translationKey->status,
                'reason' => 'native_text_synced_from_audit_source',
            ]);
        }

        return $translationKey;
    }

    private function resolveClassificationForSync(
        ?string $currentClassification,
        string $incomingClassification,
        ?string $currentNativeText,
        ?string $incomingNativeText,
    ): string {
        if (
            $currentClassification === 'backfill_by_translation'
            && $incomingClassification === 'key'
            && $incomingNativeText === null
            && $currentNativeText !== null
            && trim($currentNativeText) !== ''
        ) {
            return $currentClassification;
        }

        return $incomingClassification;
    }

    private function syncValue(
        TranslationKey $translationKey,
        string $locale,
        ?string $value,
        string $status,
    ): void {
        $translationValue = TranslationValue::query()->firstOrNew([
            'translation_key_id' => $translationKey->id,
            'locale' => $locale,
        ]);

        if ($translationValue->source !== 'manual') {
            $translationValue->fill([
                'value' => $value,
                'status' => $status,
                'source' => 'audit',
            ]);

            $translationValue->save();
        }
    }

    private function syncUsage(TranslationKey $translationKey, array $entry, string $classification): void
    {
        $raw = $entry['raw'] ?? null;
        $file = (string) ($entry['file'] ?? '');
        $line = $entry['line'] ?? null;
        $function = $entry['function'] ?? null;

        $fingerprint = $this->fingerprint(implode('|', [
            $this->usageIdentity($translationKey),
            $file,
            $line ?? '',
            $function ?? '',
        ]));

        $translationUsage = TranslationUsage::query()
            ->where('fingerprint', $fingerprint)
            ->first();

        $eventType = null;
        $oldFingerprint = null;
        $oldFile = null;
        $oldLine = null;
        $oldReason = null;

        if (! $translationUsage) {
            $translationUsage = TranslationUsage::query()
                ->where('translation_key_id', $translationKey->id)
                ->where('classification', $classification)
                ->where('function', $function)
                ->where(function ($query) use ($raw) {
                    if ($raw === null) {
                        $query
                            ->whereNull('raw')
                            ->orWhereNull('original_raw');

                        return;
                    }

                    $query
                        ->where('raw', $raw)
                        ->orWhere('original_raw', $raw);
                })
                ->first();

            if ($translationUsage) {
                $oldFingerprint = $translationUsage->fingerprint;
                $oldFile = $translationUsage->file;
                $oldLine = $translationUsage->line;
                $oldReason = $translationUsage->reason;

                $eventType = ($oldFile !== $file || $oldLine !== $line)
                    ? 'moved'
                    : 'fingerprint_changed';

                $translationUsage->fingerprint = $fingerprint;
            }
        }

        if (! $translationUsage) {
            $translationUsage = new TranslationUsage([
                'fingerprint' => $fingerprint,
            ]);
        }

        if (! $translationUsage->exists || blank($translationUsage->original_raw)) {
            $translationUsage->original_raw = $raw;
        }

        if ($translationUsage->exists && $translationUsage->reason === 'stale_audit_usage_not_seen_in_latest_sync') {
            $eventType ??= 'reactivated';
            $oldFingerprint ??= $translationUsage->fingerprint;
            $oldFile ??= $translationUsage->file;
            $oldLine ??= $translationUsage->line;
            $oldReason ??= $translationUsage->reason;
        }

        $translationUsage->fill([
            'translation_key_id' => $translationKey->id,
            'file' => $file,
            'line' => $line,
            'function' => $function,
            'classification' => $classification,
            'reason' => $entry['reason'] ?? null,
            'raw' => $raw,
        ]);

        $translationUsage->save();

        if ($eventType !== null) {
            $this->createAuditEvent([
                'translation_key_id' => $translationKey->id,
                'translation_usage_id' => $translationUsage->id,
                'entity_type' => 'translation_usage',
                'event_type' => $eventType,
                'old_fingerprint' => $oldFingerprint,
                'new_fingerprint' => $translationUsage->fingerprint,
                'old_file' => $oldFile,
                'new_file' => $translationUsage->file,
                'old_line' => $oldLine,
                'new_line' => $translationUsage->line,
                'old_key' => $translationKey->getOriginal('key'),
                'new_key' => $translationKey->key,
                'old_value' => $oldReason,
                'new_value' => $translationUsage->reason,
                'old_status' => null,
                'new_status' => null,
                'reason' => match ($eventType) {
                    'moved' => 'same_usage_seen_at_different_code_position',
                    'reactivated' => 'stale_usage_seen_again_in_latest_sync',
                    default => 'same_usage_seen_with_different_fingerprint',
                },
                'context' => [
                    'raw' => $raw,
                    'function' => $function,
                    'classification' => $classification,
                ],
            ]);
        }

        $this->seenUsageFingerprints[] = $fingerprint;
    }

    private function usageIdentity(TranslationKey $translationKey): string
    {
        $identity = $translationKey->key
            ?: $translationKey->suggested_key
            ?: $translationKey->fingerprint;

        return (string) $identity;
    }

    private function markStaleAuditUsages(array $seenFingerprints, mixed $now): int
    {
        if ($seenFingerprints === []) {
            return 0;
        }

        $staleUsages = TranslationUsage::query()
            ->whereNotIn('fingerprint', array_values(array_unique($seenFingerprints)), 'and')
            ->where(function ($query) {
                $query
                    ->whereNull('reason')
                    ->orWhere('reason', '!=', 'stale_audit_usage_not_seen_in_latest_sync');
            })
            ->get();

        foreach ($staleUsages as $usage) {
            $this->createAuditEvent([
                'translation_key_id' => $usage->translation_key_id,
                'translation_usage_id' => $usage->id,
                'entity_type' => 'translation_usage',
                'event_type' => 'stale_marked',
                'old_fingerprint' => $usage->fingerprint,
                'new_fingerprint' => $usage->fingerprint,
                'old_file' => $usage->file,
                'new_file' => $usage->file,
                'old_line' => $usage->line,
                'new_line' => $usage->line,
                'old_value' => $usage->reason,
                'new_value' => 'stale_audit_usage_not_seen_in_latest_sync',
                'reason' => 'usage_not_seen_in_latest_audit_sync',
                'context' => [
                    'raw' => $usage->raw,
                    'original_raw' => $usage->original_raw,
                    'function' => $usage->function,
                    'classification' => $usage->classification,
                ],
            ]);

            $usage->forceFill([
                'reason' => 'stale_audit_usage_not_seen_in_latest_sync',
                'updated_at' => $now,
            ])->save();
        }

        return $staleUsages->count();
    }

    private function markStaleAuditKeys(array $seenFingerprints, mixed $now): int
    {
        if ($seenFingerprints === []) {
            return 0;
        }

        $staleKeys = TranslationKey::query()
            ->where('source', 'audit')
            ->whereNotIn('fingerprint', array_values(array_unique($seenFingerprints)), 'and')
            ->where(function ($query) {
                $query
                    ->where('status', '!=', 'obsolete')
                    ->orWhereNull('obsolete_at');
            })
            ->get();

        foreach ($staleKeys as $key) {
            $this->createAuditEvent([
                'translation_key_id' => $key->id,
                'entity_type' => 'translation_key',
                'event_type' => 'stale_marked',
                'old_fingerprint' => $key->fingerprint,
                'new_fingerprint' => $key->fingerprint,
                'old_key' => $key->key,
                'new_key' => $key->key,
                'old_value' => $key->native_text,
                'new_value' => $key->native_text,
                'old_status' => $key->status,
                'new_status' => 'obsolete',
                'reason' => 'key_not_seen_in_latest_audit_sync',
                'context' => [
                    'classification' => $key->classification,
                    'suggested_key' => $key->suggested_key,
                    'last_seen_at' => $key->last_seen_at ? (string) $key->last_seen_at : null,
                ],
            ]);

            $key->forceFill([
                'status' => 'obsolete',
                'obsolete_at' => $now,
                'updated_at' => $now,
            ])->save();
        }

        return $staleKeys->count();
    }

    private function createAuditEvent(array $payload): void
    {
        DB::table('translation_audit_events')->insert([
            'translation_key_id' => $payload['translation_key_id'] ?? null,
            'translation_usage_id' => $payload['translation_usage_id'] ?? null,
            'entity_type' => $payload['entity_type'],
            'event_type' => $payload['event_type'],
            'old_fingerprint' => $payload['old_fingerprint'] ?? null,
            'new_fingerprint' => $payload['new_fingerprint'] ?? null,
            'old_file' => $payload['old_file'] ?? null,
            'new_file' => $payload['new_file'] ?? null,
            'old_line' => $payload['old_line'] ?? null,
            'new_line' => $payload['new_line'] ?? null,
            'old_key' => $payload['old_key'] ?? null,
            'new_key' => $payload['new_key'] ?? null,
            'old_value' => $payload['old_value'] ?? null,
            'new_value' => $payload['new_value'] ?? null,
            'old_status' => $payload['old_status'] ?? null,
            'new_status' => $payload['new_status'] ?? null,
            'reason' => $payload['reason'] ?? null,
            'context' => isset($payload['context'])
                ? json_encode($payload['context'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readList(string $section, string $name): array
    {
        $path = storage_path('audits/translations/' . $section . '/' . $name . '.json');

        if (! File::isFile($path)) {
            return [];
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload) || ! array_is_list($payload)) {
            return [];
        }

        return $payload;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readInvalidEntries(): array
    {
        $path = storage_path('audits/translations/compare/invalid.json');

        if (! File::isFile($path)) {
            return [];
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            return [];
        }

        $entries = [];

        foreach (($payload['code'] ?? []) as $entry) {
            if (is_array($entry)) {
                $entry['scope'] = 'code';
                $entries[] = $entry;
            }
        }

        foreach (($payload['lang'] ?? []) as $entry) {
            if (is_array($entry)) {
                $entry['scope'] = 'lang';
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    /**
     * @return array<string, array<string, string|null>>
     */
    private function loadLangValues(): array
    {
        $values = [];

        foreach ($this->readList('lang', 'keys') as $entry) {
            $fullKey = (string) ($entry['full_key'] ?? '');
            $locale = (string) ($entry['locale'] ?? '');
            $file = (string) ($entry['file'] ?? '');
            $key = (string) ($entry['key'] ?? '');

            if ($fullKey === '' || $locale === '' || $file === '' || $key === '') {
                continue;
            }

            $absolutePath = base_path($file);

            if (! File::isFile($absolutePath)) {
                continue;
            }

            $payload = require $absolutePath;

            if (! is_array($payload)) {
                continue;
            }

            $value = Arr::get($payload, $key);

            if (is_array($value)) {
                continue;
            }

            $values[$fullKey][$locale] = $value === null ? null : (string) $value;
        }

        return $values;
    }

    /**
     * @param  array<string, array<string, string|null>>  $langValues
     * @return array<int, string>
     */
    private function loadLocales(array $langValues): array
    {
        $locales = [];

        $localeSummary = $this->readList('lang', 'locales');

        foreach ($localeSummary as $entry) {
            if (($entry['locale'] ?? null) !== null) {
                $locales[] = (string) $entry['locale'];
            }
        }

        foreach ($langValues as $valuesByLocale) {
            foreach (array_keys($valuesByLocale) as $locale) {
                $locales[] = $locale;
            }
        }

        $locales = array_values(array_unique($locales));
        sort($locales);

        return $locales === [] ? self::DEFAULT_LOCALES : $locales;
    }

    private function suggestKeyForExistingKey(?string $key): ?string
    {
        if ($key === null) {
            return null;
        }

        $suggestedKey = str($key)
            ->trim()
            ->replace('\\', '.')
            ->replace('/', '.')
            ->replace('-', '_')
            ->replaceMatches('/(?<!^)[A-Z]/', '_$0')
            ->lower()
            ->replaceMatches('/[^a-z0-9_.]+/', '_')
            ->replaceMatches('/_+/', '_')
            ->replaceMatches('/\.+/', '.')
            ->replaceMatches('/(^|\\.)_+/', '$1')
            ->replaceMatches('/_+(\\.|$)/', '$1')
            ->trim('._')
            ->toString();

        return $suggestedKey !== '' ? $suggestedKey : null;
    }

    private function namespaceFromKey(?string $key): ?string
    {
        if ($key === null || ! str_contains($key, '.')) {
            return $key;
        }

        return str($key)->before('.')->toString();
    }

    private function groupFromKey(?string $key): ?string
    {
        if ($key === null || ! str_contains($key, '.')) {
            return null;
        }

        $segments = explode('.', $key);

        return count($segments) >= 2 ? $segments[1] : null;
    }

    private function fingerprint(string $value): string
    {
        return hash('sha256', $value);
    }
}
