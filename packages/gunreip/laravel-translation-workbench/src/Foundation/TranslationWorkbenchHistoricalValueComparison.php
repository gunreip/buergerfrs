<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Illuminate\Support\Facades\DB;

/** Read-only evidence; matching text does not authorize migration or deletion. */
class TranslationWorkbenchHistoricalValueComparison
{
    public const TABLES = [
        'translation_workbench_entries', 'translation_workbench_values', 'translation_workbench_dynamic_values',
        'translation_workbench_keys', 'translation_workbench_key_values',
        'translation_workbench_dynamic_key_values', 'translation_workbench_lang_values',
    ];

    public function snapshot(): array
    {
        return DB::transaction(function (): array {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ, READ ONLY');
            }
            $entries = DB::table('translation_workbench_entries')->get()->keyBy('id');
            $keys = DB::table('translation_workbench_keys')->get()->keyBy('id');
            $targets = [];
            foreach (['key_values', 'dynamic_key_values', 'lang_values'] as $store) {
                foreach (DB::table('translation_workbench_'.$store)->orderBy('id')->get() as $row) {
                    $key = $store === 'lang_values' ? $row->translation_key : ($keys[$row->key_id]->translation_key ?? null);
                    $identity = $this->identity($key, $row->locale, $store === 'dynamic_key_values' ? $row->value_key : null);
                    $targets[$identity][] = ['table' => 'translation_workbench_'.$store, 'row' => (array) $row];
                }
            }
            $rows = [];
            foreach (['values', 'dynamic_values'] as $store) {
                foreach (DB::table('translation_workbench_'.$store)->orderBy('id')->get() as $row) {
                    $entry = $entries[$row->entry_id] ?? null;
                    $explicitKeys = array_values(array_unique(array_filter([
                        $entry->translation_key ?? null, $entry->existing_key ?? null,
                    ], fn ($key) => is_string($key) && trim($key) !== '')));
                    $dynamic = $store === 'dynamic_values';
                    $locale = $dynamic ? $row->locale : (str_starts_with($row->value_key, 'source:') ? substr($row->value_key, 7) : null);
                    $value = $dynamic ? $row->value : $row->native_label;
                    $candidates = [];
                    $category = match (true) {
                        $entry === null => 'missing_entry',
                        count($explicitKeys) > 1 => 'conflicting_keys',
                        $explicitKeys === [] => 'unassigned_key',
                        $locale === null || trim($locale) === '' => 'observation_without_locale',
                        default => null,
                    };
                    if ($category === null) {
                        $candidates = $targets[$this->identity($explicitKeys[0], $locale, $dynamic ? $row->value_key : null)] ?? [];
                        $perStore = array_count_values(array_column($candidates, 'table'));
                        $category = match (true) {
                            $candidates === [] => 'legacy_only',
                            max($perStore) > 1 => 'ambiguous',
                            count(array_filter($candidates, fn ($candidate) => $candidate['row']['value'] !== $value)) > 0 => 'different',
                            default => 'identical',
                        };
                    }
                    $rows[] = [
                        'table' => 'translation_workbench_'.$store, 'row' => (array) $row,
                        'entry' => $entry === null ? null : (array) $entry,
                        'explicit_keys' => $explicitKeys, 'locale' => $locale,
                        'category' => $category, 'candidates' => $candidates,
                    ];
                }
            }

            return ['scope' => 'historical_workbench_values', 'counts' => array_count_values(array_column($rows, 'category')), 'comparisons' => $rows];
        });
    }

    private function identity(?string $key, string $locale, ?string $valueKey): string
    {
        return json_encode([$key, strtolower(str_replace('_', '-', trim($locale))), $valueKey], JSON_THROW_ON_ERROR);
    }
}
