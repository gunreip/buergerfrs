<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource;
use Livewire\Livewire;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class);

it('returns from the function body to the original caller lane', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-basic';
    $html = view($view)->render();
    $points = [];
    foreach (['prepare', 'call', 'body', 'return', 'receive'] as $part) {
        $anchor = AnchorRegistry::get('idea-to-paper-function-basic-left', 'literature.function.basic.left.'.$part.'.anchorNode-end');
        expect($anchor)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    }
    expect($points['call'][0])->toBeLessThan($points['prepare'][0]);
    expect($points['body'][0])->toEqual($points['call'][0]);
    expect($points['return'][0])->toEqual($points['prepare'][0]);
    expect($points['receive'][0])->toEqual($points['return'][0]);
    $previous = null;
    foreach ($points as $point) {
        if ($previous !== null) expect($point[1])->toBeGreaterThan($previous);
        $previous = $point[1];
    }
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    expect(ExampleSource::fromView($view)->example('function-basic-left-example'))
        ->toContain('literature.function.basic.left.call', 'literature.function.basic.left.return', 'CALL addOne(value)', 'RETURN result');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-function-basic-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 6));
});

it('lazily opens FUNCTION and returns from its component reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->assertDontSee('id="idea-to-paper-function-basic-left"', false)
        ->call('openExample', 'flow.function.flow-function-basic')
        ->assertSet('tabs.flow_index', 'flow-function')
        ->assertSet('tabs.flow_function', 'flow-function-basic')
        ->assertSee('id="idea-to-paper-function-basic-left"', false)
        ->assertSee('Recursion with a base case')
        ->call('openReference', 'parts.sideways', 'flow.function.flow-function-basic')
        ->assertDontSee('id="idea-to-paper-function-basic-left"', false)
        ->call('returnToExample')
        ->assertSet('tabs.flow_function', 'flow-function-basic')
        ->assertSee('id="idea-to-paper-function-basic-left"', false);
});

it('executes the documented function and delivers its result to the caller', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/function/code-examples/function-basic.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';ob_start();$result=demonstrateCall();$output=ob_get_clean();echo json_encode([$result,$output]);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([42, '42']);
});

it('mirrors the saved basic function while returning to each caller lane', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-basic')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['call', 'body', 'return', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-function-basic-'.$side, 'literature.function.basic.'.$side.'.'.$part.'.anchorNode-end');
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('keeps the caller numeric arguments unchanged while returning the local result', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/function/code-examples/function-arguments.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';ob_start();$result=demonstrateArguments();$output=ob_get_clean();echo json_encode([$result,$output]);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([[20, 2, 42], 'value=20; factor=2; result=42']);
});

it('shows the parameter reassignment and local calculation before returning to the caller', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-arguments';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $points = [];
    foreach (['call', 'body', 'calculate', 'return', 'receive'] as $part) {
        $a = AnchorRegistry::get('idea-to-paper-function-arguments-left', 'literature.function.arguments.left.'.$part.'.anchorNode-end');
        expect($a)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    }
    expect($points['calculate'][0])->toEqual($points['body'][0]);
    expect($points['calculate'][1])->toBeGreaterThan($points['body'][1]);
    expect($points['return'][1])->toBeGreaterThan($points['calculate'][1]);
    expect($points['return'][0])->toEqual(0);
    expect($points['receive'][0])->toEqual(0);
    expect(ExampleSource::fromView($view)->example('function-arguments-left-example'))
        ->toContain('Local parameter: 21', 'result = value × factor', 'value = 20; factor = 2');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-function-arguments-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) $numbers[] = (int) trim($node->textContent);
    sort($numbers);
    expect($numbers)->toBe(range(1, 7));
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.function.flow-function-arguments')
        ->assertSet('tabs.flow_function', 'flow-function-arguments')
        ->assertSee('id="idea-to-paper-function-arguments-left"', false)
        ->assertDontSee('id="idea-to-paper-function-basic-left"', false);
});

it('mirrors the saved arguments example while returning to each caller lane', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-arguments')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['call', 'body', 'calculate', 'return', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-function-arguments-'.$side, 'literature.function.arguments.'.$side.'.'.$part.'.anchorNode-end');
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('executes the void action before continuing at the caller', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/function/code-examples/function-void.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';demonstrateVoid();']);
    $process->mustRun();
    expect($process->getOutput())->toBe("before\nProcessed item\nafter\n");
});

it('returns control from a void function to the caller without a result assignment', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-void';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $points = [];
    foreach (['prepare', 'call', 'body', 'return', 'continue'] as $part) {
        $anchor = AnchorRegistry::get('idea-to-paper-function-void-left', 'literature.function.void.left.'.$part.'.anchorNode-end');
        expect($anchor)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    }
    expect($points['call'][0])->toBeLessThan($points['prepare'][0]);
    expect($points['body'][0])->toEqual($points['call'][0]);
    expect($points['return'][0])->toEqual($points['prepare'][0]);
    expect($points['continue'][0])->toEqual($points['prepare'][0]);
    expect($points['continue'][1])->toBeGreaterThan($points['return'][1]);
    expect(ExampleSource::fromView($view)->example('function-void-left-example'))
        ->toContain('Return control only', 'Display Processed item', 'Display after')
        ->not->toContain('Assign result', 'Value: 42');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-function-void-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) $numbers[] = (int) trim($node->textContent);
    sort($numbers);
    expect($numbers)->toBe(range(1, 6));
});

it('opens the saved argument variants and the saved void variants independently', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.function.flow-function-arguments')
        ->assertSee('id="idea-to-paper-function-arguments-left"', false)
        ->assertSee('id="idea-to-paper-function-arguments-right"', false)
        ->assertDontSee('id="idea-to-paper-function-void-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.function.flow-function-arguments')
        ->call('returnToExample')
        ->assertSet('tabs.flow_function', 'flow-function-arguments')
        ->call('openExample', 'flow.function.flow-function-void')
        ->assertSee('id="idea-to-paper-function-void-left"', false)
        ->assertDontSee('id="idea-to-paper-function-arguments-left"', false)
        ->assertSee('Function without return value');
});

it('mirrors the saved void function while returning to each caller lane', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-void')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['call', 'body', 'return', 'continue'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-function-void-'.$side, 'literature.function.void.'.$side.'.'.$part.'.anchorNode-end');
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('returns each nested call to its own caller before continuing', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-nested';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $points = [];
    foreach (['prepare', 'outer-call', 'outer-enter', 'inner-call', 'inner-body', 'inner-return', 'outer-resume', 'outer-return', 'receive'] as $part) {
        $anchor = AnchorRegistry::get('idea-to-paper-function-nested-left', 'literature.function.nested.left.'.$part.'.anchorNode-end');
        expect($anchor)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    }
    expect($points['inner-call'][0])->toBeLessThan($points['outer-call'][0]);
    expect($points['inner-return'][0])->toEqual($points['outer-enter'][0]);
    expect($points['outer-resume'][0])->toEqual($points['outer-enter'][0]);
    expect($points['outer-return'][0])->toEqual($points['prepare'][0]);
    expect($points['receive'][0])->toEqual($points['prepare'][0]);
    $previous = null;
    foreach ($points as $point) {
        if ($previous !== null) expect($point[1])->toBeGreaterThan($previous);
        $previous = $point[1];
    }
    expect(ExampleSource::fromView($view)->example('function-nested-left-example'))
        ->toContain('RETURN result = 21', 'RETURN result = 42', 'literature.function.nested.left.outer-resume');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-function-nested-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) $numbers[] = (int) trim($node->textContent);
    sort($numbers);
    expect($numbers)->toBe(range(1, 10));
});

it('executes the documented nested functions and their intermediate result', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/function/code-examples/function-nested.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';ob_start();$result=demonstrateNested();$output=ob_get_clean();echo json_encode([addOne(20),$result,$output]);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([21, 42, '42']);
});

it('navigates between the saved void examples and saved nested calls', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.function.flow-function-void')
        ->assertSee('id="idea-to-paper-function-void-left"', false)
        ->assertSee('id="idea-to-paper-function-void-right"', false)
        ->call('openReference', 'parts.sideways', 'flow.function.flow-function-void')
        ->call('returnToExample')
        ->assertSet('tabs.flow_function', 'flow-function-void')
        ->call('openExample', 'flow.function.flow-function-nested')
        ->assertSee('id="idea-to-paper-function-nested-left"', false)
        ->assertDontSee('id="idea-to-paper-function-void-left"', false)
        ->assertSee('Nested function calls');
});

it('mirrors the saved nested calls while returning to each caller lane', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-nested')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['outer-call', 'inner-call', 'inner-return', 'outer-return', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-function-nested-'.$side, 'literature.function.nested.'.$side.'.'.$part.'.anchorNode-end');
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('returns repeated calls to their distinct continuations on the caller lane', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-multiple';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $points = [];
    foreach (['prepare', 'first-call', 'first-body', 'first-return', 'first-resume', 'second-call', 'second-body', 'second-return', 'second-resume'] as $part) {
        $a = AnchorRegistry::get('idea-to-paper-function-multiple-left', 'literature.function.multiple.left.'.$part.'.anchorNode-end');
        expect($a)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    }
    expect($points['first-body'][0])->toEqual($points['second-body'][0]);
    expect($points['first-body'][0])->toBeLessThan($points['prepare'][0]);
    foreach (['first-return', 'first-resume', 'second-return', 'second-resume'] as $part) expect($points[$part][0])->toEqual($points['prepare'][0]);
    $previous = null;
    foreach ($points as $point) {
        if ($previous !== null) expect($point[1])->toBeGreaterThan($previous);
        $previous = $point[1];
    }
    expect(ExampleSource::fromView($view)->example('function-multiple-left-example'))
        ->toContain('addOne: invocation 1', 'addOne: invocation 2', 'RETURN 11', 'RETURN 41', 'literature.function.multiple.left.first-resume.anchorNode-end');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-function-multiple-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) $numbers[] = (int) trim($node->textContent);
    sort($numbers);
    expect($numbers)->toBe(range(1, 10));
});

it('uses a single function definition for two independent calls', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/function/code-examples/function-multiple.php');
    $code = file_get_contents($path);
    expect(substr_count($code, 'function addOne('))->toBe(1);
    $process = new Process([PHP_BINARY, '-r', substr($code, 5).';ob_start();$result=demonstrateMultipleCalls();$output=ob_get_clean();echo json_encode([$result,$output]);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([[11, 41], 'first=11; second=41']);
});

it('opens the saved nested calls and saved multiple call sites independently', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.function.flow-function-nested')
        ->assertSee('id="idea-to-paper-function-nested-left"', false)
        ->assertSee('id="idea-to-paper-function-nested-right"', false)
        ->call('openReference', 'parts.sideways', 'flow.function.flow-function-nested')
        ->call('returnToExample')
        ->assertSet('tabs.flow_function', 'flow-function-nested')
        ->call('openExample', 'flow.function.flow-function-multiple')
        ->assertSee('id="idea-to-paper-function-multiple-left"', false)
        ->assertDontSee('id="idea-to-paper-function-nested-left"', false)
        ->assertSee('Multiple call sites');
});

it('mirrors the saved multiple call sites and preserves their individual continuations', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-multiple')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['first-call', 'first-body', 'first-return', 'first-resume', 'second-call', 'second-return', 'second-resume'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-function-multiple-'.$side, 'literature.function.multiple.'.$side.'.'.$part.'.anchorNode-end');
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('unwinds recursive frames to their immediate callers in reverse order', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-test';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $points = [];
    foreach (['prepare', 'call-2', 'frame-2', 'call-1', 'frame-1', 'call-0', 'base-case', 'return-0', 'resume-1', 'return-1', 'resume-2', 'return-2', 'receive'] as $part) {
        $anchor = AnchorRegistry::get('idea-to-paper-function-test-left', 'literature.function.test.'.$part.'.anchorNode-end');
        expect($anchor)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    }
    expect($points['call-1'][0])->toBeLessThan($points['call-2'][0]);
    expect($points['call-0'][0])->toBeLessThan($points['call-1'][0]);
    foreach (['return-0', 'resume-1'] as $part) expect($points[$part][0])->toEqual($points['frame-1'][0]);
    foreach (['return-1', 'resume-2'] as $part) expect($points[$part][0])->toEqual($points['frame-2'][0]);
    foreach (['return-2', 'receive'] as $part) expect($points[$part][0])->toEqual($points['prepare'][0]);
    $previous = null;
    foreach ($points as $point) {
        if ($previous !== null) expect($point[1])->toBeGreaterThan($previous);
        $previous = $point[1];
    }
    expect(ExampleSource::fromView($view)->example('function-test-left-example'))
        ->toContain('IF n == 0? FALSE', 'IF n == 0? TRUE', 'Base case: RETURN 1', 'RETURN 2');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-function-test-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) $numbers[] = (int) trim($node->textContent);
    sort($numbers);
    expect($numbers)->toBe(range(1, 14));
});

it('executes the documented recursive function including its base case', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/function/code-examples/function-test.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';ob_start();$result=demonstrateRecursion();$output=ob_get_clean();echo json_encode([factorial(0),factorial(1),$result,factorial(5),$output]);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([1, 1, 2, 120, '2']);
});

it('opens the saved multiple call sites and saved recursion independently', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.function.flow-function-multiple')
        ->assertSee('id="idea-to-paper-function-multiple-left"', false)
        ->assertSee('id="idea-to-paper-function-multiple-right"', false)
        ->call('openReference', 'parts.sideways', 'flow.function.flow-function-multiple')
        ->call('returnToExample')
        ->assertSet('tabs.flow_function', 'flow-function-multiple')
        ->call('openExample', 'flow.function.flow-function-recursion')
        ->assertSee('id="idea-to-paper-function-recursion-left"', false)
        ->assertDontSee('id="idea-to-paper-function-multiple-left"', false)
        ->assertSee('Recursion with a base case');
});

it('mirrors saved recursion and returns each frame to its own caller', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-recursion';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['call-2', 'frame-2', 'call-1', 'frame-1', 'call-0', 'base-case', 'return-0', 'resume-1', 'return-1', 'resume-2', 'return-2', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-function-recursion-'.$side, 'literature.function.recursion.'.$side.'.'.$part.'.anchorNode-end');
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    foreach (['left', 'right'] as $side) {
        $x = fn ($part) => BoundsRegistry::evaluateRemExpression(AnchorRegistry::get('idea-to-paper-function-recursion-'.$side, 'literature.function.recursion.'.$side.'.'.$part.'.anchorNode-end')['x']);
        expect($x('return-0'))->toEqual($x('frame-1'));
        expect($x('return-1'))->toEqual($x('frame-2'));
        expect($x('return-2'))->toEqual($x('prepare'));
        expect(ExampleSource::fromView($view)->example('function-recursion-'.$side.'-example'))
            ->toContain('literature.function.recursion.'.$side.'.base-case', 'IF n == 0? TRUE', 'RETURN 2');
    }
});

it('opens both saved recursion examples and returns from their reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.function.flow-function-recursion')
        ->assertSee('id="idea-to-paper-function-recursion-left"', false)
        ->assertSee('id="idea-to-paper-function-recursion-right"', false)
        ->assertDontSee('id="idea-to-paper-function-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.function.flow-function-recursion')
        ->call('returnToExample')
        ->assertSet('tabs.flow_function', 'flow-function-recursion')
        ->assertSee('Recursion with a base case');
});
