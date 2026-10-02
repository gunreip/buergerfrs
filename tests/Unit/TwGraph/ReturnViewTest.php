<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource;
use Livewire\Livewire;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class);

it('keeps the early RETURN independent of the normal processing route', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-guard';
    $html = view($view)->render();
    $point = function ($suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-return-guard-left', 'literature.return.guard.left.'.$suffix);
        expect($anchor)->not->toBeNull($suffix);
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    [$x, $y] = $point('guard.false.anchorNode-end');
    expect($point('result.anchorNode-end')[0])->toEqual($x);
    expect($point('result.anchorNode-end')[1])->toEqualWithDelta($y - 6.8, 0.000001);
    expect($point('guard.true.anchorNode-end'))->not->toEqual($point('guard.false.anchorNode-end'));
    expect($html)->not->toContain('data-tw-graph-layout-issues', 'data-tw-graph-path="literature.return.guard.left.guard.true.stem"');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-return-guard-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 8));

    expect(ExampleSource::fromView($view)->example('return-guard-left-example'))->toContain('literature.return.guard.left.early-end', 'literature.return.guard.left.normal-end');
});

it('executes the documented integer function for guard and normal return cases', function ($value, $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/return/code-examples/return-guard.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';echo json_encode(doublePositive('.$value.'));']);
    $process->setTimeout(5);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe($expected);
})->with([[-3, 0], [0, 0], [1, 2], [8, 16]]);

it('loads RETURN lazily and returns to the selected example from deep reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->assertDontSee('id="idea-to-paper-return-test-left"', false)
        ->call('openExample', 'flow.return.flow-return-test')
        ->assertSet('tabs.flow_index', 'flow-return')
        ->assertSet('tabs.flow_return', 'flow-return-test')
        ->assertSee('id="idea-to-paper-return-test-left"', false)
        ->assertSee('Suggested sequence')
        ->call('openReference', 'parts.end', 'flow.return.flow-return-test')
        ->assertDontSee('id="idea-to-paper-return-test-left"', false)
        ->call('returnToExample')
        ->assertSet('tabs.flow_return', 'flow-return-test')
        ->assertSee('id="idea-to-paper-return-test-left"', false);
});

it('mirrors the saved guard clause and restores it through deep reference', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-guard';
    $html = view($view)->render();
    foreach (['guard.true.anchorNode-end', 'guard.false.anchorNode-end', 'result.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-return-guard-'.$side, 'literature.return.guard.'.$side.'.'.$suffix);
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.return.flow-return-guard')
        ->assertSee('id="idea-to-paper-return-guard-left"', false)
        ->assertSee('id="idea-to-paper-return-guard-right"', false)
        ->assertDontSee('id="idea-to-paper-return-test-left"', false)
        ->call('openReference', 'parts.end', 'flow.return.flow-return-guard')
        ->call('returnToExample')
        ->assertSet('tabs.flow_return', 'flow-return-guard');
});

it('connects the second guard only to the first FALSE route and leaves both early exits open', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-multiple-guards')->render();
    $point = function ($suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-return-multiple-guards-left', 'literature.return.multiple-guards.left.'.$suffix);
        expect($anchor)->not->toBeNull();
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    [$x, $y] = $point('guard.false.anchorNode-end');
    $second = $point('upper-guard.anchorNode-decision');
    expect($second[0])->toEqual($x);
    expect($second[1])->toEqualWithDelta($y - 6.8, 0.000001);
    [$x, $y] = $point('upper-guard.false.anchorNode-end');
    expect($point('result.anchorNode-end')[0])->toEqual($x);
    expect($point('result.anchorNode-end')[1])->toEqualWithDelta($y - 6.8, 0.000001);
    expect($html)->not->toContain('data-tw-graph-layout-issues', 'data-tw-graph-path="literature.return.multiple-guards.left.guard.true.stem"', 'data-tw-graph-path="literature.return.multiple-guards.left.upper-guard.true.stem"');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-return-multiple-guards-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 13));
});

it('executes the multiple-guard excerpt including both boundary values', function ($value, $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/return/code-examples/return-multiple-guards.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';echo json_encode(boundedDouble('.$value.'));']);
    $process->setTimeout(5);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe($expected);
})->with([[-1, 0], [0, 0], [1, 2], [99, 198], [100, 200], [101, 200]]);

it('mirrors the saved multiple guards and exposes their own code examples', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-multiple-guards';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['guard.true.anchorNode-end', 'guard.false.anchorNode-end', 'upper-guard.true.anchorNode-end', 'result.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-return-multiple-guards-'.$side, 'literature.return.multiple-guards.'.$side.'.'.$suffix);
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.return.flow-return-multiple-guards')
        ->assertSee('id="idea-to-paper-return-multiple-guards-right"', false)
        ->call('openReference', 'parts.end', 'flow.return.flow-return-multiple-guards')
        ->call('returnToExample')
        ->assertSet('tabs.flow_return', 'flow-return-multiple-guards');
});

it('returns only unmatched iterations to WHILE and keeps the successful RETURN separate', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-loop')->render();
    $point = function ($suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-return-loop-left', 'literature.return.loop.left.'.$suffix);
        expect($anchor)->not->toBeNull();
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    expect($point('body-return.anchorNode-start'))->toBe($point('decision.false.anchorNode-end'));
    expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-return'));
    expect($point('decision.true.anchorNode-end'))->not->toBe($point('body-return.anchorNode-start'));
    expect($point('fallback.anchorNode-end')[1])->toBeGreaterThan($point('loop.anchorNode-end')[1]);
    expect($html)->not->toContain('data-tw-graph-layout-issues', 'data-tw-graph-path="literature.return.loop.left.decision.true.stem"');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-return-loop-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 18));
});

it('executes the loop RETURN excerpt for empty, exhausted and matching input', function ($values, $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/return/code-examples/return-loop.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';echo json_encode(findFirstPositive('.var_export($values, true).'));']);
    $process->setTimeout(5);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe($expected);
})->with([[[], -1], [[0, -1], -1], [[3, 4], 0], [[0, -2, 5, 6], 2]]);

it('mirrors the saved RETURN loop and restores its tab from reference', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-loop';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['decision.true.anchorNode-end', 'decision.false.anchorNode-end', 'body-return.anchorNode-end', 'fallback.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-return-loop-'.$side, 'literature.return.loop.'.$side.'.'.$suffix);
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.return.flow-return-loop')
        ->assertSee('id="idea-to-paper-return-loop-right"', false)
        ->call('openReference', 'parts.end', 'flow.return.flow-return-loop')
        ->call('returnToExample')
        ->assertSet('tabs.flow_return', 'flow-return-loop');
});

it('keeps nested search returns on their own level while the match terminates the function', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-nested')->render();
    $point = function ($suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-return-nested-left', 'literature.return.nested.left.'.$suffix);
        expect($anchor)->not->toBeNull($suffix);
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    expect($point('inner-return.anchorNode-start'))->toBe($point('decision.false.anchorNode-end'));
    expect($point('inner-return.anchorNode-end'))->toBe($point('inner-loop.anchorNode-return'));
    expect($point('body-return.anchorNode-end'))->toBe($point('loop.anchorNode-return'));
    expect($point('inner-return.anchorNode-end'))->not->toBe($point('body-return.anchorNode-end'));
    expect($point('decision.true.anchorNode-end'))->not->toBe($point('inner-return.anchorNode-start'));
    expect($html)->not->toContain('data-tw-graph-layout-issues', 'data-tw-graph-path="literature.return.nested.left.decision.true.stem"');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-return-nested-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 34));
});

it('returns the first positive item across groups and handles empty groups', function ($groups, $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/return/code-examples/return-nested.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';echo json_encode(firstPositive('.var_export($groups, true).'));']);
    $process->setTimeout(5);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe($expected);
})->with([[[], 0], [[[], []], 0], [[[0, -1], [-2]], 0], [[[3, 4], [5]], 3], [[[], [0, -1], [-2, 7], [8]], 7]]);

it('mirrors the saved nested RETURN routes and exposes the saved tab', function () {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-nested')->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['inner-loop.anchorNode-return', 'decision.true.anchorNode-end', 'inner-return.anchorNode-end', 'advance.anchorNode-end', 'body-return.anchorNode-start', 'body-return.anchorNode-end', 'fallback.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-return-nested-'.$side, 'literature.return.nested.'.$side.'.'.$suffix);
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.return.flow-return-nested')
        ->assertSee('id="idea-to-paper-return-nested-left"', false)
        ->assertSee('id="idea-to-paper-return-nested-right"', false)
        ->assertDontSee('id="idea-to-paper-return-test-left"', false)
        ->call('openReference', 'parts.end', 'flow.return.flow-return-nested')
        ->call('returnToExample')
        ->assertSet('tabs.flow_return', 'flow-return-nested');
});

it('routes pending RETURN through shared FINALLY before the function ends', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-finally';
    $html = view($view)->render();
    $point = function ($suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-return-finally-left', 'literature.return.finally.left.'.$suffix);
        expect($anchor)->not->toBeNull($suffix);
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    $selection = $point('selection.anchorNode-end');
    $finally = $point('finally.anchorNode-end');
    $deliver = $point('deliver.anchorNode-end');
    expect($finally[0])->toEqual($selection[0]);
    expect($finally[1])->toBeGreaterThan($selection[1]);
    expect($deliver[0])->toEqual($finally[0]);
    expect($deliver[1])->toBeGreaterThan($finally[1]);
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-return-finally-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 7));
    expect(ExampleSource::fromView($view)->example('return-finally-left-example'))
        ->toContain('literature.return.finally.left.finally', 'literature.return.finally.left.deliver', 'literature.return.finally.left.end');
});

it('runs FINALLY exactly once and preserves the evaluated return value', function ($value, $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/return/code-examples/return-finally.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';$events=[];$result=doublePositive('.$value.',$events);$events[]="caller";echo json_encode([$result,$events]);']);
    $process->setTimeout(5);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([$expected, ['cleanup', 'caller']]);
})->with([[3, 6], [0, 0], [-5, 0]]);

it('mirrors FINALLY and exposes its saved lazy tab with both source examples', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-finally';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['selection.anchorNode-end', 'finally.anchorNode-end', 'deliver.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-return-finally-'.$side, 'literature.return.finally.'.$side.'.'.$suffix);
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    foreach (['left', 'right'] as $side) {
        expect(ExampleSource::fromView($view)->example('return-finally-'.$side.'-example'))
            ->toContain('literature.return.finally.'.$side.'.finally', 'stem-length="4rem"', 'side="'.$side.'"');
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.return.flow-return-finally')
        ->assertSet('tabs.flow_return', 'flow-return-finally')
        ->assertSee('id="idea-to-paper-return-finally-left"', false)
        ->assertSee('id="idea-to-paper-return-finally-right"', false)
        ->assertDontSee('id="idea-to-paper-return-test-left"', false)
        ->call('openReference', 'parts.end', 'flow.return.flow-return-finally')
        ->call('returnToExample')
        ->assertSet('tabs.flow_return', 'flow-return-finally');
});

it('keeps the bare early RETURN separate from the notification route', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-test';
    $html = view($view)->render();
    $point = function ($suffix) {
        $anchor = AnchorRegistry::get('idea-to-paper-return-test-left', 'literature.return.test.'.$suffix);
        expect($anchor)->not->toBeNull();
        return array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
    };
    $early = $point('guard.true.anchorNode-end');
    $normal = $point('guard.false.anchorNode-end');
    $result = $point('result.anchorNode-end');
    expect($early)->not->toEqual($normal);
    expect($result[0])->toEqual($normal[0]);
    expect($result[1])->toBeLessThan($normal[1]);
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $numbers = [];
    foreach ($xp->query('//*[@id="idea-to-paper-return-test-left"]//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $numbers[] = (int) trim($node->textContent);
    }
    sort($numbers);
    expect($numbers)->toBe(range(1, 8));
    expect(ExampleSource::fromView($view)->example('return-test-left-example'))
        ->toContain('TRUE: RETURN (no value)', 'Record notification');
});

it('skips the action on bare RETURN and records it on the normal route', function ($enabled, $expectedEvents) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/return/code-examples/return-test.php');
    $process = new Process([PHP_BINARY, '-r', substr(file_get_contents($path), 5).';$events=[];$result=notifyIfEnabled('.var_export($enabled, true).',$events);echo json_encode([$result,$events]);']);
    $process->setTimeout(5);
    $process->mustRun();
    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe([null, $expectedEvents]);
})->with([[false, []], [true, ['notified']]]);

it('mirrors saved bare RETURN exits and links their lazy tab to the reference', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-void';
    $html = view($view)->render();
    expect($html)->not->toContain('data-tw-graph-layout-issues');
    foreach (['guard.true.anchorNode-end', 'guard.false.anchorNode-end', 'result.anchorNode-end'] as $suffix) {
        $points = [];
        foreach (['left', 'right'] as $side) {
            $anchor = AnchorRegistry::get('idea-to-paper-return-void-'.$side, 'literature.return.void.'.$side.'.'.$suffix);
            expect($anchor)->not->toBeNull();
            $points[$side] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
        expect($points['right'])->toEqual([-$points['left'][0], $points['left'][1]]);
    }
    foreach (['left', 'right'] as $side) {
        expect(ExampleSource::fromView($view)->example('return-void-'.$side.'-example'))
            ->toContain('literature.return.void.'.$side.'.early-end', 'side="'.$side.'"');
    }
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.return.flow-return-void')
        ->assertSet('tabs.flow_return', 'flow-return-void')
        ->assertSee('id="idea-to-paper-return-void-left"', false)
        ->assertSee('id="idea-to-paper-return-void-right"', false)
        ->assertDontSee('id="idea-to-paper-return-test-left"', false)
        ->call('openReference', 'parts.end', 'flow.return.flow-return-void')
        ->call('returnToExample')
        ->assertSet('tabs.flow_return', 'flow-return-void');
});
