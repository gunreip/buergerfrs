<?php

namespace Tests\Support\TranslationWorkbench;

/** Exact identity/value comparison only; no fallback, guessed key or import decision. */
final class HistoricalScalarValueReview
{
    public function classify(array $row, array $published): array
    {
        $result = $row;
        unset($result['value']);
        $result['value_sha256'] = is_string($row['value'] ?? null) ? hash('sha256', $row['value']) : null;
        $result['published_sha256'] = null;
        $result['published_path'] = null;
        if (! is_string($row['key'] ?? null) || $row['key'] === '' || ! is_string($row['locale'] ?? null) || $row['locale'] === '') {
            $result['comparison'] = 'identity_unresolved';
        } elseif (str_contains($row['key'], '::')) {
            $result['comparison'] = 'namespace_outside_scope';
        } elseif (($row['namespace'] ?? '') !== '' && $row['namespace'] !== explode('.', $row['key'], 2)[0]) {
            // Historical Workbench "namespace" means the first key segment / PHP file.
            $result['comparison'] = 'namespace_key_mismatch';
        } elseif (! in_array($row['value_type'] ?? 'text', ['text', 'string'], true)) {
            $result['comparison'] = 'non_scalar_type';
        } elseif (! is_string($row['value'] ?? null)) {
            $result['comparison'] = 'no_scalar_value';
        } else {
            $match = $published[json_encode([$row['locale'], $row['key']], JSON_THROW_ON_ERROR)] ?? null;
            if ($match === null) {
                $result['comparison'] = 'no_exact_published_evidence';
            } else {
                $result['published_sha256'] = hash('sha256', $match['value']);
                $result['published_path'] = $match['path'];
                $result['comparison'] = $row['value'] === $match['value'] ? 'exact_published_match' : 'different_from_published';
            }
        }
        $result['decision'] = null;

        return $result;
    }
}
