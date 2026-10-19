<?php
// Read-only evidence for exact nonempty Legacy/Foundation text differences.
use Illuminate\Support\Facades\DB;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueComparison;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    if (DB::getDriverName() !== 'pgsql') {
        throw new RuntimeException('PostgreSQL read-only transaction required');
    }
    $report = DB::transaction(function () {
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
        $comparison = app(TranslationWorkbenchValueComparison::class)->compare(
            DB::table('translation_values as v')->leftJoin('translation_keys as k', 'k.id', '=', 'v.translation_key_id')->select('v.*', 'k.key as translation_key')->get(),
            DB::table('translation_workbench_lang_values')->get()
        );
        $conflicts = array_values(array_filter($comparison['comparisons'], fn ($g) => $g['review_category'] === 'different_text_requires_review'));
        $keys = array_values(array_unique(array_column($conflicts, 'key')));
        $tables = [];
        $tables['keys'] = DB::table('translation_workbench_keys')->whereIn('translation_key', $keys)->orderBy('id')->get();
        $ids = $tables['keys']->pluck('id');
        foreach (['key_findings', 'key_values', 'reviews', 'timeline_events'] as $suffix) {
            $tables[$suffix] = DB::table('translation_workbench_'.$suffix)->whereIn('key_id', $ids)->orderBy('id')->get();
        }
        $tables['findings'] = DB::table('translation_workbench_findings')->whereIn('id', $tables['key_findings']->pluck('finding_id'))
            ->orWhereIn('found_translation_key', $keys)->orWhereIn('existing_key', $keys)->orderBy('id')->get();
        $tables['source_files'] = DB::table('translation_workbench_source_files')->whereIn('id', $tables['findings']->pluck('source_file_id'))->orderBy('id')->get();
        return ['captured_at' => gmdate(DATE_ATOM), 'mode' => 'read_only', 'conflicts' => $conflicts, 'tables' => $tables];
    });
    $report['runtime'] = [];
    foreach ($report['conflicts'] as $g) {
        $file = lang_path($g['locale'].'/'.strtok($g['key'], '.').'.php');
        $content = is_file($file) ? require $file : [];
        $report['runtime'][] = ['file' => str_replace(base_path().'/', '', $file), 'sha256' => is_file($file) ? hash_file('sha256', $file) : null, 'file_value' => data_get($content, substr($g['key'], strpos($g['key'], '.') + 1)), 'key' => $g['key'], 'locale' => $g['locale'], 'resolved' => app('translator')->get($g['key'], [], $g['locale'])];
    }
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage().PHP_EOL);
    exit(1);
}
