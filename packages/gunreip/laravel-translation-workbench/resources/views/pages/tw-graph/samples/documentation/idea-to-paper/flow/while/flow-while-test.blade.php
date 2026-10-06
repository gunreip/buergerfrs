<x-translation-workbench::ui.common.heading-counter-group group="flow-while-test">
    <section
        class="mt-4 min-w-0 space-y-4"
        id="flow-while-test-proposals"
    >
        <flux:callout
            color="indigo"
            icon="information-circle"
        >
            <flux:callout.heading>{{ __('WHILE variants — progress and next steps') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('WHILE basic, multiple actions, WHILE with IF, Nested WHILE, Two independent inner loops and Mixed sides and crossings are saved as left/right examples. Action → Nested WHILE → Action is also saved. WHILE with SWITCH is now saved as left/right examples; the test canvas keeps its current variant for further experiments. Each CASE or DEFAULT rejoins the shared index advance before returning to the WHILE condition.') }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="min-w-0"
            color="zinc"
            icon="list-bullet"
        >
            <flux:callout.heading>{{ __('Suggested sequence') }}</flux:callout.heading>
            <flux:table class="mt-3">
                <flux:table.columns>
                    <flux:table.column>#</flux:table.column>
                    <flux:table.column>Example</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>What it demonstrates</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell>1</flux:table.cell>
                        <flux:table.cell class="whitespace-normal">WHILE basic</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge
                                color="green"
                                size="sm"
                            >Saved · left / right</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Condition → TRUE → action → condition. FALSE goes to
                            the
                            next action outside the loop. Show left/right layouts and an initially false condition (zero
                            iterations) using the same structure.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>2</flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Multiple body actions</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge
                                color="green"
                                size="sm"
                            >Saved · left / right</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Process an item → advance to the next item → retest.
                            Only
                            the final body action returns to the condition; advancing belongs inside the loop.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>3</flux:table.cell>
                        <flux:table.cell class="whitespace-normal">WHILE with IF / SWITCH</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge
                                color="green"
                                size="sm"
                            >IF saved · left / right</flux:badge>
                            <flux:badge
                                color="green"
                                size="sm"
                            >SWITCH saved · left / right</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Branches inside the body merge before the loop
                            returns.
                            A
                            SWITCH BREAK exits its SWITCH and continues the WHILE body.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>4</flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Nested WHILE</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge
                                color="green"
                                size="sm"
                            >Saved · left / right</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal">The inner FALSE exits only the inner loop; execution
                            continues in the outer body. Each return must target its own condition.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>5</flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Two independent inner loops</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge
                                color="green"
                                size="sm"
                            >Saved · left / right</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Two inner loops in separate outer-body sections, each
                            with its own condition, body, exit and return.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>6</flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Mixed sides and crossings</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge
                                color="green"
                                size="sm"
                            >Saved · left / right</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Outer left / inner right and vice versa. Explicit
                            line-jumps or entry detours distinguish crossings from connected lanes.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>7</flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Action → Nested WHILE → Action</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge
                                color="green"
                                size="sm"
                            >Saved · left / right</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Prepare inner state → inner loop → finish processing
                            →
                            outer condition. The final action follows the inner FALSE exit, including when the inner
                            body
                            runs zero times.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </flux:callout>

        <flux:callout
            color="sky"
            icon="information-circle"
        >
            <flux:callout.heading>{{ __('Common rules for the examples') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Keep the condition separate from body actions. Label TRUE, FALSE and the return direction clearly. The return rejoins the condition after initialization, so initialization is not repeated accidentally. Use conditions and body updates that make progress explicit; drawing a return alone does not guarantee termination.') }}
            </flux:callout.text>
            <flux:text class="mt-2">DO WHILE, FOR and FOREACH will get their own sections. Loop BREAK, CONTINUE,
                RETURN
                and THROW remain part of the later control-transfer examples.</flux:text>
        </flux:callout>
        <x-translation-workbench::ui.common.tw-graph-path-file
            path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/while/flow-while-test.blade.php"
            segments="3"
        />
    </section>

    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('WHILE with SWITCH') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Read one item per iteration and select its action with SWITCH: edit a draft, display a published item, or record an unknown status. Every route rejoins before advancing the index. CASE BREAK exits only the SWITCH; it does not exit the WHILE. An initially false WHILE skips the whole body and shows the summary.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />

            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.while.flow-while-test" />

            @php
                $whileSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-test',
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
                            example="while-switch-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $whileSource->example('while-switch-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                    <code>loop.attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Initialization runs once. The WHILE starts at initialize.anchorNode-end; the return never repeats initialization.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>loop.side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>left</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('The body opens to the left; the nested SWITCH uses the same physical side and runs top-bottom.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>loop.true-bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicitly 4rem between the TRUE arc and the body action.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>loop.stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicitly 3.5rem for the FALSE exit.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>condition-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('WHILE index < count? The visible beforeLength, labelGap and afterLength reserve room for the downward SWITCH and shared advance; afterLength is explicitly 30rem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>action-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Read current item. beforeLength and afterLength are 2rem; return=false opens the body for the independent SWITCH.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('TRUE attaches to the condition on its right side, in green.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('FALSE and its exit stem use red. Only this connection reaches Show summary.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inner-switch.attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Connects directly to loop.body.anchorNode-end.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inner-switch.direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bottom-top</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicitly top-bottom to continue the downward WHILE body.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>case-expression</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>SWITCH expression</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Displays SWITCH item.status. stemLength=3rem sets the first CASE entry length.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cases</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Two individually authored CASE entries: draft edits the draft; published displays the item. actionLabel specifies text, width and color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inner-switch.stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>10rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicitly 4rem between the subsequent CASE and DEFAULT entries.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cases[].exitLabel</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>BREAK</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('The standard CASE exit ends only the SWITCH. No loop BREAK is used in this example.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>case-default</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>Default action</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Unknown statuses are recorded, then join the same SWITCH output.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>advance.attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('inner-switch.anchorNode-end is the common output of every CASE and DEFAULT. Increment the index exactly once after the selected action.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>advance.direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bottom-top</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicitly top-bottom, with before-length=2rem, label-gap=4rem and after-length=2rem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>advance.node-end-dot</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicitly false, with joint-arrow-end=true for the technical connection to the return.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>body-return.attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Return begins only at advance.anchorNode-end, after the shared index increment.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>body-return.return-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Targets loop.anchorNode-return, before the next condition check. paths.loop-return derives its dimensions from both anchors.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>continue.attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('loop.anchorNode-end is the WHILE FALSE exit. Shows the summary even when the collection starts empty.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inner-switch.counter-start<br>advance.counter-end<br>body-return.counter-start<br>continue.counter-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>1</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicitly 7, 14, 15 and 19 to continue DEV numbering after the six WHILE nodes. The SWITCH uses seven counters; the return uses four.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>

            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-test"
                example="while-switch"
            >
                <flux:text class="mt-2 text-sm">
                    {{ __('BREAK leaves only the SWITCH; the shared index increment still runs inside the WHILE. Types and action helpers are supplied by the application. C and C++ use enum status values; the other examples use strings.') }}
                </flux:text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Path/To/File --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('WHILE with SWITCH') }}</flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('This test preview places a SWITCH inside the WHILE body. Follow each CASE or DEFAULT action to the shared index increment and back to the WHILE condition. BREAK leaves the SWITCH, while FALSE at the loop condition bypasses the entire body and reaches the summary.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="while-switch-example"
                        size="sm"
                    >
                        {{ __('side="left"') }}
                    </x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- while-switch-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-while-basic"
                            :dev="true"
                            :coordinates="true"
                            horizontalPadding="6rem"
                            min-height="68rem"
                            min-width="88rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.while.1.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'index = 0'], 'width' => 'default']"
                                afterLength="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- TRUE reads one item; the open body continues into the nested SWITCH. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.while.1.loop"
                                attach-to="literature.while.1.initialize.anchorNode-end"
                                side="left"
                                color="cyan"
                                true-bridge-length="4rem"
                                stem-length="3.5rem"
                                :condition-label="[
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '30rem',
                                    'text' => ['WHILE index < count?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :action-label="[
                                    'beforeLength' => '2rem',
                                    'afterLength' => '2rem',
                                    'text' => ['Read current item'],
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
                            {{-- All CASE exits and DEFAULT rejoin before the shared index increment. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                                id="literature.while.1.inner-switch"
                                :counter-start="7"
                                attach-to="literature.while.1.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="left"
                                stem-length="4rem"
                                color="sky"
                                :case-expression="[
                                    'text' => ['SWITCH item.status'],
                                    'width' => 'default',
                                    'stemLength' => '3rem',
                                ]"
                                :cases="[
                                    [
                                        'key' => 'draft',
                                        'label' => 'CASE draft',
                                        'actionLabel' => [
                                            'text' => ['Edit draft'],
                                            'width' => 'default',
                                            'color' => 'amber',
                                        ],
                                    ],
                                    [
                                        'key' => 'published',
                                        'label' => 'CASE published',
                                        'actionLabel' => [
                                            'text' => ['Display item'],
                                            'width' => 'default',
                                            'color' => 'green',
                                        ],
                                    ],
                                ]"
                                :case-default="[
                                    'text' => ['Record unknown status'],
                                    'width' => 'default',
                                    'color' => 'zinc',
                                ]"
                            />
                            {{-- The next action is an independent component inside the body. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.while.1.advance"
                                attach-to="literature.while.1.inner-switch.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['index = index + 1'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="14"
                                color="cyan"
                            />
                            {{-- Return only after advancing, to the same condition (not initialization). --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.while.1.body-return"
                                attach-to="literature.while.1.advance.anchorNode-end"
                                return-to="literature.while.1.loop.anchorNode-return"
                                side="left"
                                :counter-start="15"
                                color="cyan"
                            />
                            {{-- FALSE continues here, including when the item list starts empty. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.while.1.continue"
                                :counter-end="19"
                                attach-to="literature.while.1.loop.anchorNode-end"
                                beforeLength="4rem"
                                :step-label="['text' => ['Show summary'], 'width' => 'default']"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- while-switch-example:end --}}
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            {{-- Path/To/File --}}
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/while/flow-while-test.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
