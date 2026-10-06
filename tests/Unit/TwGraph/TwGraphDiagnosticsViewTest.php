<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

uses(TestCase::class);

it('keeps the diagnostics page wired to the latest clear project tw graph report', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.diagnostics',
    ));

    expect($source)
        ->toContain("storage_path('translation-workbench/reports/tw-graph-tests/latest.json')")
        ->toContain("storage_path('translation-workbench/reports/tw-graph-tests/latest.html')")
        ->toContain('json_decode((string) file_get_contents($twGraphReportJsonPath), true)')
        ->toContain('No TW-Graph test report exists yet.')
        ->toContain('The latest TW-Graph test report could not be decoded.')
        ->toContain('php artisan clear:project --tw-graph-tests');
});

it('keeps the diagnostics page read only and focused on report visibility', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.diagnostics',
    ));

    expect($source)
        ->toContain('The diagnostics page is read-only.')
        ->toContain('Run the checks deliberately from the console, then reload this page.')
        ->toContain('Latest TW-Graph checks')
        ->toContain('Group Summary')
        ->toContain('Check Results')
        ->not->toContain('wire:click')
        ->not->toContain('Artisan::call')
        ->not->toContain('Process(');
});

it('keeps diagnostics table columns aligned to the generated report fields', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.diagnostics',
    ));

    foreach ([
        "\$twGraphGroups = collect((array) data_get(\$twGraphReport, 'groups', []));",
        "data_get(\$twGraphGroup, 'name')",
        "data_get(\$twGraphGroup, 'description')",
        "data_get(\$twGraphGroup, 'tests')",
        "data_get(\$twGraphGroup, 'assertions')",
        "data_get(\$twGraphCheck, 'name')",
        "data_get(\$twGraphCheck, 'status'",
        "data_get(\$twGraphCheck, 'duration_ms')",
        "data_get(\$twGraphCheck, 'summary'",
        "data_get(\$twGraphCheck, 'command')",
        'translation-workbench::pages.tw-graph.partials.test-failures',
        "{{ __('Check') }}",
        "{{ __('Group') }}",
        "{{ __('Status') }}",
        "{{ __('Duration') }}",
        "{{ __('Summary') }}",
        "{{ __('Command') }}",
    ] as $required) {
        // Report the missing field, not the entire Blade document.
        expect(str_contains($source, $required), 'Missing diagnostics field: '.$required)->toBeTrue();
    }
});

it('renders readable escaped failure details including a location and preserves raw output as fallback', function (): void {
    $view = 'translation-workbench::pages.tw-graph.partials.test-failures';
    $html = view($view, ['check' => ['parsed_output' => ['failures' => [
        ['test' => 'renders nodes', 'file' => 'tests/PrimitiveViewTest.php', 'line' => 183, 'message' => "Expected <script>alert(1)</script>\nActual: missing"],
        ['test' => 'renders arcs', 'message' => 'Arc mismatch'],
    ]]]])->render();
    expect($html)->toContain('renders nodes', 'tests/PrimitiveViewTest.php:183', '&lt;script&gt;', 'Actual: missing', 'renders arcs', 'Arc mismatch')
        ->not->toContain('<script>');

    expect(view($view, ['check' => ['output' => 'PHP Fatal error: memory exhausted']])->render())
        ->toContain('PHP Fatal error: memory exhausted');
});


it('renders five check columns and a separate full width row only for failures', function (): void {
    $source = file_get_contents(View::getFinder()->find('translation-workbench::pages.tw-graph.diagnostics'));
    preg_match('/<table[^>]*data-tw-graph-check-results>.*?<\/table>/s', $source, $match);
    expect($match, 'The diagnostics check results table must exist')->not->toBeEmpty();
    $check = [
        'name' => 'Example check', 'description' => 'Description', 'duration_ms' => 12,
        'summary' => "\033[31mFailed summary\033[0m", 'command' => 'php artisan test example.php',
        'parsed_output' => ['error_details' => [['test' => 'Example test', 'message' => 'Example failure']]],
    ];
    $html = Blade::render($match[0], ['twGraphChecks' => [
        [...$check, 'status' => 'failed'],
        [...$check, 'status' => 'passed', 'summary' => 'Passed summary'],
    ]]);
    $document = new DOMDocument;
    @$document->loadHTML($html);
    $xpath = new DOMXPath($document);
    $table = '//*[@data-tw-graph-check-results]';
    expect($xpath->query($table.'/thead/tr/th')->length)->toBe(5);
    $rows = $xpath->query($table.'/tbody/tr');
    expect($rows->length)->toBe(3);
    foreach ([0, 2] as $index) {
        $cells = $xpath->query('./td', $rows->item($index));
        expect($cells->length)->toBe(5);
        expect(trim($cells->item(0)->textContent))->toContain('Example check');
        expect(trim($cells->item(1)->textContent))->toBe($index === 0 ? 'failed' : 'passed');
        expect(trim($cells->item(2)->textContent))->toBe('12 ms');
        expect(trim($cells->item(3)->textContent))->toBe($index === 0 ? 'Failed summary' : 'Passed summary');
        expect(trim($cells->item(4)->textContent))->toBe('php artisan test example.php');
    }
    $failureCells = $xpath->query('./td', $rows->item(1));
    expect($failureCells->length)->toBe(1);
    expect($failureCells->item(0)->getAttribute('colspan'))->toBe('5');
    expect($failureCells->item(0)->textContent)->toContain('Example failure');
    expect($xpath->query($table.'//td[@data-tw-graph-failure-row]')->length)->toBe(1);
});
