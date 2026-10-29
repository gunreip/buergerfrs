<?php

use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchWorkflow;
use Gunreip\TranslationWorkbench\Support\BaselineOpenValues;
use Gunreip\TranslationWorkbench\Support\BaselineReaderPreview;
use Tests\TestCase;

uses(TestCase::class);

function openValuesFixture(): array
{
    $report = [
        'state' => 'isolated_correction_application_verified', 'production_connected' => false,
        'cluster_stopped' => true, 'assertions' => 1, 'synthetic_target' => 'test.synthetic.key',
        'input_dump_sha256' => str_repeat('a', 64),
        'fresh_generation_capture_verified' => true, 'fresh_generation_arguments_verified' => true,
        'fresh_generation_published_values_verified' => true,
        'fresh_generation_capture' => ['captured' => 3, 'pending' => 1, 'excluded' => 1, 'keys' => 1, 'links' => 1, 'blocked_findings' => 1],
        'fresh_generation_arguments' => ['roles' => ['translation_source:captured' => 3, 'excluded:excluded' => 1,
            'translation_source:pending' => 1, 'option_source:pending' => 0, 'value_usage:pending' => 0],
            'arguments' => ['overridable_default' => 1, 'runtime_value_required' => 0, 'producer_observations' => 0,
                'literal_key_decision_required' => 0, 'option_arguments' => 0], 'producers' => 0],
        'fresh_generation_published_values' => ['files' => 1, 'excluded_files' => 0, 'values' => 3,
            'baseline_hash' => str_repeat('b', 64), 'locales' => ['en' => 3],
            'totals' => ['linked' => 1, 'blocked_structure' => 1, 'unassigned_key' => 1]],
    ];
    $snapshot = ['baseline_hash' => str_repeat('b', 64), 'input_dump_sha256' => str_repeat('a', 64), 'items' => []];
    foreach (['ui.test.linked' => 'linked', 'ui.type' => 'blocked_structure', 'ui.test.open' => 'unassigned_key'] as $key => $state) {
        $snapshot['items'][$key] = ['key' => $key, 'locales' => ['en' => ['published' => ['state' => $state]]]];
    }

    return [$snapshot, $report];
}

it('lists only open published keys and reconciles locale value counts', function () {
    [$snapshot, $report] = openValuesFixture();
    $result = (new BaselineOpenValues)->inspect($snapshot, $report);
    expect($result['state'])->toBe('verified')->and($result['values'])->toBe(2)
        ->and(array_column($result['rows'], 'key'))->toBe(['ui.test.open', 'ui.type']);
    $html = view('translation-workbench::livewire.workflow.baseline-open-values', [
        'baselinePreview' => ['snapshot' => $snapshot], 'baselineRehearsal' => $report,
    ])->render();
    expect($html)->toContain('Open published values by key', 'ui.type', 'selectOpenBaselineKey', 'Select key');
});

it('does not list mismatched or incomplete evidence', function () {
    [$snapshot, $report] = openValuesFixture();
    $service = new BaselineOpenValues;
    foreach (['baseline_hash', 'input_dump_sha256'] as $field) {
        $bad = $snapshot;
        $bad[$field] = str_repeat('c', 64);
        expect($service->inspect($bad, $report)['state'])->toBe('unavailable');
    }
    unset($snapshot['items']['ui.type']);
    expect($service->inspect($snapshot, $report)['rows'])->toBe([])
        ->and($service->inspect(null, $report)['state'])->toBe('unavailable');
});

it('selects an open key and clears previous confirmation without writing', function () {
    [$snapshot] = openValuesFixture();
    $reader = Mockery::mock(BaselineReaderPreview::class)->makePartial();
    $reader->shouldReceive('load')->andReturn($snapshot);
    app()->instance(BaselineReaderPreview::class, $reader);
    $component = new TranslationWorkbenchWorkflow;
    $component->activeTab = 'baseline-preview';
    $component->baselineProposalChecked = true;
    $component->baselineProposedKey = 'previous.target.key';
    $component->baselineFilter = 'linked';
    $component->selectOpenBaselineKey('ui.type');
    expect($component->baselineKey)->toBe('ui.type')->and($component->baselineFilter)->toBe('all')
        ->and($component->baselineProposalChecked)->toBeFalse()->and($component->baselineProposedKey)->toBe('');
    foreach (['ui.test.linked', 'not.in.snapshot'] as $key) {
        $component->selectOpenBaselineKey($key);
        expect($component->baselineKey)->toBe('ui.type');
    }
});
