<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Livewire\Livewire;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class);

it('keeps the authored TRY scopes and their continuations separate in both layouts', function (string $variant, int $count) {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-'.$variant;
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', '@include', 'literature.for.');
    $dom = new DOMDocument;
    @$dom->loadHTML(view($view)->render());
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    $ends = [];
    foreach (['left', 'right'] as $side) {
        $graphId = 'idea-to-paper-try-catch-'.$variant.'-'.$side;
        $root = 'literature.try-catch.'.$variant.'.'.$side.'.';
        $point = function (string $suffix) use ($graphId, $root): array {
            $anchor = AnchorRegistry::get($graphId, $root.$suffix);
            expect($anchor)->not->toBeNull($suffix);

            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        };
        if ($variant === 'if-try') {
            expect($point('inner-return.stem.anchorNode-end'))->toBe($point('outer.true.anchorNode-return'));
            expect($point('outer.false.anchorNode-return'))->toBe($point('outer.anchorNode-end'));
            expect($point('dispatch.true.anchorNode-return'))->toBe($point('dispatch.false.anchorNode-return'));
            expect($point('continue.anchorNode-end')[1])->toBeGreaterThan($point('outer.anchorNode-end')[1]);
        } elseif ($variant === 'try-if-finally') {
            expect($point('selection.true.anchorNode-return'))->toBe($point('selection.false.anchorNode-return'));
            expect($point('dispatch.anchorNode-end')[1])->toBeGreaterThan($point('selection.anchorNode-end')[1]);
            expect($point('finally.anchorNode-end')[1])->toBeGreaterThan($point('dispatch.anchorNode-end')[1]);
            expect($point('continue.anchorNode-end')[1])->toBeGreaterThan($point('finally.anchorNode-end')[1]);
        } else {
            expect($point('loop.anchorNode-start'))->toBe($point('condition.anchorNode-end'));
            expect($point('body-return.anchorNode-end'))->toBe($point('iterator.anchorNode-end'));
            if ($variant === 'try-foreach') {
                expect($point('body-return.anchorNode-start'))->toBe($point('dispatch.true.anchorNode-end'));
                expect($point('dispatch.false.anchorNode-end')[1])->toBe($point('dispatch.false.stem.anchorNode-end')[1]);
                expect($source)->not->toContain('.failure-turn');
                expect($point('failure-join.anchorNode-end'))->toBe($point('normal-exit.anchorNode-end'));
                expect($point('failure-join.anchorNode-end'))->not->toBe($point('body-return.anchorNode-end'));
                expect($point('normal-exit.anchorNode-end')[1])->toBeGreaterThan($point('loop.anchorNode-end')[1]);
                expect($point('failure-rise.anchorNode-end')[1])->toBeGreaterThan($point('dispatch.false.anchorNode-end')[1]);
            } else {
                expect($point('dispatch.true.anchorNode-return'))->toBe($point('dispatch.false.anchorNode-return'));
                $bodyEnd = $variant === 'while-try-finally' ? 'finally' : 'dispatch';
                expect($point('body-return.anchorNode-start'))->toBe($point($bodyEnd.'.anchorNode-end'));
                if ($variant === 'while-try-finally') {
                    expect($point('finally.anchorNode-end')[1])->toBeLessThan($point('dispatch.anchorNode-end')[1]);
                    expect($point('finally.anchorNode-end')[1])->toBeGreaterThan($point('iterator.anchorNode-end')[1]);
                }
            }
        }
        $ends[$side] = $point('continue.anchorNode-end');
        $graph = $xpath->query('//*[@id="'.$graphId.'"]')->item(0);
        expect($graph)->not->toBeNull();
        expect($graph->textContent)->toContain('TRY', 'CATCH', 'SUCCESS');
        expect($graph->textContent)->not->toContain('{--', '=>', 'index =');
        $numbers = [];
        foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]', $graph) as $counter) {
            $numbers[] = (int) trim($counter->textContent);
        }
        sort($numbers);
        expect($numbers)->toBe(range(1, $count));
    }
    expect($ends['right'])->toBe([-$ends['left'][0], $ends['left'][1]]);
})->with([
    ['if-try', 10], ['try-if-finally', 10], ['foreach-try', 15], ['try-foreach', 16], ['while-try-finally', 16],
]);

it('loads each TRY context lazily and restores it from deep reference', function (string $variant) {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.try-catch.flow-try-catch-'.$variant)
        ->assertSet('tabs.flow_try_catch', 'flow-try-catch-'.$variant)
        ->assertSee('id="idea-to-paper-try-catch-'.$variant.'-left"', false)
        ->assertSee('id="idea-to-paper-try-catch-'.$variant.'-right"', false)
        ->call('openReference', 'strang.flow-if-else', 'flow.try-catch.flow-try-catch-'.$variant)
        ->assertDontSee('id="idea-to-paper-try-catch-'.$variant.'-left"', false)
        ->call('returnToExample')
        ->assertSet('tabs.flow_try_catch', 'flow-try-catch-'.$variant)
        ->assertSee('id="idea-to-paper-try-catch-'.$variant.'-right"', false);
})->with(['if-try', 'try-if-finally', 'foreach-try', 'try-foreach', 'while-try-finally']);

it('executes the documented TRY scope with failures and empty inputs', function (string $variant, array $input, array $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/try-catch/code-examples/try-catch-'.$variant.'.php');
    $harness = '$input = '.var_export($input, true).';' . <<<'PHP_CODE'
$events = [];
$enabled = $input['enabled'] ?? true;
$usePrimary = $input['primary'] ?? true;
function prepareOperation() { $GLOBALS['events'][] = 'prepare'; }
function performOperation() { $GLOBALS['events'][] = 'operation'; if ($GLOBALS['input']['fail'] ?? false) { throw new RuntimeException; } }
function performPrimary() { $GLOBALS['events'][] = 'primary'; if ($GLOBALS['input']['fail'] ?? false) { throw new RuntimeException; } }
function performSecondary() { $GLOBALS['events'][] = 'secondary'; if ($GLOBALS['input']['fail'] ?? false) { throw new RuntimeException; } }
function recordSuccess($item = null) { $GLOBALS['events'][] = $item === null ? 'success' : 'success:'.$item; }
function handleFailure($error) { $GLOBALS['events'][] = 'catch'; }
function recordSkipped() { $GLOBALS['events'][] = 'skip'; }
function cleanup() { $GLOBALS['events'][] = 'finally'; }
function continueProcess() { $GLOBALS['events'][] = 'continue'; }
function loadItems() { return $GLOBALS['input']['items'] ?? []; }
function processItem($item) { $GLOBALS['events'][] = 'process:'.$item; if ($item === 'bad') { throw new RuntimeException; } }
function handleItemFailure($item, $error) { $GLOBALS['events'][] = 'catch:'.$item; }
function cleanupItem($item) { $GLOBALS['events'][] = 'finally:'.$item; }
function showSummary() { $GLOBALS['events'][] = 'summary'; }
function loadQueue() { $q = new SplQueue; foreach (loadItems() as $item) { $q->enqueue($item); } return $q; }
function prepareQueue($queue) {}
function hasNext($queue) { return !$queue->isEmpty(); }
function nextItem($queue) { return $queue->dequeue(); }
PHP_CODE;
    $process = new Process([PHP_BINARY, '-r', $harness.substr(file_get_contents($path), 5).';echo json_encode($events);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe($expected);
})->with([
    ['if-try', ['enabled' => false, 'fail' => true], ['skip', 'continue']],
    ['if-try', [], ['prepare', 'operation', 'success', 'continue']],
    ['if-try', ['fail' => true], ['prepare', 'operation', 'catch', 'continue']],
    ['try-if-finally', [], ['primary', 'success', 'finally', 'continue']],
    ['try-if-finally', ['primary' => false], ['secondary', 'success', 'finally', 'continue']],
    ['try-if-finally', ['fail' => true], ['primary', 'catch', 'finally', 'continue']],
    ['try-if-finally', ['primary' => false, 'fail' => true], ['secondary', 'catch', 'finally', 'continue']],
    ['foreach-try', ['items' => ['a', 'bad', 'c']], ['process:a', 'success:a', 'process:bad', 'catch:bad', 'process:c', 'success:c', 'summary']],
    ['foreach-try', [], ['summary']],
    ['try-foreach', ['items' => ['a', 'bad', 'c']], ['process:a', 'success:a', 'process:bad', 'catch', 'continue']],
    ['try-foreach', ['items' => ['a', 'c']], ['process:a', 'success:a', 'process:c', 'success:c', 'continue']],
    ['try-foreach', [], ['continue']],
    ['while-try-finally', ['items' => ['a', 'bad', 'c']], ['process:a', 'success:a', 'finally:a', 'process:bad', 'catch:bad', 'finally:bad', 'process:c', 'success:c', 'finally:c', 'summary']],
    ['while-try-finally', [], ['summary']],
]);
