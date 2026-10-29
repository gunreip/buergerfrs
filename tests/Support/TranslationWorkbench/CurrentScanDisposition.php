<?php

namespace Tests\Support\TranslationWorkbench;

/** Plans reuse from current source evidence only, without legacy status or text-based linking. */
final class CurrentScanDisposition
{
    public function classify(array $row, array $values, string $sourceLocale): string
    {
        if (str_starts_with($row['status'], 'excluded_')) {
            return 'excluded';
        }
        if (($row['source_context']['state'] ?? null) !== 'confirmed_call') {
            return 'expression_review';
        }
        if ($row['reason'] === 'literal_candidate') {
            return 'literal_new';
        }
        $key = $row['found_translation_key'];
        if ($key !== null) {
            if ($row['reason'] !== 'key_with_exact_lang_evidence' || count(explode('.', $key)) < 3 || str_contains($key, '::')) {
                return 'key_review';
            }

            return isset($values[$key][$sourceLocale]) ? 'reusable_key' : 'key_review';
        }

        return 'expression_review';
    }
}
