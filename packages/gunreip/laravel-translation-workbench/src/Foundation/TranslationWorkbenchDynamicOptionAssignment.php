<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Illuminate\Support\Collection;

/** Read-only migration preflight; suggestions never authorize a key assignment. */
class TranslationWorkbenchDynamicOptionAssignment
{
    public function plan(Collection $discoveries): array
    {
        $paths = $discoveries->pluck('source_path')->unique()->values()->all();
        $findings = TranslationWorkbenchFinding::query()
            ->where('status', 'active')
            ->whereHas('sourceFile', fn ($query) => $query->whereIn('path', $paths)->where('status', 'active'))
            ->with(['sourceFile', 'keys' => fn ($query) => $query
                ->where('translation_workbench_key_findings.status', 'active')
                ->where('translation_workbench_keys.status', '<>', 'obsolete')])
            ->orderBy('id')->get()->groupBy(fn ($finding) => $finding->sourceFile->path);
        $rows = $discoveries->map(function (array $discovery) use ($findings): array {
            $start = (int) $discovery['source_line'];
            $end = (int) ($discovery['source_end_line'] ?? $start);
            $candidates = $findings->get($discovery['source_path'], collect())
                ->filter(function ($finding) use ($discovery, $start, $end): bool {
                    $context = $finding->meta['dynamic_option_context'] ?? [];

                    return $finding->source_line !== null && $finding->source_line >= $start && $finding->source_line <= $end
                        && ($context['options_variable'] ?? null) === $discovery['options_variable']
                        && ($context['key_variable'] ?? null) === $discovery['key_variable']
                        && ($context['label_variable'] ?? null) === $discovery['label_variable']
                        && ($context['argument_variable_role'] ?? null) === 'label'
                        && ($context['source_expression_type'] ?? null) === ($discovery['source_expression_type'] ?? 'direct_variable')
                        && (($discovery['source_expression_type'] ?? null) !== 'indexed_option'
                            || ($finding->meta['option_owner_key'] ?? null) === ($discovery['option_owner_key'] ?? null));
                })->values();
            $keyIds = $candidates->flatMap(fn ($finding) => $finding->keys->pluck('id'))->unique()->values()->all();
            $ownerKey = $candidates->count() === 1 ? ($candidates->sole()->meta['option_owner_key'] ?? null) : null;
            $status = match (true) {
                ($discovery['source_resolution'] ?? null) === 'ambiguous_source' => 'ambiguous_source',
                $candidates->isEmpty() => 'missing_finding',
                $candidates->count() > 1 => 'ambiguous_finding',
                count($keyIds) === 0 => 'missing_key',
                count($keyIds) > 1 => 'ambiguous_key',
                $ownerKey !== null && $candidates->sole()->keys->sole()->translation_key !== $ownerKey => 'owner_key_mismatch',
                default => 'matched',
            };

            return [
                'source_path' => $discovery['source_path'], 'source_line' => $start, 'source_end_line' => $end,
                'options_variable' => $discovery['options_variable'], 'suggested_key' => $discovery['suggested_key'],
                'status' => $status,
                'source_resolution' => $discovery['source_resolution'] ?? null,
                'source_candidates' => $discovery['source_candidates'] ?? [],
                'candidate_finding_ids' => $candidates->pluck('id')->all(), 'candidate_key_ids' => $keyIds,
                'finding_id' => $status === 'matched' ? $candidates->sole()->id : null,
                'key_id' => $status === 'matched' ? $keyIds[0] : null,
            ];
        })->values();

        return [
            'mode' => 'read_only_preflight',
            'summary' => array_replace(array_fill_keys(['matched', 'missing_finding', 'ambiguous_finding', 'missing_key', 'ambiguous_key', 'owner_key_mismatch', 'ambiguous_source'], 0), $rows->countBy('status')->all()),
            'assignments' => $rows->all(),
        ];
    }
}
