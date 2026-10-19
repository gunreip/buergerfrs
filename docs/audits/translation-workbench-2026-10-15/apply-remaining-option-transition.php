<?php

// Explicit five-source migration; no full scan/provider run and no global classification cleanup.
use Gunreip\TranslationWorkbench\Console\ClassifyDynamicValues;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptions;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOptionObservationTransition;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOptionSourceTransition;
use Illuminate\Support\Facades\DB;

umask(0077);
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$apply = in_array('--apply', $argv, true);
$preflightPath = __DIR__.'/remaining-option-transition-plan.json';
$reviewPath = $preflightPath;
$preflight = json_decode(file_get_contents($preflightPath), true, flags: JSON_THROW_ON_ERROR);
$review = json_decode(file_get_contents($reviewPath), true, flags: JSON_THROW_ON_ERROR);
if (count($preflight['requests']) !== 5 || count($review['sources']) !== 5) {
    throw new RuntimeException('Refresh preparation preflight.');
}
if (DB::connection()->getDriverName() !== 'pgsql') { throw new RuntimeException('PostgreSQL required.'); }
$require = static function (bool $condition, string $message): void { if (! $condition) { throw new RuntimeException($message); } };
foreach ($review['sources'] as $source) {
    $target = $source['targets'][0];
    $path = explode(':', $target['selected_source'])[0];
    $require(hash_file('sha256', $root.'/'.$path) === $target['selected_source_sha256'], 'Declaring source changed: '.$path);
}
// These config-backed options were reviewed explicitly; detect configuration drift as well.
foreach ($preflight['requests'] as $request) {
    $discovery = $request['discovery'];
    if (in_array($discovery['options_variable'], ['roleBadgeVariantOptions', 'roleBadgeColorOptions'], true)) {
        $require($discovery['options'] === config('buergerfrs-icons.categories.role_user_management.badge.'.($discovery['options_variable'] === 'roleBadgeColorOptions' ? 'colors' : 'variants')), 'Reviewed variant config changed');
    }
}
$tables = ['findings', 'keys', 'key_findings', 'option_discoveries', 'dynamic_sources', 'dynamic_source_values',
    'dynamic_source_candidates', 'dynamic_key_values', 'lang_values', 'reviews'];
$snapshot = static function () use ($tables): array {
    $result = [];
    foreach ($tables as $table) {
        $result[$table] = DB::table('translation_workbench_'.$table)->orderBy('id')->get()->mapWithKeys(fn ($row) => [$row->id => (array) $row])->all();
    }
    return $result;
};
try {
    $report = DB::transaction(function () use ($apply, $preflight, $review, $require, $snapshot) {
        DB::statement('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
        DB::select('SELECT pg_advisory_xact_lock(?)', [2026101504]);
        $before = $snapshot();
        $resolver = app(TranslationWorkbenchDynamicOptions::class);
        $uiBefore = [];
        foreach ($review['sources'] as $source) {
            $uiBefore[$source['source_id']] = $resolver->rows($source['key_id'], $source['targets'][0]['finding_ids'][0], ['source' => 'en', 'active' => 'de']);
        }
        $backupPath = storage_path('app/private/option-transition-'.date('Ymd-His').'-'.bin2hex(random_bytes(4)).'.json');
        if (! is_dir(dirname($backupPath))) { mkdir(dirname($backupPath), 0700, true); }
        $require(file_put_contents($backupPath, json_encode($before, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)) !== false, 'Could not save pre-migration snapshot');
        $prepared = app(TranslationWorkbenchOptionObservationTransition::class)->process($preflight['requests'], true);
        $byFinding = collect($prepared)->keyBy('target_finding_id');
        $requests = [];
        foreach ($review['sources'] as $source) {
            $target = $source['targets'][0];
            $preparation = collect($preflight['requests'])->firstWhere('target_finding_id', $target['finding_ids'][0]);
            $requests[] = ['source_id' => $source['source_id'], 'old_finding_id' => $source['previous_finding_id'],
                'target_finding_id' => $target['finding_ids'][0], 'key_id' => $source['key_id'],
                'options' => $preparation['discovery']['options'], 'file_hash' => $source['file_sha256']];
        }
        $transition = app(TranslationWorkbenchOptionSourceTransition::class);
        $result = $transition->process($requests, true);
        $classifier = app(ClassifyDynamicValues::class);
        $invoke = fn (string $method, ...$args) => (new ReflectionMethod($classifier, $method))->invoke($classifier, ...$args);
        $targetIds = array_column($requests, 'target_finding_id');
        // Use the actual classifier's selection and value writers for these reviewed targets only.
        // Do not invoke its global obsoleteUnseenSources or classify unrelated findings.
        for ($pass = 0; $pass < 2; $pass++) {
            app(TranslationWorkbenchDynamicOptionSynchronizer::class)->process(collect(array_column($preflight['requests'], 'discovery')), true);
            $rows = $invoke('dynamicFindingRows')->whereIn('finding_id', $targetIds);
            $require($rows->count() === 5, 'Target classifier rows are not unique');
            foreach ($rows as $row) {
                $classifications = $invoke('classificationsForRow', $row);
                $require(count($classifications) === 1 && $classifications[0]['status'] === 'active', 'Target classification is ambiguous');
                $saved = $invoke('syncDynamicSource', $row, $classifications[0], now());
                $expected = collect($requests)->firstWhere('target_finding_id', $row->finding_id);
                $require($saved['source_id'] === $expected['source_id'], 'Source ID changed');
                $invoke('syncDynamicSourceValues', $saved['source_id'], $row, $classifications[0]);
            }
        }
        $repeat = $transition->process($requests, true);
        $require(collect($repeat)->every(fn ($row) => $row['status'] === 'already_transitioned'), 'Transition is not repeatable');
        $after = $snapshot();
        $allowed = [
            'findings' => [$targetIds, ['meta', 'updated_at']],
            'option_discoveries' => [array_column($prepared, 'observation_id'), null],
            'dynamic_sources' => [array_column($requests, 'source_id'), ['finding_id', 'option_discovery_id', 'source_line', 'source_expression', 'fingerprint', 'source_reference', 'meta', 'last_seen_at', 'updated_at']],
            'dynamic_source_candidates' => [collect($result)->flatMap(fn ($r) => $r['candidate_ids'])->all(), ['finding_id', 'updated_at']],
        ];
        $changed = [];
        foreach ($before as $table => $rows) {
            $permitted = $allowed[$table] ?? [[], []];
            $require(array_diff(array_keys($rows), array_keys($after[$table])) === [], 'Deleted rows in '.$table);
            foreach ($after[$table] as $id => $row) {
                if (($rows[$id] ?? null) === $row) { continue; }
                $require(in_array($id, $permitted[0], true), 'Unexpected changed row '.$table.':'.$id);
                if ($permitted[1] !== null) {
                    $require(isset($rows[$id]), 'Unexpected insertion '.$table.':'.$id);
                    foreach ($row as $field => $value) {
                        $require($rows[$id][$field] === $value || in_array($field, $permitted[1], true), 'Unexpected field '.$table.':'.$id.'.'.$field);
                    }
                }
                $changed[$table][] = $id;
            }
        }
        foreach ($review['sources'] as $source) {
            $actual = $resolver->rows($source['key_id'], $source['targets'][0]['finding_ids'][0], ['source' => 'en', 'active' => 'de']);
            $require($actual === $uiBefore[$source['source_id']], 'UI values changed for source '.$source['source_id']);
        }
        $report = ['mode' => $apply ? 'committed' : 'rolled_back_rehearsal', 'backup_path' => $backupPath,
            'prepared' => $prepared, 'transitions' => $result, 'repeat' => $repeat, 'changed_rows' => $changed,
            'unchanged' => ['all_source_value_rows', 'all_translation_rows', 'all_reviews', 'all_key_relations', 'other_observations_and_sources', 'UI_resolver_en_de'],
            'classification_scope' => 'five_targets_only_no_global_cleanup'];
        if (! $apply) { DB::rollBack(); }
        return $report;
    });
    $outputPath = __DIR__.'/option-remaining-transition-'.($apply ? 'applied' : 'rehearsal').'.json';
    file_put_contents($outputPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode(['mode' => $report['mode'], 'changes' => $report['changed_rows']]).PHP_EOL;
} catch (Throwable $error) {
    fwrite(STDERR, 'ROLLED BACK: '.$error->getMessage().PHP_EOL);
    exit(1);
}
