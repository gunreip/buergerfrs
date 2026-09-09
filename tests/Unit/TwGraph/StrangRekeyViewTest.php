<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('renders rekey source with merge path semantics and named label options', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-source-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-source-left
                id="sample.left.1.rekey-source"
                bridge-length="9rem"
                stem-length="4rem"
                :start-label="[
                    'text' => ['rekey source from ID #41', '2026-04-14 11:36'],
                    'width' => 'halfLong',
                    'align' => 'right',
                ]"
                :node-labels="[
                    1 => [
                        'right' => [
                            'text' => ['Origin key', 'admin.buttons.save'],
                            'width' => 'long',
                            'align' => 'left',
                        ],
                    ],
                    2 => [
                        'right' => [
                            'text' => ['Literal', 'Save'],
                            'width' => 'half',
                            'align' => 'right',
                        ],
                    ],
                    3 => [
                        'left' => [
                            'text' => ['rekeyed into this key ID #124'],
                            'width' => 'halfLong',
                            'align' => 'right',
                            'color' => 'sky',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.rekey.left.source.1.start.label.bottom.1')
        ->toContain('strang.rekey.left.source.1.start.label.right.1')
        ->toContain('strang.rekey.left.source.1.stem-1.label.right.1')
        ->toContain('strang.rekey.left.source.1.end.label.left.1')
        ->toContain('rekey source from ID #41')
        ->toContain('Origin key')
        ->toContain('admin.buttons.save')
        ->toContain('Literal')
        ->toContain('Save')
        ->toContain('rekeyed into this key ID #124')
        ->toContain('w-96')
        ->toContain('w-72')
        ->toContain('w-24')
        ->toContain('items-start text-left')
        ->toContain('items-end text-right')
        ->toContain('text-sky-800');
});

it('renders rekey target with branch path semantics stem labels and a terminal end label', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-target-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-target-right
                id="sample.right.1.rekey-target"
                bridge-length="10rem"
                stem-length="4rem"
                end-length="3rem"
                :stem-continuation="[
                    1 => [
                        'length' => '4rem',
                        'right' => [
                            'text' => ['Target key', 'ui.button.save.save'],
                            'width' => 'long',
                            'align' => 'left',
                        ],
                        'left' => [
                            'text' => ['Source', 'lang/de/ui.php'],
                            'width' => 'halfLong',
                            'align' => 'right',
                        ],
                    ],
                ]"
                :end-label="[
                    'text' => ['rekey target to ID #124', '2026-04-15 08:12'],
                    'width' => 'long',
                    'align' => 'center',
                    'color' => 'amber',
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.rekey.right.target.1.arc-west-north-1')
        ->toContain('strang.rekey.right.target.1.bridge')
        ->toContain('strang.rekey.right.target.1.stem-1.label.left.1')
        ->toContain('strang.rekey.right.target.1.stem-1.label.right.2')
        ->toContain('strang.rekey.right.target.1.end.label.top.1')
        ->toContain('Target key')
        ->toContain('ui.button.save.save')
        ->toContain('Source')
        ->toContain('lang/de/ui.php')
        ->toContain('rekey target to ID #124')
        ->toContain('2026-04-15 08:12')
        ->toContain('w-96')
        ->toContain('w-72')
        ->toContain('items-start text-left')
        ->toContain('items-end text-right')
        ->toContain('text-amber-700');
});

it('passes scalar rekey source labels with shared options through without leaking options', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-source-scalar-options-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-source-left
                id="sample.left.1.rekey-source"
                bridge-length="9rem"
                stem-length="4rem"
                :node-labels="[
                    3 => [
                        'left' => 'rekeyed into this key ID #124|2026-04-15 08:12',
                        'width' => 'halfLong',
                        'align' => 'right',
                        'justify' => true,
                        'color' => 'rose',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.rekey.left.source.1.end.label.left.1')
        ->toContain('rekeyed into this key ID #124')
        ->toContain('2026-04-15 08:12')
        ->toContain('w-72')
        ->toContain('items-end text-right')
        ->toContain('text-justify')
        ->toContain('style="text-align: justify;"')
        ->toContain('text-rose-700')
        ->not->toContain('halfLong')
        ->not->toContain('htmlspecialchars');
});

it('renders explicit rekey source end labels through the merge path end alias', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-source-end-alias-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-source-left
                id="sample.left.1.rekey-source"
                bridge-length="9rem"
                stem-length="4rem"
                :node-labels="[
                    9 => ['right' => 'Ignored overflow label'],
                    'end' => [
                        'left' => [
                            'text' => 'Explicit rekey source end left',
                            'align' => 'right',
                        ],
                        'right' => [
                            'text' => 'Explicit rekey source end right',
                            'align' => 'left',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('Explicit rekey source end left')
        ->toContain('Explicit rekey source end right')
        ->toContain('strang.rekey.left.source.1.end.label.left.1')
        ->toContain('strang.rekey.left.source.1.end.label.right.2')
        ->not->toContain('Ignored overflow label')
        ->toContain('nodeLabel-Mismatch')
        ->toContain('ignored nodes: 9');
});

it('lets explicit rekey source end labels win over numeric end labels in dev mode', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-source-end-override-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-source-left
                id="sample.left.1.rekey-source"
                bridge-length="9rem"
                stem-length="4rem"
                :node-labels="[
                    3 => ['left' => 'Numeric rekey source end label'],
                    'end' => ['left' => 'Explicit rekey source end label'],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('Explicit rekey source end label')
        ->not->toContain('Numeric rekey source end label')
        ->toContain('nodeLabel-EndOverride')
        ->toContain('end wins')
        ->toContain('numeric: 3');
});

it('passes shared scalar stem label options through rekey target continuations', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-target-scalar-options-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-target-right
                id="sample.right.1.rekey-target"
                bridge-length="10rem"
                stem-length="4rem"
                :stem-continuation="[
                    1 => [
                        'right' => 'merged into|shared key ID #124',
                        'width' => 'halfLong',
                        'align' => 'left',
                        'justify' => true,
                        'color' => 'sky',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.rekey.right.target.1.stem-1.label.right.1')
        ->toContain('merged into')
        ->toContain('shared key ID #124')
        ->toContain('w-72')
        ->toContain('items-start text-left')
        ->toContain('text-justify')
        ->toContain('style="text-align: justify;"')
        ->toContain('text-sky-800')
        ->not->toContain('halfLong')
        ->not->toContain('htmlspecialchars');
});

it('inherits graph color for rekey labels unless the label sets its own color', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-color-inheritance-test" color="violet" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-target-right
                id="sample.right.1.rekey-target"
                bridge-length="10rem"
                stem-length="4rem"
                :stem-continuation="[
                    1 => [
                        'length' => '4rem',
                        'right' => [
                            'text' => ['Inherited rekey color'],
                            'width' => 'default',
                            'align' => 'left',
                        ],
                        'left' => [
                            'text' => ['Local rekey label color'],
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
        ->toContain('Inherited rekey color')
        ->toContain('Local rekey label color')
        ->toContain('text-violet-700')
        ->toContain('text-amber-700');
});

it('renders compressed rekey source stem continuations', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-source-compressed-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-source-right
                id="sample.right.1.rekey-source"
                bridge-length="9rem"
                stem-length="4rem"
                :stem-continuation="[
                    1 => [
                        'length' => '10rem',
                        'compressed' => true,
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.rekey.right.source.1.stem-2')
        ->toContain('strang.rekey.right.source.1.stem-2.stem')
        ->toContain('strang.rekey.right.source.1.stem-2.stem.dotted')
        ->toContain('--tw-graph-protocol-local-length: calc(')
        ->toContain('tw-graph-protocol-primitive-line-dashed');
});

it('uses graph defaults for rekey source arc bridge and stem geometry', function (): void {
    config()->set('tw-graph-defaults.arc_size', '4rem');
    config()->set('tw-graph-defaults.bridge_length', '6rem');
    config()->set('tw-graph-defaults.stem_length', '5rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-source-defaults-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-source-left
                id="sample.left.1.rekey-source"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.rekey.left.source.1.arc-west-north-1')
        ->toContain('strang.rekey.left.source.1.bridge')
        ->toContain('strang.rekey.left.source.1.stem-1')
        ->toContain('--tw-graph-protocol-local-arc-size: 4rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('--tw-graph-protocol-local-length: 5rem');
});

it('keeps rekey source start shift out of default rendering', function (): void {
    config()->set('tw-graph-defaults.rekey_source_start_shift_enabled', false);
    config()->set('tw-graph-defaults.rekey_source_start_shift_length', '10rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-source-default-start-shift-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-source-left
                id="sample.left.1.rekey-source"
                start-length="1rem"
                stem-length="3rem"
                bridge-length="5rem"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.rekey.left.source.1.start')
        ->toContain('strang.rekey.left.source.1.stem-1')
        ->toContain('--tw-graph-protocol-local-length: 1rem')
        ->toContain('--tw-graph-protocol-local-length: 3rem')
        ->not->toContain('strang.rekey.left.source.1.start-shift')
        ->not->toContain('--tw-graph-protocol-local-length: 10rem');
});

it('renders explicit rekey source start shift as its own segment before the first stem', function (): void {
    config()->set('tw-graph-defaults.rekey_source_start_shift_enabled', false);
    config()->set('tw-graph-defaults.rekey_source_start_shift_length', '10rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-source-explicit-start-shift-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-source-right
                id="sample.right.1.rekey-source"
                start-length="1rem"
                :start-shift-enabled="true"
                start-shift-length="6rem"
                stem-length="3rem"
                bridge-length="5rem"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.rekey.right.source.1.start')
        ->toContain('strang.rekey.right.source.1.start-shift')
        ->toContain('strang.rekey.right.source.1.stem-1')
        ->toContain('--tw-graph-protocol-local-length: 1rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('--tw-graph-protocol-local-length: 3rem');
});

it('renders compressed rekey target stem continuations with named labels', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-target-compressed-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-target-left
                id="sample.left.1.rekey-target"
                bridge-length="9rem"
                stem-length="4rem"
                :stem-continuation="[
                    1 => [
                        'length' => '12rem',
                        'compressed' => true,
                        'beforeLength' => '3rem',
                        'gapLength' => '6rem',
                        'afterLength' => '3rem',
                        'left' => [
                            'text' => ['Target continues', 'as key ID #124'],
                            'width' => 'halfLong',
                            'align' => 'right',
                            'color' => 'amber',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.rekey.left.target.1.stem-1.stem')
        ->toContain('strang.rekey.left.target.1.stem-1.stem.dotted')
        ->toContain('strang.rekey.left.target.1.stem-1.stem.label.left.1')
        ->toContain('Target continues')
        ->toContain('as key ID #124')
        ->toContain('w-72')
        ->toContain('items-end text-right')
        ->toContain('text-amber-700')
        ->toContain('tw-graph-protocol-primitive-line-dashed');
});

it('uses graph defaults for rekey target arc bridge and stem geometry', function (): void {
    config()->set('tw-graph-defaults.arc_size', '4rem');
    config()->set('tw-graph-defaults.bridge_length', '6rem');
    config()->set('tw-graph-defaults.stem_length', '5rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-target-defaults-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-target-right
                id="sample.right.1.rekey-target"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.rekey.right.target.1.arc-west-north-1')
        ->toContain('strang.rekey.right.target.1.bridge')
        ->toContain('strang.rekey.right.target.1.stem-1')
        ->toContain('--tw-graph-protocol-local-arc-size: 4rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('--tw-graph-protocol-local-length: 5rem');
});

it('keeps rekey target continuation stems and end segment in the same branch chain', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="strang-rekey-target-continuation-test" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.rekey-target-right
                id="sample.right.1.rekey-target"
                bridge-length="10rem"
                stem-length="4rem"
                end-length="3rem"
                :stem-continuation="[
                    1 => [
                        'length' => '5rem',
                        'left' => [
                            'text' => ['Source', 'lang/de/ui.php', 'source lang value ID #801'],
                            'width' => 'halfLong',
                            'align' => 'right',
                        ],
                    ],
                    2 => [
                        'length' => '6rem',
                        'right' => [
                            'text' => ['Target key', 'ui.button.save.save'],
                            'width' => 'long',
                            'align' => 'left',
                        ],
                    ],
                ]"
                :end-label="[
                    'text' => ['rekey target to ID #124', '2026-04-15 08:12'],
                    'width' => 'long',
                    'align' => 'center',
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('strang.rekey.right.target.1.stem-1.label.left.1')
        ->toContain('strang.rekey.right.target.1.stem-2.label.right.1')
        ->toContain('strang.rekey.right.target.1.end.label.top.1')
        ->toContain('--tw-graph-protocol-local-length: 5rem')
        ->toContain('--tw-graph-protocol-local-length: 6rem')
        ->toContain('--tw-graph-protocol-local-length: 3rem')
        ->toContain('Source')
        ->toContain('source lang value ID #801')
        ->toContain('Target key')
        ->toContain('ui.button.save.save')
        ->toContain('rekey target to ID #124')
        ->toContain('2026-04-15 08:12')
        ->toContain('w-72')
        ->toContain('w-96');
});
