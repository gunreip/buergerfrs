<x-translation-workbench::ui.common.heading-counter-group group="flow-foreach-nested">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Nested FOREACH') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The outer iteration selects one group and opens a fresh item iteration for that group. The inner iteration processes and records each item, then its exhausted exit finalizes the group. Inner returns request another item; outer returns request another group. Empty groups still reach finalization; an empty group collection skips both bodies.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.foreach.flow-foreach-nested" />

            @php
                $foreachSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.foreach.flow-foreach-nested',
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
                            example="foreach-nested-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $foreachSource->example('foreach-nested-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="foreach-nested-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $foreachSource->example('foreach-nested-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Places the body and its return on the selected side.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>attach-to</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Connects a step to an existing anchor without repeating coordinate calculations.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>step-label</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Names loading, iteration setup, next-item retrieval and continuation.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>before-length<br>after-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets the stems before and after a step label.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>label-gap</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Reserves space along the step for its label.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>anchor-start<br>anchor-return</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>required (paths.loop)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Uses the next-item endpoint as the split and its start as the return target. Setup remains outside the return.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>entry-bridge-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Provides space between the TRUE arc and the body action.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>bridge-length<br>bridge-out-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>4rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets the bridges before and after the body label.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>exit-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>4rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets the exhausted FALSE exit toward continuation.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>bridge-label<br>entry-label<br>exit-label</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Defines the action and TRUE/FALSE information labels, including widths and colors.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>counter-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>1 (flow-step)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Continues DEV numbering across the authored components.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>return</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('False leaves the body open for the nested loop and subsequent actions.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>return-to</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Connects each explicit return to its own next-item or next-group step.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>segment</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Defines connecting arcs through explicit anchors, radius, color and counters.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>bridge-label.lineJumps</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Marks an explicitly referenced crossing with a line-jump.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>counter-start</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2 (paths.loop)<br>1 (paths.loop-return)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets the first DEV counter for the path.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.foreach.flow-foreach-nested"
                example="foreach-nested"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('PHP and C# use foreach; JavaScript uses for…of, Java the enhanced for and C++ the range-based for. C has no native foreach; its indexed loop is an equivalent traversal. Runtime iteration is represented conceptually, not as additional application code. Enclosing functions, imports and application actions are omitted.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Nested FOREACH — Preview') }}</flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Follow the independent item and group returns. Item processing and recording repeat inside the inner loop; finalization runs once per group after inner exhaustion. The explicit line-jump marks a crossing, not a connection. Both examples are individually authored.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="foreach-nested-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- foreach-nested-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-foreach-nested-left"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="6rem"
                            min-height="46rem"
                            min-width="88rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="[
                                    'text' => ['Load groups'],
                                    'width' => 'default',
                                ]"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Iteration setup runs once, outside its own return. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.left.iterator"
                                attach-to="literature.foreach.nested.left.initialize.anchorNode-end"
                                :step-label="[
                                    'text' => ['Open group iteration'],
                                    'width' => 'default',
                                ]"
                                after-length="5rem"
                                :node-end="false"
                                color="zinc"
                            />
                            {{-- Each outer iteration selects a group and opens its inner iteration. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.left.loop.condition"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-left',
                                    'literature.foreach.nested.left.iterator.anchorNode-end',
                                )"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="6rem"
                                :step-label="[
                                    'text' => ['FOREACH next group?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="1"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.foreach.nested.left.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-left',
                                    'literature.foreach.nested.left.loop.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-left',
                                    'literature.foreach.nested.left.iterator.anchorNode-end',
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
                                            'over' => 'literature.foreach.nested.left.body-return.stem',
                                            'radius' => '0.65rem',
                                            'side' => 'top',
                                        ],
                                    ],
                                    'text' => ['Select group', 'Open item iteration'],
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
                                    'idea-to-paper-foreach-nested-left',
                                    'literature.foreach.nested.left.loop.condition.anchorNode-end',
                                )"
                                :exit-label="[
                                    'text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red']"
                                color="cyan"
                            />
                            {{-- Turn the downward body exit into the upward inner condition. --}}
                            @php
                                $leftOuterBodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-left',
                                    'literature.foreach.nested.left.loop.body.anchorNode-end',
                                );
                                $leftInnerStart = [
                                    'x' => 'calc(' . $leftOuterBodyEnd['x'] . ' - 12rem)',
                                    'y' => 'calc(' . $leftOuterBodyEnd['y'] . ' - 8rem)',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.foreach.nested.left.inner-entry"
                                :counter-start="7"
                                attach-to="literature.foreach.nested.left.loop.body.anchorNode-end"
                                :anchor-return="$leftInnerStart"
                                side="right"
                                color="green"
                            />
                            {{-- The inner return requests another item without reopening its iteration. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.left.inner-loop.condition"
                                :anchor-start="$leftInnerStart"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="8rem"
                                :step-label="[
                                    'text' => ['FOREACH next item?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="11"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.foreach.nested.left.inner-loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-left',
                                    'literature.foreach.nested.left.inner-loop.condition.anchorNode-end',
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
                                    'idea-to-paper-foreach-nested-left',
                                    'literature.foreach.nested.left.inner-loop.condition.anchorNode-end',
                                )"
                                :exit-label="[
                                    'text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red',
                                ]"
                                color="sky"
                            />
                            {{-- Record the processed item before requesting the next item. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.left.record-item"
                                :counter-end="17"
                                attach-to="literature.foreach.nested.left.inner-loop.body.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['Record item result'],
                                    'width' => 'default',
                                ]"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.foreach.nested.left.inner-return"
                                :counter-start="18"
                                attach-to="literature.foreach.nested.left.record-item.anchorNode-end"
                                return-to="literature.foreach.nested.left.inner-loop.anchorNode-return"
                                side="left"
                                color="sky"
                            />
                            {{-- Turn inner FALSE toward the separate outer return lane. --}}
                            @php
                                $leftInnerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-left',
                                    'literature.foreach.nested.left.inner-loop.anchorNode-end',
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
                                'id' => 'literature.foreach.nested.left.outer-resume.arc-in',
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
                            {{-- Only inner FALSE finalizes the group, including for an empty group. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.left.finalize-group"
                                :counter-end="23"
                                :anchor-start="$leftAdvanceStart"
                                direction="left-right"
                                before-length="2rem"
                                label-gap="16rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['Finalize group'],
                                    'width' => 'default',
                                ]"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                color="cyan"
                            />
                            @php
                                $leftAdvanceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-left',
                                    'literature.foreach.nested.left.finalize-group.anchorNode-end',
                                );
                                $leftOuterReturnStart = [
                                    'x' => 'calc(' . $leftAdvanceEnd['x'] . ' + ' . $leftResumeArcRadius . ')',
                                    'y' => 'calc(' . $leftAdvanceEnd['y'] . ' - ' . $leftResumeArcRadius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.foreach.nested.left.outer-resume.arc-out',
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
                            {{-- Request the next group after finalization; skip outer iteration setup. --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.foreach.nested.left.body-return"
                                :counter-start="25"
                                :anchor-start="$leftOuterReturnStart"
                                return-to="literature.foreach.nested.left.loop.anchorNode-return"
                                side="left"
                                color="cyan"
                            />
                            {{-- FALSE continues here, including when the group list starts empty. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.left.continue"
                                :counter-end="29"
                                attach-to="literature.foreach.nested.left.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Show summary'],
                                    'width' => 'default',
                                ]"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- foreach-nested-left-example:end --}}
                    </div>

                    <x-translation-workbench::ui.common.heading-counter
                        example="foreach-nested-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- foreach-nested-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-foreach-nested-right"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="6rem"
                            min-height="46rem"
                            min-width="88rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="[
                                    'text' => ['Load groups'],
                                    'width' => 'default',
                                ]"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Iteration setup runs once, outside its own return. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.right.iterator"
                                attach-to="literature.foreach.nested.right.initialize.anchorNode-end"
                                :step-label="[
                                    'text' => ['Open group iteration'],
                                    'width' => 'default',
                                ]"
                                after-length="5rem"
                                :node-end="false"
                                color="zinc"
                            />
                            {{-- Each outer iteration selects a group and opens its inner iteration. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.right.loop.condition"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-right',
                                    'literature.foreach.nested.right.iterator.anchorNode-end',
                                )"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="6rem"
                                :step-label="[
                                    'text' => ['FOREACH next group?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="1"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.foreach.nested.right.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-right',
                                    'literature.foreach.nested.right.loop.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-right',
                                    'literature.foreach.nested.right.iterator.anchorNode-end',
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
                                            'over' => 'literature.foreach.nested.right.body-return.stem',
                                            'radius' => '0.65rem',
                                            'side' => 'top',
                                        ],
                                    ],
                                    'text' => ['Select group', 'Open item iteration'],
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
                                    'idea-to-paper-foreach-nested-right',
                                    'literature.foreach.nested.right.loop.condition.anchorNode-end',
                                )"
                                :exit-label="[
                                    'text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red']"
                                color="cyan"
                            />
                            {{-- Turn the downward body exit into the upward inner condition. --}}
                            @php
                                $rightOuterBodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-right',
                                    'literature.foreach.nested.right.loop.body.anchorNode-end',
                                );
                                $rightInnerStart = [
                                    'x' => 'calc(' . $rightOuterBodyEnd['x'] . ' + 12rem)',
                                    'y' => 'calc(' . $rightOuterBodyEnd['y'] . ' - 8rem)',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.foreach.nested.right.inner-entry"
                                :counter-start="7"
                                attach-to="literature.foreach.nested.right.loop.body.anchorNode-end"
                                :anchor-return="$rightInnerStart"
                                side="left"
                                color="green"
                            />
                            {{-- The inner return requests another item without reopening its iteration. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.right.inner-loop.condition"
                                :anchor-start="$rightInnerStart"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="8rem"
                                :step-label="[
                                    'text' => ['FOREACH next item?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="11"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.foreach.nested.right.inner-loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-right',
                                    'literature.foreach.nested.right.inner-loop.condition.anchorNode-end',
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
                                    'idea-to-paper-foreach-nested-right',
                                    'literature.foreach.nested.right.inner-loop.condition.anchorNode-end',
                                )"
                                :exit-label="[
                                    'text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red',
                                ]"
                                color="sky"
                            />
                            {{-- Record the processed item before requesting the next item. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.right.record-item"
                                :counter-end="17"
                                attach-to="literature.foreach.nested.right.inner-loop.body.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['Record item result'],
                                    'width' => 'default',
                                ]"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.foreach.nested.right.inner-return"
                                :counter-start="18"
                                attach-to="literature.foreach.nested.right.record-item.anchorNode-end"
                                return-to="literature.foreach.nested.right.inner-loop.anchorNode-return"
                                side="right"
                                color="sky"
                            />
                            {{-- Turn inner FALSE toward the separate outer return lane. --}}
                            @php
                                $rightInnerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-right',
                                    'literature.foreach.nested.right.inner-loop.anchorNode-end',
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
                                'id' => 'literature.foreach.nested.right.outer-resume.arc-in',
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
                            {{-- Only inner FALSE finalizes the group, including for an empty group. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.right.finalize-group"
                                :counter-end="23"
                                :anchor-start="$rightAdvanceStart"
                                direction="right-left"
                                before-length="2rem"
                                label-gap="16rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['Finalize group'],
                                    'width' => 'default',
                                ]"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                color="cyan"
                            />
                            @php
                                $rightAdvanceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-foreach-nested-right',
                                    'literature.foreach.nested.right.finalize-group.anchorNode-end',
                                );
                                $rightOuterReturnStart = [
                                    'x' => 'calc(' . $rightAdvanceEnd['x'] . ' - ' . $rightResumeArcRadius . ')',
                                    'y' => 'calc(' . $rightAdvanceEnd['y'] . ' - ' . $rightResumeArcRadius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.foreach.nested.right.outer-resume.arc-out',
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
                            {{-- Request the next group after finalization; skip outer iteration setup. --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.foreach.nested.right.body-return"
                                :counter-start="25"
                                :anchor-start="$rightOuterReturnStart"
                                return-to="literature.foreach.nested.right.loop.anchorNode-return"
                                side="right"
                                color="cyan"
                            />
                            {{-- FALSE continues here, including when the group list starts empty. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.foreach.nested.right.continue"
                                :counter-end="29"
                                attach-to="literature.foreach.nested.right.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Show summary'],
                                    'width' => 'default',
                                ]"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- foreach-nested-right-example:end --}}
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/foreach/flow-foreach-nested.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
