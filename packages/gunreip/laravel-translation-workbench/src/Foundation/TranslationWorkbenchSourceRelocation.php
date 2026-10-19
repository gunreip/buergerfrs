<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyFinding;
use Gunreip\TranslationWorkbench\Scanner\DiscoveredTranslation;

/** A reviewed file move preserves ownership; matching text alone never confirms a move. */
class TranslationWorkbenchSourceRelocation
{
    public static function matches(object $finding, DiscoveredTranslation $item): bool
    {
        $meta = is_string($finding->meta ?? null) ? json_decode($finding->meta, true) : ($finding->meta ?? []);
        $move = $meta['source_relocation'] ?? [];

        return ($move['to_path'] ?? null) === $item->sourcePath
            && ($move['expression_fingerprint'] ?? null) === $item->expressionFingerprint
            && ($move['from_path'] ?? '') !== ''
            && $finding->kind === $item->kind;
    }

    public static function assignedKey(TranslationWorkbenchFinding $finding): TranslationWorkbenchKey
    {
        $links = TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->where('status', 'active')->get();
        $key = $links->count() === 1 ? TranslationWorkbenchKey::find($links->sole()->key_id) : null;
        if (! $key || $key->status === 'obsolete') {
            throw new \RuntimeException('Reviewed source relocation requires one current key assignment for finding #'.$finding->id.'.');
        }

        return $key;
    }
}
