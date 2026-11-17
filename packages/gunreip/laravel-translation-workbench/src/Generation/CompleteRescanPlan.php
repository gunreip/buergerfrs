<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use RuntimeException;
use Symfony\Component\Process\Process;

/** Read-only scan planning: negative evidence comes from the scanner's completed inventory. */
final class CompleteRescanPlan
{
    public function build(TranslationWorkbenchScanner $scanner, array $paths, array $activeFindings, array $lang = [], array $decisions = [], ?callable $progress = null): array
    {
        $known = array_values(array_unique(array_column($activeFindings, 'path')));
        $manifest = $scanner->scanWithManifest($paths, $known, $progress ? fn ($phase, $done, $total) => $progress($phase, $done, 2 * $total + 1) : null);
        $byFile = [];
        foreach ($activeFindings as $finding) {
            $byFile[$finding['path']][] = $finding;
        }
        $items = $manifest['items']->groupBy('sourcePath');
        $files = [];
        $adapter = new CopiedSourceCapture;
        $appliedDecisions = [];
        $totalFiles = count($manifest['scanned_paths']);
        $comparedFiles = 0;
        $progress && $progress('comparing', $totalFiles, 2 * $totalFiles + 1);
        foreach ($manifest['scanned_paths'] as $path) {
            $bytes = file_get_contents(base_path($path));
            $hash = $manifest['file_hashes'][$path];
            if (hash('sha256', $bytes) !== $hash) {
                throw new RuntimeException('Source changed after discovery: '.$path);
            }
            $lines = explode("\n", $bytes);
            $rows = [];
            $issues = [];
            foreach ($items->get($path, collect()) as $item) {
                $row = $adapter->capture($item, $bytes, $hash, $lang);
                if (str_starts_with($row['status'], 'excluded_')) {
                    continue;
                }
                $row['source_window'] = self::sourceWindow($lines, $row['source_line'], $row['source_column']);
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
            $files[$path] = $this->compare($byFile[$path] ?? [], $rows, $issues);
            $progress && $progress('comparing', $totalFiles + ++$comparedFiles, 2 * $totalFiles + 1);
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
        // Record possible successors separately from the verified removals and captures.
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
                    $files[$path]['state'] = 'relocation_review';
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

    /** Compare a single quoted argument without changing any bytes inside the literal. */
    private function staticCallTokens(string $expression): array
    {
        $tokens = [];
        foreach (token_get_all('<?php '.$expression) as $token) {
            if (is_array($token) && in_array($token[0], [T_OPEN_TAG, T_WHITESPACE], true)) continue;
            $tokens[] = is_array($token) ? [$token[0], $token[1]] : $token;
        }
        if (array_slice($tokens, -2) === [',', ')']) {
            array_splice($tokens, -2, 1);
        }
        $argument = $tokens[count($tokens) - 2] ?? null;
        if (!is_array($argument) || $argument[0] !== T_CONSTANT_ENCAPSED_STRING
            || array_slice($tokens, -3, 1) !== ['('] || end($tokens) !== ')'
            || count(array_filter($tokens, static fn ($token) => $token === '(')) !== 1) return [];
        return $tokens;
    }

    public static function sourceWindow(array $lines, int $line, int $column): string
    {
        return hash('sha256', serialize([$lines[$line - 2] ?? null, $lines[$line - 1] ?? null,
            $lines[$line] ?? null, $column]));
    }

    private function committedSnapshot(string $path): array
    {
        try {
            $process = new Process(['git', 'cat-file', 'blob', 'HEAD:'.$path], base_path());
            $process->run();
            if (!$process->isSuccessful()) return [];
            $bytes = $process->getOutput();
            return ['hash' => hash('sha256', $bytes), 'lines' => explode("\n", $bytes)];
        } catch (\Symfony\Component\Process\Exception\ExceptionInterface) {
            return [];
        }
    }

    private function sourceWindowPairs(array $ids, array $positions, array $old, array $rows): array
    {
        if (!$ids || count($ids) > count($positions) || count($positions) < 2) return [];
        $targets = [];
        foreach ($positions as $position) {
            $window = $rows[$position]['source_window'];
            $targets[$window][] = $position;
        }
        $pairs = [];
        foreach ($ids as $id) {
            $window = $old[$id]['source_window'] ?? null;
            if (count($targets[$window] ?? []) !== 1) return [];
            $position = $targets[$window][0];
            $pairs[] = [$id, $position, ['method' => 'source_window', 'window' => $window,
                'from' => $old[$id]['source_line'].':'.$old[$id]['source_column'], 'to' => $position]];
            unset($targets[$window]);
        }
        return $pairs;
    }

    /** Pair repeated calls only if both inventories have the same distinct Blade roles. */
    private function bladeContextPairs(array $ids, array $positions, array $old, array $rows): array
    {
        if (count($ids) < 2 || count($ids) !== count($positions)) return [];
        $targets = [];
        foreach ($positions as $position) {
            $role = $rows[$position]['source_context']['context'] ?? null;
            if (!in_array($role, ['blade_echo', 'blade_bound_attribute'], true) || isset($targets[$role])) return [];
            $targets[$role] = $position;
        }
        $pairs = [];
        foreach ($ids as $id) {
            $role = $old[$id]['captured_context'] ?? null;
            if (!isset($targets[$role])) return [];
            $pairs[] = [$id, $targets[$role], ['method' => 'blade_context', 'context' => $role,
                'from' => $old[$id]['source_line'].':'.$old[$id]['source_column'], 'to' => $targets[$role]]];
            unset($targets[$role]);
        }
        return $pairs;
    }

    private function compare(array $old, array $rows, array $issues): array
    {
        $blocked = [];
        foreach ($issues as $issue) {
            $blocked[$issue['line'].':'.$issue['column']] = $issue['reason'];
        }
        $observations = [];
        $remaining = [];
        foreach ($rows as $row) {
            $position = $row['source_line'].':'.$row['source_column'];
            if (isset($remaining[$position])) {
                if (!$issues) throw new RuntimeException('Duplicate captured position');
                $blocked[$position] = 'duplicate_position';
            }
            $remaining[$position] = $row;
        }
        $ordered = array_values($remaining);
        usort($ordered, static fn ($a, $b) => [$a['source_line'], $a['source_column']] <=> [$b['source_line'], $b['source_column']]);
        $neighbours = [];
        foreach ($ordered as $index => $row) {
            if (isset($ordered[$index - 1]['raw_expression'], $ordered[$index + 1]['raw_expression'])) {
                $neighbours[$row['source_line'].':'.$row['source_column']] = [
                    $ordered[$index - 1]['raw_expression'], $ordered[$index + 1]['raw_expression'],
                ];
            }
        }
        $moves = [];
        $confirmed = [];
        $unmatched = [];
        foreach ($old as $finding) {
            $position = $finding['source_line'].':'.$finding['source_column'];
            if (isset($remaining[$position]) && $remaining[$position]['raw_expression'] === $finding['raw_expression']) {
                $confirmed[] = $finding['id'];
                $observations[$finding['id']] = ['from' => $position, 'to' => $position,
                    'state' => isset($blocked[$position]) ? 'review' : 'unchanged'];
                if (isset($blocked[$position])) $observations[$finding['id']]['reason'] = 'source_usage';
                unset($remaining[$position]);
            } elseif (isset($remaining[$position]) && !isset($blocked[$position])
                && $remaining[$position]['raw_source_code_literal'] !== null
                && $remaining[$position]['raw_source_code_literal'] === ($finding['raw_source_code_literal'] ?? null)
                && $remaining[$position]['raw_literal'] === $finding['raw_literal']
                && ($tokens = $this->staticCallTokens($remaining[$position]['raw_expression'])) !== []
                && $tokens === $this->staticCallTokens($finding['raw_expression'])) {
                $moves[] = ['predecessor' => $finding['id'], 'capture' => $remaining[$position],
                    'evidence' => ['method' => 'formatting_only', 'from' => $position, 'to' => $position]];
                $observations[$finding['id']] = ['from' => $position, 'to' => $position, 'state' => 'reformatted'];
                unset($remaining[$position]);
            } else {
                $unmatched[$finding['id']] = $finding;
                $observations[$finding['id']] = ['from' => $position, 'state' => 'review', 'reason' => 'no_match'];
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
        $ambiguous = false;
        $snapshot = null;
        foreach ($newExpressions as $expression => $positions) {
            $ids = $oldExpressions[$expression] ?? [];
            // Legacy captures can lack a window but still identify the exact file bytes.
            if (count($positions) > 1 && count($ids) <= count($positions)) {
                foreach ($ids as $id) {
                    $finding = $unmatched[$id];
                    if (!empty($finding['source_window']) || empty($finding['capture_hash'])) continue;
                    $snapshot ??= $this->committedSnapshot($finding['path']);
                    if (($snapshot['hash'] ?? null) === $finding['capture_hash']) {
                        $unmatched[$id]['source_window'] = self::sourceWindow(
                            $snapshot['lines'], $finding['source_line'], $finding['source_column']
                        );
                    }
                }
            }
            $pairs = $this->sourceWindowPairs($ids, $positions, $unmatched, $remaining) ?: $this->bladeContextPairs($ids, $positions, $unmatched, $remaining);
            if (count($ids) === 1 && count($positions) === 1) {
                $pairs[] = [$ids[0], $positions[0], null];
            } elseif ($ids !== [] && !$pairs) {
                $oldContexts = $newContexts = [];
                foreach ($ids as $id) {
                    $f = $unmatched[$id];
                    if (isset($f['previous_expression'], $f['next_expression'])) {
                        $signature = serialize([$f['previous_expression'], $f['next_expression']]);
                        $oldContexts[$signature][] = $id;
                    }
                }
                foreach ($positions as $position) {
                    if (isset($neighbours[$position])) $newContexts[serialize($neighbours[$position])][] = $position;
                }
                foreach ($oldContexts as $signature => $candidates) {
                    $targets = $newContexts[$signature] ?? [];
                    if (count($candidates) !== 1 || count($targets) !== 1) continue;
                    $id = $candidates[0];
                    $position = $targets[0];
                    $f = $unmatched[$id];
                    $pairs[] = [$id, $position, [
                        'method' => 'adjacent_calls', 'capture_hash' => $f['capture_hash'],
                        'from' => $observations[$id]['from'], 'to' => $position,
                        'previous_finding' => $f['previous_id'], 'next_finding' => $f['next_id'],
                        'previous_expression' => $f['previous_expression'], 'next_expression' => $f['next_expression'],
                    ]];
                }
            }
            // Apply the same one-to-one rule to what is left after proven pairs.
            // Otherwise the next identical scan would discover this move one pass later.
            $leftIds = array_values(array_diff($ids, array_column($pairs, 0)));
            $leftPositions = array_values(array_diff($positions, array_column($pairs, 1)));
            if (count($leftIds) === 1 && count($leftPositions) === 1) {
                $pairs[] = [$leftIds[0], $leftPositions[0], null];
                $leftPositions = [];
            }
            foreach ($pairs as [$id, $position, $evidence]) {
                unset($observations[$id]['reason']);
                if (isset($blocked[$position])) $observations[$id]['reason'] = 'source_usage';
                $observations[$id]['to'] = $position;
                $observations[$id]['state'] = isset($blocked[$position]) ? 'review' : 'unique_move';
                $move = ['predecessor' => $id, 'capture' => $remaining[$position]];
                if ($evidence !== null) $move['evidence'] = $evidence;
                $moves[] = $move;
                unset($unmatched[$id], $remaining[$position]);
            }
            $pending = array_values(array_filter($ids, fn ($id) => isset($unmatched[$id])));
            if ($pending === []) {
                unset($oldExpressions[$expression]);
            } elseif ($leftPositions !== []) {
                $ambiguous = true;
                foreach ($pending as $id) $observations[$id]['reason'] = 'ambiguous';
            }
        }

        if ($ambiguous) $issues[] = ['reason' => 'ambiguous_movement'];
        if ($issues) {
            // Keep unresolved predecessors in place. A move into one of those positions
            // must wait too, including when several proposed moves depend on each other.
            $held = [];
            $confirmed = array_values(array_filter($confirmed, fn ($id) => $observations[$id]['state'] === 'unchanged'));
            $moves = array_column($moves, null, 'predecessor');
            foreach ($observations as $id => $observation) {
                if ($observation['state'] === 'review') {
                    $held[$observation['from']] = true;
                    unset($moves[$id]);
                }
            }
            do {
                $previousCount = count($moves);
                foreach ($moves as $id => $move) {
                    if (isset($held[$observations[$id]['to']])) {
                        $observations[$id]['reason'] = 'occupied_destination';
                        $held[$observations[$id]['from']] = true;
                        unset($moves[$id]);
                    }
                }
            } while (count($moves) !== $previousCount);

            $created = [];
            foreach ($remaining as $position => $row) {
                if (!isset($blocked[$position]) && !isset($held[$position])
                    && !isset($oldExpressions[$row['raw_expression']])) {
                    $created[] = $row;
                }
            }
            $preserved = array_values(array_filter(array_column($old, 'id'),
                fn ($id) => isset($held[$observations[$id]['from']])));
            return ['state' => 'review', 'issues' => $issues, 'captures' => $rows,
                'preserved' => $preserved, 'position_preview' => $observations,
                'confirmed' => $confirmed, 'moved' => array_values($moves), 'created' => $created];
        }
        return ['state' => 'ready', 'confirmed' => $confirmed, 'moved' => $moves,
            'created' => array_values($remaining), 'removed' => array_keys($unmatched)];
    }
}
