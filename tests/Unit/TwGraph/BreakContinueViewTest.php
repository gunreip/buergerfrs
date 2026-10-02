<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Livewire\Livewire;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class);

it('connects BREAK outside WHILE and only the successful action back to the condition', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-while';
    $html = view($view)->render();
    $point = function ($suffix) {
        $a = AnchorRegistry::get('idea-to-paper-break-while-left', 'literature.break.while.left.'.$suffix);
        expect($a)->not->toBeNull($suffix);
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    };
    expect($point('body-return.anchorNode-start'))->toBe($point('decision.true.anchorNode-end'));
    expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-return'));
    expect($point('break-join.anchorNode-end'))->toBe($point('normal-exit.anchorNode-end'));
    expect($point('break-join.anchorNode-end')[1])->toBeGreaterThan($point('loop.anchorNode-end')[1]);
    expect($point('break-join.anchorNode-end'))->not->toBe($point('loop.anchorNode-return'));
    expect($html)->toContain('decision.false.arc2-south-west', 'FALSE: BREAK')->not->toContain('failure-turn');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    expect($xp->query('//pre/code')->length)->toBe(8);
    $counters = [];
    foreach ($xp->query('//*[@id="idea-to-paper-break-while-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $counters[] = (int) trim($node->textContent);
    }
    sort($counters);
    expect($counters)->toBe(range(1, 16));
});

it('loads BREAK lazily and returns from its component reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.break-continue.flow-break-while')
        ->assertSet('tabs.flow_index', 'flow-break-continue')
        ->assertSet('tabs.flow_break_continue', 'flow-break-while')
        ->assertSee('id="idea-to-paper-break-while-left"', false)
        ->assertSee('Suggested sequence')
        ->call('openReference', 'strang.flow-while', 'flow.break-continue.flow-break-while')
        ->assertDontSee('id="idea-to-paper-break-while-left"', false)
        ->call('returnToExample')
        ->assertSee('id="idea-to-paper-break-while-left"', false);
});

it('executes the documented BREAK without processing later items', function (array $items, array $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/break-continue/code-examples/break-while.php');
    $harness = '$items = '.var_export($items, true).';' . <<<'HARNESS'
$events = [];
function loadItems() { return $GLOBALS['items']; }
function mayProcess($item) { $GLOBALS['events'][] = 'check:'.$item; return $item !== 'stop'; }
function processItem($item) { $GLOBALS['events'][] = 'process:'.$item; }
function continueProcess() { $GLOBALS['events'][] = 'after'; }
HARNESS;
    $p = new Process([PHP_BINARY, '-r', $harness.substr(file_get_contents($path), 5).';echo json_encode($events);']);
    $p->setTimeout(5);
    $p->mustRun();
    expect(json_decode($p->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe($expected);
})->with([
    [[], ['after']],
    [['stop', 'a'], ['check:stop', 'after']],
    [['a', 'stop', 'c'], ['check:a', 'process:a', 'check:stop', 'after']],
    [['a', 'b'], ['check:a', 'process:a', 'check:b', 'process:b', 'after']],
]);

it('preserves the saved BREAK example with mirrored independent previews', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-while')->render();
    $ends = [];
    foreach (['left', 'right'] as $side) {
        $point = function ($suffix) use ($side) {
            $a = AnchorRegistry::get('idea-to-paper-break-while-'.$side, 'literature.break.while.'.$side.'.'.$suffix);
            expect($a)->not->toBeNull($suffix);
            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        };
        expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-return'));
        expect($point('break-join.anchorNode-end'))->toBe($point('normal-exit.anchorNode-end'));
        $ends[$side] = $point('break-rise.anchorNode-end');
    }
    expect($ends['right'])->toBe([-$ends['left'][0], $ends['left'][1]]);
    expect($html)->toContain('idea-to-paper-break-while-left', 'idea-to-paper-break-while-right');
});

it('routes FOR CONTINUE through the update before rechecking the condition', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-for')->render();
    $point = function ($suffix) {
        $a = AnchorRegistry::get('idea-to-paper-continue-for-left', 'literature.continue.for.left.'.$suffix);
        expect($a)->not->toBeNull($suffix);
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    };
    expect($point('decision.true.anchorNode-return'))->toBe($point('decision.false.anchorNode-return'));
    expect($point('body-return.anchorNode-start'))->toBe($point('advance.anchorNode-end'));
    expect($point('advance.anchorNode-end')[1])->toBeLessThan($point('decision.anchorNode-end')[1]);
    expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-return'));
    expect($point('body-return.anchorNode-end'))->not->toBe($point('loop.anchorNode-end'));
    expect($html)->toContain('TRUE: CONTINUE', 'FALSE: Process item', 'Show summary after FOR', 'FOR update')->not->toContain('break-join');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    expect($xp->query('//pre/code')->length)->toBe(8);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-continue-for-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 16));
});

it('loads CONTINUE lazily and restores its reference selection', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.break-continue.flow-continue-nested')
        ->assertSet('tabs.flow_break_continue', 'flow-continue-nested')
        ->assertSee('id="idea-to-paper-continue-nested-left"', false)
        ->assertDontSee('id="idea-to-paper-break-while-left"', false)
        ->call('openReference', 'strang.flow-if-else', 'flow.break-continue.flow-continue-nested')
        ->assertDontSee('id="idea-to-paper-continue-nested-left"', false)
        ->call('returnToExample')
        ->assertSee('id="idea-to-paper-continue-nested-left"', false);
});

it('executes CONTINUE with progress even if every item is skipped', function (array $items, array $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/break-continue/code-examples/continue-for.php');
    $harness = '$items = '.var_export($items, true).';' . <<<'HARNESS'
$events = [];
function loadItems() { return $GLOBALS['items']; }
function shouldSkip($item) { $GLOBALS['events'][] = 'check:'.$item; return $item === 'skip'; }
function processItem($item) { $GLOBALS['events'][] = 'process:'.$item; }
function showSummary() { $GLOBALS['events'][] = 'summary'; }
HARNESS;
    $p = new Process([PHP_BINARY, '-r', $harness.substr(file_get_contents($path), 5).';echo json_encode($events);']);
    $p->setTimeout(5);
    $p->mustRun();
    expect(json_decode($p->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe($expected);
})->with([
    [[], ['summary']],
    [['skip', 'skip'], ['check:skip', 'check:skip', 'summary']],
    [['a', 'skip', 'c'], ['check:a', 'process:a', 'check:skip', 'check:c', 'process:c', 'summary']],
    [['a', 'b'], ['check:a', 'process:a', 'check:b', 'process:b', 'summary']],
]);

it('keeps WHILE CONTINUE in two mirrored saved previews without a FOR update', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-while')->render();
    $ends = [];
    foreach (['left', 'right'] as $side) {
        $point = function ($suffix) use ($side) {
            $a = AnchorRegistry::get('idea-to-paper-continue-while-'.$side, 'literature.continue.while.'.$side.'.'.$suffix);
            expect($a)->not->toBeNull($suffix);
            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        };
        expect($point('body-return.anchorNode-start'))->toBe($point('decision.anchorNode-end'));
        expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-return'));
        $ends[$side] = $point('decision.anchorNode-end');
    }
    expect($ends['right'])->toBe([-$ends['left'][0], $ends['left'][1]]);
    expect($html)->toContain('Show summary after WHILE')->not->toContain('FOR update:', 'Protected operations may throw');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    expect($xp->query('//pre/code')->length)->toBe(8);
});

it('opens the saved WHILE CONTINUE example through deep reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.break-continue.flow-continue-while')
        ->assertSet('tabs.flow_break_continue', 'flow-continue-while')
        ->assertSee('id="idea-to-paper-continue-while-left"', false)
        ->assertSee('id="idea-to-paper-continue-while-right"', false)
        ->assertDontSee('id="idea-to-paper-continue-test-left"', false);
});

it('keeps the two nested loop return targets separate and counts every node', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-nested')->render();
    $point = function ($suffix) {
        $a = AnchorRegistry::get('idea-to-paper-continue-nested-left', 'literature.continue.nested.left.'.$suffix);
        expect($a)->not->toBeNull($suffix);
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    };
    expect($point('inner-return.anchorNode-start'))->toBe($point('decision.anchorNode-end'));
    expect($point('inner-return.anchorNode-end'))->toBe($point('inner-loop.anchorNode-return'));
    expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-return'));
    expect($point('inner-return.anchorNode-end'))->not->toBe($point('body-return.anchorNode-end'));
    expect($html)->toContain('TRUE: CONTINUE', 'itemIndex = itemIndex + 1', 'groupIndex = groupIndex + 1');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    expect($xp->query('//pre/code')->length)->toBe(8);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-continue-nested-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 32));
});

it('continues the inner loop without skipping the rest of its group or later groups', function (array $groups, array $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/break-continue/code-examples/continue-nested.php');
    $harness = '$groups = '.var_export($groups, true).';' . <<<'HARNESS'
$events = [];
function loadGroups() { return $GLOBALS['groups']; }
function shouldSkip($item) { $GLOBALS['events'][] = 'check:'.$item; return $item === 'skip'; }
function processItem($item) { $GLOBALS['events'][] = 'process:'.$item; }
function showSummary() { $GLOBALS['events'][] = 'summary'; }
HARNESS;
    $p = new Process([PHP_BINARY, '-r', $harness.substr(file_get_contents($path), 5).';echo json_encode([$events, $groupIndex]);']);
    $p->setTimeout(5);
    $p->mustRun();
    expect(json_decode($p->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([$expected, count($groups)]);
})->with([
    [[], ['summary']],
    [[[], []], ['summary']],
    [[['skip', 'skip'], [], ['skip']], ['check:skip', 'check:skip', 'check:skip', 'summary']],
    [[['skip', 'a'], [], ['b']], ['check:skip', 'check:a', 'process:a', 'check:b', 'process:b', 'summary']],
]);

it('exits only the inner loop on BREAK and joins before the outer advance', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-nested')->render();
    $point = function ($suffix) {
        $a = AnchorRegistry::get('idea-to-paper-break-nested-left', 'literature.break.nested.left.'.$suffix);
        expect($a)->not->toBeNull($suffix);
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    };
    expect($point('inner-return.anchorNode-start'))->toBe($point('decision.true.anchorNode-end'));
    expect($point('inner-return.anchorNode-end'))->toBe($point('inner-loop.anchorNode-return'));
    expect($point('break-join.anchorNode-end'))->toBe($point('inner-exit.anchorNode-end'));
    expect($point('break-join.anchorNode-end'))->not->toBe($point('loop.anchorNode-end'));
    expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-return'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-break-nested-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 33));
});

it('keeps processing later groups after an inner BREAK', function (array $groups, array $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/break-continue/code-examples/break-nested.php');
    $harness = '$groups = '.var_export($groups, true).';' . <<<'HARNESS'
$events = [];
function loadGroups() { return $GLOBALS['groups']; }
function mayProcess($item) { $GLOBALS['events'][] = 'check:'.$item; return $item !== 'stop'; }
function processItem($item) { $GLOBALS['events'][] = 'process:'.$item; }
function showSummary() { $GLOBALS['events'][] = 'summary'; }
HARNESS;
    $p = new Process([PHP_BINARY, '-r', $harness.substr(file_get_contents($path), 5).';echo json_encode([$events, $groupIndex]);']);
    $p->setTimeout(5);
    $p->mustRun();
    expect(json_decode($p->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([$expected, count($groups)]);
})->with([
    [[], ['summary']],
    [[[], []], ['summary']],
    [[['stop', 'a'], ['b']], ['check:stop', 'check:b', 'process:b', 'summary']],
    [[['a', 'stop', 'c'], [], ['d']], ['check:a', 'process:a', 'check:stop', 'check:d', 'process:d', 'summary']],
]);

it('places FINALLY before both the BREAK exit and the normal return', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-test')->render();
    $point = function ($suffix) {
        $a = AnchorRegistry::get('idea-to-paper-break-test-left', 'literature.break.test.'.$suffix);
        expect($a)->not->toBeNull($suffix);
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    };
    expect($point('question.stem.anchorNode-end')[0])->toBe($point('loop.body.anchorNode-end')[0]);
    expect($point('question.stem.anchorNode-end')[1])->toEqual($point('loop.body.anchorNode-end')[1] - 4);
    expect($point('body-return.anchorNode-start'))->toBe($point('normal-finally.anchorNode-end'));
    expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-return'));
    expect($point('break-finally.anchorNode-end')[1])->toBeGreaterThan($point('decision.outputs.false.anchorNode-end')[1]);
    expect($point('break-join.anchorNode-end'))->toBe($point('normal-exit.anchorNode-end'));
    expect($point('break-join.anchorNode-end'))->not->toBe($point('loop.anchorNode-return'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    // A zero-length return stem is omitted and therefore has no counter marker.
    $expected = range(1, 23);
    if ($xp->query('//*[@data-tw-graph-path="literature.break.test.body-return.stem"]')->length === 0) {
        $expected = array_values(array_diff($expected, [18]));
    }
    expect($numbers)->toBe($expected);
});

it('runs the actual FINALLY excerpt exactly once before normal continuation or BREAK', function (array $items, array $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/break-continue/code-examples/break-test.php');
    $harness = '$items = '.var_export($items, true).';' . <<<'HARNESS'
$events = [];
function loadItems() { return $GLOBALS['items']; }
function mayProcess($item) { return $item !== 'stop'; }
function processItem($item) { $GLOBALS['events'][] = 'process:'.$item; }
function cleanupItem($item) { $GLOBALS['events'][] = 'cleanup:'.$item; }
function showSummary() { $GLOBALS['events'][] = 'summary'; }
HARNESS;
    $p = new Process([PHP_BINARY, '-r', $harness.substr(file_get_contents($path), 5).';echo json_encode($events);']);
    $p->setTimeout(5);
    $p->mustRun();
    expect(json_decode($p->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe($expected);
})->with([
    [[], ['summary']],
    [['stop', 'a'], ['cleanup:stop', 'summary']],
    [['a', 'stop', 'b'], ['process:a', 'cleanup:a', 'cleanup:stop', 'summary']],
    [['a', 'b'], ['process:a', 'cleanup:a', 'process:b', 'cleanup:b', 'summary']],
]);

it('saves the adjusted FINALLY example as independent mirrored previews', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-finally')->render();
    $ends = [];
    foreach (['left', 'right'] as $side) {
        $point = function ($suffix) use ($side) {
            $a = AnchorRegistry::get('idea-to-paper-break-finally-'.$side, 'literature.break.finally.'.$side.'.'.$suffix);
            expect($a)->not->toBeNull($suffix);
            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        };
        expect($point('body-return.anchorNode-start'))->toBe($point('normal-finally.anchorNode-end'));
        expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-return'));
        expect($point('break-join.anchorNode-end'))->toBe($point('normal-exit.anchorNode-end'));
        $ends[$side] = $point('break-rise.anchorNode-end');
    }
    expect($ends['right'])->toBe([-$ends['left'][0], $ends['left'][1]]);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    expect($xp->query('//pre/code')->length)->toBe(8);
    foreach (['left', 'right'] as $side) {
        $numbers = [];
        foreach ($xp->query('//*[@id="idea-to-paper-break-finally-'.$side.'"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
            $numbers[] = (int) trim($node->textContent);
        }
        sort($numbers);
        expect($numbers)->toBe(range(1, 18));
    }
});

it('opens and restores the saved BREAK with FINALLY tab', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.break-continue.flow-break-finally')
        ->assertSet('tabs.flow_break_continue', 'flow-break-finally')
        ->assertSee('id="idea-to-paper-break-finally-left"', false)
        ->assertSee('id="idea-to-paper-break-finally-right"', false)
        ->call('openReference', 'strang.flow-if-else', 'flow.break-continue.flow-break-finally')
        ->assertDontSee('id="idea-to-paper-break-finally-left"', false)
        ->call('returnToExample')
        ->assertSee('id="idea-to-paper-break-finally-right"', false);
});


it('keeps the BREAK preview usable when the authored condition leaves too little return space', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-test';
    $source = file_get_contents(app('view')->getFinder()->find($view));
    $source = preg_replace("/('afterLength' => ')[^']+(')/", '${1}16rem$2', $source, 1);
    $html = \Illuminate\Support\Facades\Blade::render($source);
    expect($html)->toContain('data-tw-graph-layout-issues', 'remainingStemLength', 'literature.break.test.body-return')
        ->toContain('data-tw-graph-path="literature.break.test.continue.stem.before"')
        ->not->toContain('data-tw-graph-path="literature.break.test.body-return.arc-in"');
});

it('renders the horizontal BREAK FINALLY decision as two independent mirrored examples', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-finally-2';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $point = function ($side, $suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-break-finally-2-'.$side, 'literature.break.finally-2.'.$side.'.'.$suffix);
        expect($anchor)->not->toBeNull($side.'.'.$suffix);
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    foreach (['loop.body.anchorNode-end', 'question.stem.anchorNode-end', 'decision.outputs.true.anchorNode-end', 'decision.outputs.false.anchorNode-end', 'normal-finally.anchorNode-end', 'break-finally.anchorNode-end', 'body-return.anchorNode-start', 'body-return.anchorNode-end', 'break-join.anchorNode-end', 'continue.anchorNode-end'] as $suffix) {
        [$x, $y] = $point('left', $suffix);
        expect($point('right', $suffix))->toEqual([-$x, $y]);
    }
    foreach (['left', 'right'] as $side) {
        expect($point($side, 'body-return.anchorNode-end'))->toEqual($point($side, 'loop.anchorNode-return'));
        expect($point($side, 'break-join.anchorNode-end'))->toEqual($point($side, 'normal-exit.anchorNode-end'));
    }
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', '@for(');
    $examples = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView($view);
    foreach (['left', 'right'] as $side) {
        expect($examples->example('break-finally-2-'.$side.'-example'))->toContain('literature.break.finally-2.'.$side.'.loop');
    }
});

it('loads BREAK with FINALLY 2 lazily and returns from its deep reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->assertDontSee('id="idea-to-paper-break-finally-2-left"', false)
        ->call('openExample', 'flow.break-continue.flow-break-finally-2')
        ->assertSet('tabs.flow_break_continue', 'flow-break-finally-2')
        ->assertSee('id="idea-to-paper-break-finally-2-left"', false)
        ->assertSee('id="idea-to-paper-break-finally-2-right"', false)
        ->call('openReference', 'parts.split', 'flow.break-continue.flow-break-finally-2')
        ->call('returnToExample')
        ->assertSet('tabs.flow_break_continue', 'flow-break-finally-2');
});

it('routes pending CONTINUE and normal processing through FINALLY before the WHILE return', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-test')->render();
    $point = function ($suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-continue-test-left', 'literature.continue.test.'.$suffix);
        expect($anchor)->not->toBeNull($suffix);
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    [$joinX, $joinY] = $point('decision.anchorNode-end');
    expect($point('finally.anchorNode-end'))->toEqual([$joinX, $joinY - 8]);
    expect($point('body-return.anchorNode-start'))->toBe($point('finally.anchorNode-end'));
    expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-return'));
    expect($point('continue.anchorNode-end')[1])->toBeGreaterThan($point('loop.anchorNode-end')[1]);
    expect($html)->toContain('TRUE: CONTINUE pending', 'Cleanup current item')->not->toContain('data-tw-graph-layout-issues');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 16));
});

it('executes FINALLY once for processed and skipped items in the actual CONTINUE excerpt', function ($items, $expected, $example) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/break-continue/code-examples/'.$example.'.php');
    $harness = '$items = '.var_export($items, true).';' . <<<'HARNESS'
$events = [];
function loadItems() { return $GLOBALS['items']; }
function shouldSkip($item) { $GLOBALS['events'][] = 'check:'.$item; return $item === 'skip'; }
function processItem($item) { $GLOBALS['events'][] = 'process:'.$item; }
function cleanupItem($item) { $GLOBALS['events'][] = 'cleanup:'.$item; }
function showSummary() { $GLOBALS['events'][] = 'summary'; }
HARNESS;
    $process = new Process([PHP_BINARY, '-r', $harness.substr(file_get_contents($path), 5).';echo json_encode([$events, $index]);']);
    $process->setTimeout(5);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([$expected, count($items)]);
})->with([
    [[], ['summary']],
    [['skip'], ['check:skip', 'cleanup:skip', 'summary']],
    [['a'], ['check:a', 'process:a', 'cleanup:a', 'summary']],
    [['skip', 'a', 'skip'], ['check:skip', 'cleanup:skip', 'check:a', 'process:a', 'cleanup:a', 'check:skip', 'cleanup:skip', 'summary']],
])
    ->with(['continue-test', 'continue-finally', 'continue-finally-2']);

it('hides retired BREAK and CONTINUE test tabs and recovers an old tab selection', function () {
    $component = Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.break-continue.flow-continue-finally')
        ->assertSee('BREAK with FINALLY (1)')
        ->assertDontSee('BREAK Test')
        ->assertDontSee('CONTINUE Test');
    foreach (['flow-break-test', 'flow-continue-test'] as $tab) {
        $component->set('tabs.flow_break_continue', $tab)
            ->assertSet('tabs.flow_break_continue', 'flow-break-while')
            ->assertSee('id="idea-to-paper-break-while-left"', false)
            ->assertDontSee('id="idea-to-paper-break-test-left"', false)
            ->assertDontSee('id="idea-to-paper-continue-test-left"', false);
        expect(\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\DocumentationLinks::EXAMPLES)
            ->not->toHaveKey('flow.break-continue.'.$tab);
    }
});

it('saves two independent mirrored CONTINUE FINALLY examples with cleanup before each return', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-finally';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $point = function ($side, $suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-continue-finally-'.$side, 'literature.continue.finally.'.$side.'.'.$suffix);
        expect($anchor)->not->toBeNull($side.'.'.$suffix);
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    foreach (['loop.body.anchorNode-end', 'decision.anchorNode-end', 'finally.anchorNode-end', 'body-return.anchorNode-start', 'body-return.anchorNode-end', 'continue.anchorNode-end'] as $suffix) {
        [$x, $y] = $point('left', $suffix);
        expect($point('right', $suffix))->toEqual([-$x, $y]);
    }
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    foreach (['left', 'right'] as $side) {
        expect($point($side, 'body-return.anchorNode-start'))->toBe($point($side, 'finally.anchorNode-end'));
        expect($point($side, 'body-return.anchorNode-end'))->toBe($point($side, 'loop.anchorNode-return'));
        $numbers = [];
        foreach ($xp->query('//*[@id="idea-to-paper-continue-finally-'.$side.'"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
            $numbers[] = (int) trim($node->textContent);
        }
        sort($numbers);
        expect($numbers)->toBe(range(1, 16));
        expect(\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView($view)->example('continue-finally-'.$side.'-example'))->toContain('literature.continue.finally.'.$side.'.finally');
    }
    expect(file_get_contents(app('view')->getFinder()->find($view)))->not->toContain('@foreach');

});

it('opens the saved CONTINUE with FINALLY tab and restores it from deep reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.break-continue.flow-continue-finally')
        ->assertSet('tabs.flow_break_continue', 'flow-continue-finally')
        ->assertSee('id="idea-to-paper-continue-finally-left"', false)
        ->assertSee('id="idea-to-paper-continue-finally-right"', false)
        ->assertDontSee('id="idea-to-paper-continue-test-left"', false)
        ->call('openReference', 'strang.flow-step', 'flow.break-continue.flow-continue-finally')
        ->call('returnToExample')
        ->assertSet('tabs.flow_break_continue', 'flow-continue-finally');
});

it('mirrors the horizontal CONTINUE FINALLY split and rejoins before cleanup', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-finally-2';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $point = function ($side, $suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-continue-finally-2-'.$side, 'literature.continue.finally-2.'.$side.'.'.$suffix);
        expect($anchor)->not->toBeNull($side.'.'.$suffix);
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    foreach (['loop.body.anchorNode-end', 'question.stem.anchorNode-end', 'decision.outputs.true.anchorNode-end', 'decision.outputs.false.anchorNode-end', 'cleanup-join.anchorNode-end', 'finally.anchorNode-end', 'body-return.anchorNode-start', 'body-return.anchorNode-end', 'continue.anchorNode-end'] as $suffix) {
        [$x, $y] = $point('left', $suffix);
        expect($point('right', $suffix))->toEqual([-$x, $y]);
    }
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    foreach (['left', 'right'] as $side) {
        expect($point($side, 'body-return.anchorNode-start'))->toEqual($point($side, 'finally.anchorNode-end'));
        expect($point($side, 'body-return.anchorNode-end'))->toEqual($point($side, 'loop.anchorNode-return'));
        expect($point($side, 'body-return.anchorNode-end'))->not->toEqual($point($side, 'loop.anchorNode-end'));
        $numbers = [];
        foreach ($xp->query('//*[@id="idea-to-paper-continue-finally-2-'.$side.'"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
            $numbers[] = (int) trim($node->textContent);
        }
        sort($numbers);
        expect($numbers)->toBe(range(1, 21));
        expect(\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView($view)->example('continue-finally-2-'.$side.'-example'))->toContain('literature.continue.finally-2.'.$side.'.cleanup-join');
    }
    expect(file_get_contents(app('view')->getFinder()->find($view)))->not->toContain('@foreach');
});

it('opens the second CONTINUE FINALLY variant and restores it from deep reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.break-continue.flow-continue-finally-2')
        ->assertSet('tabs.flow_break_continue', 'flow-continue-finally-2')
        ->assertSee('CONTINUE with FINALLY (1)')
        ->assertSee('BREAK with FINALLY (1)')
        ->assertSee('id="idea-to-paper-continue-finally-2-left"', false)
        ->assertSee('id="idea-to-paper-continue-finally-2-right"', false)
        ->call('openReference', 'parts.fusion', 'flow.break-continue.flow-continue-finally-2')
        ->call('returnToExample')
        ->assertSet('tabs.flow_break_continue', 'flow-continue-finally-2');
});
