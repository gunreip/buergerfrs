<x-translation-workbench::ui.common.heading-counter-group group="flow-break-finally-2">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('BREAK with FINALLY (2) — horizontal decision') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Each item is handled inside TRY. TRUE processes the item and advances the index; FALSE requests BREAK. FINALLY cleans up exactly once on either route before normal iteration resumes or BREAK leaves the loop. An empty collection never enters TRY or FINALLY.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.break-continue.flow-break-finally-2" />

            @php
                $breakSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-finally-2',
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
                            example="break-finally-2-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Body right / decision left') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $breakSource->example('break-finally-2-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="break-finally-2-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Body left / decision right') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $breakSource->example('break-finally-2-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>loop.side<br>action-label.return</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left / true</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The mirrored loop sides leave the body open for the U-turn through action-label.return = false.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>condition-label.afterLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Explicitly 17.5rem leaves room for the horizontal decision and cleanup.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>question.geometry</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('LabelBridge geometry explicitly uses a 13rem label, 2rem incoming bridge and 12rem outgoing bridge.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>question.lineJumps</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The outgoing question bridge crosses loop.condition.stem.after without joining it.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>split.anchor-start<br>direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>required / right-left</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The question bridge endpoint supplies the one input of the binary split.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>split.outputs[].key<br>offset<br>color<br>label</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>required / required /
                                        inherited / null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('TRUE is 4rem above and FALSE 4rem below the input. Each output owns a named anchor and an informational label.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>split.arc-radius<br>min-stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>1.375rem / 1rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The splitter reuses fusion segment geometry with one consistent radius and no short compensators.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>process.geometry<br>break-action.geometry</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The action bridges explicitly use 2rem incoming length and 2rem or 10rem outgoing length.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>break-action.lineJumps</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The BREAK lane crosses normal-finally.stem.before without joining it.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>normal-finally.before-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>4rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Explicitly 12rem places cleanup below the independent BREAK bridge.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>return-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Only the normal FINALLY returns to the WHILE condition; BREAK joins normal-exit after its cleanup.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>counter-start<br>counter-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Numbers the composed components continuously from 1 to 23.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>question.stem.length</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>inherited
                                        (parts.start)</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Explicitly 4rem downward between the body exit and U-turn; the question and split follow this lower anchor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-finally-2"
                example="break-finally-2"
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
            <flux:callout.heading icon="eye">{{ __('BREAK with FINALLY (2) — horizontal decision — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The two examples mirror the body and horizontal decision. A U-turn leads into the TRY/IF question, then a line-jump crosses the WHILE condition stem. parts.split distributes the flow into TRUE and FALSE. Each route reaches FINALLY before its own return or exit.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="break-finally-2-left-example"
                        size="sm"
                    >{{ __('Body right / decision left') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- break-finally-2-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-break-finally-2-left"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally-2.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'index = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- WHILE owns the condition; the open body continues into the independent IF. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.break.finally-2.left.loop"
                                attach-to="literature.break.finally-2.left.initialize.anchorNode-end"
                                side="right"
                                true-bridge-length="4rem"
                                stem-length="3.5rem"
                                :condition-label="[
                                    'text' => ['WHILE index < count?'],
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '17.5rem',
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
                            {{-- Lower the U-turn explicitly before routing the question back to the left. --}}
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.break.finally-2.left.question.stem"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-2-left',
                                    'literature.break.finally-2.left.loop.body.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="4rem"
                                :gradient="false"
                                :joint-arrow-end="true"
                                :dev-counter-end="7"
                                color="sky"
                            />
                            @php
                                $graph = 'idea-to-paper-break-finally-2-left';
                                $radius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString(
                                    'arc_radius',
                                    '2.75rem',
                                );
                                $bodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.break.finally-2.left.question.stem.anchorNode-end',
                                );
                                $questionStart = [
                                    'x' => 'calc(' . $bodyEnd['x'] . ' - ' . $radius . ')',
                                    'y' => 'calc(' . $bodyEnd['y'] . ' - ' . $radius . ')',
                                ];
                                $questionGeometry = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::geometry(
                                    $questionStart,
                                    'right-left',
                                    '13rem',
                                    '2rem',
                                    '12rem',
                                );
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.break.finally-2.left.question.turn',
                                'anchorStart' => $bodyEnd,
                                'anchorEnd' => $questionStart,
                                'startAnchor' => 'e',
                                'endAnchor' => 's',
                                'arcRadius' => $radius,
                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'left',
                                'devCounterEnd' => 8,
                                'color' => 'sky',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                                id="literature.break.finally-2.left.question"
                                :anchor-start="$questionStart"
                                :geometry="$questionGeometry"
                                direction="right-left"
                                :label="[
                                    'text' => ['TRY', 'IF mayProcess(item)?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :line-jumps="[
                                    [
                                        'over' => 'literature.break.finally-2.left.loop.condition.stem.after',
                                        'radius' => '0.65rem',
                                        'side' => 'bottom',
                                    ],
                                ]"
                                :dev-counter-end="false"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.split
                                id="literature.break.finally-2.left.decision"
                                :anchor-start="$questionGeometry['anchorEnd']"
                                direction="right-left"
                                :outputs="[
                                    [
                                        'key' => 'true',
                                        'offset' => '4rem',
                                        'color' => 'green',
                                        'label' => ['text' => ['TRUE'], 'width' => 'half', 'side' => 'top'],
                                    ],
                                    [
                                        'key' => 'false',
                                        'offset' => '-4rem',
                                        'color' => 'red',
                                        'label' => ['text' => ['FALSE'], 'width' => 'half', 'side' => 'bottom'],
                                    ],
                                ]"
                                :counter-start="9"
                                color="sky"
                            />
                            @php
                                $trueStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.break.finally-2.left.decision.outputs.true.anchorNode-end',
                                );
                                $falseStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.break.finally-2.left.decision.outputs.false.anchorNode-end',
                                );
                                $trueGeometry = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::geometry(
                                    $trueStart,
                                    'right-left',
                                    '13rem',
                                    '2rem',
                                    '2rem',
                                );
                                $falseGeometry = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::geometry(
                                    $falseStart,
                                    'right-left',
                                    '13rem',
                                    '2rem',
                                    '10rem',
                                );
                                $trueEnd = [
                                    'x' => 'calc(' . $trueGeometry['anchorEnd']['x'] . ' - ' . $radius . ')',
                                    'y' => 'calc(' . $trueGeometry['anchorEnd']['y'] . ' - ' . $radius . ')',
                                ];
                                $falseEnd = [
                                    'x' => 'calc(' . $falseGeometry['anchorEnd']['x'] . ' - ' . $radius . ')',
                                    'y' => 'calc(' . $falseGeometry['anchorEnd']['y'] . ' + ' . $radius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                                id="literature.break.finally-2.left.process"
                                :anchor-start="$trueStart"
                                :geometry="$trueGeometry"
                                direction="right-left"
                                :label="['text' => ['Process item', 'index = index + 1'], 'width' => 'default']"
                                :dev-counter-end="12"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.break.finally-2.left.process.arc-out',
                                'anchorStart' => $trueGeometry['anchorEnd'],
                                'anchorEnd' => $trueEnd,
                                'startAnchor' => 'n',
                                'endAnchor' => 'w',
                                'arcRadius' => $radius,
                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'bottom',
                                'devCounterEnd' => 13,
                                'color' => 'green',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                                id="literature.break.finally-2.left.break-action"
                                :anchor-start="$falseStart"
                                :geometry="$falseGeometry"
                                direction="right-left"
                                :label="['text' => ['BREAK pending'], 'width' => 'default']"
                                :line-jumps="[
                                    [
                                        'over' => 'literature.break.finally-2.left.normal-finally.stem.before',
                                        'radius' => '0.65rem',
                                        'side' => 'bottom',
                                    ],
                                ]"
                                :dev-counter-end="14"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.break.finally-2.left.break-action.arc-out',
                                'anchorStart' => $falseGeometry['anchorEnd'],
                                'anchorEnd' => $falseEnd,
                                'startAnchor' => 's',
                                'endAnchor' => 'w',
                                'arcRadius' => $radius,
                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'top',
                                'devCounterEnd' => 15,
                                'color' => 'red',
                            ]" />
                            {{-- Both boxes denote the SAME FINALLY block, once on whichever route is taken. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally-2.left.normal-finally"
                                :anchor-start="$trueEnd"
                                direction="top-bottom"
                                before-length="12rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['FINALLY', 'Cleanup current item'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="16"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally-2.left.break-finally"
                                :anchor-start="$falseEnd"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['FINALLY', 'Cleanup current item'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="17"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.break.finally-2.left.body-return"
                                attach-to="literature.break.finally-2.left.normal-finally.anchorNode-end"
                                return-to="literature.break.finally-2.left.loop.anchorNode-return"
                                side="left"
                                :counter-start="18"
                                color="green"
                            />
                            {{-- The BREAK route exits only after its FINALLY has completed. --}}
                            @php
                                $leftBreak = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-2-left',
                                    'literature.break.finally-2.left.break-finally.anchorNode-end',
                                );
                                $leftExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-2-left',
                                    'literature.break.finally-2.left.loop.anchorNode-end',
                                );
                            @endphp
                            {{-- Normal exhaustion and BREAK after FINALLY meet above the loop. --}}
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.break.finally-2.left.normal-exit"
                                :anchor-start="$leftExit"
                                length="6rem"
                                :gradient="false"
                                :dev-counter-end="22"
                                color="zinc"
                            />
                            @php
                                $leftJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-2-left',
                                    'literature.break.finally-2.left.normal-exit.anchorNode-end',
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
                                id="literature.break.finally-2.left.break-rise"
                                :anchor-start="$leftBreak"
                                :length="$leftRiseLength"
                                :gradient="false"
                                :node-end="true"
                                :jointArrowEnd="true"
                                :dev-counter-end="false"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.break.finally-2.left.break-join"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-2-left',
                                    'literature.break.finally-2.left.break-rise.anchorNode-end',
                                )"
                                side="right"
                                :bridge-length="$leftBridge"
                                :node-end="false"
                                :dev-counter-end="false"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally-2.left.continue"
                                attach-to="literature.break.finally-2.left.normal-exit.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Continue after WHILE'],
                                    'width' => 'default',
                                ]"
                                :counter-end="23"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- break-finally-2-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="break-finally-2-right-example"
                        size="sm"
                    >{{ __('Body left / decision right') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- break-finally-2-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-break-finally-2-right"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally-2.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'index = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- WHILE owns the condition; the open body continues into the independent IF. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.break.finally-2.right.loop"
                                attach-to="literature.break.finally-2.right.initialize.anchorNode-end"
                                side="left"
                                true-bridge-length="4rem"
                                stem-length="3.5rem"
                                :condition-label="[
                                    'text' => ['WHILE index < count?'],
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '17.5rem',
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
                            {{-- Lower the U-turn explicitly before routing the question back to the left. --}}
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.break.finally-2.right.question.stem"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-2-right',
                                    'literature.break.finally-2.right.loop.body.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="4rem"
                                :gradient="false"
                                :joint-arrow-end="true"
                                :dev-counter-end="7"
                                color="sky"
                            />
                            @php
                                $graph = 'idea-to-paper-break-finally-2-right';
                                $radius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString(
                                    'arc_radius',
                                    '2.75rem',
                                );
                                $bodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.break.finally-2.right.question.stem.anchorNode-end',
                                );
                                $questionStart = [
                                    'x' => 'calc(' . $bodyEnd['x'] . ' + ' . $radius . ')',
                                    'y' => 'calc(' . $bodyEnd['y'] . ' - ' . $radius . ')',
                                ];
                                $questionGeometry = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::geometry(
                                    $questionStart,
                                    'left-right',
                                    '13rem',
                                    '2rem',
                                    '12rem',
                                );
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.break.finally-2.right.question.turn',
                                'anchorStart' => $bodyEnd,
                                'anchorEnd' => $questionStart,
                                'startAnchor' => 'w',
                                'endAnchor' => 's',
                                'arcRadius' => $radius,
                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'right',
                                'devCounterEnd' => 8,
                                'color' => 'sky',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                                id="literature.break.finally-2.right.question"
                                :anchor-start="$questionStart"
                                :geometry="$questionGeometry"
                                direction="left-right"
                                :label="[
                                    'text' => ['TRY', 'IF mayProcess(item)?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :line-jumps="[
                                    [
                                        'over' => 'literature.break.finally-2.right.loop.condition.stem.after',
                                        'radius' => '0.65rem',
                                        'side' => 'bottom',
                                    ],
                                ]"
                                :dev-counter-end="false"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.split
                                id="literature.break.finally-2.right.decision"
                                :anchor-start="$questionGeometry['anchorEnd']"
                                direction="left-right"
                                :outputs="[
                                    [
                                        'key' => 'true',
                                        'offset' => '4rem',
                                        'color' => 'green',
                                        'label' => ['text' => ['TRUE'], 'width' => 'half', 'side' => 'top'],
                                    ],
                                    [
                                        'key' => 'false',
                                        'offset' => '-4rem',
                                        'color' => 'red',
                                        'label' => ['text' => ['FALSE'], 'width' => 'half', 'side' => 'bottom'],
                                    ],
                                ]"
                                :counter-start="9"
                                color="sky"
                            />
                            @php
                                $trueStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.break.finally-2.right.decision.outputs.true.anchorNode-end',
                                );
                                $falseStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.break.finally-2.right.decision.outputs.false.anchorNode-end',
                                );
                                $trueGeometry = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::geometry(
                                    $trueStart,
                                    'left-right',
                                    '13rem',
                                    '2rem',
                                    '2rem',
                                );
                                $falseGeometry = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::geometry(
                                    $falseStart,
                                    'left-right',
                                    '13rem',
                                    '2rem',
                                    '10rem',
                                );
                                $trueEnd = [
                                    'x' => 'calc(' . $trueGeometry['anchorEnd']['x'] . ' + ' . $radius . ')',
                                    'y' => 'calc(' . $trueGeometry['anchorEnd']['y'] . ' - ' . $radius . ')',
                                ];
                                $falseEnd = [
                                    'x' => 'calc(' . $falseGeometry['anchorEnd']['x'] . ' + ' . $radius . ')',
                                    'y' => 'calc(' . $falseGeometry['anchorEnd']['y'] . ' + ' . $radius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                                id="literature.break.finally-2.right.process"
                                :anchor-start="$trueStart"
                                :geometry="$trueGeometry"
                                direction="left-right"
                                :label="['text' => ['Process item', 'index = index + 1'], 'width' => 'default']"
                                :dev-counter-end="12"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.break.finally-2.right.process.arc-out',
                                'anchorStart' => $trueGeometry['anchorEnd'],
                                'anchorEnd' => $trueEnd,
                                'startAnchor' => 'n',
                                'endAnchor' => 'e',
                                'arcRadius' => $radius,
                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'bottom',
                                'devCounterEnd' => 13,
                                'color' => 'green',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                                id="literature.break.finally-2.right.break-action"
                                :anchor-start="$falseStart"
                                :geometry="$falseGeometry"
                                direction="left-right"
                                :label="['text' => ['BREAK pending'], 'width' => 'default']"
                                :line-jumps="[
                                    [
                                        'over' => 'literature.break.finally-2.right.normal-finally.stem.before',
                                        'radius' => '0.65rem',
                                        'side' => 'bottom',
                                    ],
                                ]"
                                :dev-counter-end="14"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.break.finally-2.right.break-action.arc-out',
                                'anchorStart' => $falseGeometry['anchorEnd'],
                                'anchorEnd' => $falseEnd,
                                'startAnchor' => 's',
                                'endAnchor' => 'e',
                                'arcRadius' => $radius,
                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'top',
                                'devCounterEnd' => 15,
                                'color' => 'red',
                            ]" />
                            {{-- Both boxes denote the SAME FINALLY block, once on whichever route is taken. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally-2.right.normal-finally"
                                :anchor-start="$trueEnd"
                                direction="top-bottom"
                                before-length="12rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['FINALLY', 'Cleanup current item'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="16"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally-2.right.break-finally"
                                :anchor-start="$falseEnd"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['FINALLY', 'Cleanup current item'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="17"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.break.finally-2.right.body-return"
                                attach-to="literature.break.finally-2.right.normal-finally.anchorNode-end"
                                return-to="literature.break.finally-2.right.loop.anchorNode-return"
                                side="right"
                                :counter-start="18"
                                color="green"
                            />
                            {{-- The BREAK route exits only after its FINALLY has completed. --}}
                            @php
                                $rightBreak = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-2-right',
                                    'literature.break.finally-2.right.break-finally.anchorNode-end',
                                );
                                $rightExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-2-right',
                                    'literature.break.finally-2.right.loop.anchorNode-end',
                                );
                            @endphp
                            {{-- Normal exhaustion and BREAK after FINALLY meet above the loop. --}}
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.break.finally-2.right.normal-exit"
                                :anchor-start="$rightExit"
                                length="6rem"
                                :gradient="false"
                                :dev-counter-end="22"
                                color="zinc"
                            />
                            @php
                                $rightJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-2-right',
                                    'literature.break.finally-2.right.normal-exit.anchorNode-end',
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
                                id="literature.break.finally-2.right.break-rise"
                                :anchor-start="$rightBreak"
                                :length="$rightRiseLength"
                                :gradient="false"
                                :node-end="true"
                                :jointArrowEnd="true"
                                :dev-counter-end="false"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.break.finally-2.right.break-join"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-break-finally-2-right',
                                    'literature.break.finally-2.right.break-rise.anchorNode-end',
                                )"
                                side="left"
                                :bridge-length="$rightBridge"
                                :node-end="false"
                                :dev-counter-end="false"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.break.finally-2.right.continue"
                                attach-to="literature.break.finally-2.right.normal-exit.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Continue after WHILE'],
                                    'width' => 'default',
                                ]"
                                :counter-end="23"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- break-finally-2-right-example:end --}}
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/break-continue/flow-break-finally-2.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
