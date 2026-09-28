<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Livewire\Livewire;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class);

it('places the DO WHILE body before the condition and returns to its first action', function (string $variant, int $count) {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.do-while.flow-do-while-'.$variant;
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', '@include', 'literature.foreach.');
    $dom = new DOMDocument;
    @$dom->loadHTML(view($view)->render());
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    $points = [];
    foreach (['left', 'right'] as $side) {
        $graphId = 'idea-to-paper-do-while-'.$variant.'-'.$side;
        $root = 'literature.do-while.'.$variant.'.'.$side.'.';
        $point = function (string $suffix) use ($graphId, $root): array {
            $anchor = AnchorRegistry::get($graphId, $root.$suffix);
            expect($anchor)->not->toBeNull($suffix);

            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        };
        expect($point('action.anchorNode-end')[1])->toBeGreaterThan($point('initialize.anchorNode-end')[1]);
        expect($point('condition.anchorNode-end')[1])->toBeGreaterThan($point('action.anchorNode-end')[1]);
        if ($variant === 'multiple-actions') {
            expect($point('record.anchorNode-end')[1])->toBeGreaterThan($point('action.anchorNode-end')[1]);
            expect($point('condition.anchorNode-end')[1])->toBeGreaterThan($point('record.anchorNode-end')[1]);
        }
        if ($variant === 'bounded-retry') {
            expect($point('action.anchorNode-end')[1])->toBeGreaterThan($point('attempt.anchorNode-end')[1]);
        }
        expect($point('repeat.return.anchorNode-end'))->toBe($point('initialize.anchorNode-end'));
        expect($point('repeat.return.anchorNode-start')[1])->toBe($point('condition.anchorNode-end')[1]);
        expect($point('continue.anchorNode-end')[1])->toBeGreaterThan($point('condition.anchorNode-end')[1]);
        $points[$side] = $point('repeat.return.anchorNode-start');
        expect($points[$side][0] * ($side === 'left' ? -1 : 1))->toBeGreaterThan(0);
        $graph = $xpath->query('//*[@id="'.$graphId.'"]')->item(0);
        expect($graph)->not->toBeNull();
        expect($graph->textContent)->toContain('WHILE', 'TRUE', 'FALSE');
        expect($graph->textContent)->not->toContain('{--', '=>', 'FOREACH');
        $numbers = [];
        foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]', $graph) as $counter) {
            $numbers[] = (int) trim($counter->textContent);
        }
        sort($numbers);
        expect($numbers)->toBe(range(1, $count));
    }
    expect($points['right'])->toBe([-$points['left'][0], $points['left'][1]]);
})->with([['basic', 11], ['multiple-actions', 12], ['bounded-retry', 12]]);

it('loads DO WHILE examples lazily and restores the reference destination', function (string $variant) {
    Livewire::test(TwGraphDocumentation::class)
        ->assertDontSee('id="idea-to-paper-do-while-'.$variant.'-left"', false)
        ->call('openExample', 'flow.do-while.flow-do-while-'.$variant)
        ->assertSet('tabs.flow_index', 'flow-do-while')
        ->assertSet('tabs.flow_do_while', 'flow-do-while-'.$variant)
        ->assertSee('id="idea-to-paper-do-while-'.$variant.'-left"', false)
        ->assertSee('id="idea-to-paper-do-while-'.$variant.'-right"', false)
        ->call('openReference', 'strang.flow-step', 'flow.do-while.flow-do-while-'.$variant)
        ->assertDontSee('id="idea-to-paper-do-while-'.$variant.'-left"', false)
        ->call('returnToExample')
        ->assertSet('tabs.flow_do_while', 'flow-do-while-'.$variant)
        ->assertSee('id="idea-to-paper-do-while-'.$variant.'-right"', false);
})->with(['basic', 'multiple-actions', 'bounded-retry']);

it('executes the documented PHP body at least once and respects the retry bound', function (string $variant, array $outcomes, array $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/do-while/code-examples/do-while-'.$variant.'.php');
    $harness = '$outcomes = '.var_export($outcomes, true).';' . <<<'PHP_CODE'
$events = [];
function prepareOperation() { $GLOBALS['events'][] = 'prepare'; }
function performAction() { $GLOBALS['events'][] = 'action'; return 'result'; }
function recordResult($result) { $GLOBALS['events'][] = 'record'; }
function shouldRepeat($result) { $GLOBALS['events'][] = 'condition'; return false; }
function showSummary() { $GLOBALS['events'][] = 'summary'; }
function tryOperation() { $GLOBALS['events'][] = 'try'; return array_shift($GLOBALS['outcomes']) ?? false; }
function reportOutcome($success, $attempt) { $GLOBALS['events'][] = [$success, $attempt]; }
PHP_CODE;
    $process = new Process([PHP_BINARY, '-r', $harness.substr(file_get_contents($path), 5).';echo json_encode($events);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe($expected);
})->with([
    ['basic', [], ['prepare', 'action', 'condition', 'summary']],
    ['multiple-actions', [], ['prepare', 'action', 'record', 'condition', 'summary']],
    ['bounded-retry', [true], ['try', [true, 1]]],
    ['bounded-retry', [false, false, true], ['try', 'try', 'try', [true, 3]]],
    ['bounded-retry', [false, false, false, true], ['try', 'try', 'try', [false, 3]]],
]);
