<?php

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchCodeUpdateApplier;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchCodeUpdatePlanner;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTimelineRecorder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

uses(TestCase::class);

it('reports unique unwritable targets only for safe reviewed updates', function () {
    $planner = Mockery::mock(TranslationWorkbenchCodeUpdatePlanner::class);
    $planner->shouldReceive('plan')->with(['resources/views'], 10)->once()->andReturn(['updates' => [
        ['state' => 'safe_update', 'source_path' => 'blocked.php'],
        ['state' => 'safe_update', 'source_path' => 'blocked.php'],
        ['state' => 'safe_update', 'source_path' => 'writable.php'],
        ['state' => 'manual_review', 'source_path' => 'unreviewed.php'],
    ]]);
    File::shouldReceive('exists')->with(base_path('blocked.php'))->once()->andReturnTrue();
    File::shouldReceive('isWritable')->with(base_path('blocked.php'))->once()->andReturnFalse();
    File::shouldReceive('exists')->with(base_path('writable.php'))->once()->andReturnTrue();
    File::shouldReceive('isWritable')->with(base_path('writable.php'))->once()->andReturnTrue();
    File::shouldReceive('put')->never();
    $applier = new TranslationWorkbenchCodeUpdateApplier($planner, Mockery::mock(TranslationWorkbenchTimelineRecorder::class));

    expect($applier->unwritableSourcePaths(['resources/views'], 10))->toBe(['blocked.php']);
});

it('checks every target before the first source write', function () {
    $planner = Mockery::mock(TranslationWorkbenchCodeUpdatePlanner::class);
    $planner->shouldReceive('plan')->once()->andReturn(['updates' => [
        ['state' => 'safe_update', 'source_path' => 'first.php'],
        ['state' => 'safe_update', 'source_path' => 'blocked.php'],
    ]]);
    File::shouldReceive('exists')->with(base_path('first.php'))->once()->andReturnTrue();
    File::shouldReceive('isWritable')->with(base_path('first.php'))->once()->andReturnTrue();
    File::shouldReceive('exists')->with(base_path('blocked.php'))->once()->andReturnTrue();
    File::shouldReceive('isWritable')->with(base_path('blocked.php'))->once()->andReturnFalse();
    File::shouldReceive('put')->never();
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldNotReceive('recordKeyFindingEvent');

    expect(fn () => (new TranslationWorkbenchCodeUpdateApplier($planner, $recorder))->apply(write: true))
        ->toThrow(RuntimeException::class, 'blocked.php. No code updates were applied.');
});

it('keeps dry-run planning available without write access or source mutations', function () {
    $planner = Mockery::mock(TranslationWorkbenchCodeUpdatePlanner::class);
    $planner->shouldReceive('plan')->once()->andReturn(['summary' => [], 'updates' => [[
        'state' => 'safe_update', 'source_path' => 'example.blade.php',
        'finding_id' => 1, 'key_id' => 2, 'source_line' => 1,
        'translation_key' => 'ui.example', 'raw_expression' => "__('Example')", 'new_expression' => "__('ui.example')",
    ]]]);
    File::shouldReceive('exists')->with(base_path('example.blade.php'))->andReturnTrue();
    File::shouldReceive('get')->with(base_path('example.blade.php'))->andReturn("{{ __('Example') }}");
    File::shouldReceive('isWritable')->never();
    File::shouldReceive('put')->never();
    $schema = Mockery::mock();
    $schema->shouldReceive('hasTable')->with('translation_workbench_reviews')->once()->andReturnFalse();
    DB::shouldReceive('getSchemaBuilder')->once()->andReturn($schema);
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldNotReceive('recordKeyFindingEvent');

    $report = (new TranslationWorkbenchCodeUpdateApplier($planner, $recorder))->apply();
    expect($report['summary']['would_apply'])->toBe(1)
        ->and($report['summary']['applied'])->toBe(0)
        ->and($report['diff']['files'])->toHaveCount(1);
});
