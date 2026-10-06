<x-translation-workbench::ui.common.heading-counter-group group="flow-break-finally">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('WHILE with BREAK and FINALLY') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Each item is handled inside TRY. TRUE processes the item and advances the index; FALSE requests BREAK. FINALLY cleans up exactly once on either route before normal iteration resumes or BREAK leaves the loop. An empty collection never enters TRY or FINALLY.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.break-continue.flow-break-finally" />

            @php
                $breakSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-finally',
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
                            example="break-finally-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $breakSource->example('break-finally-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="break-finally-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $breakSource->example('break-finally-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>attach-to<br>anchor-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null /
                                        component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Connects each explicitly authored component to its source anchor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>condition-label.afterLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Reserves vertical room for the IF and the loop return; explicitly 18rem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>action-label.return</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('False leaves the WHILE body open for the independent IF.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>action-label.beforeLength<br>afterLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Controls the bridges around Read current item; explicitly 2rem each.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true-bridge-length<br>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem /
                                        4rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Controls WHILE entry and exhausted exit; explicitly 4rem and 5.5rem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>if-start.return<br>if-end.return</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Both IF lanes remain open: TRUE returns to WHILE, FALSE exits through BREAK.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>if-end.returnOffset</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>12rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicitly 6rem separates the two FINALLY routes; their action labels sit at different heights.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>if-end.exitDirection</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicitly bottom-top: BREAK immediately turns upward instead of following the downward IF direction.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>if-end.lineJumps</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Marks the crossing of the BREAK bridge and normal-finally.stem.before.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>return-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('The green path returns from normal-finally to loop.anchorNode-return; the BREAK path starts from break-finally.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>length<br>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component
                                        default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('The upper join uses explicit clearance and lengths derived from the two endpoint anchors.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>node-end<br>dev-counter-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component
                                        default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Avoids a duplicate marker at the shared exit; the normal exit owns the Dot.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>counter-start<br>counter-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component
                                        default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Keeps the DEV counters continuous across the separate components.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>normal-finally.attach-to<br>break-finally.attach-to<br>step-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Each FINALLY step attaches to its actual IF branch. Exactly one executes per iteration; cleanup precedes both the normal return and the BREAK exit.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-finally"
                example="break-finally"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('The two FINALLY boxes represent the same source block on two different control-flow routes, not two cleanup calls per item. Cleanup completes normally here; exceptions and control transfers from FINALLY are outside this example. C uses explicit cleanup; C++ uses a scope guard.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('WHILE with BREAK and FINALLY — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Follow each route through its FINALLY box. Green returns to the condition only after cleanup; red completes the pending BREAK only after cleanup. The red bridge jumps over the independent stem before the green FINALLY action.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="break-finally-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- break-finally-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-break-finally-left"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'index = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- WHILE owns the condition; the open body continues into the independent IF. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.break.finally.left.loop"
                                attach-to="literature.break.finally.left.initialize.anchorNode-end"
                                side="left"
                                true-bridge-length="4rem"
                                stem-length="5.5rem"
                                :condition-label="[
                                    'text' => ['WHILE index < count?'],
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '18rem',
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :action-label="[
                                    'text' => ['Read current item'],
                                    'beforeLength' => '2rem',
                                    'afterLength' => '2rem',
                                    'return' => false,
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :true-label="['text' => ['TRUE'], 'width' => 'half', 'color' => 'green']"
                                :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red']"
                                color="cyan"
                            />
                            {{-- TRUE processes and advances; FALSE executes BREAK without returning to the WHILE condition. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.break.finally.left.decision"
                                attach-to="literature.break.finally.left.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="left"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                label-gap="4rem"
                                after-length="1rem"
                                :condition-label="[
                                    'text' => ['TRY', 'IF mayProcess(item)?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE: Process item', 'index = index + 1'],
                                    'return' => false,
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: BREAK pending'],
                                    'return' => false,
                                    'returnOffset' => '6rem',
                                    'exitDirection' => 'bottom-top',
                                    'lineJumps' => [
                                        [
                                            'over' => 'literature.break.finally.left.normal-finally.stem.before',
                                            'radius' => '0.65rem',
                                            'side' => 'bottom',
                                        ],
                                    ],
                                    'width' => 'default',
                                    'color' => 'red',
                                ]"
                                :counter-start="7"
                                :left-counter-end="8"
                                :false-stem-counter="9"
                                :right-counter-end="10"
                                color="sky"
                            />
                            {{-- Both boxes denote the SAME FINALLY block, once on whichever route is taken. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally.left.normal-finally"
                                attach-to="literature.break.finally.left.decision.true.anchorNode-end"
                                direction="top-bottom"
                                before-length="4rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['FINALLY', 'Cleanup current item'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="11"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally.left.break-finally"
                                attach-to="literature.break.finally.left.decision.false.anchorNode-end"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['FINALLY', 'Cleanup current item'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="12"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.break.finally.left.body-return"
                                attach-to="literature.break.finally.left.normal-finally.anchorNode-end"
                                return-to="literature.break.finally.left.loop.anchorNode-return"
                                side="left"
                                :counter-start="13"
                                color="green"
                            />
                            {{-- The BREAK route exits only after its FINALLY has completed. --}}
                            @php
                                $leftBreak = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-left',
                                    'literature.break.finally.left.break-finally.anchorNode-end',
                                );
                                $leftExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-left',
                                    'literature.break.finally.left.loop.anchorNode-end',
                                );
                            @endphp
                            {{-- Normal exhaustion and BREAK after FINALLY meet above the loop. --}}
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.break.finally.left.normal-exit"
                                :anchor-start="$leftExit"
                                length="6rem"
                                :gradient="false"
                                :dev-counter-end="17"
                                color="zinc"
                            />
                            @php
                                $leftJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-left',
                                    'literature.break.finally.left.normal-exit.anchorNode-end',
                                );
                                $leftRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString(
                                    'arc_radius',
                                    '2.75rem',
                                );
                                $leftRiseLength =
                                    'calc(' .
                                    $leftJoin['y'] .
                                    ' - (2 * ' .
                                    $leftRadius .
                                    ') - ' .
                                    $leftBreak['y'] .
                                    ')';
                                $leftBridge =
                                    'calc((' .
                                    $leftJoin['x'] .
                                    ' - ' .
                                    $leftBreak['x'] .
                                    ') * 1 - (2 * ' .
                                    $leftRadius .
                                    '))';
                            @endphp
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.break.finally.left.break-rise"
                                :anchor-start="$leftBreak"
                                :length="$leftRiseLength"
                                :gradient="false"
                                :node-end="true"
                                :jointArrowEnd="true"
                                :dev-counter-end="false"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.break.finally.left.break-join"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-left',
                                    'literature.break.finally.left.break-rise.anchorNode-end',
                                )"
                                side="right"
                                :bridge-length="$leftBridge"
                                :node-end="false"
                                :dev-counter-end="false"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally.left.continue"
                                attach-to="literature.break.finally.left.normal-exit.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Continue after WHILE'],
                                    'width' => 'default',
                                ]"
                                :counter-end="18"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- break-finally-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="break-finally-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- break-finally-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-break-finally-right"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'index = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- WHILE owns the condition; the open body continues into the independent IF. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.break.finally.right.loop"
                                attach-to="literature.break.finally.right.initialize.anchorNode-end"
                                side="right"
                                true-bridge-length="4rem"
                                stem-length="5.5rem"
                                :condition-label="[
                                    'text' => ['WHILE index < count?'],
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '18rem',
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :action-label="[
                                    'text' => ['Read current item'],
                                    'beforeLength' => '2rem',
                                    'afterLength' => '2rem',
                                    'return' => false,
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :true-label="['text' => ['TRUE'], 'width' => 'half', 'color' => 'green']"
                                :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red']"
                                color="cyan"
                            />
                            {{-- TRUE processes and advances; FALSE executes BREAK without returning to the WHILE condition. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.break.finally.right.decision"
                                attach-to="literature.break.finally.right.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="right"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                label-gap="4rem"
                                after-length="1rem"
                                :condition-label="[
                                    'text' => ['TRY', 'IF mayProcess(item)?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE: Process item', 'index = index + 1'],
                                    'return' => false,
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: BREAK pending'],
                                    'return' => false,
                                    'returnOffset' => '6rem',
                                    'exitDirection' => 'bottom-top',
                                    'lineJumps' => [
                                        [
                                            'over' => 'literature.break.finally.right.normal-finally.stem.before',
                                            'radius' => '0.65rem',
                                            'side' => 'bottom',
                                        ],
                                    ],
                                    'width' => 'default',
                                    'color' => 'red',
                                ]"
                                :counter-start="7"
                                :left-counter-end="8"
                                :false-stem-counter="9"
                                :right-counter-end="10"
                                color="sky"
                            />
                            {{-- Both boxes denote the SAME FINALLY block, once on whichever route is taken. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally.right.normal-finally"
                                attach-to="literature.break.finally.right.decision.true.anchorNode-end"
                                direction="top-bottom"
                                before-length="4rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['FINALLY', 'Cleanup current item'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="11"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally.right.break-finally"
                                attach-to="literature.break.finally.right.decision.false.anchorNode-end"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['FINALLY', 'Cleanup current item'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="12"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.break.finally.right.body-return"
                                attach-to="literature.break.finally.right.normal-finally.anchorNode-end"
                                return-to="literature.break.finally.right.loop.anchorNode-return"
                                side="right"
                                :counter-start="13"
                                color="green"
                            />
                            {{-- The BREAK route exits only after its FINALLY has completed. --}}
                            @php
                                $rightBreak = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-right',
                                    'literature.break.finally.right.break-finally.anchorNode-end',
                                );
                                $rightExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-right',
                                    'literature.break.finally.right.loop.anchorNode-end',
                                );
                            @endphp
                            {{-- Normal exhaustion and BREAK after FINALLY meet above the loop. --}}
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.break.finally.right.normal-exit"
                                :anchor-start="$rightExit"
                                length="6rem"
                                :gradient="false"
                                :dev-counter-end="17"
                                color="zinc"
                            />
                            @php
                                $rightJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-right',
                                    'literature.break.finally.right.normal-exit.anchorNode-end',
                                );
                                $rightRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString(
                                    'arc_radius',
                                    '2.75rem',
                                );
                                $rightRiseLength =
                                    'calc(' .
                                    $rightJoin['y'] .
                                    ' - (2 * ' .
                                    $rightRadius .
                                    ') - ' .
                                    $rightBreak['y'] .
                                    ')';
                                $rightBridge =
                                    'calc((' .
                                    $rightJoin['x'] .
                                    ' - ' .
                                    $rightBreak['x'] .
                                    ') * -1 - (2 * ' .
                                    $rightRadius .
                                    '))';
                            @endphp
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.break.finally.right.break-rise"
                                :anchor-start="$rightBreak"
                                :length="$rightRiseLength"
                                :gradient="false"
                                :node-end="true"
                                :jointArrowEnd="true"
                                :dev-counter-end="false"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.break.finally.right.break-join"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-right',
                                    'literature.break.finally.right.break-rise.anchorNode-end',
                                )"
                                side="left"
                                :bridge-length="$rightBridge"
                                :node-end="false"
                                :dev-counter-end="false"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally.right.continue"
                                attach-to="literature.break.finally.right.normal-exit.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Continue after WHILE'],
                                    'width' => 'default',
                                ]"
                                :counter-end="18"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- break-finally-right-example:end --}}
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/break-continue/flow-break-finally.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
