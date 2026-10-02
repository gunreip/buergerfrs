<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource;
use Livewire\Livewire;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class);

it('resumes the awaiting function on its original lane after completion', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-basic';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $points = [];
    foreach (['start', 'await', 'complete', 'resume', 'receive'] as $part) {
        $a = AnchorRegistry::get('idea-to-paper-async-await-basic-left', 'literature.async-await.basic.left.'.$part.'.anchorNode-end');
        expect($a)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    }
    expect($points['await'][0])->toBeLessThan($points['start'][0]);
    expect($points['complete'][0])->toEqual($points['await'][0]);
    expect($points['resume'][0])->toEqual($points['start'][0]);
    expect($points['receive'][0])->toEqual($points['start'][0]);
    $previous = null;
    foreach ($points as $point) {
        if ($previous !== null) expect($point[1])->toBeGreaterThan($previous);
        $previous = $point[1];
    }
    expect(ExampleSource::fromView($view)->example('async-await-basic-left-example'))
        ->toContain('AWAIT pending', 'Suspend this function', 'Resume after AWAIT');
});

it('does not execute the continuation until the documented promise resolves', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/code-examples/async-await-basic.js');
    $code = file_get_contents($path);
    $process = new Process(['node', '-e', $code.'
        const logs = [];
        console.log = value => logs.push(value);
        let complete;
        globalThis.setTimeout = callback => { complete = callback; };
        const pending = demonstrateAwait();
        const before = [...logs];
        complete();
        const beforeMicrotask = [...logs];
        pending.then(result => process.stdout.write(JSON.stringify([before,beforeMicrotask,result,logs])));
    ']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([[], [], 42, [42]]);
});

it('opens async await lazily and returns from Deep Reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-basic')
        ->assertSet('tabs.flow_index', 'flow-async-await')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-basic')
        ->assertSee('id="idea-to-paper-async-await-basic-left"', false)
        ->assertDontSee('id="idea-to-paper-callback-loop-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-basic')
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-basic')
        ->assertSee('Await one operation');
});

it('mirrors the saved await example and preserves its continuation lane', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-basic')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['start', 'await', 'complete', 'resume', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-async-await-basic-'.$side, 'literature.async-await.basic.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('resumes each sequential await before the following operation starts', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-sequential';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $points = [];
    foreach (['start', 'first-await', 'first-complete', 'first-resume', 'second-start', 'second-await', 'second-complete', 'second-resume', 'receive'] as $part) {
        $a = AnchorRegistry::get('idea-to-paper-async-await-sequential-left', 'literature.async-await.sequential.left.'.$part.'.anchorNode-end');
        expect($a)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    }
    foreach (['first', 'second'] as $prefix) {
        expect($points[$prefix.'-complete'][0])->toEqual($points[$prefix.'-await'][0]);
        expect($points[$prefix.'-resume'][0])->toEqual($points['start'][0]);
    }
    expect($points['second-start'][0])->toEqual($points['start'][0]);
    $previous = null;
    foreach ($points as $point) {
        if ($previous !== null) expect($point[1])->toBeGreaterThan($previous);
        $previous = $point[1];
    }
    expect(ExampleSource::fromView($view)->example('async-await-sequential-left-example'))
        ->toContain('AWAIT first', 'AWAIT second', 'Second operation starts now', 'Receive result = 40');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-async-await-sequential-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) $numbers[] = (int) trim($node->textContent);
    sort($numbers);
    expect($numbers)->toBe(range(1, 10));
});

it('starts the second documented operation only after the first completion and uses its value', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/code-examples/async-await-sequential.js');
    $process = new Process(['node', '-e', file_get_contents($path).'
        (async () => {
            const timers = [], logs = [];
            globalThis.setTimeout = complete => timers.push(complete);
            console.log = value => logs.push(value);
            const pending = demonstrateSequentialAwaits();
            const beforeFirst = [timers.length, [...logs]];
            timers[0]();
            await Promise.resolve();
            const beforeSecond = [timers.length, [...logs]];
            timers[1]();
            const result = await pending;
            process.stdout.write(JSON.stringify([beforeFirst, beforeSecond, result, logs]));
        })().catch(error => { process.stderr.write(String(error)); process.exitCode = 1; });
    ']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([[1, []], [2, []], 40, [40]]);
});

it('opens saved await variants separately from the sequential test', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-basic')
        ->assertSee('id="idea-to-paper-async-await-basic-left"', false)
        ->assertSee('id="idea-to-paper-async-await-basic-right"', false)
        ->assertDontSee('id="idea-to-paper-async-await-sequential-left"', false)
        ->call('openExample', 'flow.async-await.flow-async-await-sequential')
        ->assertSee('id="idea-to-paper-async-await-sequential-left"', false)
        ->assertDontSee('id="idea-to-paper-async-await-basic-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-sequential')
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-sequential')
        ->assertSee('Sequential awaits');
});

it('mirrors the saved sequential awaits', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-sequential')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['first-await', 'first-complete', 'first-resume', 'second-start', 'second-complete', 'second-resume', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-async-await-sequential-'.$side, 'literature.async-await.sequential.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('joins both independent success lanes at the same continuation anchor', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-concurrent';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $point = function ($part, $end = 'anchorNode-end') {
        $a = AnchorRegistry::get('idea-to-paper-async-await-concurrent-left', 'literature.async-await.concurrent.left.'.$part.'.'.$end);
        expect($a)->not->toBeNull();
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    };
    expect($point('a-ready'))->toEqual($point('b-ready'));
    expect($point('receive')[0])->toEqual($point('a-ready')[0]);
    expect($point('receive')[1])->toBeGreaterThan($point('a-ready')[1]);
    expect($point('a-complete')[0])->toBeLessThan($point('await-all')[0]);
    expect($point('b-complete')[0])->toBeGreaterThan($point('await-all')[0]);
    expect($point('start-b')[1])->toBeLessThan($point('await-all')[1]);
    expect(ExampleSource::fromView($view)->example('async-await-concurrent-left-example'))
        ->toContain('Both lanes participate', 'BOTH operations completed', 'results = [20, 40]', 'attach-to="literature.async-await.concurrent.left.a-ready.anchorNode-end"');
});

it('starts both operations before awaiting and waits for both even when B finishes first', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/code-examples/async-await-concurrent.js');
    $process = new Process(['node', '-e', file_get_contents($path).'
        (async () => {
            const completions = [], logs = [];
            globalThis.setTimeout = complete => completions.push(complete);
            console.log = value => logs.push(value);
            let resumed = false;
            const pending = demonstrateConcurrentAwait().then(result => { resumed = true; return result; });
            const started = completions.length;
            completions[1]();
            await Promise.resolve();
            await Promise.resolve();
            const afterB = [resumed, [...logs]];
            completions[0]();
            const result = await pending;
            process.stdout.write(JSON.stringify([started, afterB, result, logs]));
        })().catch(error => { process.stderr.write(String(error)); process.exitCode = 1; });
    ']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([2, [false, []], [20, 40], [[20, 40]]]);
});

it('opens saved sequential awaits separately from concurrent operations', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-sequential')
        ->assertSee('id="idea-to-paper-async-await-sequential-left"', false)
        ->assertSee('id="idea-to-paper-async-await-sequential-right"', false)
        ->call('openExample', 'flow.async-await.flow-async-await-concurrent')
        ->assertSee('id="idea-to-paper-async-await-concurrent-left"', false)
        ->assertDontSee('id="idea-to-paper-async-await-sequential-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-concurrent')
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-concurrent')
        ->assertSee('Concurrent operations');
});

it('mirrors both saved concurrent lanes and preserves their common join', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-concurrent')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['start-a', 'start-b', 'await-all', 'a-complete', 'b-complete', 'a-ready', 'b-ready', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-async-await-concurrent-'.$side, 'literature.async-await.concurrent.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('routes an awaited rejection through catch and finally before continuation', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-finally';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $points = [];
    foreach (['try', 'await', 'reject', 'throw', 'catch', 'finally', 'continue'] as $part) {
        $a = AnchorRegistry::get('idea-to-paper-async-await-finally-left', 'literature.async-await.finally.left.'.$part.'.anchorNode-end');
        expect($a)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    }
    expect($points['reject'][0])->toEqual($points['await'][0]);
    expect($points['reject'][0])->toBeLessThan($points['try'][0]);
    foreach (['throw', 'catch', 'finally', 'continue'] as $part) expect($points[$part][0])->toEqual($points['try'][0]);
    $previous = null;
    foreach ($points as $point) {
        if ($previous !== null) expect($point[1])->toBeGreaterThan($previous);
        $previous = $point[1];
    }
    expect(ExampleSource::fromView($view)->example('async-await-finally-left-example'))
        ->toContain('Resume AWAIT with error', 'CATCH error', 'result = 0 (fallback)', 'FINALLY');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-async-await-finally-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) $numbers[] = (int) trim($node->textContent);
    sort($numbers);
    expect($numbers)->toBe(range(1, 8));
});

it('executes catch only on rejection and finally on both documented outcomes', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/code-examples/async-await-finally.js');
    $process = new Process(['node', '-e', file_get_contents($path).'
        (async () => {
            const logs = [], cases = [];
            console.log = value => logs.push(value);
            let complete;
            globalThis.setTimeout = callback => { complete = callback; };
            for (const shouldFail of [true, false]) {
                logs.length = 0;
                const pending = demonstrateAwaitWithFinally(shouldFail);
                const before = [...logs];
                complete();
                const result = await pending;
                cases.push([before, result, [...logs]]);
            }
            process.stdout.write(JSON.stringify(cases));
        })().catch(error => { process.stderr.write(String(error)); process.exitCode = 1; });
    ']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([
        [[], 0, ['CATCH: Load failed', 'FINALLY: cleanup', 'Continue: 0']],
        [[], 42, ['FINALLY: cleanup', 'Continue: 42']],
    ]);
});

it('opens saved concurrent operations independently from the await error example', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-concurrent')
        ->assertSee('id="idea-to-paper-async-await-concurrent-left"', false)
        ->assertSee('id="idea-to-paper-async-await-concurrent-right"', false)
        ->call('openExample', 'flow.async-await.flow-async-await-finally')
        ->assertSee('id="idea-to-paper-async-await-finally-left"', false)
        ->assertDontSee('id="idea-to-paper-async-await-concurrent-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-finally')
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-finally')
        ->assertSee('Await with TRY/CATCH/FINALLY');
});

it('mirrors the saved await error path including catch and finally', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-finally')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['try', 'await', 'reject', 'throw', 'catch', 'finally', 'continue'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-async-await-finally-'.$side, 'literature.async-await.finally.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('resumes inside the awaited iteration before returning to the next item', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-loop')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $point = function ($part) {
        $a = AnchorRegistry::get('idea-to-paper-async-await-loop-left', 'literature.async-await.loop.left.'.$part.'.anchorNode-end');
        expect($a)->not->toBeNull();
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    };
    expect($point('await')[0])->toBeLessThan($point('loop.body')[0]);
    expect($point('resume')[0])->toEqual($point('loop.body')[0]);
    expect($point('append')[1])->toBeLessThan($point('resume')[1]);
    expect($point('repeat'))->toEqual($point('prepare'));
    expect($point('receive')[1])->toBeGreaterThan($point('loop')[1]);
});

it('awaits each item before starting the next and skips empty input', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/code-examples/async-await-loop.js');
    $process = new Process(['node', '-e', file_get_contents($path).'
        (async () => {
            const timers = [], starts = [], snapshots = [];
            globalThis.setTimeout = complete => timers.push(complete);
            const original = transformAsync;
            transformAsync = item => { starts.push(item); return original(item); };
            const empty = await processItems([]);
            const emptyStarts = [...starts];
            const pending = processItems([10, 20, 30]);
            snapshots.push([...starts]);
            for (let i = 0; i < 3; i++) {
                timers[i]();
                await Promise.resolve();
                snapshots.push([...starts]);
            }
            const result = await pending;
            const failures = [];
            transformAsync = item => { failures.push(item); return Promise.reject(new Error("stop")); };
            let error;
            try { await processItems([10,20,30]); } catch (e) { error=e.message; }
            process.stdout.write(JSON.stringify([empty,emptyStarts,snapshots,result,failures,error]));
        })().catch(error => { process.stderr.write(String(error)); process.exitCode = 1; });
    ']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([
        [], [], [[10], [10, 20], [10, 20, 30], [10, 20, 30]], [11, 21, 31], [10], 'stop',
    ]);
});

it('opens saved await finally variants separately from the awaited loop', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-finally')
        ->assertSee('id="idea-to-paper-async-await-finally-left"', false)
        ->assertSee('id="idea-to-paper-async-await-finally-right"', false)
        ->call('openExample', 'flow.async-await.flow-async-await-loop')
        ->assertSee('id="idea-to-paper-async-await-loop-left"', false)
        ->assertDontSee('id="idea-to-paper-async-await-finally-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-loop')
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-loop')
        ->assertSee('Await inside a loop');
});

it('mirrors the saved awaited loop and both return connections', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-loop')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['prepare', 'loop.body', 'await', 'operation', 'resume', 'append', 'repeat', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-async-await-loop-'.$side, 'literature.async-await.loop.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('routes acknowledged cancellation into catch and cleanup without a success result', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-cancellation';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $points = [];
    foreach (['try', 'await', 'request', 'acknowledge', 'cancelled', 'catch', 'finally', 'continue'] as $part) {
        $a = AnchorRegistry::get('idea-to-paper-async-await-cancellation-left', 'literature.async-await.cancellation.left.'.$part.'.anchorNode-end');
        expect($a)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    }
    expect($points['request'][0])->toEqual($points['acknowledge'][0]);
    expect($points['cancelled'][0])->toEqual($points['try'][0]);
    $previous = null;
    foreach ($points as $point) {
        if ($previous !== null) expect($point[1])->toBeGreaterThan($previous);
        $previous = $point[1];
    }
    expect(ExampleSource::fromView($view)->example('async-await-cancellation-left-example'))
        ->toContain('External caller', 'Operation observes signal', 'CATCH cancellation', 'FINALLY', 'No success value');
});

it('cleans up cancellation and preserves completion and unrelated errors', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/code-examples/async-await-cancellation.js');
    $process = new Process(['node', '-e', file_get_contents($path).'
        (async () => {
            let timers = [], cleared = [], logs = [];
            globalThis.setTimeout = fn => { timers.push(fn); return timers.length; };
            globalThis.clearTimeout = id => cleared.push(id);
            console.log = msg => logs.push(msg);
            const running = new AbortController();
            const pending = demonstrateCancellation(running.signal);
            const before = [...logs];
            running.abort();
            const cancelled = [await pending, [...cleared], [...logs], before];
            timers=[]; cleared=[]; logs=[];
            const pre = new AbortController(); pre.abort();
            const preAborted = [await demonstrateCancellation(pre.signal), timers.length, [...logs]];
            timers=[]; cleared=[]; logs=[];
            const success = new AbortController();
            const completed = demonstrateCancellation(success.signal);
            timers[0]();
            const value = await completed;
            success.abort(); // Completion is not undone; listener is already removed.
            const normal = [value, [...cleared], [...logs]];
            logs=[];
            const unrelated = new Error("unrelated");
            loadValueAsync = () => Promise.reject(unrelated);
            let sameError=false;
            try { await demonstrateCancellation(new AbortController().signal); }
            catch (error) { sameError = error === unrelated; }
            process.stdout.write(JSON.stringify([cancelled, preAborted, normal, sameError, logs]));
        })().catch(error => { process.stderr.write(String(error)); process.exitCode = 1; });
    ']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([
        [['status' => 'cancelled'], [1], ['CATCH: cancelled', 'FINALLY: cleanup'], []],
        [['status' => 'cancelled'], 0, ['CATCH: cancelled', 'FINALLY: cleanup']],
        [['status' => 'completed', 'value' => 42], [], ['FINALLY: cleanup']],
        true, ['FINALLY: cleanup'],
    ]);
});

it('opens saved awaited loops separately from cancellation', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-loop')
        ->assertSee('id="idea-to-paper-async-await-loop-left"', false)
        ->assertSee('id="idea-to-paper-async-await-loop-right"', false)
        ->call('openExample', 'flow.async-await.flow-async-await-cancellation')
        ->assertSee('id="idea-to-paper-async-await-cancellation-left"', false)
        ->assertDontSee('id="idea-to-paper-async-await-loop-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-cancellation')
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-cancellation')
        ->assertSee('Cancellation');
});

it('mirrors the saved cancellation trace through acknowledgement and cleanup', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-cancellation';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['try', 'await', 'request', 'acknowledge', 'cancelled', 'catch', 'finally', 'continue'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-async-await-cancellation-'.$side, 'literature.async-await.cancellation.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    foreach (['left', 'right'] as $side) {
        expect(ExampleSource::fromView($view)->example('async-await-cancellation-'.$side.'-example'))
            ->toContain('External caller', 'Operation observes signal', 'CATCH cancellation', 'FINALLY');
    }
});

it('opens both saved cancellation variants and returns from their reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-cancellation')
        ->assertSee('id="idea-to-paper-async-await-cancellation-left"', false)
        ->assertSee('id="idea-to-paper-async-await-cancellation-right"', false)
        ->assertDontSee('id="idea-to-paper-async-await-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-cancellation')
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-cancellation')
        ->assertSee('Cancellation');
});

it('routes timeout expiry through cancellation acknowledgement and cleanup', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-timeout';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $points = [];
    foreach (['try', 'await', 'request', 'acknowledge', 'cancelled', 'catch', 'finally', 'continue'] as $part) {
        $a = AnchorRegistry::get('idea-to-paper-async-await-timeout-left', 'literature.async-await.timeout.left.'.$part.'.anchorNode-end');
        expect($a)->not->toBeNull();
        $points[$part] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    }
    expect($points['request'][0])->toEqual($points['acknowledge'][0]);
    expect($points['cancelled'][0])->toEqual($points['try'][0]);
    $previous = null;
    foreach ($points as $point) {
        if ($previous !== null) expect($point[1])->toBeGreaterThan($previous);
        $previous = $point[1];
    }
    expect(ExampleSource::fromView($view)->example('async-await-timeout-left-example'))
        ->toContain('20 ms deadline reached', 'Report timeout reason', 'CATCH timeout', 'Clear deadline timer');
});

it('cancels timed-out work but clears the deadline on success and unrelated failure', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/code-examples/async-await-timeout.js');
    $process = new Process(['node', '-e', file_get_contents($path).'
        (async () => {
            let timers=[], cleared=[], logs=[];
            globalThis.setTimeout = (fn,delay) => {timers.push({fn,delay});return timers.length;};
            globalThis.clearTimeout = id => cleared.push(id);
            console.log = msg => logs.push(msg);
            const pending=demonstrateTimeout();
            const delays=timers.map(t=>t.delay);
            const before=[...logs];
            timers[0].fn(); // Deadline requests cancellation of the operation.
            const timeout=[await pending,[...cleared],[...logs],before,delays];
            timers=[];cleared=[];logs=[];
            const success=demonstrateTimeout(200);
            timers[1].fn(); // Operation completes before its deadline.
            const normal=[await success,[...cleared],[...logs]];
            timers=[];cleared=[];logs=[];
            const unrelated=new Error("unrelated");
            loadValueAsync=()=>Promise.reject(unrelated);
            let sameError=false;
            try {await demonstrateTimeout();} catch(error) {sameError=error===unrelated;}
            process.stdout.write(JSON.stringify([timeout,normal,sameError,cleared,logs]));
        })().catch(error=>{process.stderr.write(String(error));process.exitCode=1;});
    ']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([
        [['status' => 'timeout'], [2, 1], ['CATCH: timeout', 'FINALLY: deadline cleared'], [], [20, 100]],
        [['status' => 'completed', 'value' => 42], [1], ['FINALLY: deadline cleared']],
        true, [1], ['FINALLY: deadline cleared'],
    ]);
});

it('opens the timeout test separately from saved cancellation', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-cancellation')
        ->assertSee('id="idea-to-paper-async-await-cancellation-right"', false)
        ->call('openExample', 'flow.async-await.flow-async-await-timeout')
        ->assertSee('id="idea-to-paper-async-await-timeout-left"', false)
        ->assertDontSee('id="idea-to-paper-async-await-cancellation-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-timeout')
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-timeout')
        ->assertSee('Timeout');
});

it('mirrors the saved timeout route and cleanup', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-timeout')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['try', 'await', 'request', 'acknowledge', 'cancelled', 'catch', 'finally', 'continue'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-async-await-timeout-'.$side, 'literature.async-await.timeout.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('collects success and failure lanes at one all-settled continuation', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-partial';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $point = function ($part) {
        $a = AnchorRegistry::get('idea-to-paper-async-await-partial-left', 'literature.async-await.partial.left.'.$part.'.anchorNode-end');
        expect($a)->not->toBeNull();
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
    };
    expect($point('a-ready'))->toEqual($point('b-ready'));
    expect($point('a-complete')[0])->toBeLessThan($point('await-all')[0]);
    expect($point('b-complete')[0])->toBeGreaterThan($point('await-all')[0]);
    expect($point('receive')[0])->toEqual($point('a-ready')[0]);
    expect($point('receive')[1])->toBeGreaterThan($point('a-ready')[1]);
    expect(ExampleSource::fromView($view)->example('async-await-partial-left-example'))
        ->toContain('AWAIT ALL SETTLED', 'A fulfilled: 20', 'B rejected: Load B failed', 'BOTH operations settled');
});

it('retains both outcomes in input order regardless of which settles first', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/code-examples/async-await-partial.js');
    $process = new Process(['node', '-e', file_get_contents($path).'
        (async () => {
            const cases=[];
            for (const order of [[1,0],[0,1]]) {
                const timers=[], logs=[];
                globalThis.setTimeout=fn=>timers.push(fn);
                console.log=msg=>logs.push(msg);
                let resumed=false;
                const pending=demonstratePartialSuccess().then(result=>{resumed=true;return result;});
                const started=timers.length;
                timers[order[0]]();
                await Promise.resolve(); await Promise.resolve();
                const intermediate=[resumed,[...logs]];
                timers[order[1]]();
                const outcomes=await pending;
                cases.push([started,intermediate,outcomes.map(o=>o.status==="fulfilled"
                    ? [o.status,o.value] : [o.status,o.reason.message,o.reason instanceof Error]),logs]);
            }
            process.stdout.write(JSON.stringify(cases));
        })().catch(error=>{process.stderr.write(String(error));process.exitCode=1;});
    ']);
    $process->mustRun();
    $expected = [2, [false, []], [['fulfilled', 20], ['rejected', 'Load B failed', true]], ['Value: 20', 'Error: Load B failed']];
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([$expected, $expected]);
});

it('opens saved timeout variants separately from partial success', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-timeout')
        ->assertSee('id="idea-to-paper-async-await-timeout-left"', false)
        ->assertSee('id="idea-to-paper-async-await-timeout-right"', false)
        ->call('openExample', 'flow.async-await.flow-async-await-partial')
        ->assertSee('id="idea-to-paper-async-await-partial-left"', false)
        ->assertDontSee('id="idea-to-paper-async-await-timeout-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-partial')
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-partial')
        ->assertSee('Partial success');
});

it('mirrors every saved partial-success lane and continuation', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-partial')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['await-all', 'a-complete', 'b-complete', 'a-ready', 'b-ready', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-async-await-partial-'.$side, 'literature.async-await.partial.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
});

it('renders separate first-completion and first-success traces with different continuations', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-first';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['completion', 'success'] as $trace) {
        $point = function ($part) use ($trace) {
            $a = AnchorRegistry::get('idea-to-paper-async-await-first-'.$trace.'-left', 'literature.async-await.first.'.$trace.'.left.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        };
        expect($point('failure')[1])->toBeGreaterThan($point('await')[1]);
        expect($point('resume')[0])->toBeGreaterThan($point('failure')[0]);
        expect($point('receive')[1])->toBeGreaterThan($point('resume')[1]);
        if ($trace === 'success') {
            expect($point('fulfilled')[1])->toBeGreaterThan($point('failure')[1]);
        }
        expect(ExampleSource::fromView($view)->example('async-await-first-'.$trace.'-left-example'))
            ->toContain('literature.async-await.first.'.$trace.'.left.receive');
    }
});

it('distinguishes first settlement from first fulfillment and reports all failures', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/code-examples/async-await-first.js');
    $process = new Process(['node', '-e', file_get_contents($path).'
        (async () => {
            const results=[];
            for (const run of [demonstrateFirstCompletion, demonstrateFirstSuccess]) {
                const timers=[];
                globalThis.setTimeout=fn=>timers.push(fn);
                let resumed=false;
                const pending=run().then(result=>{resumed=true;return result;});
                const started=timers.length;
                timers[1]();
                for(let i=0;i<8;i++) await Promise.resolve();
                const afterFailure=resumed;
                timers[0]();
                const result=await pending;
                results.push([started,afterFailure,result.status,result.value??result.error.message]);
            }
            loadAAsync=()=>Promise.reject(new Error("A failed"));
            loadBAsync=()=>Promise.reject(new Error("B failed"));
            const failed=await demonstrateFirstSuccess();
            results.push([failed.status,failed.error instanceof AggregateError,
                failed.error.errors.map(error=>error.message)]);
            process.stdout.write(JSON.stringify(results));
        })().catch(error=>{process.stderr.write(String(error));process.exitCode=1;});
    ']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([
        [2, true, 'rejected', 'Load B failed'],
        [2, false, 'fulfilled', 20],
        ['rejected', true, ['A failed', 'B failed']],
    ]);
});

it('opens partial-success mirrors separately from the selection test and returns from its reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-partial')
        ->assertSee('id="idea-to-paper-async-await-partial-left"', false)
        ->assertSee('id="idea-to-paper-async-await-partial-right"', false)
        ->call('openExample', 'flow.async-await.flow-async-await-first')
        ->assertSee('id="idea-to-paper-async-await-first-completion-left"', false)
        ->assertSee('id="idea-to-paper-async-await-first-success-left"', false)
        ->assertDontSee('id="idea-to-paper-async-await-partial-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-first')
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-first')
        ->assertSee('First completion / First success');
});

it('mirrors both saved selection rules without changing their connections', function () {
    view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-first')->render();
    foreach (['completion', 'success'] as $trace) {
        foreach (['start', 'await', 'failure', 'resume', 'receive'] as $part) {
            $points = [];
            foreach (['left', 'right'] as $side) {
                $a = AnchorRegistry::get('idea-to-paper-async-await-first-'.$trace.'-'.$side, 'literature.async-await.first.'.$trace.'.'.$side.'.'.$part.'.anchorNode-end');
                expect($a)->not->toBeNull();
                $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
            }
            expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
        }
    }
});

it('renders the limited-concurrency trace through refill and collection', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-limited';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $previousY = 0;
    foreach (['start', 'launch', 'await', 'b-complete', 'refill', 'c-complete', 'a-complete', 'resume', 'receive'] as $part) {
        $a = AnchorRegistry::get('idea-to-paper-async-await-limited-left', 'literature.async-await.limited.left.'.$part.'.anchorNode-end');
        expect($a)->not->toBeNull();
        $y = BoundsRegistry::evaluateRemExpression($a['y']);
        expect($y)->toBeGreaterThan($previousY);
        $previousY = $y;
    }
    expect(ExampleSource::fromView($view)->example('async-await-limited-left-example'))
        ->toContain('Freed worker starts C', 'Active: A + C / queued: none', 'Input order: A, B, C');
});

it('bounds active operations and refills immediately while preserving input-order outcomes', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/code-examples/async-await-limited.js');
    $process = new Process(['node', '-e', file_get_contents($path).'
        (async () => {
            const started=[], finish={}, snapshots=[];
            let active=0, peak=0;
            const error=new Error("B failed");
            const operation=item=>{
                started.push(item); active++; peak=Math.max(peak,active);
                return new Promise((resolve,reject)=>{
                    finish[item]=()=>{active--; item==="B"?reject(error):resolve(item);};
                });
            };
            const pending=mapLimited(["A","B","C"],2,operation);
            snapshots.push([...started]);
            finish.B();
            for(let i=0;i<8;i++) await Promise.resolve();
            snapshots.push([...started]);
            finish.C(); finish.A();
            const outcomes=await pending;
            let calls=0;
            const empty=await mapLimited([],2,()=>{calls++;});
            const invalid=[];
            for(const limit of [0,-1,1.5,NaN]) {
                try {await mapLimited(["A"],limit,()=>{calls++;});}
                catch(e){invalid.push(e instanceof RangeError);}
            }
            const sync=await mapLimited([1,2,3],1,n=>{if(n===2)throw error;return n;});
            process.stdout.write(JSON.stringify([snapshots,peak,active,
                outcomes.map(o=>[o.status,o.value??o.error.message]),outcomes[1].error===error,
                empty,calls,invalid,sync.map(o=>o.status)]));
        })().catch(error=>{process.stderr.write(String(error));process.exitCode=1;});
    ']);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([
        [['A', 'B'], ['A', 'B', 'C']], 2, 0,
        [['fulfilled', 'A'], ['rejected', 'B failed'], ['fulfilled', 'C']], true,
        [], 0, [true, true, true, true], ['fulfilled', 'rejected', 'fulfilled'],
    ]);
});

it('navigates between the saved selection mirrors and the next concurrency test', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-first')
        ->assertSee('id="idea-to-paper-async-await-first-completion-right"', false)
        ->assertSee('id="idea-to-paper-async-await-first-success-right"', false)
        ->call('openExample', 'flow.async-await.flow-async-await-limited')
        ->assertSee('Limited concurrency')
        ->assertSee('id="idea-to-paper-async-await-limited-left"', false)
        ->assertDontSee('id="idea-to-paper-async-await-first-completion-right"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-limited')
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-limited')
        ->assertSee('Limited concurrency');
});

it('mirrors saved limited-concurrency steps and retains independent source blocks', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-limited';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['start', 'launch', 'await', 'b-complete', 'refill', 'c-complete', 'a-complete', 'resume', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-async-await-limited-'.$side, 'literature.async-await.limited.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    foreach (['left', 'right'] as $side) {
        expect(ExampleSource::fromView($view)->example('async-await-limited-'.$side.'-example'))
            ->toContain('literature.async-await.limited.'.$side.'.refill');
    }
});

it('renders the retry delay between failure and the fresh attempt', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-test';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $previousY = 0;
    foreach (['start', 'attempt-1', 'failure', 'retry-check', 'delay', 'attempt-2', 'resume', 'receive'] as $part) {
        $a = AnchorRegistry::get('idea-to-paper-async-await-test-left', 'literature.async-await.test.left.'.$part.'.anchorNode-end');
        expect($a)->not->toBeNull();
        $y = BoundsRegistry::evaluateRemExpression($a['y']);
        expect($y)->toBeGreaterThan($previousY);
        $previousY = $y;
    }
    expect(ExampleSource::fromView($view)->example('async-await-test-left-example'))
        ->toContain('Max attempts: 3', 'AWAIT delay: 20ms', 'No third attempt');
});

it('enforces retry limits and cancellation without extra attempts or orphaned timers', function () {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/code-examples/async-await-test.js');
    $process = new Process(['node', '-e', file_get_contents($path).'
        const assert=require("assert/strict");
        (async () => {
            let nextId=0;
            const timers=new Map(), durations=[];
            globalThis.setTimeout=(fn,ms)=>{const id=++nextId;timers.set(id,fn);durations.push(ms);return id;};
            globalThis.clearTimeout=id=>timers.delete(id);
            const flush=async()=>{for(let i=0;i<12;i++)await Promise.resolve();};
            const tick=async()=>{assert.equal(timers.size,1);const [id,fn]=timers.entries().next().value;timers.delete(id);fn();await flush();};
            let controller=new AbortController();
            const demo=demonstrateRetry(controller.signal);
            await tick(); await tick(); await tick();
            assert.equal(await demo,42);
            assert.deepEqual(durations,[10,20,10]);
            assert.equal(timers.size,0);

            durations.length=0;
            let calls=0;
            const last=new TransientError("Still unavailable");
            const exhausted=retry(()=>{calls++;throw last;}, {maxAttempts:3,delayMs:20,signal:controller.signal})
                .then(()=>assert.fail("expected rejection"),error=>error);
            await tick(); await tick();
            assert.equal(await exhausted,last);
            assert.equal(calls,3);assert.deepEqual(durations,[20,20]);
            const permanent=new Error("Permanent");calls=0;
            await assert.rejects(retry(()=>{calls++;throw permanent;},{signal:controller.signal}),e=>e===permanent);
            assert.equal(calls,1);assert.equal(timers.size,0);

            controller=new AbortController();calls=0;
            const reason=new Error("Stop retry delay");
            const waiting=retry(()=>{calls++;throw last;},{signal:controller.signal})
                .then(()=>assert.fail("expected cancellation"),e=>e);
            assert.equal(timers.size,1);controller.abort(reason);
            assert.equal(await waiting,reason);assert.equal(calls,1);assert.equal(timers.size,0);
            await assert.rejects(retry(()=>{calls++;return 42;},{signal:controller.signal}),e=>e===reason);
            assert.equal(calls,1);

            controller=new AbortController();
            const inOperation=demonstrateRetry(controller.signal).then(()=>assert.fail("expected cancellation"),e=>e);
            assert.equal(timers.size,1);controller.abort(reason);
            assert.equal(await inOperation,reason);assert.equal(timers.size,0);
            controller=new AbortController();calls=0;
            for(const config of [{maxAttempts:0},{maxAttempts:1.5},{delayMs:-1},{delayMs:Infinity}]) {
                await assert.rejects(retry(()=>{calls++;return 42;},{...config,signal:controller.signal}),RangeError);
            }
            assert.equal(calls,0);
            process.stdout.write("retry contracts passed");
        })().catch(error=>{process.stderr.write(error.stack);process.exitCode=1;});
    ']);
    $process->mustRun();
    expect($process->getOutput())->toBe('retry contracts passed');
});

it('opens saved limited concurrency separately from the saved retry and its deep reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-limited')
        ->assertSee('id="idea-to-paper-async-await-limited-left"', false)
        ->assertSee('id="idea-to-paper-async-await-limited-right"', false)
        ->call('openExample', 'flow.async-await.flow-async-await-retry')
        ->assertSee('Retry with delay')
        ->assertDontSee('id="idea-to-paper-async-await-limited-right"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-retry')
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-retry')
        ->assertSee('Retry with delay');
});

it('mirrors saved retry traces and extracts both literal code examples', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-retry';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['start', 'attempt-1', 'failure', 'retry-check', 'delay', 'attempt-2', 'resume', 'receive'] as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-async-await-retry-'.$side, 'literature.async-await.retry.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    foreach (['left', 'right'] as $side) {
        expect(ExampleSource::fromView($view)->example('async-await-retry-'.$side.'-example'))
            ->toContain('literature.async-await.retry.'.$side.'.delay', 'AWAIT delay: 20ms');
    }
});

it('opens the saved retry variants and returns from their deep reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-retry')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-retry')
        ->assertSee('id="idea-to-paper-async-await-retry-left"', false)
        ->assertSee('id="idea-to-paper-async-await-retry-right"', false)
        ->assertDontSee('id="idea-to-paper-async-await-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-retry')
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-retry')
        ->assertSee('Retry with delay');
});

it('renders mirrored async lifecycle examples with independently extractable source', function (string $slug, array $parts) {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-'.$slug;
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach ($parts as $part) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $a = AnchorRegistry::get('idea-to-paper-async-await-'.$slug.'-'.$side, 'literature.async-await.'.$slug.'.'.$side.'.'.$part.'.anchorNode-end');
            expect($a)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($a[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    foreach (['left', 'right'] as $side) {
        expect(ExampleSource::fromView($view)->example('async-await-'.$slug.'-'.$side.'-example'))
            ->toContain('literature.async-await.'.$slug.'.'.$side.'.receive');
    }
})->with([
    ['group-cleanup', ['start', 'await', 'failure', 'cancel', 'acknowledge', 'cleanup', 'join', 'receive']],
    ['stream', ['start', 'next-1', 'item-1', 'next-2', 'item-2', 'stop', 'close', 'receive']],
    ['retry-deadline', ['start', 'attempt-1', 'delay', 'attempt-2', 'deadline', 'acknowledge', 'cleanup', 'receive']],
]);

it('opens each additional async example and returns from its deep reference', function (string $slug, string $title) {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.async-await.flow-async-await-'.$slug)
        ->assertSet('tabs.flow_async_await', 'flow-async-await-'.$slug)
        ->assertSee('id="idea-to-paper-async-await-'.$slug.'-left"', false)
        ->assertSee('id="idea-to-paper-async-await-'.$slug.'-right"', false)
        ->assertDontSee('id="idea-to-paper-async-await-test-left"', false)
        ->call('openReference', 'parts.sideways', 'flow.async-await.flow-async-await-'.$slug)
        ->call('returnToExample')
        ->assertSet('tabs.flow_async_await', 'flow-async-await-'.$slug)
        ->assertSee($title);
})->with([
    ['group-cleanup', 'Failure → Cancel remaining → Cleanup'],
    ['stream', 'Async iteration / Stream'],
    ['retry-deadline', 'Retry with total deadline'],
]);

function runAsyncLifecycleContract(string $slug, string $checks): void
{
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/code-examples/async-await-'.$slug.'.js');
    $harness = <<<'JS'
const assert = require('assert/strict');
const {getEventListeners} = require('node:events');
let now = 0, nextTimer = 0;
const timers = new Map();
globalThis.setTimeout = (fn, ms) => {
    const id = ++nextTimer;
    timers.set(id, {fn, at: now + ms});
    return id;
};
globalThis.clearTimeout = id => timers.delete(id);
const flush = async () => {for (let i = 0; i < 40; i++) await Promise.resolve();};
const advanceTo = async target => {
    await flush();
    for (;;) {
        const next = [...timers.entries()].sort((a,b) => a[1].at-b[1].at)[0];
        if (!next || next[1].at > target) break;
        now = next[1].at; timers.delete(next[0]); next[1].fn(); await flush();
    }
    now = target;
    await flush();
};
JS;
    $process = new Process(['node', '-e', file_get_contents($path)."\n".$harness."\n(async()=>{\n".$checks."\nprocess.stdout.write('passed');\n})().catch(error=>{process.stderr.write(error.stack);process.exitCode=1;});"]);
    $process->mustRun();
    expect($process->getOutput())->toBe('passed');
}

it('awaits all sibling cleanup and preserves the first task failure', function () {
    runAsyncLifecycleContract('group-cleanup', <<<'JS'
const original = new Error('original A error'), events = [];
let done = false, siblingSignal;
const pending = runGroup(async signal => {
    try {await delay(10, signal); throw original;}
    finally {await delay(5); events.push('A cleaned');}
}, async signal => {
    siblingSignal = signal;
    try {await delay(100, signal); return 20;}
    finally {await delay(5); events.push('B cleaned');}
}).then(() => assert.fail('expected A failure'), error => {done = true; return error;});
await advanceTo(10); assert.equal(done, false); assert.deepEqual(events, []);
await advanceTo(15);
assert.equal(siblingSignal.aborted, true);
assert.equal(done, false); assert.deepEqual(events, ['A cleaned']);
await advanceTo(20);
assert.equal(await pending, original);
assert.deepEqual(events, ['A cleaned', 'B cleaned']);
assert.equal(timers.size, 0);
assert.equal(getEventListeners(siblingSignal, 'abort').length, 0);
assert.deepEqual(await runGroup(async () => 10, async () => 20), [10, 20]);
JS);
});

it('closes an async stream after break exhaustion or cancellation without prefetching', function () {
    runAsyncLifecycleContract('stream', <<<'JS'
let controller = new AbortController(), reads = [], cleaned = 0, done = false;
const pending = consumeStream(controller.signal, {
    onRead: value => reads.push(value), onCleanup: () => cleaned++,
}).then(result => {done = true; return result;});
await advanceTo(20);
assert.equal(done, false); assert.deepEqual(reads, [10, 20]); assert.equal(cleaned, 0);
await advanceTo(25);
assert.deepEqual(await pending, [10, 20]); assert.equal(cleaned, 1);
assert.equal(timers.size, 0);
const all = consumeStream(controller.signal, {take: Infinity, onCleanup: () => cleaned++});
await advanceTo(60);
assert.deepEqual(await all, [10, 20, 30]); assert.equal(cleaned, 2);
const stopped = consumeStream(controller.signal, {onCleanup: () => cleaned++})
    .then(() => assert.fail('expected cancellation'), error => error);
const reason = new Error('Stop read'); controller.abort(reason);
await flush(); assert.equal(cleaned, 2);
await advanceTo(65);
assert.equal(await stopped, reason); assert.equal(cleaned, 3);
assert.equal(timers.size, 0);
assert.equal(getEventListeners(controller.signal, 'abort').length, 0);
JS);
});

it('keeps a single retry deadline across attempts and cleans timers on every outcome', function () {
    runAsyncLifecycleContract('retry-deadline', <<<'JS'
let attempts = [], done = false;
let controller = new AbortController();
const pending = retryWithDeadline(async (attempt, signal) => {
    attempts.push([attempt, now]);
    await delay(attempt === 1 ? 10 : 100, signal);
    if (attempt === 1) throw new TransientError('retry');
    return 42;
}, {totalMs: 50, maxAttempts: 3, delayMs: 20, signal: controller.signal})
.then(() => assert.fail('expected timeout'), error => {done = true; return error;});
await advanceTo(30); assert.deepEqual(attempts, [[1,0],[2,30]]);
await advanceTo(49); assert.equal(done, false);
await advanceTo(50);
assert.ok(await pending instanceof TimeoutError);
assert.equal(attempts.length, 2); assert.equal(timers.size, 0);
assert.equal(getEventListeners(controller.signal, 'abort').length, 0);
const success = retryWithDeadline(async (_, signal) => {await delay(5, signal); return 42;}, {signal: controller.signal});
await advanceTo(55); assert.equal(await success, 42); assert.equal(timers.size, 0);
const original = new Error('Permanent');
await assert.rejects(retryWithDeadline(async () => {throw original;}, {signal: controller.signal}), e => e === original);
assert.equal(timers.size, 0);
attempts = [];
const cancelled = retryWithDeadline(async (attempt, signal) => {
    attempts.push(attempt); await delay(10, signal); throw new TransientError('retry');
}, {signal: controller.signal}).catch(error => error);
await advanceTo(65); // Now waiting between attempts.
const reason = new Error('Caller cancelled'); controller.abort(reason);
await flush(); assert.equal(await cancelled, reason);
assert.deepEqual(attempts, [1]); assert.equal(timers.size, 0);
assert.equal(getEventListeners(controller.signal, 'abort').length, 0);
await assert.rejects(retryWithDeadline(async () => 42, {signal: controller.signal}), e => e === reason);
assert.equal(timers.size, 0);
JS);
});
