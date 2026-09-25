<x-translation-workbench::ui.common.heading-counter-group group="flow-switch-case-grouped-3">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('CASE grouped (3)') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.switch-case.flow-switch-case-grouped-3" />
            <flux:callout.text>
                {{ __('One SWITCH expression selects a CASE. Draft, review and revision share one action: Open editor. Published displays the article; DEFAULT shows a status hint. Each selected action leaves the switch via the common output. Separate CASE entry lanes merge before one shared action and one break; no subsequent action is executed.') }}
            </flux:callout.text>
            @php
                $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-grouped-3',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="flow-switch-case-grouped-3-example-1"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $docExampleSource->example('flow-switch-case-grouped-3-example-1') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="flow-switch-case-grouped-3-example-2"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $docExampleSource->example('flow-switch-case-grouped-3-example-2') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator class="mt-4" :text="__('Props used in these examples')" />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Props and connections') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns class="bg-white dark:bg-zinc-900" sticky>
                            <flux:table.column>{{ __('Prop / anchor') }}</flux:table.column>
                            <flux:table.column>{{ __('Default') }}</flux:table.column>
                            <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph-id.switch</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Root ID for all generated switch elements.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Attach the expression step to a preceding component output.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>left</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('left or right: direction of the case actions.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bottom-top</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('bottom-top or top-bottom: flow direction.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>case-expression</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>SWITCH expression; halfLong</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('A single expression step, before case selection.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cases</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]; at least one required</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Ordered cases with unique key, label and actionLabel. The key default is reserved.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cases[].entries</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional CASE entries with unique key, label and color. Each entry has its own lane; all entries merge before the group’s single actionLabel. Entry color defaults to the action color. The group’s stemLength positions the first input; entryStemLength sets all subsequent spacings.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cases[].entryStemLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>max(3rem, twice arc-radius)</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Uniform spacing between the group’s inputs; explicit values must be at least 3rem. Does not change the first entry position. Odd input counts have a straight middle lane; even counts place the output between the middle inputs.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cases[].label.align<br>cases[].entries[].label.align</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>center</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Text alignment: left, center or right. Supply label as an array with text and align. Other label options, including width and connectorLength, are preserved. A plain string remains valid.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>case-default</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>Default action; halfLong</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Action used when no case matches.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cases[].exitLabel</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>BREAK; half</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Exit annotation after the case action (once per group). Supports text, align, width, color and connector options. Alignment defaults to center. Use false to hide the annotation; routing stays unchanged.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>case-default.label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>DEFAULT; halfLong</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Entry annotation for the fallback; independent of its action text. Supports text, align, width, color and connector options. Alignment defaults to center. Use false to hide the annotation; routing stays unchanged.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>case-default.exitLabel</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>END SWITCH; half</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Annotation at the common output. Supports text, align, width, color and connector options. Alignment defaults to center. Use false to hide the annotation; routing stays unchanged.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>10rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Default length of each entry stem, including the first case and DEFAULT.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>arc-radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2.75rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Radius of the two arcs in each case route.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Minimum bridge length; shorter action labels are compensated so outputs align.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inherited / zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Expression, selection and shared return color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cases[].actionLabel.color<br>case-default.color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>component color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicit color for each action route.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>.anchorNode-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>common output</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('All case exits merge here before the independent continuation.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>case-expression.stemLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Entry stem to the first case. This example explicitly uses 3rem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cases[].stemLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Overrides the entry stem of that case. On the first case it takes precedence over case-expression.stemLength.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>case-default.stemLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Overrides the entry stem of DEFAULT. These overrides do not change the stems inside the expression step.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-grouped-3"
                example="switch-grouped-3"
            >
                <flux:text class="mt-2 text-sm">
                    {{ __('Three CASE values share one action. Adding another entry adds a case label, not another action.') }}
                </flux:text>
                <flux:text class="mt-2 text-sm">
                    {{ __('Action functions are supplied by the application. These are syntax excerpts; enclosing functions, classes and imports are omitted. C and C++ use enums instead of string cases. Left and right layouts represent the same logic unless different entry counts are shown.') }}
                </flux:text>
            </x-translation-workbench::ui.tw-graph.language-examples>

        </flux:callout>
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <flux:text class="m-3">
                    {{ __('CASE draft, CASE review and CASE revision share one action. The group uses the same entryStemLength for both intervals: the middle review lane continues straight, while the outer lanes bend toward the shared output. Change entryStemLength to adjust the entire group uniformly. Fusion arcs start at half the case arc radius. A positive stem below 1rem enlarges the arcs until the stem disappears; the output lies halfway between the outermost lanes. Case labels explain selection; actions remain inside their bridges. break marks an exit, not an additional action.') }}
                </flux:text>
                <x-translation-workbench::ui.common.heading-counter
                    example="flow-switch-case-grouped-3-example-1"
                    size="sm"
                >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- flow-switch-case-grouped-3-example-1:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-flow-switch-case-grouped-3"
                        :dev="true"
                        :coordinates="true"
                        color="cyan"
                        min-width="68rem"
                        min-height="53rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.flow-start
                            id="literature.switch.1.grouped-3.start"
                            :start-label="['text' => ['Article request'], 'width' => 'half']"
                        />
                        <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                            id="literature.switch.1.grouped-3.status"
                            attach-to="literature.switch.1.grouped-3.start.anchorNode-end"
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
                                    'entryStemLength' => '5rem',
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
                            id="literature.switch.1.grouped-3.continue"
                            attach-to="literature.switch.1.grouped-3.status.anchorNode-end"
                            color="fuchsia"
                            :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- flow-switch-case-grouped-3-example-1:end --}}
                </div>
                <x-translation-workbench::ui.common.heading-counter
                    example="flow-switch-case-grouped-3-example-2"
                    size="sm"
                >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- flow-switch-case-grouped-3-example-2:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-flow-switch-case-grouped-3-right"
                        :dev="true"
                        :coordinates="true"
                        color="cyan"
                        min-width="68rem"
                        min-height="50rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.flow-start
                            id="literature.switch.1.grouped-3-right.start"
                            :start-label="['text' => ['Article request'], 'width' => 'half']"
                        />
                        <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                            id="literature.switch.1.grouped-3-right.status"
                            attach-to="literature.switch.1.grouped-3-right.start.anchorNode-end"
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
                                                'width' => 'default',
                                            ],
                                            'color' => 'amber',
                                        ],
                                        [
                                            'key' => 'review',
                                            'label' => [
                                                'text' => ['CASE review'],
                                                'align' => 'right',
                                                'width' => 'default',
                                            ],
                                            'color' => 'orange',
                                        ],
                                        [
                                            'key' => 'revision',
                                            'label' => [
                                                'text' => ['CASE revision'],
                                                'align' => 'right',
                                                'width' => 'default',
                                            ],
                                            'color' => 'violet',
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
                            id="literature.switch.1.grouped-3-right.continue"
                            attach-to="literature.switch.1.grouped-3-right.status.anchorNode-end"
                            color="fuchsia"
                            :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- flow-switch-case-grouped-3-example-2:end --}}
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../flow/switch-case/flow-switch-case-grouped-3.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
