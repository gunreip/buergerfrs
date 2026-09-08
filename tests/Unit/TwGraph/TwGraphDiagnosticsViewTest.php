<?php

declare(strict_types=1);

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

    expect($source)
        ->toContain("\$twGraphGroups = collect((array) data_get(\$twGraphReport, 'groups', []));")
        ->toContain("data_get(\$twGraphGroup, 'name')")
        ->toContain("data_get(\$twGraphGroup, 'description')")
        ->toContain("data_get(\$twGraphGroup, 'tests')")
        ->toContain("data_get(\$twGraphGroup, 'assertions')")
        ->toContain("data_get(\$twGraphCheck, 'name')")
        ->toContain("data_get(\$twGraphCheck, 'status'")
        ->toContain("data_get(\$twGraphCheck, 'duration_ms')")
        ->toContain("data_get(\$twGraphCheck, 'summary')")
        ->toContain("data_get(\$twGraphCheck, 'command')")
        ->toContain('title="{{ data_get($twGraphCheck, \'output\') }}"')
        ->not->toContain('colspan="5"')
        ->toContain("{{ __('Check') }}")
        ->toContain("{{ __('Group') }}")
        ->toContain("{{ __('Status') }}")
        ->toContain("{{ __('Duration') }}")
        ->toContain("{{ __('Summary') }}")
        ->toContain("{{ __('Command') }}");
});
