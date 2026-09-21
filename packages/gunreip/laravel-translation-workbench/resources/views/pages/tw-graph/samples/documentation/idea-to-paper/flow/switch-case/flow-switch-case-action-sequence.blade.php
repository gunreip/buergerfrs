<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Action → Nested → Action') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.documentation-links example="flow.switch-case.flow-switch-case-action-sequence" />
        <flux:callout.text>
            {{ __('Action sequence inside one outer CASE: Prepare editing → nested SWITCH on format → Save editing result. Every inner CASE and its DEFAULT reach the final action before returning to the outer SWITCH. CASE published and the outer DEFAULT bypass the complete sequence. The left example shortens the outer bridges and routes the published entry around the inner block; the right example keeps the previous straight entry for comparison.') }}
        </flux:callout.text>
        @php
            $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-action-sequence',
            );
            $docExample1Code = $docExampleSource->example('flow-switch-case-action-sequence-example-1');
            $docExample2Code = $docExampleSource->example('flow-switch-case-action-sequence-example-2');
        @endphp
        <flux:heading
            class="mt-3"
            size="sm"
        >Outer left · Inner right · Compact entry detour</flux:heading>
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
                    <flux:table.cell class="whitespace-normal">finish attach-to<br>finish.anchorNode-end</flux:table.cell>
                    <flux:table.cell>null / generated</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Save editing result attaches to inner.anchorNode-end. The return starts only at finish.anchorNode-end, so no inner outcome skips this action.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">finish before-length<br>finish after-length</flux:table.cell>
                    <flux:table.cell>2rem / 2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Both explicitly set to 2rem around the final action.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases[].entryDetour</flux:table.cell>
                    <flux:table.cell>null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Left preview only: side=right, bridgeLength=10rem, arcRadius=2rem, beforeLength=0rem, afterLength=2rem. Total height remains stemLength=32rem; the middle stem is 22rem and the horizontal offset is 14rem. Composed by paths.stem-detour from parts.sideways and parts.start.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
        <x-translation-workbench::ui.tw-graph.language-examples
            source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-action-sequence"
            example="switch-action-sequence"
        >
            <flux:text class="mt-2 text-sm">
                {{ __('Draft, review and revision share Prepare editing, the inner switch on format, and Save editing result. Inner break statements lead to Save editing result; only the following outer break exits the outer switch.') }}
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
                {{ __('Prepare editing is the outer CASE action. actionLabel.return=false opens the nested sequence. A separate flow-step attaches to the common inner output; the return starts at finish.anchorNode-end. CASE published explicitly reserves 32rem for the inner SWITCH, final action and return. In the left example, outer bridge-length=3rem and published.entryDetour explicitly route the selection stem to the right. The detour preserves the 32rem entry height and the original endpoint. The right example retains bridge-length=8rem and a straight entry.') }}
            </flux:text>
            <flux:heading
                class="m-3"
                size="sm"
            >Outer left · Inner right · Compact entry detour</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-switch-case-action-sequence-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-switch-case-action-sequence"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="86rem"
                    min-height="80rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.switch.1.action-sequence-saved.start"
                        :start-label="['text' => ['Article request'], 'width' => 'default']"
                    />

                    {{-- Outer SWITCH: three CASE entries fuse before the shared action and inner SWITCH. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.action-sequence-saved.outer"
                        attach-to="literature.switch.1.action-sequence-saved.start.anchorNode-end"
                        side="left"
                        bridge-length="3rem"
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
                                'entryDetour' => [
                                    'side' => 'right',
                                    'bridgeLength' => '10rem',
                                    'arcRadius' => '2rem',
                                    'beforeLength' => '0rem',
                                    'afterLength' => '2rem',
                                ],
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
                        id="literature.switch.1.action-sequence-saved.inner"
                        attach-to="literature.switch.1.action-sequence-saved.outer.case.editable.anchorNode-end"
                        side="right"
                        bridge-length="2rem"
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
                                    'width' => 'default',
                                    'color' => 'violet',
                                ],
                            ],
                            [
                                'key' => 'image',
                                'label' => ['text' => ['CASE image'], 'width' => 'half', 'align' => 'right'],
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
                            'label' => ['text' => ['DEFAULT'], 'width' => 'half', 'align' => 'right'],
                            'exitLabel' => ['text' => ['END inner SWITCH'], 'width' => 'default', 'align' => 'left'],
                        ]"
                    />

                    {{-- Every inner CASE and DEFAULT reaches this action before the outer BREAK. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.action-sequence-saved.finish"
                        attach-to="literature.switch.1.action-sequence-saved.inner.anchorNode-end"
                        color="rose"
                        before-length="2rem"
                        after-length="2rem"
                        :step-label="[
                            'text' => ['Save editing result'],
                            'width' => 'default',
                        ]"
                    />

                    {{-- Return after Save editing result to this outer CASE output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-flow-switch-case-action-sequence';
                        $sequenceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.action-sequence-saved.finish.anchorNode-end',
                        );
                        $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.action-sequence-saved.outer.case.editable.anchorNode-return',
                        );
                        $returnBridge = 'calc(' . $sequenceEnd['x'] . ' - ' . $outerReturn['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.switch.1.action-sequence-saved.inner-return"
                        :anchor-start="$sequenceEnd"
                        side="right"
                        arc-radius="2.75rem"
                        :bridge-length="$returnBridge"
                        color="rose"
                        :joint-arrow-end="true"
                        dev-counter-end="R"
                    />
                    @php
                        $returnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.action-sequence-saved.inner-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.switch.1.action-sequence-saved.inner-return.stem"
                        :anchor-start="$returnEnd"
                        return-to="literature.switch.1.action-sequence-saved.outer.case.editable.anchorNode-return"
                        :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :dev-counter-end="false"
                        color="rose"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.action-sequence-saved.continue"
                        attach-to="literature.switch.1.action-sequence-saved.outer.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'default']"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-switch-case-action-sequence-example-1:end --}}
            </div>
            <flux:heading
                class="m-3"
                size="sm"
            >Outer side="right" · Inner side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-switch-case-action-sequence-example-2:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-switch-case-action-sequence-right"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="86rem"
                    min-height="80rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.switch.1.action-sequence-saved-right.start"
                        :start-label="['text' => ['Article request'], 'width' => 'default']"
                    />

                    {{-- Outer SWITCH: three CASE entries fuse before the shared action and inner SWITCH. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.action-sequence-saved-right.outer"
                        attach-to="literature.switch.1.action-sequence-saved-right.start.anchorNode-end"
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
                        id="literature.switch.1.action-sequence-saved-right.inner"
                        attach-to="literature.switch.1.action-sequence-saved-right.outer.case.editable.anchorNode-end"
                        side="left"
                        bridge-length="2rem"
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

                    {{-- Every inner CASE and DEFAULT reaches this action before the outer BREAK. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.action-sequence-saved-right.finish"
                        attach-to="literature.switch.1.action-sequence-saved-right.inner.anchorNode-end"
                        color="rose"
                        before-length="2rem"
                        after-length="2rem"
                        :step-label="[
                            'text' => ['Save editing result'],
                            'width' => 'default',
                        ]"
                    />

                    {{-- Return after Save editing result to this outer CASE output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-flow-switch-case-action-sequence-right';
                        $sequenceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.action-sequence-saved-right.finish.anchorNode-end',
                        );
                        $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.action-sequence-saved-right.outer.case.editable.anchorNode-return',
                        );
                        $returnBridge = 'calc(' . $outerReturn['x'] . ' - ' . $sequenceEnd['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.switch.1.action-sequence-saved-right.inner-return"
                        :anchor-start="$sequenceEnd"
                        side="left"
                        arc-radius="2.75rem"
                        :bridge-length="$returnBridge"
                        color="rose"
                        :joint-arrow-end="true"
                        dev-counter-end="R"
                    />
                    @php
                        $returnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.switch.1.action-sequence-saved-right.inner-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.switch.1.action-sequence-saved-right.inner-return.stem"
                        :anchor-start="$returnEnd"
                        return-to="literature.switch.1.action-sequence-saved-right.outer.case.editable.anchorNode-return"
                        :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :dev-counter-end="false"
                        color="rose"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.action-sequence-saved-right.continue"
                        attach-to="literature.switch.1.action-sequence-saved-right.outer.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'default']"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-switch-case-action-sequence-example-2:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../flow/switch-case/flow-switch-case-action-sequence.blade.php
        </flux:field>
    </flux:callout>
</section>
