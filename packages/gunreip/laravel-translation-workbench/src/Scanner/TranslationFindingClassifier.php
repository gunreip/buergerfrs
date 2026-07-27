<?php

namespace Gunreip\TranslationWorkbench\Scanner;

class TranslationFindingClassifier
{
    /**
     * Central scanner-side classification for discovered translation findings.
     *
     * Keep classification rules here. Scanner commands must not silently
     * introduce their own entry_type/candidate_type/candidate_reason decisions.
     *
     * @return array{entry_type: string, candidate_type: ?string, candidate_reason: ?string}
     */
    public function classify(string $kind, ?string $literalText, ?string $functionName): array
    {
        if ($kind === 'dynamic_resolved_static_prop') {
            return [
                'entry_type' => 'resolved_static_prop',
                'candidate_type' => null,
                'candidate_reason' => 'static_translated_prop_default',
            ];
        }

        if ($kind === 'dynamic_numeric') {
            return [
                'entry_type' => 'dynamic_numeric',
                'candidate_type' => null,
                'candidate_reason' => 'numeric_dynamic_expression',
            ];
        }

        if ($kind === 'dynamic') {
            return [
                'entry_type' => 'dynamic',
                'candidate_type' => 'dynamic',
                'candidate_reason' => 'non_literal_first_argument',
            ];
        }

        if ($functionName === 'dynamic_label') {
            return [
                'entry_type' => 'dynamic',
                'candidate_type' => 'dynamic',
                'candidate_reason' => 'dynamic_label_call',
            ];
        }

        if ($kind === 'dynamic_multi') {
            return [
                'entry_type' => 'dynamic',
                'candidate_type' => 'dynamic',
                'candidate_reason' => 'runtime_option_expression',
            ];
        }

        if ($kind === 'key') {
            return [
                'entry_type' => 'key',
                'candidate_type' => null,
                'candidate_reason' => null,
            ];
        }

        if ($this->isUiCandidateLiteral($literalText)) {
            return [
                'entry_type' => 'literal',
                'candidate_type' => 'ui',
                'candidate_reason' => 'short_literal_without_placeholders',
            ];
        }

        return [
            'entry_type' => 'literal',
            'candidate_type' => null,
            'candidate_reason' => null,
        ];
    }

    public function isUiCandidateLiteral(?string $literalText): bool
    {
        $literalText = trim((string) $literalText);

        if ($literalText === '') {
            return false;
        }

        if (strlen($literalText) > 48) {
            return false;
        }

        if (str_contains($literalText, '.') || preg_match('/[:{}%]/', $literalText)) {
            return false;
        }

        $words = preg_split('/\s+/', $literalText, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($words) < 1 || count($words) > 3) {
            return false;
        }

        return (bool) preg_match('/^[\pL\pN][\pL\pN\s_\'-]*$/u', $literalText);
    }
}
