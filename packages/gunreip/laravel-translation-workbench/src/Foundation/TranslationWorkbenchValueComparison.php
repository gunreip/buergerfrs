<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Illuminate\Support\Facades\DB;

/** Diagnostic only: no value selection, merge, export or review decision. */
class TranslationWorkbenchValueComparison
{
    public function snapshot(): array
    {
        return DB::transaction(function (): array {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ, READ ONLY');
            }
            $legacy = DB::table('translation_values as values')
                ->leftJoin('translation_keys as keys', 'keys.id', '=', 'values.translation_key_id')
                ->select('values.id', 'values.locale', 'values.value', 'values.status', 'values.is_base_duplicate', 'keys.key as translation_key')
                ->orderBy('values.id')->get();
            $workbench = DB::table('translation_workbench_lang_values')
                ->select('id', 'locale', 'value', 'status', 'translation_key')
                ->orderBy('id')->get();

            return $this->compare($legacy, $workbench);
        });
    }

    public function compare(iterable $legacy, iterable $workbench): array
    {
        $groups = [];
        $unassigned = [];
        $counts = array_fill_keys(['identical', 'different', 'legacy_only', 'workbench_only', 'ambiguous'], 0);
        foreach (['legacy' => $legacy, 'workbench' => $workbench] as $side => $rows) {
            foreach ($rows as $row) {
                $row = (array) $row;
                $key = $row['translation_key'] ?? null;
                $locale = strtolower(str_replace('_', '-', trim((string) ($row['locale'] ?? ''))));
                if (! is_string($key) || trim($key) === '' || $locale === '') {
                    $unassigned[] = ['side' => $side, 'row' => $row];

                    continue;
                }
                $identity = json_encode([$key, $locale], JSON_THROW_ON_ERROR);
                $groups[$identity] ??= ['key' => $key, 'locale' => $locale, 'legacy' => [], 'workbench' => []];
                $groups[$identity][$side][] = $row;
            }
        }
        $statusDifferences = 0;
        foreach ($groups as &$group) {
            $left = $group['legacy'];
            $right = $group['workbench'];
            $category = match (true) {
                $left === [] => 'workbench_only',
                $right === [] => 'legacy_only',
                count($left) !== 1 || count($right) !== 1 => 'ambiguous',
                $left[0]['value'] === $right[0]['value'] => 'identical',
                default => 'different',
            };
            $group['category'] = $category;
            $group['multiple_rows'] = count($left) > 1 || count($right) > 1;
            $group['status_difference'] = null;
            if (count($left) === 1 && count($right) === 1) {
                $legacyStatus = $left[0]['status'] === 'ok' ? 'active' : $left[0]['status'];
                $group['status_difference'] = $legacyStatus !== $right[0]['status'];
                $statusDifferences += (int) $group['status_difference'];
            }
            $counts[$category]++;
        }
        unset($group);
        usort($groups, fn (array $a, array $b): int => [$a['locale'], $a['key']] <=> [$b['locale'], $b['key']]);

        return [
            'scope' => 'legacy_and_foundation_database_values',
            'counts' => $counts,
            'status_differences' => $statusDifferences,
            'multiple_row_pairs' => count(array_filter($groups, fn (array $group): bool => $group['multiple_rows'])),
            'unassigned' => $unassigned,
            'comparisons' => $groups,
        ];
    }
}
