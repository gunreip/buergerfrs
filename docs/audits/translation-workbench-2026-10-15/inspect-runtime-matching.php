<?php
// Candidate selection only; no resolver command or provider execution.
use Illuminate\Support\Facades\DB;
use Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources;
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    DB::beginTransaction();
    DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $resolver = app(ResolveUnknownDynamicSources::class);
    $invoke = fn ($method, ...$args) => (new ReflectionMethod($resolver, $method))->invoke($resolver, ...$args);
    $rows = [];
    foreach ($invoke('unknownSources') as $source) {
        $selected = $invoke('bestRuntimeSourceForSource', $source);
        $candidates = DB::table('translation_workbench_dynamic_sources')->whereIn('source_type', $invoke('runtimeSourceTypes'))
            ->where('dynamic_scope', $source->dynamic_scope)->where('values_count', '>', 0)->get(['id', 'key_id', 'status', 'values_count']);
        if ($candidates->isEmpty()) { continue; }
        $rows[] = ['consumer_id' => $source->id, 'finding_id' => $source->finding_id, 'key_id' => $source->key_id,
            'scope' => $source->dynamic_scope, 'selected_id' => $selected?->id, 'candidates' => $candidates->all()];
    }
    $links = DB::table('translation_workbench_dynamic_source_candidates')->where('candidate_source_type', 'related_dynamic_source')
        ->selectRaw('review_status, status, count(*) as count')->groupBy('review_status', 'status')->get()->all();
    DB::rollBack();
    $report = ['mode' => 'read_only', 'consumers' => $rows, 'existing_links' => $links];
    $suffix = in_array('--after', $argv, true) ? 'after' : 'before';
    file_put_contents(__DIR__.'/runtime-matching-'.$suffix.'.json', json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode($report).PHP_EOL;
} catch (Throwable $e) { if (DB::transactionLevel()) { DB::rollBack(); } fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
