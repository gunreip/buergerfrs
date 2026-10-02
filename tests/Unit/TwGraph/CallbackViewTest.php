<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource;
use Livewire\Livewire;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class);

it('returns the callback to the receiving function before returning to the original caller', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-basic';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $points = [];
    foreach (['prepare', 'apply-call', 'apply-enter', 'invoke', 'callback-body', 'callback-return', 'apply-resume', 'apply-return', 'receive'] as $part) {
        $anchor = AnchorRegistry::get('idea-to-paper-callback-basic-left', 'literature.callback.basic.left.'.$part.'.anchorNode-end');
        expect($anchor)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    }
    expect($points['invoke'][0])->toBeLessThan($points['apply-enter'][0]);
    expect($points['callback-body'][0])->toEqual($points['invoke'][0]);
    expect($points['callback-return'][0])->toEqual($points['apply-enter'][0]);
    expect($points['apply-resume'][0])->toEqual($points['apply-enter'][0]);
    expect($points['apply-return'][0])->toEqual($points['prepare'][0]);
    expect($points['receive'][0])->toEqual($points['prepare'][0]);
    $previous = null;
    foreach ($points as $point) {
        if ($previous !== null) expect($point[1])->toBeGreaterThan($previous);
        $previous = $point[1];
    }
    expect(ExampleSource::fromView($view)->example('callback-basic-left-example'))
        ->toContain('callback = addOne (not called)', 'CALL callback(value)', 'Resume apply', 'Resume original caller');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-callback-basic-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) $numbers[] = (int) trim($node->textContent);
    sort($numbers);
    expect($numbers)->toBe(range(1, 10));
});

it('invokes the passed callable exactly once with the supplied argument', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/callback/code-examples/callback-basic.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';ob_start();$result=demonstrateCallback();$output=ob_get_clean();$calls=[];$alternate=apply(7,function($value)use(&$calls){$calls[]=$value;return $value*2;});echo json_encode([$result,$output,$calls,$alternate]);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([42, '42', [7], 14]);
});

it('opens callback documentation lazily and returns from Deep Reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.callback.flow-callback-basic')
        ->assertSet('tabs.flow_index', 'flow-callback')
        ->assertSet('tabs.flow_callback', 'flow-callback-basic')
        ->assertSee('id="idea-to-paper-callback-basic-left"', false)
        ->assertDontSee('id="idea-to-paper-function-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.callback.flow-callback-basic')
        ->call('returnToExample')
        ->assertSet('tabs.flow_callback', 'flow-callback-basic')
        ->assertSee('Pass and invoke a callback');
});

it('keeps FUNCTION Test source while hiding its navigation entry', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.function.flow-function-recursion')
        ->assertDontSee('FUNCTION Test')
        ->assertSee('id="idea-to-paper-function-recursion-left"', false);
    expect(file_exists(base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/function/flow-function-test.blade.php')))->toBeTrue();
});

it('mirrors the saved callback and both return levels', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-basic')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['apply-call', 'invoke', 'callback-body', 'callback-return', 'apply-resume', 'apply-return', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-callback-basic-'.$side, 'literature.callback.basic.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('returns interchangeable callbacks through their separate apply invocations', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-interchangeable';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $points = [];
    foreach (['prepare', 'first-call', 'first-enter', 'first-invoke', 'first-body', 'first-return', 'first-result', 'first-exit', 'between', 'second-call', 'second-enter', 'second-invoke', 'second-body', 'second-return', 'second-result', 'second-exit', 'receive'] as $part) {
        $a = AnchorRegistry::get('idea-to-paper-callback-interchangeable-left', 'literature.callback.interchangeable.left.'.$part.'.anchorNode-end');
        expect($a)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    }
    foreach (['first', 'second'] as $prefix) {
        expect($points[$prefix.'-body'][0])->toBeLessThan($points[$prefix.'-enter'][0]);
        expect($points[$prefix.'-return'][0])->toEqual($points[$prefix.'-enter'][0]);
        expect($points[$prefix.'-exit'][0])->toEqual($points['prepare'][0]);
    }
    expect($points['first-body'][0])->toEqual($points['second-body'][0]);
    $previous = null;
    foreach ($points as $point) {
        if ($previous !== null) expect($point[1])->toBeGreaterThan($previous);
        $previous = $point[1];
    }
    expect(ExampleSource::fromView($view)->example('callback-interchangeable-left-example'))
        ->toContain('CALL apply(20, addOne)', 'CALL apply(20, doubleValue)', 'RETURN 21', 'RETURN 40');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-callback-interchangeable-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) $numbers[] = (int) trim($node->textContent);
    sort($numbers);
    expect($numbers)->toBe(range(1, 18));
});

it('uses the same apply implementation with two different callbacks', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/callback/code-examples/callback-interchangeable.php');
    $code = file_get_contents($path);
    expect(substr_count($code, 'function apply('))->toBe(1);
    $process = new Process([PHP_BINARY, '-r', substr($code, 5).';ob_start();$result=demonstrateCallbacks();$output=ob_get_clean();echo json_encode([$result,$output]);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([[21, 40], 'first=21; second=40']);
});

it('navigates independently between the saved callback and interchangeable test', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.callback.flow-callback-basic')
        ->assertSee('id="idea-to-paper-callback-basic-left"', false)
        ->assertSee('id="idea-to-paper-callback-basic-right"', false)
        ->assertDontSee('id="idea-to-paper-callback-interchangeable-left"', false)
        ->call('openExample', 'flow.callback.flow-callback-interchangeable')
        ->assertSee('id="idea-to-paper-callback-interchangeable-left"', false)
        ->assertDontSee('id="idea-to-paper-callback-basic-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.callback.flow-callback-interchangeable')
        ->call('returnToExample')
        ->assertSet('tabs.flow_callback', 'flow-callback-interchangeable')
        ->assertSee('Interchangeable callbacks');
});

it('mirrors the saved interchangeable callbacks', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-interchangeable')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['first-call', 'first-body', 'first-return', 'first-exit', 'second-body', 'second-return', 'second-exit', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-callback-interchangeable-'.$side, 'literature.callback.interchangeable.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('returns the callback into the iteration and closes the loop before exhaustion', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-loop')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $point = function ($part) {
        $a = AnchorRegistry::get('idea-to-paper-callback-loop-left', 'literature.callback.loop.left.'.$part.'.anchorNode-end');
        expect($a)->not->toBeNull();
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    };
    expect($point('invoke')[0])->toBeLessThan($point('loop.body')[0]);
    expect($point('callback-return')[0])->toEqual($point('loop.body')[0]);
    expect($point('append')[0])->toEqual($point('loop.body')[0]);
    expect($point('append')[1])->toBeLessThan($point('callback-return')[1]);
    expect($point('repeat'))->toEqual($point('prepare'));
    expect($point('receive')[1])->toBeGreaterThan($point('loop')[1]);
});

it('calls the callback once per item in order and skips it for an empty input', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/callback/code-examples/callback-loop.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';$seen=[];$result=mapEach([10,20,30],function($n)use(&$seen){$seen[]=$n;return addOne($n);});$empty=mapEach([],function(){throw new RuntimeException("Must not run");});echo json_encode([$seen,$result,$empty,demonstrateCallbackLoop()]);']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([[10, 20, 30], [11, 21, 31], [], [11, 21, 31]]);
});

it('opens the callback loop test separately from the saved interchangeable callbacks', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.callback.flow-callback-interchangeable')
        ->assertSee('id="idea-to-paper-callback-interchangeable-right"', false)
        ->call('openExample', 'flow.callback.flow-callback-loop')
        ->assertSee('id="idea-to-paper-callback-loop-left"', false)
        ->assertDontSee('id="idea-to-paper-callback-interchangeable-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.callback.flow-callback-loop')
        ->call('returnToExample')
        ->assertSet('tabs.flow_callback', 'flow-callback-loop')
        ->assertSee('Callback inside a loop');
});

it('mirrors the saved callback loop and preserves both return destinations', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-loop')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['prepare', 'loop.body', 'invoke', 'callback-body', 'callback-return', 'append', 'repeat', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-callback-loop-'.$side, 'literature.callback.loop.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.callback.flow-callback-loop')
        ->assertSee('id="idea-to-paper-callback-loop-right"', false)
        ->assertDontSee('CALLBACK Test');
    expect(file_exists(base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/callback/flow-callback-test.blade.php')))->toBeTrue();
});
