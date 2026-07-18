<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Scanner\TranslationFingerprintFactory;
use Gunreip\TranslationWorkbench\Scanner\TranslationKeyPartsFactory;
use Gunreip\TranslationWorkbench\Support\TranslationKeySegmentFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RuntimeDynamicTranslationCollector
{
    public function __construct(
        private readonly TranslationFingerprintFactory $fingerprintFactory,
        private readonly TranslationKeyPartsFactory $keyPartsFactory,
        private readonly TranslationKeySegmentFactory $keySegmentFactory,
    ) {}

    /**
     * Observe runtime option values without making review decisions.
     *
     * This collector is intentionally append/update oriented:
     * - new values become active,
     * - currently missing values are marked stale, not deleted,
     * - review_status stays pending unless already reviewed elsewhere.
     *
     * @param  iterable<string|int, mixed>  $values
     */
    public function options(
        string $key,
        string $scope,
        iterable $values,
        string $source,
        string $origin = 'runtime',
        string $sourceType = 'runtime_options',
    ): void {
        if (! $this->enabled() || trim($key) === '' || trim($scope) === '') {
            return;
        }

        $values = $this->normalizeValues($values);

        if ($values === []) {
            return;
        }

        $sourceId = $this->syncDynamicSource(
            key: $key,
            scope: $scope,
            valuesCount: count($values),
            source: $source,
            origin: $origin,
            sourceType: $sourceType,
        );

        if ($sourceId === null) {
            return;
        }

        $this->syncDynamicSourceValues($sourceId, $values, $origin);
        $this->syncDynamicSourceCandidate($sourceId, $key, $scope, $values, $source, $origin, $sourceType);
    }

    private function enabled(): bool
    {
        return (bool) config('translation-workbench.runtime_collector.enabled', false)
            && Schema::hasTable('translation_workbench_dynamic_sources')
            && Schema::hasTable('translation_workbench_dynamic_source_values')
            && Schema::hasTable('translation_workbench_dynamic_source_candidates');
    }

    /**
     * @param  iterable<string|int, mixed>  $values
     * @return array<string, string>
     */
    private function normalizeValues(iterable $values): array
    {
        return collect($values)
            ->mapWithKeys(function (mixed $value, mixed $key): array {
                $label = match (true) {
                    is_string($value) => $value,
                    is_array($value) && is_string($value['label'] ?? null) => (string) $value['label'],
                    is_object($value) && isset($value->label) && is_string($value->label) => (string) $value->label,
                    default => null,
                };

                $key = trim((string) $key);
                $label = trim((string) $label);

                return $key !== '' && $label !== '' ? [$key => $label] : [];
            })
            ->all();
    }

    private function syncDynamicSource(
        string $key,
        string $scope,
        int $valuesCount,
        string $source,
        string $origin,
        string $sourceType,
    ): ?int {
        $keyId = $this->resolveOrCreateKeyId($key);
        $fingerprint = $this->fingerprintFactory->signature([
            'runtime-dynamic-source',
            $key,
            $scope,
            $source,
        ]);
        $classificationOrigin = $origin !== '' ? $origin : 'runtime';
        $classification = 'multi_' . $classificationOrigin;
        $attributes = [
            'key_id' => $keyId,
            'finding_id' => null,
            'option_discovery_id' => null,
            'classification' => $classification,
            'cardinality' => 'multi',
            'origin' => $classificationOrigin,
            'source_type' => $sourceType,
            'source_reference' => $source,
            'source_path' => null,
            'source_line' => null,
            'source_expression' => null,
            'dynamic_scope' => $scope,
            'values_count' => $valuesCount,
            'confidence' => 'runtime',
            'status' => 'active',
            'last_seen_at' => now(),
            'meta' => json_encode([
                'source' => 'translation-workbench:runtime-collector',
                'translation_key' => $key,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];

        $sourceRow = DB::table('translation_workbench_dynamic_sources')
            ->where('fingerprint', $fingerprint)
            ->first();

        if (! $sourceRow) {
            return (int) DB::table('translation_workbench_dynamic_sources')->insertGetId([
                ...$attributes,
                'fingerprint' => $fingerprint,
                'first_seen_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $changedValues = collect($attributes)
            ->filter(static fn(mixed $value, string $attribute): bool => ($sourceRow->{$attribute} ?? null) != $value)
            ->all();

        if ($changedValues !== []) {
            DB::table('translation_workbench_dynamic_sources')
                ->where('id', $sourceRow->id)
                ->update([
                    ...$changedValues,
                    'updated_at' => now(),
                ]);
        }

        return (int) $sourceRow->id;
    }

    private function resolveOrCreateKeyId(string $key): ?int
    {
        if (! Schema::hasTable('translation_workbench_keys')) {
            return null;
        }

        $keyRow = DB::table('translation_workbench_keys')
            ->where('translation_key', $key)
            ->orWhere('suggested_key', $key)
            ->orderByRaw('CASE WHEN translation_key = ? THEN 0 ELSE 1 END', [$key])
            ->first(['id']);

        if ($keyRow !== null) {
            return (int) $keyRow->id;
        }

        $keyParts = $this->keyPartsFactory->fromKey($key);
        $keySegments = $this->keySegmentFactory->fromKey($key);
        $fingerprint = $this->fingerprintFactory->signature([
            'foundation-key',
            '',
            $key,
        ]);

        return (int) DB::table('translation_workbench_keys')->insertGetId([
            'fingerprint' => $fingerprint,
            'translation_key' => null,
            'suggested_key' => $key,
            'namespace' => $keyParts['namespace'],
            'group' => $keyParts['group'],
            'path_key' => $keyParts['path_key'],
            'scope' => $keyParts['scope'],
            ...$keySegments,
            'key_type' => 'dynamic_candidate',
            'is_ui_key' => false,
            'is_dynamic_key' => true,
            'is_dynamic_multi' => true,
            'status' => 'open',
            'review_status' => 'pending',
            'dynamic_data_state' => 'structured',
            'meta' => json_encode([
                'source' => 'translation-workbench:runtime-collector',
                'candidate_type' => 'runtime_options',
                'candidate_reason' => 'runtime option values were observed for this key',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @param  array<string, string>  $values
     */
    private function syncDynamicSourceValues(int $sourceId, array $values, string $origin): void
    {
        $seenKeys = array_keys($values);

        foreach ($values as $valueKey => $label) {
            $existing = DB::table('translation_workbench_dynamic_source_values')
                ->where('dynamic_source_id', $sourceId)
                ->where('value_key', $valueKey)
                ->first();
            $attributes = [
                'native_label' => $label,
                'origin' => $origin,
                'status' => 'active',
            ];

            if (! $existing) {
                DB::table('translation_workbench_dynamic_source_values')->insert([
                    'dynamic_source_id' => $sourceId,
                    'value_key' => $valueKey,
                    ...$attributes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                continue;
            }

            $changedValues = collect($attributes)
                ->filter(static fn(mixed $value, string $attribute): bool => ($existing->{$attribute} ?? null) != $value)
                ->all();

            if ($changedValues !== []) {
                DB::table('translation_workbench_dynamic_source_values')
                    ->where('id', $existing->id)
                    ->update([
                        ...$changedValues,
                        'updated_at' => now(),
                    ]);
            }
        }

        DB::table('translation_workbench_dynamic_source_values')
            ->where('dynamic_source_id', $sourceId)
            ->whereNotIn('value_key', $seenKeys)
            ->where('status', 'active')
            ->update([
                'status' => 'stale',
                'updated_at' => now(),
            ]);
    }

    /**
     * @param  array<string, string>  $values
     */
    private function syncDynamicSourceCandidate(
        int $sourceId,
        string $key,
        string $scope,
        array $values,
        string $source,
        string $origin,
        string $sourceType,
    ): void {
        $attributes = [
            'key_id' => DB::table('translation_workbench_dynamic_sources')->where('id', $sourceId)->value('key_id'),
            'finding_id' => null,
            'suggested_key' => $key,
            'dynamic_scope' => $scope,
            'source_expression' => null,
            'candidate_source_type' => 'runtime_options',
            'candidate_reference' => $source,
            'candidate_values_count' => count($values),
            'candidate_values' => json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'confidence' => 'runtime',
            'review_status' => 'pending',
            'status' => 'active',
            'meta' => json_encode([
                'source' => 'translation-workbench:runtime-collector',
                'origin' => $origin,
                'source_type' => $sourceType,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
        $existing = DB::table('translation_workbench_dynamic_source_candidates')
            ->where('dynamic_source_id', $sourceId)
            ->where('candidate_source_type', 'runtime_options')
            ->where('candidate_reference', $source)
            ->first();

        if (! $existing) {
            DB::table('translation_workbench_dynamic_source_candidates')->insert([
                'dynamic_source_id' => $sourceId,
                ...$attributes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return;
        }

        $changedValues = collect($attributes)
            ->filter(static fn(mixed $value, string $attribute): bool => ($existing->{$attribute} ?? null) != $value)
            ->all();

        if ($changedValues !== []) {
            DB::table('translation_workbench_dynamic_source_candidates')
                ->where('id', $existing->id)
                ->update([
                    ...$changedValues,
                    'updated_at' => now(),
                ]);
        }
    }
}
