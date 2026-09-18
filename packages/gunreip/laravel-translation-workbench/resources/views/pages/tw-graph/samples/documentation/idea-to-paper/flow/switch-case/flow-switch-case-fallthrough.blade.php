<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('CASE fallthrough') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('One SWITCH expression selects a CASE. Draft prepares the article and falls through to Display article. Published enters Display article directly. Its break leaves the SWITCH. Other values use DEFAULT. Unlike grouped CASEs, fall-through executes two distinct actions in sequence.') }}
        </flux:callout.text>
        @php
            $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-fallthrough',
            );
            $docExample1Code = $docExampleSource->example('flow-switch-case-fallthrough-example-1');
            $docExample2Code = $docExampleSource->example('flow-switch-case-fallthrough-example-2');
        @endphp
        <flux:heading class="mt-3" size="sm">side="left"</flux:heading>
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
                    <flux:table.cell class="whitespace-normal">
                        cases[].actionLabel.bridgeOutLength<br>case-default.bridgeOutLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">calculated bridge length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Explicit outgoing bridge length after the action.
                        Fall-through does not shorten it automatically. Here 2rem keeps the outgoing bridge visible and
                        separates this action exit from the break rail. For a normal break route, the incoming bridge
                        compensates the outgoing override to keep the shared output aligned.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases[].fallThroughJoinLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">arc-radius</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Offset into the next action’s incoming bridge for the
                        joining Dot. Here 2.75rem; bridge-length is explicitly 4.75rem to leave 2rem before the text.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases[].fallThrough</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">false</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">true continues into the next action without testing its
                        CASE. The default exit annotation becomes fall-through. Cannot be combined with
                        actionLabel.return = false. All connecting arcs use arc-radius. The next stemLength must provide
                        room for three arcs; bridge-length must explicitly provide room for the joining arc and its Dot.
                    </flux:table.cell>
                </flux:table.row>
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
                    <flux:table.cell class="whitespace-normal">Action used when no case matches. Set
                        :case-default="false" to omit DEFAULT and render a plain bypass without an action. Existing
                        examples retain their configured DEFAULT.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">cases[].exitLabel</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">break; half</flux:table.cell>
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
                {{ __('cases[].fallThrough = true connects the action output to the next action entry, after its CASE selection point. It does not test the next CASE again. The example explicitly sets arc-radius=2.75rem, bridge-length=4.75rem, actionLabel.bridgeOutLength=2rem, fallThroughJoinLength=2.75rem and the following stemLength=14rem. These values can be edited independently; fallThrough does not override them. A normal break skips all remaining actions.') }}
            </flux:text>
            <flux:heading class="m-3" size="sm">side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-switch-case-fallthrough-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-switch-case-fallthrough"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="60rem"
                    min-height="58rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.switch.1.fallthrough.start"
                        :start-label="['text' => ['Article request'], 'width' => 'default']"
                    />
                    {{-- CASE draft falls through; CASE published executes only Display article. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.fallthrough.status"
                        attach-to="literature.switch.1.fallthrough.start.anchorNode-end"
                        side="left"
                        stem-length="5rem"
                        color="cyan"
                        arc-radius="2.75rem"
                        bridge-length="4.75rem"
                        :case-expression="[
                            'text' => ['SWITCH ($status)'],
                            'width' => 'default',
                            'stemLength' => '3rem',
                        ]"
                        :cases="[
                            [
                                'key' => 'draft',
                                'fallThrough' => true,
                                'fallThroughJoinLength' => '2.75rem',
                                'label' => ['text' => ['CASE draft'], 'width' => 'default', 'align' => 'left'],
                                'actionLabel' => [
                                    'text' => ['Prepare article'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                    'bridgeOutLength' => '2rem',
                                ],
                            ],
                            [
                                'key' => 'published',
                                'stemLength' => '14rem',
                                'label' => ['text' => ['CASE published'], 'width' => 'default', 'align' => 'left'],
                                'actionLabel' => [
                                    'text' => ['Display article'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ],
                                'exitLabel' => ['text' => ['break'], 'align' => 'right'],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Show status hint'],
                            'width' => 'default',
                            'color' => 'zinc',
                            'label' => ['text' => ['DEFAULT'], 'width' => 'default', 'align' => 'left'],
                            'exitLabel' => ['text' => ['END SWITCH'], 'width' => 'default', 'align' => 'right'],
                        ]"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.fallthrough.continue"
                        attach-to="literature.switch.1.fallthrough.status.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'default']"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-switch-case-fallthrough-example-1:end --}}
            </div>
            <flux:heading class="m-3" size="sm">side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-switch-case-fallthrough-example-2:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-switch-case-fallthrough-right"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="60rem"
                    min-height="58rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.switch.1.fallthrough-right.start"
                        :start-label="['text' => ['Article request'], 'width' => 'default']"
                    />
                    {{-- CASE draft falls through; CASE published executes only Display article. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                        id="literature.switch.1.fallthrough-right.status"
                        attach-to="literature.switch.1.fallthrough-right.start.anchorNode-end"
                        side="right"
                        stem-length="5rem"
                        color="cyan"
                        arc-radius="2.75rem"
                        bridge-length="4.75rem"
                        :case-expression="[
                            'text' => ['SWITCH ($status)'],
                            'width' => 'default',
                            'stemLength' => '3rem',
                        ]"
                        :cases="[
                            [
                                'key' => 'draft',
                                'fallThrough' => true,
                                'fallThroughJoinLength' => '2.75rem',
                                'label' => ['text' => ['CASE draft'], 'width' => 'default', 'align' => 'right'],
                                'actionLabel' => [
                                    'text' => ['Prepare article'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                    'bridgeOutLength' => '2rem',
                                ],
                            ],
                            [
                                'key' => 'published',
                                'stemLength' => '14rem',
                                'label' => ['text' => ['CASE published'], 'width' => 'default', 'align' => 'right'],
                                'actionLabel' => [
                                    'text' => ['Display article'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ],
                                'exitLabel' => ['text' => ['break'], 'align' => 'left'],
                            ],
                        ]"
                        :case-default="[
                            'text' => ['Show status hint'],
                            'width' => 'default',
                            'color' => 'zinc',
                            'label' => ['text' => ['DEFAULT'], 'width' => 'default', 'align' => 'right'],
                            'exitLabel' => ['text' => ['END SWITCH'], 'width' => 'default', 'align' => 'left'],
                        ]"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.switch.1.fallthrough-right.continue"
                        attach-to="literature.switch.1.fallthrough-right.status.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'default']"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-switch-case-fallthrough-example-2:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
    </flux:callout>
</section>
