<?php

// Read-only production preflight. No apply flag and no provider execution.
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOptionObservationTransition;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector;
use Illuminate\Support\Facades\DB;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$review = json_decode(file_get_contents(__DIR__.'/option-detached-source-review.json'), true, flags: JSON_THROW_ON_ERROR);
if ($review['plan_sha256'] !== hash_file('sha256', __DIR__.'/option-transition-plan.json')) {
    throw new RuntimeException('Detached source review is stale.');
}
$collector = app(TranslationWorkbenchDynamicOptionCollector::class);
$arrays = (new ReflectionMethod($collector, 'hardcodedPublicArrayOptions'))->invoke($collector);
foreach (['roleBadgeColorOptions' => ['roleUserManagementBadgeColors', 'colors'], 'roleBadgeVariantOptions' => ['roleUserManagementBadgeVariants', 'variants']] as $name => [$method, $leaf]) {
    $arrays->push(['name' => $name, 'source_file' => 'app/Livewire/Admin/RoleList.php:'.$method,
        'options' => config('buergerfrs-icons.categories.role_user_management.badge.'.$leaf, [])]);
}
$groups = $arrays->groupBy('name')->map(fn ($items) => [
    'source_file' => $items->pluck('source_file')->implode(', '),
    'options' => $items->count() === 1 ? $items->sole()['options'] : [],
    'source_candidates' => $items->values()->all(),
    'source_resolution' => $items->count() === 1 ? 'single_candidate' : 'ambiguous_source',
]);
$requests = [];
foreach ($review['sources'] as $source) {
    if ($source['status'] !== 'target_verified_observation_rebinding_required' || count($source['targets']) !== 1) {
        throw new RuntimeException('Unverified target for '.$source['source_id']);
    }
    $target = $source['targets'][0];
    $declaringPath = explode(':', $target['selected_source'])[0];
    if (hash_file('sha256', $root.'/'.$declaringPath) !== $target['selected_source_sha256']) {
        throw new RuntimeException('Changed declaring source: '.$declaringPath);
    }
    $discoveries = (new ReflectionMethod($collector, 'discoveries'))->invoke($collector, [$source['source_path']], $groups);
    $matching = $discoveries->filter(fn ($row) => $row['source_line'] <= $target['line'] && $row['source_end_line'] >= $target['line']);
    if ($matching->count() !== 1 || count($target['existing_observation_ids']) > 1 || count($target['finding_ids']) !== 1) {
        throw new RuntimeException('Ambiguous preparation for '.$source['source_id']);
    }
    $requests[] = ['observation_id' => $target['existing_observation_ids'][0] ?? null,
        'target_finding_id' => $target['finding_ids'][0], 'key_id' => $source['key_id'],
        'file_hash' => $source['file_sha256'], 'discovery' => $matching->sole()];
}
if (DB::connection()->getDriverName() !== 'pgsql') {
    throw new RuntimeException('PostgreSQL READ ONLY transaction required.');
}
$result = DB::transaction(function () use ($requests) {
    DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    return app(TranslationWorkbenchOptionObservationTransition::class)->process($requests);
});
$report = ['mode' => 'read_only_preparation_preview', 'review_sha256' => hash_file('sha256', __DIR__.'/option-detached-source-review.json'),
    'requests' => $requests, 'result' => $result, 'summary' => array_count_values(array_column($result, 'status'))];
file_put_contents(__DIR__.'/option-observation-transition-preflight.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
echo json_encode($report['summary']).PHP_EOL;
