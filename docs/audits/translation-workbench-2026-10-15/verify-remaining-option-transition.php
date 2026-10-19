<?php

// Independent post-commit verification. PostgreSQL enforces read-only access.
use Illuminate\Support\Facades\DB;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$applied = json_decode(file_get_contents(__DIR__.'/option-remaining-transition-applied.json'), true, flags: JSON_THROW_ON_ERROR);
$before = json_decode(file_get_contents($applied['backup_path']), true, flags: JSON_THROW_ON_ERROR);
if ($applied['mode'] !== 'committed' || DB::connection()->getDriverName() !== 'pgsql') {
    throw new RuntimeException('Committed report and PostgreSQL required.');
}
$report = DB::transaction(function () use ($applied, $before): array {
    DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $checks = [];
    foreach (['dynamic_source_values', 'dynamic_key_values', 'lang_values', 'reviews', 'keys', 'key_findings'] as $table) {
        $current = DB::table('translation_workbench_'.$table)->orderBy('id')->get()->mapWithKeys(fn ($row) => [$row->id => (array) $row])->all();
        if ($current !== $before[$table]) { throw new RuntimeException('Changed protected table: '.$table); }
        $checks[$table] = 'unchanged';
    }
    $targets = collect($applied['prepared'])->keyBy('target_finding_id');
    $sources = [];
    foreach ($applied['transitions'] as $transition) {
        $source = DB::table('translation_workbench_dynamic_sources')->find($transition['source_id']);
        $expected = $targets[$transition['target_finding_id']];
        $observation = DB::table('translation_workbench_option_discoveries')->find($expected['observation_id']);
        $assignment = json_decode($observation->meta, true)['foundation_assignment'];
        if ($source->finding_id !== $expected['target_finding_id'] || $source->option_discovery_id !== $expected['observation_id']
            || $source->status !== 'active' || $assignment['finding_id'] !== $source->finding_id || $assignment['key_id'] !== $source->key_id) {
            throw new RuntimeException('Unexpected resulting identity: '.$source->id);
        }
        if (isset($before['option_discoveries'][$observation->id]) && $observation->first_seen_at !== $before['option_discoveries'][$observation->id]['first_seen_at']) {
            throw new RuntimeException('Observation first_seen_at changed');
        }
        foreach ($transition['candidate_ids'] as $id) {
            $candidate = (array) DB::table('translation_workbench_dynamic_source_candidates')->find($id);
            $previous = $before['dynamic_source_candidates'][$id];
            if ($candidate['finding_id'] !== $source->finding_id) { throw new RuntimeException('Candidate finding differs'); }
            unset($candidate['finding_id'], $candidate['updated_at'], $previous['finding_id'], $previous['updated_at']);
            if ($candidate !== $previous) { throw new RuntimeException('Candidate review or payload changed'); }
        }
        $sources[] = ['source_id' => $source->id, 'finding_id' => $source->finding_id, 'observation_id' => $observation->id, 'status' => $source->status];
    }
    return ['mode' => 'post_commit_read_only_verification', 'checks' => $checks, 'sources' => $sources];
});
file_put_contents(__DIR__.'/option-remaining-transition-verified.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
echo json_encode($report).PHP_EOL;
