<x-translation-workbench::ui.common.heading-counter-group group="segments-arc">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">

        {{-- CodeBox And Props Table --}}
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Arc segments') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Four arc shapes and their reverse traversal, shown in eight individual previews. Each example uses an individually authored segment array. A labeled start dot and an end joint arrow indicate the direction. Reversing an arc swaps both the semantic anchors and their coordinates; the joint arrow direction must match the outgoing tangent. Coordinates must agree with arcRadius.') }}
            </flux:callout.text>
            @php
                $arcExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-arc',
                );
            @endphp
            <flux:separator
                class="mt-4"
                :text="__('Code examples')"
            />
            <flux:accordion
                transition
                exclusive
            >
                <flux:accordion.item expanded>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="w-n"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('w → n') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $arcExampleSource->example('segment-arc-w-n') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="e-n"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('e → n') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $arcExampleSource->example('segment-arc-e-n') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="s-w"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('s → w') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $arcExampleSource->example('segment-arc-s-w') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="s-e"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('s → e') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $arcExampleSource->example('segment-arc-s-e') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="n-w"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('n → w') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $arcExampleSource->example('segment-arc-n-w') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="n-e"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('n → e') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $arcExampleSource->example('segment-arc-n-e') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="w-s"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('w → s') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $arcExampleSource->example('segment-arc-w-s') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="e-s"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('e → s') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $arcExampleSource->example('segment-arc-e-s') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Arc segment props') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    {{-- Table --}}
                    <flux:table container:class="max-h-80">
                        <flux:table.columns
                            class="bg-white dark:bg-zinc-900"
                            sticky
                        >
                            <flux:table.column>{{ __('Prop / segment key') }}</flux:table.column>
                            <flux:table.column>{{ __('Default') }}</flux:table.column>
                            <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:segment</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Component prop containing the arc configuration. The following rows describe keys inside this array.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}

                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>segment.arc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the arc and its child elements.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>startAnchor</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>n</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Semantic start: n, e, s, or w. Use one of the eight adjacent pairs shown here.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>endAnchor</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>w</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Semantic end. Swap this with startAnchor and swap coordinates to reverse traversal.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchorStart</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicit starting coordinates; must match the semantic anchor and arc size.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchorEnd</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicit endpoint coordinates; not calculated by this segment.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>arcRadius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>canvas arc-radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Arc dimensions. These examples use 2.75rem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>nodeStart</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Enable the start anchor, its optional label and DEV counter.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>nodeEnd</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Enable the end anchor, its optional label and DEV counter.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>nodeStartDot</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>follows nodeStart</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Control the visual start dot independently of the enabled anchor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>nodeEndDot</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>follows nodeEnd</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Set false to use an end joint arrow instead of a dot.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>nodeStartSize</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional start-dot size; otherwise inherited from the canvas.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>nodeEndSize</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional end-dot size; otherwise inherited from the canvas.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>jointArrowStart</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Show a start joint arrow when nodeStart is true and nodeStartDot is false.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>jointArrowEnd</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Show an end joint arrow when nodeEnd is true and nodeEndDot is false.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>jointArrowStartDirection</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>right</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicit arrow direction: top, bottom, left, or right.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>jointArrowEndDirection</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>right</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Match the outgoing tangent. It is not inferred from the arc anchors.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>startLabel</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>unset</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Label at anchorStart, shown when nodeStart is true and text is present. Uses segments.label with a connector.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>endLabel</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>unset</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Label at anchorEnd, shown when nodeEnd is true and text is present.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>startLabel.side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>right</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Position relative to the start anchor: top, bottom, left, or right.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>endLabel.side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>left</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Position relative to the end anchor: top, bottom, left, or right.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>startLabel.width</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>default</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Label width: half, default, halfLong, or long.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>endLabel.width</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>default</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Label width: half, default, halfLong, or long.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Arc, dot, arrow and default label color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>toColor</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional second arc color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>dashed</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Draw a dashed arc.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>tone</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>line</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Palette tone.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>zIndex</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional stacking-order override.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}

                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Arc segment preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2">
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="w-n"
                            size="sm"
                        >
                            {{ __('w → n') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-arc-w-n:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-segments-arc-w-n"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="15rem"
                                horizontal-padding="7.5rem"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.arc
                                    :segment="[
                                        'id' => 'literature.segments.arc.w-n',
                                        'startAnchor' => 'w',
                                        'endAnchor' => 'n',
                                        'anchorStart' => ['x' => '-1.375rem', 'y' => '6rem'],
                                        'anchorEnd' => ['x' => '1.375rem', 'y' => '8.75rem'],
                                        'arcRadius' => '2.75rem',
                                        'nodeStart' => true,
                                        'nodeEnd' => true,
                                        'nodeEndDot' => false,
                                        'jointArrowEnd' => true,
                                        'jointArrowEndDirection' => 'right',
                                        'startLabel' => [
                                            'text' => ['Start'],
                                            'side' => 'bottom',
                                            'width' => 'half',
                                            'align' => 'center',
                                        ],
                                        'color' => 'cyan',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-w-n:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="e-n"
                            size="sm"
                        >
                            {{ __('e → n') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-arc-e-n:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-segments-arc-e-n"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="15rem"
                                horizontal-padding="7.5rem"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.arc
                                    :segment="[
                                        'id' => 'literature.segments.arc.e-n',
                                        'startAnchor' => 'e',
                                        'endAnchor' => 'n',
                                        'anchorStart' => ['x' => '1.375rem', 'y' => '6rem'],
                                        'anchorEnd' => ['x' => '-1.375rem', 'y' => '8.75rem'],
                                        'arcRadius' => '2.75rem',
                                        'nodeStart' => true,
                                        'nodeEnd' => true,
                                        'nodeEndDot' => false,
                                        'jointArrowEnd' => true,
                                        'jointArrowEndDirection' => 'left',
                                        'startLabel' => [
                                            'text' => ['Start'],
                                            'side' => 'bottom',
                                            'width' => 'half',
                                            'align' => 'center',
                                        ],
                                        'color' => 'red',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-e-n:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="s-w"
                            size="sm"
                        >
                            {{ __('s → w') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-arc-s-w:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-segments-arc-s-w"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="15rem"
                                horizontal-padding="7.5rem"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.arc
                                    :segment="[
                                        'id' => 'literature.segments.arc.s-w',
                                        'startAnchor' => 's',
                                        'endAnchor' => 'w',
                                        'anchorStart' => ['x' => '1.375rem', 'y' => '6rem'],
                                        'anchorEnd' => ['x' => '-1.375rem', 'y' => '8.75rem'],
                                        'arcRadius' => '2.75rem',
                                        'nodeStart' => true,
                                        'nodeEnd' => true,
                                        'nodeEndDot' => false,
                                        'jointArrowEnd' => true,
                                        'jointArrowEndDirection' => 'top',
                                        'startLabel' => [
                                            'text' => ['Start'],
                                            'side' => 'bottom',
                                            'width' => 'half',
                                            'align' => 'center',
                                        ],
                                        'color' => 'green',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-s-w:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="s-e"
                            size="sm"
                        >
                            {{ __('s → e') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-arc-s-e:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-segments-arc-s-e"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="15rem"
                                horizontal-padding="7.5rem"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.arc
                                    :segment="[
                                        'id' => 'literature.segments.arc.s-e',
                                        'startAnchor' => 's',
                                        'endAnchor' => 'e',
                                        'anchorStart' => ['x' => '-1.375rem', 'y' => '6rem'],
                                        'anchorEnd' => ['x' => '1.375rem', 'y' => '8.75rem'],
                                        'arcRadius' => '2.75rem',
                                        'nodeStart' => true,
                                        'nodeEnd' => true,
                                        'nodeEndDot' => false,
                                        'jointArrowEnd' => true,
                                        'jointArrowEndDirection' => 'top',
                                        'startLabel' => [
                                            'text' => ['Start'],
                                            'side' => 'bottom',
                                            'width' => 'half',
                                            'align' => 'center',
                                        ],
                                        'color' => 'yellow',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-s-e:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="n-w"
                            size="sm"
                        >
                            {{ __('n → w') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-arc-n-w:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-segments-arc-n-w"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="15rem"
                                horizontal-padding="7.5rem"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.arc
                                    :segment="[
                                        'id' => 'literature.segments.arc.n-w',
                                        'startAnchor' => 'n',
                                        'endAnchor' => 'w',
                                        'anchorStart' => ['x' => '1.375rem', 'y' => '6.75rem'],
                                        'anchorEnd' => ['x' => '-1.375rem', 'y' => '4rem'],
                                        'arcRadius' => '2.75rem',
                                        'nodeStart' => true,
                                        'nodeEnd' => true,
                                        'nodeEndDot' => false,
                                        'jointArrowEnd' => true,
                                        'jointArrowEndDirection' => 'bottom',
                                        'startLabel' => [
                                            'text' => ['Start'],
                                            'side' => 'top',
                                            'width' => 'half',
                                            'align' => 'center',
                                        ],
                                        'color' => 'fuchsia',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-n-w:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="n-e"
                            size="sm"
                        >
                            {{ __('n → e') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-arc-n-e:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-segments-arc-n-e"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="15rem"
                                horizontal-padding="7.5rem"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.arc
                                    :segment="[
                                        'id' => 'literature.segments.arc.n-e',
                                        'startAnchor' => 'n',
                                        'endAnchor' => 'e',
                                        'anchorStart' => ['x' => '-1.375rem', 'y' => '6.75rem'],
                                        'anchorEnd' => ['x' => '1.375rem', 'y' => '4rem'],
                                        'arcRadius' => '2.75rem',
                                        'nodeStart' => true,
                                        'nodeEnd' => true,
                                        'nodeEndDot' => false,
                                        'jointArrowEnd' => true,
                                        'jointArrowEndDirection' => 'bottom',
                                        'startLabel' => [
                                            'text' => ['Start'],
                                            'side' => 'top',
                                            'width' => 'half',
                                            'align' => 'center',
                                        ],
                                        'color' => 'rose',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-n-e:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="w-s"
                            size="sm"
                        >
                            {{ __('w → s') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-arc-w-s:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-segments-arc-w-s"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="15rem"
                                horizontal-padding="7.5rem"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.arc
                                    :segment="[
                                        'id' => 'literature.segments.arc.w-s',
                                        'startAnchor' => 'w',
                                        'endAnchor' => 's',
                                        'anchorStart' => ['x' => '-1.375rem', 'y' => '6.75rem'],
                                        'anchorEnd' => ['x' => '1.375rem', 'y' => '4rem'],
                                        'arcRadius' => '2.75rem',
                                        'nodeStart' => true,
                                        'nodeEnd' => true,
                                        'nodeEndDot' => false,
                                        'jointArrowEnd' => true,
                                        'jointArrowEndDirection' => 'right',
                                        'startLabel' => [
                                            'text' => ['Start'],
                                            'side' => 'top',
                                            'width' => 'half',
                                            'align' => 'center',
                                        ],
                                        'color' => 'blue',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-w-s:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="e-s"
                            size="sm"
                        >
                            {{ __('e → s') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-arc-e-s:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-segments-arc-e-s"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="15rem"
                                horizontal-padding="7.5rem"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.arc
                                    :segment="[
                                        'id' => 'literature.segments.arc.e-s',
                                        'startAnchor' => 'e',
                                        'endAnchor' => 's',
                                        'anchorStart' => ['x' => '1.375rem', 'y' => '6.75rem'],
                                        'anchorEnd' => ['x' => '-1.375rem', 'y' => '4rem'],
                                        'arcRadius' => '2.75rem',
                                        'nodeStart' => true,
                                        'nodeEnd' => true,
                                        'nodeEndDot' => false,
                                        'jointArrowEnd' => true,
                                        'jointArrowEndDirection' => 'left',
                                        'startLabel' => [
                                            'text' => ['Start'],
                                            'side' => 'top',
                                            'width' => 'half',
                                            'align' => 'center',
                                        ],
                                        'color' => 'violet',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-e-s:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/segments/segments-arc.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
