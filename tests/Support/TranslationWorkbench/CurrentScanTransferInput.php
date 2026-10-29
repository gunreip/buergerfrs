<?php

namespace Tests\Support\TranslationWorkbench;

use RuntimeException;

/** Pins current scan evidence to a real source backup for the existing generation importers. */
final class CurrentScanTransferInput
{
    public function prepare(string $root, string $scanDirectory, string $scanHash, string $destination): array
    {
        $manifestPath = $scanDirectory.'/manifest.json';
        if (hash_file('sha256', $manifestPath) !== $scanHash) {
            throw new RuntimeException('Current scan manifest changed');
        }
        $scan = json_decode(file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);
        if ($scan['mode'] !== 'current_source_scan' || $scan['state'] !== 'complete' || $scan['production_connected'] !== false
            || $scan['source_locale'] !== 'en' || $scan['language_issues'] !== []
            || hash_file('sha256', $scanDirectory.'/candidates.jsonl') !== $scan['candidates_sha256']) {
            throw new RuntimeException('Current source evidence is incomplete or unsupported');
        }
        if (file_exists($destination)) {
            throw new RuntimeException('Transfer destination must be new');
        }
        mkdir($destination, 0700, true);
        $files = [];
        foreach ($scan['file_hashes'] as $path => $hash) {
            $resolved = realpath($root.'/'.$path);
            if (! $resolved || ! str_starts_with($resolved, realpath($root).'/') || str_contains($path, '..') || is_link($root.'/'.$path)) {
                throw new RuntimeException('Invalid source path');
            }
            $bytes = file_get_contents($resolved);
            if (hash('sha256', $bytes) !== $hash) {
                throw new RuntimeException('Source changed since scan: '.$path);
            }
            $target = $destination.'/source/'.$path;
            if (! is_dir(dirname($target))) {
                mkdir(dirname($target), 0700, true);
            }
            file_put_contents($target, $bytes);
            $files[$path] = ['sha256' => $hash];
        }
        $backup = ['mode' => 'fresh_scan_source_backup', 'current_scan_manifest_sha256' => $scanHash, 'source_files' => $files];
        $backupBytes = json_encode($backup, JSON_THROW_ON_ERROR);
        file_put_contents($destination.'/backup-manifest.json', $backupBytes);
        $backupHash = hash('sha256', $backupBytes);
        $published = (new PublishedValueBaseline)->read($destination.'/source', $files);
        $language = [];
        foreach ($published['values'] as $value) {
            if ($value['locale'] === 'en') {
                $language[$value['key']] = [...$value, 'sha256' => $files[$value['path']]['sha256']];
            }
        }
        $captureFiles = [];
        $counts = [];
        $rows = [];
        $usedKeys = [];
        $input = fopen($scanDirectory.'/candidates.jsonl', 'r');
        $output = fopen($destination.'/candidates.jsonl', 'x');
        try {
            while (($line = fgets($input)) !== false) {
                $row = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
                $path = $row['source_path'];
                $expected = hash('sha256', json_encode([$path, $row['source_sha256'], $row['source_offset']], JSON_THROW_ON_ERROR));
                if ($row['capture_id'] !== $expected || ($files[$path]['sha256'] ?? null) !== $row['source_sha256']) {
                    throw new RuntimeException('Current capture identity differs');
                }
                $row['provenance']['current_scan_capture_id'] = $row['capture_id'];
                $row['provenance']['current_scan_manifest_sha256'] = $scanHash;
                $row['capture_id'] = hash('sha256', json_encode([$backupHash, $path, $row['source_sha256'], $row['source_offset']], JSON_THROW_ON_ERROR));
                if ($row['reason'] === 'key_with_exact_lang_evidence') {
                    $key = $row['found_translation_key'];
                    $value = $language[$key] ?? null;
                    $proof = $row['provenance']['suggested_literal'];
                    if ($value === null || $value['value'] !== $row['suggested_literal'] || $proof['path'] !== $value['path']
                        || $proof['sha256'] !== $value['sha256'] || $proof['locale'] !== 'en') {
                        throw new RuntimeException('Current key value evidence differs');
                    }
                    // Enrich proof only; all captured raw/suggested values remain unchanged.
                    $row['provenance']['suggested_literal']['key'] = $key;
                }
                if ($row['found_translation_key'] !== null) {
                    $usedKeys[$row['found_translation_key']] = true;
                }
                $captureFiles[$path] = $files[$path];
                $counts[$row['reason']] = ($counts[$row['reason']] ?? 0) + 1;
                fwrite($output, json_encode($row, JSON_THROW_ON_ERROR)."\n");
                $rows[] = $row;
            }
        } finally {
            fclose($input);
            fclose($output);
        }
        if (hash_file('sha256', $scanDirectory.'/candidates.jsonl') !== $scan['candidates_sha256']) {
            throw new RuntimeException('Candidates changed during preparation');
        }
        foreach ($files as $path => $info) {
            if (hash_file('sha256', $root.'/'.$path) !== $info['sha256']) {
                throw new RuntimeException('Source changed during backup: '.$path);
            }
        }
        $capture = ['state' => 'candidates_captured', 'production_connected' => false, 'active_generation_created' => false,
            'candidate_file_sha256' => hash_file('sha256', $destination.'/candidates.jsonl'), 'backup_manifest_sha256' => $backupHash,
            'scope' => $scan['scope'], 'files' => $captureFiles, 'scanner_code_sha256' => $scan['file_hashes'],
            'support_code_sha256' => ['current_scan_manifest' => $scanHash], 'counts' => $counts];
        $bytes = json_encode($capture, JSON_THROW_ON_ERROR);
        file_put_contents($destination.'/capture-manifest.json', $bytes);
        $plan = (new ExistingKeyPlan)->build($rows, array_values($language));
        $allValues = count($published['values']);
        $published['values'] = array_values(array_filter($published['values'], fn ($value) => isset($usedKeys[$value['key']])));
        $perFile = array_count_values(array_column($published['values'], 'path'));
        // Retain only current source references. Full original files remain in the pinned backup.
        foreach ($published['sources'] as $path => &$source) {
            $source['values'] = $perFile[$path] ?? 0;
        }
        unset($source);
        $published['excluded']['transfer_scope'] = ['reason' => 'values_without_current_key_reference_not_imported',
            'omitted_values' => $allValues - count($published['values'])];

        return ['capture_manifest' => $destination.'/capture-manifest.json', 'capture_hash' => hash('sha256', $bytes),
            'candidate_hash' => $capture['candidate_file_sha256'], 'backup_hash' => $backupHash,
            'key_plan' => $plan, 'published' => $published, 'omitted_values' => $allValues - count($published['values']),
            'scan_hash' => $scanHash, 'scan_counts' => $scan['counts']];
    }
}
