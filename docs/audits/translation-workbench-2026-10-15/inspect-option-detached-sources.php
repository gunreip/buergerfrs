<?php

// Read-only review of historical sources whose own finding differs from their observation.
// Uses source parsing and two previously audited config readers; never runs providers.
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptions;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\DB;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$plan = json_decode(file_get_contents(__DIR__.'/option-transition-plan.json'), true, flags: JSON_THROW_ON_ERROR);
$evidence = collect($plan['observations'])->flatMap(fn ($row) => $row['linked_sources'])
    ->where('status', 'active')->whereIn('identity_review', ['different_source_file', 'previous_option_context_missing']);
$pdo = DB::connection()->getPdo();
if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
    throw new RuntimeException('PostgreSQL READ ONLY transaction required.');
}
$pdo->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
try {
    $collector = app(TranslationWorkbenchDynamicOptionCollector::class);
    $arrays = (new ReflectionMethod($collector, 'hardcodedPublicArrayOptions'))->invoke($collector);
    foreach (['roleBadgeColorOptions' => ['roleUserManagementBadgeColors', 'colors'], 'roleBadgeVariantOptions' => ['roleUserManagementBadgeVariants', 'variants']] as $name => [$method, $leaf]) {
        $arrays->push(['name' => $name, 'source_file' => 'app/Livewire/Admin/RoleList.php:'.$method,
            'options' => config('buergerfrs-icons.categories.role_user_management.badge.'.$leaf, [])]);
    }
    $groups = $arrays->groupBy('name')->map(fn ($items) => [
        'source_file' => $items->pluck('source_file')->implode(', '),
        'options' => $items->count() === 1 ? $items->sole()['options'] : [],
        'source_candidates' => $items->values()->all(),
        'source_resolution' => $items->count() === 1 ? 'single_candidate' : 'ambiguous_source',
    ]);
    $normalize = static function (array $values): array { ksort($values); return $values; };
    $rows = [];
    foreach ($evidence as $prior) {
        $source = DB::table('translation_workbench_dynamic_sources')->find($prior['id']);
        if (! $source || $source->status !== 'active' || $source->finding_id !== $prior['finding_id'] || $source->key_id !== $prior['key_id']) {
            throw new RuntimeException('Refresh source plan for '.$prior['id']);
        }
        $old = TranslationWorkbenchFinding::with('sourceFile')->findOrFail($source->finding_id);
        $path = $old->sourceFile->path;
        $manifest = app(TranslationWorkbenchScanner::class)->scanWithManifest([$path]);
        if (! in_array($path, $manifest['scanned_paths'], true)) {
            throw new RuntimeException('Historical source path not scanned: '.$path);
        }
        $context = $old->meta['dynamic_option_context'] ?? null;
        $contextBasis = 'stored_previous_context';
        if ($context === null && preg_match('/^__\(\$(\w+)\[\$(\w+)\]\s*\?\?\s*\$\w+\)$/', $old->raw_expression, $match)) {
            $context = ['options_variable' => $match[1], 'key_variable' => $match[2], 'label_variable' => '',
                'source_expression_type' => 'indexed_option'];
            $contextBasis = 'restricted_legacy_indexed_expression';
        }
        $calls = $manifest['items']->filter(function ($item) use ($context) {
            if (! is_array($context) || ! isset($item->meta['option_owner_key'])
                || ($item->meta['source_context'] ?? null) === 'commented_out'
                || ($item->meta['translation_relevant'] ?? true) === false) { return false; }
            $current = $item->meta['dynamic_option_context'] ?? [];
            foreach (['options_variable', 'key_variable', 'label_variable', 'source_expression_type'] as $field) {
                if (($context[$field] ?? null) !== ($current[$field] ?? null)) { return false; }
            }
            return true;
        })->values();
        $discoveries = (new ReflectionMethod($collector, 'discoveries'))->invoke($collector, [$path], $groups);
        $targets = [];
        foreach ($calls as $call) {
            $findings = TranslationWorkbenchFinding::where('status', 'active')
                ->whereHas('sourceFile', fn ($q) => $q->where('path', $path)->where('status', 'active'))
                ->where('raw_expression', $call->rawExpression)
                ->with(['keys' => fn ($q) => $q->where('translation_workbench_key_findings.status', 'active')->where('translation_workbench_keys.status', '<>', 'obsolete')])->get();
            $matching = $findings->count() === 1 && $findings->sole()->keys->count() === 1
                && $findings->sole()->keys->sole()->id === $source->key_id
                && $findings->sole()->keys->sole()->translation_key === $call->meta['option_owner_key'];
            $observations = $discoveries->filter(fn ($row) => $row['options_variable'] === $context['options_variable']
                && $row['key_variable'] === $context['key_variable'] && $row['label_variable'] === $context['label_variable']
                && $call->sourceLine >= $row['source_line'] && $call->sourceLine <= $row['source_end_line'])->values();
            $current = $observations->count() === 1 ? $observations->sole() : null;
            $values = DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $source->id)->where('status', 'active')->pluck('native_label', 'value_key')->all();
            $targets[] = ['line' => $call->sourceLine, 'expression' => $call->rawExpression, 'owner_key' => $call->meta['option_owner_key'],
                'finding_ids' => $findings->pluck('id')->all(), 'same_active_key' => $matching,
                'source_resolution' => $current['source_resolution'] ?? null, 'selected_source' => $current['source_reference'] ?? null,
                'options_count' => count($current['options'] ?? []),
                'values_identical' => $current !== null && $current['options'] !== [] && $normalize($current['options']) === $normalize($values),
                'existing_observation_ids' => DB::table('translation_workbench_option_discoveries')->where('status', '<>', 'obsolete')->where('source_path', $path)
                    ->where('options_variable', $context['options_variable'])->where('key_variable', $context['key_variable'])->where('label_variable', $context['label_variable'])->pluck('id')->all(),
                'selected_source_sha256' => $current !== null && is_file($root.'/'.explode(':', $current['source_reference'])[0])
                    ? hash_file('sha256', $root.'/'.explode(':', $current['source_reference'])[0]) : null,
                'editor_baseline_en_de' => $matching ? app(TranslationWorkbenchDynamicOptions::class)->rows($source->key_id, $findings->sole()->id, ['source' => 'en', 'active' => 'de']) : [],
                'linked_runtime_source_ids' => $matching ? app(TranslationWorkbenchDynamicOptions::class)->linkedRuntimeSourceIdsForFinding((object) ['id' => $findings->sole()->id, 'key_id' => $source->key_id]) : [],
                'existing_target_source_ids' => DB::table('translation_workbench_dynamic_sources')->whereIn('finding_id', $findings->pluck('id'))->where('status', '<>', 'obsolete')->pluck('id')->all(),
            ];
        }
        $links = DB::table('translation_workbench_dynamic_source_candidates')->where('dynamic_source_id', $source->id)
            ->orWhere('candidate_reference', 'dynamic_source:'.$source->id)->orWhere('meta->related_source_id', $source->id)->get();
        $rows[] = ['source_id' => $source->id, 'previous_finding_id' => $source->finding_id, 'previous_finding_status' => $old->status,
            'previous_expression' => $old->raw_expression, 'source_path' => $path, 'file_sha256' => $manifest['file_hashes'][$path],
            'historical_observation_id' => $source->option_discovery_id, 'key_id' => $source->key_id, 'context_basis' => $contextBasis,
            'targets' => $targets,
            'status' => count($targets) === 1 && $targets[0]['same_active_key'] && $targets[0]['values_identical']
                && $targets[0]['source_resolution'] === 'view_bound_candidate' && $targets[0]['existing_target_source_ids'] === []
                    ? 'target_verified_observation_rebinding_required' : 'needs_review',
            'candidate_links' => $links->map(fn ($r) => ['id' => $r->id, 'dynamic_source_id' => $r->dynamic_source_id, 'finding_id' => $r->finding_id,
                'reference' => $r->candidate_reference, 'status' => $r->status, 'review_status' => $r->review_status])->all(),
            'translation_rows_for_shared_key' => DB::table('translation_workbench_dynamic_key_values')->where('key_id', $source->key_id)->count()];
    }
    $report = ['captured_at' => date(DATE_ATOM), 'mode' => 'read_only_no_assignment', 'transaction' => 'REPEATABLE READ READ ONLY',
        'plan_sha256' => hash_file('sha256', __DIR__.'/option-transition-plan.json'),
        'summary' => collect($rows)->countBy('status')->all(), 'sources' => $rows];
} finally {
    $pdo->exec('ROLLBACK');
}
file_put_contents(__DIR__.'/option-detached-source-review.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
echo json_encode($report['summary']).PHP_EOL;
