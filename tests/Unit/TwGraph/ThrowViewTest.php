<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource;
use Livewire\Livewire;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class);

it('connects THROW to CATCH and then to normal continuation', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-catch';
    $html = view($view)->render();
    $point = function ($suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-throw-catch-left', 'literature.throw.caught.left.'.$suffix);
        expect($anchor)->not->toBeNull($suffix);
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    $raised = $point('raise.anchorNode-end');
    $caught = $point('catch.anchorNode-end');
    $continued = $point('continue.anchorNode-end');
    expect($raised[0])->toBeLessThan($point('try.anchorNode-end')[0]);
    expect($caught[0])->toEqual($raised[0]);
    expect($caught[1])->toBeGreaterThan($raised[1]);
    expect($continued[0])->toEqual($caught[0]);
    expect($continued[1])->toBeGreaterThan($caught[1]);
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-throw-catch-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 5));
    expect(ExampleSource::fromView($view)->example('throw-catch-left-example'))
        ->toContain('literature.throw.caught.left.raise', 'literature.throw.caught.left.catch', 'literature.throw.caught.left.continue');
});

it('skips the remaining TRY action and continues after the matching handler', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/code-examples/throw-catch.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';$events=[];demonstrateThrow($events);echo json_encode($events);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe(['try', 'caught', 'after']);
});

it('lazily opens THROW and returns from its component reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->assertDontSee('id="idea-to-paper-throw-foreach-catch-left"', false)
        ->call('openExample', 'flow.throw.flow-throw-foreach-catch')
        ->assertSet('tabs.flow_index', 'flow-throw')
        ->assertSet('tabs.flow_throw', 'flow-throw-foreach-catch')
        ->assertSee('id="idea-to-paper-throw-foreach-catch-left"', false)
        ->assertSee('Suggested sequence')
        ->call('openReference', 'strang.flow-step', 'flow.throw.flow-throw-foreach-catch')
        ->assertDontSee('id="idea-to-paper-throw-foreach-catch-left"', false)
        ->call('returnToExample')
        ->assertSet('tabs.flow_throw', 'flow-throw-foreach-catch')
        ->assertSee('id="idea-to-paper-throw-foreach-catch-left"', false);
});

it('mirrors the saved matching CATCH example and supports reference navigation', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-catch';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['raise.anchorNode-end', 'catch.anchorNode-end', 'continue.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-throw-catch-'.$side, 'literature.throw.caught.'.$side.'.'.$suffix);
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    foreach (['left', 'right'] as $side) {
        expect(ExampleSource::fromView($view)->example('throw-catch-'.$side.'-example'))
            ->toContain('literature.throw.caught.'.$side.'.raise', 'literature.throw.caught.'.$side.'.catch');
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.throw.flow-throw-catch')
        ->assertSet('tabs.flow_throw', 'flow-throw-catch')
        ->assertSee('id="idea-to-paper-throw-catch-left"', false)
        ->assertSee('id="idea-to-paper-throw-catch-right"', false)
        ->assertDontSee('id="idea-to-paper-throw-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.throw.flow-throw-catch')
        ->call('returnToExample')
        ->assertSet('tabs.flow_throw', 'flow-throw-catch');
});

it('propagates from the called function past both unreachable actions', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/code-examples/throw-propagation.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';$events=[];demonstratePropagation($events);echo json_encode($events);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe(['outer-try', 'inner', 'outer-caught', 'after']);
});

it('connects the helper throw directly to the outer handler with consecutive counters', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-propagation';
    $html = view($view)->render();
    $point = function ($suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-throw-propagation-left', 'literature.throw.propagation.left.'.$suffix);
        expect($anchor)->not->toBeNull();
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    expect($point('inner.anchorNode-end')[1])->toBeGreaterThan($point('try.anchorNode-end')[1]);
    expect($point('raise.anchorNode-end')[0])->toBeLessThan($point('inner.anchorNode-end')[0]);
    expect($point('catch.anchorNode-end')[0])->toEqual($point('raise.anchorNode-end')[0]);
    expect($point('continue.anchorNode-end')[1])->toBeGreaterThan($point('catch.anchorNode-end')[1]);
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-throw-propagation-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 6));
    expect(ExampleSource::fromView($view)->example('throw-propagation-left-example'))->toContain('literature.throw.propagation.left.inner', 'Outer CATCH');
});

it('mirrors saved propagation and supports its lazy reference round trip', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-propagation')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['inner.anchorNode-end', 'raise.anchorNode-end', 'catch.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-throw-propagation-'.$side, 'literature.throw.propagation.'.$side.'.'.$suffix);
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.throw.flow-throw-propagation')
        ->assertSet('tabs.flow_throw', 'flow-throw-propagation')
        ->assertSee('id="idea-to-paper-throw-propagation-left"', false)
        ->assertSee('id="idea-to-paper-throw-propagation-right"', false)
        ->assertDontSee('id="idea-to-paper-throw-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.throw.flow-throw-propagation')
        ->call('returnToExample')
        ->assertSet('tabs.flow_throw', 'flow-throw-propagation');
});

it('logs before rethrow and skips every interrupted continuation', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/code-examples/throw-rethrow.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';$events=[];demonstrateRethrow($events);echo json_encode($events);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe(['outer-try', 'inner', 'inner-log', 'outer-caught', 'after']);
});

it('renders logging and rethrow as separate connected actions', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-rethrow';
    $html = view($view)->render();
    $previous = null;
    foreach (['raise', 'inner-catch', 'rethrow', 'catch', 'continue'] as $part) {
        $anchor = AnchorRegistry::get('idea-to-paper-throw-rethrow-left', 'literature.throw.rethrow.left.'.$part.'.anchorNode-end');
        expect($anchor)->not->toBeNull();
        $point = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        if ($previous !== null) {
            expect($point[0])->toEqual($previous[0]);
            expect($point[1])->toBeGreaterThan($previous[1]);
        }
        $previous = $point;
    }
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-throw-rethrow-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 8));
    expect(ExampleSource::fromView($view)->example('throw-rethrow-left-example'))->toContain('literature.throw.rethrow.left.inner-catch', 'literature.throw.rethrow.left.rethrow');
});

it('mirrors saved rethrow and supports its lazy reference round trip', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-rethrow')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['inner.anchorNode-end', 'raise.anchorNode-end', 'catch.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-throw-rethrow-'.$side, 'literature.throw.rethrow.'.$side.'.'.$suffix);
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.throw.flow-throw-rethrow')
        ->assertSet('tabs.flow_throw', 'flow-throw-rethrow')
        ->assertSee('id="idea-to-paper-throw-rethrow-left"', false)
        ->assertSee('id="idea-to-paper-throw-rethrow-right"', false)
        ->assertDontSee('id="idea-to-paper-throw-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.throw.flow-throw-rethrow')
        ->call('returnToExample')
        ->assertSet('tabs.flow_throw', 'flow-throw-rethrow');
});


it('executes FINALLY once before the pending exception reaches the outer handler', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/code-examples/throw-finally.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';$events=[];demonstrateFinally($events);echo json_encode($events);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe(['outer-try', 'inner', 'inner-log', 'cleanup', 'outer-caught', 'after']);
});

it('places FINALLY between RETHROW and the outer CATCH', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally';
    $html = view($view)->render();
    $previous = null;
    foreach (['raise', 'inner-catch', 'rethrow', 'finally', 'catch', 'continue'] as $part) {
        $anchor = AnchorRegistry::get('idea-to-paper-throw-finally-left', 'literature.throw.finally.left.'.$part.'.anchorNode-end');
        expect($anchor)->not->toBeNull();
        $point = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        if ($previous !== null) {
            expect($point[0])->toEqual($previous[0]);
            expect($point[1])->toBeGreaterThan($previous[1]);
        }
        $previous = $point;
    }
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-throw-finally-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 9));
    expect(ExampleSource::fromView($view)->example('throw-finally-left-example'))->toContain('literature.throw.finally.left.inner-catch', 'literature.throw.finally.left.rethrow');
});


it('mirrors saved FINALLY propagation and supports its lazy reference round trip', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['inner.anchorNode-end', 'raise.anchorNode-end', 'catch.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-throw-finally-'.$side, 'literature.throw.finally.'.$side.'.'.$suffix);
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.throw.flow-throw-finally')
        ->assertSet('tabs.flow_throw', 'flow-throw-finally')
        ->assertSee('id="idea-to-paper-throw-finally-left"', false)
        ->assertSee('id="idea-to-paper-throw-finally-right"', false)
        ->assertDontSee('id="idea-to-paper-throw-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.throw.flow-throw-finally')
        ->call('returnToExample')
        ->assertSet('tabs.flow_throw', 'flow-throw-finally');
});



it('cancels the pending return when FINALLY throws and leaves the caller assignment unchanged', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/code-examples/throw-finally-return.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';$events=[];$result=demonstrateFinallyThrow($events);echo json_encode([$result,$events]);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([null, ['outer-try', 'pending-return', 'cleanup', 'outer-caught', 'after']]);
});

it('connects the pending RETURN to cleanup and the cleanup exception handler', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally-return';
    $html = view($view)->render();
    $point = function ($part) {
        $anchor = AnchorRegistry::get('idea-to-paper-throw-finally-return-left', 'literature.throw.finally-return.left.'.$part.'.anchorNode-end');
        expect($anchor)->not->toBeNull();
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    expect($point('finally')[1])->toBeGreaterThan($point('pending-return')[1]);
    expect($point('raise')[0])->toBeLessThan($point('finally')[0]);
    expect($point('catch')[0])->toEqual($point('raise')[0]);
    expect($point('continue')[1])->toBeGreaterThan($point('catch')[1]);
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-throw-finally-return-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 7));
    expect(ExampleSource::fromView($view)->example('throw-finally-return-left-example'))->toContain('literature.throw.finally-return.left.pending-return', 'Cancel pending RETURN');
});

it('mirrors saved FINALLY return replacement and supports its lazy reference round trip', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally-return')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['pending-return.anchorNode-end', 'raise.anchorNode-end', 'catch.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-throw-finally-return-'.$side, 'literature.throw.finally-return.'.$side.'.'.$suffix);
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.throw.flow-throw-finally-return')
        ->assertSet('tabs.flow_throw', 'flow-throw-finally-return')
        ->assertSee('id="idea-to-paper-throw-finally-return-left"', false)
        ->assertSee('id="idea-to-paper-throw-finally-return-right"', false)
        ->assertDontSee('id="idea-to-paper-throw-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.throw.flow-throw-finally-return')
        ->call('returnToExample')
        ->assertSet('tabs.flow_throw', 'flow-throw-finally-return');
});




it('propagates the cleanup exception and retains the original as its previous exception', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/code-examples/throw-finally-exception.php');
    $code = substr(file_get_contents($path), 5).';$events=[];$error=demonstrateReplacement($events);echo json_encode([$events,get_class($error),$error->getMessage(),get_class($error->getPrevious()),$error->getPrevious()->getMessage()]);';
    $process = new Process([PHP_BINARY, '-r', $code]);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([
        ['outer-try', 'original', 'cleanup', 'outer-caught', 'after'],
        'RuntimeException', 'Cleanup failed', 'InvalidArgumentException', 'Original failure',
    ]);
});

it('connects the original failure to cleanup and the replacement exception handler', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally-exception';
    $html = view($view)->render();
    $point = function ($part) {
        $anchor = AnchorRegistry::get('idea-to-paper-throw-finally-exception-left', 'literature.throw.finally-exception.left.'.$part.'.anchorNode-end');
        expect($anchor)->not->toBeNull();
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    expect($point('finally')[1])->toBeGreaterThan($point('original-error')[1]);
    expect($point('raise')[0])->toBeLessThan($point('finally')[0]);
    expect($point('catch')[0])->toEqual($point('raise')[0]);
    expect($point('continue')[1])->toBeGreaterThan($point('catch')[1]);
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-throw-finally-exception-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 7));
    expect(ExampleSource::fromView($view)->example('throw-finally-exception-left-example'))->toContain('literature.throw.finally-exception.left.original-error', 'Replace original exception');
});


it('mirrors saved FINALLY exception replacement and supports its lazy reference round trip', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally-exception')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['original-error.anchorNode-end', 'raise.anchorNode-end', 'catch.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-throw-finally-exception-'.$side, 'literature.throw.finally-exception.'.$side.'.'.$suffix);
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.throw.flow-throw-finally-exception')
        ->assertSet('tabs.flow_throw', 'flow-throw-finally-exception')
        ->assertSee('id="idea-to-paper-throw-finally-exception-left"', false)
        ->assertSee('id="idea-to-paper-throw-finally-exception-right"', false)
        ->assertDontSee('id="idea-to-paper-throw-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.throw.flow-throw-finally-exception')
        ->call('returnToExample')
        ->assertSet('tabs.flow_throw', 'flow-throw-finally-exception');
});

it('connects FINALLY RETURN to the caller without entering CATCH', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-return-finally';
    $html = view($view)->render();
    $point = function ($part) {
        $anchor = AnchorRegistry::get('idea-to-paper-throw-return-finally-left', 'literature.throw.return-finally.left.'.$part.'.anchorNode-end');
        expect($anchor)->not->toBeNull();
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    expect($point('finally')[1])->toBeGreaterThan($point('original-error')[1]);
    expect($point('return')[0])->toBeLessThan($point('finally')[0]);
    expect($point('caller')[0])->toEqual($point('return')[0]);
    expect($point('continue')[1])->toBeGreaterThan($point('caller')[1]);
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-throw-return-finally-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 7));
    expect(ExampleSource::fromView($view)->example('throw-return-finally-left-example'))->toContain('literature.throw.return-finally.left.original-error', 'Replace pending exception');
});

it('suppresses the pending exception when FINALLY returns a value', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/code-examples/throw-return-finally.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';$events=[];$result=demonstrateFinallyReturn($events);echo json_encode([$result,$events]);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([
        7, ['outer-try', 'original', 'cleanup', 'received', 'after'],
    ]);
});

it('mirrors saved RETURN inside FINALLY and supports its lazy reference round trip', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-return-finally')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['original-error.anchorNode-end', 'return.anchorNode-end', 'caller.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-throw-return-finally-'.$side, 'literature.throw.return-finally.'.$side.'.'.$suffix);
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.throw.flow-throw-return-finally')
        ->assertSet('tabs.flow_throw', 'flow-throw-return-finally')
        ->assertSee('id="idea-to-paper-throw-return-finally-left"', false)
        ->assertSee('id="idea-to-paper-throw-return-finally-right"', false)
        ->assertDontSee('id="idea-to-paper-throw-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.throw.flow-throw-return-finally')
        ->call('returnToExample')
        ->assertSet('tabs.flow_throw', 'flow-throw-return-finally');
});

it('runs cleanup before an unhandled exception leaves the call and skips normal continuation', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/code-examples/throw-unhandled.php');
    $process = new Process([PHP_BINARY, '-d', 'display_errors=stderr', '-r', substr(file_get_contents($path), 5).';demonstrateUnhandled();']);
    $process->run();
    expect($process->isSuccessful())->toBeFalse();
    expect($process->getOutput())->toBe("call\noriginal\ncleanup\n");
    expect($process->getErrorOutput())->toContain('Uncaught InvalidArgumentException: Original failure');
    expect($process->getOutput().$process->getErrorOutput())->not->toContain('unreachable-after-call');
});

it('ends the unhandled example at an exception boundary without a recovery route', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-unhandled';
    $html = view($view)->render();
    $points = [];
    foreach (['original-error', 'finally', 'propagate', 'boundary'] as $part) {
        $anchor = AnchorRegistry::get('idea-to-paper-throw-unhandled-left', 'literature.throw.unhandled.left.'.$part.'.anchorNode-end');
        expect($anchor)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    }
    expect($points['finally'][1])->toBeGreaterThan($points['original-error'][1]);
    expect($points['propagate'][0])->toBeLessThan($points['finally'][0]);
    expect($points['boundary'][0])->toEqual($points['propagate'][0]);
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $source = ExampleSource::fromView($view)->example('throw-unhandled-left-example');
    expect($source)->toContain('literature.throw.unhandled.left.boundary', 'No normal continuation')
        ->not->toContain('literature.throw.unhandled.left.catch', 'literature.throw.unhandled.left.continue');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-throw-unhandled-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 6));
});

it('mirrors saved unhandled exception and supports its lazy reference round trip', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-unhandled')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['original-error.anchorNode-end', 'propagate.anchorNode-end', 'boundary.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-throw-unhandled-'.$side, 'literature.throw.unhandled.'.$side.'.'.$suffix);
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.throw.flow-throw-unhandled')
        ->assertSet('tabs.flow_throw', 'flow-throw-unhandled')
        ->assertSee('id="idea-to-paper-throw-unhandled-left"', false)
        ->assertSee('id="idea-to-paper-throw-unhandled-right"', false)
        ->assertDontSee('id="idea-to-paper-throw-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.throw.flow-throw-unhandled')
        ->call('returnToExample')
        ->assertSet('tabs.flow_throw', 'flow-throw-unhandled');
});

it('leaves FOREACH at the first invalid item and handles failure outside the loop', function ($input, $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/code-examples/throw-foreach.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';echo json_encode(processItems('.var_export($input, true).'));']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe($expected);
})->with([
    'middle failure' => [[true, false, true], ['processed:0', 'caught', 'after']],
    'first failure' => [[false, true], ['caught', 'after']],
    'all valid' => [[true, true], ['processed:0', 'processed:1', 'completed', 'after']],
    'empty' => [[], ['completed', 'after']],
]);

it('joins the outer CATCH to the normal loop exit without returning to iteration', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-foreach';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $point = function ($suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-throw-foreach-left', 'literature.throw.foreach.left.'.$suffix);
        expect($anchor)->not->toBeNull();
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    expect($point('failure-join.anchorNode-end'))->toEqual($point('normal-exit.anchorNode-end'));
    expect($point('catch.anchorNode-end')[1])->toBeGreaterThan($point('dispatch.false.anchorNode-end')[1]);
    $source = ExampleSource::fromView($view)->example('throw-foreach-left-example');
    expect($source)->toContain('return-to="literature.throw.foreach.left.loop.anchorNode-return"', 'Outer CATCH', 'FALSE: THROW');
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.throw.flow-throw-foreach')
        ->assertSee('THROW inside FOREACH')
        ->assertSee('id="idea-to-paper-throw-foreach-left"', false);
});

it('exposes authored failure-route calculations and its stem to arc joint arrow', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-foreach')->render();
    foreach (['failure-rise', 'failure-join.bridge1'] as $part) {
        expect($html)->toContain('data-tw-graph-calculated-marker="literature.throw.foreach.left.'.$part.'" data-tw-graph-length-kind="calculated"');
    }
    expect($html)->toContain('literature.throw.foreach.left.failure-rise.end.joint-arrow');
    expect($html)->toContain('data-tw-graph-calculated-marker="literature.throw.foreach.left.normal-exit" data-tw-graph-length-kind="prop"');
    expect($html)->not->toContain('data-tw-graph-layout-issues');
});

it('mirrors saved THROW inside FOREACH and supports its lazy reference round trip', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-foreach')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['condition.anchorNode-end', 'dispatch.false.anchorNode-end', 'failure-join.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-throw-foreach-'.$side, 'literature.throw.foreach.'.$side.'.'.$suffix);
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.throw.flow-throw-foreach')
        ->assertSet('tabs.flow_throw', 'flow-throw-foreach')
        ->assertSee('id="idea-to-paper-throw-foreach-left"', false)
        ->assertSee('id="idea-to-paper-throw-foreach-right"', false)
        ->assertDontSee('id="idea-to-paper-throw-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.throw.flow-throw-foreach')
        ->call('returnToExample')
        ->assertSet('tabs.flow_throw', 'flow-throw-foreach');
});

it('handles exceptions inside FOREACH and processes subsequent items', function ($input, $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/code-examples/throw-foreach-catch.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';echo json_encode(processItems('.var_export($input, true).'));']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe($expected);
})->with([
    'middle failure' => [[true, false, true], ['processed:0', 'caught:1', 'processed:2', 'completed', 'after']],
    'all invalid' => [[false, false], ['caught:0', 'caught:1', 'completed', 'after']],
    'all valid' => [[true, true], ['processed:0', 'processed:1', 'completed', 'after']],
    'empty' => [[], ['completed', 'after']],
]);

it('returns the joined per-item outcomes to FOREACH instead of leaving the loop', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-foreach-catch';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $point = function ($suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-throw-foreach-catch-left', 'literature.throw.foreach-catch.left.'.$suffix);
        expect($anchor)->not->toBeNull();
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    expect($point('body-return.anchorNode-end'))->toEqual($point('loop.anchorNode-return'));
    expect($point('body-return.anchorNode-start'))->toEqual($point('dispatch.anchorNode-end'));
    $source = ExampleSource::fromView($view)->example('throw-foreach-catch-left-example');
    expect($source)->toContain('attach-to="literature.throw.foreach-catch.left.dispatch.anchorNode-end"', 'CATCH: report item failure')
        ->not->toContain('failure-rise', 'failure-join', 'Outer CATCH');
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.throw.flow-throw-foreach-catch')
        ->assertSee('CATCH inside FOREACH')
        ->assertSee('id="idea-to-paper-throw-foreach-catch-left"', false);
});

it('mirrors saved CATCH inside FOREACH and supports its lazy reference round trip', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-foreach-catch')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['condition.anchorNode-end', 'dispatch.false.anchorNode-end', 'body-return.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-throw-foreach-catch-'.$side, 'literature.throw.foreach-catch.'.$side.'.'.$suffix);
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.throw.flow-throw-foreach-catch')
        ->assertSet('tabs.flow_throw', 'flow-throw-foreach-catch')
        ->assertSee('id="idea-to-paper-throw-foreach-catch-left"', false)
        ->assertSee('id="idea-to-paper-throw-foreach-catch-right"', false)
        ->assertDontSee('id="idea-to-paper-throw-test-left"', false)
        ->call('openReference', 'strang.flow-step', 'flow.throw.flow-throw-foreach-catch')
        ->call('returnToExample')
        ->assertSet('tabs.flow_throw', 'flow-throw-foreach-catch');
});

it('keeps the completed THROW laboratory out of navigation while retaining its source', function () {
    expect((new ReflectionClass(TwGraphDocumentation::class))->getConstant('TABS')['flow_throw'])->not->toContain('flow-throw-test');
    expect((\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\DocumentationLinks::EXAMPLES['flow.throw.flow-throw-test'] ?? null))->toBeNull();
    expect(view()->exists('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-test'))->toBeTrue();
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.throw.flow-throw-foreach-catch')
        ->assertDontSee('THROW Test')
        ->assertDontSee('In THROW Test')
        ->assertDontSee('id="idea-to-paper-throw-test-left"', false);
});
