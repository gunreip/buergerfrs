<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Livewire\Livewire;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class);

it('connects BREAK outside WHILE and only the successful action back to the condition', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-test';
    $html = view($view)->render();
    $point = function ($suffix) {
        $a = AnchorRegistry::get('idea-to-paper-break-test-left', 'literature.break.test.'.$suffix);
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
    expect($xp->query('//pre/code')->length)->toBe(7);
    $counters = [];
    foreach ($xp->query('//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-dev-node-counter ")]') as $node) {
        $counters[] = (int) trim($node->textContent);
    }
    sort($counters);
    expect($counters)->toBe(range(1, 16));
});

it('loads BREAK lazily and returns from its component reference', function () {
    Livewire::test(TwGraphDocumentation::class)
        ->call('openExample', 'flow.break-continue.flow-break-test')
        ->assertSet('tabs.flow_index', 'flow-break-continue')
        ->assertSet('tabs.flow_break_continue', 'flow-break-test')
        ->assertSee('id="idea-to-paper-break-test-left"', false)
        ->assertSee('Suggested sequence')
        ->call('openReference', 'strang.flow-while', 'flow.break-continue.flow-break-test')
        ->assertDontSee('id="idea-to-paper-break-test-left"', false)
        ->call('returnToExample')
        ->assertSee('id="idea-to-paper-break-test-left"', false);
});

it('executes the documented BREAK without processing later items', function (array $items, array $expected) {
    $path = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/break-continue/code-examples/break-test.php');
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
