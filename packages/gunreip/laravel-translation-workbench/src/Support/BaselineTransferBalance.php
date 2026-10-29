<?php

namespace Gunreip\TranslationWorkbench\Support;

use InvalidArgumentException;

/** Read-only accounting of the complete rehearsal generation, not a migration approval. */
final class BaselineTransferBalance
{
    public function inspect(?array $report): array
    {
        if ((new BaselineRehearsalReport)->validate($report) === null) {
            return ['state' => 'unavailable', 'sections' => []];
        }
        foreach (['capture', 'arguments', 'published_values'] as $step) {
            if (($report['fresh_generation_'.$step.'_verified'] ?? null) !== true) {
                return ['state' => 'unavailable', 'sections' => []];
            }
        }
        try {
            $c = $report['fresh_generation_capture'] ?? [];
            $a = $report['fresh_generation_arguments'] ?? [];
            $p = $report['fresh_generation_published_values'] ?? [];
            if (! is_array($c) || ! is_array($a) || ! is_array($p)) {
                throw new InvalidArgumentException;
            }
            $n = static function (array $row, string $key): int {
                $value = $row[$key] ?? null;
                if (! is_int($value) || $value < 0) {
                    throw new InvalidArgumentException;
                }

                return $value;
            };
            $roles = $a['roles'] ?? null;
            $arguments = $a['arguments'] ?? null;
            $totals = $p['totals'] ?? null;
            $locales = $p['locales'] ?? null;
            if (! is_array($roles) || ! is_array($arguments) || ! is_array($totals) || ! is_array($locales) || $locales === []) {
                throw new InvalidArgumentException;
            }
            $captured = $n($c, 'captured');
            $pending = $n($c, 'pending');
            $excluded = $n($c, 'excluded');
            $links = $n($c, 'links');
            $blocked = $n($c, 'blocked_findings');
            $keys = $n($c, 'keys');
            $translation = $n($roles, 'translation_source:pending');
            $options = $n($roles, 'option_source:pending');
            $usage = $n($roles, 'value_usage:pending');
            $translationArguments = $n($arguments, 'overridable_default') + $n($arguments, 'runtime_value_required')
                + $n($arguments, 'producer_observations') + $n($arguments, 'literal_key_decision_required');
            $values = $n($p, 'values');
            $linkedValues = $n($totals, 'linked');
            $blockedValues = $n($totals, 'blocked_structure');
            $unassignedValues = $n($totals, 'unassigned_key');
            $localeValues = 0;
            foreach (array_keys($locales) as $locale) {
                if (! is_string($locale)) {
                    throw new InvalidArgumentException;
                }
                $localeValues += $n($locales, $locale);
            }
            if (count($roles) !== 5 || count($arguments) !== 5
                || $n($roles, 'translation_source:captured') !== $captured || $n($roles, 'excluded:excluded') !== $excluded
                || $translation + $options + $usage !== $pending
                || $translationArguments !== $translation || $n($arguments, 'option_arguments') !== $options
                || $links + $blocked > $captured || $keys > $links
                || $linkedValues + $blockedValues + $unassignedValues !== $values || $localeValues !== $values) {
                throw new InvalidArgumentException;
            }
            $row = static fn (string $label, int $count, string $status): array => compact('label', 'count', 'status');

            return ['state' => 'verified', 'sections' => [
                ['title' => __('Source capture'), 'unit' => __('Occurrences'), 'total' => $captured + $pending + $excluded, 'rows' => [
                    $row(__('Captured findings: evidence preserved, not necessarily edited or translated'), $captured, 'preserved'),
                    $row(__('Pending capture cases'), $pending, 'open'),
                    $row(__('Excluded source occurrences'), $excluded, 'excluded'),
                ]],
                ['title' => __('Key assignment within captured findings'), 'unit' => __('Findings'), 'total' => $captured, 'rows' => [
                    $row(__('Findings linked to evidenced valid keys'), $links, 'preserved'),
                    $row(__('Findings blocked by key structure'), $blocked, 'open'),
                    $row(__('Other captured findings without a key link'), $captured - $links - $blocked, 'open'),
                ]],
                ['title' => __('Pending capture roles'), 'unit' => __('Occurrences'), 'total' => $pending, 'rows' => [
                    $row(__('Pending translation sources'), $translation, 'open'),
                    $row(__('Pending option sources'), $options, 'open'),
                    $row(__('Value usages: not independent translation tasks'), $usage, 'evidence_only'),
                ]],
                ['title' => __('Published language evidence'), 'unit' => __('Locale values'), 'total' => $values, 'rows' => [
                    $row(__('Published values linked to active keys'), $linkedValues, 'preserved'),
                    $row(__('Published values blocked by key structure'), $blockedValues, 'open'),
                    $row(__('Published values without an active key'), $unassignedValues, 'open'),
                ]],
                ['title' => __('Language file scope'), 'unit' => __('Files'), 'total' => $n($p, 'files') + $n($p, 'excluded_files'), 'rows' => [
                    $row(__('Included application PHP language files'), $n($p, 'files'), 'preserved'),
                    $row(__('Language files outside the tested scope'), $n($p, 'excluded_files'), 'excluded'),
                ]],
            ], 'keys' => $keys, 'locales' => count($locales),
                'argument_records' => $translationArguments + $options, 'producers' => $n($a, 'producers')];
        } catch (InvalidArgumentException) {
            return ['state' => 'inconsistent', 'sections' => []];
        }
    }
}
