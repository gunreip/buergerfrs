<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('2 nested CASEs in SWITCH (2)') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.documentation-links example="flow.switch-case.flow-switch-case-two-nested-2" />
        <flux:callout.text>
            {{ __('One SWITCH expression selects a CASE. CASE draft, review and revision merge into one Prepare editing action and share the nested SWITCH. The inner SWITCH opens to the left but stays to the right of the outer selection rail. Its expression selects an editor by format. Its break exits only the inner SWITCH; the explicit return then rejoins the outer output rail. CASE published enters its own independent SWITCH on channel after Prepare delivery. The first preview opens both inner SWITCHes to the left; the second mirrors only the published inner SWITCH to the right. Each inner SWITCH returns only to its own outer CASE; the outer DEFAULT skips both inner blocks.') }}
        </flux:callout.text>
        @php
            $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-two-nested-2',
            );
            $docExample1Code = $docExampleSource->example('flow-switch-case-two-nested-2-example-1');
            $docExample2Code = $docExampleSource->example('flow-switch-case-two-nested-2-example-2');
        @endphp
        <flux:heading
            class="mt-3"
            size="sm"
        >Outer right · Editable left · Published left</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $docExample1Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">Outer right · Editable left · Published right</flux:heading>
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
            </flux:table.rows>
        </flux:table>
        <x-translation-workbench::ui.tw-graph.language-examples
            source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-two-nested-2"
            example="switch-two-nested"
        >
            <flux:text class="mt-2 text-sm">
                {{ __('Draft, review and revision share the inner switch on format. Published uses a separate inner switch on channel. Each inner break exits only its own switch; the following outer break exits the outer switch.') }}
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
                {{ __('The outer SWITCH, both inner SWITCH components and their two returns are authored separately. actionLabel.return = false removes the direct outer return stem, so no route bypasses the nested block. The outer SWITCH explicitly uses bridge-length = 8rem to keep the left-facing inner SWITCH on the right of the outer selection rail. CASE published explicitly reserves 22rem and DEFAULT 29rem for the preceding inner SWITCH and its return.') }}
            </flux:text>
            <flux:heading
                class="m-3"
                size="sm"
            >Outer right · Editable left · Published left</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-switch-case-two-nested-2-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-switch-case-two-nested-2"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="86rem"
                    min-height="94rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.switch.1.two-nested-2.start"
                        :start-label="['text' => ['Article request'], 'width' => 'default']"
                    />

                    {{-- Outer SWITCH: three CASE entries fuse before the shared action and inner SWITCH. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.two-nested-2.outer"
                        attach-to="literature.switch.1.two-nested-2.start.anchorNode-end"
                        side="right"
                        bridge-length="8rem"
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
                                'stemLength' => '22rem',
                                'label' => ['text' => ['CASE published'], 'width' => 'default', 'align' => 'right'],
                                'actionLabel' => [
                                    'text' => ['Prepare delivery'],
                                    'width' => 'default',
                                    'color' => 'green',
                                    'return' => false,
                                ],
                                'exitLabel' => ['text' => ['Nested delivery'], 'width' => 'default', 'align' => 'left'],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Show status hint'],
                            'stemLength' => '29rem',
                            'width' => 'default',
                            'color' => 'zinc',
                            'label' => ['text' => ['DEFAULT'], 'width' => 'default', 'align' => 'right'],
                            'exitLabel' => ['text' => ['END outer SWITCH'], 'width' => 'default', 'align' => 'left'],
                        ]"
                    />

                    {{-- Inner SWITCH: choose an editor from the article format. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.two-nested-2.inner"
                        attach-to="literature.switch.1.two-nested-2.outer.case.editable.anchorNode-end"
                        side="left"
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
                                    'width' => 'default',
                                    'color' => 'violet',
                                ],
                            ],
                            [
                                'key' => 'image',
                                'label' => ['text' => ['CASE image'], 'width' => 'half', 'align' => 'left'],
                                'actionLabel' => [
                                    'text' => ['Open image editor'],
                                    'width' => 'default',
                                    'color' => 'sky',
                                ],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Open plain editor'],
                            'width' => 'default',
                            'color' => 'orange',
                            'label' => ['text' => ['DEFAULT'], 'width' => 'half', 'align' => 'left'],
                            'exitLabel' => ['text' => ['END inner SWITCH'], 'width' => 'default', 'align' => 'right'],
                        ]"
                    />

                    {{-- Return above the inner SWITCH to the outer CASE output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-flow-switch-case-two-nested-2';
                        $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-2.inner.anchorNode-end',
                        );
                        $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-2.outer.case.editable.anchorNode-return',
                        );
                        $returnBridge = 'calc(' . $outerReturn['x'] . ' - ' . $innerEnd['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.switch.1.two-nested-2.inner-return"
                        :anchor-start="$innerEnd"
                        side="left"
                        arc-radius="2.75rem"
                        :bridge-length="$returnBridge"
                        color="amber"
                        :joint-arrow-end="true"
                        dev-counter-end="R1"
                    />
                    @php
                        $returnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-2.inner-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.switch.1.two-nested-2.inner-return.stem"
                        :anchor-start="$returnEnd"
                        return-to="literature.switch.1.two-nested-2.outer.case.editable.anchorNode-return"
                        :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :dev-counter-end="false"
                        color="amber"
                    />
                    {{-- Inner SWITCH: choose a delivery action from the publication channel. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.two-nested-2.inner-published"
                        attach-to="literature.switch.1.two-nested-2.outer.case.published.anchorNode-end"
                        side="left"
                        stem-length="2rem"
                        color="green"
                        :case-expression="[
                            'text' => ['SWITCH ($channel)'],
                            'width' => 'default',
                            'stemLength' => '3rem',
                        ]"
                        :cases="[
                            [
                                'key' => 'web',
                                'label' => ['text' => ['CASE web'], 'width' => 'half', 'align' => 'left'],
                                'actionLabel' => [
                                    'text' => ['Publish online'],
                                    'width' => 'default',
                                    'color' => 'violet',
                                ],
                            ],
                            [
                                'key' => 'print',
                                'label' => ['text' => ['CASE print'], 'width' => 'half', 'align' => 'left'],
                                'actionLabel' => [
                                    'text' => ['Queue print job'],
                                    'width' => 'default',
                                    'color' => 'sky',
                                ],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Queue manual delivery'],
                            'width' => 'default',
                            'color' => 'orange',
                            'label' => ['text' => ['DEFAULT'], 'width' => 'half', 'align' => 'left'],
                            'exitLabel' => ['text' => ['END delivery SWITCH'], 'width' => 'default', 'align' => 'right'],
                        ]"
                    />

                    {{-- Return above the inner SWITCH to the outer CASE output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-flow-switch-case-two-nested-2';
                        $publishedInnerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-2.inner-published.anchorNode-end',
                        );
                        $publishedOuterReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-2.outer.case.published.anchorNode-return',
                        );
                        $publishedReturnBridge = 'calc(' . $publishedOuterReturn['x'] . ' - ' . $publishedInnerEnd['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.switch.1.two-nested-2.inner-published-return"
                        :anchor-start="$publishedInnerEnd"
                        side="left"
                        arc-radius="2.75rem"
                        :bridge-length="$publishedReturnBridge"
                        color="green"
                        :joint-arrow-end="true"
                        dev-counter-end="R2"
                    />
                    @php
                        $publishedReturnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-2.inner-published-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.switch.1.two-nested-2.inner-published-return.stem"
                        :anchor-start="$publishedReturnEnd"
                        return-to="literature.switch.1.two-nested-2.outer.case.published.anchorNode-return"
                        :length="'calc(' . $publishedOuterReturn['y'] . ' - ' . $publishedReturnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :dev-counter-end="false"
                        color="green"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.two-nested-2.continue"
                        attach-to="literature.switch.1.two-nested-2.outer.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'default']"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-switch-case-two-nested-2-example-1:end --}}
            </div>
            <flux:heading
                class="m-3"
                size="sm"
            >Outer right · Editable left · Published right</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-switch-case-two-nested-2-example-2:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-switch-case-two-nested-2-mixed"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="86rem"
                    min-height="94rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.switch.1.two-nested-2-mixed.start"
                        :start-label="['text' => ['Article request'], 'width' => 'default']"
                    />

                    {{-- Outer SWITCH: three CASE entries fuse before the shared action and inner SWITCH. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.two-nested-2-mixed.outer"
                        attach-to="literature.switch.1.two-nested-2-mixed.start.anchorNode-end"
                        side="right"
                        bridge-length="8rem"
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
                                'stemLength' => '22rem',
                                'label' => ['text' => ['CASE published'], 'width' => 'default', 'align' => 'right'],
                                'actionLabel' => [
                                    'text' => ['Prepare delivery'],
                                    'width' => 'default',
                                    'color' => 'green',
                                    'return' => false,
                                ],
                                'exitLabel' => ['text' => ['Nested delivery'], 'width' => 'default', 'align' => 'left'],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Show status hint'],
                            'stemLength' => '29rem',
                            'width' => 'default',
                            'color' => 'zinc',
                            'label' => ['text' => ['DEFAULT'], 'width' => 'default', 'align' => 'right'],
                            'exitLabel' => ['text' => ['END outer SWITCH'], 'width' => 'default', 'align' => 'left'],
                        ]"
                    />

                    {{-- Inner SWITCH: choose an editor from the article format. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.two-nested-2-mixed.inner"
                        attach-to="literature.switch.1.two-nested-2-mixed.outer.case.editable.anchorNode-end"
                        side="left"
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
                                    'width' => 'default',
                                    'color' => 'violet',
                                ],
                            ],
                            [
                                'key' => 'image',
                                'label' => ['text' => ['CASE image'], 'width' => 'half', 'align' => 'left'],
                                'actionLabel' => [
                                    'text' => ['Open image editor'],
                                    'width' => 'default',
                                    'color' => 'sky',
                                ],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Open plain editor'],
                            'width' => 'default',
                            'color' => 'orange',
                            'label' => ['text' => ['DEFAULT'], 'width' => 'half', 'align' => 'left'],
                            'exitLabel' => ['text' => ['END inner SWITCH'], 'width' => 'default', 'align' => 'right'],
                        ]"
                    />

                    {{-- Return above the inner SWITCH to the outer CASE output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-flow-switch-case-two-nested-2-mixed';
                        $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-2-mixed.inner.anchorNode-end',
                        );
                        $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-2-mixed.outer.case.editable.anchorNode-return',
                        );
                        $returnBridge = 'calc(' . $outerReturn['x'] . ' - ' . $innerEnd['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.switch.1.two-nested-2-mixed.inner-return"
                        :anchor-start="$innerEnd"
                        side="left"
                        arc-radius="2.75rem"
                        :bridge-length="$returnBridge"
                        color="amber"
                        :joint-arrow-end="true"
                        dev-counter-end="R1"
                    />
                    @php
                        $returnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-2-mixed.inner-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.switch.1.two-nested-2-mixed.inner-return.stem"
                        :anchor-start="$returnEnd"
                        return-to="literature.switch.1.two-nested-2-mixed.outer.case.editable.anchorNode-return"
                        :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :dev-counter-end="false"
                        color="amber"
                    />
                    {{-- Inner SWITCH: choose a delivery action from the publication channel. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.two-nested-2-mixed.inner-published"
                        attach-to="literature.switch.1.two-nested-2-mixed.outer.case.published.anchorNode-end"
                        side="right"
                        stem-length="2rem"
                        color="green"
                        :case-expression="[
                            'text' => ['SWITCH ($channel)'],
                            'width' => 'default',
                            'stemLength' => '3rem',
                        ]"
                        :cases="[
                            [
                                'key' => 'web',
                                'label' => ['text' => ['CASE web'], 'width' => 'half', 'align' => 'right'],
                                'actionLabel' => [
                                    'text' => ['Publish online'],
                                    'width' => 'default',
                                    'color' => 'violet',
                                ],
                            ],
                            [
                                'key' => 'print',
                                'label' => ['text' => ['CASE print'], 'width' => 'half', 'align' => 'right'],
                                'actionLabel' => [
                                    'text' => ['Queue print job'],
                                    'width' => 'default',
                                    'color' => 'sky',
                                ],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Queue manual delivery'],
                            'width' => 'default',
                            'color' => 'orange',
                            'label' => ['text' => ['DEFAULT'], 'width' => 'half', 'align' => 'right'],
                            'exitLabel' => ['text' => ['END delivery SWITCH'], 'width' => 'default', 'align' => 'left'],
                        ]"
                    />

                    {{-- Return above the inner SWITCH to the outer CASE output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-flow-switch-case-two-nested-2-mixed';
                        $publishedInnerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-2-mixed.inner-published.anchorNode-end',
                        );
                        $publishedOuterReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-2-mixed.outer.case.published.anchorNode-return',
                        );
                        $publishedReturnBridge = 'calc(' . $publishedInnerEnd['x'] . ' - ' . $publishedOuterReturn['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.switch.1.two-nested-2-mixed.inner-published-return"
                        :anchor-start="$publishedInnerEnd"
                        side="right"
                        arc-radius="2.75rem"
                        :bridge-length="$publishedReturnBridge"
                        color="green"
                        :joint-arrow-end="true"
                        dev-counter-end="R2"
                    />
                    @php
                        $publishedReturnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.two-nested-2-mixed.inner-published-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.switch.1.two-nested-2-mixed.inner-published-return.stem"
                        :anchor-start="$publishedReturnEnd"
                        return-to="literature.switch.1.two-nested-2-mixed.outer.case.published.anchorNode-return"
                        :length="'calc(' . $publishedOuterReturn['y'] . ' - ' . $publishedReturnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :dev-counter-end="false"
                        color="green"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.two-nested-2-mixed.continue"
                        attach-to="literature.switch.1.two-nested-2-mixed.outer.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'default']"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-switch-case-two-nested-2-example-2:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../flow/switch-case/flow-switch-case-two-nested-2.blade.php
        </flux:field>
    </flux:callout>
</section>
