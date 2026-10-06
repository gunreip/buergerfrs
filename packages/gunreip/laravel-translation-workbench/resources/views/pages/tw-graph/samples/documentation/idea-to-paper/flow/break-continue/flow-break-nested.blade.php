<x-translation-workbench::ui.common.heading-counter-group group="flow-break-nested">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Nested WHILE with BREAK') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The outer WHILE selects a group. The inner WHILE reads each item and advances itemIndex. IF mayProcess(item) is TRUE, processing returns to the inner condition. FALSE executes BREAK and joins the inner exhausted exit before groupIndex advances. Remaining items in that group are skipped; later groups still run.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.break-continue.flow-break-nested" />

            @php
                $breakSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-nested',
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
                            example="break-nested-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $breakSource->example('break-nested-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="break-nested-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $breakSource->example('break-nested-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>attach-to<br>anchor-start</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null / component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Connects each independent component to the intended loop level.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>condition-label.afterLength</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Outer 6rem and inner 27rem explicitly reserve room for the nested routes.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>action-label.return</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Both WHILE bodies remain open for their independently authored continuation.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>action-label.beforeLength<br>action-label.afterLength</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>4rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Outer 2rem / 16rem, inner 2rem / 2rem set the bridges around each body action.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>action-label.lineJumps</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('The outer action bridge jumps over the separate outer return stem without joining it.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true-bridge-length<br>stem-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem / 4rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets loop entry and FALSE exit lengths; both entries use 4rem, the outer exit 3.5rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>if-start<br>if-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('TRUE processes the item and returns to the inner condition; FALSE exits the inner loop through BREAK.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>return-to</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('inner-return targets inner-loop.anchorNode-return; body-return targets loop.anchorNode-return.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>anchor-return</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>required (loop-return)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('The inner entry explicitly turns to an anchor 12rem outward and 8rem below the outer body end.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>segments.arc.segment</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('The two resume arcs use the canvas radius and explicit calculated endpoints to connect inner exhaustion or BREAK to the outer advance.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>advance.step-label<br>label-gap</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null / 4rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Inner exhaustion and BREAK increment groupIndex; its horizontal step uses a 16rem label gap.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>node-end-dot<br>joint-arrow-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true / false (flow-step)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Technical connections use joint arrows; the shared IF endpoint keeps its Dot.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>counter-start<br>counter-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Numbers both loops, the IF and both returns continuously from 1 to 33.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>if-end.return<br>if-end.returnOffset<br>if-end.exitDirection</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true / 12rem / direction</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('False, 4rem and bottom-top explicitly open BREAK upward. Its lineJumps cross the independent inner-return stem; the join targets inner-exit before the group update.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-nested"
                example="break-nested"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('An unlabelled BREAK exits only the innermost loop. It does not end the outer group loop. Empty groups still advance groupIndex. Application helpers, enclosing functions and imports are omitted.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Nested WHILE with BREAK — Preview') }}</flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The red BREAK lane joins the inner FALSE exit before the group update. The green processing lane returns to the inner condition. Only outer FALSE reaches the summary. Line-jumps mark crossings without connections.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="break-nested-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- break-nested-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-break-nested-left"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="6rem"
                            min-height="46rem"
                            min-width="88rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.nested.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load groups', 'groupIndex = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Each outer iteration selects a group and resets its inner index. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.break.nested.left.loop"
                                :counter-start="1"
                                attach-to="literature.break.nested.left.initialize.anchorNode-end"
                                side="left"
                                color="cyan"
                                true-bridge-length="4rem"
                                stem-length="3.5rem"
                                :condition-label="[
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '6rem',
                                    'text' => ['WHILE groupIndex < groupCount?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :action-label="[
                                    'beforeLength' => '2rem',
                                    'afterLength' => '16rem',
                                    'lineJumps' => [
                                        ['over' => 'literature.break.nested.left.body-return.stem', 'radius' => '0.65rem', 'side' => 'top'],
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
                                    'idea-to-paper-break-nested-left',
                                    'literature.break.nested.left.loop.body.anchorNode-end',
                                );
                                $leftInnerStart = [
                                    'x' => 'calc(' . $leftOuterBodyEnd['x'] . ' - 12rem)',
                                    'y' => 'calc(' . $leftOuterBodyEnd['y'] . ' - 8rem)',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.break.nested.left.inner-entry"
                                :counter-start="7"
                                attach-to="literature.break.nested.left.loop.body.anchorNode-end"
                                :anchor-return="$leftInnerStart"
                                side="right"
                                color="green"
                            />
                            {{-- The inner return skips initialization, so itemIndex is not reset on every item. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.break.nested.left.inner-loop"
                                :counter-start="11"
                                :anchor-start="$leftInnerStart"
                                side="left"
                                color="sky"
                                true-bridge-length="4rem"
                                stem-length="4rem"
                                :condition-label="[
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '27rem',
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
                            {{-- BREAK leaves the inner loop; the outer loop still advances and processes subsequent groups. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.break.nested.left.decision"
                                attach-to="literature.break.nested.left.inner-loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="left"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                label-gap="4rem"
                                after-length="1rem"
                                :condition-label="['text' => ['IF mayProcess(item)?'], 'width' => 'default']"
                                :if-start="['text' => ['TRUE: Process item'], 'return' => false, 'width' => 'default', 'color' => 'green']"
                                :if-end="[
                                    'text' => ['FALSE: BREAK'],
                                    'return' => false,
                                    'returnOffset' => '4rem',
                                    'exitDirection' => 'bottom-top',
                                    'lineJumps' => [
                                        ['over' => 'literature.break.nested.left.inner-return.stem', 'radius' => '0.65rem', 'side' => 'bottom'],
                                    ],
                                    'width' => 'default',
                                    'color' => 'red',
                                ]"
                                :counter-start="17"
                                :left-counter-end="18"
                                :false-stem-counter="19"
                                :right-counter-end="20"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.break.nested.left.inner-return"
                                :counter-start="21"
                                attach-to="literature.break.nested.left.decision.true.anchorNode-end"
                                return-to="literature.break.nested.left.inner-loop.anchorNode-return"
                                side="left"
                                color="sky"
                            />
                            {{-- Both BREAK and inner exhaustion join before the outer group update. --}}
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.break.nested.left.inner-exit"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-nested-left', 'literature.break.nested.left.inner-loop.anchorNode-end',
                                )"
                                length="6rem"
                                :gradient="false"
                                :dev-counter-end="25"
                                color="sky"
                            />
                            @php
                                $breakStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-nested-left', 'literature.break.nested.left.decision.false.anchorNode-end',
                                );
                                $breakJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-nested-left', 'literature.break.nested.left.inner-exit.anchorNode-end',
                                );
                                $breakRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('arc_radius', '2.75rem');
                                $breakRise = 'calc(' . $breakJoin['y'] . ' - (2 * ' . $breakRadius . ') - ' . $breakStart['y'] . ')';
                                $breakBridge = 'calc(' . $breakJoin['x'] . ' - ' . $breakStart['x'] . ' - (2 * ' . $breakRadius . '))';
                            @endphp
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.break.nested.left.break-rise"
                                :anchor-start="$breakStart"
                                :length="$breakRise"
                                :gradient="false"
                                :node-end="false"
                                :dev-counter-end="false"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.break.nested.left.break-join"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-nested-left', 'literature.break.nested.left.break-rise.anchorNode-end',
                                )"
                                side="right"
                                :bridge-length="$breakBridge"
                                :node-end="false"
                                :dev-counter-end="false"
                                color="red"
                            />
                            {{-- Turn inner FALSE toward the separate outer return lane. --}}
                            @php
                                $leftInnerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-nested-left',
                                    'literature.break.nested.left.inner-exit.anchorNode-end',
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
                                'id' => 'literature.break.nested.left.outer-resume.arc-in',
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
                            {{-- Inner exhaustion OR BREAK advances groupIndex; CONTINUE is not part of this example. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.nested.left.advance"
                                :counter-end="27"
                                :anchor-start="$leftAdvanceStart"
                                direction="left-right"
                                before-length="2rem"
                                label-gap="16rem"
                                after-length="2rem"
                                :step-label="['text' => ['groupIndex = groupIndex + 1'], 'width' => 'default']"
                                :node-end-dot="false"
                                color="cyan"
                            />
                            @php
                                $leftAdvanceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-nested-left',
                                    'literature.break.nested.left.advance.anchorNode-end',
                                );
                                $leftOuterReturnStart = [
                                    'x' => 'calc(' . $leftAdvanceEnd['x'] . ' + ' . $leftResumeArcRadius . ')',
                                    'y' => 'calc(' . $leftAdvanceEnd['y'] . ' - ' . $leftResumeArcRadius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.break.nested.left.outer-resume.arc-out',
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
                                id="literature.break.nested.left.body-return"
                                :counter-start="29"
                                :anchor-start="$leftOuterReturnStart"
                                return-to="literature.break.nested.left.loop.anchorNode-return"
                                side="left"
                                color="cyan"
                            />
                            {{-- FALSE continues here, including when the group list starts empty. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.nested.left.continue"
                                :counter-end="33"
                                attach-to="literature.break.nested.left.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="['text' => ['Show summary'], 'width' => 'default']"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- break-nested-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="break-nested-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- break-nested-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-break-nested-right"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="6rem"
                            min-height="46rem"
                            min-width="88rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.nested.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load groups', 'groupIndex = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Each outer iteration selects a group and resets its inner index. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.break.nested.right.loop"
                                :counter-start="1"
                                attach-to="literature.break.nested.right.initialize.anchorNode-end"
                                side="right"
                                color="cyan"
                                true-bridge-length="4rem"
                                stem-length="3.5rem"
                                :condition-label="[
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '6rem',
                                    'text' => ['WHILE groupIndex < groupCount?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :action-label="[
                                    'beforeLength' => '2rem',
                                    'afterLength' => '16rem',
                                    'lineJumps' => [
                                        ['over' => 'literature.break.nested.right.body-return.stem', 'radius' => '0.65rem', 'side' => 'top'],
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
                                    'idea-to-paper-break-nested-right',
                                    'literature.break.nested.right.loop.body.anchorNode-end',
                                );
                                $rightInnerStart = [
                                    'x' => 'calc(' . $rightOuterBodyEnd['x'] . ' + 12rem)',
                                    'y' => 'calc(' . $rightOuterBodyEnd['y'] . ' - 8rem)',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.break.nested.right.inner-entry"
                                :counter-start="7"
                                attach-to="literature.break.nested.right.loop.body.anchorNode-end"
                                :anchor-return="$rightInnerStart"
                                side="left"
                                color="green"
                            />
                            {{-- The inner return skips initialization, so itemIndex is not reset on every item. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.break.nested.right.inner-loop"
                                :counter-start="11"
                                :anchor-start="$rightInnerStart"
                                side="right"
                                color="sky"
                                true-bridge-length="4rem"
                                stem-length="4rem"
                                :condition-label="[
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '27rem',
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
                            {{-- BREAK leaves the inner loop; the outer loop still advances and processes subsequent groups. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.break.nested.right.decision"
                                attach-to="literature.break.nested.right.inner-loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="right"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                label-gap="4rem"
                                after-length="1rem"
                                :condition-label="['text' => ['IF mayProcess(item)?'], 'width' => 'default']"
                                :if-start="['text' => ['TRUE: Process item'], 'return' => false, 'width' => 'default', 'color' => 'green']"
                                :if-end="[
                                    'text' => ['FALSE: BREAK'],
                                    'return' => false,
                                    'returnOffset' => '4rem',
                                    'exitDirection' => 'bottom-top',
                                    'lineJumps' => [
                                        ['over' => 'literature.break.nested.right.inner-return.stem', 'radius' => '0.65rem', 'side' => 'bottom'],
                                    ],
                                    'width' => 'default',
                                    'color' => 'red',
                                ]"
                                :counter-start="17"
                                :left-counter-end="18"
                                :false-stem-counter="19"
                                :right-counter-end="20"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.break.nested.right.inner-return"
                                :counter-start="21"
                                attach-to="literature.break.nested.right.decision.true.anchorNode-end"
                                return-to="literature.break.nested.right.inner-loop.anchorNode-return"
                                side="right"
                                color="sky"
                            />
                            {{-- Both BREAK and inner exhaustion join before the outer group update. --}}
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.break.nested.right.inner-exit"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-nested-right', 'literature.break.nested.right.inner-loop.anchorNode-end',
                                )"
                                length="6rem"
                                :gradient="false"
                                :dev-counter-end="25"
                                color="sky"
                            />
                            @php
                                $rightBreakStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-nested-right', 'literature.break.nested.right.decision.false.anchorNode-end',
                                );
                                $rightBreakJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-nested-right', 'literature.break.nested.right.inner-exit.anchorNode-end',
                                );
                                $rightBreakRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('arc_radius', '2.75rem');
                                $rightBreakRise = 'calc(' . $rightBreakJoin['y'] . ' - (2 * ' . $rightBreakRadius . ') - ' . $rightBreakStart['y'] . ')';
                                $rightBreakBridge = 'calc(' . $rightBreakStart['x'] . ' - ' . $rightBreakJoin['x'] . ' - (2 * ' . $rightBreakRadius . '))';
                            @endphp
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.break.nested.right.break-rise"
                                :anchor-start="$rightBreakStart"
                                :length="$rightBreakRise"
                                :gradient="false"
                                :node-end="false"
                                :dev-counter-end="false"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.break.nested.right.break-join"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-nested-right', 'literature.break.nested.right.break-rise.anchorNode-end',
                                )"
                                side="left"
                                :bridge-length="$rightBreakBridge"
                                :node-end="false"
                                :dev-counter-end="false"
                                color="red"
                            />
                            {{-- Turn inner FALSE toward the separate outer return lane. --}}
                            @php
                                $rightInnerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-nested-right',
                                    'literature.break.nested.right.inner-exit.anchorNode-end',
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
                                'id' => 'literature.break.nested.right.outer-resume.arc-in',
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
                            {{-- Inner exhaustion OR BREAK advances groupIndex; CONTINUE is not part of this example. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.nested.right.advance"
                                :counter-end="27"
                                :anchor-start="$rightAdvanceStart"
                                direction="right-left"
                                before-length="2rem"
                                label-gap="16rem"
                                after-length="2rem"
                                :step-label="['text' => ['groupIndex = groupIndex + 1'], 'width' => 'default']"
                                :node-end-dot="false"
                                color="cyan"
                            />
                            @php
                                $rightAdvanceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-nested-right',
                                    'literature.break.nested.right.advance.anchorNode-end',
                                );
                                $rightOuterReturnStart = [
                                    'x' => 'calc(' . $rightAdvanceEnd['x'] . ' - ' . $rightResumeArcRadius . ')',
                                    'y' => 'calc(' . $rightAdvanceEnd['y'] . ' - ' . $rightResumeArcRadius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.break.nested.right.outer-resume.arc-out',
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
                                id="literature.break.nested.right.body-return"
                                :counter-start="29"
                                :anchor-start="$rightOuterReturnStart"
                                return-to="literature.break.nested.right.loop.anchorNode-return"
                                side="right"
                                color="cyan"
                            />
                            {{-- FALSE continues here, including when the group list starts empty. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.nested.right.continue"
                                :counter-end="33"
                                attach-to="literature.break.nested.right.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="['text' => ['Show summary'], 'width' => 'default']"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- break-nested-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/break-continue/flow-break-nested.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
