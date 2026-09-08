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
                stem-length="4rem"
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
                stem-length="4rem"
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
        ->toContain('strang.merge.right.1.start.label.right.1')
        ->toContain('strang.merge.right.1.stem-1.label.right.1')
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
        ->toContain('strang.merge.left.1.start.label.right.1')
        ->toContain('strang.merge.left.1.start.label.left.2')
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
                stem-length="4rem"
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

it('uses graph defaults for merge arc bridge and stem geometry', function (): void {
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
        ->toContain('strang.merge.left.1.stem-1')
        ->toContain('--tw-graph-protocol-local-arc-size: 4rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('--tw-graph-protocol-local-length: 5rem');
});

it('renders merge extensions with explicit lengths and named label options', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-extension-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                bridge-length="8rem"
                stem-length="4rem"
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

it('passes scalar merge extension labels with shared options through without leaking options', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-merge-extension-scalar-label-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="sample.left.1.merge"
                bridge-length="8rem"
                stem-length="4rem"
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
                stem-length="4rem"
                :extension-count="1"
                extension-bridge-length="7rem"
                extension-stem-length="5rem"
            />
            <x-translation-workbench::ui.tw-graph.strang.merge-right
                id="sample.right.1.merge"
                bridge-length="8rem"
                stem-length="4rem"
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
