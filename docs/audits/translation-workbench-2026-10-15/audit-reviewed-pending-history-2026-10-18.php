<?php
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchConsolidationEvidence;
try {
$result = DB::transaction(function () {
    DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $links = DB::table('translation_workbench_key_findings as l')->join('translation_workbench_findings as f', 'f.id', '=', 'l.finding_id')->where('l.status', 'active')->where('f.status', 'active')->orderBy('l.key_id')->get(['l.*']);
    $groups = $links->groupBy('finding_id')->only([6716, 6731, 4617, 5093])->filter(fn ($r) => $r->count() > 1);
    $ids = $groups->keys();
    $keys = $groups->flatten(1)->pluck('key_id')->unique();
    $paths = DB::table('translation_workbench_findings as f')->join('translation_workbench_source_files as s', 's.id', '=', 'f.source_file_id')->whereIn('f.id', $ids)->pluck('s.path')->unique()->values()->all();
    $report = app(TranslationWorkbenchSourceReconciliation::class)->plan($paths);
    return [
        'groups' => $groups->map(fn ($r) => $r->pluck('key_id')->all()),
        'source_rows' => collect($report['rows'])->filter(fn ($r) => count(array_intersect($r['finding_ids'], $ids->all())) > 0)->values(),
        'keys' => DB::table('translation_workbench_keys')->whereIn('id', $keys)->orderBy('id')->get(),
        'values' => DB::table('translation_workbench_key_values')->whereIn('key_id', $keys)->orderBy('id')->get(),
        'dynamic_values' => DB::table('translation_workbench_dynamic_key_values')->whereIn('key_id', $keys)->orderBy('id')->get(),
        'reviews' => DB::table('translation_workbench_reviews')->whereIn('key_id', $keys)->orderBy('id')->get(),
        'consolidation_evidence' => app(TranslationWorkbenchConsolidationEvidence::class)->all()->filter(fn ($r) => $keys->contains($r->key_id))->values(),
        'largest_group_events' => DB::table('translation_workbench_timeline_events')->whereIn('key_id', [321,4826,4827,3145,5240,4507,4613])->where('event_type', '!=', 'finding_seen')->orderBy('id')->get(),
    ];
});
echo json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage().PHP_EOL);
    exit(1);
}
