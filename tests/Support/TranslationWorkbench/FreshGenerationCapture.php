<?php

namespace Tests\Support\TranslationWorkbench;

use PDO;
use RuntimeException;

/** Reuses the reviewed capture and key importers on an empty versioned schema. */
final class FreshGenerationCapture
{
    public function verify(PDO $db, string $root, string $backup, string $backupHash, callable $check, callable $reject): array
    {
        $read = static fn (string $path): array => json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
        $audit = $root.'/docs/audits/translation-workbench-2026-10-15/';
        $context = $read($audit.'copied-source-context-2026-10-19.json');
        $manifestPath = $root.'/'.$context['manifest'];
        $manifest = $read($manifestPath);
        if (! str_starts_with((string) realpath($manifestPath), realpath($backup).'/')
            || hash_file('sha256', $manifestPath) !== $context['manifest_sha256']
            || $manifest['backup_manifest_sha256'] !== $backupHash) {
            throw new RuntimeException('Capture belongs to another backup');
        }
        foreach (['files', 'language_files'] as $collection) {
            foreach ($manifest[$collection] as $path => $info) {
                $resolved = realpath($backup.'/source/'.$path);
                if (! $resolved || ! str_starts_with($resolved, realpath($backup.'/source').'/')
                    || hash_file('sha256', $resolved) !== (is_array($info) ? $info['sha256'] : $info)) {
                    throw new RuntimeException('Capture source evidence changed');
                }
            }
        }
        $capture = new StaticCaptureImporter;
        $reject(fn () => $capture->import($db, $manifestPath, str_repeat('0', 64), $context['candidate_file_sha256']),
            'Wrong capture manifest accepted');
        $result = $capture->import($db, $manifestPath, $context['manifest_sha256'], $context['candidate_file_sha256']);
        $check($capture->import($db, $manifestPath, $context['manifest_sha256'], $context['candidate_file_sha256'])['replayed'],
            'Full capture retry not recognized');
        $check((int) $db->query('SELECT count(*) FROM translation_workbench_scan_runs')->fetchColumn() === 1,
            'Capture retry generated another scan');
        $query = $db->prepare('SELECT o.disposition,f.*,s.path FROM capture_outcomes o
            LEFT JOIN translation_workbench_findings f ON f.id=o.finding_id
            LEFT JOIN translation_workbench_source_files s ON s.id=f.source_file_id
            WHERE o.manifest_sha256=? AND o.capture_id=?');
        $stream = fopen(dirname($manifestPath).'/candidates.jsonl', 'r');
        $total = 0;
        try {
            while (($line = fgets($stream)) !== false) {
                $row = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
                $query->execute([$context['manifest_sha256'], $row['capture_id']]);
                $stored = $query->fetch(PDO::FETCH_ASSOC);
                if (! $stored) {
                    throw new RuntimeException('Capture disposition missing');
                }
                if ($stored['disposition'] === 'captured') {
                    foreach (['raw_expression', 'raw_source_code_literal', 'raw_literal', 'suggested_literal', 'suggested_key'] as $field) {
                        if ($stored[$field] !== $row[$field]) {
                            throw new RuntimeException('Fresh capture changed '.$field);
                        }
                    }
                    if ($stored['path'] !== $row['source_path'] || (int) $stored['source_line'] !== $row['source_line']
                        || (int) $stored['source_column'] !== $row['source_column']) {
                        throw new RuntimeException('Fresh capture position changed');
                    }
                } elseif ($stored['id'] !== null) {
                    throw new RuntimeException('Pending or excluded evidence became an active finding');
                }
                $total++;
            }
        } finally {
            fclose($stream);
        }
        $check($total === array_sum(array_intersect_key($result, array_flip(['captured', 'pending', 'excluded'])))
            && $total === (int) $db->query('SELECT count(*) FROM capture_outcomes')->fetchColumn(),
            'Capture accounting lost or duplicated evidence');
        $findingHash = static fn (): string => $db->query('SELECT md5(string_agg(row_to_json(f)::text,chr(10) ORDER BY id)) FROM translation_workbench_findings f')->fetchColumn();
        $before = $findingHash();
        $keyReport = $read($audit.'existing-key-links-2026-10-19.json');
        $planPath = $root.'/'.$keyReport['plan'];
        if ($keyReport['capture_manifest_sha256'] !== $context['manifest_sha256']
            || ! str_starts_with((string) realpath($planPath), realpath($backup).'/')
            || hash_file('sha256', $planPath) !== $keyReport['plan_sha256']) {
            throw new RuntimeException('Key plan does not match verified capture');
        }
        $plan = $read($planPath);
        $keys = new ExistingKeyImporter;
        $bad = $plan;
        $bad['items'][count($bad['items']) - 1]['key'] = 'invented.wrong.key';
        $reject(fn () => $keys->import($db, $bad, $context['manifest_sha256']), 'Changed key evidence accepted');
        $check((int) $db->query('SELECT count(*) FROM translation_workbench_keys')->fetchColumn() === 0
            && (int) $db->query('SELECT count(*) FROM existing_key_imports')->fetchColumn() === 0,
            'Failed key import left partial assignments');
        $linked = $keys->import($db, $plan, $context['manifest_sha256']);
        $check($keys->import($db, $plan, $context['manifest_sha256'])['replayed'], 'Key import retry not recognized');
        $check($linked['keys'] === $plan['eligible_keys'] && $linked['links'] === $plan['eligible_findings'],
            'Fresh generation key counts differ');
        $check((int) $db->query('SELECT count(*) FROM translation_workbench_key_findings')->fetchColumn() === $linked['links'],
            'Key retry duplicated links');
        $check((int) $db->query("SELECT count(*) FROM existing_key_observations WHERE state='blocked_structure' AND (key_id IS NOT NULL OR link_id IS NOT NULL)")->fetchColumn() === 0,
            'Blocked structure was automatically assigned');
        $check((int) $db->query('SELECT count(*) FROM translation_workbench_key_findings l
            JOIN translation_workbench_findings f ON f.id=l.finding_id
            JOIN translation_workbench_key_names n ON n.key_id=l.key_id
            WHERE n.full_name<>f.raw_source_code_literal OR f.raw_literal IS NOT NULL OR f.suggested_key IS NOT NULL')->fetchColumn() === 0,
            'Key assignment invented raw evidence or assigned a literal');
        $check((int) $db->query('SELECT count(DISTINCT finding_id) FROM existing_key_observations')->fetchColumn() === count($plan['items']),
            'Key observations merged distinct findings');
        $check($findingHash() === $before, 'Key assignment changed raw findings');
        foreach (['translation_workbench_events', 'translation_workbench_reviews', 'translation_workbench_value_revisions'] as $table) {
            $check((int) $db->query("SELECT count(*) FROM $table")->fetchColumn() === 0, 'Capture imported history or decisions');
        }

        return ['captured' => $result['captured'], 'pending' => $result['pending'], 'excluded' => $result['excluded'],
            'keys' => $linked['keys'], 'links' => $linked['links'], 'blocked_findings' => count($plan['items']) - $linked['links'],
            'capture_manifest_sha256' => $context['manifest_sha256'], 'key_plan_sha256' => $keyReport['plan_sha256']];
    }
}
