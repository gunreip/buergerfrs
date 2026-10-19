<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Illuminate\Support\Collection;

/** Match a whole file/group before writing; archived findings never own current occurrences. */
class TranslationWorkbenchOccurrenceMatcher
{
    public function compare(Collection $items, Collection $stored): array
    {
        $rows = [];
        $claimed = [];
        $reserved = [];
        $pending = [];
        $currentSignatures = $items->groupBy('sourceSignature');
        // Archived findings are evidence, never current occurrence candidates.
        $storedSignatures = $stored->where('status', '!=', 'obsolete')->groupBy('source_signature');
        $expressionKey = static fn ($path, $fingerprint) => json_encode([$path, $fingerprint], JSON_THROW_ON_ERROR);
        $storedExpressions = $stored->where('status', 'active')->groupBy(fn ($f) => $expressionKey($f->source_path, $f->expression_fingerprint));
        foreach ($items as $index => $item) {
            $exact = ($storedSignatures->get($item->sourceSignature) ?? collect());
            $historicalPosition = false;
            if ($exact->isEmpty()) {
                // Upgrade line-only historical identities only when that line has one matching call.
                $identity = $expressionKey($item->sourcePath, $item->expressionFingerprint);
                $sameLine = $items->filter(fn ($other) => $other->sourcePath === $item->sourcePath
                    && $other->sourceLine === $item->sourceLine && $other->expressionFingerprint === $item->expressionFingerprint
                    && $other->kind === $item->kind && $other->suggestedKey === $item->suggestedKey);
                $legacy = ($storedExpressions->get($identity) ?? collect())->filter(fn ($f) => ($f->source_column ?? null) === null
                    && $f->source_line === $item->sourceLine && $f->kind === $item->kind && ($f->suggested_key === $item->suggestedKey || TranslationWorkbenchSourceRelocation::matches($f, $item))
                    && ! isset($claimed[$f->id]));
                if ($sameLine->count() === 1 && $legacy->count() === 1) {
                    $exact = $legacy;
                    $historicalPosition = true;
                }
            }
            if ($exact->count() === 1 && $currentSignatures[$item->sourceSignature]->count() === 1) {
                $finding = $exact->first();
                $claimed[$finding->id] = true;
                $state = $finding->status === 'active' ? ($historicalPosition ? 'position_refined' : 'unchanged') : 'reappeared';
                if (($item->meta['source_context'] ?? '') === 'commented_out' || ($item->kind === 'literal' && trim((string) $item->literalText) === '')) {
                    $state = 'ignored';
                }
                $rows[] = $this->row($state, $item, [$finding]);
            } else {
                $pending[] = $item;
            }
        }
        $pendingExpressions = collect($pending)->groupBy(fn ($i) => $expressionKey($i->sourcePath, $i->expressionFingerprint));
        foreach ($pending as $item) {
            $exact = ($storedSignatures->get($item->sourceSignature) ?? collect());
            if ($exact->isNotEmpty() || $currentSignatures[$item->sourceSignature]->count() > 1) {
                // A duplicate current signature cannot identify an old position either.
                $identity = $expressionKey($item->sourcePath, $item->expressionFingerprint);
                $possible = $exact->merge(($storedExpressions->get($identity) ?? collect())
                    ->filter(fn ($f) => $f->kind === $item->kind && ($f->suggested_key === $item->suggestedKey || TranslationWorkbenchSourceRelocation::matches($f, $item)) && ! isset($claimed[$f->id])))
                    ->unique('id');
                foreach ($possible as $finding) {
                    $reserved[$finding->id] = true;
                }
                $rows[] = $this->row('ambiguous', $item, $possible->all());
                continue;
            }
            // Match remaining occurrences only after unchanged active positions are claimed.
            $identity = $expressionKey($item->sourcePath, $item->expressionFingerprint);
            $candidates = ($storedExpressions->get($identity) ?? collect())
                ->filter(fn ($f) => $f->kind === $item->kind && ($f->suggested_key === $item->suggestedKey || TranslationWorkbenchSourceRelocation::matches($f, $item)) && ! isset($claimed[$f->id]));
            $current = $pendingExpressions->get($identity) ?? collect();
            $ignored = ($item->meta['source_context'] ?? '') === 'commented_out'
                || ($item->kind === 'literal' && trim((string) $item->literalText) === '');
            if (! $ignored && $candidates->count() === 1 && $current->count() === 1 && ! isset($claimed[$candidates->first()->id])) {
                $finding = $candidates->first();
                $claimed[$finding->id] = true;
                $rows[] = $this->row('moved', $item, [$finding]);
            } elseif ($candidates->isNotEmpty()) {
                foreach ($candidates as $f) {
                    $reserved[$f->id] = true;
                }
                $rows[] = $this->row('ambiguous', $item, $candidates->all());
            } else {
                $rows[] = $this->row($ignored ? 'ignored' : 'new', $item, []);
            }
        }
        foreach ($stored as $finding) {
            if ($finding->status === 'active' && ! isset($claimed[$finding->id]) && ! isset($reserved[$finding->id])) {
                $rows[] = [
                    'state' => 'missing', 'path' => $finding->source_path,
                    'old_line' => $finding->source_line, 'line' => null,
                    'finding_ids' => [$finding->id], 'scanned_key' => null,
                ];
            }
        }
        return $rows;
    }

    private function row(string $state, object $item, array $findings): array
    {
        return [
            'state' => $state, 'path' => $item->sourcePath, 'source_signature' => $item->sourceSignature,
            'old_line' => count($findings) === 1 ? $findings[0]->source_line : null,
            'line' => $item->sourceLine, 'finding_ids' => array_map(fn ($f) => $f->id, $findings),
            'scanned_key' => $item->translationKey,
            'position' => ['source_line' => $item->sourceLine, 'source_column' => $item->sourceColumn, 'source_signature' => $item->sourceSignature, 'source_fingerprint' => $item->sourceFingerprint],
        ];
    }
}
