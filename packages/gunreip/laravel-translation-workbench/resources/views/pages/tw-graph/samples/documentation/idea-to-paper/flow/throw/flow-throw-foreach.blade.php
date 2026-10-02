<x-translation-workbench::ui.common.heading-counter-group group="flow-throw-foreach">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('THROW inside FOREACH') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('One TRY surrounds the whole FOREACH. Valid items are processed and return to the next-item check. An invalid item throws InvalidArgumentException and leaves the loop immediately. The outer CATCH reports it; remaining items and the rest of the TRY body are skipped. Execution then continues after TRY/CATCH.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.throw.flow-throw-foreach" />

            @php
                $throwSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-foreach',
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
                            example="throw-foreach-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-foreach-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="throw-foreach-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-foreach-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>side</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Keeps the loop, decision and success return on the same side.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>after-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The condition uses 12rem to leave room for the authored decision and handler.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start / if-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Valid items return to the loop; invalid items THROW into the outer CATCH.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>return</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('False keeps the loop body open for the explicit validation and success return.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>exitDirection</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>top-bottom</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The exceptional decision exit points bottom-top toward the outer CATCH.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>lineJumps</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Marks the crossing between the exception lane and the successful loop return.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>attach-to / return-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Connects each route to its real continuation; only success targets the loop return.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>length / bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The normal exit is explicitly 6rem. Failure rise and bridge lengths use the registered anchors and default arc radius.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-end /
                                        dev-counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Continues marker numbering across the loop, decision, outer handler and final join.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-foreach"
                example="throw-foreach"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('Each exception-based snippet places CATCH outside the iteration. For [valid, invalid, valid], only the first item is processed. Empty input and all-valid input complete normally without entering CATCH. C uses an explicit failure status and exits the iteration before reporting it.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('THROW inside FOREACH — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Green returns to FOREACH after processing a valid item. Red THROW exits the loop and reaches the amber outer CATCH. Both normal exhaustion and handled failure meet at the continuation after TRY/CATCH; the CATCH route never returns to another iteration.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-foreach-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-foreach-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-foreach-left"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="[
                                    'text' => ['Load items:', ' valid, invalid, valid'],
                                    'width' => 'default',
                                ]"
                                after-length="3rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Iteration setup runs once; the return skips this step. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach.left.iterator"
                                attach-to="literature.throw.foreach.left.initialize.anchorNode-end"
                                :step-label="['text' => ['TRY: open iteration'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Check before every iteration, including the first. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach.left.condition"
                                attach-to="literature.throw.foreach.left.iterator.anchorNode-end"
                                before-length="2rem"
                                {{-- label-gap="4rem" --}}
                                after-length="12rem"
                                :step-label="[
                                    'text' => ['FOREACH next item?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="1"
                                color="cyan"
                            />
                            {{-- TRUE selects an item; FALSE leaves the loop. --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.throw.foreach.left.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-left',
                                    'literature.throw.foreach.left.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-left',
                                    'literature.throw.foreach.left.iterator.anchorNode-end',
                                )"
                                side="left"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="2rem"
                                exit-length="5.5rem"
                                :bridge-label="[
                                    'text' => ['Read current item'],
                                    'width' => 'default',
                                    'align' => 'center',
                                    'color' => 'green',
                                ]"
                                :entry-label="['text' => ['TRUE'], 'width' => 'half', 'side' => 'top', 'color' => 'green']"
                                :exit-label="[
                                    'text' => ['FALSE'],
                                    'width' => 'half',
                                    'side' => 'right',
                                    'color' => 'red',
                                ]"
                                color="cyan"
                            />
                            {{-- Only valid items return to FOREACH. THROW leaves the entire loop. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.throw.foreach.left.dispatch"
                                attach-to="literature.throw.foreach.left.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="left"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['IF current item is valid?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE', 'Process valid item'],
                                    'return' => false,
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: THROW', 'InvalidArgumentException'],
                                    'return' => false,
                                    'returnOffset' => '4rem',
                                    'exitDirection' => 'bottom-top',
                                    'lineJumps' => [
                                        [
                                            'over' => 'literature.throw.foreach.left.body-return.stem',
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
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.throw.foreach.left.body-return"
                                attach-to="literature.throw.foreach.left.dispatch.true.anchorNode-end"
                                return-to="literature.throw.foreach.left.loop.anchorNode-return"
                                side="left"
                                :counter-start="11"
                                color="green"
                            />
                            {{-- This handler belongs to the outer TRY, not to each iteration. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach.left.catch"
                                attach-to="literature.throw.foreach.left.dispatch.false.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="['text' => ['Outer CATCH', 'Report invalid item'], 'width' => 'default']"
                                :counter-end="15"
                                color="amber"
                            />
                            {{-- The explicit CATCH exitDirection leads directly upward outside the success return. --}}
                            @php
                                $leftFailure = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-left',
                                    'literature.throw.foreach.left.catch.anchorNode-end',
                                );
                                $leftExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-left',
                                    'literature.throw.foreach.left.loop.anchorNode-end',
                                );
                            @endphp
                            {{-- Both exhaustion and handled failure join above the loop. --}}
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.throw.foreach.left.normal-exit"
                                :anchor-start="$leftExit"
                                length="6rem"
                                :gradient="false"
                                :dev-counter-end="16"
                                color="zinc"
                            />
                            @php
                                $leftJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-left',
                                    'literature.throw.foreach.left.normal-exit.anchorNode-end',
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
                                    $leftFailure['y'] .
                                    ')';
                                $leftBridge =
                                    'calc((' .
                                    $leftJoin['x'] .
                                    ' - ' .
                                    $leftFailure['x'] .
                                    ') * 1 - (2 * ' .
                                    $leftRadius .
                                    '))';
                                $lengthProvenance = $__env->getConsumableComponentData('twGraphCalculatedLengths');
                                $lengthProvenance?->record(
                                    'literature.throw.foreach.left.failure-rise',
                                    'parts.start',
                                    'literature.throw.foreach.left.failure-rise',
                                    'length',
                                    ['start' => $leftFailure, 'target' => $leftJoin, 'arcRadius' => $leftRadius],
                                    'Vertical distance to the join minus both arc radii.',
                                );
                                $lengthProvenance?->record(
                                    'literature.throw.foreach.left.failure-join.bridge1',
                                    'parts.sideways',
                                    'literature.throw.foreach.left.failure-join',
                                    'bridge-length',
                                    ['start' => $leftFailure, 'target' => $leftJoin, 'arcRadius' => $leftRadius],
                                    'Horizontal distance to the join minus both arc radii.',
                                );
                            @endphp
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.throw.foreach.left.failure-rise"
                                :anchor-start="$leftFailure"
                                :length="$leftRiseLength"
                                :gradient="false"
                                :joint-arrow-end="true"
                                :dev-counter-end="17"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.foreach.left.failure-join"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-left',
                                    'literature.throw.foreach.left.failure-rise.anchorNode-end',
                                )"
                                side="left"
                                :bridge-length="$leftBridge"
                                :node-end="false"
                                :dev-counter-end="false"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach.left.continue"
                                attach-to="literature.throw.foreach.left.normal-exit.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Continue after TRY'],
                                    'width' => 'default',
                                ]"
                                :counter-end="18"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-foreach-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-foreach-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-foreach-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-foreach-right"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="[
                                    'text' => ['Load items:', ' valid, invalid, valid'],
                                    'width' => 'default',
                                ]"
                                after-length="3rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Iteration setup runs once; the return skips this step. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach.right.iterator"
                                attach-to="literature.throw.foreach.right.initialize.anchorNode-end"
                                :step-label="['text' => ['TRY: open iteration'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Check before every iteration, including the first. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach.right.condition"
                                attach-to="literature.throw.foreach.right.iterator.anchorNode-end"
                                before-length="2rem"
                                {{-- label-gap="4rem" --}}
                                after-length="12rem"
                                :step-label="[
                                    'text' => ['FOREACH next item?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="1"
                                color="cyan"
                            />
                            {{-- TRUE selects an item; FALSE leaves the loop. --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.throw.foreach.right.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-right',
                                    'literature.throw.foreach.right.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-right',
                                    'literature.throw.foreach.right.iterator.anchorNode-end',
                                )"
                                side="right"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="2rem"
                                exit-length="5.5rem"
                                :bridge-label="[
                                    'text' => ['Read current item'],
                                    'width' => 'default',
                                    'align' => 'center',
                                    'color' => 'green',
                                ]"
                                :entry-label="['text' => ['TRUE'], 'width' => 'half', 'side' => 'top', 'color' => 'green']"
                                :exit-label="[
                                    'text' => ['FALSE'],
                                    'width' => 'half',
                                    'side' => 'left',
                                    'color' => 'red',
                                ]"
                                color="cyan"
                            />
                            {{-- Only valid items return to FOREACH. THROW leaves the entire loop. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.throw.foreach.right.dispatch"
                                attach-to="literature.throw.foreach.right.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="right"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['IF current item is valid?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE', 'Process valid item'],
                                    'return' => false,
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: THROW', 'InvalidArgumentException'],
                                    'return' => false,
                                    'returnOffset' => '4rem',
                                    'exitDirection' => 'bottom-top',
                                    'lineJumps' => [
                                        [
                                            'over' => 'literature.throw.foreach.right.body-return.stem',
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
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.throw.foreach.right.body-return"
                                attach-to="literature.throw.foreach.right.dispatch.true.anchorNode-end"
                                return-to="literature.throw.foreach.right.loop.anchorNode-return"
                                side="right"
                                :counter-start="11"
                                color="green"
                            />
                            {{-- This handler belongs to the outer TRY, not to each iteration. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach.right.catch"
                                attach-to="literature.throw.foreach.right.dispatch.false.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="['text' => ['Outer CATCH', 'Report invalid item'], 'width' => 'default']"
                                :counter-end="15"
                                color="amber"
                            />
                            {{-- The explicit CATCH exitDirection leads directly upward outside the success return. --}}
                            @php
                                $rightFailure = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-right',
                                    'literature.throw.foreach.right.catch.anchorNode-end',
                                );
                                $rightExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-right',
                                    'literature.throw.foreach.right.loop.anchorNode-end',
                                );
                            @endphp
                            {{-- Both exhaustion and handled failure join above the loop. --}}
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.throw.foreach.right.normal-exit"
                                :anchor-start="$rightExit"
                                length="6rem"
                                :gradient="false"
                                :dev-counter-end="16"
                                color="zinc"
                            />
                            @php
                                $rightJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-right',
                                    'literature.throw.foreach.right.normal-exit.anchorNode-end',
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
                                    $rightFailure['y'] .
                                    ')';
                                $rightBridge =
                                    'calc((' .
                                    $rightJoin['x'] .
                                    ' - ' .
                                    $rightFailure['x'] .
                                    ') * -1 - (2 * ' .
                                    $rightRadius .
                                    '))';
                                $lengthProvenance = $__env->getConsumableComponentData('twGraphCalculatedLengths');
                                $lengthProvenance?->record(
                                    'literature.throw.foreach.right.failure-rise',
                                    'parts.start',
                                    'literature.throw.foreach.right.failure-rise',
                                    'length',
                                    ['start' => $rightFailure, 'target' => $rightJoin, 'arcRadius' => $rightRadius],
                                    'Vertical distance to the join minus both arc radii.',
                                );
                                $lengthProvenance?->record(
                                    'literature.throw.foreach.right.failure-join.bridge1',
                                    'parts.sideways',
                                    'literature.throw.foreach.right.failure-join',
                                    'bridge-length',
                                    ['start' => $rightFailure, 'target' => $rightJoin, 'arcRadius' => $rightRadius],
                                    'Horizontal distance to the join minus both arc radii.',
                                );
                            @endphp
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.throw.foreach.right.failure-rise"
                                :anchor-start="$rightFailure"
                                :length="$rightRiseLength"
                                :gradient="false"
                                :joint-arrow-end="true"
                                :dev-counter-end="17"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.foreach.right.failure-join"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-right',
                                    'literature.throw.foreach.right.failure-rise.anchorNode-end',
                                )"
                                side="right"
                                :bridge-length="$rightBridge"
                                :node-end="false"
                                :dev-counter-end="false"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach.right.continue"
                                attach-to="literature.throw.foreach.right.normal-exit.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Continue after TRY'],
                                    'width' => 'default',
                                ]"
                                :counter-end="18"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-foreach-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/flow-throw-foreach.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
