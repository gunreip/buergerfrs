<?php

// Read-only replan after the detached-source transition. No providers, no writes to the database.
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector;
use Illuminate\Support\Facades\DB;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$previous = json_decode(file_get_contents(__DIR__.'/option-source-transition-preflight.json'), true, flags: JSON_THROW_ON_ERROR);
$payload = json_decode(file_get_contents(__DIR__.'/option-payload-review.json'), true, flags: JSON_THROW_ON_ERROR);
$collector = app(TranslationWorkbenchDynamicOptionCollector::class);
$arrays = (new ReflectionMethod($collector, 'hardcodedPublicArrayOptions'))->invoke($collector);
foreach (['roleBadgeColorOptions' => ['roleUserManagementBadgeColors', 'colors'], 'roleBadgeVariantOptions' => ['roleUserManagementBadgeVariants', 'variants']] as $name => [$method, $leaf]) {
    $arrays->push(['name' => $name, 'source_file' => 'app/Livewire/Admin/RoleList.php:'.$method,
        'options' => config('buergerfrs-icons.categories.role_user_management.badge.'.$leaf, [])]);
}
$groups = $arrays->groupBy('name')->map(fn ($items) => [
    'source_file' => $items->pluck('source_file')->implode(', '), 'options' => $items->count() === 1 ? $items->sole()['options'] : [],
    'source_candidates' => $items->values()->all(), 'source_resolution' => $items->count() === 1 ? 'single_candidate' : 'ambiguous_source',
]);
if (DB::connection()->getDriverName() !== 'pgsql') { throw new RuntimeException('PostgreSQL required.'); }
$report = DB::transaction(function () use ($previous, $payload, $collector, $groups, $root) {
    DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $rows = []; $preparations = []; $sources = [];
    $normalize = static function (array $v): array { ksort($v); return $v; };
    foreach ($previous['requests'] as $request) {
        $source = DB::table('translation_workbench_dynamic_sources')->find($request['source_id']);
        $observation = DB::table('translation_workbench_option_discoveries')->find($source->option_discovery_id);
        if ($source->finding_id !== $request['old_finding_id'] || $source->status !== 'active') { throw new RuntimeException('Source changed: '.$source->id); }
        $occupied = DB::table('translation_workbench_dynamic_sources')->where('finding_id', $request['target_finding_id'])
            ->where('key_id', $request['key_id'])->where('option_discovery_id', $observation->id)->where('id', '<>', $source->id)->get();
        $links = DB::table('translation_workbench_dynamic_source_candidates')->where('candidate_reference', 'dynamic_source:'.$source->id)
            ->orWhere('dynamic_source_id', $source->id)->get();
        $values = DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $source->id)->where('status', 'active')->pluck('native_label', 'value_key')->all();
        $duplicates = $occupied->map(function ($row) use ($values, $normalize) {
            $other = DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $row->id)->where('status', 'active')->pluck('native_label', 'value_key')->all();
            return ['source_id' => $row->id, 'status' => $row->status, 'values_identical' => $normalize($values) === $normalize($other)];
        })->all();
        $rows[] = ['source_id' => $source->id, 'target_finding_id' => $request['target_finding_id'], 'observation_id' => $observation->id,
            'status' => $occupied->isEmpty() ? 'unoccupied_target' : 'existing_target_requires_reconciliation', 'occupied_targets' => $duplicates,
            'candidate_links' => $links->map(fn ($r) => ['id' => $r->id, 'dynamic_source_id' => $r->dynamic_source_id, 'finding_id' => $r->finding_id,
                'reference' => $r->candidate_reference, 'review_status' => $r->review_status, 'status' => $r->status])->all()];
        if ($occupied->isNotEmpty()) { continue; }
        $target = DB::table('translation_workbench_findings')->find($request['target_finding_id']);
        $path = DB::table('translation_workbench_source_files')->where('id', $target->source_file_id)->value('path');
        if (hash_file('sha256', $root.'/'.$path) !== $request['file_hash']) { throw new RuntimeException('View changed: '.$path); }
        $found = (new ReflectionMethod($collector, 'discoveries'))->invoke($collector, [$path], $groups)
            ->filter(fn ($r) => $r['options_variable'] === $observation->options_variable && $r['key_variable'] === $observation->key_variable
                && $r['label_variable'] === $observation->label_variable && $r['source_line'] <= $target->source_line && $r['source_end_line'] >= $target->source_line)->values();
        if ($found->count() !== 1 || $found->sole()['source_resolution'] !== 'view_bound_candidate'
            || $normalize($found->sole()['options']) !== $normalize($request['options'])) { throw new RuntimeException('Payload not verified: '.$source->id); }
        $discovery = $found->sole();
        $preparations[] = ['observation_id' => $observation->id, 'target_finding_id' => $target->id, 'key_id' => $request['key_id'], 'file_hash' => $request['file_hash'], 'discovery' => $discovery];
        $sources[] = ['source_id' => $source->id, 'previous_finding_id' => $source->finding_id, 'historical_observation_id' => $observation->id,
            'key_id' => $source->key_id, 'file_sha256' => $request['file_hash'],
            'targets' => [['finding_ids' => [$target->id], 'selected_source' => $discovery['source_reference'],
                'selected_source_sha256' => hash_file('sha256', $root.'/'.explode(':', $discovery['source_reference'])[0])]]];
    }
    return ['mode' => 'read_only_remaining_source_plan', 'rows' => $rows, 'requests' => $preparations, 'sources' => $sources,
        'summary' => collect($rows)->countBy('status')->all()];
});
file_put_contents(__DIR__.'/remaining-option-transition-plan.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
echo json_encode($report['summary']).PHP_EOL;
