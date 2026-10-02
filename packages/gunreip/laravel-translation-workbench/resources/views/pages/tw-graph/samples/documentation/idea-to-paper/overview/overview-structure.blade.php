<flux:callout
    class="min-w-0"
    color="emerald"
>
    <flux:callout.heading icon="eye">{{ __('Main tabs') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">
        {{ __('Idea to Paper starts at the top. Merge paths and their extensions connect the main tabs on the left and right. Inventory and Overview end here; other tabs remain open. Strang and Parts branch below Deep Reference as sibling sub-tabs.') }}
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
                {{-- overview-structure-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-overview-structure"
                    :dev="true"
                    :coordinates="true"
                    min-width="48rem"
                    min-height="64rem"
                    horizontal-padding="3rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.overview.start"
                        direction="top-bottom"
                        length="4rem"
                        color="zinc"
                        :start-label="[
                            'text' => [__('Idea to Paper')],
                            'side' => 'bottom',
                            'align' => 'center',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.merge-left
                        id="literature.overview.tabs.left"
                        attach-to="literature.overview.start.anchorNode-end"
                        color="sky"
                        start-length="0rem"
                        bridge-length="6rem"
                        :stem-lengths="[1 => '4rem']"
                        :node-labels="[
                            'start' => false,
                        ]"
                        :extension-count="6"
                        extension-start-length="0rem"
                        extension-bridge-length="8rem"
                        :extension-stem-lengths="[5 => '10rem', 6 => '5rem']"
                        :extension-bridge-continuations="[4 => '12rem', 5 => '10rem', 6 => '7rem']"
                        :extension-node-labels="[
                            1 => [
                                'start' => false,
                            ],
                            2 => [
                                'start' => false,
                            ],
                            3 => [
                                'start' => false,
                            ],
                            4 => [
                                'start' => false,
                            ],
                        ]"
                        :extension-end-labels="[
                            5 => [
                                'text' => [__('Overview')],
                                'width' => 'half',
                                'align' => 'center',
                                'side' => 'bottom',
                            ],
                            6 => [
                                'text' => [__('Inventory')],
                                'width' => 'half',
                                'align' => 'center',
                                'side' => 'bottom',
                            ],
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.merge-right
                        id="literature.overview.tabs.right"
                        attach-to="literature.overview.start.anchorNode-end"
                        color="emerald"
                        start-length="0rem"
                        bridge-length="6rem"
                        :stem-lengths="[1 => '4rem']"
                        :node-labels="[
                            'start' => false,
                        ]"
                        :extension-count="5"
                        extension-start-length="0rem"
                        extension-stem-length="4rem"
                        extension-bridge-length="8rem"
                        :extension-node-labels="[
                            1 => [
                                'start' => false,
                            ],
                            2 => [
                                'start' => false,
                            ],
                            3 => [
                                'start' => false,
                            ],
                            4 => [
                                'start' => false,
                            ],
                            5 => [
                                'start' => false,
                            ],
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.parts"
                        attach-to="strang.merge-left.node.1"
                        direction="top-bottom"
                        color="sky"
                        before-length="1rem"
                        after-length="1rem"
                        :step-caps="false"
                        :step-label="[
                            'text' => [__('Parts')],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.segments"
                        attach-to="strang.merge-left.extension.1.node.1"
                        direction="top-bottom"
                        color="sky"
                        before-length="1rem"
                        after-length="1rem"
                        :step-caps="false"
                        :step-label="[
                            'text' => [__('Segments')],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.primitives"
                        attach-to="strang.merge-left.extension.2.node.1"
                        direction="top-bottom"
                        color="sky"
                        before-length="1rem"
                        after-length="1rem"
                        :step-caps="false"
                        :step-label="[
                            'text' => [__('Primitives')],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.canvas"
                        attach-to="strang.merge-left.extension.3.node.1"
                        direction="top-bottom"
                        color="sky"
                        before-length="1rem"
                        after-length="1rem"
                        :step-caps="false"
                        :step-label="[
                            'text' => [__('Canvas')],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference"
                        attach-to="strang.merge-left.extension.4.node.1"
                        direction="top-bottom"
                        color="sky"
                        before-length="1rem"
                        after-length="1rem"
                        :step-caps="false"
                        :step-label="[
                            'text' => [__('Deep Reference')],
                            'width' => 'default',
                            'align' => 'center',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.paths"
                        attach-to="strang.merge-right.node.1"
                        direction="top-bottom"
                        color="emerald"
                        before-length="1rem"
                        after-length="1rem"
                        :step-caps="false"
                        :step-label="[
                            'text' => [__('Paths')],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.strang-trunk"
                        attach-to="strang.merge-right.extension.1.node.1"
                        direction="top-bottom"
                        color="emerald"
                        before-length="1rem"
                        after-length="1rem"
                        :step-caps="false"
                        :step-label="[
                            'text' => [__('Strang Trunk')],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.strang-merge"
                        attach-to="strang.merge-right.extension.2.node.1"
                        direction="top-bottom"
                        color="emerald"
                        before-length="1rem"
                        after-length="1rem"
                        :step-caps="false"
                        :step-label="[
                            'text' => [__('Strang Merge')],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.strang-branch"
                        attach-to="strang.merge-right.extension.3.node.1"
                        direction="top-bottom"
                        color="emerald"
                        before-length="1rem"
                        after-length="1rem"
                        :step-caps="false"
                        :step-label="[
                            'text' => [__('Strang Branch')],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.strang-rekey"
                        attach-to="strang.merge-right.extension.4.node.1"
                        direction="top-bottom"
                        color="emerald"
                        before-length="1rem"
                        after-length="1rem"
                        :step-caps="false"
                        :step-label="[
                            'text' => [__('Strang Rekey')],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.flow"
                        attach-to="strang.merge-right.extension.5.node.1"
                        direction="top-bottom"
                        color="emerald"
                        before-length="1rem"
                        after-length="1rem"
                        :step-caps="false"
                        :step-label="[
                            'text' => [__('Flow')],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang-stem"
                        attach-to="literature.overview.deep-reference.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="3rem"
                        label-gap="0rem"
                        after-length="0rem"
                        :step-caps="false"
                    />

                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.overview.deep-reference.strang-branch"
                        :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            'idea-to-paper-overview-structure',
                            'literature.overview.deep-reference.strang-stem.anchorNode-end',
                        )"
                        side="right"
                        direction="top-bottom"
                        bridge-length="0rem"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang"
                        attach-to="literature.overview.deep-reference.strang-branch.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="2rem"
                        after-length="2rem"
                        :step-caps="false"
                        :step-label="['text' => [__('Strang')], 'width' => 'half', 'align' => 'center']"
                    />

                    {{-- Strang / flow-switch-case --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.flow-switch-case"
                        attach-to="literature.overview.deep-reference.strang.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="0rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['flow-switch-case'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / flow-start --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.flow-start"
                        attach-to="literature.overview.deep-reference.strang.flow-switch-case.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['flow-start'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / flow-step --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.flow-step"
                        attach-to="literature.overview.deep-reference.strang.flow-start.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['flow-step'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / flow-if --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.flow-if"
                        attach-to="literature.overview.deep-reference.strang.flow-step.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['flow-if'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / flow-if-else --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.flow-if-else"
                        attach-to="literature.overview.deep-reference.strang.flow-if.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['flow-if-else'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / flow-if-elseif --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.flow-if-elseif"
                        attach-to="literature.overview.deep-reference.strang.flow-if-else.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['flow-if-elseif'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / flow-if-elseif-multi --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.flow-if-elseif-multi"
                        attach-to="literature.overview.deep-reference.strang.flow-if-elseif.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['flow-if-elseif-multi'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / flow-if-ternary --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.flow-if-ternary"
                        attach-to="literature.overview.deep-reference.strang.flow-if-elseif-multi.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['flow-if-ternary'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / trunk --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.trunk"
                        attach-to="literature.overview.deep-reference.strang.flow-if-ternary.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['trunk'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / merge-left --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.merge-left"
                        attach-to="literature.overview.deep-reference.strang.trunk.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['merge-left'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / merge-right --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.merge-right"
                        attach-to="literature.overview.deep-reference.strang.merge-left.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['merge-right'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / branch-left --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.branch-left"
                        attach-to="literature.overview.deep-reference.strang.merge-right.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['branch-left'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / branch-right --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.branch-right"
                        attach-to="literature.overview.deep-reference.strang.branch-left.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['branch-right'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / branch-end --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.branch-end"
                        attach-to="literature.overview.deep-reference.strang.branch-right.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['branch-end'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / rekey-source-left --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.rekey-source-left"
                        attach-to="literature.overview.deep-reference.strang.branch-end.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['rekey-source-left'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / rekey-source-right --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.rekey-source-right"
                        attach-to="literature.overview.deep-reference.strang.rekey-source-left.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['rekey-source-right'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / rekey-target-left --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.rekey-target-left"
                        attach-to="literature.overview.deep-reference.strang.rekey-source-right.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['rekey-target-left'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / rekey-target-right --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.rekey-target-right"
                        attach-to="literature.overview.deep-reference.strang.rekey-target-left.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['rekey-target-right'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / flow-while --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.strang.flow-while"
                        attach-to="literature.overview.deep-reference.strang.rekey-target-right.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'left' => [
                                'text' => ['flow-while'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />

                    {{-- Strang / unlabeled end --}}
                    <x-translation-workbench::ui.tw-graph.parts.end
                        id="literature.overview.deep-reference.strang.end-cap"
                        :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            'idea-to-paper-overview-structure',
                            'literature.overview.deep-reference.strang.flow-while.anchorNode-end',
                        )"
                        direction="top-bottom"
                        length="2rem"
                        cap-length="4rem"
                        color="sky"
                    />

                    {{-- Keep Parts below the expanded Strang sub-tree. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.parts-stem"
                        attach-to="literature.overview.deep-reference.strang-stem.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="70rem"
                        label-gap="0rem"
                        after-length="0rem"
                        :step-caps="false"
                    />

                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.overview.deep-reference.parts-branch"
                        :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            'idea-to-paper-overview-structure',
                            'literature.overview.deep-reference.parts-stem.anchorNode-end',
                        )"
                        side="right"
                        direction="top-bottom"
                        bridge-length="4rem"
                        color="sky"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.parts"
                        attach-to="literature.overview.deep-reference.parts-branch.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="2rem"
                        after-length="2rem"
                        :step-caps="false"
                        :step-label="['text' => [__('Parts')], 'width' => 'half', 'align' => 'center']"
                    />

                    {{-- Parts / start --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.parts.start"
                        attach-to="literature.overview.deep-reference.parts.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="0rem"
                        :step-caps="false"
                        :node-labels="[
                            'right' => [
                                'text' => ['start'],
                                'width' => 'half',
                                'align' => 'left',
                            ],
                        ]"
                    />

                    {{-- Parts / end --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.parts.end"
                        attach-to="literature.overview.deep-reference.parts.start.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'right' => [
                                'text' => ['end'],
                                'width' => 'half',
                                'align' => 'left',
                            ],
                        ]"
                    />

                    {{-- Parts / sideways --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.parts.sideways"
                        attach-to="literature.overview.deep-reference.parts.end.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'right' => [
                                'text' => ['sideways'],
                                'width' => 'half',
                                'align' => 'left',
                            ],
                        ]"
                    />

                    {{-- Parts / chain --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.parts.chain"
                        attach-to="literature.overview.deep-reference.parts.sideways.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'right' => [
                                'text' => ['chain'],
                                'width' => 'half',
                                'align' => 'left',
                            ],
                        ]"
                    />

                    {{-- Parts / fusion --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.parts.fusion"
                        attach-to="literature.overview.deep-reference.parts.chain.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'right' => [
                                'text' => ['fusion'],
                                'width' => 'half',
                                'align' => 'left',
                            ],
                        ]"
                    />

                    {{-- Parts / split --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.overview.deep-reference.parts.split"
                        attach-to="literature.overview.deep-reference.parts.fusion.anchorNode-end"
                        direction="top-bottom"
                        color="sky"
                        before-length="0rem"
                        label-gap="0rem"
                        after-length="3rem"
                        :step-caps="false"
                        :node-labels="[
                            'right' => [
                                'text' => ['split'],
                                'width' => 'half',
                                'align' => 'left',
                            ],
                        ]"
                    />

                    {{-- Parts / unlabeled end --}}
                    <x-translation-workbench::ui.tw-graph.parts.end
                        id="literature.overview.deep-reference.parts.end-cap"
                        :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            'idea-to-paper-overview-structure',
                            'literature.overview.deep-reference.parts.split.anchorNode-end',
                        )"
                        direction="top-bottom"
                        length="2rem"
                        cap-length="4rem"
                        color="sky"
                    />

                </x-translation-workbench::ui.tw-graph>
                {{-- overview-structure-example:end --}}
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
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.overview-structure')->example('overview-structure-example') }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/overview/overview-structure.blade.php"
        segments="3"
    />
</flux:callout>
