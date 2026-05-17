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
use Illuminate\Support\Facades\File;

#[Signature('translations:sync-audits')]
#[Description('Sync translation audit JSON files into translation management tables.')]
class SyncTranslationAudits extends Command
{
    private const DEFAULT_LOCALES = ['de', 'en'];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();

        $langValues = $this->loadLangValues();
        $locales = $this->loadLocales($langValues);

        $seenFingerprints = [];

        TranslationUsage::query()->delete();

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
                suggestedKey: null,
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
                suggestedKey: null,
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
                suggestedKey: null,
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
                suggestedKey: null,
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

        $staleCount = $this->pruneStaleAuditKeys($seenFingerprints);

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
                ['Stale audit keys pruned', $staleCount],
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

        if (! $translationKey->exists) {
            $translationKey->first_seen_at = $now;
        }

        $translationKey->fill([
            'key' => $key,
            'namespace' => $this->namespaceFromKey($key ?? $suggestedKey),
            'group' => $this->groupFromKey($key ?? $suggestedKey),
            'status' => $status,
            'classification' => $classification,
            'source' => 'audit',
            'suggested_key' => $suggestedKey,
            'native_text' => $nativeText,
            'last_seen_at' => $now,
            'obsolete_at' => $status === 'obsolete' ? $now : null,
        ]);

        $translationKey->save();

        return $translationKey;
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
        $fingerprint = $this->fingerprint(implode('|', [
            $translationKey->fingerprint,
            $entry['file'] ?? '',
            $entry['line'] ?? '',
            $entry['function'] ?? '',
            $entry['raw'] ?? '',
            $classification,
        ]));

        TranslationUsage::query()->updateOrCreate(
            ['fingerprint' => $fingerprint],
            [
                'translation_key_id' => $translationKey->id,
                'file' => (string) ($entry['file'] ?? ''),
                'line' => $entry['line'] ?? null,
                'function' => $entry['function'] ?? null,
                'classification' => $classification,
                'reason' => $entry['reason'] ?? null,
                'raw' => $entry['raw'] ?? null,
            ],
        );
    }

    private function pruneStaleAuditKeys(array $seenFingerprints): int
    {
        if ($seenFingerprints === []) {
            return 0;
        }

        return TranslationKey::query()
            ->where('source', 'audit')
            ->whereNotIn('fingerprint', array_values(array_unique($seenFingerprints)))
            ->delete();
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
