<?php
// Read-only provenance inventory, no runtime collectors or resolver writes.
use Illuminate\Support\Facades\DB;
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$reviewStatus = in_array('--confirmed', $argv, true) ? 'confirmed' : 'pending';
try {
    DB::beginTransaction();
    DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $rows = [];
    foreach (DB::table('translation_workbench_dynamic_source_candidates')->where('candidate_source_type', 'related_dynamic_source')->where('review_status', $reviewStatus)->where('status', 'active')->orderBy('id')->get() as $c) {
        $source = DB::table('translation_workbench_dynamic_sources')->find($c->dynamic_source_id);
        $consumerId = preg_match('/^dynamic_source:(\d+)$/', $c->candidate_reference, $m) ? (int) $m[1] : null;
        $consumer = $consumerId ? DB::table('translation_workbench_dynamic_sources')->find($consumerId) : null;
        $finding = DB::table('translation_workbench_findings')->find($c->finding_id);
        $file = $finding ? DB::table('translation_workbench_source_files')->find($finding->source_file_id) : null;
        $key = DB::table('translation_workbench_keys')->find($c->key_id);
        $activeKeys = $finding ? DB::table('translation_workbench_key_findings as l')->join('translation_workbench_keys as k', 'k.id', '=', 'l.key_id')->where('l.finding_id', $finding->id)->where('l.status', 'active')->where('k.status', '<>', 'obsolete')->pluck('k.id')->all() : [];
        $rows[] = ['id' => $c->id, 'runtime_source' => $source ? ['id' => $source->id, 'status' => $source->status, 'key_id' => $source->key_id, 'scope' => $source->dynamic_scope, 'type' => $source->source_type] : null,
            'consumer' => $consumer ? ['id' => $consumer->id, 'status' => $consumer->status, 'finding_id' => $consumer->finding_id, 'key_id' => $consumer->key_id, 'scope' => $consumer->dynamic_scope] : null,
            'finding_id' => $c->finding_id, 'finding_status' => $finding?->status, 'key_id' => $c->key_id, 'key_status' => $key?->status, 'active_key_ids' => $activeKeys,
            'path' => $file?->path, 'file_status' => $file?->status, 'source_line' => $finding?->source_line, 'snapshot_count' => $c->candidate_values_count,
            'consumer_finding_matches' => $consumer?->finding_id === $c->finding_id,
            'meta' => json_decode($c->meta ?? '{}', true)];
    }
    DB::rollBack();
    $report = ['mode' => 'read_only', 'review_status' => $reviewStatus, 'count' => count($rows), 'candidates' => $rows];
    file_put_contents(__DIR__.'/'.$reviewStatus.'-runtime-links.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode($report, JSON_UNESCAPED_SLASHES).PHP_EOL;
} catch (Throwable $e) { if (DB::transactionLevel()) { DB::rollBack(); } fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
