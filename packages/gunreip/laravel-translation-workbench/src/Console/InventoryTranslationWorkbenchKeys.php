<?php

// packages/gunreip/laravel-translation-workbench/src/Console/InventoryTranslationWorkbenchKeys.php

// php artisan translation-workbench:inventory-keys
// php artisan translation-workbench:inventory-keys --dry-run
// php artisan translation-workbench:inventory-keys --sync
// php artisan translation-workbench:inventory-keys --sync --source-locale=en

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyInventory;
use Gunreip\TranslationWorkbench\Scanner\TranslationKeyPartsFactory;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Signature('translation-workbench:inventory-keys
    {--sync : Write the current established-key inventory into the database.}
    {--dry-run : Report only; do not write inventory database rows.}
    {--source-locale=en : Source locale used to split source and target lang values.}')]
#[Description('Build an inventory of established Translation Workbench keys and their code/value usage counts.')]
class InventoryTranslationWorkbenchKeys extends Command
{
    use WritesTranslationWorkbenchReports;

    public function handle(TranslationKeyPartsFactory $keyPartsFactory): int
    {
        if (! $this->hasRequiredTables()) {
            $this->error('Required Translation Workbench tables are missing. Run the workbench migrations first.');
            $this->writeTranslationWorkbenchReport(summary: ['error' => 'missing_required_tables']);

            return self::FAILURE;
        }

        $sync = (bool) $this->option('sync') && ! (bool) $this->option('dry-run');
        $sourceLocale = $this->sourceLocale();
        $now = now();
        $rows = $this->inventoryRows($keyPartsFactory, $sourceLocale, $now);
        $summary = [
            'inventory_rows' => $rows->count(),
            'active_rows' => $rows->where('inventory_status', 'active')->count(),
            'shared_rows' => $rows->where('is_shared', true)->count(),
            'ui_rows' => $rows->where('is_ui', true)->count(),
            'dynamic_rows' => $rows->filter(static fn(array $row): bool => (bool) $row['is_dynamic'] || (bool) $row['is_dynamic_multi'])->count(),
            'orphaned_lang_value_rows' => $rows->where('is_orphaned_lang_value', true)->count(),
            'candidate_for_lang_delete_rows' => $rows->where('candidate_for_lang_delete', true)->count(),
            'stale_marked' => 0,
            'synced' => $sync,
            'source_locale' => $sourceLocale,
        ];

        if ($sync) {
            DB::transaction(function () use ($rows, $now, &$summary): void {
                $seen = $rows->pluck('normalized_translation_key')->all();

                foreach ($rows as $row) {
                    $existing = TranslationWorkbenchKeyInventory::query()
                        ->where('normalized_translation_key', $row['normalized_translation_key'])
                        ->first();

                    if ($existing) {
                        $existing->forceFill([
                            ...$row,
                            'first_seen_at' => $existing->first_seen_at ?? $row['first_seen_at'],
                            'scan_count' => ((int) $existing->scan_count) + 1,
                        ])->save();

                        continue;
                    }

                    TranslationWorkbenchKeyInventory::query()->create($row);
                }

                $summary['stale_marked'] = TranslationWorkbenchKeyInventory::query()
                    ->where('inventory_status', 'active')
                    ->whereNotIn('normalized_translation_key', $seen)
                    ->update([
                        'inventory_status' => 'stale',
                        'last_seen_at' => $now,
                        'updated_at' => $now,
                    ]);
            });
        }

        $this->components->info('Translation Workbench key inventory finished.');
        $this->line('Inventory rows: ' . number_format($summary['inventory_rows']));
        $this->line('Shared rows: ' . number_format($summary['shared_rows']));
        $this->line('Dynamic rows: ' . number_format($summary['dynamic_rows']));
        $this->line('Orphaned lang-value rows: ' . number_format($summary['orphaned_lang_value_rows']));
        $this->line('Candidate for lang delete rows: ' . number_format($summary['candidate_for_lang_delete_rows']));

        if (! $sync) {
            $this->warn('Dry run/report only: no key inventory rows were written. Use --sync to update the inventory table.');
        } else {
            $this->line('Stale marked: ' . number_format($summary['stale_marked']));
        }

        $this->writeTranslationWorkbenchReport(
            summary: $summary,
            planSummary: [
                'rows' => $rows
                    ->take(250)
                    ->values()
                    ->all(),
            ],
        );

        return self::SUCCESS;
    }

    private function hasRequiredTables(): bool
    {
        return collect([
            'translation_workbench_key_inventory',
            'translation_workbench_keys',
            'translation_workbench_key_findings',
            'translation_workbench_findings',
            'translation_workbench_lang_values',
        ])->every(static fn(string $table): bool => Schema::hasTable($table));
    }

    private function sourceLocale(): string
    {
        $locale = trim((string) $this->option('source-locale'));

        return $locale !== '' ? $locale : 'en';
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function inventoryRows(TranslationKeyPartsFactory $keyPartsFactory, string $sourceLocale, mixed $now): Collection
    {
        $keys = $this->establishedTranslationKeys();
        $keyRecords = $this->keyRecordsByTranslationKey();
        $findingCounts = $this->findingCountsByTranslationKey();
        $relationCounts = $this->relationCountsByTranslationKey();
        $langValueCounts = $this->langValueCountsByTranslationKey($sourceLocale);
        $workbenchValueCounts = $this->workbenchValueCountsByTranslationKey();
        $dynamicValueCounts = $this->dynamicValueCountsByTranslationKey();
        $dynamicSourceCounts = $this->dynamicSourceCountsByTranslationKey();
        $sharedCounts = $this->sharedFindingCountsByTranslationKey();

        return $keys
            ->map(function (string $translationKey) use (
                $keyPartsFactory,
                $now,
                $keyRecords,
                $findingCounts,
                $relationCounts,
                $langValueCounts,
                $workbenchValueCounts,
                $dynamicValueCounts,
                $dynamicSourceCounts,
                $sharedCounts,
                $sourceLocale,
            ): array {
                $normalizedKey = $this->normalizeTranslationKey($translationKey);
                $keyRecord = $keyRecords->get($normalizedKey, [
                    'key_record_count' => 0,
                    'reviewed_key_count' => 0,
                    'is_ui' => false,
                    'is_dynamic' => false,
                    'is_dynamic_multi' => false,
                    'key_type' => null,
                ]);
                $finding = $findingCounts->get($normalizedKey, []);
                $relation = $relationCounts->get($normalizedKey, []);
                $langValues = $langValueCounts->get($normalizedKey, []);
                $parts = $keyPartsFactory->fromKey($translationKey);
                $sourceActive = (int) ($langValues['source_active'] ?? 0);
                $sourceObsolete = (int) ($langValues['source_obsolete'] ?? 0);
                $sourceDeleted = (int) ($langValues['source_deleted'] ?? 0);
                $targetActive = (int) ($langValues['target_active'] ?? 0);
                $targetObsolete = (int) ($langValues['target_obsolete'] ?? 0);
                $targetDeleted = (int) ($langValues['target_deleted'] ?? 0);
                $langValueCount = $sourceActive + $sourceObsolete + $sourceDeleted + $targetActive + $targetObsolete + $targetDeleted;
                $activeFindings = (int) ($finding['active'] ?? 0);
                $commentedOutFindings = (int) ($finding['commented_out'] ?? 0);
                $obsoleteFindings = (int) ($finding['obsolete'] ?? 0);
                $dynamicValueCount = (int) ($dynamicValueCounts->get($normalizedKey, 0));
                $dynamicSourceCount = (int) ($dynamicSourceCounts->get($normalizedKey, 0));
                $sharedFindingCount = (int) ($sharedCounts->get($normalizedKey, 0));
                $isDynamic = (bool) ($keyRecord['is_dynamic'] ?? false) || $dynamicValueCount > 0 || $dynamicSourceCount > 0;
                $isDynamicMulti = (bool) ($keyRecord['is_dynamic_multi'] ?? false) || $dynamicValueCount > 1;
                $hasActiveCodeUsage = $activeFindings > 0;
                $hasLangValues = $langValueCount > 0;
                $isOrphanedLangValue = $hasLangValues && ! $hasActiveCodeUsage && $commentedOutFindings === 0;

                return [
                    'translation_key' => $translationKey,
                    'normalized_translation_key' => $normalizedKey,
                    'namespace' => $parts['namespace'],
                    'group' => $parts['group'],
                    'key_type' => $keyRecord['key_type'] ?? null,
                    'inventory_status' => 'active',
                    'key_record_count' => (int) ($keyRecord['key_record_count'] ?? 0),
                    'reviewed_key_count' => (int) ($keyRecord['reviewed_key_count'] ?? 0),
                    'finding_active_count' => $activeFindings,
                    'finding_commented_out_count' => $commentedOutFindings,
                    'finding_obsolete_count' => $obsoleteFindings,
                    'relation_active_count' => (int) ($relation['active'] ?? 0),
                    'relation_commented_out_count' => (int) ($relation['commented_out'] ?? 0),
                    'relation_obsolete_count' => (int) ($relation['obsolete'] ?? 0),
                    'source_value_active_count' => $sourceActive,
                    'source_value_obsolete_count' => $sourceObsolete,
                    'source_value_deleted_count' => $sourceDeleted,
                    'target_value_active_count' => $targetActive,
                    'target_value_obsolete_count' => $targetObsolete,
                    'target_value_deleted_count' => $targetDeleted,
                    'lang_file_locale_count' => (int) ($langValues['locale_count'] ?? 0),
                    'workbench_value_count' => (int) $workbenchValueCounts->get($normalizedKey, 0),
                    'dynamic_value_count' => $dynamicValueCount,
                    'dynamic_source_count' => $dynamicSourceCount,
                    'shared_finding_count' => $sharedFindingCount,
                    'is_shared' => $sharedFindingCount > 1,
                    'is_ui' => (bool) ($keyRecord['is_ui'] ?? false) || str_starts_with($translationKey, 'ui.'),
                    'is_dynamic' => $isDynamic,
                    'is_dynamic_multi' => $isDynamicMulti,
                    'has_active_code_usage' => $hasActiveCodeUsage,
                    'has_only_obsolete_code_usage' => ! $hasActiveCodeUsage && $commentedOutFindings === 0 && $obsoleteFindings > 0,
                    'has_lang_values' => $hasLangValues,
                    'is_orphaned_lang_value' => $isOrphanedLangValue,
                    'candidate_for_lang_delete' => $isOrphanedLangValue && ($sourceActive > 0 || $targetActive > 0),
                    'first_seen_at' => $now,
                    'last_seen_at' => $now,
                    'scan_count' => 1,
                    'meta' => [
                        'source' => 'translation-workbench:inventory-keys',
                        'source_locale' => $sourceLocale,
                    ],
                ];
            })
            ->sortBy('translation_key')
            ->values();
    }

    /**
     * @return Collection<int, string>
     */
    private function establishedTranslationKeys(): Collection
    {
        $keys = collect();

        $keys = $keys->merge(
            DB::table('translation_workbench_keys')
                ->whereNotNull('translation_key')
                ->where(function ($query): void {
                    $query->where('review_status', 'reviewed')
                        ->orWhere('is_ui_key', true)
                        ->orWhere('is_dynamic_key', true)
                        ->orWhere('is_dynamic_multi', true);
                })
                ->pluck('translation_key'),
        );

        $keys = $keys->merge(DB::table('translation_workbench_lang_values')->whereNotNull('translation_key')->pluck('translation_key'));

        if (Schema::hasTable('translation_workbench_key_values')) {
            $keys = $keys->merge(
                DB::table('translation_workbench_key_values')
                    ->join('translation_workbench_keys as keys', 'keys.id', '=', 'translation_workbench_key_values.key_id')
                    ->whereNotNull('keys.translation_key')
                    ->pluck('keys.translation_key'),
            );
        }

        if (Schema::hasTable('translation_workbench_dynamic_key_values')) {
            $keys = $keys->merge(
                DB::table('translation_workbench_dynamic_key_values')
                    ->join('translation_workbench_keys as keys', 'keys.id', '=', 'translation_workbench_dynamic_key_values.key_id')
                    ->whereNotNull('keys.translation_key')
                    ->pluck('keys.translation_key'),
            );
        }

        return $keys
            ->map(fn(mixed $key): ?string => $this->normalizeTranslationKey((string) $key))
            ->filter()
            ->unique()
            ->values();
    }

    private function keyRecordsByTranslationKey(): Collection
    {
        return DB::table('translation_workbench_keys')
            ->whereNotNull('translation_key')
            ->selectRaw('LOWER(TRIM(BOTH \'.\' FROM translation_key)) as normalized_translation_key')
            ->selectRaw('COUNT(*) as key_record_count')
            ->selectRaw("SUM(CASE WHEN review_status = 'reviewed' THEN 1 ELSE 0 END) as reviewed_key_count")
            ->selectRaw('MAX(CASE WHEN is_ui_key THEN 1 ELSE 0 END) as is_ui')
            ->selectRaw('MAX(CASE WHEN is_dynamic_key THEN 1 ELSE 0 END) as is_dynamic')
            ->selectRaw('MAX(CASE WHEN is_dynamic_multi THEN 1 ELSE 0 END) as is_dynamic_multi')
            ->selectRaw('MIN(key_type) as key_type')
            ->groupBy('normalized_translation_key')
            ->get()
            ->keyBy('normalized_translation_key')
            ->map(fn(object $row): array => [
                'key_record_count' => (int) $row->key_record_count,
                'reviewed_key_count' => (int) $row->reviewed_key_count,
                'is_ui' => (bool) $row->is_ui,
                'is_dynamic' => (bool) $row->is_dynamic,
                'is_dynamic_multi' => (bool) $row->is_dynamic_multi,
                'key_type' => $row->key_type !== null ? (string) $row->key_type : null,
            ]);
    }

    private function findingCountsByTranslationKey(): Collection
    {
        return $this->statusCountsByTranslationKey(
            DB::table('translation_workbench_key_findings as key_findings')
                ->join('translation_workbench_keys as keys', 'keys.id', '=', 'key_findings.key_id')
                ->join('translation_workbench_findings as findings', 'findings.id', '=', 'key_findings.finding_id')
                ->whereNotNull('keys.translation_key')
                ->selectRaw('LOWER(TRIM(BOTH \'.\' FROM keys.translation_key)) as normalized_translation_key')
                ->selectRaw('findings.status as status')
                ->selectRaw('COUNT(DISTINCT findings.id) as total')
                ->groupBy('normalized_translation_key', 'findings.status')
                ->get(),
        );
    }

    private function relationCountsByTranslationKey(): Collection
    {
        return $this->statusCountsByTranslationKey(
            DB::table('translation_workbench_key_findings as key_findings')
                ->join('translation_workbench_keys as keys', 'keys.id', '=', 'key_findings.key_id')
                ->whereNotNull('keys.translation_key')
                ->selectRaw('LOWER(TRIM(BOTH \'.\' FROM keys.translation_key)) as normalized_translation_key')
                ->selectRaw('key_findings.status as status')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('normalized_translation_key', 'key_findings.status')
                ->get(),
        );
    }

    private function langValueCountsByTranslationKey(string $sourceLocale): Collection
    {
        return DB::table('translation_workbench_lang_values')
            ->whereNotNull('translation_key')
            ->selectRaw('LOWER(TRIM(BOTH \'.\' FROM translation_key)) as normalized_translation_key')
            ->selectRaw('COUNT(DISTINCT locale) as locale_count')
            ->selectRaw("SUM(CASE WHEN locale = ? AND status = 'active' THEN 1 ELSE 0 END) as source_active", [$sourceLocale])
            ->selectRaw("SUM(CASE WHEN locale = ? AND status = 'obsolete' THEN 1 ELSE 0 END) as source_obsolete", [$sourceLocale])
            ->selectRaw("SUM(CASE WHEN locale = ? AND status = 'deleted' THEN 1 ELSE 0 END) as source_deleted", [$sourceLocale])
            ->selectRaw("SUM(CASE WHEN locale <> ? AND status = 'active' THEN 1 ELSE 0 END) as target_active", [$sourceLocale])
            ->selectRaw("SUM(CASE WHEN locale <> ? AND status = 'obsolete' THEN 1 ELSE 0 END) as target_obsolete", [$sourceLocale])
            ->selectRaw("SUM(CASE WHEN locale <> ? AND status = 'deleted' THEN 1 ELSE 0 END) as target_deleted", [$sourceLocale])
            ->groupBy('normalized_translation_key')
            ->get()
            ->keyBy('normalized_translation_key')
            ->map(fn(object $row): array => [
                'locale_count' => (int) $row->locale_count,
                'source_active' => (int) $row->source_active,
                'source_obsolete' => (int) $row->source_obsolete,
                'source_deleted' => (int) $row->source_deleted,
                'target_active' => (int) $row->target_active,
                'target_obsolete' => (int) $row->target_obsolete,
                'target_deleted' => (int) $row->target_deleted,
            ]);
    }

    private function workbenchValueCountsByTranslationKey(): Collection
    {
        if (! Schema::hasTable('translation_workbench_key_values')) {
            return collect();
        }

        return DB::table('translation_workbench_key_values')
            ->join('translation_workbench_keys as keys', 'keys.id', '=', 'translation_workbench_key_values.key_id')
            ->whereNotNull('keys.translation_key')
            ->selectRaw('LOWER(TRIM(BOTH \'.\' FROM keys.translation_key)) as normalized_translation_key')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('normalized_translation_key')
            ->pluck('total', 'normalized_translation_key')
            ->map(fn(mixed $count): int => (int) $count);
    }

    private function dynamicValueCountsByTranslationKey(): Collection
    {
        if (! Schema::hasTable('translation_workbench_dynamic_key_values')) {
            return collect();
        }

        return DB::table('translation_workbench_dynamic_key_values')
            ->join('translation_workbench_keys as keys', 'keys.id', '=', 'translation_workbench_dynamic_key_values.key_id')
            ->whereNotNull('keys.translation_key')
            ->selectRaw('LOWER(TRIM(BOTH \'.\' FROM keys.translation_key)) as normalized_translation_key')
            ->selectRaw('COUNT(DISTINCT value_key) as total')
            ->groupBy('normalized_translation_key')
            ->pluck('total', 'normalized_translation_key')
            ->map(fn(mixed $count): int => (int) $count);
    }

    private function dynamicSourceCountsByTranslationKey(): Collection
    {
        if (! Schema::hasTable('translation_workbench_dynamic_sources')) {
            return collect();
        }

        return DB::table('translation_workbench_dynamic_sources as sources')
            ->join('translation_workbench_keys as keys', 'keys.id', '=', 'sources.key_id')
            ->where('sources.status', '<>', 'obsolete')
            ->whereNotNull('keys.translation_key')
            ->selectRaw('LOWER(TRIM(BOTH \'.\' FROM keys.translation_key)) as normalized_translation_key')
            ->selectRaw('COUNT(DISTINCT sources.id) as total')
            ->groupBy('normalized_translation_key')
            ->pluck('total', 'normalized_translation_key')
            ->map(fn(mixed $count): int => (int) $count);
    }

    private function sharedFindingCountsByTranslationKey(): Collection
    {
        return DB::table('translation_workbench_reviews as reviews')
            ->join('translation_workbench_keys as keys', 'keys.id', '=', 'reviews.key_id')
            ->where('reviews.decision', 'translation_key_bulk_equalized')
            ->whereNotNull('keys.translation_key')
            ->selectRaw('LOWER(TRIM(BOTH \'.\' FROM keys.translation_key)) as normalized_translation_key')
            ->selectRaw('COUNT(DISTINCT reviews.finding_id) as total')
            ->groupBy('normalized_translation_key')
            ->pluck('total', 'normalized_translation_key')
            ->map(fn(mixed $count): int => (int) $count);
    }

    private function statusCountsByTranslationKey(Collection $rows): Collection
    {
        return $rows
            ->groupBy('normalized_translation_key')
            ->map(fn(Collection $group): array => $group
                ->mapWithKeys(fn(object $row): array => [(string) $row->status => (int) $row->total])
                ->all());
    }

    private function normalizeTranslationKey(string $translationKey): ?string
    {
        $translationKey = strtolower(trim(preg_replace('/\.+/', '.', trim($translationKey)), '.'));

        return $translationKey !== '' ? $translationKey : null;
    }
}
