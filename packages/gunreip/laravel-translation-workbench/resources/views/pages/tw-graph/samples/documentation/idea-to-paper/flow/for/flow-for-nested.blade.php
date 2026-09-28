<x-translation-workbench::ui.common.heading-counter-group group="flow-for-nested">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Nested FOR') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Initialize groupIndex once. Each outer FOR selects a group and initializes itemIndex for its inner FOR. The inner increment returns only to the item condition. Its FALSE exit advances groupIndex before returning to the group condition. Empty groups skip item processing; an empty group list skips both bodies.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.for.flow-for-nested" />

            @php
                $forSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-nested',
                );
            @endphp
            <x-translation-workbench::ui.common.separator-code-example-tw-graph />
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
                            example="for-nested-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $forSource->example('for-nested-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="for-nested-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $forSource->example('for-nested-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <x-translation-workbench::ui.common.separator-props-used-tw-graph />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Props and connections') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns
                            class="bg-white dark:bg-zinc-900"
                            sticky
                        >
                            <flux:table.column>{{ __('Prop / anchor') }}</flux:table.column>
                            <flux:table.column>{{ __('Default') }}</flux:table.column>
                            <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>side</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>left</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets the side independently for each loop and return.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>anchor-start<br>anchor-return</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>required (paths.loop)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Connects the condition end to the split and its start to the correct return target.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>attach-to<br>return-to</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Links actions and returns to explicitly named endpoints.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>step-label</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null (flow-step)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Names preparation, initialization, conditions, counter updates and continuation.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>before-length<br>after-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem (flow-step)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets the stems around each action or condition. All non-default lengths are authored in the example.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>label-gap</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null (resolved from text)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Reserves space for each condition or action label.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>return</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true (paths.loop)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('False leaves each body open for its own counter update and explicit return.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>entry-bridge-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets the bridge between the TRUE arc and the body action.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>bridge-length<br>bridge-out-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>4rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets the bridges around each body action, including space for nested loops and crossings.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>exit-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>4rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets the FALSE exit stem of each loop.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>bridge-label</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Defines each body action, its width, color and explicit lineJumps.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>entry-label<br>entry-label-anchor<br>exit-label</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[]<br>null<br>[]</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Attaches TRUE to the condition anchor and FALSE to the exit.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>segment</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>required (segments.arc)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Explicitly defines each connecting arc with its anchor, direction, geometry and counter.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>counter-start<br>counter-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Keeps the DEV counters continuous across independent component calls.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-nested"
                example="for-nested"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('The FOR header contains initialization, condition and increment. These syntax excerpts use application-provided actions; enclosing functions, classes and imports are omitted.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Nested FOR — Preview') }}</flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Compare the left and right layouts while following the two return paths. The inner return skips itemIndex initialization; the outer return selects the next group and resets itemIndex. The connector after the inner FALSE exit rejoins the separate outer return lane.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="for-nested-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- for-nested-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-for-nested-left"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="6rem"
                            min-height="46rem"
                            min-width="88rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="[
                                    'text' => ['Load groups'],
                                    'width' => 'default',
                                ]"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- FOR initialization runs once, outside its own return. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.left.initialize-index"
                                attach-to="literature.for.nested.left.initialize.anchorNode-end"
                                :step-label="[
                                    'text' => ['groupIndex = 0'],
                                    'width' => 'default',
                                ]"
                                after-length="5rem"
                                :node-end="false"
                                color="zinc"
                            />
                            {{-- Each outer iteration selects a group and resets its inner index. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.left.loop.condition"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-left',
                                    'literature.for.nested.left.initialize-index.anchorNode-end',
                                )"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="6rem"
                                :step-label="[
                                    'text' => ['FOR groupIndex < groupCount?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="1"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.for.nested.left.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-left',
                                    'literature.for.nested.left.loop.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-left',
                                    'literature.for.nested.left.initialize-index.anchorNode-end',
                                )"
                                side="left"
                                :counter-start="2"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="16rem"
                                exit-length="3.5rem"
                                :bridge-label="[
                                    'lineJumps' => [
                                        [
                                            'over' => 'literature.for.nested.left.body-return.stem',
                                            'radius' => '0.65rem',
                                            'side' => 'top',
                                        ],
                                    ],
                                    'text' => ['Select group', 'itemIndex = 0'],
                                    'color' => 'green',
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :entry-label="[
                                    'text' => ['TRUE'],
                                    'width' => 'half',
                                    'side' => 'right',
                                    'color' => 'green',
                                ]"
                                :entry-label-anchor="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-left',
                                    'literature.for.nested.left.loop.condition.anchorNode-end',
                                )"
                                :exit-label="[
                                    'text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red']"
                                color="cyan"
                            />
                            {{-- Turn the downward body exit into the upward inner condition. --}}
                            @php
                                $leftOuterBodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-left',
                                    'literature.for.nested.left.loop.body.anchorNode-end',
                                );
                                $leftInnerStart = [
                                    'x' => 'calc(' . $leftOuterBodyEnd['x'] . ' - 12rem)',
                                    'y' => 'calc(' . $leftOuterBodyEnd['y'] . ' - 8rem)',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.for.nested.left.inner-entry"
                                :counter-start="7"
                                attach-to="literature.for.nested.left.loop.body.anchorNode-end"
                                :anchor-return="$leftInnerStart"
                                side="right"
                                color="green"
                            />
                            {{-- The inner return skips initialization, so itemIndex is not reset on every item. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.left.inner-loop.condition"
                                :anchor-start="$leftInnerStart"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="8rem"
                                :step-label="[
                                    'text' => ['FOR itemIndex < itemCount?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="11"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.for.nested.left.inner-loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-left',
                                    'literature.for.nested.left.inner-loop.condition.anchorNode-end',
                                )"
                                :anchor-return="$leftInnerStart"
                                side="left"
                                :counter-start="12"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="2rem"
                                exit-length="4rem"
                                :bridge-label="[
                                    'text' => ['Process current item'],
                                    'color' => 'green',
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :entry-label="[
                                    'text' => ['TRUE'], 'width' => 'half',
                                    'side' => 'right', 'color' => 'green',
                                ]"
                                :entry-label-anchor="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-left',
                                    'literature.for.nested.left.inner-loop.condition.anchorNode-end',
                                )"
                                :exit-label="[
                                    'text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red',
                                ]"
                                color="sky"
                            />
                            {{-- Advance only the inner index, then recheck the inner condition. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.left.inner-advance"
                                :counter-end="17"
                                attach-to="literature.for.nested.left.inner-loop.body.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['itemIndex = itemIndex + 1'],
                                    'width' => 'default',
                                ]"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.for.nested.left.inner-return"
                                :counter-start="18"
                                attach-to="literature.for.nested.left.inner-advance.anchorNode-end"
                                return-to="literature.for.nested.left.inner-loop.anchorNode-return"
                                side="left"
                                color="sky"
                            />
                            {{-- Turn inner FALSE toward the separate outer return lane. --}}
                            @php
                                $leftInnerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-left',
                                    'literature.for.nested.left.inner-loop.anchorNode-end',
                                );
                                $leftResumeArcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString(
                                    'arc_radius',
                                    '2.75rem',
                                );
                                $leftAdvanceStart = [
                                    'x' => 'calc(' . $leftInnerExit['x'] . ' + ' . $leftResumeArcRadius . ')',
                                    'y' => 'calc(' . $leftInnerExit['y'] . ' + ' . $leftResumeArcRadius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.for.nested.left.outer-resume.arc-in',
                                'devCounterEnd' => 22,
                                'anchorStart' => $leftInnerExit,
                                'anchorEnd' => $leftAdvanceStart,
                                'startAnchor' => 'w',
                                'endAnchor' => 'n',
                                'arcRadius' => $leftResumeArcRadius,
                                'color' => 'cyan',

                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'right',
                            ]" />
                            {{-- Only inner FALSE advances the group index, including for an empty group. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.left.advance"
                                :counter-end="23"
                                :anchor-start="$leftAdvanceStart"
                                direction="left-right"
                                before-length="2rem"
                                label-gap="16rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['groupIndex = groupIndex + 1'],
                                    'width' => 'default',
                                ]"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                color="cyan"
                            />
                            @php
                                $leftAdvanceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-left',
                                    'literature.for.nested.left.advance.anchorNode-end',
                                );
                                $leftOuterReturnStart = [
                                    'x' => 'calc(' . $leftAdvanceEnd['x'] . ' + ' . $leftResumeArcRadius . ')',
                                    'y' => 'calc(' . $leftAdvanceEnd['y'] . ' - ' . $leftResumeArcRadius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.for.nested.left.outer-resume.arc-out',
                                'devCounterEnd' => 24,
                                'anchorStart' => $leftAdvanceEnd,
                                'anchorEnd' => $leftOuterReturnStart,
                                'startAnchor' => 'n',
                                'endAnchor' => 'e',
                                'arcRadius' => $leftResumeArcRadius,
                                'color' => 'cyan',

                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'bottom',
                            ]" />
                            {{-- Return only after advancing, to the same condition (not initialization). --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.for.nested.left.body-return"
                                :counter-start="25"
                                :anchor-start="$leftOuterReturnStart"
                                return-to="literature.for.nested.left.loop.anchorNode-return"
                                side="left"
                                color="cyan"
                            />
                            {{-- FALSE continues here, including when the group list starts empty. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.left.continue"
                                :counter-end="29"
                                attach-to="literature.for.nested.left.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Show summary'],
                                    'width' => 'default',
                                ]"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- for-nested-left-example:end --}}
                    </div>

                    <x-translation-workbench::ui.common.heading-counter
                        example="for-nested-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- for-nested-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-for-nested-right"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="6rem"
                            min-height="46rem"
                            min-width="88rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="[
                                    'text' => ['Load groups'],
                                    'width' => 'default',
                                ]"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- FOR initialization runs once, outside its own return. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.right.initialize-index"
                                attach-to="literature.for.nested.right.initialize.anchorNode-end"
                                :step-label="[
                                    'text' => ['groupIndex = 0'],
                                    'width' => 'default',
                                ]"
                                after-length="5rem"
                                :node-end="false"
                                color="zinc"
                            />
                            {{-- Each outer iteration selects a group and resets its inner index. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.right.loop.condition"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-right',
                                    'literature.for.nested.right.initialize-index.anchorNode-end',
                                )"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="6rem"
                                :step-label="[
                                    'text' => ['FOR groupIndex < groupCount?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="1"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.for.nested.right.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-right',
                                    'literature.for.nested.right.loop.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-right',
                                    'literature.for.nested.right.initialize-index.anchorNode-end',
                                )"
                                side="right"
                                :counter-start="2"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="16rem"
                                exit-length="3.5rem"
                                :bridge-label="[
                                    'lineJumps' => [
                                        [
                                            'over' => 'literature.for.nested.right.body-return.stem',
                                            'radius' => '0.65rem',
                                            'side' => 'top',
                                        ],
                                    ],
                                    'text' => ['Select group', 'itemIndex = 0'],
                                    'color' => 'green',
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :entry-label="[
                                    'text' => ['TRUE'],
                                    'width' => 'half',
                                    'side' => 'left',
                                    'color' => 'green',
                                ]"
                                :entry-label-anchor="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-right',
                                    'literature.for.nested.right.loop.condition.anchorNode-end',
                                )"
                                :exit-label="[
                                    'text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red']"
                                color="cyan"
                            />
                            {{-- Turn the downward body exit into the upward inner condition. --}}
                            @php
                                $rightOuterBodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-right',
                                    'literature.for.nested.right.loop.body.anchorNode-end',
                                );
                                $rightInnerStart = [
                                    'x' => 'calc(' . $rightOuterBodyEnd['x'] . ' + 12rem)',
                                    'y' => 'calc(' . $rightOuterBodyEnd['y'] . ' - 8rem)',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.for.nested.right.inner-entry"
                                :counter-start="7"
                                attach-to="literature.for.nested.right.loop.body.anchorNode-end"
                                :anchor-return="$rightInnerStart"
                                side="left"
                                color="green"
                            />
                            {{-- The inner return skips initialization, so itemIndex is not reset on every item. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.right.inner-loop.condition"
                                :anchor-start="$rightInnerStart"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="8rem"
                                :step-label="[
                                    'text' => ['FOR itemIndex < itemCount?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="11"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.for.nested.right.inner-loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-right',
                                    'literature.for.nested.right.inner-loop.condition.anchorNode-end',
                                )"
                                :anchor-return="$rightInnerStart"
                                side="right"
                                :counter-start="12"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="2rem"
                                exit-length="4rem"
                                :bridge-label="[
                                    'text' => ['Process current item'],
                                    'color' => 'green',
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :entry-label="[
                                    'text' => ['TRUE'], 'width' => 'half',
                                    'side' => 'left', 'color' => 'green',
                                ]"
                                :entry-label-anchor="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-right',
                                    'literature.for.nested.right.inner-loop.condition.anchorNode-end',
                                )"
                                :exit-label="[
                                    'text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red',
                                ]"
                                color="sky"
                            />
                            {{-- Advance only the inner index, then recheck the inner condition. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.right.inner-advance"
                                :counter-end="17"
                                attach-to="literature.for.nested.right.inner-loop.body.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['itemIndex = itemIndex + 1'],
                                    'width' => 'default',
                                ]"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.for.nested.right.inner-return"
                                :counter-start="18"
                                attach-to="literature.for.nested.right.inner-advance.anchorNode-end"
                                return-to="literature.for.nested.right.inner-loop.anchorNode-return"
                                side="right"
                                color="sky"
                            />
                            {{-- Turn inner FALSE toward the separate outer return lane. --}}
                            @php
                                $rightInnerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-right',
                                    'literature.for.nested.right.inner-loop.anchorNode-end',
                                );
                                $rightResumeArcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString(
                                    'arc_radius',
                                    '2.75rem',
                                );
                                $rightAdvanceStart = [
                                    'x' => 'calc(' . $rightInnerExit['x'] . ' - ' . $rightResumeArcRadius . ')',
                                    'y' => 'calc(' . $rightInnerExit['y'] . ' + ' . $rightResumeArcRadius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.for.nested.right.outer-resume.arc-in',
                                'devCounterEnd' => 22,
                                'anchorStart' => $rightInnerExit,
                                'anchorEnd' => $rightAdvanceStart,
                                'startAnchor' => 'e',
                                'endAnchor' => 'n',
                                'arcRadius' => $rightResumeArcRadius,
                                'color' => 'cyan',

                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'left',
                            ]" />
                            {{-- Only inner FALSE advances the group index, including for an empty group. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.right.advance"
                                :counter-end="23"
                                :anchor-start="$rightAdvanceStart"
                                direction="right-left"
                                before-length="2rem"
                                label-gap="16rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['groupIndex = groupIndex + 1'],
                                    'width' => 'default',
                                ]"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                color="cyan"
                            />
                            @php
                                $rightAdvanceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-nested-right',
                                    'literature.for.nested.right.advance.anchorNode-end',
                                );
                                $rightOuterReturnStart = [
                                    'x' => 'calc(' . $rightAdvanceEnd['x'] . ' - ' . $rightResumeArcRadius . ')',
                                    'y' => 'calc(' . $rightAdvanceEnd['y'] . ' - ' . $rightResumeArcRadius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.for.nested.right.outer-resume.arc-out',
                                'devCounterEnd' => 24,
                                'anchorStart' => $rightAdvanceEnd,
                                'anchorEnd' => $rightOuterReturnStart,
                                'startAnchor' => 'n',
                                'endAnchor' => 'w',
                                'arcRadius' => $rightResumeArcRadius,
                                'color' => 'cyan',

                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'bottom',
                            ]" />
                            {{-- Return only after advancing, to the same condition (not initialization). --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.for.nested.right.body-return"
                                :counter-start="25"
                                :anchor-start="$rightOuterReturnStart"
                                return-to="literature.for.nested.right.loop.anchorNode-return"
                                side="right"
                                color="cyan"
                            />
                            {{-- FALSE continues here, including when the group list starts empty. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.nested.right.continue"
                                :counter-end="29"
                                attach-to="literature.for.nested.right.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Show summary'],
                                    'width' => 'default',
                                ]"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- for-nested-right-example:end --}}
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/for/flow-for-nested.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
