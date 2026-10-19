<?php

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOptionSourceConsolidation;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptions;
use Illuminate\Support\Facades\DB;

umask(0077);
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$apply = in_array('--apply', $argv, true);
$verify = in_array('--verify', $argv, true);
$base = __DIR__.'/option-duplicate-consolidation';
$require = static function (bool $condition, string $reason): void { if (! $condition) { throw new RuntimeException($reason); } };
$tables = ['findings', 'keys', 'key_findings', 'option_discoveries', 'dynamic_sources', 'dynamic_source_values',
    'dynamic_source_candidates', 'dynamic_key_values', 'lang_values', 'reviews'];
$snapshot = static function () use ($tables): array {
    $rows = [];
    foreach ($tables as $table) {
        $rows[$table] = DB::table('translation_workbench_'.$table)->orderBy('id')->get()->mapWithKeys(fn ($r) => [$r->id => (array) $r])->all();
    }
    return $rows;
};
$validate = static function (array $before, array $after, array $requests, array $ui) use ($require): void {
    $allowed = ['dynamic_sources' => array_column($requests, 'source_id'), 'dynamic_source_candidates' => collect($requests)->flatMap(fn ($r) => array_keys($r['candidate_replacements']))->all()];
    foreach ($before as $table => $rows) {
        $require(array_keys($rows) === array_keys($after[$table]), 'Rows inserted or deleted: '.$table);
        foreach ($rows as $id => $row) {
            $current = $after[$table][$id];
            if ($current === $row) { continue; }
            $require(in_array($id, $allowed[$table] ?? [], true), 'Unexpected changed row '.$table.':'.$id);
            foreach ($row as $field => $value) {
                $require($current[$field] === $value || in_array($field, ['status', 'meta', 'updated_at'], true), 'Unexpected field '.$table.':'.$id.'.'.$field);
            }
            $require($current['status'] === 'obsolete', 'Duplicate is not historical');
            $oldMeta = json_decode($row['meta'] ?? '{}', true, flags: JSON_THROW_ON_ERROR);
            $newMeta = json_decode($current['meta'], true, flags: JSON_THROW_ON_ERROR);
            $added = $table === 'dynamic_sources' ? 'superseded_by_source_id' : 'superseded_by_candidate_id';
            unset($newMeta[$added]);
            $require($oldMeta === $newMeta, 'Historical metadata changed');
        }
    }
    $resolver = app(TranslationWorkbenchDynamicOptions::class);
    foreach ($requests as $request) {
        $source = $after['dynamic_sources'][$request['source_id']];
        $keep = $after['dynamic_sources'][$request['canonical_source_id']];
        $require($source['status'] === 'obsolete' && json_decode($source['meta'], true)['superseded_by_source_id'] === $keep['id'], 'Missing historical source reference');
        $require($keep === $before['dynamic_sources'][$keep['id']] && $keep['status'] === 'active', 'Canonical source changed');
        foreach ($request['candidate_replacements'] as $oldId => $newId) {
            $candidate = $after['dynamic_source_candidates'][$oldId];
            $require($candidate['status'] === 'obsolete' && $candidate['review_status'] === 'confirmed'
                && json_decode($candidate['meta'], true)['superseded_by_candidate_id'] === $newId, 'Candidate history missing');
            $require($after['dynamic_source_candidates'][$newId] === $before['dynamic_source_candidates'][$newId], 'Canonical candidate changed');
        }
        $actual = $resolver->rows($keep['key_id'], $keep['finding_id'], ['source' => 'en', 'active' => 'de']);
        $require($actual === $ui[$keep['id']], 'UI output changed');
    }
};
try {
    $require(DB::connection()->getDriverName() === 'pgsql', 'PostgreSQL required');
    if ($verify) {
        $report = json_decode(file_get_contents($base.'-applied.json'), true, flags: JSON_THROW_ON_ERROR);
        $backup = json_decode(file_get_contents($report['backup_path']), true, flags: JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($backup, $report, $snapshot, $validate): void {
            DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
            $validate($backup['rows'], $snapshot(), $report['requests'], $backup['ui']);
        });
        file_put_contents($base.'-verified.json', json_encode(['mode' => 'post_commit_read_only_verified', 'sources' => array_column($report['requests'], 'source_id')], JSON_PRETTY_PRINT).PHP_EOL);
        echo "Post-commit verification passed.\n";
        exit;
    }
    $plan = json_decode(file_get_contents(__DIR__.'/remaining-option-transition-plan.json'), true, flags: JSON_THROW_ON_ERROR);
    $prior = json_decode(file_get_contents(__DIR__.'/option-source-transition-preflight.json'), true, flags: JSON_THROW_ON_ERROR);
    $report = DB::transaction(function () use ($apply, $plan, $prior, $require, $snapshot, $validate) {
        DB::statement('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
        DB::select('SELECT pg_advisory_xact_lock(?)', [2026101504]);
        $before = $snapshot(); $requests = []; $ui = [];
        foreach ($plan['rows'] as $row) {
            if ($row['status'] !== 'existing_target_requires_reconciliation') { continue; }
            $require(count($row['occupied_targets']) === 1 && $row['occupied_targets'][0]['values_identical'], 'Ambiguous canonical source');
            $keepId = $row['occupied_targets'][0]['source_id'];
            $old = $before['dynamic_sources'][$row['source_id']];
            $keep = $before['dynamic_sources'][$keepId];
            $review = collect($prior['requests'])->firstWhere('source_id', $old['id']);
            $replacements = [];
            foreach ($row['candidate_links'] as $link) {
                $candidate = $before['dynamic_source_candidates'][$link['id']];
                $matches = collect($before['dynamic_source_candidates'])->filter(fn ($c) => $c['candidate_reference'] === 'dynamic_source:'.$keepId
                    && $c['candidate_source_type'] === 'related_dynamic_source' && $c['dynamic_source_id'] === $candidate['dynamic_source_id']
                    && $c['key_id'] === $candidate['key_id'] && $c['status'] === 'active' && $c['review_status'] === 'confirmed')->values();
                $require($matches->count() === 1, 'Canonical confirmed candidate is not unique');
                $replacements[$candidate['id']] = $matches->sole()['id'];
            }
            $requests[] = ['source_id' => $old['id'], 'canonical_source_id' => $keepId, 'key_id' => $old['key_id'],
                'options' => $review['options'], 'file_hash' => $review['file_hash'], 'candidate_replacements' => $replacements];
            $ui[$keepId] = app(TranslationWorkbenchDynamicOptions::class)->rows($keep['key_id'], $keep['finding_id'], ['source' => 'en', 'active' => 'de']);
        }
        $require(count($requests) === 3, 'Expected three reviewed duplicates');
        $backupPath = storage_path('app/private/option-consolidation-'.date('Ymd-His').'-'.bin2hex(random_bytes(4)).'.json');
        $require(file_put_contents($backupPath, json_encode(['rows' => $before, 'ui' => $ui], JSON_THROW_ON_ERROR)) !== false, 'Backup failed');
        $service = app(TranslationWorkbenchOptionSourceConsolidation::class);
        $service->process($requests);
        $results = $service->process($requests, true);
        $repeat = $service->process($requests, true);
        $require(collect($repeat)->every(fn ($r) => $r['status'] === 'already_consolidated'), 'Repeat differs');
        $validate($before, $snapshot(), $requests, $ui);
        $report = ['mode' => $apply ? 'committed' : 'rolled_back_rehearsal', 'backup_path' => $backupPath, 'requests' => $requests,
            'result' => $results, 'repeat' => $repeat, 'values_reviews_translations_ui' => 'unchanged'];
        if (! $apply) { DB::rollBack(); }
        return $report;
    });
    file_put_contents($base.($apply ? '-applied.json' : '-rehearsal.json'), json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode(['mode' => $report['mode'], 'results' => $report['result']]).PHP_EOL;
} catch (Throwable $error) {
    fwrite(STDERR, 'FAILED / TRANSACTION ROLLED BACK: '.$error->getMessage().PHP_EOL); exit(1);
}
