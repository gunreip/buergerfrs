<?php

namespace Tests\Support\TranslationWorkbench;

use RuntimeException;

/** Evidence projection for the isolated rehearsal; never creates translation keys. */
final class CaptureSourceRole
{
    public function classify(array $capture, ?array $review): array
    {
        if ($review !== null) {
            foreach (['capture_id', 'source_path', 'source_sha256', 'raw_expression'] as $field) {
                if ($review[$field] !== $capture[$field]) {
                    throw new RuntimeException('Review does not belong to this capture');
                }
            }
            if ($review['original_reason'] !== $capture['reason'] || $review['decision'] !== null || $review['automatic_import'] !== false) {
                throw new RuntimeException('Expected undecided source evidence');
            }
        }
        $reason = $capture['reason'];
        if (in_array($reason, ['commented_source', 'context_excluded_string'], true)) {
            if ($review !== null) {
                throw new RuntimeException('Excluded source has a review');
            }

            return ['role' => 'excluded', 'state' => 'excluded', 'evidence' => []];
        }
        if (($capture['source_context']['state'] ?? null) !== 'confirmed_call') {
            throw new RuntimeException('Unconfirmed source context');
        }
        if (in_array($reason, ['literal_candidate', 'key_with_exact_lang_evidence'], true)) {
            if ($review !== null) {
                throw new RuntimeException('Unexpected review of static capture');
            }

            return ['role' => 'translation_source', 'state' => 'captured', 'evidence' => []];
        }
        if ($review === null || ! in_array($reason, ['dynamic_or_nonliteral_argument', 'special_call_requires_context', 'key_like_argument_without_lang_evidence'], true)) {
            throw new RuntimeException('Missing or unknown source classification');
        }
        $role = match ($review['category']) {
            'serialization_only', 'serialized_translated_prop' => 'value_usage',
            'explicit_option_owner' => 'option_source',
            'overridable_prop_default', 'runtime_translation_argument', 'key_or_literal_review' => 'translation_source',
            default => throw new RuntimeException('Unknown review category'),
        };

        return ['role' => $role, 'state' => 'pending', 'evidence' => $review['evidence']];
    }
}
