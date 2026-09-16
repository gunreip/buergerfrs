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
        <x-translation-workbench::ui.tw-graph graph-id="flow-if-else-attach-test" :dev="true" :coordinates="false" color="cyan">
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

            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                id="sample.flow.1.process.decision-1"
                attach-to="sample.flow.1.process.step-1.anchorNode-end"
                bridge-length="8rem"
                :condition-label="['text' => ['Decision']]"
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
        ->toContain('sample.flow.1.process.decision-1.question.stem.after')
        ->toContain('sample.flow.1.process.decision-1.false.bridge1')
        ->toContain('sample.flow.1.process.decision-1.false.stem')
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
    config()->set('tw-graph-defaults.min_height', '64rem');

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

it('bounds label bridges using the rendered text box instead of a fixed line height', function (array $text, int $maxLines, bool $dev): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph.segments.label-bridge
            id="label-bounds" :dev="$dev"
            :label="['text' => $text, 'width' => 'half', 'maxLines' => $maxLines]"
        />
    BLADE, compact('text', 'maxLines', 'dev'));
    $document = new DOMDocument;
    @$document->loadHTML($html);
    $xpath = new DOMXPath($document);
    $boxes = $xpath->query('//*[@data-tw-graph-dev-box="label-bounds.label.center.1.dev-box"]');
    expect($boxes->length)->toBe($dev ? 1 : 0);
    expect($xpath->query('//*[@data-tw-graph-dev-box="label-bounds.label.center.1.mask.dev-box"]')->length)->toBe(0);
    if ($dev) {
        $box = $boxes->item(0);
        expect($box->parentNode->getAttribute('data-tw-graph-path'))->toBe('label-bounds.label.center.1');
        expect($box->getAttribute('style'))->toContain('inset: -0.35rem')->not->toContain('height:');
        expect($xpath->query('.//*[contains(@class, "tw-graph-protocol-primitive-text-line")]', $box->parentNode)->length)
            ->toBe(min(count($text), $maxLines));
    }
})->with([
    'one line' => [['Inner ENDIF'], 3, true],
    'two lines' => [['IF review is required', 'THEN check the result'], 3, true],
    'wrapped text' => [[str_repeat('Long condition text ', 12), 'Second line', 'Third line'], 3, true],
    'limited lines' => [['First', 'Second', 'Third'], 1, true],
    'DEV disabled' => [['First', 'Second'], 3, false],
]);

it('honors explicit intro and ENDIF sides without changing the condition rail', function (string $side, ?string $introSide, ?string $endSide, bool $withElse): void {
    $intro = ['text' => ['Intro'], 'width' => 'half', 'align' => 'center'];
    $end = ['text' => ['End'], 'width' => 'half', 'align' => 'left'];
    if ($introSide !== null) {
        $intro['side'] = $introSide;
    }
    if ($endSide !== null) {
        $end['side'] = $endSide;
    }
    $conditions = $withElse ? [
        ['key' => 'next', 'label' => ['text' => ['Next']]],
        ['key' => 'else', 'label' => ['text' => ['Else']], 'thenContinuation' => 'arc-east-north'],
    ] : [];
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="label-side-test" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.if-else-endif
                id="label-side" :side="$side"
                :intro-label="$intro" :end-label="$end"
                end-bridge-length="1.75rem"
                :elseif-conditions="$conditions"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'intro', 'end', 'conditions'));
    $introRight = ($introSide ?? $side) === 'right';
    $endRight = ($endSide ?? $side) === 'right';
    expect($html)
        ->toContain('label-side.start.' . ($introRight ? 'arc-west-north' : 'arc-east-north'))
        ->toContain('label-side.endif.' . ($endRight ? 'arc-south-east' : 'arc-south-west'))
        ->toContain('label-side.if.' . ($side === 'right' ? 'arc-east-north' : 'arc-west-north'));
    if ($endSide !== null) {
        $last = $withElse ? 'elseif.else' : 'if';
        $turn = $endRight ? 'arc-west-north' : 'arc-east-north';
        $document = new DOMDocument;
        @$document->loadHTML($html);
        $xpath = new DOMXPath($document);
        $direction = $endRight ? 'right' : 'left';
        expect($xpath->query('//*[@title="label-side.' . $last . '.then.' . $turn . '.end.joint-arrow" and contains(@class, "joint-arrow-' . $direction . '")]')->length)->toBe(1);
        if (! $withElse) {
            // A requested ENDIF turn must preserve the authored THEN stem.
            expect($html)->toContain('label-side.if.' . ($side === 'right' ? 'arc-south-west.stem' : 'arc-south-east.stem'));
        }
    }
    $from = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('label-side-test', 'label-side.conditions.then.anchorNode-end');
    $to = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('label-side-test', 'label-side.endif.anchorNode-end');
    $delta = $endRight ? 'calc(' . $to['x'] . ' - ' . $from['x'] . ')' : 'calc(' . $from['x'] . ' - ' . $to['x'] . ')';
    \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::forgetGraph('label-side-distance');
    \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::put('label-side-distance', 'point', '0rem', $delta, '0rem', '0rem', 'center');
    expect(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::canvasMetrics('label-side-distance')['maxYRem'])->toBe(12.25);
})->with([
    'unchanged left' => ['left', null, null, true],
    'unchanged right' => ['right', null, null, true],
    'left end right' => ['left', null, 'right', true],
    'right end left' => ['right', null, 'left', true],
    'left intro right' => ['left', 'right', null, true],
    'right intro left' => ['right', 'left', null, true],
    'both units right' => ['left', 'right', 'right', true],
    'both units left' => ['right', 'left', 'left', true],
    'single IF end right' => ['left', null, 'right', false],
    'single IF end left' => ['right', null, 'left', false],
]);

it('moves node labels independently of IF geometry', function (string $side, string $labelSide): void {
    $template = <<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="node-label-side-test">
            <x-translation-workbench::ui.tw-graph.strang.if-else-endif
                id="node-label-side" :side="$side"
                :intro-label="$intro" :end-label="$end"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE;
    $intro = ['text' => ['IF'], 'placement' => 'node'];
    $end = ['text' => ['ENDIF'], 'placement' => 'node'];
    Blade::render($template, compact('side', 'intro', 'end'));
    $anchors = [];
    foreach (['start', 'conditions.then', 'endif'] as $part) {
        $anchors[$part] = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('node-label-side-test', 'node-label-side.' . $part . '.anchorNode-end');
    }
    $intro['side'] = $labelSide;
    $end['side'] = $labelSide;
    $html = Blade::render($template, compact('side', 'intro', 'end'));
    foreach ($anchors as $part => $anchor) {
        expect($anchor)->not->toBeNull();
        expect(\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('node-label-side-test', 'node-label-side.' . $part . '.anchorNode-end'))->toBe($anchor);
    }
    expect($html)->toContain('node-label-side.start.intro.label.center.1.connector');
    expect($html)->toContain('node-label-side.endif.label.center.1.connector');
})->with([
    ['left', 'right'], ['right', 'left'], ['left', 'top'], ['right', 'bottom'],
]);

it('routes a question into true and false action bridges with attachable final arcs', function (string $direction, string $side, float $questionY, float $outgoingY, float $outgoingX, string $stemLength = '8rem'): void {
    $graphId = 'binary-decision-' . $direction . '-' . $side;
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :graph-id="$graphId" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                id="binary.decision"
                :side="$side"
                :stem-length="$stemLength"
                :direction="$direction"
                :anchor-start="['x' => '3rem', 'y' => '10rem']"
                before-length="2rem"
                label-gap="6rem"
                after-length="3rem"
                arc-size="2rem"
                left-bridge-length="2rem"
                right-bridge-length="3rem"
                :condition-label="['text' => ['IF approved?', 'Review completed'], 'width' => 'halfLong']"
                :if-start="['text' => ['True'], 'width' => 'half']"
                :if-end="['text' => ['False', 'Try again'], 'width' => 'default']"
            />
            <x-translation-workbench::ui.tw-graph.strang.flow-step
                id="binary.followup"
                attach-to="binary.decision.anchorNode-true"
                :direction="$direction"
                before-length="1rem"
                label-gap="2rem"
                after-length="1rem"
                :step-label="['text' => ['Continue']]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('graphId', 'direction', 'side', 'stemLength'));

    $anchor = fn(string $name): ?array => \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($graphId, $name);
    $evaluate = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn(string $expression): float => $evaluate->invoke(null, $expression);
    $junction = $anchor('binary.decision.anchorNode-decision');
    $true = $anchor('binary.decision.anchorNode-true');
    $false = $anchor('binary.decision.anchorNode-false');
    $document = new DOMDocument();
    @$document->loadHTML($html);
    $sharedNodes = 0;
    foreach ((new DOMXPath($document))->query('//*[contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-node ")]') as $node) {
        $style = $node->getAttribute('style');
        if (preg_match('/--tw-graph-protocol-anchor-x: ([^;]+);/', $style, $x) && preg_match('/--tw-graph-protocol-anchor-y: ([^;]+);/', $style, $y)) {
            if ($number($x[1]) === $outgoingX && $number($y[1]) === $outgoingY) {
                $sharedNodes++;
            }
        }
    }
    expect($sharedNodes)->toBe(1);
    expect($html)->toContain('binary.decision.false.stem.end.joint-arrow');
    expect($html)->toMatch('/binary\.decision\.true\.arc2-[a-z-]+\.end\.joint-arrow/');
    $trueExitArrows = (new DOMXPath($document))->query('//*[starts-with(@data-tw-graph-path, "binary.decision.true.arc2-") and contains(@data-tw-graph-path, ".end.joint-arrow")]');
    expect($trueExitArrows)->toHaveCount(1);
    expect($trueExitArrows->item(0)->getAttribute('class'))->toContain('tw-graph-protocol-primitive-joint-arrow-' . ($direction === 'top-bottom' ? 'bottom' : 'top'));
    foreach (['true', 'false'] as $branch) {
        $stems = (new DOMXPath($document))->query('//*[@data-tw-graph-path="binary.decision.' . $branch . '.stem"]');
        expect($stems)->toHaveCount(1);
        expect($stems->item(0)->getAttribute('class'))->not->toContain('tw-graph-protocol-primitive-line-start');
    }

    expect($junction)->toBe($anchor('binary.decision.question.anchorNode-end'))
        ->and($number($junction['y']))->toBe($questionY)
        ->and($true)->toBe($anchor('binary.decision.left.anchorNode-end'))
        ->and($false)->toBe($anchor('binary.decision.right.anchorNode-end'))
        ->and($number($true['x']))->toBe($outgoingX)
        ->and($number($false['x']))->toBe($outgoingX)
        ->and($number($anchor('binary.decision.true.stem.anchorNode-end')['y']))->toBe($outgoingY)
        ->and($number($anchor('binary.decision.true.stem.anchorNode-end')['x']))->toBe($outgoingX)
        ->and($anchor('binary.decision.anchorNode-end'))->toBe($false)
        ->and($number($true['y']))->toBe($outgoingY)
        ->and($number($false['y']))->toBe($outgoingY)
        ->and($number($anchor('binary.followup.anchorNode-end')['x']))->toBe($outgoingX)
        ->and($number($anchor('binary.followup.anchorNode-end')['y']))->toBe($outgoingY + ($direction === 'bottom-top' ? 4.0 : -4.0))
        ->and($html)->toContain('binary.decision.question.label', 'binary.decision.true.bridge1.label.center.1', 'binary.decision.false.bridge1.label.center.1', 'IF approved?', 'True', 'False')
        ->not->toContain('binary.decision.decision-label', 'binary.decision.left.bridge.arc-out.joint-arrow');
})->with([['bottom-top', 'left', 21.0, 33.0, -19.0], ['top-bottom', 'left', -1.0, -13.0, -19.0], ['bottom-top', 'right', 21.0, 33.0, 25.0], ['top-bottom', 'right', -1.0, -13.0, 25.0], ['bottom-top', 'left', 21.0, 29.25, -19.0, '1rem']]);

it('keeps chain continuation aligned with the rendered sideways radius', function (string $direction, string $side, ?string $override): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="chain-radius-test" arc-size="2.75rem">
            <x-translation-workbench::ui.tw-graph.parts.chain
                :direction="$direction"
                arc-radius="2rem"
                bridge-length="6rem"
                :parts="[
                    ['type' => 'sideways', 'id' => 'radius.sideways', 'side' => $side, 'arcRadius' => $override],
                    ['type' => 'end', 'id' => 'radius.end', 'length' => '4rem'],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('direction', 'side', 'override'));

    $sideways = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('chain-radius-test', 'radius.sideways.anchorNode-end');
    $document = new DOMDocument();
    @$document->loadHTML($html);
    $line = (new DOMXPath($document))->query('//*[@data-tw-graph-path="radius.end"]')->item(0);
    expect($line)->not->toBeNull();
    preg_match('/--tw-graph-protocol-start-x: ([^;]+);/', $line->getAttribute('style'), $entryX);
    preg_match('/--tw-graph-protocol-start-y: ([^;]+);/', $line->getAttribute('style'), $entryY);
    $evaluate = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn (string $value): float => $evaluate->invoke(null, $value);
    $radius = $override === null ? 2.0 : 3.0;
    expect($number($sideways['x']))->toBe(($side === 'left' ? 1 : -1) * (6.0 + 2 * $radius))
        ->and($number($entryX[1]))->toBe($number($sideways['x']))
        ->and($number($entryY[1]))->toBe($number($sideways['y']));
})->with([
    ['bottom-top', 'left', null],
    ['bottom-top', 'right', null],
    ['top-bottom', 'left', null],
    ['top-bottom', 'right', null],
    ['bottom-top', 'left', '3rem'],
]);

it('renders ternary values with separate branch information and an attachable result', function (string $side, string $direction): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="ternary-test" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-ternary
                id="ternary.expression"
                :side="$side"
                :direction="$direction"
                :condition-label="['text' => ['$name !== null?']]"
                :if-start="['text' => ['$name'], 'width' => 'half']"
                :if-end="['text' => ['Unknown', 'Fallback'], 'width' => 'default']"
                stem-length="6rem"
                color="cyan"
            />
            <x-translation-workbench::ui.tw-graph.strang.flow-step
                id="ternary.assignment"
                attach-to="ternary.expression.anchorNode-end"
                :direction="$direction"
                before-length="1rem"
                label-gap="2rem"
                after-length="1rem"
                :step-label="['text' => ['$label = result']]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'direction'));
    expect($html)->toContain('$name !== null?', '$name', 'Unknown', 'Fallback', 'True', 'False', '$label = result');
    expect($html)->toContain('ternary.expression.true.anchorNode-end.label-', 'ternary.expression.false.stem.label.');
    $end = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('ternary-test', 'ternary.expression.anchorNode-end');
    $assignment = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('ternary-test', 'ternary.assignment.anchorNode-end');
    $evaluate = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn (string $value): float => $evaluate->invoke(null, $value);
    expect($number($assignment['x']))->toBe($number($end['x']))
        ->and($number($assignment['y']))->toBe($number($end['y']) + ($direction === 'bottom-top' ? 4.0 : -4.0));
})->with([['left', 'bottom-top'], ['right', 'bottom-top'], ['left', 'top-bottom'], ['right', 'top-bottom']]);

it('forwards ternary dimensions to the shared route geometry', function (string $property, string $value, float $questionDelta, float $endDelta): void {
    $render = function (array $overrides): array {
        Blade::render(<<<'BLADE'
            <x-translation-workbench::ui.tw-graph graph-id="ternary-dimensions">
                <x-translation-workbench::ui.tw-graph.strang.flow-if-ternary
                    id="dimension.expression"
                    :before-length="$overrides['before'] ?? '2rem'"
                    :stem-length="$overrides['stem'] ?? '8rem'"
                    :arc-radius="$overrides['arc'] ?? '2rem'"
                    :condition-label="['text' => ['Condition']]"
                />
            </x-translation-workbench::ui.tw-graph>
        BLADE, compact('overrides'));
        $question = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('ternary-dimensions', 'dimension.expression.question.anchorNode-end');
        $end = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('ternary-dimensions', 'dimension.expression.anchorNode-end');
        $evaluate = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::class, 'evaluateRemExpression');
        return [$evaluate->invoke(null, $question['y']), $evaluate->invoke(null, $end['y'])];
    };
    $baseline = $render([]);
    $changed = $render([$property => $value]);
    expect($changed[0] - $baseline[0])->toBe($questionDelta)
        ->and($changed[1] - $baseline[1])->toBe($endDelta);
})->with([
    ['before', '6rem', 4.0, 4.0],
    ['stem', '12rem', 0.0, 4.0],
    ['arc', '3rem', 0.0, 2.0],
]);

it('renders counters at both ternary branch dots and the common output only in DEV mode', function (bool $dev): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="ternary-counters" :dev="$dev">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-ternary id="counted.ternary" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('dev'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    foreach ([
        'counted.ternary.true.arc2-south-west.node.end' => '1',
        'counted.ternary.false.stem.node.end' => '2',
        'counted.ternary.false.arc2-south-west.node.end' => '3',
    ] as $id => $counter) {
        $nodes = $xpath->query('//*[contains(@class, "tw-graph-protocol-primitive-dev-node-counter") and @data-tw-graph-path="' . $id . '"]');
        expect($nodes->length)->toBe($dev ? 1 : 0);
        if ($dev) {
            expect(trim($nodes->item(0)->textContent))->toBe($counter);
        }
    }
})->with([true, false]);

it('routes a simple IF around its action with a plain full width bypass', function (string $side, string $direction, string $width): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="simple-if-test" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.flow-if
                id="simple.if"
                :side="$side"
                :direction="$direction"
                before-length="6rem"
                stem-length="8rem"
                true-bridge-length="3rem"
                :condition-label="['text' => ['IF approved?']]"
                :if-start="['text' => ['Publish paper', 'Notify author'], 'width' => $width]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'direction', 'width'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//*[@data-tw-graph-path="simple.if.false.bridge1"]')->length)->toBe(1);
    expect($xpath->query('//*[@data-tw-graph-path="simple.if.true.bridge1.label.center.1"]')->length)->toBe(1);
    expect($html)->not->toContain('simple.if.false.bridge1.label.center.1');
    $trueEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('simple-if-test', 'simple.if.true.stem.anchorNode-end');
    $commonEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('simple-if-test', 'simple.if.anchorNode-end');
    $evaluate = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::class, 'evaluateRemExpression');
    foreach (['x', 'y'] as $axis) {
        expect($evaluate->invoke(null, $trueEnd[$axis]))->toBe($evaluate->invoke(null, $commonEnd[$axis]));
    }
})->with([
    ['left', 'bottom-top', 'half'], ['right', 'bottom-top', 'long'],
    ['left', 'top-bottom', 'long'], ['right', 'top-bottom', 'half'],
]);

it('wires ELSEIF only after the first False output and joins all three routes', function (string $side, string $direction, string $width, array $falseLabel = []): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="elseif-test" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif
                id="checked.elseif"
                :side="$side"
                :if-end="$falseLabel"
                :direction="$direction"
                :anchor-start="['x' => '3rem', 'y' => '4rem']"
                before-length="4rem"
                elseif-before-length="8rem"
                stem-length="10rem"
                arc-radius="3rem"
                :condition-label="['text' => ['IF approved?']]"
                :if-start="['text' => ['Publish'], 'width' => $width]"
                :elseifs="[[
                    'conditionLabel' => ['text' => ['ELSEIF changes?']],
                    'actionLabel' => ['text' => ['Revise', 'Notify'], 'width' => 'default'],
                ]]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'direction', 'width', 'falseLabel'));
    $anchor = fn (string $suffix) => \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('elseif-test', 'checked.elseif.' . $suffix);
    $evaluate = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn (string $value): float => $evaluate->invoke(null, $value);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $before = $xpath->query('//*[@data-tw-graph-path="checked.elseif.elseif.question.stem.before"]')->item(0);
    expect($before)->not->toBeNull();
    foreach (['x', 'y'] as $axis) {
        preg_match('/--tw-graph-protocol-start-' . $axis . ': ([^;]+);/', $before->getAttribute('style'), $coordinate);
        expect($number($coordinate[1]))->toBe($number($anchor('question.anchorNode-end')[$axis]));
        expect($number($anchor('true.stem.anchorNode-end')[$axis]))->toBe($number($anchor('elseif.true.anchorNode-end')[$axis]));
        expect($number($anchor('elseif.true.stem.anchorNode-end')[$axis]))->toBe($number($anchor('anchorNode-end')[$axis]));
    }
    $hasFalseText = isset($falseLabel['text']);
    expect($xpath->query('//*[@data-tw-graph-path="checked.elseif.elseif.false.bridge1"]')->length)->toBe($hasFalseText ? 0 : 1);
    expect($xpath->query('//*[@data-tw-graph-path="checked.elseif.elseif.false.bridge1.label.center.1"]')->length)->toBe($hasFalseText ? 1 : 0);
    expect($html)->toContain('IF approved?', 'ELSEIF changes?', 'Publish', 'Revise', 'Notify');
})->with([
    ['left', 'bottom-top', 'half'], ['right', 'bottom-top', 'long'],
    ['left', 'top-bottom', 'long'], ['right', 'top-bottom', 'half'],
    ['left', 'bottom-top', 'half', ['color' => 'zinc', 'text' => ['Fallback'], 'width' => 'long']],
    ['right', 'top-bottom', 'half', ['color' => 'zinc']],
]);

it('applies false label color to the entire lane with or without a text label', function (bool $withText, string $side): void {
    $falseLabel = ['color' => 'zinc'];
    if ($withText) {
        $falseLabel += ['text' => ['Fallback action'], 'badgeColor' => 'rose', 'width' => 'long'];
    }
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="false-lane-test" :path-tone="false" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.flow-if
                id="colored.if"
                color="cyan"
                :side="$side"
                :if-end="$falseLabel"
                :if-start="['text' => ['Action']]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('falseLabel', 'side'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $lines = $xpath->query('//*[starts-with(@data-tw-graph-path, "colored.if.false") and (contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-line ") or contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-arc ") or contains(concat(" ", normalize-space(@class), " "), " tw-graph-protocol-primitive-joint-arrow "))]');
    expect($lines->length)->toBeGreaterThanOrEqual(4);
    foreach ($lines as $line) {
        $colorVariable = str_ends_with($line->getAttribute('data-tw-graph-path'), '.bridge1.bridge-out')
            ? '--tw-graph-protocol-local-to-color-rgb: ' : '--tw-graph-protocol-local-color-rgb: ';
        expect($line->getAttribute('style'))->toContain($colorVariable . \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb('zinc'));
    }
    $labels = $xpath->query('//*[@data-tw-graph-path="colored.if.false.bridge1.label.center.1"]');
    expect($labels->length)->toBe($withText ? 1 : 0);
    if ($withText) {
        expect($labels->item(0)->textContent)->toContain('Fallback action');
        expect($html)->toContain('text-rose-700');
    } else {
        expect($xpath->query('//*[@data-tw-graph-path="colored.if.false.bridge1"]')->length)->toBe(1);
    }
    $get = fn (string $name) => \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('false-lane-test', 'colored.if.' . $name);
    $evaluate = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::class, 'evaluateRemExpression');
    foreach (['x', 'y'] as $axis) {
        expect($evaluate->invoke(null, $get('true.stem.anchorNode-end')[$axis]))->toBe($evaluate->invoke(null, $get('anchorNode-end')[$axis]));
    }
})->with([[false, 'left'], [true, 'left'], [false, 'right'], [true, 'right']]);

it('keeps true and ELSEIF route colors independent from their badges and false lanes', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="branch-colors" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                id="color.binary" color="cyan"
                :if-start="['text' => ['Publish'], 'color' => 'green', 'badgeColor' => 'amber']"
                :if-end="['color' => 'zinc']"
            />
            <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif
                id="color.elseif" color="cyan"
                :if-start="['text' => ['Publish'], 'color' => 'green']"
                :elseifs="[[
                    'conditionLabel' => ['text' => ['ELSEIF revise?'], 'color' => 'violet', 'badgeColor' => 'amber'],
                    'actionLabel' => ['text' => ['Revise']],
                ]]"
                :if-end="['color' => 'zinc']"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    foreach ([
        'color.binary.question.stem.before' => 'cyan',
        'color.binary.true.stem' => 'green',
        'color.binary.true.arc2-south-west' => 'green',
        'color.binary.false.stem' => 'zinc',
        'color.elseif.true.stem' => 'green',
        'color.elseif.elseif.question.stem.before' => 'cyan',
        'color.elseif.elseif.true.stem' => 'green',
        'color.elseif.elseif.false.stem' => 'zinc',
    ] as $id => $color) {
        $nodes = $xpath->query('//*[@data-tw-graph-path="' . $id . '"]');
        expect($nodes->length)->toBe(1);
        expect($nodes->item(0)->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: ' . \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color));
    }
    expect($html)->toContain('text-amber-700');
});

it('connects every multi ELSEIF test only to the preceding False output', function (int $count, string $side, string $direction, ?string $firstColor): void {
    $branches = [];
    for ($i = 1; $i <= $count; $i++) {
        $branches[] = [
            'key' => 'branch-' . $i,
            'beforeLength' => ($i + 5) . 'rem',
            'conditionLabel' => ['text' => ['ELSEIF ' . $i . '?'], 'color' => $i % 2 ? 'violet' : 'blue'],
            'actionLabel' => ['text' => ['Action ' . $i, 'Next line'], 'width' => $i % 2 ? 'long' : 'half', 'color' => 'amber'],
        ];
    }
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="multi-test" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                id="multi" :side="$side" :direction="$direction"
                :elseifs="$branches"
                :if-end="['color' => 'zinc']"
                :if-start="['text' => ['First action'], 'width' => 'half', 'color' => $firstColor]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('branches', 'side', 'direction', 'firstColor'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $anchor = fn (string $id) => \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('multi-test', $id);
    $evaluate = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn (string $value): float => $evaluate->invoke(null, $value);
    $returnRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($firstColor ?? 'zinc');
    $stems = $xpath->query('//*[contains(@class, "tw-graph-protocol-primitive-line") and contains(@data-tw-graph-path, ".true.stem") and not(contains(@data-tw-graph-path, ".node."))]');
    expect($stems->length)->toBe($count + 1);
    foreach ($stems as $stem) {
        expect($stem->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: ' . $returnRgb);
    }
    $previous = 'multi.if';
    for ($i = 1; $i <= $count; $i++) {
        $current = 'multi.elseif.branch-' . $i;
        $before = $xpath->query('//*[@data-tw-graph-path="' . $current . '.question.stem.before"]')->item(0);
        expect($before)->not->toBeNull();
        $rgb = fn (string $color) => \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color);
        expect($before->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: ' . $rgb($i === 1 ? 'zinc' : (($i - 1) % 2 ? 'violet' : 'blue')));
        $after = $xpath->query('//*[@data-tw-graph-path="' . $current . '.question.stem.after"]')->item(0);
        expect($after->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: ' . $rgb($i % 2 ? 'violet' : 'blue'));
        $dot = $xpath->query('//*[@data-tw-graph-path="' . $previous . '.question.stem.after.node.end" and contains(@class, "tw-graph-protocol-primitive-node")]')->item(0);
        expect($dot)->not->toBeNull();
        expect($dot->getAttribute('style'))->toContain('--tw-graph-protocol-z-index: 21');
        expect($before->getAttribute('style'))->toContain('--tw-graph-protocol-z-index: 20');

        foreach (['x', 'y'] as $axis) {
            preg_match('/--tw-graph-protocol-start-' . $axis . ': ([^;]+);/', $before->getAttribute('style'), $coordinate);
            expect($number($coordinate[1]))->toBe($number($anchor($previous . '.question.anchorNode-end')[$axis]));
            expect($number($anchor($previous . '.true.stem.anchorNode-end')[$axis]))->toBe($number($anchor($current . '.true.anchorNode-end')[$axis]));
        }
        $arcName = $side === 'left' ? 'south-west' : 'south-east';
        if ($direction === 'bottom-top') {
            $arc = $xpath->query('//*[@data-tw-graph-path="' . $current . '.true.arc2-' . $arcName . '"]')->item(0);
            expect($arc)->not->toBeNull();
            expect($arc->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: ' . $rgb('amber'));
            $actionDot = $xpath->query('//*[@data-tw-graph-path="' . $current . '.true.arc2-' . $arcName . '.node.end" and contains(@class, "tw-graph-protocol-primitive-node")]')->item(0);
            expect($actionDot)->not->toBeNull();
            expect($actionDot->getAttribute('style'))
                ->toContain('--tw-graph-protocol-local-color-rgb: ' . $rgb('amber'))
                ->toContain('--tw-graph-protocol-z-index: 21');

        }
        $previous = $current;
    }
    foreach (['x', 'y'] as $axis) {
        expect($number($anchor($previous . '.true.stem.anchorNode-end')[$axis]))->toBe($number($anchor('multi.anchorNode-end')[$axis]));
    }
    expect($xpath->query('//*[@data-tw-graph-path="' . $previous . '.false.bridge1"]')->length)->toBe(1);
    expect($html)->not->toContain($previous . '.false.bridge1.label.center.1');
    $counter = $xpath->query('//*[contains(@class, "tw-graph-protocol-primitive-dev-node-counter") and @data-tw-graph-path="' . $previous . '.false.stem.node.end"]')->item(0);
    expect(trim($counter->textContent))->toBe((string) ($count * 2 + 3));
})->with([[1, 'left', 'bottom-top', null], [3, 'right', 'bottom-top', 'green'], [3, 'left', 'top-bottom', 'rose'], [5, 'right', 'top-bottom', null]]);

it('rejects empty multi ELSEIF definitions and duplicate branch keys', function (array $branches, string $message): void {
    expect(fn () => Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph>
            <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi :elseifs="$branches" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, ['branches' => $branches]))->toThrow(\Exception::class, $message);
})->with([[[], 'requires at least one'], [[['key' => 'same'], ['key' => 'same']], 'keys must be nonempty and unique']]);


it('requires exactly one structured elseifs entry for the single ELSEIF variant', function (mixed $elseifs): void {
    expect(fn () => Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="invalid-single-elseif">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif :elseifs="$elseifs" />
        </x-translation-workbench::ui.tw-graph>
    BLADE, ['elseifs' => $elseifs]))->toThrow(Exception::class, 'requires exactly one elseifs entry');
})->with([
    'empty list' => [[]],
    'multiple entries' => [[[], []]],
    'unstructured entry' => [['invalid']],
    'invalid list' => [null],
]);

it('passes DEV mode to action node and question label bounding boxes', function (bool $dev): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="label-boxes" :dev="$dev">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                id="label-boxes.multi"
                :if-start="['text' => ['Publish', 'Notify']]"
                :elseifs="[
                    ['key' => 'changes', 'conditionLabel' => ['text' => ['Changes requested?', 'Review notes']], 'actionLabel' => ['text' => ['Revise']]],
                    ['key' => 'sources', 'conditionLabel' => ['text' => ['Sources missing?', 'Check references']], 'actionLabel' => ['text' => ['Add sources']]],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, ['dev' => $dev]);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    foreach ([
        'label-boxes.multi.if.true.bridge1.label.center.1',
        'label-boxes.multi.elseif.changes.true.anchorNode-end.label-2',
        'label-boxes.multi.elseif.sources.question.label',
    ] as $id) {
        expect($xpath->query('//*[@data-tw-graph-path="' . $id . '"]')->length)->toBe(1);
        expect($xpath->query('//*[@data-tw-graph-path="' . $id . '"]/*[@data-tw-graph-dev-box="' . $id . '.dev-box"]')->length)->toBe($dev ? 1 : 0);
    }
})->with([true, false]);

it('leaves configured action returns open while publishing their join anchors', function (string $variant, string $side, string $direction): void {
    $template = <<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="open-return" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.VARIANT
                id="open" :side="$side" :direction="$direction"
                :if-start="['text' => ['Nested action'], 'return' => false]"
                :elseifs="[
                    ['key' => 'next', 'conditionLabel' => ['text' => ['Next condition?']], 'actionLabel' => ['text' => ['Another nested action'], 'return' => false]],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE;
    $html = Blade::render(str_replace('VARIANT', $variant, $template), compact('side', 'direction'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $prefixes = match ($variant) {
        'flow-if-elseif-multi' => ['open.if', 'open.elseif.next'],
        'flow-if-elseif' => ['open', 'open.elseif'],
        default => ['open'],
    };
    foreach ($prefixes as $prefix) {
        expect($xpath->query('//*[@data-tw-graph-path="' . $prefix . '.true.stem"]')->length)->toBe(0);
        expect(\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('open-return', $prefix . '.true.anchorNode-end'))->not->toBeNull();
        expect(\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('open-return', $prefix . '.true.anchorNode-return'))->not->toBeNull();
    }
    expect(\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('open-return', 'open.anchorNode-end'))->not->toBeNull();
})->with(['flow-if', 'flow-if-else', 'flow-if-elseif', 'flow-if-elseif-multi'])
    ->with(['left', 'right'])->with(['bottom-top', 'top-bottom']);

it('honors local if-end stem length across IF variants and keeps their outputs joined', function (string $variant, ?string $length, bool $withText, string $direction): void {
    $ifEnd = ['stemLength' => $length, 'color' => 'zinc'];
    if ($withText) {
        $ifEnd['text'] = ['Fallback', 'Second line'];
    }
    $template = <<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="local-end-stem">
            <x-translation-workbench::ui.tw-graph.strang.VARIANT
                id="local" :direction="$direction" stem-length="8rem"
                :if-start="['text' => ['Action']]" :if-end="$ifEnd"
                :elseifs="[['key' => 'next', 'conditionLabel' => ['text' => ['Next?']], 'actionLabel' => ['text' => ['Next action']]]]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE;
    $html = Blade::render(str_replace('VARIANT', $variant, $template), compact('ifEnd', 'direction'));
    $prefix = match ($variant) {
        'flow-if-elseif-multi' => 'local.elseif.next',
        'flow-if-elseif' => 'local.elseif',
        default => 'local',
    };
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $stem = $xpath->query('//*[@data-tw-graph-path="' . $prefix . '.false.stem"]')->item(0);
    preg_match('/--tw-graph-protocol-local-length: ([^;]+);/', $stem->getAttribute('style'), $match);
    $evaluate = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn (string $value): float => $evaluate->invoke(null, $value);
    $actual = $number($match[1]);
    if ($length === '0rem') {
        expect($actual)->toBeGreaterThan(0.0)->toBeLessThan(8.0);
    } else {
        expect($actual)->toBe($length === null ? 8.0 : 12.0);
    }
    $get = fn (string $suffix) => \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('local-end-stem', $prefix . '.' . $suffix);
    foreach (['x', 'y'] as $axis) {
        expect($number($get('true.stem.anchorNode-end')[$axis]))->toBe($number($get('anchorNode-end')[$axis]));
    }
})->with(['flow-if', 'flow-if-else', 'flow-if-elseif', 'flow-if-elseif-multi', 'flow-if-ternary'])
    ->with([[null, false, 'bottom-top'], ['12rem', false, 'top-bottom'], ['12rem', true, 'bottom-top'], ['0rem', true, 'top-bottom']]);

it('keeps earlier actions off a later open ELSEIF output in either orientation', function (string $side, string $direction, bool $lastOpen): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="later-nested">
            <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                id="outer" :side="$side" :direction="$direction"
                :if-start="['text' => ['First action']]"
                :elseifs="[
                    ['key' => 'nested', 'conditionLabel' => ['text' => ['Nested?']], 'actionLabel' => ['text' => ['Nested action'], 'return' => false, 'returnOffset' => '9rem']],
                    ['key' => 'last', 'conditionLabel' => ['text' => ['Last?']], 'actionLabel' => ['text' => ['Last action'], 'return' => ! $lastOpen]],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'direction', 'lastOpen'));
    $get = fn (string $suffix) => \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('later-nested', 'outer.' . $suffix);
    $evaluate = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn (string $value): float => $evaluate->invoke(null, $value);
    $target = $get($lastOpen ? 'anchorNode-end' : 'elseif.last.true.anchorNode-end');
    foreach (['x', 'y'] as $axis) {
        expect($number($get('if.true.stem.anchorNode-end')[$axis]))->toBe($number($target[$axis]));
        expect($number($get('elseif.nested.true.anchorNode-return')[$axis]))->toBe($number($target[$axis]));
    }
    $offset = $number($get('elseif.nested.true.anchorNode-end')['x']) - $number($get('if.true.anchorNode-end')['x']);
    expect($offset)->toBe($side === 'left' ? -9.0 : 9.0);
    expect($html)->not->toContain('data-tw-graph-path="outer.elseif.nested.true.stem"');
})->with(['left', 'right'])->with(['bottom-top', 'top-bottom'])->with([false, true]);
