<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchSourceFile;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\DB;

/** Read-only source/database comparison; never infers identity from key text alone. */
class TranslationWorkbenchSourceReconciliation
{
    public function __construct(private readonly TranslationWorkbenchScanner $scanner) {}

    public function plan(?array $paths = null): array
    {
        $scan = $this->scanner->scanWithManifest($paths, TranslationWorkbenchSourceFile::query()->pluck('path'));
        $stored = DB::table('translation_workbench_findings as f')
            ->join('translation_workbench_source_files as s', 's.id', '=', 'f.source_file_id')
            ->whereIn('s.path', $scan['reconciliation_paths'])
            ->orderBy('f.id')->get(['f.*', 's.path as source_path']);
        $rows = app(TranslationWorkbenchOccurrenceMatcher::class)->compare($scan['items'], $stored);
        $ids = collect($rows)->pluck('finding_ids')->flatten()->unique();
        $links = DB::table('translation_workbench_key_findings as l')
            ->join('translation_workbench_keys as k', 'k.id', '=', 'l.key_id')
            ->whereIn('l.finding_id', $ids)->where('l.status', 'active')->orderBy('k.id')
            ->get(['l.finding_id', 'k.id', 'k.translation_key', 'k.review_status', 'k.status'])
            ->groupBy('finding_id');
        foreach ($rows as &$row) {
            $row['assignments'] = collect($row['finding_ids'])->flatMap(fn ($id) => ($links->get($id) ?? collect())->map(fn ($link) => (array) $link))->all();
            $row['assignment_issue'] = match (true) {
                collect($row['assignments'])->contains(fn ($a) => $a['status'] === 'obsolete') => 'active_link_to_obsolete_key',
                collect($row['assignments'])->groupBy('finding_id')->contains(fn ($group) => $group->count() > 1) => 'multiple_active_assignments',
                $row['finding_ids'] !== [] && $row['assignments'] === [] => 'no_active_assignment',
                default => null,
            };
            // A code/key difference is information, not authorization to overwrite a reviewed key.
            $row['reviewed_key_differs_from_code'] = $row['scanned_key'] !== null && collect($row['assignments'])->contains(
                fn ($a) => $a['review_status'] === 'reviewed' && $a['translation_key'] !== $row['scanned_key']
            );
        }
        unset($row);

        return [
            'read_only' => true,
            'scope' => ['scanned_paths' => $scan['scanned_paths'], 'missing_paths' => $scan['missing_paths']],
            'summary' => collect($rows)->countBy('state')->all(),
            'rows' => $rows,
            'limitations' => ['Cross-file moves are not inferred.', 'Scanner line/signature granularity is retained; indistinguishable occurrences require review.', 'Differences in reviewed keys are not automatically corrected.'],
        ];
    }

}
