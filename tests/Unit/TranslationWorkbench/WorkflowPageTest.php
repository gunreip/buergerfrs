<?php

use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchWorkflow;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('renders only the selected workflow graph and validates tab changes', function () {
    Livewire::test(TranslationWorkbenchWorkflow::class)
        ->assertSeeHtml('translation-workflow-legacy')
        ->assertDontSeeHtml('translation-workflow-workbench')
        ->set('activeTab', 'workbench')
        ->assertSeeHtml('translation-workflow-workbench')
        ->assertDontSeeHtml('translation-workflow-legacy')
        ->set('activeTab', 'target')
        ->assertSeeHtml('translation-workflow-target')
        ->assertDontSeeHtml('translation-workflow-workbench')
        ->set('activeTab', 'write-contract')
        ->assertSeeHtml('workflow-write-contract')
        ->assertSee('Acceptance criteria — behavior tests still to implement')
        ->assertSeeHtml('contract-w01')
        ->assertSeeHtml('contract-w07')
        ->assertDontSeeHtml('data-tw-graph-preview-tools')
        ->set('activeTab', '../invalid')
        ->assertSet('activeTab', 'legacy');
});

it('keeps the workflow page behind the workbench administration middleware', function () {
    $route = Route::getRoutes()->getByName('admin.translation-workbench.workflow');
    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('admin/translation-workbench/workflow')
        ->and($route->gatherMiddleware())->toContain('auth', 'verified', 'role:Admin|Super-Admin');
});

it('documents every artisan command called by the legacy orchestrator', function () {
    $runner = file_get_contents(app_path('Console/Commands/ProjectTranslations.php'));
    preg_match_all("/'(translations:[a-z-]+(?::[a-z-]+)*)'/", $runner, $matches);
    $documentation = collect(glob(base_path('packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/legacy/*.blade.php')))
        ->map(fn ($path) => file_get_contents($path))->implode("\n");

    foreach (array_unique($matches[1]) as $command) {
        expect($documentation)->toContain($command);
    }

    expect($documentation)->toContain('git apply --check latest.patch', '--apply-diffs?', 'NOT --skip-export', 'NOT --skip-audits', '--apply-usage-decisions?', 'Previously completed writes remain');
});

it('documents the workbench commands and their distinct write and refresh gates', function () {
    $runner = file_get_contents(base_path('packages/gunreip/laravel-translation-workbench/src/Console/RunTranslationWorkbench.php'));
    preg_match_all("/'(translation-workbench:[a-z-]+)'/", $runner, $matches);
    $documentation = collect(glob(base_path('packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/workbench/*.blade.php')))
        ->map(fn ($path) => file_get_contents($path))->implode("\n");

    foreach (array_unique($matches[1]) as $command) {
        expect($documentation)->toContain($command);
    }

    expect($documentation)->toContain('--complete AND NOT --dry-run', '--write --only-apply',
        'Independent scans, not a migration', 'No --write in this call',
        'Previously completed writes remain', '--skip-lang-file-export',
        'Deferred import after initial export', 'Final source-change preview');
});

it('renders the target lifecycle in one graph with separate stop and publication routes', function () {
    $view = view('translation-workbench::livewire.workflow.target')->render();
    $source = file_get_contents(base_path('packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/target.blade.php'));

    $document = new DOMDocument;
    @$document->loadHTML($view);
    $xpath = new DOMXPath($document);

    expect($view)->not->toContain('Both outputs remain separate:')
        ->and($xpath->query('//*[@data-tw-graph-preview-tools]')->length)->toBe(1)
        ->and($source)->toContain('attach-to="workflow.target.approval.false.anchorNode-end"',
            'attach-to="workflow.target.verification.false.anchorNode-end"',
            "'workflow.target.approval.true.anchorNode-end'",
            "'workflow.target.verification.true.anchorNode-end'",
            'No overwrite of editorial decisions', 'Stale / invalid plan → STOP, no writes',
            'TW-Graph renders the supplied history', 'Proposal, not implemented behavior');
});
