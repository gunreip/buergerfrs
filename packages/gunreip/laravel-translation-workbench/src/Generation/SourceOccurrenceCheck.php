<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use RuntimeException;

/** Read-only position evidence. Text matches are candidates, never identity assignments. */
final class SourceOccurrenceCheck
{
    /** Compare all occurrences against one captured read per file, then verify those reads. */
    public function inspectGroup(array $findings, string $root, bool $includeChangedCalls = false): array
    {
        $files = [];
        foreach ($findings as $finding) {
            $relative = $finding['path'];
            if (array_key_exists($relative, $files)) {
                continue;
            }
            try {
                $path = (new SourceReplacementPreview)->path($root, $relative);
                $bytes = @file_get_contents($path);
                if ($bytes === false) {
                    throw new RuntimeException('Source file cannot be read.');
                }
                $files[$relative] = ['bytes' => $bytes, 'sha256' => hash('sha256', $bytes)];
            } catch (RuntimeException $error) {
                $files[$relative] = ['error' => $error->getMessage()];
            }
        }
        $results = [];
        foreach ($findings as $finding) {
            $file = $files[$finding['path']];
            $check = isset($file['error'])
                ? ['state' => 'source_unavailable', 'detail' => $file['error'], 'positions' => []]
                : $this->compare($finding, $file['bytes']);
            if ($includeChangedCalls && !isset($file['error'])) {
                $check = $this->includeChangedCalls($finding, $files[$finding['path']], $check);
            }
            $results[] = ['id' => (int) $finding['id'], 'path' => $finding['path'], ...$check];
        }
        foreach ($files as $relative => $file) {
            if (isset($file['error'])) {
                continue;
            }
            try {
                $path = (new SourceReplacementPreview)->path($root, $relative);
                $unchanged = @hash_file('sha256', $path) === $file['sha256'];
            } catch (RuntimeException) {
                $unchanged = false;
            }
            if (! $unchanged) {
                foreach ($results as &$result) {
                    if ($result['path'] === $relative) {
                        $result['state'] = 'source_changed';
                        $result['detail'] = 'The source file changed during inspection. Check it again.';
                        $result['positions'] = [];
                    }
                }
                unset($result);
            }
        }
        return $results;
    }

    public function inspect(array $finding, string $root, bool $includeChangedCalls = false): array
    {
        try {
            $path = (new SourceReplacementPreview)->path($root, $finding['path']);
        } catch (RuntimeException $error) {
            return ['state' => 'source_unavailable', 'detail' => $error->getMessage(), 'positions' => []];
        }
        $bytes = @file_get_contents($path);
        if ($bytes === false) {
            return ['state' => 'source_unavailable', 'detail' => 'Source file cannot be read.', 'positions' => []];
        }
        $result = $this->compare($finding, $bytes);
        $result['sha256'] = hash('sha256', $bytes);
        if ($includeChangedCalls) {
            $file = ['bytes' => $bytes, 'sha256' => $result['sha256']];
            $result = $this->includeChangedCalls($finding, $file, $result);
        }
        $lines = explode("\n", $bytes);
        $positions = [['line' => (int) $finding['source_line'], 'column' => (int) $finding['source_column'], 'captured' => true],
            ...array_slice($result['positions'], 0, 5)];
        $result['contexts'] = [];
        foreach ($positions as $position) {
            $start = max(0, $position['line'] - 3);
            $snippet = [];
            $displayLines = [];
            $remaining = empty($position['captured']) ? strlen((string) ($position['expression'] ?? $finding['raw_expression'])) : 0;
            foreach (array_slice($lines, $start, 5, true) as $index => $text) {
                $snippet[] = ($index + 1).': '.$text;
                $offset = $index + 1 === $position['line'] ? max(0, $position['column'] - 1) : 0;
                $length = $index + 1 >= $position['line'] ? min($remaining, max(0, strlen($text) - $offset)) : 0;
                $displayLines[] = ['number' => $index + 1, 'focus' => $index + 1 === $position['line'],
                    'before' => substr($text, 0, $offset), 'match' => substr($text, $offset, $length),
                    'after' => substr($text, $offset + $length)];
                if ($index + 1 >= $position['line'] && $remaining > 0) {
                    $remaining = max(0, $remaining - $length - 1);
                }
            }
            $result['contexts'][] = [...$position, 'text' => implode("\n", $snippet), 'lines' => $displayLines];
        }
        if (@hash_file('sha256', $path) !== $result['sha256']) {
            return ['state' => 'source_changed', 'detail' => 'The source file changed during inspection. Check it again.', 'positions' => []];
        }
        return $result;
    }

    private function includeChangedCalls(array $finding, array &$file, array $result): array
    {
        if ($result['state'] === 'position_confirmed' || !isset($finding['raw_source_code_literal'])) {
            return $result;
        }

        $function = trim(strstr($finding['raw_expression'], '(', true) ?: '');
        $adapter = new CopiedSourceCapture;
        $changed = [];
        $file['calls'] ??= app(TranslationWorkbenchScanner::class)->scanSource($finding['path'], $file['bytes']);
        foreach ($file['calls'] as $item) {
            if ($item->functionName !== $function || $item->rawExpression === $finding['raw_expression']) {
                continue;
            }
            $capture = $file['captures'][$item->meta['source_offset']] ??= $adapter->capture($item, $file['bytes'], $file['sha256'], []);
            if ($capture['raw_source_code_literal'] === $finding['raw_source_code_literal']
                && $capture['raw_literal'] === $finding['raw_literal']
                && $capture['raw_expression'] !== $finding['raw_expression']) {
                $before = $this->callStructure($finding['raw_expression']);
                $reformatted = $before !== null && $before === $this->callStructure($capture['raw_expression']);
                $changed[] = ['line' => $item->sourceLine, 'column' => $item->sourceColumn, 'expression' => $capture['raw_expression'],
                    'change' => $reformatted ? 'reformatted' : 'changed'];
            }
        }
        if ($changed) {
            $result['positions'] = [...$result['positions'], ...$changed];
            $result['state'] = 'changed_matches';
            $result['detail'] = 'Candidates use the same translation function and first argument. Compare their positions and calls below.';
        }
        return $result;
    }

    private function callStructure(string $expression): ?string
    {
        try {
            $php = preg_replace('/^@(lang|js)\s*\(/', '__(', $expression);
            $nodes = (new ParserFactory)->createForHostVersion()->parse('<?php '.$php.';');
            return (new NodeDumper)->dump($nodes);
        } catch (Error) {
            return null;
        }
    }

    public function compare(array $finding, string $bytes): array
    {
        $expression = (string) $finding['raw_expression'];
        $lines = explode("\n", $bytes);
        $line = (int) $finding['source_line'];
        $column = (int) $finding['source_column'];
        if ($expression !== '' && $line > 0 && $column > 0 && isset($lines[$line - 1]) && $column <= strlen($lines[$line - 1]) + 1) {
            $offset = array_sum(array_map('strlen', array_slice($lines, 0, $line - 1))) + $line - 1 + $column - 1;
            if (substr($bytes, $offset, strlen($expression)) === $expression) {
                return ['state' => 'position_confirmed', 'detail' => 'The captured expression matches at its recorded position.', 'positions' => []];
            }
        }
        $positions = [];
        if ($expression !== '') {
            $offset = 0;
            while (($at = strpos($bytes, $expression, $offset)) !== false) {
                $prefix = substr($bytes, 0, $at);
                $start = strrpos($prefix, "\n");
                $positions[] = ['line' => substr_count($prefix, "\n") + 1, 'column' => $start === false ? $at + 1 : $at - $start];
                $offset = $at + strlen($expression);
            }
        }
        return ['state' => match (count($positions)) { 0 => 'expression_missing', 1 => 'possible_move', default => 'ambiguous_matches' },
            'detail' => match (count($positions)) {
                0 => 'The captured expression was not found verbatim in this file. It may have changed, been removed or moved elsewhere.',
                1 => 'One exact text match exists at another position. This is a possible move, not a verified successor. Context and active-call checks are still required.',
                default => 'Several exact text matches exist. The original occurrence cannot be assigned to a new position from text alone.',
            }, 'positions' => $positions];
    }
}
