<?php

use Gunreip\TranslationWorkbench\Support\BaselineTransferBalance;
use Tests\TestCase;

uses(TestCase::class);

function transferBalanceFixture(): array
{
    return [
        'state' => 'isolated_correction_application_verified', 'production_connected' => false,
        'cluster_stopped' => true, 'assertions' => 1363, 'synthetic_target' => 'rehearsal.correction.type',
        'input_dump_sha256' => str_repeat('a', 64),
        'fresh_generation_capture_verified' => true, 'fresh_generation_arguments_verified' => true,
        'fresh_generation_published_values_verified' => true,
        'fresh_generation_capture' => ['captured' => 19940, 'pending' => 72, 'excluded' => 108,
            'keys' => 768, 'links' => 1792, 'blocked_findings' => 415],
        'fresh_generation_arguments' => ['roles' => ['translation_source:captured' => 19940,
            'excluded:excluded' => 108, 'translation_source:pending' => 31, 'option_source:pending' => 28, 'value_usage:pending' => 13],
            'arguments' => ['overridable_default' => 3, 'runtime_value_required' => 11,
                'producer_observations' => 7, 'literal_key_decision_required' => 10, 'option_arguments' => 28], 'producers' => 6],
        'fresh_generation_published_values' => ['files' => 54, 'excluded_files' => 32, 'values' => 2016,
            'totals' => ['linked' => 1597, 'blocked_structure' => 22, 'unassigned_key' => 397],
            'locales' => ['de' => 967, 'de-at' => 11, 'de-be' => 10, 'de-ch' => 11, 'de-de' => 15,
                'de-it' => 11, 'de-li' => 10, 'de-lu' => 10, 'en' => 971]],
    ];
}

it('keeps units and overlapping subsets separate', function () {
    $result = (new BaselineTransferBalance)->inspect(transferBalanceFixture());
    expect($result['state'])->toBe('verified')
        ->and(array_column($result['sections'], 'total'))->toBe([20120, 19940, 72, 2016, 86])
        ->and($result['sections'][1]['rows'][2]['count'])->toBe(17733)
        ->and($result['sections'][2]['rows'][2]['status'])->toBe('evidence_only')
        ->and($result['keys'])->toBe(768)->and($result['locales'])->toBe(9)
        ->and($result['argument_records'])->toBe(59);
    foreach ($result['sections'] as $section) {
        expect(array_sum(array_column($section['rows'], 'count')))->toBe($section['total']);
    }
    expect($result)->not->toHaveKey('grand_total');
});

it('does not treat absent or unverified steps as zero', function () {
    $service = new BaselineTransferBalance;
    expect($service->inspect(null)['state'])->toBe('unavailable');
    foreach (['capture', 'arguments', 'published_values'] as $step) {
        $report = transferBalanceFixture();
        unset($report['fresh_generation_'.$step.'_verified']);
        expect($service->inspect($report))->toBe(['state' => 'unavailable', 'sections' => []]);
    }
});

it('rejects inconsistent accounting without presenting partial totals', function (string $path, mixed $value) {
    $report = transferBalanceFixture();
    data_set($report, $path, $value);
    expect((new BaselineTransferBalance)->inspect($report))->toBe(['state' => 'inconsistent', 'sections' => []]);
})->with([
    ['fresh_generation_capture.pending', 71],
    ['fresh_generation_capture.links', 20000],
    ['fresh_generation_capture.keys', 2000],
    ['fresh_generation_capture.excluded', -1],
    ['fresh_generation_capture.captured', '19940'],
    ['fresh_generation_arguments.roles.value_usage:pending', 12],
    ['fresh_generation_arguments.arguments.option_arguments', 27],
    ['fresh_generation_arguments.producers', null],
    ['fresh_generation_published_values.totals.unassigned_key', 398],
    ['fresh_generation_published_values.locales.en', 970],
    ['fresh_generation_published_values.files', []],
    ['fresh_generation_arguments', false],
]);

it('renders the complete balance using Flux sections', function () {
    $html = view('translation-workbench::livewire.workflow.baseline-transfer-balance', [
        'baselineRehearsal' => transferBalanceFixture(),
    ])->render();
    expect($html)->toContain('Recorded transfer balance', '20120', '17733', 'Evidence preserved',
        'Value usages: not independent translation tasks', 'Do not add their totals together');
});
