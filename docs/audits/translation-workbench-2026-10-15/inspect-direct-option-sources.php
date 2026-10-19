<?php
// Read-only source ownership and complete current UI baseline.
use Illuminate\Support\Facades\DB;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptions;
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    DB::beginTransaction();
    DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $sources = [];
    foreach (DB::table('translation_workbench_dynamic_sources as s')->where('s.status', '<>', 'obsolete')
        ->whereExists(fn ($q) => $q->selectRaw('1')->from('translation_workbench_dynamic_source_values as v')->whereColumn('v.dynamic_source_id', 's.id')->where('v.status', 'active'))->orderBy('s.id')->get() as $s) {
        $f = $s->finding_id ? DB::table('translation_workbench_findings')->find($s->finding_id) : null;
        $file = $f ? DB::table('translation_workbench_source_files')->find($f->source_file_id) : null;
        $keys = $f ? DB::table('translation_workbench_key_findings')->where('finding_id', $f->id)->where('status', 'active')->pluck('key_id')->all() : [];
        $sources[] = ['id' => $s->id, 'type' => $s->source_type, 'status' => $s->status, 'finding_id' => $s->finding_id, 'key_id' => $s->key_id,
            'finding_status' => $f?->status, 'file_status' => $file?->status, 'active_key_ids' => $keys, 'values_count' => $s->values_count];
    }
    $keyIds = DB::table('translation_workbench_dynamic_sources')->where('status', '<>', 'obsolete')->whereNotNull('key_id')->pluck('key_id');
    $targets = DB::table('translation_workbench_key_findings as l')->join('translation_workbench_findings as f', 'f.id', '=', 'l.finding_id')
        ->join('translation_workbench_source_files as sf', 'sf.id', '=', 'f.source_file_id')->join('translation_workbench_keys as k', 'k.id', '=', 'l.key_id')
        ->whereIn('l.key_id', $keyIds)->where('l.status', 'active')->where('f.status', 'active')->where('sf.status', 'active')->where('k.status', '<>', 'obsolete')
        ->orderBy('f.id')->get(['f.id', 'l.key_id']);
    $rows = [];
    foreach ($targets as $t) {
        $values = app(TranslationWorkbenchDynamicOptions::class)->rows($t->key_id, $t->id, ['source' => 'en', 'active' => 'de']);
        $rows[] = ['finding_id' => $t->id, 'key_id' => $t->key_id, 'count' => count($values), 'sha256' => hash('sha256', json_encode($values, JSON_THROW_ON_ERROR))];
    }
    DB::rollBack();
    $after = in_array('--after', $argv, true);
    $report = ['mode' => 'read_only', 'sources' => $sources, 'ui' => $rows];
    if ($after) {
        $before = json_decode(file_get_contents(__DIR__.'/direct-option-sources-before.json'), true, flags: JSON_THROW_ON_ERROR);
        $report['ui_unchanged'] = $rows === $before['ui'];
        if (! $report['ui_unchanged']) { throw new RuntimeException('UI differs from baseline; inspect before proceeding'); }
    }
    file_put_contents(__DIR__.'/direct-option-sources-'.($after ? 'after' : 'before').'.json', json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode(['sources' => $sources, 'ui_targets' => count($rows), 'ui_unchanged' => $report['ui_unchanged'] ?? null]).PHP_EOL;
} catch (Throwable $e) { if (DB::transactionLevel()) { DB::rollBack(); } fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
