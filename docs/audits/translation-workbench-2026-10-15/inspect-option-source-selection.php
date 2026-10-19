<?php

// Source-only replay: public array parser plus the two audited config readers.
// No provider method, runtime collector, synchronization or database query is invoked.
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$plan = json_decode(file_get_contents(__DIR__.'/option-transition-plan.json'), true, flags: JSON_THROW_ON_ERROR);
$collector = app(TranslationWorkbenchDynamicOptionCollector::class);
$sources = (new ReflectionMethod($collector, 'hardcodedPublicArrayOptions'))->invoke($collector);
foreach (['roleBadgeColorOptions' => ['roleUserManagementBadgeColors', 'colors'], 'roleBadgeVariantOptions' => ['roleUserManagementBadgeVariants', 'variants']] as $name => [$method, $leaf]) {
    $sources->push(['name' => $name, 'source_file' => 'app/Livewire/Admin/RoleList.php:'.$method,
        'options' => config('buergerfrs-icons.categories.role_user_management.badge.'.$leaf, [])]);
}
$groups = $sources->groupBy('name')->map(fn ($candidates) => [
    'source_file' => $candidates->pluck('source_file')->implode(', '),
    'options' => $candidates->count() === 1 ? $candidates->sole()['options'] : [],
    'source_candidates' => $candidates->values()->all(),
    'source_resolution' => $candidates->count() === 1 ? 'single_candidate' : 'ambiguous_source',
]);
$paths = collect($plan['observations'])->where('status', 'finding_context_refresh_required')->pluck('source_path')->unique()->all();
$observations = (new ReflectionMethod($collector, 'discoveries'))->invoke($collector, $paths, $groups);
$rows = $observations->map(fn ($row) => [
    'path' => $row['source_path'], 'line' => $row['source_line'], 'variable' => $row['options_variable'],
    'resolution' => $row['source_resolution'], 'selected_source' => $row['source_reference'], 'options_count' => count($row['options']),
])->all();
$report = ['mode' => 'source_only_audit_with_two_reviewed_config_readers',
    'summary' => collect($rows)->countBy('resolution')->all(), 'observations' => $rows];
file_put_contents(__DIR__.'/option-source-selection.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
echo json_encode($report['summary']).PHP_EOL;
