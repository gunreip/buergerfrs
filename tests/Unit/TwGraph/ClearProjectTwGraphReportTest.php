<?php

declare(strict_types=1);

use App\Console\Commands\ClearProject;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

uses(TestCase::class);

it('parses json pest output from the last json line', function (): void {
    $command = new ClearProject();
    $method = new ReflectionMethod(ClearProject::class, 'parseJsonProcessOutput');
    $method->setAccessible(true);

    $parsed = $method->invoke($command, implode(PHP_EOL, [
        'INFO Some previous line',
        '{"tool":"pest","result":"passed","tests":406,"assertions":3259}',
    ]));

    expect($parsed)->toMatchArray([
        'tool' => 'pest',
        'result' => 'passed',
        'tests' => 406,
        'assertions' => 3259,
    ]);
});

it('summarizes parsed pest output without forcing console readers through raw output', function (): void {
    $command = new ClearProject();
    $method = new ReflectionMethod(ClearProject::class, 'processSummary');
    $method->setAccessible(true);

    $summary = $method->invoke($command, '', [
        'result' => 'passed',
        'tests' => 406,
        'assertions' => 3259,
    ]);

    expect($summary)->toBe('passed, 406 tests, 3259 assertions');
});

it('writes tw graph test reports as json and html files', function (): void {
    $originalStoragePath = app()->storagePath();
    $temporaryStorage = sys_get_temp_dir() . '/tw-graph-report-test-' . bin2hex(random_bytes(8));
    app()->useStoragePath($temporaryStorage);
    try {
        $directory = storage_path('translation-workbench/reports/tw-graph-tests');
        File::deleteDirectory($directory);

        $command = new ClearProject();
        $method = new ReflectionMethod(ClearProject::class, 'writeTwGraphTestsReport');
        $method->setAccessible(true);

        $paths = $method->invoke($command, [
            'generated_at' => '2026-09-07T10:00:00+02:00',
            'status' => 'passed',
            'groups' => [
                [
                    'name' => 'Data-driven graph assembly',
                    'description' => 'Timeline-chain graph data, preview builders, labels, outcomes, and noise classification.',
                    'status' => 'passed',
                    'checks' => 1,
                    'tests' => 406,
                    'assertions' => 3259,
                    'duration_ms' => 5459,
                ],
            ],
            'checks' => [
                [
                    'name' => 'Pest: Data-driven graph assembly',
                    'type' => 'pest',
                    'group' => 'Data-driven graph assembly',
                    'description' => 'Timeline-chain graph data, preview builders, labels, outcomes, and noise classification.',
                    'command' => '/usr/bin/php artisan test tests/Unit/TwGraph --stop-on-failure',
                    'exit_code' => 0,
                    'status' => 'passed',
                    'duration_ms' => 5459,
                    'summary' => 'passed, 406 tests, 3259 assertions',
                    'output' => '{"tool":"pest","result":"passed","tests":406,"assertions":3259}',
                    'parsed_output' => [
                        'tool' => 'pest',
                        'result' => 'passed',
                        'tests' => 406,
                        'assertions' => 3259,
                    ],
                ],
            ],
        ]);

        expect($paths)->toMatchArray([
            'json' => $directory . '/latest.json',
            'html' => $directory . '/latest.html',
        ])
            ->and(File::exists($paths['json']))->toBeTrue()
            ->and(File::exists($paths['html']))->toBeTrue()
            ->and(json_decode(File::get($paths['json']), true))->toHaveKey('status', 'passed')
            ->and(data_get(json_decode(File::get($paths['json']), true), 'groups.0'))->toMatchArray([
                'name' => 'Data-driven graph assembly',
                'status' => 'passed',
                'tests' => 406,
                'assertions' => 3259,
            ])
            ->and(data_get(json_decode(File::get($paths['json']), true), 'checks.0'))->toMatchArray([
                'name' => 'Pest: Data-driven graph assembly',
                'status' => 'passed',
                'summary' => 'passed, 406 tests, 3259 assertions',
            ])
            ->and(File::get($paths['html']))
            ->toContain('TW-Graph Test Report')
            ->toContain('Group Summary')
            ->toContain('Data-driven graph assembly')
            ->toContain('passed, 406 tests, 3259 assertions')
            ->toContain('/usr/bin/php artisan test tests/Unit/TwGraph --stop-on-failure')
            ->not->toContain('colspan="5"')
            ->not->toContain('<pre>{"tool":"pest"');
    } finally {
        app()->useStoragePath($originalStoragePath);
        File::deleteDirectory($temporaryStorage);
    }
});

it('keeps clear project tw graph checks opt in through the command signature', function (): void {
    $command = new ClearProject();

    expect($command->getDefinition()->hasOption('tw-graph-tests'))->toBeTrue()
        ->and($command->getDefinition()->getOption('tw-graph-tests')->getDescription())
        ->toContain('Run the TW-Graph Pest suite');
});

it('exposes multiple pest failures with their locations and safely escaped messages in html', function (): void {
    $check = [
        'name' => 'Pest: Primitives',
        'status' => 'failed',
        'parsed_output' => ['failures' => [
            ['test' => 'renders nodes', 'file' => 'tests/PrimitiveViewTest.php', 'line' => 183, 'message' => 'Expected <script>alert(1)</script>'],
            ['test' => 'renders arcs', 'message' => 'Arc mismatch'],
        ]],
    ];
    $command = new ClearProject();
    $details = (new ReflectionMethod(ClearProject::class, 'processFailureDetails'))->invoke($command, $check);
    expect($details)->toBe([
        "renders nodes\ntests/PrimitiveViewTest.php:183\nExpected <script>alert(1)</script>",
        "renders arcs\nArc mismatch",
    ]);
    $html = (new ReflectionMethod(ClearProject::class, 'twGraphTestsReportHtml'))->invoke($command, ['checks' => [$check]]);
    expect($html)->toContain('Failure details', 'renders nodes', 'tests/PrimitiveViewTest.php:183', 'renders arcs', 'Arc mismatch', '&lt;script&gt;')
        ->not->toContain('<script>');
});

it('retains raw output for failed processes without structured pest failures', function (): void {
    $check = ['status' => 'failed', 'output' => 'PHP Fatal error: Allowed memory size exhausted'];
    $command = new ClearProject();
    expect((new ReflectionMethod(ClearProject::class, 'processFailureDetails'))->invoke($command, $check))
        ->toBe([$check['output']]);
    $html = (new ReflectionMethod(ClearProject::class, 'twGraphTestsReportHtml'))->invoke($command, ['checks' => [$check]]);
    expect($html)->toContain($check['output']);
});

it('runs every TW Graph test file once without stopping at the first failure', function (): void {
    $command = new ClearProject;
    $groups = (new ReflectionMethod(ClearProject::class, 'twGraphTestGroups'))->invoke($command);
    $paths = array_merge(...array_column($groups, 'paths'));
    $expected = array_map(fn ($path) => 'tests/Unit/TwGraph/'.basename($path), glob(base_path('tests/Unit/TwGraph/*Test.php')));
    sort($paths);
    sort($expected);
    expect($paths)->toBe($expected);
    expect(array_column($groups, 'name'))->toContain('Props contracts');
    $arguments = (new ReflectionMethod(ClearProject::class, 'twGraphPestCommand'))->invoke($command, ['first.php', 'second.php']);
    expect($arguments)->toBe([PHP_BINARY, 'artisan', 'test', 'first.php', 'second.php']);
});

it('includes structured Pest error details as well as assertion failures', function (): void {
    $check = ['status' => 'failed', 'parsed_output' => [
        'failures' => [['test' => 'first', 'message' => 'bridge-length expected 5rem; rendered 0rem']],
        'error_details' => [['test' => 'second', 'file' => 'component.blade.php', 'line' => 42, 'message' => 'color expected cyan; rendered amber']],
    ]];
    $command = new ClearProject;
    $details = (new ReflectionMethod(ClearProject::class, 'processFailureDetails'))->invoke($command, $check);
    expect($details)->toHaveCount(2);
    expect(implode("\n", $details))->toContain('first', 'second', 'component.blade.php:42', 'bridge-length', 'color');
    $html = view('translation-workbench::pages.tw-graph.partials.test-failures', compact('check'))->render();
    expect($html)->toContain('first', 'second', 'component.blade.php:42', 'bridge-length', 'color');
});

it('shows activity for silent checks and preserves captured process output', function (bool $decorated): void {
    $output = new \Symfony\Component\Console\Output\BufferedOutput(decorated: $decorated);
    $progress = new \App\Support\Console\ProcessProgress($output, heartbeatSeconds: 0.05);
    $process = new \Symfony\Component\Process\Process([
        PHP_BINARY, '-r', 'usleep(250000); echo "{\"tests\":3}"; fwrite(STDERR, "diagnostic");',
    ]);

    $progress->run($process, 'Silent group', 2, 5);

    $console = $output->fetch();
    expect($console)->toContain('[2/5 checks completed] running: Silent group')
        ->toContain('[3/5 checks completed] passed: Silent group')
        ->not->toContain('{"tests":3}', 'diagnostic');
    expect(substr_count($console, 'running: Silent group'))->toBeGreaterThan(1);
    expect($process->getOutput())->toBe('{"tests":3}');
    expect($process->getErrorOutput())->toBe('diagnostic');
    expect($process->getExitCode())->toBe(0);
    if (! $decorated) {
        expect($console)->not->toContain("\033", "\r");
    }
})->with([false, true]);

it('reports a failed check and allows subsequent checks to complete', function (): void {
    $output = new \Symfony\Component\Console\Output\BufferedOutput;
    $progress = new \App\Support\Console\ProcessProgress($output);
    $failed = new \Symfony\Component\Process\Process([PHP_BINARY, '-r', 'fwrite(STDERR, "failure details"); exit(2);']);
    $next = new \Symfony\Component\Process\Process([PHP_BINARY, '-r', 'echo "next check";']);

    $progress->run($failed, 'First group', 0, 2);
    $progress->run($next, 'Second group', 1, 2);

    expect($output->fetch())->toContain('[1/2 checks completed] failed: First group')
        ->toContain('[2/2 checks completed] passed: Second group');
    expect($failed->getExitCode())->toBe(2);
    expect($failed->getErrorOutput())->toBe('failure details');
    expect($next->isSuccessful())->toBeTrue();
});

it('retains process timeouts and finishes the indicator with a failure', function (): void {
    $output = new \Symfony\Component\Console\Output\BufferedOutput(decorated: true);
    $progress = new \App\Support\Console\ProcessProgress($output);
    $process = new \Symfony\Component\Process\Process([PHP_BINARY, '-r', 'sleep(5);']);
    $process->setTimeout(0.15);

    expect(fn () => $progress->run($process, 'Timeout group', 0, 1))
        ->toThrow(\Symfony\Component\Process\Exception\ProcessTimedOutException::class);
    expect($output->fetch())->toContain('[1/1 checks completed] failed: Timeout group');
    expect($process->isRunning())->toBeFalse();
});

it('respects quiet console output', function (): void {
    $output = new \Symfony\Component\Console\Output\BufferedOutput(
        verbosity: \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_QUIET,
    );
    $process = new \Symfony\Component\Process\Process([PHP_BINARY, '-r', 'echo "result";']);
    (new \App\Support\Console\ProcessProgress($output))->run($process, 'Quiet group', 0, 1);

    expect($output->fetch())->toBe('');
    expect($process->getOutput())->toBe('result');
});
