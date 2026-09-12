<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('passes width alignment and justify options through parts start labels', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="parts-start-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.parts.start
                id="sample.center.1.start"
                :anchor-start="['x' => '0rem', 'y' => '0rem']"
                :node-label-right="[
                    'text' => ['Right aligned', 'justified label'],
                    'width' => 'halfLong',
                    'align' => 'right',
                    'justify' => true,
                    'color' => 'amber',
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.center.1.start.label.right.1')
        ->toContain('Right aligned')
        ->toContain('justified label')
        ->toContain('w-72')
        ->toContain('items-end text-right')
        ->toContain('text-justify')
        ->toContain('text-align: justify;')
        ->toContain('text-amber-700');
});

it('lets flow start inherit graph color unless it overrides it', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="flow-start-color-test" color="cyan" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.flow-start
                id="sample.flow.1.start"
                :start-label="[
                    'text' => ['Inherited color'],
                    'width' => 'default',
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('--tw-graph-protocol-local-color-rgb: 6 182 212')
        ->toContain('text-cyan-800')
        ->not->toContain('--tw-graph-protocol-local-color-rgb: 113 113 122');
});

it('passes half width labels and extension geometry through parts sideways', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="parts-sideways-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.parts.sideways
                id="sample.left.1.sideways"
                side="left"
                extension="3rem"
                :anchor-start="['x' => '0rem', 'y' => '0rem']"
                :node-label-left="[
                    'text' => ['Compact', 'side note'],
                    'width' => 'half',
                    'align' => 'left',
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.left.1.sideways.extension-stem1')
        ->toContain('sample.left.1.sideways.extension-stem2')
        ->toContain('sample.left.1.sideways.anchorNode-end.label-2')
        ->toContain('Compact')
        ->toContain('side note')
        ->toContain('w-24')
        ->toContain('items-start text-left')
        ->toContain('text-align: left;');
});

it('renders right sideways parts with mirrored arc names labels and joint arrows', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="parts-sideways-right-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.parts.sideways
                id="sample.right.1.sideways"
                side="right"
                bridge-length="7rem"
                :anchor-start="['x' => '0rem', 'y' => '0rem']"
                :node-label-right="[
                    'text' => ['Right side'],
                    'width' => 'long',
                    'align' => 'left',
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.right.1.sideways.arc1-east-north')
        ->toContain('sample.right.1.sideways.bridge1')
        ->toContain('sample.right.1.sideways.arc2-south-west')
        ->toContain('sample.right.1.sideways.arc-in.bridge.joint-arrow')
        ->toContain('sample.right.1.sideways.bridge.arc-out.joint-arrow')
        ->toContain('tw-graph-protocol-primitive-joint-arrow-left')
        ->toContain('sample.right.1.sideways.anchorNode-end.label-1')
        ->toContain('Right side')
        ->toContain('w-96')
        ->toContain('items-start text-left');
});

it('renders label bridge segments with centered labels and bridge joint arrows', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="label-bridge-segment-test" :dev="true" :coordinates="false" color="cyan">
            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                id="sample.flow.1.label-bridge"
                :label="['text' => ['Inline label'], 'width' => 'half', 'align' => 'center']"
                :anchor-start="['x' => '0rem', 'y' => '0rem']"
                direction="left-right"
                bridge-length="0.1rem"
                color="cyan"
                z-index="20"
                :dev="true"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.flow.1.label-bridge.bridge-in')
        ->toContain('sample.flow.1.label-bridge.label.center.1')
        ->toContain('sample.flow.1.label-bridge.label.center.1.mask')
        ->toContain('sample.flow.1.label-bridge.bridge-out')
        ->toContain('sample.flow.1.label-bridge.bridge-out.end.joint-arrow')
        ->toContain('Inline label')
        ->toContain('tw-graph-protocol-label-bridge-mask')
        ->toContain('--tw-graph-protocol-z-index: 21')
        ->toContain('w-24')
        ->toContain('--tw-graph-protocol-z-index: 22')
        ->toContain('--tw-graph-protocol-local-length: 0.25rem')
        ->not->toContain('--tw-graph-protocol-local-length: 0.1rem');
});

it('lets flow steps attach to registered flow start anchors', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="flow-step-attach-test" :dev="true" :coordinates="false" color="cyan">
            <x-translation-workbench::ui.tw-graph.strang.flow-start
                id="sample.flow.1.process"
                start-length="7rem"
                :node-end-dot="false"
            />

            <x-translation-workbench::ui.tw-graph.strang.flow-step
                id="sample.flow.1.process.step-1"
                attach-to="sample.flow.1.process.anchorNode-end"
                before-length="2rem"
                after-length="2rem"
                :step-label="['text' => ['Attached step']]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.flow.1.process.step-1.stem.before')
        ->toContain('--tw-graph-protocol-start-y: calc(0rem + 7rem)')
        ->toContain('Attached step');
});

it('lets flow decisions and branch steps attach to registered flow anchors', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="flow-decision-attach-test" :dev="true" :coordinates="false" color="cyan">
            <x-translation-workbench::ui.tw-graph.strang.flow-start
                id="sample.flow.1.process"
                start-length="7rem"
                :node-end-dot="false"
            />

            <x-translation-workbench::ui.tw-graph.strang.flow-step
                id="sample.flow.1.process.step-1"
                attach-to="sample.flow.1.process.anchorNode-end"
                before-length="2rem"
                after-length="2rem"
                :step-label="['text' => ['Attached step']]"
            />

            <x-translation-workbench::ui.tw-graph.strang.flow-decision
                id="sample.flow.1.process.decision-1"
                attach-to="sample.flow.1.process.step-1.anchorNode-end"
                bridge-length="8rem"
                :decision-label="['text' => ['Decision']]"
            />

            <x-translation-workbench::ui.tw-graph.strang.flow-step
                id="sample.flow.1.process.right-step"
                attach-to="sample.flow.1.process.decision-1.right.anchorNode-end"
                before-length="1rem"
                after-length="1rem"
                :step-label="['text' => ['Right branch']]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.flow.1.process.decision-1.anchorNode-decision')
        ->toContain('sample.flow.1.process.decision-1.right.bridge1')
        ->toContain('--tw-graph-protocol-local-length: 8rem')
        ->toContain('sample.flow.1.process.right-step.stem.before')
        ->toContain('Right branch');
});

it('keeps flow elseif group width alignment quiet by normalizing condition labels', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="flow-if-elseif-group-width-test" :dev="true" :coordinates="false" color="cyan">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-group
                id="sample.flow.1.elseif"
                :conditions="[
                    [
                        'key' => '1',
                        'label' => [
                            'text' => ['ELSEIF compact'],
                            'width' => 'half',
                        ],
                    ],
                    [
                        'key' => '2',
                        'label' => [
                            'text' => ['ELSEIF long'],
                            'width' => 'long',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.flow.1.elseif.1.label.center.1')
        ->toContain('sample.flow.1.elseif.2.label.center.1')
        ->toContain('w-96')
        ->not->toContain('conditionWidth-Mismatch');
});

it('normalizes flow if and elseif condition widths in one coordinated condition set', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="flow-if-condition-set-width-test" :dev="true" :coordinates="false" color="cyan">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-condition-set
                id="sample.flow.1.conditions"
                if-id="sample.flow.1.if"
                elseif-id="sample.flow.1.elseif"
                :if-condition-label="[
                    'text' => ['IF compact'],
                    'width' => 'half',
                ]"
                :elseif-conditions="[
                    [
                        'key' => '1',
                        'label' => [
                            'text' => ['ELSEIF long'],
                            'width' => 'long',
                        ],
                    ],
                ]"
            />

            <x-translation-workbench::ui.tw-graph.strang.flow-if-end
                id="sample.flow.1.endif"
                attach-to="sample.flow.1.conditions.then.anchorNode-end"
                bridge-length="0.75rem"
                :end-label="['text' => ['ENDIF'], 'width' => 'half']"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.flow.1.if.bridge-in')
        ->toContain('sample.flow.1.if.bridge-out')
        ->toContain('sample.flow.1.if.bridge-out.end.joint-arrow')
        ->toContain('sample.flow.1.if.label.center.1')
        ->toContain('sample.flow.1.elseif.1.bridge-in')
        ->toContain('sample.flow.1.elseif.1.bridge-out')
        ->toContain('sample.flow.1.elseif.1.bridge-out.end.joint-arrow')
        ->toContain('sample.flow.1.elseif.1.label.center.1')
        ->toContain('sample.flow.1.endif.bridge-in')
        ->toContain('sample.flow.1.endif.bridge-out')
        ->toContain('sample.flow.1.endif.bridge-out.end.joint-arrow')
        ->toContain('sample.flow.1.endif.label.center.1')
        ->toContain('ENDIF')
        ->toContain('w-96')
        ->not->toContain('conditionWidth-Mismatch');
});

it('lets flow elseif rows transition from parent color to condition color', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="flow-if-condition-color-transition-test" :dev="true" :coordinates="false" color="cyan">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-condition-set
                id="sample.flow.1.conditions"
                if-id="sample.flow.1.if"
                elseif-id="sample.flow.1.elseif"
                color="cyan"
                :if-condition-label="['text' => ['IF'], 'width' => 'half']"
                :elseif-conditions="[
                    [
                        'key' => 'amber',
                        'color' => 'amber',
                        'label' => ['text' => ['ELSEIF amber'], 'width' => 'half'],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.flow.1.elseif.amber.arc-west-north')
        ->toContain('--tw-graph-protocol-local-surface-color-rgb: 222 248 252')
        ->toContain('--tw-graph-protocol-local-to-surface-color-rgb: 254 245 222')
        ->toContain('--tw-graph-protocol-local-dark-surface-color-rgb: 14 98 113')
        ->toContain('--tw-graph-protocol-local-to-dark-surface-color-rgb: 115 91 31')
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 6 182 212;[^>]*--tw-graph-protocol-local-to-color-rgb: 245 158 11;[^>]*title="sample\.flow\.1\.elseif\.amber\.bridge-in"/s')
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 245 158 11;[^>]*--tw-graph-protocol-local-to-color-rgb: 6 182 212;[^>]*title="sample\.flow\.1\.elseif\.amber\.bridge-out"/s')
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 6 182 212;[^>]*title="sample\.flow\.1\.elseif\.amber\.bridge-out\.end\.joint-arrow"/s')
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 6 182 212;[^>]*title="sample\.flow\.1\.elseif\.amber\.arc-west-north"/s')
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 6 182 212;[^>]*title="sample\.flow\.1\.elseif\.amber\.arc-south-east"/s')
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 6 182 212;[^>]*title="sample\.flow\.1\.elseif\.amber\.stem"/s')
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 6 182 212;[^>]*title="sample\.flow\.1\.elseif\.amber\.arc-south-east\.stem"/s');
});

it('keeps flow if start condition set and end bridge lengths within visible bounds', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="flow-if-bridge-minimum-test" :dev="true" :coordinates="false" color="cyan">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-start
                id="sample.flow.1.if-start"
                bridge-length="0.1rem"
                :intro-label="['text' => ['IF'], 'width' => 'half']"
            />

            <x-translation-workbench::ui.tw-graph.strang.flow-if-condition-set
                id="sample.flow.1.conditions-min"
                if-id="sample.flow.1.if-min"
                attach-to="sample.flow.1.if-start.anchorNode-end"
                bridge-length="0.1rem"
                :if-condition-label="['text' => ['IF min'], 'width' => 'half']"
            />

            <x-translation-workbench::ui.tw-graph.strang.flow-if-condition-set
                id="sample.flow.1.conditions-max"
                if-id="sample.flow.1.if-max"
                attach-to="sample.flow.1.conditions-min.then.anchorNode-end"
                bridge-length="24rem"
                :if-condition-label="['text' => ['IF max'], 'width' => 'half']"
            />

            <x-translation-workbench::ui.tw-graph.strang.flow-if-condition-set
                id="sample.flow.1.conditions-render-min"
                if-id="sample.flow.1.if-render-min"
                attach-to="sample.flow.1.conditions-max.then.anchorNode-end"
                bridge-length="0.35rem"
                :if-condition-label="['text' => ['IF render min'], 'width' => 'half']"
            />

            <x-translation-workbench::ui.tw-graph.strang.flow-if-end
                id="sample.flow.1.if-end"
                attach-to="sample.flow.1.conditions-render-min.then.anchorNode-end"
                bridge-length="0.1rem"
                :end-label="['text' => ['ENDIF'], 'width' => 'half']"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.flow.1.if-start.bridge-in')
        ->toContain('sample.flow.1.if-min.bridge-in')
        ->toContain('sample.flow.1.if-min.bridge-out')
        ->toContain('sample.flow.1.if-max.bridge-in')
        ->toContain('sample.flow.1.if-max.bridge-out')
        ->toContain('sample.flow.1.if-render-min.bridge-in')
        ->toContain('sample.flow.1.if-render-min.bridge-out')
        ->toContain('sample.flow.1.if-end.bridge-in')
        ->toContain('tw-graph-protocol-tone-surface')
        ->toContain('--tw-graph-protocol-local-length: 0.25rem')
        ->toContain('--tw-graph-protocol-local-length: 1.15rem')
        ->toContain('--tw-graph-protocol-local-length: 12rem')
        ->not->toContain('--tw-graph-protocol-local-length: 0.1rem')
        ->not->toContain('--tw-graph-protocol-local-length: 0.35rem')
        ->not->toContain('--tw-graph-protocol-local-length: 24rem');
});

it('renders an if else endif wrapper from the atomic flow if strangs', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="flow-if-wrapper-test" :dev="true" :coordinates="false" color="cyan">
            <x-translation-workbench::ui.tw-graph.strang.if-else-endif
                id="sample.flow.1.ifelse"
                bridge-length="1.75rem"
                :intro-label="['text' => ['IF / ELSE flow'], 'width' => 'half']"
                :if-condition-label="['text' => ['IF ready'], 'width' => 'default']"
                :elseif-conditions="[
                    [
                        'key' => 'review',
                        'color' => 'amber',
                        'label' => ['text' => ['ELSEIF review'], 'width' => 'default'],
                    ],
                ]"
                :end-label="['text' => ['ENDIF'], 'width' => 'half']"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.flow.1.ifelse.start')
        ->toContain('sample.flow.1.ifelse.endif')
        ->toContain('sample.flow.1.ifelse.if.bridge-in')
        ->toContain('sample.flow.1.ifelse.elseif.review.bridge-in')
        ->toContain('sample.flow.1.ifelse.endif.bridge-in')
        ->toContain('tw-graph-protocol-tone-surface');
});

it('renders parts end with configured direction cap label and counter', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="parts-end-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.parts.end
                id="sample.center.1.end"
                direction="top-bottom"
                length="3rem"
                cap-length="2rem"
                color="green"
                :anchor-start="['x' => '1rem', 'y' => '8rem']"
                :end-label="[
                    'text' => ['End marker'],
                    'side' => 'top',
                    'width' => 'halfLong',
                ]"
                dev-counter-end="9"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.center.1.end')
        ->toContain('tw-graph-protocol-primitive-line-top-bottom')
        ->toContain('--tw-graph-protocol-local-length: 3rem')
        ->toContain('--tw-graph-protocol-line-end-cap-length: 2rem')
        ->toContain('sample.center.1.end.cap.end')
        ->toContain('9')
        ->toContain('sample.center.1.end.label.bottom.1')
        ->toContain('End marker')
        ->toContain('w-72')
        ->toContain('text-green-800');
});

it('advances anchors across hand authored part chains', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="parts-chain-test" :dev="true" :coordinates="false" color="green">
            <x-translation-workbench::ui.tw-graph.parts.chain
                stem-length="5rem"
                bridge-length="6rem"
                :parts="[
                    [
                        'type' => 'start',
                        'id' => 'sample.center.1.start',
                        'length' => '5rem',
                    ],
                    [
                        'type' => 'sideways',
                        'id' => 'sample.left.1.sideways',
                        'side' => 'left',
                        'bridgeLength' => '6rem',
                    ],
                    [
                        'type' => 'end',
                        'id' => 'sample.center.1.end',
                        'length' => '2rem',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.center.1.start')
        ->toContain('--tw-graph-protocol-start-y: 0rem')
        ->toContain('--tw-graph-protocol-end-y: calc(0rem + 5rem)')
        ->toContain('sample.left.1.sideways.arc1-west-north')
        ->toContain('--tw-graph-protocol-start-y: calc(calc(0rem + 5rem) + 2.75rem)')
        ->toContain('sample.center.1.end')
        ->toContain('--tw-graph-protocol-start-x: calc(0rem + calc(2.75rem + 6rem + 2.75rem))');
});

it('inherits graph level part chain lengths when individual parts stay unset', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="parts-chain-inherited-lengths-test" :dev="true" :coordinates="false" color="emerald">
            <x-translation-workbench::ui.tw-graph.parts.chain
                stem-length="7rem"
                bridge-length="9rem"
                cap-length="2.25rem"
                :parts="[
                    [
                        'type' => 'start',
                        'id' => 'sample.center.1.inherited-start',
                    ],
                    [
                        'type' => 'sideways',
                        'id' => 'sample.left.1.inherited-sideways',
                        'side' => 'left',
                    ],
                    [
                        'type' => 'end',
                        'id' => 'sample.center.1.inherited-end',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.center.1.inherited-start')
        ->toContain('--tw-graph-protocol-local-length: 7rem')
        ->toContain('sample.left.1.inherited-sideways.bridge1')
        ->toContain('--tw-graph-protocol-local-length: 9rem')
        ->toContain('sample.center.1.inherited-end')
        ->toContain('--tw-graph-protocol-line-end-cap-length: 2.25rem')
        ->toContain('text-emerald-800');
});

it('keeps individual part lengths ahead of part chain defaults', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="parts-chain-local-lengths-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.parts.chain
                stem-length="7rem"
                bridge-length="9rem"
                cap-length="2.25rem"
                :parts="[
                    [
                        'type' => 'start',
                        'id' => 'sample.center.1.local-start',
                        'length' => '3rem',
                    ],
                    [
                        'type' => 'sideways',
                        'id' => 'sample.left.1.local-sideways',
                        'side' => 'left',
                        'bridgeLength' => '4rem',
                    ],
                    [
                        'type' => 'end',
                        'id' => 'sample.center.1.local-end',
                        'length' => '2rem',
                        'capLength' => '1.5rem',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.center.1.local-start')
        ->toContain('--tw-graph-protocol-local-length: 3rem')
        ->toContain('sample.left.1.local-sideways.bridge1')
        ->toContain('--tw-graph-protocol-local-length: 4rem')
        ->toContain('sample.center.1.local-end')
        ->toContain('--tw-graph-protocol-local-length: 2rem')
        ->toContain('--tw-graph-protocol-line-end-cap-length: 1.5rem');
});

it('accepts public kebab case keys in hand authored part chain arrays', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="parts-chain-public-keys-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.parts.chain
                stem-length="7rem"
                bridge-length="9rem"
                cap-length="2.25rem"
                :parts="[
                    [
                        'type' => 'start',
                        'id' => 'sample.center.1.public-start',
                        'stem-length' => '3rem',
                        'node-label-right' => [
                            'text' => ['Public start key'],
                            'width' => 'halfLong',
                        ],
                    ],
                    [
                        'type' => 'sideways',
                        'id' => 'sample.left.1.public-sideways',
                        'side' => 'left',
                        'arc-radius' => '4rem',
                        'bridge-length' => '5rem',
                        'node-label-left' => [
                            'text' => ['Public sideways key'],
                            'width' => 'half',
                        ],
                    ],
                    [
                        'type' => 'end',
                        'id' => 'sample.center.1.public-end',
                        'stem-length' => '2rem',
                        'cap-length' => '1.5rem',
                        'end-label' => [
                            'text' => ['Public end key'],
                            'width' => 'halfLong',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.center.1.public-start')
        ->toContain('--tw-graph-protocol-local-length: 3rem')
        ->toContain('sample.center.1.public-start.label.right.1')
        ->toContain('Public start key')
        ->toContain('sample.left.1.public-sideways.arc1-west-north')
        ->toContain('--tw-graph-protocol-local-arc-size: 4rem')
        ->toContain('sample.left.1.public-sideways.bridge1')
        ->toContain('--tw-graph-protocol-local-length: 5rem')
        ->toContain('sample.left.1.public-sideways.anchorNode-end.label-2')
        ->toContain('Public sideways key')
        ->toContain('sample.center.1.public-end')
        ->toContain('--tw-graph-protocol-local-length: 2rem')
        ->toContain('--tw-graph-protocol-line-end-cap-length: 1.5rem')
        ->toContain('sample.center.1.public-end.label.top.1')
        ->toContain('Public end key');
});

it('accepts backend friendly snake case keys in hand authored part chain arrays', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="parts-chain-snake-keys-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.parts.chain
                stem-length="7rem"
                bridge-length="9rem"
                cap-length="2.25rem"
                :parts="[
                    [
                        'type' => 'start',
                        'id' => 'sample.center.1.snake-start',
                        'stem_length' => '3rem',
                        'node_label_right' => [
                            'text' => ['Snake start key'],
                            'width' => 'halfLong',
                        ],
                    ],
                    [
                        'type' => 'sideways',
                        'id' => 'sample.right.1.snake-sideways',
                        'side' => 'right',
                        'arc_radius' => '4rem',
                        'bridge_length' => '5rem',
                        'node_label_right' => [
                            'text' => ['Snake sideways key'],
                            'width' => 'half',
                        ],
                    ],
                    [
                        'type' => 'end',
                        'id' => 'sample.center.1.snake-end',
                        'stem_length' => '2rem',
                        'cap_length' => '1.5rem',
                        'end_label' => [
                            'text' => ['Snake end key'],
                            'width' => 'halfLong',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.center.1.snake-start')
        ->toContain('--tw-graph-protocol-local-length: 3rem')
        ->toContain('sample.center.1.snake-start.label.right.1')
        ->toContain('Snake start key')
        ->toContain('sample.right.1.snake-sideways.arc1-east-north')
        ->toContain('--tw-graph-protocol-local-arc-size: 4rem')
        ->toContain('sample.right.1.snake-sideways.bridge1')
        ->toContain('--tw-graph-protocol-local-length: 5rem')
        ->toContain('sample.right.1.snake-sideways.anchorNode-end.label-1')
        ->toContain('Snake sideways key')
        ->toContain('sample.center.1.snake-end')
        ->toContain('--tw-graph-protocol-local-length: 2rem')
        ->toContain('--tw-graph-protocol-line-end-cap-length: 1.5rem')
        ->toContain('sample.center.1.snake-end.label.top.1')
        ->toContain('Snake end key');
});

it('inherits graph level lengths through part chains when chain and parts stay unset', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph
            graph-id="parts-chain-graph-level-lengths-test"
            :dev="true"
            :coordinates="false"
            stem-length="8rem"
            bridge-length="11rem"
            cap-length="2.75rem"
        >
            <x-translation-workbench::ui.tw-graph.parts.chain
                :parts="[
                    [
                        'type' => 'start',
                        'id' => 'sample.center.1.graph-default-start',
                    ],
                    [
                        'type' => 'sideways',
                        'id' => 'sample.left.1.graph-default-sideways',
                        'side' => 'left',
                    ],
                    [
                        'type' => 'end',
                        'id' => 'sample.center.1.graph-default-end',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.center.1.graph-default-start')
        ->toContain('--tw-graph-protocol-local-length: 8rem')
        ->toContain('sample.left.1.graph-default-sideways.bridge1')
        ->toContain('--tw-graph-protocol-local-length: 11rem')
        ->toContain('sample.center.1.graph-default-end')
        ->toContain('--tw-graph-protocol-line-end-cap-length: 2.75rem');
});

it('keeps part chain color inheritance simple with local part color as the only override', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="parts-chain-color-contract-test" :dev="true" :coordinates="false" color="green">
            <x-translation-workbench::ui.tw-graph.parts.chain
                color="amber"
                :parts="[
                    [
                        'type' => 'start',
                        'id' => 'sample.center.1.chain-color-start',
                        'nodeLabelRight' => [
                            'text' => 'Chain color label',
                        ],
                    ],
                    [
                        'type' => 'sideways',
                        'id' => 'sample.left.1.local-color-sideways',
                        'side' => 'left',
                        'color' => 'sky',
                        'nodeLabelLeft' => [
                            'text' => 'Local color label',
                        ],
                    ],
                    [
                        'type' => 'end',
                        'id' => 'sample.center.1.chain-color-end',
                        'endLabel' => [
                            'text' => 'Inherited end label',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('Chain color label')
        ->toContain('text-amber-700')
        ->toContain('Local color label')
        ->toContain('text-sky-800')
        ->toContain('Inherited end label')
        ->toContain('text-amber-700');
});

it('keeps explicit chained part label colors ahead of inherited chain color', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="parts-chain-label-color-test" :dev="true" :coordinates="false" color="green">
            <x-translation-workbench::ui.tw-graph.parts.chain
                color="amber"
                :parts="[
                    [
                        'type' => 'start',
                        'id' => 'sample.center.1.label-color-start',
                        'nodeLabelRight' => [
                            'text' => 'Explicit start label color',
                            'color' => 'rose',
                        ],
                    ],
                    [
                        'type' => 'sideways',
                        'id' => 'sample.left.1.label-color-sideways',
                        'side' => 'left',
                        'nodeLabelLeft' => [
                            'text' => 'Explicit sideways label color',
                            'color' => 'sky',
                        ],
                    ],
                    [
                        'type' => 'end',
                        'id' => 'sample.center.1.label-color-end',
                        'endLabel' => [
                            'text' => 'Explicit end label color',
                            'color' => 'green',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('Explicit start label color')
        ->toContain('text-rose-700')
        ->toContain('Explicit sideways label color')
        ->toContain('text-sky-800')
        ->toContain('Explicit end label color')
        ->toContain('text-green-800');
});

it('advances chain anchors in top bottom direction', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="parts-chain-top-bottom-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.parts.chain
                direction="top-bottom"
                :parts="[
                    [
                        'type' => 'start',
                        'id' => 'sample.center.1.start',
                        'length' => '4rem',
                    ],
                    [
                        'type' => 'sideways',
                        'id' => 'sample.right.1.sideways',
                        'side' => 'right',
                        'bridgeLength' => '5rem',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.center.1.start')
        ->toContain('--tw-graph-protocol-end-y: calc(0rem - 4rem)')
        ->toContain('sample.right.1.sideways.arc1-east-south')
        ->toContain('--tw-graph-protocol-start-y: calc(0rem + calc(4rem * -1))')
        ->toContain('--tw-graph-protocol-start-x: 0rem');
});

it('renders primitive text labels with fixed configured width variants', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.text id="primitive.default" text="Default" />
        <x-translation-workbench::ui.tw-graph.primitives.text id="primitive.half" text="Half" :half="true" />
        <x-translation-workbench::ui.tw-graph.primitives.text id="primitive.half-long" text="Half long" :half-long="true" />
        <x-translation-workbench::ui.tw-graph.primitives.text id="primitive.long" text="Long" :long="true" />
    BLADE);

    expect($html)
        ->toContain('primitive.default')
        ->toContain('w-48')
        ->toContain('primitive.half')
        ->toContain('w-24')
        ->toContain('primitive.half-long')
        ->toContain('w-72')
        ->toContain('primitive.long')
        ->toContain('w-96');
});

it('renders primitive text alignment and justification explicitly', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.text
            id="primitive.justified"
            :text="['A longer line', 'Second line']"
            align="left"
            :justify="true"
        />
    BLADE);

    expect($html)
        ->toContain('primitive.justified')
        ->toContain('items-start text-left')
        ->toContain('text-justify')
        ->toContain('block w-full')
        ->toContain('style="text-align: justify;"');
});

it('limits primitive text rendering to the configured number of lines', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.text
            id="primitive.max-lines"
            :text="['Line one', 'Line two', 'Line three']"
            :max-lines="2"
        />
    BLADE);

    expect($html)
        ->toContain('Line one')
        ->toContain('Line two')
        ->not->toContain('Line three');
});

it('renders ordinal label fragments with superscript suffixes', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.text
            id="primitive.ordinal"
            :text="[
                ['ordinal' => ['number' => 21, 'suffix' => 'st'], 'text' => ' sample event'],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('primitive.ordinal')
        ->toContain('>21</span>')
        ->toContain('>st</sup>')
        ->toContain(' sample event');
});

it('renders stem-compressed with proportional split when anchor end is configured', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.stem-compressed
            :dev="true"
            :segment="[
                'id' => 'segment.compressed.test',
                'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
                'anchorEnd' => ['x' => '0rem', 'y' => '12rem'],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('segment.compressed.test.stem.before')
        ->toContain('segment.compressed.test.stem.dotted')
        ->toContain('segment.compressed.test.stem.after')
        ->toContain('calc(calc(12rem - 0rem) / 4)')
        ->toContain('calc(calc(12rem - 0rem) / 2)')
        ->toContain('tw-graph-protocol-primitive-line-dashed');
});

it('renders node images only when a source is present', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.node-image
            id="sample.center.1.portrait"
            source="/vendor/translation-workbench/resume-a-einstein.png"
            size="5rem"
            anchor-x="2rem"
            anchor-y="8rem"
            alt="Portrait"
            color="amber"
            z-index="44"
        />
        <x-translation-workbench::ui.tw-graph.primitives.node-image id="sample.center.1.empty" />
    BLADE);

    expect($html)
        ->toContain('sample.center.1.portrait')
        ->toContain('src="/vendor/translation-workbench/resume-a-einstein.png"')
        ->toContain('alt="Portrait"')
        ->toContain('--tw-graph-protocol-node-image-size: 5rem')
        ->toContain('--tw-graph-protocol-anchor-x: 2rem')
        ->toContain('--tw-graph-protocol-anchor-y: 8rem')
        ->toContain('--tw-graph-protocol-local-color-rgb: 245 158 11')
        ->toContain('--tw-graph-protocol-z-index: 44')
        ->not->toContain('sample.center.1.empty');
});

it('renders joint arrows with explicit direction color and z index', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
            id="sample.left.1.bridge.arc-out.joint-arrow"
            direction="left"
            anchor-x="-4rem"
            anchor-y="12rem"
            color="green"
            z-index="33"
        />
    BLADE);

    expect($html)
        ->toContain('sample.left.1.bridge.arc-out.joint-arrow')
        ->toContain('tw-graph-protocol-primitive-joint-arrow-left')
        ->toContain('--tw-graph-protocol-anchor-x: -4rem')
        ->toContain('--tw-graph-protocol-anchor-y: 12rem')
        ->toContain('--tw-graph-protocol-local-color-rgb: 44 144 103')
        ->toContain('--tw-graph-protocol-z-index: 33');
});

it('renders root graph dev transparency and coordinate visibility flags independently', function (): void {
    $devHtml = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="root-dev-test" :dev="true" :coordinates="false" />
    BLADE);
    $coordinatesHtml = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="root-coordinates-test" :dev="false" :coordinates="true" />
    BLADE);

    expect($devHtml)
        ->toContain('id="root-dev-test"')
        ->toContain('--tw-graph-protocol-color-alpha: 0.5')
        ->toContain('tw-graph-protocol-coordinates-disabled')
        ->and($coordinatesHtml)
        ->toContain('id="root-coordinates-test"')
        ->toContain('--tw-graph-protocol-color-alpha: 1')
        ->not->toContain('tw-graph-protocol-coordinates-disabled');
});

it('treats string false dev props as disabled across root and nested graph elements', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="root-dev-string-false-test" dev="false" coordinates="false" color="cyan">
            <x-translation-workbench::ui.tw-graph.parts.start
                id="sample.center.1.start"
                :node-label-right="['text' => ['Visible label']]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('id="root-dev-string-false-test"')
        ->toContain('--tw-graph-protocol-color-alpha: 1')
        ->toContain('tw-graph-protocol-coordinates-disabled')
        ->toContain('Visible label')
        ->not->toContain('tw-graph-protocol-dev-only')
        ->not->toContain('tw-graph-protocol-primitive-dev-node-counter');
});

it('uses central graph defaults for root canvas geometry styles', function (): void {
    config()->set('tw-graph-defaults.line_width', '0.5rem');
    config()->set('tw-graph-defaults.node_size', '1.5rem');
    config()->set('tw-graph-defaults.arc_size', '4rem');
    config()->set('tw-graph-defaults.slot_min_height', '64rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="root-defaults-test" color="green" :dev="false" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.parts.start id="slot.center.1.start" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('id="root-defaults-test"')
        ->toContain('--tw-graph-protocol-path-width: 0.5rem')
        ->toContain('--tw-graph-protocol-node-size: 1.5rem')
        ->toContain('--tw-graph-protocol-arc-size: 4rem')
        ->toContain('--tw-graph-protocol-min-height: 64rem');
});

it('keeps local root canvas geometry props ahead of central graph defaults', function (): void {
    config()->set('tw-graph-defaults.line_width', '0.5rem');
    config()->set('tw-graph-defaults.node_size', '1.5rem');
    config()->set('tw-graph-defaults.arc_size', '4rem');

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph
            graph-id="root-local-defaults-test"
            color="green"
            line-width="0.75rem"
            node-size="2rem"
            arc-size="5rem"
            :dev="false"
            :coordinates="false"
        >
            <x-translation-workbench::ui.tw-graph.parts.start id="slot.center.1.start" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('id="root-local-defaults-test"')
        ->toContain('--tw-graph-protocol-path-width: 0.75rem')
        ->toContain('--tw-graph-protocol-node-size: 2rem')
        ->toContain('--tw-graph-protocol-arc-size: 5rem')
        ->not->toContain('--tw-graph-protocol-path-width: 0.5rem')
        ->not->toContain('--tw-graph-protocol-node-size: 1.5rem')
        ->not->toContain('--tw-graph-protocol-arc-size: 4rem');
});

it('renders slot canvas metrics only for slot based manual graphs', function (): void {
    $slotHtml = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="root-slot-test" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.parts.start id="slot.center.1.start" />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    $protocolHtml = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="root-protocol-test" :dev="true" :coordinates="false" />
    BLADE);

    expect($slotHtml)
        ->toContain('tw-graph-protocol-canvas-slot')
        ->toContain('--tw-graph-protocol-calculated-width')
        ->and($protocolHtml)
        ->not->toContain('tw-graph-protocol-canvas-slot')
        ->not->toContain('--tw-graph-protocol-calculated-width');
});

it('passes node images and extension geometry through chained handmade parts', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="parts-chain-image-extension-test" :dev="true" :coordinates="false" color="green">
            <x-translation-workbench::ui.tw-graph.parts.chain
                stem-length="4rem"
                bridge-length="5rem"
                :parts="[
                    [
                        'type' => 'start',
                        'id' => 'sample.center.1.birth',
                        'length' => '4rem',
                        'nodeImage' => [
                            'source' => '/vendor/translation-workbench/resume-a-einstein.png',
                            'size' => '4rem',
                            'alt' => 'Portrait',
                        ],
                    ],
                    [
                        'type' => 'sideways',
                        'id' => 'sample.left.1.school',
                        'side' => 'left',
                        'bridgeLength' => '5rem',
                        'extension' => '3rem',
                        'nodeLabelRight' => [
                            'text' => ['Extended note'],
                            'width' => 'half',
                        ],
                    ],
                    [
                        'type' => 'end',
                        'id' => 'sample.center.1.close',
                        'length' => '2rem',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('sample.center.1.birth.anchorNode-end.image')
        ->toContain('src="/vendor/translation-workbench/resume-a-einstein.png"')
        ->toContain('--tw-graph-protocol-node-image-size: 4rem')
        ->toContain('sample.left.1.school.extension-stem1')
        ->toContain('sample.left.1.school.extension-stem2')
        ->toContain('sample.left.1.school.anchorNode-end.label-1')
        ->toContain('Extended note')
        ->toContain('w-24')
        ->toContain('sample.center.1.close')
        ->toContain('--tw-graph-protocol-start-x: calc(0rem + calc(2.75rem + 5rem + 2.75rem))')
        ->toContain('--tw-graph-protocol-start-y: calc(calc(0rem + 4rem) + calc(2.75rem + 2.75rem + 3rem + 3rem))');
});

it('positions right IF sections opposite the left geometry while preserving height and readable labels', function (): void {
    foreach (['left', 'right'] as $side) {
        $html = Blade::render(<<<'BLADE'
            <x-translation-workbench::ui.tw-graph :graph-id="'if-side-' . $side" :dev="true">
                <x-translation-workbench::ui.tw-graph.strang.if-else-endif
                    id="side-test" :side="$side" :bypass="true"
                    :anchor-start="['x' => '7rem', 'y' => '3rem']"
                    :if-condition-label="['text' => ['Readable condition'], 'width' => 'long', 'align' => 'left']"
                    :elseif-conditions="[['key' => 'else', 'label' => ['text' => ['Fallback']], 'thenContinuation' => 'arc-east-north']]"
                />
            </x-translation-workbench::ui.tw-graph>
        BLADE, ['side' => $side]);

        expect($html)->toContain('Readable condition')->toContain('Fallback')->not->toContain('scaleX');
        $document = new DOMDocument;
        @$document->loadHTML($html);
        $xpath = new DOMXPath($document);
        $arc = $side === 'right' ? 'arc-west-north' : 'arc-east-north';
        $direction = $side === 'right' ? 'right' : 'left';
        expect($xpath->query('//*[@title="side-test.start.' . $arc . '.end.joint-arrow" and contains(@class, "joint-arrow-' . $direction . '")]')->length)->toBe(1);
    }

    foreach (['start.anchorNode-end', 'if.condition.anchorNode-end', 'if.then.anchorNode-end', 'elseif.else.then.anchorNode-end', 'endif.anchorNode-end'] as $name) {
        $left = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('if-side-left', 'side-test.' . $name);
        $right = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('if-side-right', 'side-test.' . $name);
        expect($left)->not->toBeNull();
        expect($right)->not->toBeNull();
        \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::forgetGraph('side-comparison');
        \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::put(
            'side-comparison', 'point',
            'calc(' . $left['x'] . ' + ' . $right['x'] . ')',
            'calc(' . $left['y'] . ' - ' . $right['y'] . ')',
            '0rem', '0rem', 'center',
        );
        $metrics = \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::canvasMetrics('side-comparison', '0rem', '0rem');
        expect($metrics['maxXRem'])->toBe(14.0);
        expect($metrics['minYRem'])->toBe(0.0);
        expect($metrics['maxYRem'])->toBe(0.0);
    }
});
