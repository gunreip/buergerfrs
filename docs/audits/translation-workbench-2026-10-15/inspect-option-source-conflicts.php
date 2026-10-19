<?php
// Use the actual resolver query, before its in-memory first-row reduction. No duplicate eligibility rules.
use Illuminate\Support\Facades\DB;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptions;
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    DB::beginTransaction();
    DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $baseline = json_decode(file_get_contents(__DIR__.'/direct-option-sources-before.json'), true, flags: JSON_THROW_ON_ERROR);
    $sourceQuery = null;
    $capture = false;
    DB::listen(function ($query) use (&$sourceQuery, &$capture) {
        if ($capture && str_contains($query->sql, 'translation_workbench_dynamic_source_values') && str_contains($query->sql, 'native_label')) {
            $sourceQuery = [$query->sql, $query->bindings];
        }
    });
    $conflicts = []; $duplicates = 0;
    foreach ($baseline['ui'] as $target) {
        $capture = true; $sourceQuery = null;
        app(TranslationWorkbenchDynamicOptions::class)->rows($target['key_id'], $target['finding_id'], ['source' => 'en', 'active' => 'de']);
        $capture = false;
        if (! $sourceQuery) { throw new RuntimeException('Source query not captured'); }
        $sql = preg_replace('/^select /i', 'select sources.id as source_id, ', $sourceQuery[0], 1);
        $groups = collect(DB::select($sql, $sourceQuery[1]))->groupBy('value_key');
        foreach ($groups as $valueKey => $values) {
            if ($values->count() < 2) { continue; }
            $duplicates++;
            if ($values->pluck('native_label')->uniqueStrict()->count() < 2) { continue; }
            $conflicts[] = ['finding_id' => $target['finding_id'], 'key_id' => $target['key_id'], 'value_key' => $valueKey,
                'sources' => $values->map(fn ($r) => ['id' => $r->source_id, 'value_sha256' => hash('sha256', (string) $r->native_label)])->all()];
        }
    }
    DB::rollBack();
    $report = ['mode' => 'read_only_actual_resolver_query', 'targets' => count($baseline['ui']), 'duplicate_groups' => $duplicates, 'conflict_groups' => count($conflicts), 'conflicts' => $conflicts];
    file_put_contents(__DIR__.'/option-source-conflicts.json', json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode($report).PHP_EOL;
} catch (Throwable $e) { if (DB::transactionLevel()) { DB::rollBack(); } fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
