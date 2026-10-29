<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use RuntimeException;

/** Read-only scan planning: negative evidence comes from the scanner's completed inventory. */
final class CompleteRescanPlan
{
    public function build(TranslationWorkbenchScanner $scanner, array $paths, array $activeFindings, array $lang = [], array $decisions = []): array
    {
        $known = array_values(array_unique(array_column($activeFindings, 'path')));
        $manifest = $scanner->scanWithManifest($paths, $known);
        $byFile = [];
        foreach ($activeFindings as $finding) {
            $byFile[$finding['path']][] = $finding;
        }
        $items = $manifest['items']->groupBy('sourcePath');
        $files = [];
        $adapter = new CopiedSourceCapture;
        $appliedDecisions = [];
        foreach ($manifest['scanned_paths'] as $path) {
            $bytes = file_get_contents(base_path($path));
            $hash = $manifest['file_hashes'][$path];
            if (hash('sha256', $bytes) !== $hash) {
                throw new RuntimeException('Source changed after discovery: '.$path);
            }
            $rows = [];
            $issues = [];
            foreach ($items->get($path, collect()) as $item) {
                $row = $adapter->capture($item, $bytes, $hash, $lang);
                if (str_starts_with($row['status'], 'excluded_')) {
                    continue;
                }
                $usage = $decisions === [] ? null : ($decisions[ScanCaptureDecisions::identity($row)] ?? null);
                if ($row['reason'] === 'dynamic_or_nonliteral_argument' && $row['source_context']['state'] === 'confirmed_call' && $usage) {
                    $appliedDecisions[] = ['path' => $path, 'line' => $row['source_line'], 'column' => $row['source_column'],
                        'event_id' => $usage['id'], 'decision' => $usage['decision']];
                    if ($usage['decision'] === 'database_value') {
                        $existing = array_filter($byFile[$path] ?? [], fn ($old) =>
                            ((int) $old['source_line'] === $row['source_line'] && (int) $old['source_column'] === $row['source_column'])
                            || $old['raw_expression'] === $row['raw_expression']);
                        if ($existing === []) {
                            continue;
                        }
                        $issues[] = ['line' => $row['source_line'], 'column' => $row['source_column'], 'reason' => 'database_value_finding_conflict'];
                    } else {
                        $issues[] = ['line' => $row['source_line'], 'column' => $row['source_column'], 'reason' => 'translation_work_required'];
                    }
                    $rows[] = $row;
                    continue;
                }
                if ($row['source_context']['state'] !== 'confirmed_call'
                    || ! in_array($row['reason'], ['literal_candidate', 'key_with_exact_lang_evidence'], true)) {
                    $issues[] = ['line' => $row['source_line'], 'column' => $row['source_column'], 'reason' => $row['reason']];
                }
                $rows[] = $row;
            }
            $files[$path] = $issues !== []
                ? ['state' => 'review', 'issues' => $issues, 'captures' => $rows, 'preserved' => array_column($byFile[$path] ?? [], 'id')]
                : $this->compare($byFile[$path] ?? [], $rows);
        }
        foreach ($manifest['missing_paths'] as $path) {
            $files[$path] = ['state' => 'missing_confirmed', 'removed' => array_column($byFile[$path] ?? [], 'id')];
        }
        foreach ($byFile as $path => $old) {
            if (! isset($files[$path])) {
                $files[$path] = ['state' => 'outside_scope', 'preserved' => array_column($old, 'id')];
            }
        }
        // An exact expression in a new file is a relocation candidate, never proof.
        // Preserve both sides pending a decision, including multiple possible destinations.
        $destinations = [];
        foreach ($files as $path => $file) {
            foreach ($file['created'] ?? [] as $row) {
                $destinations[$row['raw_expression']][] = ['path' => $path, 'line' => $row['source_line'], 'column' => $row['source_column']];
            }
        }
        $relocations = [];
        foreach ($manifest['missing_paths'] as $path) {
            foreach ($byFile[$path] ?? [] as $old) {
                $matches = $destinations[$old['raw_expression']] ?? [];
                if ($matches !== []) {
                    $relocations[] = ['finding' => $old['id'], 'source' => $path, 'candidates' => $matches];
                    $files[$path] = ['state' => 'relocation_review', 'preserved' => array_column($byFile[$path], 'id')];
                    foreach ($matches as $match) {
                        $destination = $match['path'];
                        $files[$destination]['state'] = 'relocation_review';
                    }
                }
            }
        }
        // Revalidate read and negative evidence; never return a plan built over changing files.
        $again = $scanner->scanWithManifest($paths, $known);
        foreach (['file_hashes', 'scanned_paths', 'missing_paths', 'scanned_directories'] as $field) {
            if ($again[$field] !== $manifest[$field]) {
                throw new RuntimeException('Scan inventory changed during planning');
            }
        }
        ksort($files);

        return ['mode' => 'read_only_complete_rescan_plan', 'usage_decisions' => $appliedDecisions, 'files' => $files, 'relocations' => $relocations,
            'inventory' => ['hashes' => $manifest['file_hashes'], 'directories' => $manifest['scanned_directories'], 'missing' => $manifest['missing_paths']],
            'states' => array_count_values(array_column($files, 'state'))];
    }

    private function compare(array $old, array $rows): array
    {
        $remaining = [];
        foreach ($rows as $row) {
            $position = $row['source_line'].':'.$row['source_column'];
            if (isset($remaining[$position])) {
                throw new RuntimeException('Duplicate captured position');
            }
            $remaining[$position] = $row;
        }
        $confirmed = [];
        $unmatched = [];
        foreach ($old as $finding) {
            $position = $finding['source_line'].':'.$finding['source_column'];
            if (isset($remaining[$position]) && $remaining[$position]['raw_expression'] === $finding['raw_expression']) {
                $confirmed[] = $finding['id'];
                unset($remaining[$position]);
            } else {
                $unmatched[$finding['id']] = $finding;
            }
        }
        $oldExpressions = [];
        $newExpressions = [];
        foreach ($unmatched as $id => $finding) {
            $oldExpressions[$finding['raw_expression']][] = $id;
        }
        foreach ($remaining as $position => $row) {
            $newExpressions[$row['raw_expression']][] = $position;
        }
        $moves = [];
        foreach ($newExpressions as $expression => $positions) {
            $ids = $oldExpressions[$expression] ?? [];
            if ($ids !== [] && (count($ids) !== 1 || count($positions) !== 1)) {
                return ['state' => 'review', 'issues' => [['reason' => 'ambiguous_movement']], 'preserved' => array_column($old, 'id')];
            }
            if (count($ids) === 1) {
                $moves[] = ['predecessor' => $ids[0], 'capture' => $remaining[$positions[0]]];
                unset($unmatched[$ids[0]], $remaining[$positions[0]]);
            }
        }

        return ['state' => 'ready', 'confirmed' => $confirmed, 'moved' => $moves,
            'created' => array_values($remaining), 'removed' => array_keys($unmatched)];
    }
}
