<?php
// Read-only provenance inventory. Does not call providers, UI mounts or runtime collectors.
use Illuminate\Support\Facades\DB;
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    if (DB::connection()->getDriverName() !== 'pgsql') { throw new RuntimeException('PostgreSQL required'); }
    $report = DB::transaction(function () {
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
        $keys = DB::table('translation_workbench_keys')->whereIn('id', [3039, 3267])->get(['id', 'translation_key', 'status']);
        $sources = DB::table('translation_workbench_dynamic_sources')->whereIn('key_id', [3039, 3267])
            ->whereIn('source_type', ['runtime_db_options', 'runtime_hardcoded_options'])->orderBy('id')->get();
        $rows = $sources->map(function ($s) {
            $values = DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $s->id)->where('status', 'active')->orderBy('value_key')->get(['value_key', 'native_label']);
            return ['id' => $s->id, 'key_id' => $s->key_id, 'status' => $s->status, 'source_type' => $s->source_type,
                'reference' => $s->source_reference, 'values_count' => $s->values_count, 'active_values_count' => $values->count(),
                'values_sha256' => hash('sha256', $values->toJson()),
                'confirmed_links' => DB::table('translation_workbench_dynamic_source_candidates')->where('dynamic_source_id', $s->id)
                    ->where('review_status', 'confirmed')->get(['id', 'key_id', 'finding_id', 'candidate_reference', 'status'])->all()];
        });
        return ['mode' => 'read_only', 'keys' => $keys->all(), 'runtime_sources' => $rows->all(),
            'observations' => DB::table('translation_workbench_option_discoveries')->whereIn('id', [4, 9, 10, 12, 13])
                ->get(['id', 'source_path', 'source_type', 'options_count', 'options', 'meta'])->all()];
    });
    file_put_contents(__DIR__.'/document-option-runtime.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode($report['runtime_sources']).PHP_EOL;
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
