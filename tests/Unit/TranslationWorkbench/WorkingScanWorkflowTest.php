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
        'scanPositionFinding' => null, 'scanWorkflowOpen' => true, 'scanWorkflowId' => 'test-run', 'testToken' => '',
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

it('previews individual positions while preserving a file with an unresolved dynamic call', function () {
    $directory = storage_path('framework/testing/scan-position-'.uniqid());
    Illuminate\Support\Facades\File::ensureDirectoryExists($directory);
    try {
        $path = substr($directory, strlen(base_path()) + 1).'/file.blade.php';
        file_put_contents(base_path($path), "{{ __('Reason') }}\n{{ __('Reason') }}\n{{ __('Lang key') }}\n{{ __('Lang key') }}\n{{ __(\$key) }}\n{{ __('Open') }}\n{{ __('New') }}\n{{ __('New') }}");
        config(['translation-workbench.exclude_paths' => [], 'translation-workbench.exclude_directory_names' => []]);
        $old = [];
        foreach ([[642, 11, "__('Reason')"], [21312, 2, "__('Reason')"], [639, 1, "__('Lang key')"], [700, 10, "__('Open')"]] as [$id, $line, $raw]) {
            $old[] = ['id' => $id, 'source_line' => $line, 'source_column' => 4, 'raw_expression' => $raw, 'path' => $path];
        }
        $file = (new Gunreip\TranslationWorkbench\Generation\CompleteRescanPlan)
            ->build(app(Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner::class), [$path], $old)['files'][$path];
        expect($file['state'])->toBe('review')->and(array_column($file['moved'], 'predecessor'))->toBe([700])
            ->and(array_column($file['created'], 'source_line'))->toBe([7, 8])->and($file['confirmed'])->toBe([21312])
            ->and($file['preserved'])->toBe([642, 639])
            ->and($file['position_preview'][642])->toMatchArray(['state'=>'unique_move', 'from'=>'11:4', 'to'=>'1:4'])
            ->and($file['position_preview'][21312]['state'])->toBe('unchanged')
            ->and($file['position_preview'][639]['reason'])->toBe('ambiguous');
        config(['cache.default' => 'array']);
        $coordinator = new ScanRunCoordinator;
        $run = (string) Str::uuid();
        $coordinator->store($run, ['actor' => 7, 'state' => 'preview_ready', 'preview' => [
            'request' => ['plan' => ['files' => [$path => $file]]],
            'counts' => array_fill_keys(['confirmed','created','moved','removed','review_files','outside_scope_files','suggested_keys_initialized','usage_decisions'], 0),
        ]]);
        $status = $coordinator->status($run, 7);
        $observations = $coordinator->positionPreview($run, 7, $path);
        expect($status['preview']['position_files'])->toBe([$path])->and($observations)->toBe($file['position_preview']);
        view()->share('errors', new Illuminate\Support\ViewErrorBag);
        $html = view('translation-workbench::livewire.translation-workbench.modals.run-scan', [
            'scanPositionFinding' => null, 'scanWorkflowOpen' => true, 'scanWorkflowId' => $run, 'scanWorkflow' => $status, 'scanStorage' => [], 'testToken' => '',
            'scanPositionRows' => new Illuminate\Pagination\LengthAwarePaginator($observations, 3, 25, 1, ['pageName' => 'scanPositionPage']),
        ])->render();
        expect($html)->toContain('#642', '11:4', '1:4', 'Unique move', 'Decision required', 'Unchanged');
    } finally {
        Illuminate\Support\Facades\File::deleteDirectory($directory);
    }
});

it('uses two matching neighbours to free an occupied move and retains its evidence', function () {
    $directory = storage_path('framework/testing/scan-neighbours-'.uniqid());
    Illuminate\Support\Facades\File::ensureDirectoryExists($directory);
    try {
        $path = substr($directory, strlen(base_path()) + 1).'/calls.blade.php';
        $calls = ['Before', 'Label', 'Blocking', 'Reason', 'Before', 'Label', 'Old'];
        file_put_contents(base_path($path), implode("\n", array_map(fn ($label) => "{{ __('".$label."') }}", $calls)));
        config(['translation-workbench.exclude_paths' => [], 'translation-workbench.exclude_directory_names' => []]);
        $old = [];
        foreach ([[639, 4, 'Label', 'Before', 'Blocking'], [657, 9, 'Label', 'Before', 'Old'], [642, 10, 'Reason', null, null]] as [$id, $line, $label, $prev, $next]) {
            $old[] = ['id' => $id, 'source_line' => $line, 'source_column' => 4, 'path' => $path, 'raw_expression' => "__('".$label."')",
                'capture_hash' => 'original', 'previous_id' => $id - 1, 'next_id' => $id + 1,
                'previous_expression' => $prev ? "__('".$prev."')" : null, 'next_expression' => $next ? "__('".$next."')" : null];
        }
        $scan = fn ($findings) => (new Gunreip\TranslationWorkbench\Generation\CompleteRescanPlan)
            ->build(app(Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner::class), [$path], $findings)['files'][$path];
        $moves = array_column($scan($old)['moved'], null, 'predecessor');
        expect($moves[639]['evidence'])->toMatchArray(['from' => '4:4', 'to' => '2:4', 'previous_finding' => 638, 'next_finding' => 640])
            ->and($moves[657]['evidence']['to'])->toBe('6:4')
            ->and($moves[642]['capture']['source_line'])->toBe(4);

        $old[1]['next_expression'] = null;
        $byId = array_column($scan($old)['moved'], null, 'predecessor');
        expect($byId[657]['capture']['source_line'])->toBe(6);

        $old[1]['next_expression'] = $old[0]['next_expression'];
        $unresolved = $scan($old);
        expect($unresolved['moved'])->toBe([])->and($unresolved['position_preview'][642]['reason'])->toBe('occupied_destination');
    } finally {
        Illuminate\Support\Facades\File::deleteDirectory($directory);
    }
});

it('pairs repeated moved calls by their distinct captured Blade contexts', function () {
    $directory = storage_path('framework/testing/scan-blade-context-'.uniqid());
    Illuminate\Support\Facades\File::ensureDirectoryExists($directory);
    try {
        $path = substr($directory, strlen(base_path()) + 1).'/file.blade.php';
        file_put_contents(base_path($path), "<x-example :heading=\"__('Compare source')\" />\n{{ __('Compare source') }}");
        config(['translation-workbench.exclude_paths' => [], 'translation-workbench.exclude_directory_names' => []]);
        $old = [];
        foreach (['blade_echo', 'blade_bound_attribute'] as $index => $role) {
            $old[] = ['id' => $index + 1, 'source_line' => 10 + $index, 'source_column' => 4,
                'raw_expression' => "__('Compare source')", 'path' => $path, 'captured_context' => $role];
        }
        $scanner = app(Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner::class);
        $planner = new Gunreip\TranslationWorkbench\Generation\CompleteRescanPlan;
        $moves = $planner->build($scanner, [$path], $old)['files'][$path]['moved'];
        expect(array_column(array_column($moves, 'capture'), 'source_line'))->toBe([2, 1])
            ->and(array_column(array_column($moves, 'evidence'), 'method'))->toBe(['blade_context', 'blade_context']);
        $old[1]['captured_context'] = 'blade_echo';
        $file = $planner->build($scanner, [$path], $old)['files'][$path];
        expect($file['moved'])->toBe([])->and($file['preserved'])->toBe([1, 2]);
    } finally {
        Illuminate\Support\Facades\File::deleteDirectory($directory);
    }
});

it('reconciles repeated moved calls only with unique matching source windows', function () {
    $directory = storage_path('framework/testing/scan-windows-'.uniqid());
    Illuminate\Support\Facades\File::ensureDirectoryExists($directory);
    try {
        $path = substr($directory, strlen(base_path()) + 1).'/table.blade.php';
        $lines = [];
        $old = [];
        $planner = new Gunreip\TranslationWorkbench\Generation\CompleteRescanPlan;
        foreach (['header', 'body', 'footer'] as $index => $section) {
            array_push($lines, "<section id=\"$section\">", "{{ __('Same') }}", '</section>');
            $old[] = ['id' => $index + 1, 'path' => $path, 'source_line' => count($lines) + 10,
                'source_column' => 4, 'raw_expression' => "__('Same')", 'captured_context' => 'blade_echo',
                'source_window' => $planner::sourceWindow($lines, count($lines) - 1, 4)];
        }
        config(['translation-workbench.exclude_paths' => [], 'translation-workbench.exclude_directory_names' => []]);
        $scanner = app(Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner::class);
        file_put_contents(base_path($path), implode("\n", $lines));
        $moves = $planner->build($scanner, [$path], $old)['files'][$path]['moved'];
        expect(array_column(array_column($moves, 'capture'), 'source_line'))->toBe([2, 5, 8])
            ->and(array_unique(array_column(array_column($moves, 'evidence'), 'method')))->toBe(['source_window']);
        // One predecessor moved; two additional calls now use the same literal.
        $file = $planner->build($scanner, [$path], [$old[1]])['files'][$path];
        expect(array_column($file['moved'], 'predecessor'))->toBe([2])
            ->and($file['moved'][0]['capture']['source_line'])->toBe(5)
            ->and(array_column($file['created'], 'source_line'))->toBe([2, 8]);
        $active = [];
        foreach ([$file['moved'][0]['capture'], ...$file['created']] as $index => $capture) {
            $active[] = $capture + ['path' => $path, 'id' => $index + 10];
        }
        $again = $planner->build($scanner, [$path], $active)['files'][$path];
        expect($again['created'])->toBe([])->and($again['moved'])->toBe([])
            ->and($again['confirmed'])->toHaveCount(3);
        $lines[3] = $lines[0];
        file_put_contents(base_path($path), implode("\n", $lines));
        expect($planner->build($scanner, [$path], $old)['files'][$path]['moved'])->toBe([]);
    } finally {
        Illuminate\Support\Facades\File::deleteDirectory($directory);
    }
});
