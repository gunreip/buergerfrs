<section
    class="mt-4 min-w-0 space-y-4"
    id="flow-while-test-proposals"
>
    <flux:callout
        color="indigo"
        icon="information-circle"
    >
        <flux:callout.heading>{{ __('WHILE variants — progress and next steps') }}</flux:callout.heading>
        <flux:callout.text>WHILE basic, multiple actions, WHILE with IF, Nested WHILE, Two independent inner loops
            and Mixed sides and crossings are saved as left/right examples. Current test: Action → Nested WHILE →
            Action.
            Prepare a group, process its items, finalize the group and advance the outer index.
            The SWITCH body variant remains planned.</flux:callout.text>
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
                    <flux:table.cell class="whitespace-normal">Condition → TRUE → action → condition. FALSE goes to the
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
                            color="zinc"
                            size="sm"
                        >SWITCH planned</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Branches inside the body merge before the loop returns.
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
                    <flux:table.cell class="whitespace-normal">Prepare inner state → inner loop → finish processing →
                        outer condition. The final action follows the inner FALSE exit, including when the inner body
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
        <flux:callout.text>Keep the condition separate from body actions. Label TRUE, FALSE and the return direction
            clearly. The return rejoins the condition after initialization, so initialization is not repeated
            accidentally. Use conditions and body updates that make progress explicit; drawing a return alone does not
            guarantee termination.</flux:callout.text>
        <flux:text class="mt-2">DO WHILE, FOR and FOREACH will get their own sections. Loop BREAK, CONTINUE, RETURN
            and THROW remain part of the later control-transfer examples.</flux:text>
    </flux:callout>
    <flux:field class="flex justify-end font-mono text-xs text-zinc-400">
        .../flow/while/flow-while-test.blade.php
    </flux:field>
</section>

<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>Action → Nested WHILE → Action</flux:callout.heading>
        <flux:callout.text>Prepare each group before entering its item loop. Process its items, then finalize the
            group after the inner FALSE exit. Finalization also runs for an empty group. Only after finalization does
            the group index advance and the outer WHILE check its condition again. Preparation and finalization each
            run once per outer iteration; the inner return repeats neither action.</flux:callout.text>
        @php
            $whileSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-test',
            );
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >side="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $whileSource->example('while-action-sequence-example') }}</x-translation-workbench::ui.tw-graph.code-box>
        <x-translation-workbench::ui.tw-graph.documentation-links example="flow.while.flow-while-test" />
        <flux:heading
            class="mt-4"
            size="sm"
        >Props and connections</flux:heading>
        <flux:table
            class="mt-3"
            container:class="max-h-80"
        >
            <flux:table.columns sticky>
                <flux:table.column>Prop / anchor</flux:table.column>
                <flux:table.column>Default</flux:table.column>
                <flux:table.column>Purpose</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">side</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">left</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Physical side of the body and return: left or right. The
                        FALSE exit stays on the main axis.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">attach-to</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Connect the loop after initialization. The return rejoins
                        here without repeating initialization.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">condition-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">WHILE pending items?</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Condition text, width, align and badge color.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">action-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Process next item</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">First action inside the horizontal body bridge. The
                        separate advance step makes progress.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">condition-label.beforeLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Stem before the condition.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">condition-label.labelGap</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">4rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Space reserved for the condition text.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">condition-label.afterLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Stem between the condition and the TRUE/FALSE split: 6rem
                        outside, 8rem inside.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">arc-radius</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">inherited / 2.75rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Common radius of the four loop corners.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">true-bridge-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Separate bridge after the TRUE arc. The TRUE label is
                        attached to the condition here; the return bridge grows by the same length.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">action-label.beforeLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">4rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Bridge before the body action.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">action-label.afterLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">4rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Bridge after the body action: 16rem outside reserves the
                        separate return lane and its crossing; 2rem inside.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">stem-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">4rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">FALSE stem to the public exit. The independent return
                        uses the remaining height after its body. Each loop has its own exit and return.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">true-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">TRUE</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Information at the body entry; text, width, align, side
                        and connector settings.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">false-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">FALSE</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Information at the loop exit; text, width, align, side
                        and connector settings.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">inherited / zinc</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Loop paths and labels; explicit label colors remain
                        configurable.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">anchorNode-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">connection</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Initialization and return meet before the condition.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">connection</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">FALSE exit. Attach the next independent action here.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">action-label.return</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">true</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Explicitly false here: leave the body open for the
                        independent nested loop or advance action. Both automatic returns are omitted. inner-entry uses
                        the same rounded path to reverse the incoming direction; its 12rem horizontal and 8rem vertical
                        offsets are explicit.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">body.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">connection</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Outer body: inner-entry turns down-left-up toward the
                        inner WHILE. Inner body: attach the item increment with direction top-bottom.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">anchorNode-return</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">connection</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Target before the condition. paths.loop-return connects
                        the final body action to this target; its remaining stem length comes from both anchors.
                        Insufficient room produces an error instead of moving the condition.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">node-end-dot</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">true (flow-step)</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Explicitly false for the advance action: no Dot at this
                        technical connection.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">joint-arrow-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">false</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Explicitly true for the advance action: a downward
                        joint-arrow connects to the return stem.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">action-label.color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">inherited</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Color of the complete TRUE action route, including both
                        arcs and the entry bridge. Explicitly green here.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">true-label.anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">bridge</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">condition attaches TRUE to the decision Dot; bridge
                        keeps its original position at the entry bridge end.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">true-label.side</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">top</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Right in the left example, left in the right example.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">false-label.color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">inherited</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Colors the FALSE stem, its end Dot and DEV counter.
                        Explicitly red here.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">action-label.lineJumps</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">The outer action's outgoing bridge jumps over
                        body-return.stem. The crossing is not a connection.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">outer-resume arcs / advance</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">explicit composition</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Two segments.arc using the canvas radius (default
                        2.75rem) turn the finalization exit
                        into a horizontal flow-step, then downward into the outer return. Its label-gap of 16rem
                        sets the horizontal separation; both arc endpoint calculations are visible in the example.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">counter-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">1 (flow-while)</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">First DEV counter of this WHILE. The open loop uses six
                        numbers, a closed loop ten. Steps, manual arcs and independent returns specify their following
                        counter values explicitly in this example.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">finalize.attach-to</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">The inner FALSE exit starts finalization even for zero
                        inner iterations. Its before-length and after-length are explicitly 4rem. The outer index
                        advance follows finalize.anchorNode-end; the inner return bypasses finalization.
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
        <x-translation-workbench::ui.tw-graph.language-examples
            source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-test"
            example="while-action-sequence"
        />
    </flux:callout>
    <flux:callout
        class="min-w-0"
        color="zinc"
        icon="eye"
    >
        <flux:callout.heading>Action → Nested WHILE → Action</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <flux:heading
                class="mt-4"
                size="sm"
            >side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- while-action-sequence-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-while-basic"
                    :dev="true"
                    :coordinates="true"
                    horizontalPadding="6rem"
                    min-height="60rem"
                    min-width="88rem"
                >
                    {{-- Initialization runs once. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.1.initialize"
                        :anchor-start="['x' => '0rem', 'y' => '2rem']"
                        :step-label="['text' => ['Load groups', 'groupIndex = 0'], 'width' => 'default']"
                        afterLength="5rem"
                        color="zinc"
                        :node-end="false"
                    />
                    {{-- Each outer iteration selects a group and resets its inner index. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-while
                        id="literature.while.1.loop"
                        :counter-start="1"
                        attach-to="literature.while.1.initialize.anchorNode-end"
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
                                ['over' => 'literature.while.1.body-return.stem', 'radius' => '0.65rem', 'side' =>
                                    'top'
                                ],
                            ],
                            'text' => ['Prepare group', 'itemIndex = 0'],
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
                        :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red']"
                    />
                    {{-- Turn the downward body exit into the upward inner condition. --}}
                    @php
                        $leftOuterBodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            'idea-to-paper-while-basic',
                            'literature.while.1.loop.body.anchorNode-end',
                        );
                        $leftInnerStart = [
                            'x' => 'calc(' . $leftOuterBodyEnd['x'] . ' - 12rem)',
                            'y' => 'calc(' . $leftOuterBodyEnd['y'] . ' - 8rem)',
                        ];
                    @endphp
                    <x-translation-workbench::ui.tw-graph.paths.loop-return
                        id="literature.while.1.inner-entry"
                        :counter-start="7"
                        attach-to="literature.while.1.loop.body.anchorNode-end"
                        :anchor-return="$leftInnerStart"
                        side="right"
                        color="green"
                    />
                    {{-- The inner return skips initialization, so itemIndex is not reset on every item. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-while
                        id="literature.while.1.inner-loop"
                        :counter-start="11"
                        :anchor-start="$leftInnerStart"
                        side="left"
                        color="sky"
                        true-bridge-length="4rem"
                        stem-length="4rem"
                        :condition-label="[
                            'beforeLength' => '2rem',
                            'labelGap' => '4rem',
                            'afterLength' => '8rem',
                            'text' => ['WHILE itemIndex < itemCount?'],
                            'width' => 'default',
                            'align' => 'center',
                        ]"
                        :action-label="[
                            'beforeLength' => '2rem',
                            'afterLength' => '2rem',
                            'text' => ['Process current item'],
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
                            'text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red',
                        ]"
                    />
                    {{-- Advance only the inner index, then recheck the inner condition. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.1.inner-advance"
                        :counter-end="17"
                        attach-to="literature.while.1.inner-loop.body.anchorNode-end"
                        direction="top-bottom"
                        before-length="2rem"
                        label-gap="4rem"
                        after-length="2rem"
                        :step-label="['text' => ['itemIndex = itemIndex + 1'], 'width' => 'default']"
                        :node-end-dot="false"
                        :joint-arrow-end="true"
                        color="sky"
                    />
                    <x-translation-workbench::ui.tw-graph.paths.loop-return
                        id="literature.while.1.inner-return"
                        :counter-start="18"
                        attach-to="literature.while.1.inner-advance.anchorNode-end"
                        return-to="literature.while.1.inner-loop.anchorNode-return"
                        side="left"
                        color="sky"
                    />
                    {{-- Inner FALSE always finalizes this group, including when it had no items. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.1.finalize"
                        attach-to="literature.while.1.inner-loop.anchorNode-end"
                        before-length="4rem"
                        label-gap="4rem"
                        after-length="4rem"
                        :step-label="['text' => ['Finalize group'], 'width' => 'default']"
                        :counter-end="22"
                        :node-end-dot="false"
                        :joint-arrow-end="true"
                        color="amber"
                    />
                    {{-- Turn the finalized group toward the separate outer return lane. --}}
                    @php
                        $leftInnerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            'idea-to-paper-while-basic',
                            'literature.while.1.finalize.anchorNode-end',
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
                        'id' => 'literature.while.1.outer-resume.arc-in',
                        'devCounterEnd' => 23,
                        'anchorStart' => $leftInnerExit,
                        'anchorEnd' => $leftAdvanceStart,
                        'startAnchor' => 'w',
                        'endAnchor' => 'n',
                        'arcRadius' => $leftResumeArcRadius,
                        'color' => 'cyan',
                        'dev' => true,
                        'nodeEnd' => true,
                        'nodeEndDot' => false,
                        'jointArrowEnd' => true,
                        'jointArrowEndDirection' => 'right',
                    ]" />
                    {{-- Advance the outer index only after finalization. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.1.advance"
                        :counter-end="24"
                        :anchor-start="$leftAdvanceStart"
                        direction="left-right"
                        before-length="2rem"
                        label-gap="16rem"
                        after-length="2rem"
                        :step-label="['text' => ['groupIndex = groupIndex + 1'], 'width' => 'default']"
                        :node-end-dot="false"
                        :joint-arrow-end="true"
                        color="cyan"
                    />
                    @php
                        $leftAdvanceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            'idea-to-paper-while-basic',
                            'literature.while.1.advance.anchorNode-end',
                        );
                        $leftOuterReturnStart = [
                            'x' => 'calc(' . $leftAdvanceEnd['x'] . ' + ' . $leftResumeArcRadius . ')',
                            'y' => 'calc(' . $leftAdvanceEnd['y'] . ' - ' . $leftResumeArcRadius . ')',
                        ];
                    @endphp
                    <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                        'id' => 'literature.while.1.outer-resume.arc-out',
                        'devCounterEnd' => 25,
                        'anchorStart' => $leftAdvanceEnd,
                        'anchorEnd' => $leftOuterReturnStart,
                        'startAnchor' => 'n',
                        'endAnchor' => 'e',
                        'arcRadius' => $leftResumeArcRadius,
                        'color' => 'cyan',
                        'dev' => true,
                        'nodeEnd' => true,
                        'nodeEndDot' => false,
                        'jointArrowEnd' => true,
                        'jointArrowEndDirection' => 'bottom',
                    ]" />
                    {{-- Return only after advancing, to the same condition (not initialization). --}}
                    <x-translation-workbench::ui.tw-graph.paths.loop-return
                        id="literature.while.1.body-return"
                        :counter-start="26"
                        :anchor-start="$leftOuterReturnStart"
                        return-to="literature.while.1.loop.anchorNode-return"
                        side="left"
                        color="cyan"
                    />
                    {{-- FALSE continues here, including when the group list starts empty. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.1.continue"
                        :counter-end="30"
                        attach-to="literature.while.1.loop.anchorNode-end"
                        beforeLength="4rem"
                        :step-label="['text' => ['Show summary'], 'width' => 'default']"
                        color="violet"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- while-action-sequence-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="mt-3 flex justify-end font-mono text-xs text-zinc-400">
            .../flow/while/flow-while-test.blade.php
        </flux:field>
    </flux:callout>
</section>
