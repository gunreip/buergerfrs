<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('renders start segment labels with width alignment justify and badge color', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.start
            :dev="true"
            :segment="[
                'id' => 'segment.start.test',
                'color' => 'green',
                'startLabel' => [
                    'text' => ['Start label', 'with details'],
                    'side' => 'top',
                    'width' => 'halfLong',
                    'align' => 'left',
                    'justify' => true,
                    'badgeColor' => 'amber',
                ],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('segment.start.test.label.top.1')
        ->toContain('Start label')
        ->toContain('with details')
        ->toContain('tw-graph-protocol-primitive-text-label-top')
        ->toContain('w-72')
        ->toContain('items-start text-left')
        ->toContain('text-justify')
        ->toContain('text-amber-700');
});

it('renders end segment cap counter and default top end label', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.end
            :dev="true"
            :segment="[
                'id' => 'segment.end.test',
                'color' => 'rose',
                'devCounterEnd' => 7,
                'endLabel' => [
                    'text' => ['End label', 'chain end'],
                    'width' => 'long',
                ],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('segment.end.test.cap.end')
        ->toContain('7')
        ->toContain('segment.end.test.label.top.1')
        ->toContain('End label')
        ->toContain('chain end')
        ->toContain('tw-graph-protocol-primitive-text-label-top')
        ->toContain('w-96')
        ->toContain('text-rose-700');
});

it('passes end label formatting options through to primitive text', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.end
            :segment="[
                'id' => 'segment.end.formatted',
                'color' => 'green',
                'endLabel' => [
                    'text' => ['Formatted end', 'secondary line', 'third line'],
                    'width' => 'halfLong',
                    'align' => 'right',
                    'justify' => true,
                    'maxLines' => 2,
                    'color' => 'amber',
                ],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('segment.end.formatted.label.top.1')
        ->toContain('Formatted end')
        ->toContain('secondary line')
        ->not->toContain('third line')
        ->toContain('w-72')
        ->toContain('items-end text-right')
        ->toContain('text-justify')
        ->toContain('text-amber-700');
});

it('renders step segments with automatic label gap and centered label props', function (): void {
    config()->set('tw-graph-defaults.label_offset', '1rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.step
            :dev="true"
            :segment="[
                'id' => 'segment.step.test',
                'color' => 'amber',
                'beforeLength' => '1rem',
                'afterLength' => '3rem',
                'stepLabel' => [
                    'text' => ['Source inactive', 'shared obsolete', '9 rows'],
                    'width' => 'halfLong',
                    'align' => 'right',
                ],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('segment.step.test.stem.before')
        ->toContain('segment.step.test.stem.after')
        ->toContain('calc(4.75rem + (1rem * 2))')
        ->toContain('segment.step.test.label')
        ->toContain('Source inactive')
        ->toContain('shared obsolete')
        ->toContain('9 rows')
        ->toContain('tw-graph-protocol-primitive-text-label-center')
        ->toContain('w-72')
        ->toContain('items-end text-right');
});

it('renders vertical path node labels on right and left by default', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.path
            :dev="true"
            :segment="[
                'id' => 'segment.path.vertical',
                'direction' => 'bottom-top',
                'nodeEnd' => [
                    ['text' => ['Right label'], 'width' => 'halfLong'],
                    ['text' => ['Left label'], 'width' => 'half'],
                ],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('segment.path.vertical.label.right.1')
        ->toContain('segment.path.vertical.label.left.2')
        ->toContain('Right label')
        ->toContain('Left label')
        ->toContain('tw-graph-protocol-primitive-text-label-right')
        ->toContain('tw-graph-protocol-primitive-text-label-left')
        ->toContain('w-72')
        ->toContain('w-24');
});

it('renders horizontal path node labels on top and bottom by default', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.path
            :dev="true"
            :segment="[
                'id' => 'segment.path.horizontal',
                'direction' => 'left-right',
                'anchorEnd' => ['x' => '8rem', 'y' => '0rem'],
                'length' => '8rem',
                'nodeEnd' => [
                    ['text' => ['Top label']],
                    ['text' => ['Bottom label']],
                ],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('segment.path.horizontal.label.top.1')
        ->toContain('segment.path.horizontal.label.bottom.2')
        ->toContain('Top label')
        ->toContain('Bottom label')
        ->toContain('tw-graph-protocol-primitive-text-label-top')
        ->toContain('tw-graph-protocol-primitive-text-label-bottom');
});

it('keeps dev counters when only the visible node dot is hidden', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.path
            :dev="true"
            :segment="[
                'id' => 'segment.path.hidden-dot',
                'nodeEnd' => true,
                'nodeEndDot' => false,
                'devCounterEnd' => 3,
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('segment.path.hidden-dot.node.end')
        ->toContain('3')
        ->not->toContain('tw-graph-protocol-primitive-node"');
});

it('keeps start dev counters when only the start dot is hidden', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.path
            :dev="true"
            :segment="[
                'id' => 'segment.path.hidden-start-dot',
                'nodeStart' => true,
                'nodeStartDot' => false,
                'devCounterStart' => 1,
                'nodeEnd' => true,
                'devCounterEnd' => 2,
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('segment.path.hidden-start-dot.node.start')
        ->toContain('segment.path.hidden-start-dot.node.end')
        ->toContain('1')
        ->toContain('2')
        ->not->toContain('tw-graph-protocol-primitive-line-node-start')
        ->toContain('tw-graph-protocol-primitive-line-node-end');
});

it('renders path start and end caps from explicit cap flags', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.path
            :segment="[
                'id' => 'segment.path.capped',
                'capStart' => true,
                'capEnd' => true,
                'capLength' => '2rem',
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('segment.path.capped')
        ->toContain('tw-graph-protocol-primitive-line-cap-start')
        ->toContain('tw-graph-protocol-primitive-line-end')
        ->toContain('--tw-graph-protocol-line-end-cap-length: 2rem');
});

it('normalizes impossible label sides for path direction', function (): void {
    $horizontalHtml = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.path
            :segment="[
                'id' => 'segment.path.side-horizontal',
                'direction' => 'left-right',
                'nodeEnd' => [
                    ['text' => ['Invalid side'], 'side' => 'left'],
                ],
            ]"
        />
    BLADE);
    $verticalHtml = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.path
            :segment="[
                'id' => 'segment.path.side-vertical',
                'direction' => 'bottom-top',
                'nodeEnd' => [
                    ['text' => ['Invalid side'], 'side' => 'top'],
                ],
            ]"
        />
    BLADE);

    expect($horizontalHtml)
        ->toContain('segment.path.side-horizontal.label.top.1')
        ->not->toContain('segment.path.side-horizontal.label.left.1')
        ->and($verticalHtml)
        ->toContain('segment.path.side-vertical.label.right.1')
        ->not->toContain('segment.path.side-vertical.label.top.1');
});

it('renders step segments with explicit label gap instead of the automatic gap', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.step
            :segment="[
                'id' => 'segment.step.explicit-gap',
                'beforeLength' => '1rem',
                'labelGap' => '9rem',
                'afterLength' => '2rem',
                'stepLabel' => ['text' => ['Explicit gap']],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('segment.step.explicit-gap.label')
        ->toContain('Explicit gap')
        ->toContain('calc(9rem / 2)')
        ->toContain('calc(calc(0rem + 1rem) + 9rem)');
});

it('calculates automatic step gaps from the visible step label line count', function (): void {
    config()->set('tw-graph-defaults.label_offset', '0.5rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.step
            :segment="[
                'id' => 'segment.step.one-line',
                'stepLabel' => ['text' => ['One line']],
            ]"
        />
        <x-translation-workbench::ui.tw-graph.segments.step
            :segment="[
                'id' => 'segment.step.two-lines',
                'stepLabel' => ['text' => ['One', 'Two']],
            ]"
        />
        <x-translation-workbench::ui.tw-graph.segments.step
            :segment="[
                'id' => 'segment.step.three-lines',
                'stepLabel' => ['text' => ['One', 'Two', 'Three', 'Four']],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('segment.step.one-line.label')
        ->toContain('calc(2.75rem + (0.5rem * 2))')
        ->toContain('segment.step.two-lines.label')
        ->toContain('calc(3.75rem + (0.5rem * 2))')
        ->toContain('segment.step.three-lines.label')
        ->toContain('calc(4.75rem + (0.5rem * 2))')
        ->toContain('Three')
        ->not->toContain('Four');
});
