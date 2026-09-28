<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Livewire\Livewire;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class);

it('joins handled TRY outcomes before FINALLY or continuation in both authored layouts', function (string $variant, int $count) {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-'.$variant;
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', '@include', 'literature.do-while.');
    $dom = new DOMDocument;
    @$dom->loadHTML(view($view)->render());
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    $points = [];
    foreach (['left', 'right'] as $side) {
        $graphId = 'idea-to-paper-try-catch-'.$variant.'-'.$side;
        $root = 'literature.try-catch.'.$variant.'.'.$side.'.';
        $point = function (string $suffix) use ($graphId, $root): array {
            $anchor = AnchorRegistry::get($graphId, $root.$suffix);
            expect($anchor)->not->toBeNull($suffix);

            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        };
        $end = $point('dispatch.anchorNode-end');
        if ($variant === 'multiple') {
            expect($point('dispatch.if.true.anchorNode-return'))->toBe($point('dispatch.elseif.validation.true.anchorNode-end'));
            expect($point('dispatch.elseif.validation.true.anchorNode-return'))->toBe($point('dispatch.elseif.storage.true.anchorNode-end'));
            expect($point('dispatch.elseif.storage.true.anchorNode-return'))->toBe($end);
            expect($point('dispatch.false.anchorNode-return'))->toBe($end);
        } else {
            expect($point('dispatch.true.anchorNode-return'))->toBe($end);
            expect($point('dispatch.false.anchorNode-return'))->toBe($end);
        }
        if ($variant === 'finally') {
            expect($point('finally.anchorNode-end')[0])->toBe($end[0]);
            expect($point('finally.anchorNode-end')[1])->toBeGreaterThan($end[1]);
            expect($point('continue.anchorNode-end')[1])->toBeGreaterThan($point('finally.anchorNode-end')[1]);
        }
        expect($point('continue.anchorNode-end')[0])->toBe($end[0]);
        expect($point('continue.anchorNode-end')[1])->toBeGreaterThan($end[1]);
        $points[$side] = $end;
        $graph = $xpath->query('//*[@id="'.$graphId.'"]')->item(0);
        expect($graph)->not->toBeNull();
        expect($graph->textContent)->toContain('TRY', 'SUCCESS', 'CATCH', 'Continue process');
        expect($graph->textContent)->not->toContain('{--', '=>', 'DO WHILE', 'IF condition?');
        $numbers = [];
        foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]', $graph) as $counter) {
            $numbers[] = (int) trim($counter->textContent);
        }
        sort($numbers);
        expect($numbers)->toBe(range(1, $count));
    }
    expect($points['right'])->toBe([-$points['left'][0], $points['left'][1]]);
})->with([['basic', 5], ['finally', 6], ['multiple', 9]]);

it('loads TRY CATCH lazily and restores its reference destination', function (string $variant) {
    $component = $variant === 'multiple' ? 'strang.flow-if-elseif-multi' : 'strang.flow-if-else';
    Livewire::test(TwGraphDocumentation::class)
        ->assertDontSee('id="idea-to-paper-try-catch-'.$variant.'-left"', false)
        ->call('openExample', 'flow.try-catch.flow-try-catch-'.$variant)
        ->assertSet('tabs.flow_index', 'flow-try-catch')
        ->assertSet('tabs.flow_try_catch', 'flow-try-catch-'.$variant)
        ->assertSee('id="idea-to-paper-try-catch-'.$variant.'-left"', false)
        ->assertSee('id="idea-to-paper-try-catch-'.$variant.'-right"', false)
        ->call('openReference', $component, 'flow.try-catch.flow-try-catch-'.$variant)
        ->assertDontSee('id="idea-to-paper-try-catch-'.$variant.'-left"', false)
        ->call('returnToExample')
        ->assertSet('tabs.flow_try_catch', 'flow-try-catch-'.$variant)
        ->assertSee('id="idea-to-paper-try-catch-'.$variant.'-right"', false);
})->with(['basic', 'finally', 'multiple']);

it('executes one documented handler and runs FINALLY after either handled outcome', function (string $variant, ?string $failure, array $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/try-catch/code-examples/try-catch-'.$variant.'.php');
    $harness = '$failure = '.var_export($failure, true).';' . <<<'PHP_CODE'
$events = [];
class ValidationFailure extends RuntimeException {}
class StorageFailure extends RuntimeException {}
class DetailedValidationFailure extends ValidationFailure {}
function performOperation() {
    $GLOBALS['events'][] = 'try';
    if ($GLOBALS['failure']) { throw new $GLOBALS['failure']('example'); }
}
function recordSuccess() { $GLOBALS['events'][] = 'success'; }
function handleFailure($error) { $GLOBALS['events'][] = 'catch'; }
function handleValidation($error) { $GLOBALS['events'][] = 'validation'; }
function handleStorage($error) { $GLOBALS['events'][] = 'storage'; }
function handleOther($error) { $GLOBALS['events'][] = 'other'; }
function cleanup() { $GLOBALS['events'][] = 'finally'; }
function continueProcess() { $GLOBALS['events'][] = 'continue'; }
PHP_CODE;
    $process = new Process([PHP_BINARY, '-r', $harness.substr(file_get_contents($path), 5).';echo json_encode($events);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe($expected);
})->with([
    ['basic', null, ['try', 'success', 'continue']],
    ['basic', 'RuntimeException', ['try', 'catch', 'continue']],
    ['finally', null, ['try', 'success', 'finally', 'continue']],
    ['finally', 'RuntimeException', ['try', 'catch', 'finally', 'continue']],
    ['multiple', null, ['try', 'success', 'continue']],
    ['multiple', 'ValidationFailure', ['try', 'validation', 'continue']],
    ['multiple', 'DetailedValidationFailure', ['try', 'validation', 'continue']],
    ['multiple', 'StorageFailure', ['try', 'storage', 'continue']],
    ['multiple', 'RuntimeException', ['try', 'other', 'continue']],
]);
