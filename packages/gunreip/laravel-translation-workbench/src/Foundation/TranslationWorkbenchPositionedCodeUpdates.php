<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

/** Validate a complete occurrence map before replacing selected calls, from right to left. */
class TranslationWorkbenchPositionedCodeUpdates
{
    public function apply(Collection $selected, Collection $context, bool $write): array
    {
        $results = [];
        foreach ($selected->groupBy('source_path') as $path => $rows) {
            $absolute = base_path($path);
            $source = File::exists($absolute) ? File::get($absolute) : null;
            $edits = [];
            $valid = $source !== null;
            foreach ($rows->groupBy('raw_expression') as $expression => $expressionRows) {
                $calls = $source === null ? [] : app(TranslationWorkbenchScanner::class)->activeSourceCalls($source, $expression);
                $neighbors = $context->where('source_path', $path)->where('raw_expression', $expression);
                $mapped = [];
                foreach ($neighbors as $neighbor) {
                    if (! in_array($neighbor['state'], ['safe_update', 'already_current'], true)) { $valid = false; break; }
                    $matches = array_values(array_filter($calls, function ($call) use ($source, $neighbor) {
                        $prefix = substr($source, 0, $call['offset']);
                        $line = substr_count($prefix, "\n") + 1;
                        $newline = strrpos($prefix, "\n");
                        $column = $call['offset'] - ($newline === false ? -1 : $newline);
                        return $line === ($neighbor['source_line'] ?? null) && $column === ($neighbor['source_column'] ?? null);
                    }));
                    if (count($matches) !== 1 || isset($mapped[$matches[0]['offset']])) { $valid = false; break; }
                    $mapped[$matches[0]['offset']] = $neighbor['finding_id'];
                    if ($expressionRows->contains('finding_id', $neighbor['finding_id'])) {
                        $edits[] = ['offset' => $matches[0]['offset'], 'length' => strlen($matches[0]['expression']),
                            'replacement' => $neighbor['new_expression'], 'finding_id' => $neighbor['finding_id']];
                    }
                }
                if (count($mapped) !== count($calls) || $calls === []) { $valid = false; }
            }
            if (count($edits) !== $rows->count()) { $valid = false; }
            usort($edits, fn ($a, $b) => $b['offset'] <=> $a['offset']);
            $lastStart = PHP_INT_MAX;
            foreach ($edits as $edit) {
                if ($edit['offset'] + $edit['length'] > $lastStart) { $valid = false; }
                $lastStart = $edit['offset'];
            }
            $updated = $source;
            if ($valid) {
                foreach ($edits as $edit) {
                    $updated = substr_replace($updated, $edit['replacement'], $edit['offset'], $edit['length']);
                }
                if ($write) {
                    // Refuse a changed file rather than applying offsets from another revision.
                    if (! File::exists($absolute) || File::get($absolute) !== $source) { $valid = false; }
                    else { File::put($absolute, $updated); }
                }
            }
            foreach ($rows as $row) {
                $edit = collect($edits)->firstWhere('finding_id', $row['finding_id']);
                $newOffset = $valid ? $edit['offset'] + collect($edits)->where('offset', '<', $edit['offset'])
                    ->sum(fn ($earlier) => strlen($earlier['replacement']) - $earlier['length']) : null;
                $prefix = $valid ? substr($updated, 0, $newOffset) : '';
                $newline = strrpos($prefix, "\n");
                $results[$row['finding_id']] = [...$row,
                    'state' => $valid ? ($write ? 'applied' : 'would_apply') : 'duplicate_expression',
                    'reason' => $valid ? 'verified_source_position' : 'source_expression_has_competing_key_assignments',
                    'replacement_scope' => 'single_verified_source_position',
                    'source_offset' => $valid ? $edit['offset'] : null,
                    'source_length' => $valid ? $edit['length'] : null,
                    'updated_source_hash' => $valid ? hash('sha256', $updated) : null,
                    'updated_source_line' => $valid ? substr_count($prefix, "\n") + 1 : null,
                    'updated_source_column' => $valid ? $newOffset - ($newline === false ? -1 : $newline) : null];
            }
        }
        return $results;
    }
}
