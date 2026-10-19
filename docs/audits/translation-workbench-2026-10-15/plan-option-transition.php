<?php

// Read-only evidence, not an apply script. Never invokes the option collector/providers.
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\DB;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$pdo = DB::connection()->getPdo();
if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
    throw new RuntimeException('PostgreSQL READ ONLY transaction required.');
}
$pdo->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
try {
    $observations = TranslationWorkbenchOptionDiscovery::where('status', '<>', 'obsolete')->orderBy('id')->get();
    $manifest = app(TranslationWorkbenchScanner::class)->scanWithManifest($observations->pluck('source_path')->unique()->all());
    $findings = TranslationWorkbenchFinding::where('status', 'active')
        ->whereHas('sourceFile', fn ($q) => $q->where('status', 'active')->whereIn('path', $observations->pluck('source_path')))
        ->with(['sourceFile', 'keys' => fn ($q) => $q->where('translation_workbench_key_findings.status', 'active')->where('translation_workbench_keys.status', '<>', 'obsolete')])->get();
    $sources = DB::table('translation_workbench_dynamic_sources')->whereIn('option_discovery_id', $observations->pluck('id'))->get();
    $previousFindings = TranslationWorkbenchFinding::with('sourceFile')->whereIn('id', $sources->pluck('finding_id')->filter())->get()->keyBy('id');
    $rows = [];
    foreach ($observations as $observation) {
        // Historic line numbers and suggested keys cannot establish identity after edits.
        $calls = $manifest['items']->filter(function ($item) use ($observation) {
            $context = $item->meta['dynamic_option_context'] ?? [];
            return $item->sourcePath === $observation->source_path
                && ($item->meta['source_context'] ?? null) !== 'commented_out'
                && ($item->meta['translation_relevant'] ?? true) !== false
                && isset($item->meta['option_owner_key'])
                && ($context['options_variable'] ?? null) === $observation->options_variable
                && ($context['key_variable'] ?? null) === $observation->key_variable
                && ($context['label_variable'] ?? null) === $observation->label_variable;
        })->values();
        $candidates = $calls->map(function ($item) use ($findings) {
            $stored = $findings->filter(fn ($finding) => $finding->sourceFile->path === $item->sourcePath
                && $finding->raw_expression === $item->rawExpression)->values();
            $owner = $item->meta['option_owner_key'];
            $matching = $stored->count() === 1 && $stored->sole()->keys->count() === 1
                && $stored->sole()->keys->sole()->translation_key === $owner;
            return ['line' => $item->sourceLine, 'owner_key' => $owner,
                'context' => $item->meta['dynamic_option_context'],
                'finding_ids' => $stored->pluck('id')->all(),
                'key_ids' => $stored->flatMap(fn ($finding) => $finding->keys->pluck('id'))->unique()->values()->all(),
                'exact_active_finding_and_key' => $matching,
                'stored_context_matches' => $stored->count() === 1 && ($stored->sole()->meta['dynamic_option_context'] ?? null) === $item->meta['dynamic_option_context']];
        });
        $status = match (true) {
            ! in_array($observation->source_path, $manifest['scanned_paths'], true) => 'path_not_scanned',
            $calls->isEmpty() => 'no_supported_explicit_call',
            $calls->count() > 1 => 'ambiguous_current_calls',
            ! $candidates->sole()['exact_active_finding_and_key'] => 'finding_or_key_needs_review',
            ! $candidates->sole()['stored_context_matches'] => 'finding_context_refresh_required',
            default => 'finding_link_verified_source_review_required',
        };
        $rows[] = ['observation_id' => $observation->id, 'source_path' => $observation->source_path,
            'historic_line' => $observation->source_line, 'options_variable' => $observation->options_variable,
            'options_count' => $observation->options_count, 'status' => $status,
            'current_calls' => $candidates->all(),
            'linked_sources' => $sources->where('option_discovery_id', $observation->id)->map(function ($source) use ($candidates, $previousFindings, $observation) {
                $previous = $previousFindings->get($source->finding_id);
                $target = $candidates->count() === 1 && $candidates->sole()['exact_active_finding_and_key'] ? $candidates->sole() : null;
                $previousContext = $previous?->meta['dynamic_option_context'] ?? null;
                $sameContext = $target !== null && is_array($previousContext);
                foreach (['options_variable', 'key_variable', 'label_variable', 'argument_variable_role', 'source_expression_type'] as $field) {
                    $sameContext = $sameContext && isset($previousContext[$field], $target['context'][$field])
                        && $previousContext[$field] === $target['context'][$field];
                }
                $reason = match (true) {
                    $target === null => 'no_verified_current_target',
                    $previous === null => 'missing_previous_finding',
                    $previous->sourceFile?->path !== $observation->source_path => 'different_source_file',
                    ! in_array($source->key_id, $target['key_ids'], true) => 'different_key',
                    in_array($source->finding_id, $target['finding_ids'], true) => 'same_finding',
                    ! is_array($previousContext) => 'previous_option_context_missing',
                    ! $sameContext => 'different_option_context',
                    default => 'same_option_context_changed_expression',
                };
                return [
                'id' => $source->id, 'status' => $source->status, 'finding_id' => $source->finding_id, 'key_id' => $source->key_id,
                'source_reference' => $source->source_reference, 'source_type' => $source->source_type,
                'identity_review' => $reason,
                'previous_finding' => $previous === null ? null : [
                    'status' => $previous->status, 'source_path' => $previous->sourceFile?->path,
                    'source_line' => $previous->source_line, 'raw_expression' => $previous->raw_expression,
                    'found_translation_key' => $previous->found_translation_key, 'meta' => $previous->meta,
                ],
                'matches_current_call' => $candidates->count() === 1
                    && $candidates->sole()['exact_active_finding_and_key']
                    && in_array($source->finding_id, $candidates->sole()['finding_ids'], true)
                    && in_array($source->key_id, $candidates->sole()['key_ids'], true),
            ]; })->values()->all()];
    }
    $report = ['captured_at' => date(DATE_ATOM), 'mode' => 'read_only_evidence_no_assignment',
        'transaction' => 'REPEATABLE READ READ ONLY', 'file_hashes' => $manifest['file_hashes'],
        'summary' => collect($rows)->countBy('status')->all(),
        'active_linked_sources' => collect($rows)->flatMap(fn ($row) => $row['linked_sources'])->where('status', 'active')->count(),
        'active_linked_sources_needing_review' => collect($rows)->flatMap(fn ($row) => $row['linked_sources'])->where('status', 'active')->where('matches_current_call', false)->count(),
        'active_source_identity_summary' => collect($rows)->flatMap(fn ($row) => $row['linked_sources'])->where('status', 'active')->countBy('identity_review')->all(),
        'observations' => $rows];
} finally {
    $pdo->exec('ROLLBACK');
}
file_put_contents(__DIR__.'/option-transition-plan.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
echo json_encode($report['summary']).PHP_EOL;
