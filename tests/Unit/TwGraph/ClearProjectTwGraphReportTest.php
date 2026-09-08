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
});

it('keeps clear project tw graph checks opt in through the command signature', function (): void {
    $command = new ClearProject();

    expect($command->getDefinition()->hasOption('tw-graph-tests'))->toBeTrue()
        ->and($command->getDefinition()->getOption('tw-graph-tests')->getDescription())
        ->toContain('Run the TW-Graph Pest suite');
});
