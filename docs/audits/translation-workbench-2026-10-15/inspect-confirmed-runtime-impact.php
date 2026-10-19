<?php
// Read-only counterfactual: compare actual UI rows with only currently bound confirmed links.
use Illuminate\Support\Facades\DB;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptions;
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    DB::beginTransaction();
    DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $strict = new class extends TranslationWorkbenchDynamicOptions {
        public function linkedRuntimeSourceIdsForFinding(object $selectedFinding): array {
            return DB::table('translation_workbench_dynamic_source_candidates as c')
                ->join('translation_workbench_findings as f', 'f.id', '=', 'c.finding_id')
                ->join('translation_workbench_source_files as sf', 'sf.id', '=', 'f.source_file_id')
                ->join('translation_workbench_key_findings as kf', fn ($q) => $q->on('kf.finding_id', '=', 'f.id')->on('kf.key_id', '=', 'c.key_id'))
                ->join('translation_workbench_keys as k', 'k.id', '=', 'c.key_id')
                ->where('f.status', 'active')->where('sf.status', 'active')->where('kf.status', 'active')->where('k.status', '<>', 'obsolete')
                ->whereIn('c.dynamic_source_id', parent::linkedRuntimeSourceIdsForFinding($selectedFinding))
                ->where('c.review_status', 'confirmed')->where('c.status', 'active')->where('c.candidate_source_type', 'related_dynamic_source')
                ->where(fn ($q) => $q->where('c.finding_id', $selectedFinding->id)->orWhere('c.key_id', $selectedFinding->key_id))
                ->distinct()->pluck('c.dynamic_source_id')->all();
        }
    };
    $actual = app(TranslationWorkbenchDynamicOptions::class);
    $keyIds = DB::table('translation_workbench_dynamic_source_candidates')->where('review_status', 'confirmed')->where('status', 'active')->where('candidate_source_type', 'related_dynamic_source')->distinct()->pluck('key_id');
    $targets = DB::table('translation_workbench_key_findings as l')->join('translation_workbench_findings as f', 'f.id', '=', 'l.finding_id')
        ->join('translation_workbench_source_files as sf', 'sf.id', '=', 'f.source_file_id')->join('translation_workbench_keys as k', 'k.id', '=', 'l.key_id')
        ->whereIn('l.key_id', $keyIds)->where('l.status', 'active')->where('f.status', 'active')->where('sf.status', 'active')->where('k.status', '<>', 'obsolete')
        ->orderBy('f.id')->get(['f.id', 'l.key_id', 'sf.path']);
    $rows = [];
    foreach ($targets as $target) {
        $a = $actual->rows($target->key_id, $target->id, ['source' => 'en', 'active' => 'de']);
        $b = $strict->rows($target->key_id, $target->id, ['source' => 'en', 'active' => 'de']);
        $selected = (object) ['id' => $target->id, 'key_id' => $target->key_id];
        $rows[] = ['finding_id' => $target->id, 'key_id' => $target->key_id, 'path' => $target->path,
            'linked_source_ids' => $actual->linkedRuntimeSourceIdsForFinding($selected), 'strict_linked_source_ids' => $strict->linkedRuntimeSourceIdsForFinding($selected),
            'current_row_count' => count($a), 'strict_row_count' => count($b), 'ui_unchanged' => $a === $b,
            'current_sha256' => hash('sha256', json_encode($a, JSON_THROW_ON_ERROR)), 'strict_sha256' => hash('sha256', json_encode($b, JSON_THROW_ON_ERROR))];
    }
    DB::rollBack();
    $report = ['mode' => 'read_only_counterfactual_not_applied', 'targets' => $rows,
        'summary' => ['targets' => count($rows), 'changed_ui' => count(array_filter($rows, fn ($r) => ! $r['ui_unchanged']))]];
    $after = in_array('--after', $argv, true);
    if ($after) {
        $before = json_decode(file_get_contents(__DIR__.'/confirmed-runtime-impact.json'), true, flags: JSON_THROW_ON_ERROR);
        $old = array_column($before['targets'], 'current_sha256', 'finding_id');
        $new = array_column($rows, 'current_sha256', 'finding_id');
        if ($old !== $new) { throw new RuntimeException('Actual UI rows differ from the pre-change baseline'); }
        $report['mode'] = 'read_only_post_change_verification';
        $report['baseline_ui_unchanged'] = true;
    }
    file_put_contents(__DIR__.'/confirmed-runtime-impact'.($after ? '-after' : '').'.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode(['mode' => $report['mode'], 'summary' => $report['summary'], 'baseline_ui_unchanged' => $report['baseline_ui_unchanged'] ?? null]).PHP_EOL;
} catch (Throwable $e) { if (DB::transactionLevel()) { DB::rollBack(); } fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
