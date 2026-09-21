<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('2 nested CASEs in SWITCH (3)') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.documentation-links example="flow.switch-case.flow-switch-case-two-nested-3" />
        <flux:callout.text>
            {{ __('Mixed sides: outer left / inner right and outer right / inner left. Grouped CASE entries open an independent format SWITCH. Its wider action lanes cross the outer published selection stem. Explicit line-jumps on the three inner action bridges and the return bridge mark crossings without connections.') }}
        </flux:callout.text>
        @php
            $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-two-nested-3',
            );
            $docExample1Code = $docExampleSource->example('flow-switch-case-two-nested-3-example-1');
            $docExample2Code = $docExampleSource->example('flow-switch-case-two-nested-3-example-2');
        @endphp
        <flux:heading
            class="mt-3"
            size="sm"
        >Outer side="left" · Inner side="right"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $docExample1Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">Outer side="right" · Inner side="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $docExample2Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Props and connections') }}</flux:heading>
        <flux:table
            class="mt-3"
            container:class="max-h-80"
        >
            <flux:table.columns sticky>
                <flux:table.column>{{ __('Prop / anchor') }}</flux:table.column>
                <flux:table.column>{{ __('Default') }}</flux:table.column>
                <flux:table.column>{{ __('Purpose') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">id</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">graph-id.switch</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Root ID for all generated switch elements.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">attach-to</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Attach the expression step to a preceding component
                        output.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">side</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">left</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">left or right: direction of the case actions.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">direction</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">bottom-top</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">bottom-top or top-bottom: flow direction.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">case-expression</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">SWITCH expression; halfLong</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">A single expression step, before case selection.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">[]; at least one required</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Ordered cases with unique key, label and actionLabel. The
                        key default is reserved.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases[].entries</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Optional CASE entries with unique key, label and color.
                        Each entry has its own lane;
                        all entries merge before the group’s single actionLabel. Entry color defaults to the action
                        color.
                        The group’s stemLength positions the first input; entryStemLength sets all subsequent spacings.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases[].actionLabel.return</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">true</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">false leaves the case action output open for a nested
                        block. Connect that block back explicitly; no direct return stem is drawn.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">.case.&lt;key&gt;.anchorNode-return</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">next route output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Join the outer output rail here after the nested block.
                        The next CASE stemLength reserves the vertical space.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases[].entryStemLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">max(3rem, twice arc-radius)</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Uniform spacing between the group’s inputs; explicit
                        values must be at least 3rem. Does not
                        change the first entry position.
                        Odd input counts have a straight middle lane; even counts place the output between the middle
                        inputs.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases[].label.align<br>cases[].entries[].label.align
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">center</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Text alignment: left, center or right. Supply label as an
                        array
                        with text and align. Other label options, including width and connectorLength, are preserved.
                        A plain string remains valid.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">case-default</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Default action; halfLong</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Action used when no case matches.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases[].exitLabel</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">BREAK; half</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Exit annotation after the case action (once per group).
                        Supports text, align, width, color and connector options. Alignment defaults to center. Use
                        false to hide the annotation; routing stays unchanged.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">case-default.label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">DEFAULT; halfLong</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Entry annotation for the fallback; independent of its
                        action text. Supports text, align, width, color and connector options. Alignment defaults to
                        center. Use false to hide the annotation; routing stays unchanged.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">case-default.exitLabel</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">END SWITCH; half</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Annotation at the common output. Supports text, align,
                        width, color and connector options. Alignment defaults to center. Use false to hide the
                        annotation; routing stays unchanged.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">stem-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">10rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Default length of each entry stem, including the first
                        case and DEFAULT.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">arc-radius</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">2.75rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Radius of the two arcs in each case route.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">bridge-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Minimum bridge length; shorter action labels are
                        compensated so outputs align.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">inherited / zinc</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Expression, selection and shared return color.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases[].actionLabel.color<br>case-default.color
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">component color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Explicit color for each action route.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">common output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">All case exits merge here before the independent
                        continuation.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">case-expression.stemLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">stem-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Entry stem to the first case. This example explicitly
                        uses 3rem.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases[].stemLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">stem-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Overrides the entry stem of that case. On the first case
                        it takes precedence over case-expression.stemLength.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">case-default.stemLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">stem-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Overrides the entry stem of DEFAULT. These overrides do
                        not change the stems inside the expression step.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases[].actionLabel.bridgeOutLength<br>case-default.bridgeOutLength</flux:table.cell>
                    <flux:table.cell>null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">16rem here: keeps the crossing in the outgoing bridge, beyond the action text.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases[].actionLabel.lineJumps<br>case-default.lineJumps<br>parts.sideways :line-jumps</flux:table.cell>
                    <flux:table.cell>[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Explicit crossings: over identifies the crossed line, radius sets the jump size and side=top places it above the bridge. Missing or invalid crossings produce DEV mismatch diagnostics.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
        <x-translation-workbench::ui.tw-graph.language-examples
            source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-two-nested-3"
            example="switch-grouped-nested"
        >
            <flux:text class="mt-2 text-sm">
                {{ __('Draft, review and revision share Prepare editing and one inner switch on format. The inner break exits only the inner switch; the following outer break exits the outer switch.') }}
            </flux:text>
            <flux:text class="mt-2 text-sm">
                {{ __('Action functions are supplied by the application. These are syntax excerpts; enclosing functions, classes and imports are omitted. C and C++ use enums instead of string cases. Left and right layouts represent the same logic unless different entry counts are shown.') }}
            </flux:text>
        </x-translation-workbench::ui.tw-graph.language-examples>

    </flux:callout>
    <flux:callout
        class="min-w-0"
        color="zinc"
        icon="eye"
    >
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <flux:text class="m-3">
                {{ __('Outer bridge-length=4rem and inner bridge-length=12rem create the crossings. Each inner action uses bridgeOutLength=16rem so the crossing lies on the outgoing bridge, outside its text. Four explicit lineJumps target outer.case.published.entry. CASE published reserves 32rem for the inner SWITCH and return. All values are editable in the example source.') }}
            </flux:text>
            <flux:heading
                class="m-3"
                size="sm"
            >Outer side="left" · Inner side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-switch-case-two-nested-3-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-switch-case-two-nested-3"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="86rem"
                    min-height="80rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.switch.1.two-nested-3.start"
                        :start-label="['text' => ['Article request'], 'width' => 'default']"
                    />

                    {{-- Outer SWITCH: three CASE entries fuse before the shared action and inner SWITCH. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.two-nested-3.outer"
                        attach-to="literature.switch.1.two-nested-3.start.anchorNode-end"
                        side="left"
                        bridge-length="4rem"
                        stem-length="3rem"
                        color="cyan"
                        :case-expression="[
                            'text' => ['SWITCH ($status)'],
                            'width' => 'default',
                            'stemLength' => '3rem',
                        ]"
                        :cases="[
                            [
                                'key' => 'editable',
                                'entryStemLength' => '4rem',
                                'entries' => [
                                    [
                                        'key' => 'draft',
                                        'label' => ['text' => ['CASE draft'], 'width' => 'default', 'align' => 'left'],
                                        'color' => 'amber',
                                    ],
                                    [
                                        'key' => 'review',
                                        'label' => ['text' => ['CASE review'], 'width' => 'default', 'align' => 'left'],
                                        'color' => 'orange',
                                    ],
                                    [
                                        'key' => 'revision',
                                        'label' => [
                                            'text' => ['CASE revision'],
                                            'width' => 'default',
                                            'align' => 'left',
                                        ],
                                        'color' => 'violet',
                                    ],
                                ],
                                'actionLabel' => [
                                    'text' => ['Prepare editing'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                    'return' => false,
                                ],
                                'exitLabel' => ['text' => ['Nested SWITCH'], 'width' => 'default', 'align' => 'right'],
                            ],
                            [
                                'key' => 'published',
                                'stemLength' => '32rem',
                                'label' => ['text' => ['CASE published'], 'width' => 'default', 'align' => 'left'],
                                'actionLabel' => [
                                    'text' => ['Display article'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ],
                                'exitLabel' => ['text' => ['BREAK'], 'align' => 'right'],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Show status hint'],
                            'width' => 'default',
                            'color' => 'zinc',
                            'label' => ['text' => ['DEFAULT'], 'width' => 'default', 'align' => 'left'],
                            'exitLabel' => ['text' => ['END outer SWITCH'], 'width' => 'default', 'align' => 'right'],
                        ]"
                    />

                    {{-- Inner SWITCH: choose an editor from the article format. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.two-nested-3.inner"
                        attach-to="literature.switch.1.two-nested-3.outer.case.editable.anchorNode-end"
                        side="right"
                        bridge-length="12rem"
                        stem-length="2rem"
                        color="amber"
                        :case-expression="[
                            'text' => ['SWITCH ($format)'],
                            'width' => 'default',
                            'stemLength' => '3rem',
                        ]"
                        :cases="[
                            [
                                'key' => 'text',
                                'label' => ['text' => ['CASE text'], 'width' => 'half', 'align' => 'right'],
                                'actionLabel' => [
                                    'text' => ['Open text editor'],
                                    'bridgeOutLength' => '16rem',
                                    'lineJumps' => [
                                        ['over' => 'literature.switch.1.two-nested-3.outer.case.published.entry', 'radius' => '0.65rem', 'side' => 'top'],
                                    ],
                                    'width' => 'default',
                                    'color' => 'violet',
                                ],
                            ],
                            [
                                'key' => 'image',
                                'label' => ['text' => ['CASE image'], 'width' => 'half', 'align' => 'right'],
                                'actionLabel' => [
                                    'text' => ['Open image editor'],
                                    'bridgeOutLength' => '16rem',
                                    'lineJumps' => [
                                        ['over' => 'literature.switch.1.two-nested-3.outer.case.published.entry', 'radius' => '0.65rem', 'side' => 'top'],
                                    ],
                                    'width' => 'default',
                                    'color' => 'sky',
                                ],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Open plain editor'],
                            'bridgeOutLength' => '16rem',
                                    'lineJumps' => [
                                        ['over' => 'literature.switch.1.two-nested-3.outer.case.published.entry', 'radius' => '0.65rem', 'side' => 'top'],
                                    ],
                            'width' => 'default',
                            'color' => 'orange',
                            'label' => ['text' => ['DEFAULT'], 'width' => 'half', 'align' => 'right'],
                            'exitLabel' => ['text' => ['END inner SWITCH'], 'width' => 'default', 'align' => 'left'],
                        ]"
                    />

                    {{-- Return above the inner SWITCH to the outer CASE output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-flow-switch-case-two-nested-3';
                        $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-3.inner.anchorNode-end',
                        );
                        $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-3.outer.case.editable.anchorNode-return',
                        );
                        $returnBridge = 'calc(' . $innerEnd['x'] . ' - ' . $outerReturn['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.switch.1.two-nested-3.inner-return"
                        :anchor-start="$innerEnd"
                        side="right"
                        arc-radius="2.75rem"
                        :bridge-length="$returnBridge"
                        :line-jumps="[
                            ['over' => 'literature.switch.1.two-nested-3.outer.case.published.entry', 'radius' => '0.65rem', 'side' => 'top'],
                        ]"
                        color="amber"
                        :joint-arrow-end="true"
                        dev-counter-end="R"
                    />
                    @php
                        $returnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-3.inner-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.switch.1.two-nested-3.inner-return.stem"
                        :anchor-start="$returnEnd"
                        return-to="literature.switch.1.two-nested-3.outer.case.editable.anchorNode-return"
                        :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :dev-counter-end="false"
                        color="amber"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.two-nested-3.continue"
                        attach-to="literature.switch.1.two-nested-3.outer.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'default']"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-switch-case-two-nested-3-example-1:end --}}
            </div>
            <flux:heading
                class="m-3"
                size="sm"
            >Outer side="right" · Inner side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-switch-case-two-nested-3-example-2:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-switch-case-two-nested-3-right"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="86rem"
                    min-height="80rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.switch.1.two-nested-3-right.start"
                        :start-label="['text' => ['Article request'], 'width' => 'default']"
                    />

                    {{-- Outer SWITCH: three CASE entries fuse before the shared action and inner SWITCH. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.two-nested-3-right.outer"
                        attach-to="literature.switch.1.two-nested-3-right.start.anchorNode-end"
                        side="right"
                        bridge-length="4rem"
                        stem-length="3rem"
                        color="cyan"
                        :case-expression="[
                            'text' => ['SWITCH ($status)'],
                            'width' => 'default',
                            'stemLength' => '3rem',
                        ]"
                        :cases="[
                            [
                                'key' => 'editable',
                                'entryStemLength' => '4rem',
                                'entries' => [
                                    [
                                        'key' => 'draft',
                                        'label' => ['text' => ['CASE draft'], 'width' => 'default', 'align' => 'right'],
                                        'color' => 'amber',
                                    ],
                                    [
                                        'key' => 'review',
                                        'label' => ['text' => ['CASE review'], 'width' => 'default', 'align' => 'right'],
                                        'color' => 'orange',
                                    ],
                                    [
                                        'key' => 'revision',
                                        'label' => [
                                            'text' => ['CASE revision'],
                                            'width' => 'default',
                                            'align' => 'right',
                                        ],
                                        'color' => 'violet',
                                    ],
                                ],
                                'actionLabel' => [
                                    'text' => ['Prepare editing'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                    'return' => false,
                                ],
                                'exitLabel' => ['text' => ['Nested SWITCH'], 'width' => 'default', 'align' => 'left'],
                            ],
                            [
                                'key' => 'published',
                                'stemLength' => '32rem',
                                'label' => ['text' => ['CASE published'], 'width' => 'default', 'align' => 'right'],
                                'actionLabel' => [
                                    'text' => ['Display article'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ],
                                'exitLabel' => ['text' => ['BREAK'], 'align' => 'left'],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Show status hint'],
                            'width' => 'default',
                            'color' => 'zinc',
                            'label' => ['text' => ['DEFAULT'], 'width' => 'default', 'align' => 'right'],
                            'exitLabel' => ['text' => ['END outer SWITCH'], 'width' => 'default', 'align' => 'left'],
                        ]"
                    />

                    {{-- Inner SWITCH: choose an editor from the article format. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.two-nested-3-right.inner"
                        attach-to="literature.switch.1.two-nested-3-right.outer.case.editable.anchorNode-end"
                        side="left"
                        bridge-length="12rem"
                        stem-length="2rem"
                        color="amber"
                        :case-expression="[
                            'text' => ['SWITCH ($format)'],
                            'width' => 'default',
                            'stemLength' => '3rem',
                        ]"
                        :cases="[
                            [
                                'key' => 'text',
                                'label' => ['text' => ['CASE text'], 'width' => 'half', 'align' => 'left'],
                                'actionLabel' => [
                                    'text' => ['Open text editor'],
                                    'bridgeOutLength' => '16rem',
                                    'lineJumps' => [
                                        ['over' => 'literature.switch.1.two-nested-3-right.outer.case.published.entry', 'radius' => '0.65rem', 'side' => 'top'],
                                    ],
                                    'width' => 'default',
                                    'color' => 'violet',
                                ],
                            ],
                            [
                                'key' => 'image',
                                'label' => ['text' => ['CASE image'], 'width' => 'half', 'align' => 'left'],
                                'actionLabel' => [
                                    'text' => ['Open image editor'],
                                    'bridgeOutLength' => '16rem',
                                    'lineJumps' => [
                                        ['over' => 'literature.switch.1.two-nested-3-right.outer.case.published.entry', 'radius' => '0.65rem', 'side' => 'top'],
                                    ],
                                    'width' => 'default',
                                    'color' => 'sky',
                                ],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Open plain editor'],
                            'bridgeOutLength' => '16rem',
                                    'lineJumps' => [
                                        ['over' => 'literature.switch.1.two-nested-3-right.outer.case.published.entry', 'radius' => '0.65rem', 'side' => 'top'],
                                    ],
                            'width' => 'default',
                            'color' => 'orange',
                            'label' => ['text' => ['DEFAULT'], 'width' => 'half', 'align' => 'left'],
                            'exitLabel' => ['text' => ['END inner SWITCH'], 'width' => 'default', 'align' => 'right'],
                        ]"
                    />

                    {{-- Return above the inner SWITCH to the outer CASE output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-flow-switch-case-two-nested-3-right';
                        $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-3-right.inner.anchorNode-end',
                        );
                        $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-3-right.outer.case.editable.anchorNode-return',
                        );
                        $returnBridge = 'calc(' . $outerReturn['x'] . ' - ' . $innerEnd['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.switch.1.two-nested-3-right.inner-return"
                        :anchor-start="$innerEnd"
                        side="left"
                        arc-radius="2.75rem"
                        :bridge-length="$returnBridge"
                        :line-jumps="[
                            ['over' => 'literature.switch.1.two-nested-3-right.outer.case.published.entry', 'radius' => '0.65rem', 'side' => 'top'],
                        ]"
                        color="amber"
                        :joint-arrow-end="true"
                        dev-counter-end="R"
                    />
                    @php
                        $returnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-3-right.inner-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.switch.1.two-nested-3-right.inner-return.stem"
                        :anchor-start="$returnEnd"
                        return-to="literature.switch.1.two-nested-3-right.outer.case.editable.anchorNode-return"
                        :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :dev-counter-end="false"
                        color="amber"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.two-nested-3-right.continue"
                        attach-to="literature.switch.1.two-nested-3-right.outer.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'default']"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-switch-case-two-nested-3-example-2:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../flow/switch-case/flow-switch-case-two-nested-3.blade.php
        </flux:field>
    </flux:callout>
</section>
