<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('passes named merge node label options and explicit label colors through', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                bridge-length="8rem"
                :stem-lengths="[1 => '4rem']"
                :start-label="[
                    'text' => ['Merge source', '2026-04-14 11:36'],
                    'width' => 'halfLong',
                    'align' => 'right',
                    'color' => 'red',
                ]"
                :node-labels="[
                    1 => [
                        'right' => ['text' => ['Origin key', 'admin.buttons.save']],
                        'width' => 'long',
                        'align' => 'left',
                    ],
                    2 => [
                        'right' => [
                            'text' => ['Literal', 'Save'],
                            'width' => 'half',
                            'align' => 'right',
                            'justify' => true,
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.start.label.bottom.1')
        ->toContain('strang.merge.left.1.start.label.right.1')
        ->toContain('strang.merge.left.1.stem-1.label.right.1')
        ->toContain('Merge source')
        ->toContain('2026-04-14 11:36')
        ->toContain('Origin key')
        ->toContain('admin.buttons.save')
        ->toContain('Literal')
        ->toContain('Save')
        ->toContain('w-96')
        ->toContain('w-72')
        ->toContain('w-24')
        ->toContain('items-start text-left')
        ->toContain('items-end text-right')
        ->toContain('text-justify')
        ->toContain('text-red-700')
        ->toContain('text-green-800');
});

it('passes right merge scalar side labels through without leaking shared formatting options', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-right-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-right
                id="sample.right.1.merge"
                bridge-length="8rem"
                :stem-lengths="[1 => '4rem']"
                :node-labels="[
                    1 => [
                        'left' => 'Literal|Save',
                        'width' => 'halfLong',
                        'align' => 'right',
                        'justify' => true,
                    ],
                    2 => [
                        'left' => [
                            'text' => ['Origin key', 'admin.buttons.save'],
                            'width' => 'long',
                            'align' => 'left',
                            'color' => 'amber',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.right.1.start.label.left.1')
        ->toContain('strang.merge.right.1.stem-1.label.left.1')
        ->toContain('Literal')
        ->toContain('Save')
        ->toContain('Origin key')
        ->toContain('admin.buttons.save')
        ->toContain('w-72')
        ->toContain('w-96')
        ->toContain('items-end text-right')
        ->toContain('items-start text-left')
        ->toContain('text-justify')
        ->toContain('text-green-800')
        ->toContain('text-amber-700');
});

it('keeps merge node label shared options out of both side label texts', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-two-sided-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                :node-labels="[
                    1 => [
                        'width' => 'halfLong',
                        'align' => 'right',
                        'justify' => true,
                        'left' => [
                            'text' => 'First seen|2026-04-14 11:36',
                        ],
                        'right' => [
                            'text' => 'Literal|Save',
                            'align' => 'left',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.start.label.left.1')
        ->toContain('strang.merge.left.1.start.label.right.2')
        ->toContain('First seen')
        ->toContain('2026-04-14 11:36')
        ->toContain('Literal')
        ->toContain('Save')
        ->toContain('w-72')
        ->toContain('text-justify')
        ->not->toContain('halfLong');
});

it('renders merge stem continuations with compressed geometry', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-compressed-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-right
                id="sample.right.1.merge"
                bridge-length="8rem"
                :stem-lengths="[1 => '4rem']"
                :stem-continuation="[
                    1 => [
                        'length' => '12rem',
                        'compressed' => true,
                        'beforeLength' => '3rem',
                        'gapLength' => '6rem',
                        'afterLength' => '3rem',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.right.1.stem-2')
        ->toContain('strang.merge.right.1.stem-2.stem')
        ->toContain('strang.merge.right.1.stem-2.stem.dotted')
        ->toContain('tw-graph-protocol-primitive-line-dashed');
});

it('uses graph defaults for merge arc and bridge geometry without auto-rendering a stem', function (): void {
    config()->set('tw-graph-defaults.arc_size', '4rem');
    config()->set('tw-graph-defaults.bridge_length', '6rem');
    config()->set('tw-graph-defaults.stem_length', '5rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-defaults-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.arc-west-north-1')
        ->toContain('strang.merge.left.1.bridge')
        ->not->toContain('strang.merge.left.1.stem-1')
        ->toContain('--tw-graph-protocol-local-arc-size: 4rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->not->toContain('--tw-graph-protocol-local-length: 5rem');
});

it('keeps merge start shift out of default rendering', function (): void {
    config()->set('tw-graph-defaults.merge_start_shift_enabled', false);
    config()->set('tw-graph-defaults.merge_start_shift_length', '10rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-default-start-shift-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                start-length="1rem"
                :stem-lengths="[1 => '3rem']"
                bridge-length="5rem"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.start')
        ->toContain('strang.merge.left.1.stem-1')
        ->toContain('--tw-graph-protocol-local-length: 1rem')
        ->toContain('--tw-graph-protocol-local-length: 3rem')
        ->not->toContain('strang.merge.left.1.start-shift')
        ->not->toContain('--tw-graph-protocol-local-length: 10rem');
});

it('renders one merge stem for each explicit stem length entry', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-multiple-stem-lengths-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                start-length="1rem"
                :stem-lengths="[1 => '3rem', 2 => '4rem']"
                bridge-length="5rem"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.stem-1')
        ->toContain('strang.merge.left.1.stem-2')
        ->toContain('--tw-graph-protocol-local-length: 3rem')
        ->toContain('--tw-graph-protocol-local-length: 4rem');
});

it('renders a joint arrow instead of a merge stem dot when the stem has no labels', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-stem-joint-arrow-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                :stem-lengths="[1 => '3rem']"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.stem-1.end.joint-arrow')
        ->toContain('tw-graph-protocol-primitive-joint-arrow-top')
        ->toContain('strang.merge.left.1.stem-1.anchorNode-end');
});

it('keeps the merge stem dot when the stem has a label', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-stem-label-dot-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                :stem-lengths="[1 => '3rem']"
                :node-labels="[
                    2 => ['right' => 'Stem label'],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('Stem label')
        ->toContain('strang.merge.left.1.stem-1.label.right.1')
        ->not->toContain('strang.merge.left.1.stem-1.end.joint-arrow');
});

it('renders explicit merge start shift as its own segment before the first stem', function (): void {
    config()->set('tw-graph-defaults.merge_start_shift_enabled', false);
    config()->set('tw-graph-defaults.merge_start_shift_length', '10rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-explicit-start-shift-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                start-length="1rem"
                :start-shift-enabled="true"
                start-shift-length="6rem"
                :stem-lengths="[1 => '3rem']"
                bridge-length="5rem"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.start')
        ->toContain('strang.merge.left.1.start-shift')
        ->toContain('strang.merge.left.1.stem-1')
        ->toContain('--tw-graph-protocol-local-length: 1rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('--tw-graph-protocol-local-length: 3rem');
});

it('renders a joint arrow instead of the merge start dot when start shift is visible', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-start-shift-joint-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                start-length="1rem"
                :start-shift-enabled="true"
                start-shift-length="6rem"
                :stem-lengths="[1 => '3rem']"
                bridge-length="5rem"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.start.start-shift.joint-arrow')
        ->toContain('tw-graph-protocol-primitive-joint-arrow-top')
        ->toContain('tw-graph-protocol-primitive-line-node-end"');
});

it('renders merge start node labels at the start shift end anchor', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-start-shift-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                start-length="1rem"
                :start-shift-enabled="true"
                start-shift-length="6rem"
                bridge-length="5rem"
                :node-labels="[
                    1 => ['right' => 'start shift end label'],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.start.start-shift.joint-arrow')
        ->toContain('start shift end label')
        ->toContain('strang.merge.left.1.start-shift.label.right.1');
});

it('keeps merge start node labels on the visible start dot without start shift', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-start-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                start-length="1rem"
                start-shift-length="0.5rem"
                bridge-length="5rem"
                :node-labels="[
                    1 => ['right' => 'visible start dot label'],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('visible start dot label')
        ->not->toContain('strang.merge.left.1.start-shift')
        ->not->toContain('strang.merge.left.1.start.start-shift.joint-arrow');
});

it('shows a dev mismatch badge when merge node labels target non-existing anchors', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-node-label-mismatch-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                :stem-lengths="[1 => '3rem']"
                :node-labels="[
                    2 => ['right' => 'Existing stem label'],
                    9 => ['right' => 'Ignored overflow label'],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('Existing stem label')
        ->not->toContain('Ignored overflow label')
        ->toContain('nodeLabel-Mismatch')
        ->toContain('labels: 2')
        ->toContain('anchors: 3')
        ->toContain('ignored: 1')
        ->toContain('ignored nodes: 9');
});

it('keeps merge node label mismatch quiet when labels fit existing anchors', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-node-label-fit-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                :stem-lengths="[1 => '3rem']"
                :node-labels="[
                    2 => ['right' => 'Existing stem label'],
                    3 => ['left' => 'Existing attach label'],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('Existing stem label')
        ->toContain('Existing attach label')
        ->not->toContain('nodeLabel-Mismatch');
});

it('does not render merge node labels on arc and bridge intermediate anchors', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-intermediate-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                :stem-lengths="[1 => '3rem']"
                :node-labels="[
                    2 => ['right' => 'Existing stem label'],
                    4 => ['left' => 'Ignored intermediate label'],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('Existing stem label')
        ->not->toContain('Ignored intermediate label')
        ->toContain('nodeLabel-Mismatch')
        ->toContain('ignored nodes: 4');
});

it('renders the final merge end label through the last semantic node label anchor', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-end-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                :stem-lengths="[1 => '3rem', 2 => '3rem']"
                :node-labels="[
                    4 => ['left' => 'Final merge end label'],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('Final merge end label')
        ->toContain('strang.merge.left.1.end.label.left.1')
        ->not->toContain('nodeLabel-Mismatch');
});

it('renders explicit merge end labels independent from the numeric node count', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-explicit-end-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                :stem-lengths="[1 => '3rem']"
                :node-labels="[
                    9 => ['right' => 'Ignored overflow label'],
                    'end' => [
                        'left' => [
                            'text' => 'Explicit merge end left',
                            'align' => 'right',
                        ],
                        'right' => [
                            'text' => 'Explicit merge end right',
                            'align' => 'left',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('Explicit merge end left')
        ->toContain('Explicit merge end right')
        ->toContain('strang.merge.left.1.end.label.left.1')
        ->toContain('strang.merge.left.1.end.label.right.2')
        ->not->toContain('Ignored overflow label')
        ->toContain('nodeLabel-Mismatch')
        ->toContain('ignored nodes: 9');
});

it('lets explicit merge end labels win over numeric merge end labels in dev mode', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-end-label-override-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                :stem-lengths="[1 => '3rem']"
                :node-labels="[
                    3 => ['left' => 'Numeric merge end label'],
                    'end' => ['left' => 'Explicit merge end label'],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('Explicit merge end label')
        ->not->toContain('Numeric merge end label')
        ->toContain('nodeLabel-EndOverride')
        ->toContain('end wins')
        ->toContain('numeric: 3');
});

it('renders merge extensions with explicit lengths and named label options', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-extension-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                bridge-length="8rem"
                :stem-lengths="[1 => '4rem']"
                :extension-count="1"
                extension-bridge-length="9rem"
                extension-stem-length="5rem"
                :extension-stem-continuations="[
                    1 => [
                        1 => ['length' => '6rem'],
                    ],
                ]"
                :extension-node-labels="[
                    1 => [
                        'start' => [
                            'text' => ['Merge extension', '2026-04-14 11:36'],
                            'width' => 'half',
                            'align' => 'center',
                        ],
                        1 => [
                            'right' => [
                                'text' => ['Origin key', 'admin.buttons.save'],
                                'width' => 'long',
                                'align' => 'left',
                            ],
                        ],
                        3 => [
                            'right' => [
                                'text' => ['Extension stem', 'keeps options'],
                                'width' => 'halfLong',
                                'align' => 'left',
                                'justify' => true,
                                'color' => 'amber',
                            ],
                        ],
                        4 => [
                            'top' => [
                                'text' => ['Attached to merge'],
                                'width' => 'default',
                            ],
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.extension-1.start')
        ->toContain('strang.merge.left.1.extension-1.stem-1')
        ->toContain('strang.merge.left.1.extension-1.stem-2')
        ->toContain('strang.merge.left.1.extension-1.bridge')
        ->toContain('Merge extension')
        ->toContain('2026-04-14 11:36')
        ->toContain('Origin key')
        ->toContain('admin.buttons.save')
        ->toContain('Extension stem')
        ->toContain('keeps options')
        ->toContain('Attached to merge')
        ->toContain('--tw-graph-protocol-local-length: 9rem')
        ->toContain('--tw-graph-protocol-local-length: 5rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('w-24')
        ->toContain('w-72')
        ->toContain('w-96')
        ->toContain('items-start text-left')
        ->toContain('text-justify')
        ->toContain('text-amber-700');
});

it('renders explicit merge extension start shift as its own segment', function (): void {
    config()->set('tw-graph-defaults.merge_extension_start_shift_enabled', false);
    config()->set('tw-graph-defaults.merge_extension_start_shift_length', '10rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-extension-start-shift-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                start-length="1rem"
                :stem-lengths="[1 => '3rem']"
                bridge-length="5rem"
                :extension-count="1"
                extension-start-length="2rem"
                :extension-start-shift-enabled="true"
                extension-start-shift-length="7rem"
                extension-stem-length="4rem"
                extension-bridge-length="6rem"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.extension-1.start')
        ->toContain('strang.merge.left.1.extension-1.start-shift')
        ->toContain('strang.merge.left.1.extension-1.stem-1')
        ->toContain('--tw-graph-protocol-local-length: 2rem')
        ->toContain('--tw-graph-protocol-local-length: 7rem')
        ->toContain('--tw-graph-protocol-local-length: 4rem');
});

it('renders a joint arrow instead of the merge extension start dot when start shift is visible', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-extension-start-shift-joint-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                start-length="1rem"
                :stem-lengths="[1 => '3rem']"
                bridge-length="5rem"
                :extension-count="1"
                extension-start-length="2rem"
                :extension-start-shift-enabled="true"
                extension-start-shift-length="7rem"
                extension-stem-length="4rem"
                extension-bridge-length="6rem"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.extension-1.start.start-shift.joint-arrow')
        ->toContain('tw-graph-protocol-primitive-joint-arrow-top');
});

it('renders canonical merge extension continuation labels separately from geometry', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-extension-canonical-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                :extension-count="1"
                :extension-stem-continuations="[
                    1 => [
                        1 => ['length' => '4rem'],
                        2 => ['length' => '5rem'],
                    ],
                ]"
                :extension-node-labels="[
                    1 => [
                        3 => [
                            'labels' => [
                                'left' => [
                                    'text' => ['Finding ID #42', 'archive note'],
                                    'align' => 'right',
                                ],
                            ],
                            'width' => 'halfLong',
                            'color' => 'amber',
                        ],
                        4 => [
                            'labels' => [
                                'right' => [
                                    'text' => ['Finding ID #43', 'review note'],
                                    'align' => 'left',
                                ],
                            ],
                            'width' => 'halfLong',
                            'color' => 'sky',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.extension-1.stem-2.label.left.1')
        ->toContain('strang.merge.left.1.extension-1.stem-3.label.right.1')
        ->toContain('Finding ID #42')
        ->toContain('archive note')
        ->toContain('Finding ID #43')
        ->toContain('review note')
        ->toContain('w-72')
        ->toContain('text-amber-700')
        ->toContain('text-sky-800')
        ->not->toContain('compressed')
        ->not->toContain('htmlspecialchars');
});

it('passes scalar merge extension labels with shared options through without leaking options', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-extension-scalar-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                bridge-length="8rem"
                :stem-lengths="[1 => '4rem']"
                :extension-count="1"
                extension-bridge-length="9rem"
                extension-stem-length="5rem"
                :extension-node-labels="[
                    1 => [
                        1 => [
                            'right' => 'merged into|shared key ID #124',
                            'width' => 'halfLong',
                            'align' => 'left',
                            'justify' => true,
                            'color' => 'sky',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.extension-1.start.label.right.1')
        ->toContain('merged into')
        ->toContain('shared key ID #124')
        ->toContain('w-72')
        ->toContain('items-start text-left')
        ->toContain('text-justify')
        ->toContain('text-sky-800')
        ->not->toContain('halfLong')
        ->not->toContain('htmlspecialchars');
});

it('renders independent left and right merge extensions without mirrored length assumptions', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-extension-side-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                bridge-length="8rem"
                :stem-lengths="[1 => '4rem']"
                :extension-count="1"
                extension-bridge-length="7rem"
                extension-stem-length="5rem"
            />
            <x-translation-workbench::ui.tw-graph.strang.merge-right
                id="sample.right.1.merge"
                bridge-length="8rem"
                :stem-lengths="[1 => '4rem']"
                :extension-count="1"
                extension-bridge-length="13rem"
                extension-stem-length="9rem"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.merge.left.1.extension-1.bridge')
        ->toContain('strang.merge.right.1.extension-1.bridge')
        ->toContain('--tw-graph-protocol-local-length: 7rem')
        ->toContain('--tw-graph-protocol-local-length: 13rem')
        ->toContain('--tw-graph-protocol-local-length: 5rem')
        ->toContain('--tw-graph-protocol-local-length: 9rem')
        ->toContain('tw-graph-protocol-primitive-line-left-right')
        ->toContain('tw-graph-protocol-primitive-line-right-left');
});
