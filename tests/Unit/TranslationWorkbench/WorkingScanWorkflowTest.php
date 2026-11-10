<?php

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess;
use Gunreip\TranslationWorkbench\Generation\ScanRunCoordinator;
use Gunreip\TranslationWorkbench\Generation\WorkingScanService;
use Gunreip\TranslationWorkbench\Jobs\RunWorkbenchScan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class);

it('runs a read-only background preview and keeps the executable request out of browser state', function () {
    config(['cache.default' => 'array']);
    $coordinator = new ScanRunCoordinator;
    $id = (string) Str::uuid();
    $target = ['database' => 'isolated'];
    Cache::put($coordinator->key($id), ['actor' => 7, 'target' => $target, 'state' => 'queued_preview'], 60);
    Auth::shouldReceive('onceUsingId')->with(7)->once()->andReturn(true);
    $access = Mockery::mock(TranslationWorkbenchEditorAccess::class);
    $access->shouldReceive('authorize')->once();
    app()->instance(TranslationWorkbenchEditorAccess::class, $access);
    $service = Mockery::mock(WorkingScanService::class);
    $service->shouldReceive('target')->once()->andReturn($target);
    $service->shouldReceive('preview')->once()->andReturn(['counts' => ['created' => 2], 'request' => ['private' => true]]);
    $service->shouldNotReceive('apply');
    (new RunWorkbenchScan($id, false))->handle($service, $coordinator);
    expect($coordinator->status($id, 7))->toMatchArray(['state' => 'preview_ready'])
        ->and($coordinator->status($id, 7)['preview'])->not->toHaveKey('request');
    expect(fn () => $coordinator->status($id, 8))->toThrow(RuntimeException::class, 'another user');
    Queue::fake();
    $coordinator->confirm($id, 7);
    Queue::assertPushed(RunWorkbenchScan::class, fn ($job) => $job->apply && $job->connection === 'background');
    expect(fn () => $coordinator->confirm($id, 7))->toThrow(RuntimeException::class, 'completed scan preview');
});

it('requires a fresh confirmation when a background scan finds a changed preview', function () {
    config(['cache.default' => 'array']);
    $coordinator = new ScanRunCoordinator;
    $id = (string) Str::uuid();
    $target = ['database' => 'isolated'];
    $preview = ['fingerprint' => 'original'];
    Cache::put($coordinator->key($id), ['actor' => 7, 'target' => $target, 'state' => 'queued_apply', 'preview' => $preview], 60);
    Auth::shouldReceive('onceUsingId')->with(7)->once()->andReturn(true);
    $access = Mockery::mock(TranslationWorkbenchEditorAccess::class);
    $access->shouldReceive('authorize')->once();
    app()->instance(TranslationWorkbenchEditorAccess::class, $access);
    $service = Mockery::mock(WorkingScanService::class);
    $service->shouldReceive('target')->once()->andReturn($target);
    $service->shouldReceive('apply')->once()->with($preview, 7, Mockery::type('callable'))->andReturn(['state' => 'changed', 'preview' => ['fingerprint' => 'fresh']]);
    (new RunWorkbenchScan($id, true))->handle($service, $coordinator);
    expect($coordinator->status($id, 7)['state'])->toBe('changed')
        ->and($coordinator->status($id, 7)['preview']['fingerprint'])->toBe('fresh');
});

it('reports file-based scan progress without changing the reconciliation plan', function () {
    $directory = storage_path('framework/testing/scan-progress-'.uniqid());
    Illuminate\Support\Facades\File::ensureDirectoryExists($directory);
    try {
        file_put_contents($directory.'/one.blade.php', "{{ __('First literal') }}");
        file_put_contents($directory.'/two.blade.php', "{{ __('Second literal') }}");
        $scanner = app(Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner::class);
        $plan = new Gunreip\TranslationWorkbench\Generation\CompleteRescanPlan;
        config(['translation-workbench.exclude_paths' => [], 'translation-workbench.exclude_directory_names' => []]);
        $scope = substr($directory, strlen(base_path()) + 1);
        $expected = $plan->build($scanner, [$scope], []);
        $updates = [];
        $actual = $plan->build($scanner, [$scope], [], progress: function ($phase, $done, $total) use (&$updates) {
            $updates[] = [$phase, $done, $total];
        });
        expect($actual)->toBe($expected)
            ->and($updates)->toBe([
                ['scanning', 0, 5], ['scanning', 1, 5], ['scanning', 2, 5],
                ['comparing', 2, 5], ['comparing', 3, 5], ['comparing', 4, 5],
            ]);
    } finally {
        Illuminate\Support\Facades\File::deleteDirectory($directory);
    }
});

it('keeps completed scan steps when another pass repeats the same phases', function () {
    $progress = new Gunreip\TranslationWorkbench\Generation\ScanProgressSteps;
    expect($progress->advance('scanning', 2, 9)[0]['percent'])->toBe(50);
    $steps = $progress->advance('comparing', 5, 9);
    expect($steps[0])->toMatchArray(['percent' => 100, 'status' => 'completed'])
        ->and($steps[1]['percent'])->toBe(25);
    $progress->advance('preview_complete', 9, 9);
    $steps = $progress->advance('scanning', 0, 9);
    expect(count($steps))->toBe(4)
        ->and($steps[0]['percent'])->toBe(100)
        ->and($steps[3])->toMatchArray(['percent' => 0, 'status' => 'running']);
    $progress->advance('scanning', 2, 9);
    $steps = $progress->finish(false);
    expect($steps[3])->toMatchArray(['percent' => 50, 'status' => 'failed'])
        ->and($steps[0]['status'])->toBe('completed');
});

it('renders separate completed and running progress bars in the scan modal', function () {
    view()->share('errors', new Illuminate\Support\ViewErrorBag);
    $html = view('translation-workbench::livewire.translation-workbench.modals.run-scan', [
        'scanWorkflowOpen' => true, 'scanWorkflowId' => 'test-run', 'testToken' => '',
        'errors' => new Illuminate\Support\ViewErrorBag,
        'scanStorage' => ['scans' => 3, 'confirmations' => 42, 'snapshot_bytes' => 2048, 'result_bytes' => 1024, 'cache' => ['supported' => true, 'bytes' => 512, 'expired' => 2]],
        'scanWorkflow' => ['state' => 'scanning', 'progress' => ['steps' => [
            ['phase' => 'scanning', 'percent' => 100, 'status' => 'completed'],
            ['phase' => 'comparing', 'percent' => 25, 'status' => 'running'],
        ]]],
    ])->render();
    expect(substr_count($html, 'data-flux-progress'))->toBe(2)
        ->and($html)->toContain('Reading source files', 'Comparing findings', 'Completed', 'In progress', 'steps.0.percent', 'steps.1.percent')
        ->and($html)->toContain('value="100"', '--flux-progress-percentage: 100%', 'value="25"', '--flux-progress-percentage: 25%', 'Stored scan snapshots', 'Finding confirmations across scans');
});

it('keeps the scan plan out of status reads and confirmation writes', function () {
    config(['cache.default' => 'array']);
    $coordinator = new ScanRunCoordinator;
    $id = (string) Str::uuid();
    $request = ['source' => str_repeat('Large immutable scan evidence ', 600000)];
    $coordinator->store($id, ['actor' => 7, 'state' => 'preview_ready', 'updated' => time(),
        'preview' => ['fingerprint' => 'preview', 'request' => $request]]);
    $record = Cache::get($coordinator->key($id));
    expect(strlen(serialize($record)))->toBeLessThan(1000)
        ->and($record['preview'])->not->toHaveKey('request')
        ->and($coordinator->previewForWorker($id, $record)['request'])->toBe($request);
    Queue::fake();
    $coordinator->confirm($id, 7);
    expect(strlen(serialize(Cache::get($coordinator->key($id)))))->toBeLessThan(1000)
        ->and($coordinator->status($id, 7)['state'])->toBe('queued_apply');
    Cache::forget($coordinator->key($id).':plan');
    expect(fn () => $coordinator->previewForWorker($id, $record))->toThrow(RuntimeException::class, 'expired');
});
