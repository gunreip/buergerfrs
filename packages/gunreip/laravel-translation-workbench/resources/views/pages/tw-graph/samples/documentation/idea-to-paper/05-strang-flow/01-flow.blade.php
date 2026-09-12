{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/05-strang-flow/01-flow.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
    $graphId = $ideaToPaperGraphId ?? 'idea-to-paper-step-08-flow';
    $renderMode = $renderMode ?? 'documentation';
    $flowProps = [
        [
            'name' => 'id',
            'default' => 'auto id',
            'keys' => '',
            'effect' =>
                'Stable element prefix for every flow strand, including labels, DEV identifiers, bounds, and attach targets.',
        ],
        [
            'name' => 'component-counter',
            'default' => '1',
            'keys' => '',
            'effect' => 'Optional DEV/component counter value used by flow-start, flow-step, and flow-decision.',
        ],
        [
            'name' => 'direction',
            'default' => 'bottom-top',
            'keys' => '',
            'effect' =>
                'Main flow direction. The first flow examples stay vertical; later decision branches can turn left or right.',
        ],
        [
            'name' => 'attach-to',
            'default' => 'null',
            'keys' => '',
            'effect' => 'Existing anchor id to continue from. Used by flow-step, flow-decision, flow-if-* and if-else-endif.',
        ],
        [
            'name' => ':anchor-start',
            'default' => "['x' => '0rem', 'y' => '0rem']",
            'keys' => 'x, y',
            'effect' => 'Manual start coordinate when no attach-to anchor is used.',
        ],
        [
            'name' => 'color',
            'default' => 'inherited graph color / zinc',
            'keys' => '',
            'effect' => 'Flow color inherited from tw-graph unless the flow component overrides it.',
        ],
        [
            'name' => 'z-index',
            'default' => '20',
            'keys' => '',
            'effect' => 'Layer order for the flow component and its segments.',
        ],
        [
            'name' => 'dev-mode',
            'default' => 'inherited :dev',
            'keys' => '',
            'effect' => 'Optional local DEV override for counters and debug helpers.',
        ],
        [
            'name' => 'dev-counter-color',
            'default' => 'zinc / inherited',
            'keys' => '',
            'effect' => 'Badge color for DEV node counters where the component exposes it.',
        ],
        [
            'name' => ':start-label',
            'default' => 'null',
            'keys' => 'text, side, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines',
            'effect' =>
                'Flow-start label that names the entry point, for example a process start, first milestone, or initial state.',
        ],
        [
            'name' => ':start-node-labels',
            'default' => '[]',
            'keys' => 'left, right -> text, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines',
            'effect' =>
                'Optional left/right facts at the flow-start end anchor, using the same text-label structure as trunk and branch labels.',
        ],
        [
            'name' => 'node-label-left / node-label-right',
            'default' => 'null',
            'keys' => 'text, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines',
            'effect' => 'Shorthand single-side labels for flow-start when a full start-node-labels array would be too much.',
        ],
        [
            'name' => 'start-length / length',
            'default' => 'stem-length',
            'keys' => '',
            'effect' => 'Length of the first visible flow-start stem before the first anchor node.',
        ],
        [
            'name' => 'node-end / node-end-dot',
            'default' => 'true / null',
            'keys' => '',
            'effect' => 'Controls whether the end anchor exists and whether its visible dot is rendered.',
        ],
        [
            'name' => ':node-image',
            'default' => 'null',
            'keys' => 'src, size, alt, color, zIndex',
            'effect' => 'Optional image marker at a flow-start node, following the generic tw-graph node image structure.',
        ],
        [
            'name' => 'before-length / after-length',
            'default' => '2rem / 2rem',
            'keys' => '',
            'effect' =>
                'Lengths around a flow-step label. The label gap is calculated from the step label height unless label-gap is set explicitly.',
        ],
        [
            'name' => 'label-gap',
            'default' => 'graph label-gap / calculated',
            'keys' => '',
            'effect' => 'Explicit gap around a flow-step label; otherwise the step uses the label height.',
        ],
        [
            'name' => ':step-label',
            'default' => 'null',
            'keys' => 'text, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines',
            'effect' => 'Centered label that names the process step, status, or decision reason.',
        ],
        [
            'name' => ':node-labels',
            'default' => '[]',
            'keys' => 'end -> left/right -> text, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines',
            'effect' =>
                'Optional facts at the flow-step or flow-decision end anchors. For flow-step the public anchor is usually end.',
        ],
        [
            'name' => 'step-caps / cap-length',
            'default' => 'true / graph cap-length',
            'keys' => '',
            'effect' => 'Controls the small caps around a flow-step label and their length.',
        ],
        [
            'name' => 'arc-radius / arc-size',
            'default' => 'graph arc-size',
            'keys' => '',
            'effect' => 'Radius/size of flow-decision and flow-if arcs. arc-size is the current canonical prop.',
        ],
        [
            'name' => 'bridge-length',
            'default' => 'graph bridge-length / label-bridge minimum',
            'keys' => '',
            'effect' => 'Common bridge length for flow-decision and the flow-if label bridges unless a more specific bridge prop overrides it.',
        ],
        [
            'name' => 'left-bridge-length / right-bridge-length',
            'default' => 'bridge-length',
            'keys' => '',
            'effect' => 'Side-specific bridge lengths for flow-decision.',
        ],
        [
            'name' => 'extension / left-extension / right-extension',
            'default' => '0rem',
            'keys' => '',
            'effect' => 'Optional additional reach for flow-decision branches.',
        ],
        [
            'name' => ':decision-label',
            'default' => 'null',
            'keys' => 'text, side, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines',
            'effect' =>
                'Centered label at the decision anchor. The flow continues only through the left/right sideways branches.',
        ],
        [
            'name' => 'decision-label-side',
            'default' => 'top',
            'keys' => '',
            'effect' => 'Default side for a flow-decision label when the label itself does not set side.',
        ],
        [
            'name' => 'path-tone',
            'default' => 'surface',
            'keys' => '',
            'effect' => 'Tone used by flow-if paths, arcs, and label bridges.',
        ],
        [
            'name' => 'start-bridge-length / condition-bridge-length / end-bridge-length',
            'default' => 'bridge-length',
            'keys' => '',
            'effect' => 'Specific bridge lengths for the if-else-endif wrapper: intro, condition rows, and ENDIF.',
        ],
        [
            'name' => 'bypass',
            'default' => 'false',
            'keys' => '',
            'effect' => 'Connects the IF entry rail directly to the ENDIF output when the condition is not met.',
        ],
        [
            'name' => 'then-arc-reach',
            'default' => 'arc-size',
            'keys' => '',
            'effect' => 'Horizontal reach of the THEN/output arc in a flow-if condition row.',
        ],
        [
            'name' => 'left-stem-length / then-stem-length',
            'default' => 'stem-length',
            'keys' => '',
            'effect' => 'Vertical lengths for the ELSE/next-condition rail and the THEN/output continuation.',
        ],
        [
            'name' => 'left-stem',
            'default' => 'true',
            'keys' => '',
            'effect' => 'Enables or suppresses the left rail continuation in a flow-if condition row.',
        ],
        [
            'name' => 'then-continuation',
            'default' => 'stem',
            'keys' => 'stem, arc-east-north, none',
            'effect' => 'Defines how the THEN/output side continues after the condition label.',
        ],
        [
            'name' => ':intro-label',
            'default' => "['text' => ['IF / ELSE flow', 'section starts']]",
            'keys' => 'text, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines',
            'effect' => 'Opening label for flow-if-start or the if-else-endif wrapper.',
        ],
        [
            'name' => ':if-condition-label',
            'default' => 'null',
            'keys' => 'text, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines',
            'effect' => 'Label for the first IF condition row inside flow-if-condition-set or if-else-endif.',
        ],
        [
            'name' => ':elseif-conditions / :conditions',
            'default' => '[]',
            'keys' => 'key, id, color, label, conditionLabel, conditionRailWidth, arcSize, bridgeLength, thenArcReach, leftStemLength, thenStemLength, leftStem, thenContinuation, pathTone, zIndex, devMode, devCounterColor',
            'effect' => 'Rows after the first IF row. Each entry may be ELSEIF, ELSE, DEFAULT, or any handmade condition label.',
        ],
        [
            'name' => 'condition-rail-width',
            'default' => 'widest condition label width',
            'keys' => '',
            'effect' => 'Shared label width for IF/ELSEIF/ELSE rows so the condition rail remains aligned.',
        ],
        [
            'name' => ':end-label',
            'default' => "['text' => ['ENDIF']]",
            'keys' => 'text, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines',
            'effect' => 'Closing label rendered by flow-if-end or the if-else-endif wrapper.',
        ],
        [
            'name' => 'if-id / elseif-id',
            'default' => 'derived from id',
            'keys' => '',
            'effect' => 'Optional stable id prefixes for the generated IF and ELSEIF condition rows.',
        ],
    ];
@endphp

@if ($renderMode === 'documentation')
    <section
        class="grid gap-4 lg:grid-cols-2"
        x-data="{ flowVariant: 'start', flowIfVariant: 'if' }"
    >
        <flux:callout
            color="cyan"
            icon="workflow"
        >
            <flux:callout.heading>
                {{ __('8. Flow') }}
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('A flow strand is intended for process-like graphs: steps, decisions, side paths, joins, and returns. It stays close to the existing strang layer, but the language is neutral enough for business processes, documentation flows, and programming-like control structures.') }}
            </flux:callout.text>

            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow._flow-code-tabs', [
                'flowProps' => $flowProps,
            ])

        </flux:callout>

        <flux:callout
            color="zinc"
            icon="square-dashed-text"
        >
            <flux:callout.heading>
                <span class="flex w-full flex-wrap items-center justify-between gap-3">
                    <span>{{ __('Step 8 preview') }}</span>
                    <flux:badge
                        size="sm"
                        color="cyan"
                        x-show="flowVariant === 'start'"
                    >
                        {{ __('flow start') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="sky"
                        x-show="flowVariant === 'step'"
                    >
                        {{ __('flow step') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="emerald"
                        x-show="flowVariant === 'decision'"
                    >
                        {{ __('flow decision') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="amber"
                        x-show="flowVariant === 'branchSteps'"
                    >
                        {{ __('flow branch steps') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="fuchsia"
                        x-show="flowVariant === 'if'"
                    >
                        {{ __('flow if') }}
                    </flux:badge>
                </span>
            </flux:callout.heading>

            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow._flow-preview-variants', [
                'dev' => $dev,
                'coordinates' => $coordinates,
                'graphId' => $graphId,
                'renderMode' => $renderMode,
            ])
        </flux:callout>
    </section>
@endif
