<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Arc segments') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Four arc shapes and their reverse traversal, shown in two rows. Each example uses an individually authored segment array. A labeled start dot and an end joint arrow indicate the direction. Reversing an arc swaps both the semantic anchors and their coordinates; the joint arrow direction must match the outgoing tangent. Coordinates must agree with arcSize.') }}
        </flux:callout.text>
        @php
            $arcExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-arc',
            );
            $arcWNCode = $arcExampleSource->example('segment-arc-w-n');
            $arcENCode = $arcExampleSource->example('segment-arc-e-n');
            $arcSWCode = $arcExampleSource->example('segment-arc-s-w');
            $arcSECode = $arcExampleSource->example('segment-arc-s-e');
            $arcNWCode = $arcExampleSource->example('segment-arc-n-w');
            $arcNECode = $arcExampleSource->example('segment-arc-n-e');
            $arcWSCode = $arcExampleSource->example('segment-arc-w-s');
            $arcESCode = $arcExampleSource->example('segment-arc-e-s');
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >w → n</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $arcWNCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >e → n</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $arcENCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >s → w</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $arcSWCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >s → e</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $arcSECode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >n → w</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $arcNWCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >n → e</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $arcNECode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >w → s</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $arcWSCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >e → s</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $arcESCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Arc segment props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop / segment key') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:segment
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Component prop
                            containing the arc configuration. The following rows describe keys inside this array.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Component prop;
                            overrides segment.dev.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">segment.arc
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the arc
                            and its child elements.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">startAnchor
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">n</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Semantic start: n, e,
                            s, or w. Use one of the eight adjacent pairs shown here.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">endAnchor
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">w</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Semantic end. Swap this
                            with startAnchor and swap coordinates to reverse traversal.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchorStart
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt;
                            &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Explicit starting
                            coordinates; must match the semantic anchor and arc size.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchorEnd
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt;
                            &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Explicit endpoint
                            coordinates; not calculated by this segment.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arcSize
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">canvas arc-size
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Arc dimensions. These
                            examples use 2.75rem.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">nodeStart
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable the start
                            anchor, its optional label and DEV counter.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">nodeEnd
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable the end anchor,
                            its optional label and DEV counter.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">nodeStartDot
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">follows nodeStart
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Control the visual
                            start dot independently of the enabled anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">nodeEndDot
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">follows nodeEnd
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Set false to use an end
                            joint arrow instead of a dot.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">nodeStartSize
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional start-dot
                            size; otherwise inherited from the canvas.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">nodeEndSize
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional end-dot size;
                            otherwise inherited from the canvas.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">jointArrowStart
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Show a start joint
                            arrow when nodeStart is true and nodeStartDot is false.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">jointArrowEnd
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Show an end joint arrow
                            when nodeEnd is true and nodeEndDot is false.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">
                            jointArrowStartDirection</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">right</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Explicit arrow
                            direction: top, bottom, left, or right.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">jointArrowEndDirection
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">right</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Match the outgoing
                            tangent. It is not inferred from the arc anchors.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">startLabel
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">unset</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Label at anchorStart,
                            shown when nodeStart is true and text is present. Uses segments.label with a connector.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">endLabel
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">unset</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Label at anchorEnd,
                            shown when nodeEnd is true and text is present.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">startLabel.side
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">right
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Position relative to
                            the start anchor: top, bottom, left, or right.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">endLabel.side
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Position relative to
                            the end anchor: top, bottom, left, or right.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">startLabel.width
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">default
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Label width: half,
                            default, halfLong, or long.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">endLabel.width
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">default
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Label width: half,
                            default, halfLong, or long.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Arc, dot, arrow and
                            default label color.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">toColor
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional second arc
                            color.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dashed
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Draw a dashed arc.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">tone</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">line</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Palette tone.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zIndex
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional
                            stacking-order override.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Diagnostic boxes and
                            counters inside the segment configuration; overridden by :dev.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout
        class="min-w-0"
        color="zinc"
        icon="eye"
    >
        <flux:callout.heading>{{ __('Arc segment preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div class="mt-4 overflow-x-auto">
                <div class="grid min-w-[48rem] grid-cols-4 gap-4">
                    <div class="min-w-0">
                        <flux:heading size="sm">w → n</flux:heading>
                        <div
                            class="mt-3 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
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
                                        'arcSize' => '2.75rem',
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
                                    :dev="true"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-w-n:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <flux:heading size="sm">e → n</flux:heading>
                        <div
                            class="mt-3 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
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
                                        'arcSize' => '2.75rem',
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
                                    :dev="true"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-e-n:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <flux:heading size="sm">s → w</flux:heading>
                        <div
                            class="mt-3 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
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
                                        'arcSize' => '2.75rem',
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
                                    :dev="true"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-s-w:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <flux:heading size="sm">s → e</flux:heading>
                        <div
                            class="mt-3 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
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
                                        'arcSize' => '2.75rem',
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
                                    :dev="true"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-s-e:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <flux:heading size="sm">n → w</flux:heading>
                        <div
                            class="mt-3 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
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
                                        'arcSize' => '2.75rem',
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
                                    :dev="true"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-n-w:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <flux:heading size="sm">n → e</flux:heading>
                        <div
                            class="mt-3 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
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
                                        'arcSize' => '2.75rem',
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
                                    :dev="true"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-n-e:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <flux:heading size="sm">w → s</flux:heading>
                        <div
                            class="mt-3 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
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
                                        'arcSize' => '2.75rem',
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
                                    :dev="true"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-w-s:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <flux:heading size="sm">e → s</flux:heading>
                        <div
                            class="mt-3 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
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
                                        'arcSize' => '2.75rem',
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
                                    :dev="true"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-arc-e-s:end --}}
                        </div>
                    </div>
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/segments/segments-arc.blade.php
        </flux:field>
    </flux:callout>
</section>
