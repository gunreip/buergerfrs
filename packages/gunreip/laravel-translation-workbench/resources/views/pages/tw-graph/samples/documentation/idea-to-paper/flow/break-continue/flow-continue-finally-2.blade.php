<x-translation-workbench::ui.common.heading-counter-group group="flow-continue-finally-2">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('CONTINUE with FINALLY (2) — horizontal decision') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The body reads an item and advances the index before TRY. A horizontal question and split route TRUE past processing with CONTINUE pending; FALSE processes the item. Both lanes join before one FINALLY cleanup and return to the next WHILE check.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.break-continue.flow-continue-finally-2" />

            @php
                $continueSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-finally-2',
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
                            example="continue-finally-2-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Body right / decision left') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $continueSource->example('continue-finally-2-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="continue-finally-2-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Body left / decision right') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $continueSource->example('continue-finally-2-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>loop.side<br>action-label.return</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left / true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('The body and horizontal decision face opposite sides. The open body reads the item and advances the index before TRY.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label.afterLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicitly 17.5rem leaves room for the U-turn, horizontal decision and cleanup.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>question.stem.length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>inherited</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicitly 4rem lowers the U-turn before the question.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>question.geometry<br>line-jumps</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null / []</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('The question uses a 13rem label, 2rem incoming and 12rem outgoing bridge, with a 0.65rem line-jump over the WHILE condition stem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>split.outputs[].key<br>offset<br>color<br>label</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>required / required / inherited / null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('TRUE sits 4rem above and FALSE 4rem below the input. TRUE requests CONTINUE; FALSE processes the item.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>process.geometry<br>continue-action.geometry</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Both action bridges explicitly use a 13rem label and 2rem incoming and outgoing lengths.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>fusion.inputs[].key<br>anchor<br>color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>required / required / inherited</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('The actual action endpoints feed the shared fusion. Both routes enter FINALLY once.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>finally.before-length<br>label-gap<br>after-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem / 4rem / 2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('The cleanup step points downward and explicitly uses 2rem / 4rem / 2rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>return-to</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Only the FINALLY endpoint feeds the return to loop.anchorNode-return. CONTINUE never joins the exhausted loop exit.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-start<br>counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the composed components continuously from 1 to 21.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-finally-2"
                example="continue-finally-2"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('Both routes enter the same FINALLY block exactly once per item. The index advances before TRY, including when processing is skipped. Cleanup completes normally here; exceptions and control transfers from FINALLY are outside this example. C uses explicit cleanup; C++ uses a scope guard.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('CONTINUE with FINALLY (2) — horizontal decision — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The two examples mirror the body and horizontal decision. A U-turn leads into the TRY/IF question, then a line-jump crosses the WHILE condition stem. parts.split distributes the flow into TRUE and FALSE. parts.fusion joins the two routes before the shared FINALLY action and loop return.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="continue-finally-2-left-example"
                        size="sm"
                    >{{ __('Body right / decision left') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- continue-finally-2-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-continue-finally-2-left"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.finally-2.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'index = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- WHILE owns the condition; the open body continues into the independent IF. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.continue.finally-2.left.loop"
                                attach-to="literature.continue.finally-2.left.initialize.anchorNode-end"
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
                                    'text' => ['Read current item', 'index = index + 1'],
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
                                id="literature.continue.finally-2.left.question.stem"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-continue-finally-2-left',
                                    'literature.continue.finally-2.left.loop.body.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="4rem"
                                :gradient="false"
                                :joint-arrow-end="true"
                                :dev-counter-end="7"
                                color="sky"
                            />
                            @php
                                $graph = 'idea-to-paper-continue-finally-2-left';
                                $radius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString(
                                    'arc_radius',
                                    '2.75rem',
                                );
                                $bodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.continue.finally-2.left.question.stem.anchorNode-end',
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
                                'id' => 'literature.continue.finally-2.left.question.turn',
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
                                id="literature.continue.finally-2.left.question"
                                :anchor-start="$questionStart"
                                :geometry="$questionGeometry"
                                direction="right-left"
                                :label="[
                                    'text' => ['TRY', 'IF shouldSkip(item)?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :line-jumps="[
                                    [
                                        'over' => 'literature.continue.finally-2.left.loop.condition.stem.after',
                                        'radius' => '0.65rem',
                                        'side' => 'bottom',
                                    ],
                                ]"
                                :dev-counter-end="false"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.split
                                id="literature.continue.finally-2.left.decision"
                                :anchor-start="$questionGeometry['anchorEnd']"
                                direction="right-left"
                                :outputs="[
                                    [
                                        'key' => 'true',
                                        'offset' => '4rem',
                                        'color' => 'amber',
                                        'label' => ['text' => ['TRUE'], 'width' => 'half', 'side' => 'top'],
                                    ],
                                    [
                                        'key' => 'false',
                                        'offset' => '-4rem',
                                        'color' => 'green',
                                        'label' => ['text' => ['FALSE'], 'width' => 'half', 'side' => 'bottom'],
                                    ],
                                ]"
                                :counter-start="9"
                                color="sky"
                            />
                            @php
                                $trueStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.continue.finally-2.left.decision.outputs.true.anchorNode-end',
                                );
                                $falseStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.continue.finally-2.left.decision.outputs.false.anchorNode-end',
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
                                    '2rem',
                                );
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                                id="literature.continue.finally-2.left.continue-action"
                                :anchor-start="$trueStart"
                                :geometry="$trueGeometry"
                                direction="right-left"
                                :label="['text' => ['CONTINUE pending'], 'width' => 'default']"
                                :dev-counter-end="12"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                                id="literature.continue.finally-2.left.process"
                                :anchor-start="$falseStart"
                                :geometry="$falseGeometry"
                                direction="right-left"
                                :label="['text' => ['Process item'], 'width' => 'default']"
                                :dev-counter-end="13"
                                color="green"
                            />
                            {{-- Both outcomes must pass through the same cleanup before returning. --}}
                            <x-translation-workbench::ui.tw-graph.parts.fusion
                                id="literature.continue.finally-2.left.cleanup-join"
                                direction="right-left"
                                :inputs="[
                                    ['key' => 'skip', 'anchor' => $trueGeometry['anchorEnd'], 'color' => 'amber'],
                                    ['key' => 'process', 'anchor' => $falseGeometry['anchorEnd'], 'color' => 'green'],
                                ]"
                                :dev-counter-end="14"
                                color="violet"
                            />
                            @php
                                $cleanupJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.continue.finally-2.left.cleanup-join.anchorNode-end',
                                );
                                $cleanupStart = [
                                    'x' => 'calc(' . $cleanupJoin['x'] . ' - ' . $radius . ')',
                                    'y' => 'calc(' . $cleanupJoin['y'] . ' - ' . $radius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.continue.finally-2.left.cleanup-turn',
                                'anchorStart' => $cleanupJoin,
                                'anchorEnd' => $cleanupStart,
                                'startAnchor' => 'n',
                                'endAnchor' => 'w',
                                'arcRadius' => $radius,
                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'bottom',
                                'devCounterEnd' => 15,
                                'color' => 'violet',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.finally-2.left.finally"
                                :anchor-start="$cleanupStart"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['FINALLY', 'Cleanup current item'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="16"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.continue.finally-2.left.body-return"
                                attach-to="literature.continue.finally-2.left.finally.anchorNode-end"
                                return-to="literature.continue.finally-2.left.loop.anchorNode-return"
                                side="left"
                                :counter-start="17"
                                color="green"
                            />
                            {{-- Only normal exhaustion reaches the summary. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.finally-2.left.continue"
                                attach-to="literature.continue.finally-2.left.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="['text' => ['Show summary after WHILE'], 'width' => 'default']"
                                :counter-end="21"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- continue-finally-2-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="continue-finally-2-right-example"
                        size="sm"
                    >{{ __('Body left / decision right') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- continue-finally-2-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-continue-finally-2-right"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.finally-2.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'index = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- WHILE owns the condition; the open body continues into the independent IF. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.continue.finally-2.right.loop"
                                attach-to="literature.continue.finally-2.right.initialize.anchorNode-end"
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
                                    'text' => ['Read current item', 'index = index + 1'],
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
                                id="literature.continue.finally-2.right.question.stem"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-continue-finally-2-right',
                                    'literature.continue.finally-2.right.loop.body.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="4rem"
                                :gradient="false"
                                :joint-arrow-end="true"
                                :dev-counter-end="7"
                                color="sky"
                            />
                            @php
                                $graph = 'idea-to-paper-continue-finally-2-right';
                                $radius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString(
                                    'arc_radius',
                                    '2.75rem',
                                );
                                $bodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.continue.finally-2.right.question.stem.anchorNode-end',
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
                                'id' => 'literature.continue.finally-2.right.question.turn',
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
                                id="literature.continue.finally-2.right.question"
                                :anchor-start="$questionStart"
                                :geometry="$questionGeometry"
                                direction="left-right"
                                :label="[
                                    'text' => ['TRY', 'IF shouldSkip(item)?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :line-jumps="[
                                    [
                                        'over' => 'literature.continue.finally-2.right.loop.condition.stem.after',
                                        'radius' => '0.65rem',
                                        'side' => 'bottom',
                                    ],
                                ]"
                                :dev-counter-end="false"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.split
                                id="literature.continue.finally-2.right.decision"
                                :anchor-start="$questionGeometry['anchorEnd']"
                                direction="left-right"
                                :outputs="[
                                    [
                                        'key' => 'true',
                                        'offset' => '4rem',
                                        'color' => 'amber',
                                        'label' => ['text' => ['TRUE'], 'width' => 'half', 'side' => 'top'],
                                    ],
                                    [
                                        'key' => 'false',
                                        'offset' => '-4rem',
                                        'color' => 'green',
                                        'label' => ['text' => ['FALSE'], 'width' => 'half', 'side' => 'bottom'],
                                    ],
                                ]"
                                :counter-start="9"
                                color="sky"
                            />
                            @php
                                $trueStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.continue.finally-2.right.decision.outputs.true.anchorNode-end',
                                );
                                $falseStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.continue.finally-2.right.decision.outputs.false.anchorNode-end',
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
                                    '2rem',
                                );
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                                id="literature.continue.finally-2.right.continue-action"
                                :anchor-start="$trueStart"
                                :geometry="$trueGeometry"
                                direction="left-right"
                                :label="['text' => ['CONTINUE pending'], 'width' => 'default']"
                                :dev-counter-end="12"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                                id="literature.continue.finally-2.right.process"
                                :anchor-start="$falseStart"
                                :geometry="$falseGeometry"
                                direction="left-right"
                                :label="['text' => ['Process item'], 'width' => 'default']"
                                :dev-counter-end="13"
                                color="green"
                            />
                            {{-- Both outcomes must pass through the same cleanup before returning. --}}
                            <x-translation-workbench::ui.tw-graph.parts.fusion
                                id="literature.continue.finally-2.right.cleanup-join"
                                direction="left-right"
                                :inputs="[
                                    ['key' => 'skip', 'anchor' => $trueGeometry['anchorEnd'], 'color' => 'amber'],
                                    ['key' => 'process', 'anchor' => $falseGeometry['anchorEnd'], 'color' => 'green'],
                                ]"
                                :dev-counter-end="14"
                                color="violet"
                            />
                            @php
                                $cleanupJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    $graph,
                                    'literature.continue.finally-2.right.cleanup-join.anchorNode-end',
                                );
                                $cleanupStart = [
                                    'x' => 'calc(' . $cleanupJoin['x'] . ' + ' . $radius . ')',
                                    'y' => 'calc(' . $cleanupJoin['y'] . ' - ' . $radius . ')',
                                ];
                            @endphp
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.continue.finally-2.right.cleanup-turn',
                                'anchorStart' => $cleanupJoin,
                                'anchorEnd' => $cleanupStart,
                                'startAnchor' => 'n',
                                'endAnchor' => 'e',
                                'arcRadius' => $radius,
                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'bottom',
                                'devCounterEnd' => 15,
                                'color' => 'violet',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.finally-2.right.finally"
                                :anchor-start="$cleanupStart"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['FINALLY', 'Cleanup current item'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="16"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.continue.finally-2.right.body-return"
                                attach-to="literature.continue.finally-2.right.finally.anchorNode-end"
                                return-to="literature.continue.finally-2.right.loop.anchorNode-return"
                                side="right"
                                :counter-start="17"
                                color="green"
                            />
                            {{-- Only normal exhaustion reaches the summary. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.finally-2.right.continue"
                                attach-to="literature.continue.finally-2.right.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="['text' => ['Show summary after WHILE'], 'width' => 'default']"
                                :counter-end="21"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- continue-finally-2-right-example:end --}}
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/break-continue/flow-continue-finally-2.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
