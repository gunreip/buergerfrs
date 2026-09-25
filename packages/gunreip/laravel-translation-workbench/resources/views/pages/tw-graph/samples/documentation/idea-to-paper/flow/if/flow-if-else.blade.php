<x-translation-workbench::ui.common.heading-counter-group group="flow-if-else">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('IF ELSE') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.if.flow-if-else" />
            <flux:callout.text>
                {{ __('An IF ELSE first evaluates its question inside a step. True executes Publish paper; False executes Revise draft. Continue process follows the common output. The shared output splits into two stacked action bridges pointing in the same direction. True routes sideways first and then follows a stem; False follows its stem first and then routes sideways. Both paths meet at one end anchor. Per execution only one branch is taken. Node labels remain optional explanations; the question and results are embedded in the path.') }}
            </flux:callout.text>
            @php
                $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-else',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="flow-if-else-example-1"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $docExampleSource->example('flow-if-else-example-1') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="flow-if-else-example-2"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $docExampleSource->example('flow-if-else-example-2') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator class="mt-4" :text="__('Props used in these examples')" />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('IF ELSE props') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns class="bg-white dark:bg-zinc-900" sticky>
                            <flux:table.column>{{ __('Prop') }}</flux:table.column>
                            <flux:table.column>{{ __('Default') }}</flux:table.column>
                            <flux:table.column>{{ __('Array keys') }}</flux:table.column>
                            <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>auto id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Stable prefix. The question uses .question, the two routes use .true and .false, and .anchorNode-end is the common continuation.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Incoming anchor before the question step.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:anchor-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    x, y
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Fallback incoming coordinate when no attach-to anchor is available.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bottom-top</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    bottom-top, top-bottom
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Vertical direction for the question, both arcs and branch continuations.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:condition-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>IF condition?</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    text, width, align, badgeColor, maxLines
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Question embedded in the incoming step, not attached as a separate node label.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>before-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Stem before the question.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>label-gap</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>auto from question lines</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Space occupied by the question in the stem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>after-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Stem between the question and the shared split anchor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>step-caps</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Draws caps bordering the question gap.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cap-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph cap-length / 1.75rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Size of the question-step caps.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>arc-radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph arc-radius / 2.75rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Size of the incoming and final arcs.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0.75rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Minimum straight length before and after each outcome label. Both route spans align to the larger required total; the shorter route receives longer straight sections.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>left-bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Legacy alias of true-bridge-length.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>right-bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Legacy alias of false-bridge-length.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:if-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>True; width=half</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    text, width, align, badgeColor, maxLines
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Action text on the route that crosses sideways first, then follows its stem to the common end.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:if-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>False; width=half</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    text, width, align, badgeColor, maxLines
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Action text on the route that follows its stem first, then crosses sideways to the common end.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:node-labels</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    left, right → text, width, align, connectorLength, connectorGap
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Additional information at the single shared end anchor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>node-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Visibility of the shared end anchor, rendered once by the final False arc.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inherited graph color / zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Base color for the question and paths. Label badgeColor can distinguish outcomes.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>z-index</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>20</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Rendering layer.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>left</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    left, right
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Side toward which both bridges run; default left uses east-north and south-west arcs.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>8rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Vertical offset between the True and False bridges. The same length follows True and precedes False; enough space for the label line heights plus 2rem is enforced.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true-bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Requested straight length before and after the True label. May grow to match the False route span.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false-bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Requested straight length before and after the False label. May grow to match the True route span.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true-node-labels</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    left, right
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional information at the True arc output, before its stem. Labelled anchors use a dot instead of a joint arrow.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false-node-labels</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    left, right
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional information at the False stem output, before its arc. Labelled anchors use a dot instead of a joint arrow.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>left-counter-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>1</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('DEV counter at the True arc output. Set false to hide this counter.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false-stem-counter</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('DEV counter at the False stem output. Set false to hide this counter.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>right-counter-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>3</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('DEV counter at the common output. Set false to hide this counter.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>if-end.stemLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length / 8rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Overrides the incoming stem of the final False route, including a textless bypass. The opposite return stem adjusts with it; label clearance remains a minimum.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <flux:text class="mt-3">
                {{ __('if-start: color sets the complete True route, including its returning stem. badgeColor can override the embedded label color. Without a color, the route inherits the component color.') }}
            </flux:text>
            <flux:text class="mt-3">
                {{ __('if-end: color sets the entire False lane, badgeColor can override the label color. Without text the bridge stays continuous. The canvas controls path-tone.') }}
            </flux:text>
            <flux:text class="mt-3">
                {{ __('Continue after both routes with attach-to="…decision-1.anchorNode-end". The older true/false and left/right output aliases now refer to this same shared continuation; branch-specific actions belong in the corresponding action bridge.') }}
            </flux:text>
        </flux:callout>
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <x-translation-workbench::ui.common.heading-counter
                    example="flow-if-else-example-1"
                    size="sm"
                >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- flow-if-else-example-1:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-step-08-flow-if-else"
                        :dev="true"
                        :coordinates="true"
                        color="zinc"
                        horizontal-padding="12rem"
                        min-width="56rem"
                        min-height="48rem"
                    >
                        <div class="pointer-events-none opacity-25">
                            <x-translation-workbench::ui.tw-graph.strang.flow-start
                                id="literature.flow.1.paper-process"
                                start-length="7rem"
                                :node-end-dot="false"
                                :start-label="[
                                    'text' => ['Paper process', 'start'],
                                    'width' => 'halfLong',
                                    'align' => 'center',
                                ]"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.flow.1.paper-process.step-1"
                                attach-to="literature.flow.1.paper-process.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="[
                                    'text' => ['Draft prepared'],
                                    'width' => 'halfLong',
                                    'align' => 'center',
                                ]"
                            />
                        </div>

                        <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                            id="literature.flow.1.paper-process.decision-1"
                            :true-node-labels="[
                                'left' => ['text' => ['True'], 'width' => 'half', 'badgeColor' => 'green'],
                            ]"
                            :false-node-labels="[
                                'right' => ['text' => ['False'], 'width' => 'half', 'badgeColor' => 'rose'],
                            ]"
                            attach-to="literature.flow.1.paper-process.step-1.anchorNode-end"
                            color="cyan"
                            before-length="2rem"
                            after-length="2rem"
                            arc-radius="2.75rem"
                            side="left"
                            stem-length="4rem"
                            bridge-length="2rem"
                            true-bridge-length="5rem"
                            false-bridge-length="3rem"
                            :condition-label="[
                                'text' => ['IF reviewApproved?'],
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                            :if-start="[
                                'color' => 'green',
                                'text' => ['Publish paper'],
                                'width' => 'half',
                                'align' => 'center',
                                'badgeColor' => 'green',
                            ]"
                            :if-end="[
                                'text' => ['Revise draft'],
                                'width' => 'half',
                                'align' => 'center',
                                'badgeColor' => 'rose',
                                'color' => 'rose',
                            ]"
                        />
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.paper-process.continue"
                            attach-to="literature.flow.1.paper-process.decision-1.anchorNode-end"
                            before-length="2rem"
                            after-length="2rem"
                            color="zinc"
                            :step-label="[
                                'text' => ['Continue process'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- flow-if-else-example-1:end --}}
                </div>
                <x-translation-workbench::ui.common.heading-counter
                    example="flow-if-else-example-2"
                    size="sm"
                >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- flow-if-else-example-2:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-step-08-flow-if-else-right"
                        :dev="true"
                        :coordinates="true"
                        color="zinc"
                        horizontal-padding="12rem"
                        min-width="56rem"
                        min-height="48rem"
                    >
                        <div class="pointer-events-none opacity-25">
                            <x-translation-workbench::ui.tw-graph.strang.flow-start
                                id="literature.flow.1.paper-process-right"
                                start-length="7rem"
                                :node-end-dot="false"
                                :start-label="[
                                    'text' => ['Paper process', 'start'],
                                    'width' => 'halfLong',
                                    'align' => 'center',
                                ]"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.flow.1.paper-process-right.step-1"
                                attach-to="literature.flow.1.paper-process-right.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="[
                                    'text' => ['Draft prepared'],
                                    'width' => 'halfLong',
                                    'align' => 'center',
                                ]"
                            />
                        </div>

                        <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                            id="literature.flow.1.paper-process-right.decision-1"
                            :true-node-labels="[
                                'right' => ['text' => ['True'], 'width' => 'half', 'badgeColor' => 'green'],
                            ]"
                            :false-node-labels="[
                                'left' => ['text' => ['False'], 'width' => 'half', 'badgeColor' => 'rose'],
                            ]"
                            attach-to="literature.flow.1.paper-process-right.step-1.anchorNode-end"
                            color="cyan"
                            before-length="2rem"
                            after-length="2rem"
                            arc-radius="2.75rem"
                            side="right"
                            stem-length="4rem"
                            bridge-length="2rem"
                            true-bridge-length="5rem"
                            false-bridge-length="3rem"
                            :condition-label="[
                                'text' => ['IF reviewApproved?'],
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                            :if-start="[
                                'color' => 'green',
                                'text' => ['Publish paper'],
                                'width' => 'half',
                                'align' => 'center',
                                'badgeColor' => 'green',
                            ]"
                            :if-end="[
                                'text' => ['Revise draft'],
                                'width' => 'half',
                                'align' => 'center',
                                'badgeColor' => 'rose',
                            ]"
                        />
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.paper-process-right.continue"
                            attach-to="literature.flow.1.paper-process-right.decision-1.anchorNode-end"
                            before-length="2rem"
                            after-length="2rem"
                            color="zinc"
                            :step-label="[
                                'text' => ['Continue process'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- flow-if-else-example-2:end --}}
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../flow/if/flow-if-else.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
