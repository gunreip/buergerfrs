<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>Mixed sides and crossings</flux:callout.heading>
        <flux:callout.text>Initialize the group index once. Each outer iteration selects a group and resets its item
            index. The inner WHILE processes that group's items and advances only the item index. Its FALSE exit
            advances the group index, then returns to the outer condition. An empty group skips the inner body;
            an empty group list skips both bodies. The inner loop occupies the opposite side. The outer action bridge
            crosses the inner condition stem and increment stem; explicit line-jumps distinguish these crossings from
            connections.
        </flux:callout.text>
        @php
            $whileSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-mixed',
            );
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >outer="left" · inner="right"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $whileSource->example('while-mixed-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >outer="right" · inner="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $whileSource->example('while-mixed-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
        <x-translation-workbench::ui.tw-graph.documentation-links example="flow.while.flow-while-mixed" />
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
                    <flux:table.cell class="whitespace-normal">Bridge after the body action: 40rem outside reserves the
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
                    <flux:table.cell class="whitespace-normal">Outer body: inner-entry turns toward the inner WHILE on
                        the opposite side.
                        Inner body: the downward item increment uses before-length="6rem" to clear the outer bridge.
                    </flux:table.cell>
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
                        inner-loop.condition.stem.after and inner-advance.stem.before. Neither crossing is a connection.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">outer-resume arcs / advance</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">explicit composition</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Two segments.arc using the canvas radius (default
                        2.75rem) turn inner FALSE
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
            </flux:table.rows>
        </flux:table>
        <x-translation-workbench::ui.tw-graph.language-examples
            source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-mixed"
            example="while-nested"
        />
    </flux:callout>
    <flux:callout
        class="min-w-0"
        color="zinc"
        icon="eye"
    >
        <flux:callout.heading>Mixed sides · outer / inner</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <flux:heading
                class="mt-4"
                size="sm"
            >outer="left" · inner="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- while-mixed-left-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-while-mixed-left"
                    :dev="true"
                    :coordinates="true"
                    horizontalPadding="6rem"
                    min-height="46rem"
                    min-width="88rem"
                >
                    {{-- Initialization runs once. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.mixed.left.initialize"
                        :anchor-start="['x' => '0rem', 'y' => '2rem']"
                        :step-label="['text' => ['Load groups', 'groupIndex = 0'], 'width' => 'default']"
                        afterLength="5rem"
                        color="zinc"
                        :node-end="false"
                    />
                    {{-- Each outer iteration selects a group and resets its inner index. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-while
                        id="literature.while.mixed.left.loop"
                        :counter-start="1"
                        attach-to="literature.while.mixed.left.initialize.anchorNode-end"
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
                            'afterLength' => '40rem',
                            'lineJumps' => [
                                ['over' => 'literature.while.mixed.left.inner-loop.condition.stem.after', 'radius' => '0.65rem',
                                    'side' =>
                                    'top'
                                ],
                                ['over' => 'literature.while.mixed.left.inner-advance.stem.before', 'radius' => '0.65rem',
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
                        :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red']"
                    />
                    {{-- Turn the downward body exit into the upward inner condition. --}}
                    @php
                        $leftOuterBodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            'idea-to-paper-while-mixed-left',
                            'literature.while.mixed.left.loop.body.anchorNode-end',
                        );
                        $leftInnerStart = [
                            'x' => 'calc(' . $leftOuterBodyEnd['x'] . ' + 12rem)',
                            'y' => 'calc(' . $leftOuterBodyEnd['y'] . ' - 8rem)',
                        ];
                    @endphp
                    <x-translation-workbench::ui.tw-graph.paths.loop-return
                        id="literature.while.mixed.left.inner-entry"
                        :counter-start="7"
                        attach-to="literature.while.mixed.left.loop.body.anchorNode-end"
                        :anchor-return="$leftInnerStart"
                        side="left"
                        color="green"
                    />
                    {{-- The inner return skips initialization, so itemIndex is not reset on every item. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-while
                        id="literature.while.mixed.left.inner-loop"
                        :counter-start="11"
                        :anchor-start="$leftInnerStart"
                        side="right"
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
                            'anchor' => 'condition', 'side' => 'right', 'color' => 'green',
                        ]"
                        :false-label="[
                            'text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red',
                        ]"
                    />
                    {{-- Advance only the inner index, then recheck the inner condition. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.mixed.left.inner-advance"
                        :counter-end="17"
                        attach-to="literature.while.mixed.left.inner-loop.body.anchorNode-end"
                        direction="top-bottom"
                        before-length="6rem"
                        label-gap="4rem"
                        after-length="2rem"
                        :step-label="['text' => ['itemIndex = itemIndex + 1'], 'width' => 'default']"
                        :node-end-dot="false"
                        :joint-arrow-end="true"
                        color="sky"
                    />
                    <x-translation-workbench::ui.tw-graph.paths.loop-return
                        id="literature.while.mixed.left.inner-return"
                        :counter-start="18"
                        attach-to="literature.while.mixed.left.inner-advance.anchorNode-end"
                        return-to="literature.while.mixed.left.inner-loop.anchorNode-return"
                        side="right"
                        color="sky"
                    />
                    {{-- Turn inner FALSE toward the separate outer return lane. --}}
                    @php
                        $leftInnerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            'idea-to-paper-while-mixed-left',
                            'literature.while.mixed.left.inner-loop.anchorNode-end',
                        );
                        $leftResumeArcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString(
                            'arc_radius',
                            '2.75rem',
                        );
                        $leftAdvanceStart = [
                            'x' => 'calc(' . $leftInnerExit['x'] . ' - ' . $leftResumeArcRadius . ')',
                            'y' => 'calc(' . $leftInnerExit['y'] . ' + ' . $leftResumeArcRadius . ')',
                        ];
                    @endphp
                    <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                        'id' => 'literature.while.mixed.left.outer-resume.arc-in',
                        'devCounterEnd' => 22,
                        'anchorStart' => $leftInnerExit,
                        'anchorEnd' => $leftAdvanceStart,
                        'startAnchor' => 'e',
                        'endAnchor' => 'n',
                        'arcRadius' => $leftResumeArcRadius,
                        'color' => 'red',
                        'dev' => true,
                        'nodeEnd' => true,
                        'nodeEndDot' => false,
                        'jointArrowEnd' => true,
                        'jointArrowEndDirection' => 'left',
                    ]" />
                    {{-- Only inner FALSE advances the group index, including for an empty group. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.mixed.left.advance"
                        :counter-end="23"
                        :anchor-start="$leftAdvanceStart"
                        direction="right-left"
                        before-length="2rem"
                        label-gap="16rem"
                        after-length="2rem"
                        :step-label="['text' => ['groupIndex = groupIndex + 1'], 'width' => 'default']"
                        :node-end-dot="false"
                        :joint-arrow-end="true"
                        color="red"
                    />
                    @php
                        $leftAdvanceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            'idea-to-paper-while-mixed-left',
                            'literature.while.mixed.left.advance.anchorNode-end',
                        );
                        $leftOuterReturnStart = [
                            'x' => 'calc(' . $leftAdvanceEnd['x'] . ' - ' . $leftResumeArcRadius . ')',
                            'y' => 'calc(' . $leftAdvanceEnd['y'] . ' - ' . $leftResumeArcRadius . ')',
                        ];
                    @endphp
                    <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                        'id' => 'literature.while.mixed.left.outer-resume.arc-out',
                        'devCounterEnd' => 24,
                        'anchorStart' => $leftAdvanceEnd,
                        'anchorEnd' => $leftOuterReturnStart,
                        'startAnchor' => 'n',
                        'endAnchor' => 'w',
                        'arcRadius' => $leftResumeArcRadius,
                        'color' => 'red',
                        'dev' => true,
                        'nodeEnd' => true,
                        'nodeEndDot' => false,
                        'jointArrowEnd' => true,
                        'jointArrowEndDirection' => 'bottom',
                    ]" />
                    {{-- Return only after advancing, to the same condition (not initialization). --}}
                    <x-translation-workbench::ui.tw-graph.paths.loop-return
                        id="literature.while.mixed.left.body-return"
                        :counter-start="25"
                        :anchor-start="$leftOuterReturnStart"
                        return-to="literature.while.mixed.left.loop.anchorNode-return"
                        side="left"
                        color="red"
                    />
                    {{-- FALSE continues here, including when the group list starts empty. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.mixed.left.continue"
                        :counter-end="29"
                        attach-to="literature.while.mixed.left.loop.anchorNode-end"
                        beforeLength="4rem"
                        :step-label="['text' => ['Show summary'], 'width' => 'default']"
                        color="violet"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- while-mixed-left-example:end --}}
            </div>
            <flux:heading
                class="mt-4"
                size="sm"
            >outer="right" · inner="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- while-mixed-right-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-while-mixed-right"
                    :dev="true"
                    :coordinates="true"
                    horizontalPadding="6rem"
                    min-height="46rem"
                    min-width="88rem"
                >
                    {{-- Initialization runs once. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.mixed.right.initialize"
                        :anchor-start="['x' => '0rem', 'y' => '2rem']"
                        :step-label="['text' => ['Load groups', 'groupIndex = 0'], 'width' => 'default']"
                        afterLength="5rem"
                        color="zinc"
                        :node-end="false"
                    />
                    {{-- Each outer iteration selects a group and resets its inner index. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-while
                        id="literature.while.mixed.right.loop"
                        :counter-start="1"
                        attach-to="literature.while.mixed.right.initialize.anchorNode-end"
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
                            'afterLength' => '40rem',
                            'lineJumps' => [
                                ['over' => 'literature.while.mixed.right.inner-loop.condition.stem.after', 'radius' =>
                                    '0.65rem', 'side' =>
                                    'top'
                                ],
                                ['over' => 'literature.while.mixed.right.inner-advance.stem.before', 'radius' =>
                                    '0.65rem', 'side' => 'top'
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
                        :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red']"
                    />
                    {{-- Turn the downward body exit into the upward inner condition. --}}
                    @php
                        $rightOuterBodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            'idea-to-paper-while-mixed-right',
                            'literature.while.mixed.right.loop.body.anchorNode-end',
                        );
                        $rightInnerStart = [
                            'x' => 'calc(' . $rightOuterBodyEnd['x'] . ' - 12rem)',
                            'y' => 'calc(' . $rightOuterBodyEnd['y'] . ' - 8rem)',
                        ];
                    @endphp
                    <x-translation-workbench::ui.tw-graph.paths.loop-return
                        id="literature.while.mixed.right.inner-entry"
                        :counter-start="7"
                        attach-to="literature.while.mixed.right.loop.body.anchorNode-end"
                        :anchor-return="$rightInnerStart"
                        side="right"
                        color="green"
                    />
                    {{-- The inner return skips initialization, so itemIndex is not reset on every item. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-while
                        id="literature.while.mixed.right.inner-loop"
                        :counter-start="11"
                        :anchor-start="$rightInnerStart"
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
                        id="literature.while.mixed.right.inner-advance"
                        :counter-end="17"
                        attach-to="literature.while.mixed.right.inner-loop.body.anchorNode-end"
                        direction="top-bottom"
                        before-length="6rem"
                        label-gap="4rem"
                        after-length="2rem"
                        :step-label="['text' => ['itemIndex = itemIndex + 1'], 'width' => 'default']"
                        :node-end-dot="false"
                        :joint-arrow-end="true"
                        color="sky"
                    />
                    <x-translation-workbench::ui.tw-graph.paths.loop-return
                        id="literature.while.mixed.right.inner-return"
                        :counter-start="18"
                        attach-to="literature.while.mixed.right.inner-advance.anchorNode-end"
                        return-to="literature.while.mixed.right.inner-loop.anchorNode-return"
                        side="left"
                        color="sky"
                    />
                    {{-- Turn inner FALSE toward the separate outer return lane. --}}
                    @php
                        $rightInnerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            'idea-to-paper-while-mixed-right',
                            'literature.while.mixed.right.inner-loop.anchorNode-end',
                        );
                        $rightResumeArcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString(
                            'arc_radius',
                            '2.75rem',
                        );
                        $rightAdvanceStart = [
                            'x' => 'calc(' . $rightInnerExit['x'] . ' + ' . $rightResumeArcRadius . ')',
                            'y' => 'calc(' . $rightInnerExit['y'] . ' + ' . $rightResumeArcRadius . ')',
                        ];
                    @endphp
                    <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                        'id' => 'literature.while.mixed.right.outer-resume.arc-in',
                        'devCounterEnd' => 22,
                        'anchorStart' => $rightInnerExit,
                        'anchorEnd' => $rightAdvanceStart,
                        'startAnchor' => 'w',
                        'endAnchor' => 'n',
                        'arcRadius' => $rightResumeArcRadius,
                        'color' => 'red',
                        'dev' => true,
                        'nodeEnd' => true,
                        'nodeEndDot' => false,
                        'jointArrowEnd' => true,
                        'jointArrowEndDirection' => 'right',
                    ]" />
                    {{-- Only inner FALSE advances the group index, including for an empty group. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.mixed.right.advance"
                        :counter-end="23"
                        :anchor-start="$rightAdvanceStart"
                        direction="left-right"
                        before-length="2rem"
                        label-gap="16rem"
                        after-length="2rem"
                        :step-label="['text' => ['groupIndex = groupIndex + 1'], 'width' => 'default']"
                        :node-end-dot="false"
                        :joint-arrow-end="true"
                        color="red"
                    />
                    @php
                        $rightAdvanceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            'idea-to-paper-while-mixed-right',
                            'literature.while.mixed.right.advance.anchorNode-end',
                        );
                        $rightOuterReturnStart = [
                            'x' => 'calc(' . $rightAdvanceEnd['x'] . ' + ' . $rightResumeArcRadius . ')',
                            'y' => 'calc(' . $rightAdvanceEnd['y'] . ' - ' . $rightResumeArcRadius . ')',
                        ];
                    @endphp
                    <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                        'id' => 'literature.while.mixed.right.outer-resume.arc-out',
                        'devCounterEnd' => 24,
                        'anchorStart' => $rightAdvanceEnd,
                        'anchorEnd' => $rightOuterReturnStart,
                        'startAnchor' => 'n',
                        'endAnchor' => 'e',
                        'arcRadius' => $rightResumeArcRadius,
                        'color' => 'red',
                        'dev' => true,
                        'nodeEnd' => true,
                        'nodeEndDot' => false,
                        'jointArrowEnd' => true,
                        'jointArrowEndDirection' => 'bottom',
                    ]" />
                    {{-- Return only after advancing, to the same condition (not initialization). --}}
                    <x-translation-workbench::ui.tw-graph.paths.loop-return
                        id="literature.while.mixed.right.body-return"
                        :counter-start="25"
                        :anchor-start="$rightOuterReturnStart"
                        return-to="literature.while.mixed.right.loop.anchorNode-return"
                        side="right"
                        color="red"
                    />
                    {{-- FALSE continues here, including when the group list starts empty. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.mixed.right.continue"
                        :counter-end="29"
                        attach-to="literature.while.mixed.right.loop.anchorNode-end"
                        beforeLength="4rem"
                        :step-label="['text' => ['Show summary'], 'width' => 'default']"
                        color="violet"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- while-mixed-right-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="mt-3 flex justify-end font-mono text-xs text-zinc-400">
            .../flow/while/flow-while-mixed.blade.php
        </flux:field>
    </flux:callout>
</section>
