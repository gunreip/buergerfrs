<?php
// Read-only evidence for the documented category key conflict; no providers or commands executed.
use Illuminate\Support\Facades\DB;
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    if (DB::connection()->getDriverName() !== 'pgsql') { throw new RuntimeException('PostgreSQL required'); }
    $report = DB::transaction(function () {
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
        $keys = [3188, 3267];
        $tables = [];
        foreach (['keys', 'key_findings', 'dynamic_sources', 'dynamic_key_values', 'key_values', 'reviews', 'timeline_events'] as $suffix) {
            $tables[$suffix] = DB::table('translation_workbench_'.$suffix)->whereIn($suffix === 'keys' ? 'id' : 'key_id', $keys)->orderBy('id')->get();
        }
        $tables['lang_values'] = DB::table('translation_workbench_lang_values')->where(function ($query) use ($tables) {
            foreach ($tables['keys'] as $key) { $query->orWhere('translation_key', $key->translation_key)->orWhere('translation_key', 'like', $key->translation_key.'.%'); }
        })->orderBy('id')->get();
        $tables['dynamic_source_values'] = DB::table('translation_workbench_dynamic_source_values')
            ->whereIn('dynamic_source_id', $tables['dynamic_sources']->pluck('id'))->orderBy('id')->get();
        $tables['dynamic_source_candidates'] = DB::table('translation_workbench_dynamic_source_candidates')
            ->whereIn('key_id', $keys)->orWhereIn('dynamic_source_id', $tables['dynamic_sources']->pluck('id'))->orderBy('id')->get();
        $tables['findings'] = DB::table('translation_workbench_findings')->whereIn('id', $tables['key_findings']->pluck('finding_id'))->orderBy('id')->get();
        $tables['source_files'] = DB::table('translation_workbench_source_files')->whereIn('id', $tables['findings']->pluck('source_file_id'))->orderBy('id')->get();
        return ['mode' => 'read_only', 'tables' => $tables, 'counts' => collect($tables)->map->count()->all()];
    });
    umask(0077);
    $path = storage_path('app/private/category-key-conflict.json');
    file_put_contents($path, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode($report['counts']).PHP_EOL;
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
