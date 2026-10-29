<?php

namespace Gunreip\TranslationWorkbench\Support;

use RuntimeException;

/** Joint checkpoint using a caller-supplied read-only DB reader. Writers must still revalidate under locks. */
final class BaselineCorrectionPreflight
{
    public function inspect(array $snapshot, string $source, string $target, string $root, callable $readDatabase): array
    {
        $target = trim($target);
        $files = new BaselineProposedSourceChanges;
        $first = $files->previewAll($snapshot, $source, $target, $root);
        if ($first['status'] !== 'preview') {
            throw new RuntimeException('Complete source proposal is not verified');
        }
        $database = new BaselineProposalEvidence;
        $context = $database->compare($snapshot, $source, $target, $readDatabase());
        $last = $files->previewAll($snapshot, $source, $target, $root);
        if ($last['status'] !== 'preview' || ! hash_equals($first['fingerprint'], $last['fingerprint'])) {
            throw new RuntimeException('Referenced files changed during the joint check');
        }
        $current = $database->compare($snapshot, $source, $target, $readDatabase());
        if (! hash_equals($context['database_fingerprint'], $current['database_fingerprint'])) {
            throw new RuntimeException('Database context changed during the joint check');
        }
        $evidence = ['schema' => 1, 'source' => $source, 'target' => $target,
            'snapshot_sha256' => $last['snapshot_sha256'],
            'source_fingerprint' => $last['fingerprint'],
            'database_fingerprint' => $current['database_fingerprint']];

        return [...$evidence, 'status' => 'verified', 'checked_at' => gmdate('Y-m-d H:i:s').' UTC',
            'source_files' => count($last['files']), 'source_occurrences' => $last['finding_count'],
            'published_values' => $current['published_count'],
            'fingerprint' => hash('sha256', json_encode($evidence, JSON_THROW_ON_ERROR))];
    }
}
