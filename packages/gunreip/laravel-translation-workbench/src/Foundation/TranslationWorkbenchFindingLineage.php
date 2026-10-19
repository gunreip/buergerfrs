<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Illuminate\Support\Facades\DB;

/** Read-only source-position navigation; never changes finding or key assignments. */
class TranslationWorkbenchFindingLineage
{
    public function rows(?int $findingId): array
    {
        if (! $findingId || ! ($finding = TranslationWorkbenchFinding::find($findingId))) { return []; }
        $meta = $finding->meta ?? [];
        if (! isset($meta['predecessor_finding_id']) && ! isset($meta['successor_finding_id'])) { return []; }
        $members = [];
        $previous = TranslationWorkbenchFinding::find($meta['predecessor_finding_id'] ?? 0);
        if ($previous && $previous->status === 'obsolete' && ($previous->meta['successor_finding_id'] ?? null) === $finding->id) {
            $members[] = ['role' => 'predecessor', 'finding' => $previous];
        }
        $members[] = ['role' => 'selected', 'finding' => $finding];
        $next = TranslationWorkbenchFinding::find($meta['successor_finding_id'] ?? 0);
        if ($next && $finding->status === 'obsolete' && ($next->meta['predecessor_finding_id'] ?? null) === $finding->id) {
            $members[] = ['role' => 'successor', 'finding' => $next];
        }
        return array_map(function ($member) {
            $row = $member['finding'];
            return ['role' => $member['role'], 'id' => $row->id, 'status' => $row->status,
                'path' => $row->sourceFile?->path, 'line' => $row->source_line, 'column' => $row->source_column,
                'keys' => DB::table('translation_workbench_key_findings')->where('finding_id', $row->id)
                    ->orderBy('key_id')->get(['key_id', 'status'])->map(fn ($link) => (array) $link)->all()];
        }, $members);
    }
}
