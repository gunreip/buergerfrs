<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/** Current eligibility, independent of UI filters and historical shared-key suggestions. */
class TranslationWorkbenchSharedLiteralCandidates
{
    public function groups(): Builder
    {
        $literal = app(TranslationWorkbenchBulkKeyWriter::class)->bulkLiteralSqlExpression('duplicate_findings');

        return DB::table('translation_workbench_findings as duplicate_findings')
            ->join('translation_workbench_source_files as duplicate_source_files', 'duplicate_source_files.id', '=', 'duplicate_findings.source_file_id')
            ->where('duplicate_findings.status', 'active')
            ->where('duplicate_source_files.status', 'active')
            ->whereRaw("NULLIF(BTRIM(COALESCE(duplicate_findings.literal_text, duplicate_findings.literal_text_suggested, '')), '') IS NOT NULL")
            ->whereRaw("NULLIF(BTRIM(duplicate_findings.suggested_key), '') IS NOT NULL")
            ->selectRaw($literal.' as bulk_literal')
            ->selectRaw('COUNT(*) as bulk_literal_count')
            ->selectRaw('COUNT(DISTINCT BTRIM(duplicate_findings.suggested_key)) as suggested_key_count')
            ->groupByRaw($literal)
            ->havingRaw('COUNT(DISTINCT BTRIM(duplicate_findings.suggested_key)) >= 2');
    }
}
