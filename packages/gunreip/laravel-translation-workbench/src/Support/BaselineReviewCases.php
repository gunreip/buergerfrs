<?php

namespace Gunreip\TranslationWorkbench\Support;

/** Exported database evidence only; never opens a database or saves a decision. */
class BaselineReviewCases
{
    public function load(): ?array
    {
        $path = storage_path('app/private/workbench-previews/historical-review.json');
        if (! is_readable($path)) {
            return null;
        }
        try {
            $bytes = @file_get_contents($path);

            return $bytes === false ? null : $this->validate(json_decode($bytes, true, flags: JSON_THROW_ON_ERROR));
        } catch (\JsonException|\ErrorException) {
            return null;
        }
    }

    public function validate(mixed $data): ?array
    {
        if (! is_array($data) || ($data['mode'] ?? null) !== 'isolated_review_preview'
            || ($data['cluster_stopped'] ?? null) !== true || ! is_array($data['rows'] ?? null)
            || ! is_int($data['open_cases'] ?? null) || ! is_int($data['total_cases'] ?? null)
            || count(array_filter($data['rows'], fn ($row) => is_array($row) && ($row['state'] ?? null) === 'needs_review')) !== $data['open_cases'] || $data['total_cases'] < $data['open_cases']
            || ! $this->isHash($data['backup_manifest_sha256'] ?? null) || ! $this->isHash($data['dump_sha256'] ?? null)) {
            return null;
        }
        $ids = [];
        foreach ($data['rows'] as $row) {
            if (! is_array($row) || ! $this->isHash($row['id'] ?? null) || ! $this->isHash($row['version'] ?? null)
                || isset($ids[$row['id']]) || ! in_array($row['state'] ?? null, ['needs_review', 'recorded'], true)
                || ! is_array($row['evidence'] ?? null) || ! is_string($row['required_action'] ?? null)) {
                return null;
            }
            $e = $row['evidence'];
            if (! in_array($e['source_table'] ?? null, ['translation_values', 'translation_workbench_lang_values'], true)
                || ! is_int($e['source_id'] ?? null) || $e['source_id'] < 1
                || ! is_string($e['key'] ?? null) || ! is_string($e['locale'] ?? null)
                || ! is_string($e['status'] ?? null)
                || ! in_array($row['reason'] ?? null, ['different_from_published', 'no_exact_published_evidence', 'exact_published_match', 'no_scalar_value'], true)
                || $row['reason'] !== ($e['comparison'] ?? null)
                || ! array_key_exists('historical_value', $row)
                || (! is_string($row['historical_value']) && $row['historical_value'] !== null)
                || (is_string($row['historical_value']) ? hash('sha256', $row['historical_value']) : null) !== ($e['value_sha256'] ?? null)) {
                return null;
            }
            $published = $row['published_value'] ?? null;
            if (in_array($row['reason'], ['different_from_published', 'exact_published_match'], true)) {
                if (! is_string($published) || hash('sha256', $published) !== ($e['published_sha256'] ?? null)
                    || ! is_string($e['published_path'] ?? null)
                    || (($published === $row['historical_value']) !== ($row['reason'] === 'exact_published_match'))) {
                    return null;
                }
            } elseif ($published !== null || ($e['published_sha256'] ?? null) !== null) {
                return null;
            }
            $needsReview = in_array($row['reason'], ['different_from_published', 'no_exact_published_evidence'], true);
            if (($row['state'] === 'needs_review') !== $needsReview) {
                return null;
            }
            $subject = 'historical:'.$data['backup_manifest_sha256'].':'.$e['source_table'].':'.$e['source_id'];
            ksort($e);
            $version = hash('sha256', json_encode([json_encode($e, JSON_THROW_ON_ERROR), $row['reason'], $row['required_action'], $needsReview], JSON_THROW_ON_ERROR));
            if (($row['subject'] ?? null) !== $subject || hash('sha256', $subject) !== $row['id'] || $version !== $row['version']) {
                return null;
            }
            $ids[$row['id']] = true;
        }

        foreach ($data['published_values'] ?? [] as $published) {
            if (! is_array($published) || ! is_string($published['key'] ?? null) || ! is_string($published['locale'] ?? null)
                || ! is_string($published['value'] ?? null) || ! is_string($published['path'] ?? null)) {
                return null;
            }
        }

        return $data;
    }

    private function isHash(mixed $value): bool
    {
        return is_string($value) && preg_match('/^[a-f0-9]{64}$/D', $value) === 1;
    }
}
