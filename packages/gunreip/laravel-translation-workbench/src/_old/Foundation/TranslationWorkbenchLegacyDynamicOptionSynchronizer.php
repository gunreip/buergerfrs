<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use App\Support\Locale\LocaleCode;
use Closure;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchDynamicValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchEntry;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchEvent;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchValue;
use Gunreip\TranslationWorkbench\Scanner\SuggestedKeyFactory;
use Gunreip\TranslationWorkbench\Scanner\TranslationFindingNormalizer;
use Gunreip\TranslationWorkbench\Scanner\TranslationFingerprintFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/** Existing persistence boundary, retained explicitly until Foundation migration. */
class TranslationWorkbenchLegacyDynamicOptionSynchronizer
{
    private bool $databaseWarningShown = false;

    public function __construct(
        private readonly SuggestedKeyFactory $suggestedKeyFactory,
        private readonly TranslationFingerprintFactory $fingerprintFactory,
        private readonly TranslationFindingNormalizer $findingNormalizer,
    ) {}

    public function process(Collection $discoveries, bool $sync, string $sourceLocale, ?Closure $warning = null): array
    {
        $this->databaseWarningShown = false;
        $summary = [
            'discoveries' => $discoveries->count(),
            'matched_entries' => 0,
            'unmatched_entries' => 0,
            'options_found' => 0,
            'options_created' => 0,
            'options_updated' => 0,
            'options_unchanged' => 0,
            'dynamic_values_created' => 0,
            'dynamic_values_updated' => 0,
            'dynamic_values_unchanged' => 0,
            'dynamic_values_skipped_missing_table' => 0,
            'options_unresolved' => 0,
            'entry_discovery_created' => 0,
            'entry_discovery_updated' => 0,
            'entry_discovery_unchanged' => 0,
            'workbench_entries_created' => 0,
            'option_discoveries_created' => 0,
            'option_discoveries_updated' => 0,
            'option_discoveries_unchanged' => 0,
            'untranslated_option_labels' => 0,
            'suggested_equal' => 0,
            'suggested_different' => 0,
            'suggested_missing_entry' => 0,
        ];

        $runner = function () use ($discoveries, $sync, $sourceLocale, $warning, &$summary): void {
            foreach ($discoveries as $discovery) {
                $entry = $this->matchingEntry($discovery, $warning);
                $options = $discovery['options'];

                if ($sync && ! $entry) {
                    $entry = $this->ensureWorkbenchEntry($discovery);
                    $summary['workbench_entries_created']++;
                }

                $suggestedState = $this->suggestedState($discovery, $entry);
                $summary[$entry ? 'matched_entries' : 'unmatched_entries']++;

                if ($options === []) {
                    $summary['options_unresolved']++;
                }

                $summary['options_found'] += count($options);
                $summary[$suggestedState['summary_key']]++;
                $summary['untranslated_option_labels'] += $discovery['label_usage'] === 'plain_label' ? 1 : 0;

                if ($sync) {
                    $summary[$this->syncOptionDiscovery($discovery, $entry, $suggestedState['label'])]++;

                    if (! $entry) {
                        continue;
                    }

                    $summary[$this->syncEntryDiscovery($entry, $discovery, $suggestedState['label'])]++;

                    if ($options !== []) {
                        foreach ($options as $valueKey => $nativeLabel) {
                            $summary[$this->syncOption($entry, $discovery, $valueKey, $nativeLabel)]++;
                            $summary[$this->syncDynamicSourceValue($entry, $discovery, $valueKey, $nativeLabel, $sourceLocale)]++;
                        }
                    }
                }

            }
        };

        $sync ? DB::transaction($runner) : $runner();

        return $summary;
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>}  $discovery
     */
    private function matchingEntry(array $discovery, ?Closure $warning): ?TranslationWorkbenchEntry
    {
        $scope = $discovery['scope'];
        $suggestedKey = $discovery['suggested_key'];

        try {
            return TranslationWorkbenchEntry::query()
                ->where(function ($query) use ($suggestedKey, $scope): void {
                    $query
                        ->where('suggested_key', $suggestedKey)
                        ->orWhere('translation_key', $suggestedKey)
                        ->orWhere('meta->dynamic_scope', $scope);
                })
                ->orderByRaw(
                    'CASE WHEN suggested_key = ? THEN 0 WHEN translation_key = ? THEN 1 ELSE 2 END',
                    [$suggestedKey, $suggestedKey],
                )
                ->orderBy('id')
                ->first();
        } catch (Throwable $exception) {
            if (! $this->databaseWarningShown) {
                if ($warning !== null) {
                    $warning($exception);
                }

                $this->databaseWarningShown = true;
            }

            return null;
        }
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>, source_reference?: string}  $discovery
     */
    private function ensureWorkbenchEntry(array $discovery): TranslationWorkbenchEntry
    {
        $suggestedKey = trim($discovery['suggested_key']);
        $keyResult = $this->suggestedKeyFactory->forExistingKey($suggestedKey);
        $sourceExpression = '@foreach ($'.$discovery['options_variable'].' as $'.$discovery['key_variable'].' => $'.$discovery['label_variable'].')';
        $sourceSignature = $this->fingerprintFactory->signature([
            $discovery['source_path'],
            (string) $discovery['source_line'],
            'dynamic_option_discovery',
            $suggestedKey,
            $sourceExpression,
        ]);
        $fingerprint = $this->fingerprintFactory->signature([
            'dynamic_option_discovery',
            $suggestedKey,
        ]);
        $attributes = [
            'fingerprint' => $fingerprint,
            'source_signature' => $sourceSignature,
            'source_fingerprint' => $this->fingerprintFactory->sourceFingerprint(
                $discovery['source_path'],
                $discovery['source_line'],
                $sourceExpression,
            ),
            'expression_fingerprint' => $this->fingerprintFactory->expressionFingerprint($sourceExpression),
            'semantic_fingerprint' => $this->fingerprintFactory->signature([
                'dynamic_multi',
                $suggestedKey,
            ]),
            'kind' => 'dynamic_multi',
            'entry_type' => 'dynamic',
            'candidate_type' => 'dynamic',
            'candidate_reason' => 'dynamic_option_discovery',
            'source_type' => str_ends_with($discovery['source_path'], '.blade.php') ? 'blade' : 'php',
            'source_path' => $discovery['source_path'],
            'source_line' => $discovery['source_line'],
            'function_name' => 'dynamic_option_discovery',
            'raw_expression' => $sourceExpression,
            'literal_text' => null,
            'literal_text_suggested' => $this->findingNormalizer->literalTextSuggestedFromScope($discovery['scope']),
            'translation_key' => null,
            'translation_key_source' => null,
            'existing_key' => null,
            'suggested_key' => $suggestedKey,
            'namespace' => $keyResult->namespace,
            'group' => $keyResult->group,
            'meta' => [
                'scanner' => 'dynamic_option_discovery',
                'dynamic_scope' => $discovery['scope'],
                'suggested_dynamic_key' => $discovery['suggested_dynamic_key'],
                'options_variable' => $discovery['options_variable'],
                'key_variable' => $discovery['key_variable'],
                'label_variable' => $discovery['label_variable'],
                'label_usage' => $discovery['label_usage'],
                'source_type' => $discovery['source_type'],
            ],
            'status' => 'open',
            'review_status' => 'pending',
            'first_seen_at' => now(),
            'last_seen_at' => now(),
            'scan_count' => 1,
        ];

        $entry = TranslationWorkbenchEntry::query()
            ->where('fingerprint', $fingerprint)
            ->first();

        if ($entry) {
            return $entry;
        }

        $entry = TranslationWorkbenchEntry::query()->create($attributes);

        $this->recordEvent($entry, 'dynamic_option_entry_created', null, $attributes);

        return $entry;
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>, source_reference?: string}  $discovery
     */
    private function syncOption(TranslationWorkbenchEntry $entry, array $discovery, string $valueKey, string $nativeLabel): string
    {
        $attributes = [
            'native_label' => $nativeLabel,
            'source_type' => $discovery['source_type'],
            'source_reference' => (string) ($discovery['source_reference'] ?? $discovery['source_path'].':'.$discovery['source_line']),
            'status' => 'open',
            'last_seen_at' => now(),
            'meta' => [
                'source' => 'translation-workbench:discover-dynamic-options',
                'scope' => $discovery['scope'],
                'suggested_key' => $discovery['suggested_key'],
                'suggested_dynamic_key' => $discovery['suggested_dynamic_key'],
                'options_variable' => $discovery['options_variable'],
                'label_usage' => $discovery['label_usage'],
            ],
        ];
        $value = TranslationWorkbenchValue::query()
            ->where('entry_id', $entry->id)
            ->where('value_key', $valueKey)
            ->first();

        if (! $value) {
            $value = TranslationWorkbenchValue::query()->create([
                'entry_id' => $entry->id,
                'value_key' => $valueKey,
                ...$attributes,
                'first_seen_at' => now(),
            ]);

            $this->recordEvent($entry, 'dynamic_option_discovered', null, [
                'value_id' => $value->id,
                'value_key' => $valueKey,
                ...$attributes,
            ]);

            return 'options_created';
        }

        $oldValues = $value->only(array_keys($attributes));
        $changed = collect($attributes)
            ->filter(static fn (mixed $attribute, string $key): bool => ($oldValues[$key] ?? null) !== $attribute)
            ->all();

        if ($changed === []) {
            return 'options_unchanged';
        }

        $value->forceFill($attributes)->save();

        $this->recordEvent($entry, 'dynamic_option_updated', $oldValues, $attributes, [
            'value_id' => $value->id,
            'value_key' => $valueKey,
        ]);

        return 'options_updated';
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>, source_reference?: string}  $discovery
     */
    private function syncDynamicSourceValue(TranslationWorkbenchEntry $entry, array $discovery, string $valueKey, string $nativeLabel, string $sourceLocale): string
    {
        if (! Schema::hasTable('translation_workbench_dynamic_values')) {
            return 'dynamic_values_skipped_missing_table';
        }

        $locale = LocaleCode::normalize($sourceLocale);
        $locale = $locale !== '' ? $locale : 'en';
        $attributes = [
            'value' => $nativeLabel,
            'status' => trim($nativeLabel) !== '' ? 'ok' : 'missing',
            'source' => 'dynamic_option_discovery',
            'reviewed_at' => trim($nativeLabel) !== '' ? now() : null,
            'reviewed_by_user_id' => trim($nativeLabel) !== '' ? auth()->id() : null,
            'meta' => [
                'source' => 'translation-workbench:discover-dynamic-options',
                'scope' => $discovery['scope'],
                'suggested_key' => $discovery['suggested_key'],
                'suggested_dynamic_key' => $discovery['suggested_dynamic_key'],
                'options_variable' => $discovery['options_variable'],
                'label_usage' => $discovery['label_usage'],
                'source_type' => $discovery['source_type'],
                'source_reference' => (string) ($discovery['source_reference'] ?? $discovery['source_path'].':'.$discovery['source_line']),
            ],
        ];
        $dynamicValue = TranslationWorkbenchDynamicValue::query()
            ->where('entry_id', $entry->id)
            ->where('value_key', $valueKey)
            ->where('locale', $locale)
            ->first();

        if (! $dynamicValue) {
            $dynamicValue = TranslationWorkbenchDynamicValue::query()->create([
                'entry_id' => $entry->id,
                'value_key' => $valueKey,
                'locale' => $locale,
                ...$attributes,
            ]);

            $this->recordEvent($entry, 'dynamic_source_value_discovered', null, [
                'dynamic_value_id' => $dynamicValue->id,
                'value_key' => $valueKey,
                'locale' => $locale,
                ...$attributes,
            ]);

            return 'dynamic_values_created';
        }

        $oldValues = $dynamicValue->only(array_keys($attributes));
        $changed = collect($attributes)
            ->filter(static fn (mixed $attribute, string $key): bool => ($oldValues[$key] ?? null) !== $attribute)
            ->all();

        if ($changed === []) {
            return 'dynamic_values_unchanged';
        }

        $dynamicValue->forceFill($attributes)->save();

        $this->recordEvent($entry, 'dynamic_source_value_updated', $oldValues, [
            'dynamic_value_id' => $dynamicValue->id,
            'value_key' => $valueKey,
            'locale' => $locale,
            ...$attributes,
        ]);

        return 'dynamic_values_updated';
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>, source_reference?: string}  $discovery
     */
    private function syncOptionDiscovery(array $discovery, ?TranslationWorkbenchEntry $entry, string $suggestedState): string
    {
        $fingerprint = $this->optionDiscoveryFingerprint($discovery);
        $attributes = [
            'matched_entry_id' => $entry?->id,
            'scope' => $discovery['scope'],
            'suggested_key' => $discovery['suggested_key'],
            'suggested_dynamic_key' => $discovery['suggested_dynamic_key'],
            'workbench_suggested_key' => $entry?->suggested_key,
            'suggested_state' => $suggestedState,
            'source_path' => $discovery['source_path'],
            'source_line' => $discovery['source_line'],
            'options_variable' => $discovery['options_variable'],
            'key_variable' => $discovery['key_variable'],
            'label_variable' => $discovery['label_variable'],
            'label_usage' => $discovery['label_usage'],
            'source_type' => $discovery['source_type'],
            'source_reference' => (string) ($discovery['source_reference'] ?? $discovery['source_path'].':'.$discovery['source_line']),
            'options_count' => count($discovery['options']),
            'status' => 'open',
            'last_seen_at' => now(),
            'options' => $discovery['options'],
            'meta' => [
                'source' => 'translation-workbench:discover-dynamic-options',
                'matched_entry_translation_key' => $entry?->translation_key,
                'matched_entry_status' => $entry?->status,
            ],
        ];
        $optionDiscovery = TranslationWorkbenchOptionDiscovery::query()
            ->where('fingerprint', $fingerprint)
            ->first();

        if (! $optionDiscovery) {
            TranslationWorkbenchOptionDiscovery::query()->create([
                'fingerprint' => $fingerprint,
                ...$attributes,
                'first_seen_at' => now(),
            ]);

            return 'option_discoveries_created';
        }

        $oldValues = $optionDiscovery->only(array_keys($attributes));
        $compareOldValues = $oldValues;
        $compareNewValues = $attributes;
        unset($compareOldValues['last_seen_at'], $compareNewValues['last_seen_at']);
        $changed = collect($compareNewValues)
            ->filter(static fn (mixed $attribute, string $key): bool => ($compareOldValues[$key] ?? null) !== $attribute)
            ->all();

        if ($changed === []) {
            $optionDiscovery->forceFill(['last_seen_at' => now()])->save();

            return 'option_discoveries_unchanged';
        }

        $optionDiscovery->forceFill($attributes)->save();

        return 'option_discoveries_updated';
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>, source_reference?: string}  $discovery
     */
    private function syncEntryDiscovery(TranslationWorkbenchEntry $entry, array $discovery, string $suggestedState): string
    {
        $oldMeta = is_array($entry->meta) ? $entry->meta : [];
        $oldDiscovery = $oldMeta['dynamic_option_discovery'] ?? null;
        $newDiscovery = [
            'scope' => $discovery['scope'],
            'suggested_key' => $discovery['suggested_key'],
            'suggested_dynamic_key' => $discovery['suggested_dynamic_key'],
            'source_path' => $discovery['source_path'],
            'source_line' => $discovery['source_line'],
            'options_variable' => $discovery['options_variable'],
            'key_variable' => $discovery['key_variable'],
            'label_variable' => $discovery['label_variable'],
            'label_usage' => $discovery['label_usage'],
            'source_type' => $discovery['source_type'],
            'source_reference' => (string) ($discovery['source_reference'] ?? $discovery['source_path'].':'.$discovery['source_line']),
            'options_count' => count($discovery['options']),
            'suggested_state' => $suggestedState,
            'synced_at' => now()->toISOString(),
        ];

        $compareOldDiscovery = $oldDiscovery;
        unset($compareOldDiscovery['synced_at']);
        $compareNewDiscovery = $newDiscovery;
        unset($compareNewDiscovery['synced_at']);

        if ($compareOldDiscovery === $compareNewDiscovery) {
            return 'entry_discovery_unchanged';
        }

        $newMeta = [
            ...$oldMeta,
            'dynamic_option_discovery' => $newDiscovery,
        ];

        $entry->forceFill(['meta' => $newMeta])->save();

        $this->recordEvent(
            $entry,
            $oldDiscovery === null ? 'dynamic_option_discovery_created' : 'dynamic_option_discovery_updated',
            ['dynamic_option_discovery' => $oldDiscovery],
            ['dynamic_option_discovery' => $newDiscovery],
        );

        return $oldDiscovery === null
            ? 'entry_discovery_created'
            : 'entry_discovery_updated';
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>}  $discovery
     * @return array{label: string, summary_key: string}
     */
    private function suggestedState(array $discovery, ?TranslationWorkbenchEntry $entry): array
    {
        if (! $entry) {
            return [
                'label' => 'no_workbench_entry',
                'summary_key' => 'suggested_missing_entry',
            ];
        }

        $workbenchSuggestedKey = trim((string) $entry->suggested_key);

        if ($workbenchSuggestedKey === $discovery['suggested_key']) {
            return [
                'label' => 'suggested_equal',
                'summary_key' => 'suggested_equal',
            ];
        }

        return [
            'label' => $workbenchSuggestedKey === '' ? 'workbench_suggested_missing' : 'suggested_different',
            'summary_key' => 'suggested_different',
        ];
    }

    private function recordEvent(
        TranslationWorkbenchEntry $entry,
        string $eventType,
        ?array $oldValues,
        array $newValues,
        array $context = [],
    ): void {
        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => $eventType,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'context' => [
                'source' => 'translation-workbench:discover-dynamic-options',
                ...$context,
            ],
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>, source_reference?: string}  $discovery
     */
    private function optionDiscoveryFingerprint(array $discovery): string
    {
        return $this->fingerprintFactory->signature([
            $discovery['scope'],
            $discovery['suggested_key'],
            $discovery['suggested_dynamic_key'],
            $discovery['source_path'],
            (string) $discovery['source_line'],
            $discovery['options_variable'],
            $discovery['key_variable'],
            $discovery['label_variable'],
        ]);
    }
}
