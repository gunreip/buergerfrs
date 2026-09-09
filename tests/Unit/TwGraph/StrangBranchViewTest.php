<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('passes named branch right stem continuation label options through to rendered labels', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-right
                id="sample.right.1.branch"
                bridge-length="8rem"
                :stem-continuation="[
                    1 => [
                        'length' => '4rem',
                        'right' => [
                            'text' => ['Needs info', 'assigned to support'],
                            'width' => 'halfLong',
                            'align' => 'left',
                            'justify' => true,
                            'color' => 'amber',
                        ],
                        'left' => [
                            'text' => ['SLA paused'],
                            'width' => 'half',
                            'align' => 'right',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.branch.right.1.stem-1.label.left.1')
        ->toContain('strang.branch.right.1.stem-1.label.right.2')
        ->toContain('Needs info')
        ->toContain('assigned to support')
        ->toContain('SLA paused')
        ->toContain('w-72')
        ->toContain('w-24')
        ->toContain('items-start text-left')
        ->toContain('items-end text-right')
        ->toContain('text-justify')
        ->toContain('text-align: justify;')
        ->toContain('text-amber-700');
});

it('keeps scalar branch continuation labels from leaking shared formatting options into text', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-scalar-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-left
                id="sample.left.1.branch"
                bridge-length="8rem"
                :stem-continuation="[
                    1 => [
                        'length' => '4rem',
                        'left' => 'finding ID #42|ui.checkout.save|2026-04-14 11:36',
                        'width' => 'halfLong',
                        'align' => 'right',
                        'justify' => true,
                        'color' => 'amber',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.branch.left.1.stem-1.label.left.1')
        ->toContain('finding ID #42')
        ->toContain('ui.checkout.save')
        ->toContain('2026-04-14 11:36')
        ->toContain('w-72')
        ->toContain('items-end text-right')
        ->toContain('text-justify')
        ->toContain('text-align: justify;')
        ->toContain('text-amber-700')
        ->toContain('--tw-graph-protocol-text-label-offset: calc(var(--tw-graph-protocol-node-half) + 2rem + 0.25rem)')
        ->not->toContain('halfLong right')
        ->not->toContain('default right')
        ->not->toContain('justify true');
});

it('renders branch stem joint arrows when forced stem anchors have no labels', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-joint-arrow-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-left
                id="sample.left.1.branch"
                bridge-length="8rem"
                :stem-continuation="[
                    1 => ['length' => '4rem', 'force' => true],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toMatch('/class="[^"]*tw-graph-protocol-primitive-joint-arrow-left[^"]*"[^>]*title="strang\.branch\.left\.1\.arc-east-north-1\.end\.joint-arrow"/')
        ->toMatch('/class="[^"]*tw-graph-protocol-primitive-joint-arrow-top[^"]*"[^>]*title="strang\.branch\.left\.1\.arc-south-west-2\.end\.joint-arrow"/')
        ->toContain('strang.branch.left.1.bridge-1.end.joint-arrow')
        ->toContain('strang.branch.left.1.stem-1.end.joint-arrow')
        ->not->toContain('strang.branch.left.1.stem-1.label.left')
        ->not->toContain('strang.branch.left.1.stem-1.label.right');
});

it('reports branch node label mismatch when numeric labels exceed branch anchors', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-node-label-mismatch-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-right
                id="sample.right.1.branch"
                bridge-length="8rem"
                :node-labels="[
                    4 => ['right' => 'Ignored branch label'],
                ]"
                :stem-continuation="[
                    1 => ['length' => '4rem', 'force' => true],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('nodeLabel-Mismatch')
        ->toContain('ignored: 1')
        ->not->toContain('Ignored branch label');
});

it('inherits graph color for branch labels unless the label sets its own color', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-color-inheritance-test" color="violet" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-right
                id="sample.right.1.branch"
                bridge-length="8rem"
                :stem-continuation="[
                    1 => [
                        'length' => '4rem',
                        'right' => [
                            'text' => ['Inherited graph color'],
                            'width' => 'default',
                            'align' => 'left',
                        ],
                        'left' => [
                            'text' => ['Local label color'],
                            'width' => 'default',
                            'align' => 'right',
                            'color' => 'amber',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('Inherited graph color')
        ->toContain('Local label color')
        ->toContain('text-violet-700')
        ->toContain('text-amber-700');
});

it('promotes the first labeled branch stem onto the step end anchor without losing label options', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-step-label-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-left
                id="sample.left.1.branch"
                bridge-length="8rem"
                :step="[
                    'stepLabel' => [
                        'text' => ['Source inactive', 'shared obsolete'],
                        'width' => 'halfLong',
                    ],
                ]"
                :stem-continuation="[
                    1 => [
                        'length' => '4rem',
                        'left' => [
                            'text' => ['finding ID #42', '2026-04-14 11:36'],
                            'width' => 'halfLong',
                            'align' => 'right',
                        ],
                        'right' => [
                            'text' => ['ui.checkout.save'],
                            'width' => 'long',
                            'align' => 'left',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.branch.left.1.step-1.label')
        ->toContain('strang.branch.left.1.step-1.label.left.1')
        ->toContain('strang.branch.left.1.step-1.label.right.2')
        ->toContain('Source inactive')
        ->toContain('shared obsolete')
        ->toContain('finding ID #42')
        ->toContain('2026-04-14 11:36')
        ->toContain('ui.checkout.save')
        ->toContain('w-72')
        ->toContain('w-96')
        ->toContain('items-end text-right')
        ->toContain('items-start text-left');
});

it('renders branch extensions from configured branch anchors with named labels', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-extension-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-left
                id="sample.left.1.branch"
                bridge-length="8rem"
                :stem-continuation="[
                    1 => ['length' => '4rem', 'force' => true],
                ]"
                :branch-extension="[
                    'stem.1' => [
                        1 => [
                            'bridgeLength' => '9rem',
                            'stemLength' => '5rem',
                            'nodeLabels' => [
                                3 => [
                                    'left' => [
                                        'text' => ['Extension start', 'from stem 1'],
                                        'width' => 'halfLong',
                                        'align' => 'right',
                                        'color' => 'amber',
                                    ],
                                    'right' => [
                                        'text' => ['Extension detail'],
                                        'width' => 'long',
                                        'align' => 'left',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.left.1.branch.extension.1.arc.in')
        ->toContain('sample.left.1.branch.extension.1.bridge')
        ->toContain('sample.left.1.branch.extension.1.arc')
        ->toContain('sample.left.1.branch.extension.1.stem')
        ->toContain('sample.left.1.branch.extension.1.stem.label.left.1')
        ->toContain('sample.left.1.branch.extension.1.stem.label.right.2')
        ->toContain('Extension start')
        ->toContain('from stem 1')
        ->toContain('Extension detail')
        ->toContain('--tw-graph-protocol-local-length: 9rem')
        ->toContain('--tw-graph-protocol-local-length: 5rem')
        ->toContain('w-72')
        ->toContain('w-96')
        ->toContain('items-end text-right')
        ->toContain('items-start text-left')
        ->toContain('text-amber-700');
});

it('renders right branch extensions from branch anchors with mirrored named labels', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-right-extension-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-right
                id="sample.right.1.branch"
                bridge-length="8rem"
                :stem-continuation="[
                    1 => ['length' => '4rem', 'force' => true],
                ]"
                :branch-extension="[
                    'stem.1' => [
                        1 => [
                            'bridgeLength' => '12rem',
                            'stemLength' => '6rem',
                            'nodeLabels' => [
                                3 => [
                                    'right' => [
                                        'text' => ['Right extension start', 'from stem 1'],
                                        'width' => 'halfLong',
                                        'align' => 'left',
                                        'color' => 'amber',
                                    ],
                                    'left' => [
                                        'text' => ['Mirrored detail'],
                                        'width' => 'long',
                                        'align' => 'right',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.right.1.branch.extension.1.arc.in')
        ->toContain('sample.right.1.branch.extension.1.bridge')
        ->toContain('sample.right.1.branch.extension.1.arc')
        ->toContain('sample.right.1.branch.extension.1.stem')
        ->toContain('sample.right.1.branch.extension.1.stem.label.left.1')
        ->toContain('sample.right.1.branch.extension.1.stem.label.right.2')
        ->toContain('Right extension start')
        ->toContain('from stem 1')
        ->toContain('Mirrored detail')
        ->toContain('--tw-graph-protocol-local-length: 12rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('w-72')
        ->toContain('w-96')
        ->toContain('items-start text-left')
        ->toContain('items-end text-right')
        ->toContain('text-amber-700');
});

it('renders branch returns from branch stem anchors without falling back to the trunk', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-return-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-right
                id="sample.right.1.branch"
                bridge-length="8rem"
                :stem-continuation="[
                    1 => ['length' => '4rem', 'force' => true],
                    2 => ['length' => '4rem', 'force' => true],
                ]"
                :branch-return="[
                    1 => [
                        'attachTo' => 'stem.2',
                        'bridgeLength' => '11rem',
                        'fallback' => false,
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.right.1.branch.return.1.arc.in')
        ->toContain('sample.right.1.branch.return.1.bridge')
        ->toContain('sample.right.1.branch.return.1.arc.out')
        ->toContain('--tw-graph-protocol-local-length: 11rem')
        ->toContain('tw-graph-protocol-primitive-line-right-left')
        ->not->toContain('tw-graph-protocol-primitive-line-dashed')
        ->not->toContain('tw-graph-protocol-primitive-arc-dashed');
});

it('marks branch return close targets as forced trunk nodes', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-return-close-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="sample.center.1.trunk"
                :stem-count="4"
                color="sky"
            />
            <x-translation-workbench::ui.tw-graph.strang.branch-left
                id="sample.left.1.branch"
                attach-to="trunk.center.1.stem-2.anchorNode-end"
                bridge-length="8rem"
                :stem-continuation="[
                    1 => ['length' => '4rem', 'force' => true],
                ]"
                :branch-return="[
                    1 => [
                        'attachTo' => 'stem.1',
                        'closeTo' => '+2',
                        'bridgeLength' => '10rem',
                        'fallback' => false,
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('trunk.center.1.stem-4.anchorNode-end.forced-node')
        ->toContain('--tw-graph-protocol-local-color-rgb: 14 165 233')
        ->toContain('--tw-graph-protocol-z-index: 20')
        ->not->toContain('strang.trunk.center.1.center.stem-4.anchorNode-end.forced-node');
});

it('passes branch extension return bridge labels and options through', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-extension-return-bridge-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-left
                id="sample.left.1.branch"
                bridge-length="8rem"
                :stem-continuation="[
                    1 => ['length' => '4rem', 'force' => true],
                ]"
                :branch-extension="[
                    'stem.1' => [
                        1 => [
                            'bridgeLength' => '9rem',
                            'stemLength' => '5rem',
                            'returnBridge' => [
                                'bridgeLength' => '11rem',
                                'nodeLabels' => [
                                    1 => [
                                        'text' => ['Return turn'],
                                        'width' => 'half',
                                        'align' => 'center',
                                        'color' => 'amber',
                                    ],
                                    2 => [
                                        'bottom' => [
                                            'text' => ['Back to trunk', 'solid path'],
                                            'width' => 'halfLong',
                                            'align' => 'right',
                                            'justify' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.branch.left.1.extension-1.return-1.arc-1.label.top.1')
        ->toContain('strang.branch.left.1.extension-1.return-1.bridge.label.bottom.2')
        ->toContain('Return turn')
        ->toContain('Back to trunk')
        ->toContain('solid path')
        ->toContain('w-24')
        ->toContain('w-72')
        ->toContain('items-end text-right')
        ->toContain('text-justify')
        ->toContain('text-amber-700')
        ->not->toContain('halfLong right');
});

it('renders unresolved branch returns as fallback dashed paths by default', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-return-fallback-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-left
                id="sample.left.1.branch"
                bridge-length="8rem"
                :stem-continuation="[
                    1 => ['length' => '4rem', 'force' => true],
                ]"
                :branch-return="[
                    1 => [
                        'attachTo' => 'stem.99',
                        'bridgeLength' => '10rem',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.left.1.branch.return.1')
        ->not->toContain('Fallback anchor used')
        ->toContain('--tw-graph-protocol-local-length: 10rem')
        ->toContain('tw-graph-protocol-primitive-line-dashed')
        ->toContain('tw-graph-protocol-primitive-arc-dashed');
});

it('warns for unresolved branch returns when fallback is explicitly disabled', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-return-fallback-warning-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-left
                id="sample.left.1.branch"
                bridge-length="8rem"
                :stem-continuation="[
                    1 => ['length' => '4rem', 'force' => true],
                ]"
                :branch-return="[
                    1 => [
                        'attachTo' => 'stem.99',
                        'bridgeLength' => '10rem',
                        'fallback' => false,
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.left.1.branch.return.1')
        ->toContain('Fallback anchor used')
        ->toContain('--tw-graph-protocol-local-length: 10rem')
        ->not->toContain('tw-graph-protocol-primitive-line-dashed')
        ->not->toContain('tw-graph-protocol-primitive-arc-dashed');
});

it('derives branch continuation counters from rendered stems without explicit sample counters', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-counter-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-right
                id="sample.right.1.branch"
                bridge-length="8rem"
                :stem-continuation="[
                    1 => ['length' => '4rem', 'force' => true],
                    2 => ['length' => '4rem', 'force' => true],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.branch.right.1.stem-2.anchorNode-end')
        ->toMatch('/title="strang\.branch\.right\.1\.stem-2\.anchorNode-end"[\s\S]*?>\s*5\s*<\/div>/')
        ->not->toMatch('/>\s*E\s*<\/div>/');
});

it('uses graph defaults for branch arc bridge and stem geometry', function (): void {
    config()->set('tw-graph-defaults.arc_size', '4rem');
    config()->set('tw-graph-defaults.bridge_length', '7rem');
    config()->set('tw-graph-defaults.stem_length', '5rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-branch-defaults-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.branch-left
                id="sample.left.1.branch"
                :stem-continuation="[
                    1 => ['force' => true],
                ]"
                :branch-extension="[
                    'stem.1' => [
                        1 => [],
                    ],
                ]"
                :branch-return="[
                    1 => [
                        'attachTo' => 'stem.1',
                        'fallback' => false,
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.branch.left.1.arc-east-north-1')
        ->toContain('strang.branch.left.1.bridge-1')
        ->toContain('strang.branch.left.1.stem-1')
        ->toContain('sample.left.1.branch.extension.1.arc.in')
        ->toContain('sample.left.1.branch.extension.1.bridge')
        ->toContain('sample.left.1.branch.extension.1.stem')
        ->toContain('sample.left.1.branch.return.1.arc.in')
        ->toContain('sample.left.1.branch.return.1.bridge')
        ->toContain('--tw-graph-protocol-local-arc-size: 4rem')
        ->toContain('--tw-graph-protocol-local-length: 7rem')
        ->toContain('--tw-graph-protocol-local-length: 5rem');
});
