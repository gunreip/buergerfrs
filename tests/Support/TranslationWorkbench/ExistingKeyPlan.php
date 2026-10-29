<?php

namespace Tests\Support\TranslationWorkbench;

use RuntimeException;

/** Reconstructs existing source usage only; never assigns keys by equal translation text. */
final class ExistingKeyPlan
{
    public function build(array $captures, array $language): array
    {
        $values = [];
        foreach ($language as $row) {
            if ($row['locale'] !== 'en') {
                throw new RuntimeException('Expected exact source locale');
            }
            if (isset($values[$row['key']])) {
                throw new RuntimeException('Ambiguous language evidence');
            }
            $values[$row['key']] = $row;
        }
        $items = [];
        $seen = [];
        $keys = [];
        foreach ($captures as $row) {
            if (isset($seen[$row['capture_id']])) {
                throw new RuntimeException('Duplicate capture');
            }
            $seen[$row['capture_id']] = true;
            if ($row['reason'] !== 'key_with_exact_lang_evidence') {
                continue;
            }
            $key = $row['found_translation_key'];
            $evidence = $values[$key] ?? null;
            $proof = $row['provenance']['suggested_literal'] ?? null;
            if ($row['source_context']['state'] !== 'confirmed_call' || $row['raw_source_code_literal'] !== $key
                || $row['raw_literal'] !== null || $row['suggested_key'] !== null || $evidence === null
                || $row['suggested_literal'] !== $evidence['value'] || $proof === null
                || $proof['path'] !== $evidence['path'] || $proof['sha256'] !== $evidence['sha256'] || $proof['locale'] !== 'en' || $proof['key'] !== $key) {
                throw new RuntimeException('Existing key evidence differs');
            }
            // User contract: group + nested array path, never silently repair ui.type to ui.type.type.
            $eligible = preg_match('/^[a-zA-Z0-9_-]+(?:\.[a-zA-Z0-9_-]+){2,}$/D', $key) === 1;
            $items[] = ['capture_id' => $row['capture_id'], 'key' => $key, 'namespace' => '',
                'state' => $eligible ? 'eligible' : 'blocked_structure', 'source_path' => $row['source_path'],
                'source_line' => $row['source_line'], 'source_sha256' => $row['source_sha256'], 'evidence' => $evidence];
            $keys[$key] = $eligible;
        }

        return ['items' => $items, 'key_count' => count($keys), 'eligible_keys' => count(array_filter($keys)),
            'eligible_findings' => count(array_filter($items, fn ($i) => $i['state'] === 'eligible')),
            'blocked_keys' => array_keys(array_filter($keys, fn ($eligible) => ! $eligible))];
    }
}
