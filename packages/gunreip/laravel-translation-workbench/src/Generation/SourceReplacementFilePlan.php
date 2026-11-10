<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use RuntimeException;

/** Compose all selected edits against the same original bytes and recapture affected calls once. */
final class SourceReplacementFilePlan
{
    public function build(string $path, string $before, array $rows, array $previews, TranslationWorkbenchScanner $scanner): array
    {
        $selected = array_column($previews, null, 'finding');
        $edits = array_column($previews, 'edit');
        usort($edits, fn ($a, $b) => $a['offset'] <=> $b['offset']);
        $end = -1;
        foreach ($edits as $edit) {
            if ($edit['offset'] < $end) throw new RuntimeException('Selected source edits overlap. Review them separately.');
            $end = $edit['offset'] + $edit['length'];
        }
        $after = $before;
        foreach (array_reverse($edits) as $edit) {
            $after = substr_replace($after, $edit['replacement'], $edit['offset'], $edit['length']);
        }
        $lines = explode("\n", $before);
        $scanned = $scanner->scanSource($path, $after)->groupBy(fn ($item) => $item->meta['source_offset'] ?? -1);
        $transitions = [];
        foreach ($rows as $row) {
            $id = (int) $row['id'];
            $line = (int) $row['source_line'];
            $column = (int) $row['source_column'];
            $offset = array_sum(array_map('strlen', array_slice($lines, 0, $line - 1))) + $line - 1 + $column - 1;
            if ($line < 1 || $column < 1 || ! isset($lines[$line - 1]) || $column > strlen($lines[$line - 1]) + 1
                || $row['raw_expression'] === '' || substr($before, $offset, strlen($row['raw_expression'])) !== $row['raw_expression']) {
                throw new SourceScanRequired($id, $path, $line, $column);
            }
            $preview = $selected[$id] ?? null;
            $newOffset = $offset;
            foreach ($edits as $edit) {
                $ownEdit = $preview !== null && $preview['edit'] === $edit;
                if (! $ownEdit && $offset < $edit['offset'] + $edit['length'] && $offset + strlen($row['raw_expression']) > $edit['offset']) {
                    throw new RuntimeException('Another stored call overlaps the edited argument: finding #'.$id.'.');
                }
                if ($edit['offset'] + $edit['length'] <= $offset) $newOffset += strlen($edit['replacement']) - $edit['length'];
            }
            $prefix = substr($after, 0, $newOffset);
            $newLine = substr_count($prefix, "\n") + 1;
            $lastBreak = strrpos($prefix, "\n");
            $newColumn = $newOffset - ($lastBreak === false ? -1 : $lastBreak);
            if ($preview === null && $newLine === $line && $newColumn === $column) continue;
            $matches = $scanned->get($newOffset, collect());
            if ($matches->count() !== 1) throw new RuntimeException('The changed call cannot be uniquely recaptured by the scanner: #'.$id.'.');
            $evidence = $preview === null ? [] : [$preview['key'] => ['value' => $preview['published_value'],
                'provenance' => ['path' => $preview['language_path'], 'sha256' => $preview['language_sha256'], 'locale' => $preview['source_locale']]]];
            $capture = (new CopiedSourceCapture)->capture($matches->first(), $after, hash('sha256', $after), $evidence);
            if ($capture['source_context']['state'] !== 'confirmed_call') throw new RuntimeException('Scanner did not confirm an active source call.');
            if ($preview !== null && $capture['found_translation_key'] !== $preview['key']) throw new RuntimeException('Scanner did not confirm the proposed key call.');
            if ($preview === null && ($capture['raw_expression'] !== $row['raw_expression'] || $capture['raw_literal'] !== $row['raw_literal'] || $capture['raw_source_code_literal'] !== $row['raw_source_code_literal'])) {
                throw new RuntimeException('Moved finding content changed: #'.$id.'.');
            }
            $transitions[] = ['predecessor' => $id, 'before_expression' => $row['raw_expression'],
                'key' => $row['key_id'] === null ? null : (int) $row['key_id'], 'movement' => $preview === null, 'capture' => $capture];
        }
        if (count(array_filter($transitions, fn ($t) => !$t['movement'])) !== count($previews)) throw new RuntimeException('Not all selected findings were recaptured.');
        usort($transitions, fn ($a, $b) => [$a['movement'], $a['predecessor']] <=> [$b['movement'], $b['predecessor']]);
        return ['after' => $after, 'transitions' => $transitions];
    }
}
