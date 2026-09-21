<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('CASE grouped (>4)') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.documentation-links example="flow.switch-case.flow-switch-case-grouped-multi" />
        <flux:callout.text>
            {{ __('One SWITCH expression selects a CASE. Four inputs on the left and five inputs on the right share one action: Open editor. The right example adds CASE reopened. Published displays the article; DEFAULT shows a status hint. Each selected action leaves the switch via the common output. Separate CASE entry lanes merge before one shared action and one break; no subsequent action is executed.') }}
        </flux:callout.text>
        @php
            $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-grouped-multi',
            );
            $docExample1Code = $docExampleSource->example('flow-switch-case-grouped-multi-example-1');
            $docExample2Code = $docExampleSource->example('flow-switch-case-grouped-multi-example-2');
        @endphp
        <flux:heading
            class="mt-3"
            size="sm"
        >4 inputs / side="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $docExample1Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >5 inputs / side="right"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $docExample2Code }}</x-translation-workbench::ui.tw-graph.code-box>
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
            source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-grouped-multi"
            example="switch-grouped-4"
        >
            <flux:text class="mt-2 text-sm">
                {{ __('Left preview: four CASE values lead to the same Open editor action.') }}
            </flux:text>
            <flux:text class="mt-2 text-sm">
                {{ __('Action functions are supplied by the application. These are syntax excerpts; enclosing functions, classes and imports are omitted. C and C++ use enums instead of string cases. Left and right layouts represent the same logic unless different entry counts are shown.') }}
            </flux:text>
        </x-translation-workbench::ui.tw-graph.language-examples>
        <x-translation-workbench::ui.tw-graph.language-examples
            source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-grouped-multi"
            example="switch-grouped-5"
        >
            <flux:text class="mt-2 text-sm">
                {{ __('Right preview: five CASE values lead to the same Open editor action.') }}
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
                {{ __('Left: four entries merge between review and revision. Right: five entries include reopened, with revision continuing straight through the center. Each outer stem joins the next inner arc end without another dot or joint arrow. All entry arcs use a shared radius; short positive compensators are replaced by direct arc pairs. The helper descriptions, labels and actions remain individually editable.') }}
            </flux:text>
            <flux:heading
                class="mt-3"
                size="sm"
            >4 inputs / side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-switch-case-grouped-multi-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-switch-case-grouped-multi"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="68rem"
                    min-height="59rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.switch.1.grouped-multi.start"
                        :start-label="['text' => ['Article request'], 'width' => 'default']"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.grouped-multi.status"
                        attach-to="literature.switch.1.grouped-multi.start.anchorNode-end"
                        side="left"
                        stem-length="4rem"
                        color="cyan"
                        :case-expression="[
                            'text' => ['SWITCH ($status)'],
                            'width' => 'halfLong',
                            'stemLength' => '3rem',
                        ]"
                        :cases="[
                            [
                                'key' => 'editable',
                                'entryStemLength' => '4rem',
                                'entries' => [
                                    [
                                        'key' => 'draft',
                                        'label' => [
                                            'text' => ['CASE draft'],
                                            'align' => 'left',
                                            'width' => 'default',
                                        ],
                                        'color' => 'amber',
                                    ],
                                    [
                                        'key' => 'review',
                                        'label' => [
                                            'text' => ['CASE review'],
                                            'align' => 'left',
                                            'width' => 'default',
                                        ],
                                        'color' => 'orange',
                                    ],
                                    [
                                        'key' => 'revision',
                                        'label' => [
                                            'text' => ['CASE revision'],
                                            'align' => 'left',
                                            'width' => 'default',
                                        ],
                                        'color' => 'violet',
                                    ],
                                    [
                                        'key' => 'returned',
                                        'label' => [
                                            'text' => ['CASE returned'],
                                            'align' => 'left',
                                            'width' => 'default',
                                        ],
                                        'color' => 'sky',
                                    ],
                                ],
                                'exitLabel' => [
                                    'text' => ['BREAK'],
                                    'align' => 'right',
                                ],
                                'actionLabel' => [
                                    'text' => ['Open editor'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                ],
                            ],
                            [
                                'key' => 'published',
                                'stemLength' => '4rem',
                                'label' => [
                                    'text' => ['CASE published'],
                                    'align' => 'left',
                                ],
                                'exitLabel' => [
                                    'text' => ['BREAK'],
                                    'align' => 'right',
                                ],
                                'actionLabel' => [
                                    'text' => ['Display article'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ],
                            ],
                        ]"
                        :case-default="[
                            'label' => [
                                'text' => ['DEFAULT'],
                                'align' => 'left',
                            ],
                            'exitLabel' => [
                                'text' => ['END SWITCH'],
                                'align' => 'right',
                            ],
                            'text' => ['Show status hint'],
                            'width' => 'default',
                            'color' => 'zinc',
                        ]"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.grouped-multi.continue"
                        attach-to="literature.switch.1.grouped-multi.status.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-switch-case-grouped-multi-example-1:end --}}
            </div>
            <flux:heading
                class="mt-4"
                size="sm"
            >5 inputs / side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-switch-case-grouped-multi-example-2:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-switch-case-grouped-multi-right"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="68rem"
                    min-height="65rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.switch.1.grouped-multi-right.start"
                        :start-label="['text' => ['Article request'], 'width' => 'half']"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.grouped-multi-right.status"
                        attach-to="literature.switch.1.grouped-multi-right.start.anchorNode-end"
                        side="right"
                        stem-length="4rem"
                        color="cyan"
                        :case-expression="[
                            'text' => ['SWITCH ($status)'],
                            'width' => 'halfLong',
                            'stemLength' => '3rem',
                        ]"
                        :cases="[
                            [
                                'key' => 'editable',
                                'entryStemLength' => '3rem',
                                'entries' => [
                                    [
                                        'key' => 'draft',
                                        'label' => [
                                            'text' => ['CASE draft'],
                                            'align' => 'right',
                                            'width' => 'half',
                                        ],
                                        'color' => 'amber',
                                    ],
                                    [
                                        'key' => 'review',
                                        'label' => [
                                            'text' => ['CASE review'],
                                            'align' => 'right',
                                            'width' => 'half',
                                        ],
                                        'color' => 'orange',
                                    ],
                                    [
                                        'key' => 'revision',
                                        'label' => [
                                            'text' => ['CASE revision'],
                                            'align' => 'right',
                                            'width' => 'half',
                                        ],
                                        'color' => 'violet',
                                    ],
                                    [
                                        'key' => 'returned',
                                        'label' => [
                                            'text' => ['CASE returned'],
                                            'align' => 'right',
                                            'width' => 'default',
                                        ],
                                        'color' => 'sky',
                                    ],
                                    [
                                        'key' => 'reopened',
                                        'label' => [
                                            'text' => ['CASE reopened'],
                                            'align' => 'right',
                                            'width' => 'default',
                                        ],
                                        'color' => 'rose',
                                    ],
                                ],
                                'exitLabel' => [
                                    'text' => ['BREAK'],
                                    'align' => 'left',
                                ],
                                'actionLabel' => [
                                    'text' => ['Open editor'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                ],
                            ],
                            [
                                'key' => 'published',
                                'stemLength' => '4rem',
                                'label' => [
                                    'text' => ['CASE published'],
                                    'align' => 'right',
                                ],
                                'exitLabel' => [
                                    'text' => ['BREAK'],
                                    'align' => 'left',
                                ],
                                'actionLabel' => [
                                    'text' => ['Display article'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ],
                            ],
                        ]"
                        :case-default="[
                            'label' => [
                                'text' => ['DEFAULT'],
                                'width' => 'half',
                                'align' => 'right',
                            ],
                            'exitLabel' => [
                                'text' => ['END SWITCH'],
                                'align' => 'left',
                            ],
                            'text' => ['Show status hint'],
                            'width' => 'default',
                            'color' => 'zinc',
                        ]"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.grouped-multi-right.continue"
                        attach-to="literature.switch.1.grouped-multi-right.status.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-switch-case-grouped-multi-example-2:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../flow/switch-case/flow-switch-case-grouped-multi.blade.php
        </flux:field>
    </flux:callout>
</section>
