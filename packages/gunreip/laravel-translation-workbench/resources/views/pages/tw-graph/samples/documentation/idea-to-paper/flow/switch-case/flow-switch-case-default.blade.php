<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('CASEs single') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.documentation-links example="flow.switch-case.flow-switch-case-default" />
        <flux:callout.text>
            {{ __('One SWITCH expression selects a CASE. Draft opens the editor; published displays the article; DEFAULT shows a status hint. Each selected action leaves the switch via the common output. This first example has no fall-through.') }}
        </flux:callout.text>
        @php
            $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-default',
            );
            $docExample1Code = $docExampleSource->example('flow-switch-case-default-example-1');
            $docExample2Code = $docExampleSource->example('flow-switch-case-default-example-2');
        @endphp
        <flux:heading class="mt-4" size="sm">side="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $docExample1Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">side="right"</flux:heading>
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
                    <flux:table.cell class="whitespace-normal">case-default</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Default action; halfLong</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Action used when no case matches.</flux:table.cell>
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
            source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-default"
        >
            <flux:text class="mt-2 text-sm">
                {{ __('The action functions represent the graph actions and are supplied by the application. These are syntax excerpts; enclosing functions, classes and imports are omitted.') }}
            </flux:text>
            <flux:text class="mt-2 text-sm">
                {{ __('C and C++ use enum values because their switch statements do not accept strings. PHP compares case values loosely; JavaScript uses strict equality. C# and Java also support string cases. Each example exits the selected case with break and then continues after the switch.') }}
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
                {{ __('Case labels explain selection; actions remain inside their bridges. break marks an exit, not an additional action.') }}
            </flux:text>
            <flux:heading class="m-3" size="sm">side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-switch-case-default-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-switch-case-default"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="68rem"
                    min-height="44rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.switch.1.basic.start"
                        :start-label="['text' => ['Article request'], 'width' => 'half']"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.basic.status"
                        attach-to="literature.switch.1.basic.start.anchorNode-end"
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
                                'key' => 'draft',
                                'label' => 'CASE draft',
                                'actionLabel' => [
                                    'text' => ['Open editor'],
                                    'width' => 'halfLong',
                                    'color' => 'amber',
                                ],
                            ],
                            [
                                'key' => 'published',
                                'stemLength' => '4rem',
                                'label' => 'CASE published',
                                'actionLabel' => [
                                    'text' => ['Display article'],
                                    'width' => 'halfLong',
                                    'color' => 'green',
                                ],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Show status hint'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                        ]"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.basic.continue"
                        attach-to="literature.switch.1.basic.status.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-switch-case-default-example-1:end --}}
            </div>
            <flux:heading class="m-3" size="sm">side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-switch-case-default-example-2:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-switch-case-default-right"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="68rem"
                    min-height="44rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.switch.1.basic-right.start"
                        :start-label="['text' => ['Article request'], 'width' => 'half']"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.basic-right.status"
                        attach-to="literature.switch.1.basic-right.start.anchorNode-end"
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
                                'key' => 'draft',
                                'label' => 'CASE draft',
                                'actionLabel' => [
                                    'text' => ['Open editor'],
                                    'width' => 'halfLong',
                                    'color' => 'amber',
                                ],
                            ],
                            [
                                'key' => 'published',
                                'stemLength' => '4rem',
                                'label' => 'CASE published',
                                'actionLabel' => [
                                    'text' => ['Display article'],
                                    'width' => 'halfLong',
                                    'color' => 'green',
                                ],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Show status hint'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                        ]"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.basic-right.continue"
                        attach-to="literature.switch.1.basic-right.status.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-switch-case-default-example-2:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../flow/switch-case/flow-switch-case-default.blade.php
        </flux:field>
    </flux:callout>
</section>
