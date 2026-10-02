<flux:callout
    class="min-w-0"
    color="emerald"
>
    <flux:callout.heading icon="eye">{{ __('Flow topics and example groups') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">
        {{ __('Each Flow topic branches into its saved example tabs. Only tab names appear in the tree; short explanations are available in the tooltips.') }}
    </flux:callout.text>
    <x-translation-workbench::ui.tw-graph.preview-tools
        :dev="$dev ?? true"
        :coordinates="$coordinates ?? false"
    >
        <div
            class="mt-3 overflow-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
            style="max-height: 48rem;"
        >
            <div class="w-full min-w-0">
                {{-- overview-flow-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-overview-flow"
                    :dev="true"
                    :coordinates="true"
                    min-width="104rem"
                    min-height="24rem"
                    horizontal-padding="3rem"
                >
                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.root.stem"
                        direction="top-bottom"
                        length="3.25rem"
                        :anchor-start="['x' => '0rem', 'y' => '0rem']"
                        :anchor-end="['x' => '0rem', 'y' => '-3.25rem']"
                        color="zinc"
                        :node-start="true"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.label
                        id="literature.overview.flow.root.label"
                        anchor-x="0rem"
                        anchor-y="0rem"
                        side="right"
                        color="zinc"
                        :label="[
                            'text' => [__('Flow')],
                            'width' => 'halfLong',
                            'align' => 'left',
                            'tooltip' => __('Control structures, their sub-tabs and saved examples.'),
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.root.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '0rem', 'y' => '-3.25rem'],
                            'anchorEnd' => ['x' => '2.75rem', 'y' => '-6rem'],
                            'color' => 'zinc',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.root.bridge"
                        direction="left-right"
                        length="76rem"
                        :anchor-start="['x' => '2.75rem', 'y' => '-6rem']"
                        :anchor-end="['x' => '78.75rem', 'y' => '-6rem']"
                        color="zinc"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.column-1.column',
                            'startAnchor' => 'w',
                            'endAnchor' => 's',
                            'anchorStart' => ['x' => '2.75rem', 'y' => '-6rem'],
                            'anchorEnd' => ['x' => '5.5rem', 'y' => '-8.75rem'],
                            'color' => 'zinc',
                        ]"
                    />

                    {{-- Flow start --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-start.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '5.5rem', 'y' => '-8.75rem']"
                        :anchor-end="['x' => '5.5rem', 'y' => '-11rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-start.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '5.5rem', 'y' => '-11rem'],
                            'anchorEnd' => ['x' => '8.25rem', 'y' => '-13.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow start')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Entry into an execution flow.'),
                            ],
                        ]"
                    />

                    {{-- Flow step --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-step.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '5.5rem', 'y' => '-11rem']"
                        :anchor-end="['x' => '5.5rem', 'y' => '-16rem']"
                        color="indigo"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-step.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '5.5rem', 'y' => '-16rem'],
                            'anchorEnd' => ['x' => '8.25rem', 'y' => '-18.75rem'],
                            'color' => 'indigo',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow step')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('An action within an execution flow.'),
                            ],
                        ]"
                    />

                    {{-- Flow branch steps --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-branch-steps.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '5.5rem', 'y' => '-16rem']"
                        :anchor-end="['x' => '5.5rem', 'y' => '-21rem']"
                        color="emerald"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-branch-steps.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '5.5rem', 'y' => '-21rem'],
                            'anchorEnd' => ['x' => '8.25rem', 'y' => '-23.75rem'],
                            'color' => 'emerald',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow branch steps')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Multiple actions along a branch.'),
                            ],
                        ]"
                    />

                    {{-- Flow IF --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '5.5rem', 'y' => '-21rem']"
                        :anchor-end="['x' => '5.5rem', 'y' => '-26rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '5.5rem', 'y' => '-26rem'],
                            'anchorEnd' => ['x' => '8.25rem', 'y' => '-28.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow IF')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Conditional branches and nested decisions.'),
                            ],
                        ]"
                    />

                    {{-- IF --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-simple.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-28.75rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-31rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-simple.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-31rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-33.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('A simple IF executes its action only when the condition is true.'),
                            ],
                        ]"
                    />

                    {{-- IF ELSE --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-else.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-31rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-36rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-else.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-36rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-38.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF ELSE')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('An IF ELSE first evaluates its question inside a step.'),
                            ],
                        ]"
                    />

                    {{-- IF ELSEIF --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-elseif.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-36rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-41rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-elseif.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-41rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-43.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF ELSEIF')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The first matching condition selects one action.'),
                            ],
                        ]"
                    />

                    {{-- IF ELSEIF multi --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-elseif-multi.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-41rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-46rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-elseif-multi.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-46rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-48.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF ELSEIF multi')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('IF ELSEIF multi supports one or more individually defined ELSEIF branches.'),
                            ],
                        ]"
                    />

                    {{-- IF ternär --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-ternary.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-46rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-51rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-ternary.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-51rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-53.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF ternär')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'A ternary expression evaluates a condition and yields exactly one selected value.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- IF nested 1 --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-nested-1.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-51rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-56rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-nested-1.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-56rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-58.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF nested 1')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The outer IF enters the amber inner IF only when review is required.'),
                            ],
                        ]"
                    />

                    {{-- IF nested 2 --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-nested-2.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-56rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-61rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-nested-2.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-61rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-63.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF nested 2')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The amber inner IF belongs to the second outer ELSEIF (sources).'),
                            ],
                        ]"
                    />

                    {{-- IF nested 3 --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-nested-3.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-61rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-66rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-nested-3.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-66rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-68.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF nested 3')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The amber inner IF belongs to the outer ELSE fallback.'),
                            ],
                        ]"
                    />

                    {{-- IF nested 4 --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-nested-4.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-66rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-71rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-nested-4.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-71rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-73.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF nested 4')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The amber inner IF belongs to the last outer ELSEIF (deferred).'),
                            ],
                        ]"
                    />

                    {{-- IF nested 5 --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-nested-5.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-71rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-76rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-nested-5.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-76rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-78.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF nested 5')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'Two independent inner IF blocks belong to different outer ELSEIF actions: automatic approval (sky) and deferred review (amber).',
                                            ),
                            ],
                        ]"
                    />

                    {{-- IF nested 6 --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-nested-6.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-76rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-81rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-nested-6.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-81rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-83.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF nested 6')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Three nested levels: the outer deferred action enters the amber review IF.'),
                            ],
                        ]"
                    />

                    {{-- IF nested 7 --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-nested-7.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-81rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-86rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-nested-7.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-86rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-88.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF nested 7')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Three nested levels: the outer deferred action enters the amber review IF.'),
                            ],
                        ]"
                    />

                    {{-- IF nested 8 --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-nested-8.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-86rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-91rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-nested-8.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-91rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-93.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF nested 8')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'Mixed sides: an outer left IF contains an inner right IF, and the second example reverses both sides.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- IF nested 9 --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-if-nested-9.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-91rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-96rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-if-nested-9.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-96rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-98.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF nested 9')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'Action sequence inside one outer branch: prepare review data, run the nested IF, then save the result.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Flow SWITCH/CASE --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case.stem"
                        direction="top-bottom"
                        length="75rem"
                        :anchor-start="['x' => '5.5rem', 'y' => '-26rem']"
                        :anchor-end="['x' => '5.5rem', 'y' => '-101rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '5.5rem', 'y' => '-101rem'],
                            'anchorEnd' => ['x' => '8.25rem', 'y' => '-103.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow SWITCH/CASE')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Case selection, shared actions, fall-through and nested switches.'),
                            ],
                        ]"
                    />

                    {{-- CASEs single --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case-default.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-103.75rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-106rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case-default.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-106rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-108.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CASEs single')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('One SWITCH expression selects a CASE.'),
                            ],
                        ]"
                    />

                    {{-- CASEs grouped --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case-grouped.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-106rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-111rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case-grouped.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-111rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-113.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CASEs grouped')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('One SWITCH expression selects a CASE.'),
                            ],
                        ]"
                    />

                    {{-- CASE grouped (3) --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case-grouped-3.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-111rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-116rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case-grouped-3.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-116rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-118.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CASE grouped (3)')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('One SWITCH expression selects a CASE.'),
                            ],
                        ]"
                    />

                    {{-- CASE grouped (>4) --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case-grouped-multi.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-116rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-121rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case-grouped-multi.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-121rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-123.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CASE grouped (>4)')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('One SWITCH expression selects a CASE.'),
                            ],
                        ]"
                    />

                    {{-- CASE nested --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case-nested.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-121rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-126rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case-nested.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-126rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-128.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CASE nested')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('One SWITCH expression selects a CASE.'),
                            ],
                        ]"
                    />

                    {{-- CASEs without DEFAULT --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case-without-default.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-126rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-131rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case-without-default.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-131rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-133.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CASEs without DEFAULT')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('One SWITCH expression selects a CASE.'),
                            ],
                        ]"
                    />

                    {{-- CASE fallthrough --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case-fallthrough.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-131rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-136rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case-fallthrough.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-136rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-138.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CASE fallthrough')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('One SWITCH expression selects a CASE.'),
                            ],
                        ]"
                    />

                    {{-- CASE SWITCH CASE (1) --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case-nested-1.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-136rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-141rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case-nested-1.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-141rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-143.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CASE SWITCH CASE (1)')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('One SWITCH expression selects a CASE.'),
                            ],
                        ]"
                    />

                    {{-- CASE SWITCH CASE (2) --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case-nested-2.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-141rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-146rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case-nested-2.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-146rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-148.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CASE SWITCH CASE (2)')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('One SWITCH expression selects a CASE.'),
                            ],
                        ]"
                    />

                    {{-- 2 nested CASEs in SWITCH (1) --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case-two-nested-1.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-146rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-151rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case-two-nested-1.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-151rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-153.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('2 nested CASEs in SWITCH (1)')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('One SWITCH expression selects a CASE.'),
                            ],
                        ]"
                    />

                    {{-- 2 nested CASEs in SWITCH (2) --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case-two-nested-2.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-151rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-156rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case-two-nested-2.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-156rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-158.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('2 nested CASEs in SWITCH (2)')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('One SWITCH expression selects a CASE.'),
                            ],
                        ]"
                    />

                    {{-- 2 nested CASEs in SWITCH (3) --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case-two-nested-3.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-156rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-161rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case-two-nested-3.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-161rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-163.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('2 nested CASEs in SWITCH (3)')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Mixed sides: outer left / inner right and outer right / inner left.'),
                            ],
                        ]"
                    />

                    {{-- Action → Nested → Action --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-switch-case-action-sequence.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-161rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-166rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-switch-case-action-sequence.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-166rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-168.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Action → Nested → Action')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'Action sequence inside one outer CASE: Prepare editing → nested SWITCH on format → Save editing result.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Flow WHILE --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-while.stem"
                        direction="top-bottom"
                        length="70rem"
                        :anchor-start="['x' => '5.5rem', 'y' => '-101rem']"
                        :anchor-end="['x' => '5.5rem', 'y' => '-171rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-while.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '5.5rem', 'y' => '-171rem'],
                            'anchorEnd' => ['x' => '8.25rem', 'y' => '-173.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow WHILE')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Loops that check their condition before entering the body.'),
                            ],
                        ]"
                    />

                    {{-- WHILE basic --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-while-basic.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-173.75rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-176rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-while-basic.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-176rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-178.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('WHILE basic')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Check the condition before every iteration.'),
                            ],
                        ]"
                    />

                    {{-- WHILE multiple actions --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-while-multiple-actions.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-176rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-181rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-while-multiple-actions.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-181rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-183.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('WHILE multiple actions')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Initialize the item list and index once.'),
                            ],
                        ]"
                    />

                    {{-- WHILE with IF --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-while-if.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-181rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-186rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-while-if.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-186rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-188.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('WHILE with IF')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Read the current item inside the WHILE body.'),
                            ],
                        ]"
                    />

                    {{-- WHILE with SWITCH --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-while-switch.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-186rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-191rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-while-switch.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-191rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-193.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('WHILE with SWITCH')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'Read one item per iteration and select its action with SWITCH: edit a draft, display a published item, or record an unknown status.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Nested WHILE --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-while-nested.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-191rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-196rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-while-nested.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-196rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-198.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Nested WHILE')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Initialize the group index once.'),
                            ],
                        ]"
                    />

                    {{-- Two independent inner loops --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-while-independent.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-196rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-201rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-while-independent.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-201rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-203.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Two independent inner loops')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Each group first processes its items, then sends its notifications.'),
                            ],
                        ]"
                    />

                    {{-- Mixed sides and crossings --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-while-mixed.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-201rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-206rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-while-mixed.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-206rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-208.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Mixed sides and crossings')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Initialize the group index once.'),
                            ],
                        ]"
                    />

                    {{-- Action → Nested WHILE → Action --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-while-action-sequence.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '8.25rem', 'y' => '-206rem']"
                        :anchor-end="['x' => '8.25rem', 'y' => '-211rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-while-action-sequence.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '8.25rem', 'y' => '-211rem'],
                            'anchorEnd' => ['x' => '11rem', 'y' => '-213.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Action → Nested WHILE → Action')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Prepare each group before entering its item loop.'),
                            ],
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.column-2.column',
                            'startAnchor' => 'w',
                            'endAnchor' => 's',
                            'anchorStart' => ['x' => '40.75rem', 'y' => '-6rem'],
                            'anchorEnd' => ['x' => '43.5rem', 'y' => '-8.75rem'],
                            'color' => 'zinc',
                        ]"
                    />

                    {{-- Flow FOR --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-for.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '43.5rem', 'y' => '-8.75rem']"
                        :anchor-end="['x' => '43.5rem', 'y' => '-11rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-for.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '43.5rem', 'y' => '-11rem'],
                            'anchorEnd' => ['x' => '46.25rem', 'y' => '-13.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow FOR')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Loops with initialization, condition and increment.'),
                            ],
                        ]"
                    />

                    {{-- FOR basic --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-for-basic.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-13.75rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-16rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-for-basic.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-16rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-18.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('FOR basic')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Load the items once.'),
                            ],
                        ]"
                    />

                    {{-- FOR descending --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-for-descending.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-16rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-21rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-for-descending.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-21rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-23.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('FOR descending')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Load the items once.'),
                            ],
                        ]"
                    />

                    {{-- FOR multiple actions --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-for-multiple-actions.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-21rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-26rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-for-multiple-actions.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-26rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-28.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('FOR multiple actions')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Load the items once and initialize index to zero.'),
                            ],
                        ]"
                    />

                    {{-- FOR with IF --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-for-if.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-26rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-31rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-for-if.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-31rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-33.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('FOR with IF')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Load the items once and initialize index to zero.'),
                            ],
                        ]"
                    />

                    {{-- FOR with SWITCH --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-for-switch.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-31rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-36rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-for-switch.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-36rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-38.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('FOR with SWITCH')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Load the items once and initialize index to zero.'),
                            ],
                        ]"
                    />

                    {{-- Nested FOR --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-for-nested.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-36rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-41rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-for-nested.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-41rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-43.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Nested FOR')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Initialize groupIndex once.'),
                            ],
                        ]"
                    />

                    {{-- Two independent inner loops --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-for-independent.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-41rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-46rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-for-independent.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-46rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-48.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Two independent inner loops')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Each group first processes its items, then sends its notifications.'),
                            ],
                        ]"
                    />

                    {{-- Mixed sides and crossings --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-for-mixed.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-46rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-51rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-for-mixed.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-51rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-53.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Mixed sides and crossings')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Initialize the group index once.'),
                            ],
                        ]"
                    />

                    {{-- Action → Nested FOR → Action --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-for-action-sequence.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-51rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-56rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-for-action-sequence.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-56rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-58.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Action → Nested FOR → Action')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'Prepare each group before its inner FOR and finalize it after the inner FALSE exit.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Flow FOREACH --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-foreach.stem"
                        direction="top-bottom"
                        length="50rem"
                        :anchor-start="['x' => '43.5rem', 'y' => '-11rem']"
                        :anchor-end="['x' => '43.5rem', 'y' => '-61rem']"
                        color="indigo"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-foreach.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '43.5rem', 'y' => '-61rem'],
                            'anchorEnd' => ['x' => '46.25rem', 'y' => '-63.75rem'],
                            'color' => 'indigo',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow FOREACH')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Iteration over collection elements or key/value pairs.'),
                            ],
                        ]"
                    />

                    {{-- Collection --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-foreach-collection.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-63.75rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-66rem']"
                        color="indigo"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-foreach-collection.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-66rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-68.75rem'],
                            'color' => 'indigo',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Collection')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'FOREACH prepares iteration once, then obtains the next item before each body execution.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Key / Value --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-foreach-key-value.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-66rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-71rem']"
                        color="indigo"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-foreach-key-value.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-71rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-73.75rem'],
                            'color' => 'indigo',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Key / Value')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Each iteration provides a key and its associated value.'),
                            ],
                        ]"
                    />

                    {{-- Nested FOREACH --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-foreach-nested.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-71rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-76rem']"
                        color="indigo"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-foreach-nested.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-76rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-78.75rem'],
                            'color' => 'indigo',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Nested FOREACH')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'The outer iteration selects one group and opens a fresh item iteration for that group.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Flow DO WHILE --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-do-while.stem"
                        direction="top-bottom"
                        length="20rem"
                        :anchor-start="['x' => '43.5rem', 'y' => '-61rem']"
                        :anchor-end="['x' => '43.5rem', 'y' => '-81rem']"
                        color="emerald"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-do-while.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '43.5rem', 'y' => '-81rem'],
                            'anchorEnd' => ['x' => '46.25rem', 'y' => '-83.75rem'],
                            'color' => 'emerald',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow DO WHILE')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Loops that check their condition after executing the body.'),
                            ],
                        ]"
                    />

                    {{-- DO WHILE basic --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-do-while-basic.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-83.75rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-86rem']"
                        color="emerald"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-do-while-basic.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-86rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-88.75rem'],
                            'color' => 'emerald',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('DO WHILE basic')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('DO WHILE executes the action before checking its condition.'),
                            ],
                        ]"
                    />

                    {{-- Multiple actions --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-do-while-multiple-actions.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-86rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-91rem']"
                        color="emerald"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-do-while-multiple-actions.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-91rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-93.75rem'],
                            'color' => 'emerald',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Multiple actions')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Both body actions run in order before the condition is checked.'),
                            ],
                        ]"
                    />

                    {{-- Bounded retry --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-do-while-bounded-retry.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-91rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-96rem']"
                        color="emerald"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-do-while-bounded-retry.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-96rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-98.75rem'],
                            'color' => 'emerald',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Bounded retry')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Start with attempt = 0 and maxAttempts = 3.'),
                            ],
                        ]"
                    />

                    {{-- Flow TRY/CATCH/FINALLY --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-try-catch.stem"
                        direction="top-bottom"
                        length="20rem"
                        :anchor-start="['x' => '43.5rem', 'y' => '-81rem']"
                        :anchor-end="['x' => '43.5rem', 'y' => '-101rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-try-catch.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '43.5rem', 'y' => '-101rem'],
                            'anchorEnd' => ['x' => '46.25rem', 'y' => '-103.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow TRY/CATCH/FINALLY')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Exception handling and cleanup, also within conditions and loops.'),
                            ],
                        ]"
                    />

                    {{-- TRY / CATCH --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-try-catch-basic.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-103.75rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-106rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-try-catch-basic.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-106rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-108.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('TRY / CATCH')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('TRY performs the operation.'),
                            ],
                        ]"
                    />

                    {{-- TRY / CATCH / FINALLY --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-try-catch-finally.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-106rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-111rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-try-catch-finally.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-111rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-113.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('TRY / CATCH / FINALLY')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'Normal completion and a handled exception both reach FINALLY, which performs cleanup once before continuation.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Multiple CATCH clauses --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-try-catch-multiple.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-111rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-116rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-try-catch-multiple.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-116rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-118.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Multiple CATCH clauses')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Normal completion records success.'),
                            ],
                        ]"
                    />

                    {{-- IF → TRY/CATCH --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-try-catch-if-try.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-116rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-121rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-try-catch-if-try.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-121rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-123.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('IF → TRY/CATCH')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Only the enabled IF branch prepares and performs the protected operation.'),
                            ],
                        ]"
                    />

                    {{-- TRY → IF/ELSE → FINALLY --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-try-catch-try-if-finally.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-121rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-126rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-try-catch-try-if-finally.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-126rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-128.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('TRY → IF/ELSE → FINALLY')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The boolean IF and its selected action belong to TRY.'),
                            ],
                        ]"
                    />

                    {{-- FOREACH → TRY/CATCH --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-try-catch-foreach-try.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-126rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-131rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-try-catch-foreach-try.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-131rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-133.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('FOREACH → TRY/CATCH')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('TRY is inside FOREACH.'),
                            ],
                        ]"
                    />

                    {{-- TRY → FOREACH → CATCH --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-try-catch-try-foreach.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-131rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-136rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-try-catch-try-foreach.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-136rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-138.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('TRY → FOREACH → CATCH')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('TRY surrounds the entire FOREACH.'),
                            ],
                        ]"
                    />

                    {{-- WHILE → TRY/CATCH/FINALLY --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-try-catch-while-try-finally.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-136rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-141rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-try-catch-while-try-finally.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-141rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-143.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('WHILE → TRY/CATCH/FINALLY')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'Each WHILE iteration reads one queue item, performs TRY/CATCH and then runs FINALLY exactly once.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Flow BREAK / CONTINUE --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-break-continue.stem"
                        direction="top-bottom"
                        length="45rem"
                        :anchor-start="['x' => '43.5rem', 'y' => '-101rem']"
                        :anchor-end="['x' => '43.5rem', 'y' => '-146rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-break-continue.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '43.5rem', 'y' => '-146rem'],
                            'anchorEnd' => ['x' => '46.25rem', 'y' => '-148.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow BREAK / CONTINUE')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Exit a loop or proceed to its next iteration.'),
                            ],
                        ]"
                    />

                    {{-- BREAK in WHILE --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-break-while.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-148.75rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-151rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-break-while.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-151rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-153.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('BREAK in WHILE')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('WHILE checks for another item before each iteration.'),
                            ],
                        ]"
                    />

                    {{-- CONTINUE in WHILE --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-continue-while.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-151rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-156rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-continue-while.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-156rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-158.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CONTINUE in WHILE')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('WHILE checks for another item before each iteration.'),
                            ],
                        ]"
                    />

                    {{-- CONTINUE in FOR --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-continue-for.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-156rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-161rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-continue-for.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-161rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-163.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CONTINUE in FOR')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('FOR initializes the index once.'),
                            ],
                        ]"
                    />

                    {{-- CONTINUE in nested WHILE --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-continue-nested.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-161rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-166rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-continue-nested.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-166rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-168.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CONTINUE in nested WHILE')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The outer WHILE selects a group and resets itemIndex.'),
                            ],
                        ]"
                    />

                    {{-- BREAK in nested WHILE --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-break-nested.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-166rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-171rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-break-nested.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-171rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-173.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('BREAK in nested WHILE')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The outer WHILE selects a group.'),
                            ],
                        ]"
                    />

                    {{-- BREAK with FINALLY (1) --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-break-finally.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-171rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-176rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-break-finally.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-176rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-178.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('BREAK with FINALLY (1)')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Each item is handled inside TRY.'),
                            ],
                        ]"
                    />

                    {{-- BREAK with FINALLY (2) --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-break-finally-2.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-176rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-181rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-break-finally-2.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-181rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-183.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('BREAK with FINALLY (2)')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Each item is handled inside TRY.'),
                            ],
                        ]"
                    />

                    {{-- CONTINUE with FINALLY (1) --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-continue-finally.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-181rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-186rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-continue-finally.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-186rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-188.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CONTINUE with FINALLY (1)')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The body reads an item and advances the index before entering TRY.'),
                            ],
                        ]"
                    />

                    {{-- CONTINUE with FINALLY (2) --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-continue-finally-2.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '46.25rem', 'y' => '-186rem']"
                        :anchor-end="['x' => '46.25rem', 'y' => '-191rem']"
                        color="rose"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-continue-finally-2.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '46.25rem', 'y' => '-191rem'],
                            'anchorEnd' => ['x' => '49rem', 'y' => '-193.75rem'],
                            'color' => 'rose',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CONTINUE with FINALLY (2)')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The body reads an item and advances the index before TRY.'),
                            ],
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.column-3.column',
                            'startAnchor' => 'w',
                            'endAnchor' => 's',
                            'anchorStart' => ['x' => '78.75rem', 'y' => '-6rem'],
                            'anchorEnd' => ['x' => '81.5rem', 'y' => '-8.75rem'],
                            'color' => 'zinc',
                        ]"
                    />

                    {{-- Flow RETURN --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-return.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '81.5rem', 'y' => '-8.75rem']"
                        :anchor-end="['x' => '81.5rem', 'y' => '-11rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-return.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '81.5rem', 'y' => '-11rem'],
                            'anchorEnd' => ['x' => '84.25rem', 'y' => '-13.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow RETURN')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Leave a function, optionally returning a value.'),
                            ],
                        ]"
                    />

                    {{-- Guard clause --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-return-guard.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-13.75rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-16rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-return-guard.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-16rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-18.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Guard clause')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('RETURN ends the current function and supplies a value to its caller.'),
                            ],
                        ]"
                    />

                    {{-- Multiple guards --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-return-multiple-guards.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-16rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-21rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-return-multiple-guards.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-21rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-23.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Multiple guards')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The function checks two guards in sequence.'),
                            ],
                        ]"
                    />

                    {{-- RETURN from a loop --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-return-loop.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-21rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-26rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-return-loop.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-26rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-28.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('RETURN from a loop')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('findFirstPositive(values) scans the input in order.'),
                            ],
                        ]"
                    />

                    {{-- RETURN from nested loops --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-return-nested.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-26rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-31rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-return-nested.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-31rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-33.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('RETURN from nested loops')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('firstPositive(groups) scans each group and its items.'),
                            ],
                        ]"
                    />

                    {{-- RETURN with FINALLY --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-return-finally.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-31rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-36rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-return-finally.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-36rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-38.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('RETURN with FINALLY')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('doublePositive(value) evaluates its return expression inside TRY.'),
                            ],
                        ]"
                    />

                    {{-- RETURN without a value --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-return-void.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-36rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-41rem']"
                        color="violet"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-return-void.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-41rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-43.75rem'],
                            'color' => 'violet',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('RETURN without a value')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'notifyIfEnabled(enabled) is a procedure: it performs an action but supplies no result expression.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Flow THROW / RETHROW --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-throw.stem"
                        direction="top-bottom"
                        length="35rem"
                        :anchor-start="['x' => '81.5rem', 'y' => '-11rem']"
                        :anchor-end="['x' => '81.5rem', 'y' => '-46rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-throw.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '81.5rem', 'y' => '-46rem'],
                            'anchorEnd' => ['x' => '84.25rem', 'y' => '-48.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow THROW / RETHROW')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Raise, propagate or rethrow an exception.'),
                            ],
                        ]"
                    />

                    {{-- THROW to matching CATCH --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-throw-catch.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-48.75rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-51rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-throw-catch.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-51rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-53.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('THROW to matching CATCH')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'An explicit THROW interrupts the TRY body and transfers control to a matching CATCH.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Propagation to outer CATCH --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-throw-propagation.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-51rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-56rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-throw-propagation.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-56rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-58.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Propagation to outer CATCH')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The outer TRY calls validateInput().'),
                            ],
                        ]"
                    />

                    {{-- RETHROW after logging --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-throw-rethrow.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-56rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-61rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-throw-rethrow.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-61rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-63.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('RETHROW after logging')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The outer TRY calls validateAndLog().'),
                            ],
                        ]"
                    />

                    {{-- FINALLY during propagation --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-throw-finally.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-61rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-66rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-throw-finally.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-66rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-68.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('FINALLY during propagation')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The outer TRY calls validateAndCleanup().'),
                            ],
                        ]"
                    />

                    {{-- THROW in FINALLY replaces RETURN --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-throw-finally-return.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-66rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-71rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-throw-finally-return.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-71rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-73.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('THROW in FINALLY replaces RETURN')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('computeAndCleanup() evaluates RETURN 42 inside TRY.'),
                            ],
                        ]"
                    />

                    {{-- THROW in FINALLY replaces an exception --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-throw-finally-exception.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-71rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-76rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-throw-finally-exception.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-76rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-78.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('THROW in FINALLY replaces an exception')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('failAndCleanup() throws InvalidArgumentException inside TRY.'),
                            ],
                        ]"
                    />

                    {{-- RETURN inside FINALLY --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-throw-return-finally.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-76rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-81rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-throw-return-finally.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-81rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-83.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('RETURN inside FINALLY')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'failAndReturn() throws inside TRY, but FINALLY returns 7 before the exception reaches the caller.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Unhandled exception --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-throw-unhandled.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-81rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-86rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-throw-unhandled.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-86rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-88.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Unhandled exception')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('failWithCleanup() throws inside TRY and has no CATCH.'),
                            ],
                        ]"
                    />

                    {{-- THROW inside FOREACH --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-throw-foreach.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-86rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-91rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-throw-foreach.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-91rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-93.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('THROW inside FOREACH')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('One TRY surrounds the whole FOREACH.'),
                            ],
                        ]"
                    />

                    {{-- CATCH inside FOREACH --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-throw-foreach-catch.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-91rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-96rem']"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-throw-foreach-catch.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-96rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-98.75rem'],
                            'color' => 'sky',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('CATCH inside FOREACH')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Each FOREACH iteration has its own TRY/CATCH.'),
                            ],
                        ]"
                    />

                    {{-- Flow FUNCTION --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-function.stem"
                        direction="top-bottom"
                        length="55rem"
                        :anchor-start="['x' => '81.5rem', 'y' => '-46rem']"
                        :anchor-end="['x' => '81.5rem', 'y' => '-101rem']"
                        color="indigo"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-function.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '81.5rem', 'y' => '-101rem'],
                            'anchorEnd' => ['x' => '84.25rem', 'y' => '-103.75rem'],
                            'color' => 'indigo',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow FUNCTION')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Function calls, arguments, return values and recursion.'),
                            ],
                        ]"
                    />

                    {{-- Function call and return value --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-function-basic.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-103.75rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-106rem']"
                        color="indigo"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-function-basic.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-106rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-108.75rem'],
                            'color' => 'indigo',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Function call and return value')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The caller prepares 41 and calls addOne(value).'),
                            ],
                        ]"
                    />

                    {{-- Arguments and local variables --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-function-arguments.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-106rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-111rem']"
                        color="indigo"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-function-arguments.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-111rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-113.75rem'],
                            'color' => 'indigo',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Arguments and local variables')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The caller supplies value = 20 and factor = 2 to adjust(value, factor).'),
                            ],
                        ]"
                    />

                    {{-- Function without return value --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-function-void.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-111rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-116rem']"
                        color="indigo"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-function-void.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-116rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-118.75rem'],
                            'color' => 'indigo',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Function without return value')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The caller displays before and calls logMessage(message).'),
                            ],
                        ]"
                    />

                    {{-- Nested function calls --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-function-nested.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-116rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-121rem']"
                        color="indigo"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-function-nested.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-121rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-123.75rem'],
                            'color' => 'indigo',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Nested function calls')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The caller invokes doubleAdjusted(20).'),
                            ],
                        ]"
                    />

                    {{-- Multiple call sites --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-function-multiple.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-121rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-126rem']"
                        color="indigo"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-function-multiple.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-126rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-128.75rem'],
                            'color' => 'indigo',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Multiple call sites')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'The caller invokes the same addOne(value) function at two separate call sites: first with 10, then with 40.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Recursion with a base case --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-function-recursion.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-126rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-131rem']"
                        color="indigo"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-function-recursion.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-131rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-133.75rem'],
                            'color' => 'indigo',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Recursion with a base case')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'A concrete execution of factorial(2) creates three separate call frames for n = 2, n = 1 and n = 0.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Flow CALLBACK --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-callback.stem"
                        direction="top-bottom"
                        length="35rem"
                        :anchor-start="['x' => '81.5rem', 'y' => '-101rem']"
                        :anchor-end="['x' => '81.5rem', 'y' => '-136rem']"
                        color="emerald"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-callback.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '81.5rem', 'y' => '-136rem'],
                            'anchorEnd' => ['x' => '84.25rem', 'y' => '-138.75rem'],
                            'color' => 'emerald',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow CALLBACK')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Pass a function to be invoked by another operation.'),
                            ],
                        ]"
                    />

                    {{-- Pass and invoke a callback --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-callback-basic.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-138.75rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-141rem']"
                        color="emerald"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-callback-basic.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-141rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-143.75rem'],
                            'color' => 'emerald',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Pass and invoke a callback')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'The caller passes addOne as a callable together with value 41 to apply(value, callback).',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Interchangeable callbacks --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-callback-interchangeable.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-141rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-146rem']"
                        color="emerald"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-callback-interchangeable.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-146rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-148.75rem'],
                            'color' => 'emerald',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Interchangeable callbacks')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The caller invokes apply twice with the same value 20.'),
                            ],
                        ]"
                    />

                    {{-- Callback inside a loop --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-callback-loop.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-146rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-151rem']"
                        color="emerald"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-callback-loop.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-151rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-153.75rem'],
                            'color' => 'emerald',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Callback inside a loop')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('The caller supplies items [10, 20, 30] and the addOne callback.'),
                            ],
                        ]"
                    />

                    {{-- Flow ASYNC/AWAIT --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await.stem"
                        direction="top-bottom"
                        length="20rem"
                        :anchor-start="['x' => '81.5rem', 'y' => '-136rem']"
                        :anchor-end="['x' => '81.5rem', 'y' => '-156rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '81.5rem', 'y' => '-156rem'],
                            'anchorEnd' => ['x' => '84.25rem', 'y' => '-158.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Flow ASYNC/AWAIT')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Asynchronous operations, concurrency, cancellation and retries.'),
                            ],
                        ]"
                    />

                    {{-- Await one operation --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-basic.stem"
                        direction="top-bottom"
                        length="2.25rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-158.75rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-161rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-basic.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-161rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-163.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Await one operation')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Start an asynchronous operation and keep its pending handle.'),
                            ],
                        ]"
                    />

                    {{-- Sequential awaits --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-sequential.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-161rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-166rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-sequential.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-166rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-168.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Sequential awaits')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Start loadValueAsync() and AWAIT its result 20.'),
                            ],
                        ]"
                    />

                    {{-- Concurrent operations --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-concurrent.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-166rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-171rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-concurrent.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-171rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-173.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Concurrent operations')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Start independent operations A and B before the shared AWAIT ALL.'),
                            ],
                        ]"
                    />

                    {{-- Await with TRY/CATCH/FINALLY --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-finally.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-171rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-176rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-finally.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-176rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-178.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Await with TRY/CATCH/FINALLY')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Inside TRY, start loadValueAsync() and await the pending operation.'),
                            ],
                        ]"
                    />

                    {{-- Await inside a loop --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-loop.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-176rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-181rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-loop.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-181rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-183.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Await inside a loop')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Process items [10, 20, 30] sequentially.'),
                            ],
                        ]"
                    />

                    {{-- Cancellation --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-cancellation.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-181rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-186rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-cancellation.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-186rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-188.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Cancellation')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Inside TRY, start an operation with a cancellation signal and AWAIT it.'),
                            ],
                        ]"
                    />

                    {{-- Timeout --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-timeout.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-186rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-191rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-timeout.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-191rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-193.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Timeout')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'Arm a 20 ms timeout and start a cancellation-aware operation that normally completes after 100 ms.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Partial success --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-partial.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-191rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-196rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-partial.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-196rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-198.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Partial success')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Start operations A and B before AWAIT ALL SETTLED.'),
                            ],
                        ]"
                    />

                    {{-- First completion / First success --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-first.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-196rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-201rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-first.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-201rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-203.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('First completion / First success')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'Compare the same scenario with two selection rules: both operations start, B fails first, and A succeeds later with 20.',
                                            ),
                            ],
                        ]"
                    />

                    {{-- Limited concurrency --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-limited.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-201rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-206rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-limited.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-206rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-208.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Limited concurrency')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Run A, B and C with two worker slots.'),
                            ],
                        ]"
                    />

                    {{-- Retry with delay --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-retry.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-206rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-211rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-retry.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-211rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-213.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Retry with delay')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Attempt an operation at most three times, including the initial attempt.'),
                            ],
                        ]"
                    />

                    {{-- Failure → Cancel remaining → Cleanup --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-group-cleanup.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-211rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-216rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-group-cleanup.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-216rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-218.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Failure → Cancel remaining → Cleanup')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Start A and B as one task group.'),
                            ],
                        ]"
                    />

                    {{-- Async iteration / Stream --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-stream.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-216rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-221rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-stream.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-221rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-223.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Async iteration / Stream')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __('Consume an asynchronous source containing 10, 20 and 30.'),
                            ],
                        ]"
                    />

                    {{-- Retry with total deadline --}}

                    <x-translation-workbench::ui.tw-graph.segments.path
                        id="literature.overview.flow.flow-async-await-retry-deadline.stem"
                        direction="top-bottom"
                        length="5rem"
                        :anchor-start="['x' => '84.25rem', 'y' => '-221rem']"
                        :anchor-end="['x' => '84.25rem', 'y' => '-226rem']"
                        color="amber"
                    />

                    <x-translation-workbench::ui.tw-graph.segments.arc
                        :segment="[
                            'id' => 'literature.overview.flow.flow-async-await-retry-deadline.arc',
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'anchorStart' => ['x' => '84.25rem', 'y' => '-226rem'],
                            'anchorEnd' => ['x' => '87rem', 'y' => '-228.75rem'],
                            'color' => 'amber',
                            'nodeEnd' => true,
                            'endLabel' => [
                                'text' => [__('Retry with total deadline')],
                                'side' => 'right',
                                'width' => 'halfLong',
                                'align' => 'left',
                                'connectorLength' => '1rem',
                                'tooltip' => __(
                                                'Use one 50ms deadline for the whole retry operation, with at most three attempts and a 20ms delay.',
                                            ),
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- overview-flow-example:end --}}
            </div>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <flux:accordion class="mt-4">
        <flux:accordion.item>
            <flux:callout
                color="indigo"
                icon="code"
            >
                <flux:accordion.heading>{{ __('TW-Graph code example') }}</flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.overview-flow')->example('overview-flow-example') }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/overview/overview-flow.blade.php"
        segments="3"
    />
</flux:callout>
