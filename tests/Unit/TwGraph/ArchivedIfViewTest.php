<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

// Archive regression coverage only; not a public authoring API.

it('keeps flow elseif group width alignment quiet by normalizing condition labels', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="flow-if-elseif-group-width-test" :dev="true" :coordinates="false" color="cyan">
            <x-translation-workbench::ui.tw-graph.strang._old.flow-if-elseif-group
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
            <x-translation-workbench::ui.tw-graph.strang._old.flow-if-condition-set
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

            <x-translation-workbench::ui.tw-graph.strang._old.flow-if-end
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
            <x-translation-workbench::ui.tw-graph.strang._old.flow-if-condition-set
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
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 6 182 212;[^>]*--tw-graph-protocol-local-to-color-rgb: 245 158 11;[^>]*data-tw-graph-path="sample\.flow\.1\.elseif\.amber\.bridge-in"/s')
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 245 158 11;[^>]*--tw-graph-protocol-local-to-color-rgb: 6 182 212;[^>]*data-tw-graph-path="sample\.flow\.1\.elseif\.amber\.bridge-out"/s')
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 6 182 212;[^>]*data-tw-graph-path="sample\.flow\.1\.elseif\.amber\.bridge-out\.end\.joint-arrow"/s')
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 6 182 212;[^>]*data-tw-graph-path="sample\.flow\.1\.elseif\.amber\.arc-west-north"/s')
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 6 182 212;[^>]*data-tw-graph-path="sample\.flow\.1\.elseif\.amber\.arc-south-east"/s')
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 6 182 212;[^>]*data-tw-graph-path="sample\.flow\.1\.elseif\.amber\.stem"/s')
        ->toMatch('/--tw-graph-protocol-local-color-rgb: 6 182 212;[^>]*data-tw-graph-path="sample\.flow\.1\.elseif\.amber\.arc-south-east\.stem"/s');
});

it('keeps flow if start condition set and end bridge lengths within visible bounds', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="flow-if-bridge-minimum-test" :dev="true" :coordinates="false" color="cyan">
            <x-translation-workbench::ui.tw-graph.strang._old.flow-if-start
                id="sample.flow.1.if-start"
                bridge-length="0.1rem"
                :intro-label="['text' => ['IF'], 'width' => 'half']"
            />

            <x-translation-workbench::ui.tw-graph.strang._old.flow-if-condition-set
                id="sample.flow.1.conditions-min"
                if-id="sample.flow.1.if-min"
                attach-to="sample.flow.1.if-start.anchorNode-end"
                bridge-length="0.1rem"
                :if-condition-label="['text' => ['IF min'], 'width' => 'half']"
            />

            <x-translation-workbench::ui.tw-graph.strang._old.flow-if-condition-set
                id="sample.flow.1.conditions-max"
                if-id="sample.flow.1.if-max"
                attach-to="sample.flow.1.conditions-min.then.anchorNode-end"
                bridge-length="24rem"
                :if-condition-label="['text' => ['IF max'], 'width' => 'half']"
            />

            <x-translation-workbench::ui.tw-graph.strang._old.flow-if-condition-set
                id="sample.flow.1.conditions-render-min"
                if-id="sample.flow.1.if-render-min"
                attach-to="sample.flow.1.conditions-max.then.anchorNode-end"
                bridge-length="0.35rem"
                :if-condition-label="['text' => ['IF render min'], 'width' => 'half']"
            />

            <x-translation-workbench::ui.tw-graph.strang._old.flow-if-end
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
            <x-translation-workbench::ui.tw-graph.strang._old.if-else-endif
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

it('positions right IF sections opposite the left geometry while preserving height and readable labels', function (): void {
    foreach (['left', 'right'] as $side) {
        $html = Blade::render(<<<'BLADE'
            <x-translation-workbench::ui.tw-graph :graph-id="'if-side-' . $side" :dev="true">
                <x-translation-workbench::ui.tw-graph.strang._old.if-else-endif
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
        expect($xpath->query('//*[@data-tw-graph-path="side-test.start.' . $arc . '.end.joint-arrow" and contains(@class, "joint-arrow-' . $direction . '")]')->length)->toBe(1);
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
            <x-translation-workbench::ui.tw-graph.strang._old.if-else-endif
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
        expect($xpath->query('//*[@data-tw-graph-path="label-side.' . $last . '.then.' . $turn . '.end.joint-arrow" and contains(@class, "joint-arrow-' . $direction . '")]')->length)->toBe(1);
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
            <x-translation-workbench::ui.tw-graph.strang._old.if-else-endif
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
