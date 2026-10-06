<x-translation-workbench::ui.common.heading-counter-group group="flow-return-nested">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('RETURN from nested WHILE') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('firstPositive(groups) scans each group and its items. A positive item returns immediately from the entire function, leaving both loops. Only inner exhaustion advances the group index. Empty input, empty groups and groups without a match eventually reach RETURN 0.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.return.flow-return-nested" />

            @php
                $returnSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-nested',
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
                            example="return-nested-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $returnSource->example('return-nested-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="return-nested-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $returnSource->example('return-nested-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>action-label.return</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Both WHILE bodies remain open for the explicitly connected inner loop and decision.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>condition-label.afterLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The outer loop uses 2rem; the inner loop uses 14rem to leave room for its decision and return.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>action-label.afterLength<br>lineJumps</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>4rem / []</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The outer body uses 16rem and an explicit 0.65rem line-jump over the outer return stem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>inner-entry.anchor-return</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The inner condition starts 12rem outward and 8rem below the outer body endpoint.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>if-start.return<br>returnOffset</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true / 0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('TRUE stays open, offsets its endpoint by 8rem and terminates with RETURN item.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>if-end.return<br>returnOffset</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true / 12rem (open
                                        FALSE)</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('FALSE stays open with offset 0rem and no label; it returns directly to the inner condition.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>return-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('inner-return targets inner-loop.anchorNode-return; body-return targets loop.anchorNode-return after advancing groupIndex.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>fallback.attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Only the outer exhausted exit reaches RETURN 0. A successful RETURN never joins either loop continuation.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>counter-start<br>counter-end<br>dev-counter-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The composition numbers all markers from 1 to 34.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-nested"
                example="return-nested"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('Each snippet returns the first positive value across all groups, or 0 when none exists. RETURN leaves both loops because it exits the function; BREAK would exit only the inner loop. C uses an explicit Group structure. Imports and class wrappers are omitted where needed.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('RETURN from nested WHILE — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The amber RETURN route terminates independently. A failed item test returns to the inner condition. Only the inner FALSE exit advances the group, then returns to the outer condition. The outer FALSE exit alone reaches the fallback. The line-jump marks a crossing without a connection.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="return-nested-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- return-nested-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-return-nested-left"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="6rem"
                            min-height="46rem"
                            min-width="88rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.nested.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="[
                                    'text' => ['FUNCTION firstPositive(groups)', 'groupIndex = 0'],
                                    'width' => 'default',
                                ]"
                                after-length="5rem"
                                label-gap="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Each outer iteration selects a group and resets its inner index. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.return.nested.left.loop"
                                :counter-start="1"
                                attach-to="literature.return.nested.left.initialize.anchorNode-end"
                                side="left"
                                color="cyan"
                                true-bridge-length="4rem"
                                stem-length="3.5rem"
                                :condition-label="[
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '2rem',
                                    'text' => ['WHILE groupIndex < groupCount?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :action-label="[
                                    'beforeLength' => '2rem',
                                    'afterLength' => '16rem',
                                    'lineJumps' => [
                                        ['over' => 'literature.return.nested.left.body-return.stem', 'radius' =>
                                            '0.65rem',
                                            'side' => 'top'
                                        ],
                                    ],
                                    'text' => ['Select group', 'itemIndex = 0'],
                                    'color' => 'green',
                                    'return' => false,
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :true-label="[
                                    'text' => ['TRUE'],
                                    'width' => 'half',
                                    'anchor' => 'condition',
                                    'side' => 'right',
                                    'color' => 'green',
                                ]"
                                :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red']"
                            />
                            {{-- Turn the downward body exit into the upward inner condition. --}}
                            @php
                                $leftOuterBodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-nested-left',
                                    'literature.return.nested.left.loop.body.anchorNode-end',
                                );
                                $leftInnerStart = [
                                    'x' => 'calc(' . $leftOuterBodyEnd['x'] . ' - 12rem)',
                                    'y' => 'calc(' . $leftOuterBodyEnd['y'] . ' - 8rem)',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.return.nested.left.inner-entry"
                                :counter-start="7"
                                attach-to="literature.return.nested.left.loop.body.anchorNode-end"
                                :anchor-return="$leftInnerStart"
                                side="right"
                                color="green"
                            />
                            {{-- The inner return skips initialization, so itemIndex is not reset on every item. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.return.nested.left.inner-loop"
                                :counter-start="11"
                                :anchor-start="$leftInnerStart"
                                side="left"
                                color="sky"
                                true-bridge-length="4rem"
                                stem-length="4rem"
                                :condition-label="[
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '14rem',
                                    'text' => ['WHILE itemIndex < itemCount?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :action-label="[
                                    'beforeLength' => '2rem',
                                    'afterLength' => '2rem',
                                    'text' => ['Read current item', 'itemIndex = itemIndex + 1'],
                                    'color' => 'green',
                                    'return' => false,
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :true-label="[
                                    'text' => ['TRUE'], 'width' => 'half',
                                    'anchor' => 'condition', 'side' => 'right', 'color' => 'green',
                                ]"
                                :false-label="[
                                    'text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red',
                                ]"
                            />
                            {{-- A successful RETURN ends the entire function. Only FALSE continues searching this group. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.return.nested.left.decision"
                                attach-to="literature.return.nested.left.inner-loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="left"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                label-gap="2.5rem"
                                after-length="1rem"
                                :condition-label="['text' => ['IF item > 0?'], 'width' => 'default']"
                                :if-start="[
                                    'text' => ['TRUE: RETURN item'],
                                    'return' => false,
                                    'returnOffset' => '4rem',
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :if-end="['return' => false, 'returnOffset' => '0rem', 'color' => 'green']"
                                :counter-start="17"
                                :left-counter-end="18"
                                :false-stem-counter="19"
                                :right-counter-end="20"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.nested.left.found-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-nested-left',
                                    'literature.return.nested.left.decision.true.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="12.8rem"
                                :dev-counter-end="21"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.return.nested.left.inner-return"
                                :counter-start="22"
                                attach-to="literature.return.nested.left.decision.false.anchorNode-end"
                                return-to="literature.return.nested.left.inner-loop.anchorNode-return"
                                side="left"
                                color="sky"
                            />
                            {{-- Turn inner FALSE toward the separate outer return lane. --}}
                            @php
                                $leftInnerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-nested-left',
                                    'literature.return.nested.left.inner-loop.anchorNode-end',
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
                                'id' => 'literature.return.nested.left.outer-resume.arc-in',
                                'devCounterEnd' => 26,
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
                                id="literature.return.nested.left.advance"
                                :counter-end="27"
                                :anchor-start="$leftAdvanceStart"
                                direction="left-right"
                                before-length="2rem"
                                label-gap="14rem"
                                after-length="2rem"
                                :step-label="['text' => ['groupIndex = groupIndex + 1'], 'width' => 'default']"
                                :node-end-dot="false"
                                color="cyan"
                            />
                            @php
                                $leftAdvanceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-nested-left',
                                    'literature.return.nested.left.advance.anchorNode-end',
                                );
                                $leftOuterReturnStart = [
                                    'x' => 'calc(' . $leftAdvanceEnd['x'] . ' + ' . $leftResumeArcRadius . ')',
                                    'y' => 'calc(' . $leftAdvanceEnd['y'] . ' - ' . $leftResumeArcRadius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.return.nested.left.outer-resume.arc-out',
                                'devCounterEnd' => 28,
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
                                id="literature.return.nested.left.body-return"
                                :counter-start="29"
                                :anchor-start="$leftOuterReturnStart"
                                return-to="literature.return.nested.left.loop.anchorNode-return"
                                side="left"
                                color="cyan"
                            />
                            {{-- FALSE continues here, including when the group list starts empty. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.nested.left.fallback"
                                :counter-end="33"
                                attach-to="literature.return.nested.left.loop.anchorNode-end"
                                before-length="4rem"
                                after-length="4rem"
                                label-gap="2.5rem"
                                :step-label="['text' => ['RETURN 0'], 'width' => 'default']"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.nested.left.not-found-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-nested-left',
                                    'literature.return.nested.left.fallback.anchorNode-end',
                                )"
                                :dev-counter-end="34"
                                length="4rem"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- return-nested-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="return-nested-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- return-nested-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-return-nested-right"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="6rem"
                            min-height="46rem"
                            min-width="88rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.nested.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="[
                                    'text' => ['FUNCTION firstPositive(groups)', 'groupIndex = 0'],
                                    'width' => 'default',
                                ]"
                                after-length="5rem"
                                label-gap="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Each outer iteration selects a group and resets its inner index. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.return.nested.right.loop"
                                :counter-start="1"
                                attach-to="literature.return.nested.right.initialize.anchorNode-end"
                                side="right"
                                color="cyan"
                                true-bridge-length="4rem"
                                stem-length="3.5rem"
                                :condition-label="[
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '2rem',
                                    'text' => ['WHILE groupIndex < groupCount?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :action-label="[
                                    'beforeLength' => '2rem',
                                    'afterLength' => '16rem',
                                    'lineJumps' => [
                                        ['over' => 'literature.return.nested.right.body-return.stem', 'radius' =>
                                            '0.65rem',
                                            'side' => 'top'
                                        ],
                                    ],
                                    'text' => ['Select group', 'itemIndex = 0'],
                                    'color' => 'green',
                                    'return' => false,
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :true-label="[
                                    'text' => ['TRUE'],
                                    'width' => 'half',
                                    'anchor' => 'condition',
                                    'side' => 'left',
                                    'color' => 'green',
                                ]"
                                :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red']"
                            />
                            {{-- Turn the downward body exit into the upward inner condition. --}}
                            @php
                                $rightOuterBodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-nested-right',
                                    'literature.return.nested.right.loop.body.anchorNode-end',
                                );
                                $rightInnerStart = [
                                    'x' => 'calc(' . $rightOuterBodyEnd['x'] . ' + 12rem)',
                                    'y' => 'calc(' . $rightOuterBodyEnd['y'] . ' - 8rem)',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.return.nested.right.inner-entry"
                                :counter-start="7"
                                attach-to="literature.return.nested.right.loop.body.anchorNode-end"
                                :anchor-return="$rightInnerStart"
                                side="left"
                                color="green"
                            />
                            {{-- The inner return skips initialization, so itemIndex is not reset on every item. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.return.nested.right.inner-loop"
                                :counter-start="11"
                                :anchor-start="$rightInnerStart"
                                side="right"
                                color="sky"
                                true-bridge-length="4rem"
                                stem-length="4rem"
                                :condition-label="[
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '14rem',
                                    'text' => ['WHILE itemIndex < itemCount?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :action-label="[
                                    'beforeLength' => '2rem',
                                    'afterLength' => '2rem',
                                    'text' => ['Read current item', 'itemIndex = itemIndex + 1'],
                                    'color' => 'green',
                                    'return' => false,
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :true-label="[
                                    'text' => ['TRUE'], 'width' => 'half',
                                    'anchor' => 'condition', 'side' => 'left', 'color' => 'green',
                                ]"
                                :false-label="[
                                    'text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red',
                                ]"
                            />
                            {{-- A successful RETURN ends the entire function. Only FALSE continues searching this group. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.return.nested.right.decision"
                                attach-to="literature.return.nested.right.inner-loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="right"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                label-gap="2.5rem"
                                after-length="1rem"
                                :condition-label="['text' => ['IF item > 0?'], 'width' => 'default']"
                                :if-start="[
                                    'text' => ['TRUE: RETURN item'],
                                    'return' => false,
                                    'returnOffset' => '4rem',
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :if-end="['return' => false, 'returnOffset' => '0rem', 'color' => 'green']"
                                :counter-start="17"
                                :left-counter-end="18"
                                :false-stem-counter="19"
                                :right-counter-end="20"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.nested.right.found-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-nested-right',
                                    'literature.return.nested.right.decision.true.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="12.8rem"
                                :dev-counter-end="21"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.return.nested.right.inner-return"
                                :counter-start="22"
                                attach-to="literature.return.nested.right.decision.false.anchorNode-end"
                                return-to="literature.return.nested.right.inner-loop.anchorNode-return"
                                side="right"
                                color="sky"
                            />
                            {{-- Turn inner FALSE toward the separate outer return lane. --}}
                            @php
                                $rightInnerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-nested-right',
                                    'literature.return.nested.right.inner-loop.anchorNode-end',
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
                                'id' => 'literature.return.nested.right.outer-resume.arc-in',
                                'devCounterEnd' => 26,
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
                                id="literature.return.nested.right.advance"
                                :counter-end="27"
                                :anchor-start="$rightAdvanceStart"
                                direction="right-left"
                                before-length="2rem"
                                label-gap="14rem"
                                after-length="2rem"
                                :step-label="['text' => ['groupIndex = groupIndex + 1'], 'width' => 'default']"
                                :node-end-dot="false"
                                color="cyan"
                            />
                            @php
                                $rightAdvanceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-nested-right',
                                    'literature.return.nested.right.advance.anchorNode-end',
                                );
                                $rightOuterReturnStart = [
                                    'x' => 'calc(' . $rightAdvanceEnd['x'] . ' - ' . $rightResumeArcRadius . ')',
                                    'y' => 'calc(' . $rightAdvanceEnd['y'] . ' - ' . $rightResumeArcRadius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.return.nested.right.outer-resume.arc-out',
                                'devCounterEnd' => 28,
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
                                id="literature.return.nested.right.body-return"
                                :counter-start="29"
                                :anchor-start="$rightOuterReturnStart"
                                return-to="literature.return.nested.right.loop.anchorNode-return"
                                side="right"
                                color="cyan"
                            />
                            {{-- FALSE continues here, including when the group list starts empty. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.nested.right.fallback"
                                :counter-end="33"
                                attach-to="literature.return.nested.right.loop.anchorNode-end"
                                before-length="4rem"
                                after-length="4rem"
                                label-gap="2.5rem"
                                :step-label="['text' => ['RETURN 0'], 'width' => 'default']"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.nested.right.not-found-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-nested-right',
                                    'literature.return.nested.right.fallback.anchorNode-end',
                                )"
                                :dev-counter-end="34"
                                length="4rem"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- return-nested-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/return/flow-return-nested.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
