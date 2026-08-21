{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/tw-graph-authoring.blade.php --}}

<flux:callout
    class="mt-4"
    color="zinc"
    icon="git-branch-plus"
>
    <flux:callout.heading>
        {{ __('TW Graph authoring layer') }}
    </flux:callout.heading>

    <flux:callout.text>
        {{ __('The new package-local component family lives under resources/views/components/ui/tw-graph. The existing tw-graph-protocol renderer remains frozen as the reference implementation while the authoring API is rebuilt step by step.') }}
    </flux:callout.text>

    <div class="mt-3 grid gap-3 text-sm md:grid-cols-3">
        <flux:callout
            color="sky"
            icon="file-code"
        >
            <flux:callout.heading>{{ __('Component chain') }}</flux:callout.heading>
            <flux:callout.text>
                <span class="font-mono text-xs">
                    {{ __('tw-graph->strang.*->paths.*->segments.*->primitives.*') }}
                </span>
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="green"
            icon="folder-tree"
        >
            <flux:callout.heading>{{ __('Package boundary') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Graph components are kept in the translation-workbench package, not in the app component tree.') }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="fuchsia"
            icon="database"
        >
            <flux:callout.heading>{{ __('Geometry cache') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Component props define intent; the JSON protocol stores calculated coordinates and is never edited manually.') }}
            </flux:callout.text>
        </flux:callout>
    </div>

    @php
        $twGraphAuthoringGraphId = 'tw-graph-authoring-sandbox';
        $twGraphAuthoringDefaults = [
            'color' => 'zinc',
            'lineLength' => '4rem',
            'lineWidth' => '0.25rem',
            'nodeSize' => '0.95rem',
            'arcSize' => '2.75rem',
            'capLength' => '1.75rem',
            'bridgeLength' => 'lineLength',
            'stemHeight' => 'lineLength',
            'connectorLength' => '2rem',
            'connectorGap' => '0.25rem',
            'pathCount' => '10',
        ];
    @endphp

    <flux:callout
        class="mt-4 w-full"
        color="zinc"
        icon="git-branch"
        x-data="{ twGraphAuthoringDev: true }"
    >
        <flux:callout.heading>
            <span class="flex w-full items-center justify-between gap-3">
                <span>{{ __('TW Graph sandbox') }}</span>
                <flux:field
                    class="items-center gap-2"
                    variant="inline"
                    x-on:click.stop
                >
                    <flux:switch
                        class="switch-colored hover:cursor-pointer"
                        x-model="twGraphAuthoringDev"
                    />
                    <flux:label class="text-xs opacity-70 hover:cursor-pointer">
                        {{ __('DEV') }}
                    </flux:label>
                </flux:field>
            </span>
        </flux:callout.heading>

        <flux:callout.text>
            {{ __('This section starts with an empty tw-graph authoring container. The protocol will later be generated from the component tree and shown for debug/review, but it is not used as the render source.') }}
        </flux:callout.text>

        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
            <flux:badge color="zinc">
                {{ __('Graph ID') }}: {{ $twGraphAuthoringGraphId }}
            </flux:badge>
            <flux:badge color="amber">
                {{ __('Protocol') }}: {{ __('component generated later') }}
            </flux:badge>
            <flux:badge color="zinc">
                {{ __('Render source') }}: {{ __('component tree') }}
            </flux:badge>
        </div>

        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
            @foreach ($twGraphAuthoringDefaults as $defaultName => $defaultValue)
                <flux:badge color="zinc">
                    {{ $defaultName }}={{ $defaultValue }}
                </flux:badge>
            @endforeach
        </div>

        <div
            class="mt-4 overflow-x-auto overflow-y-visible pb-10"
            x-bind:class="{ 'tw-graph-protocol-dev-disabled': !twGraphAuthoringDev }"
        >
            <x-translation-workbench::ui.tw-graph
                class="mb-8 mt-8 rounded-lg border border-zinc-300 pb-10 dark:border-zinc-700"
                :graph-id="$twGraphAuthoringGraphId"
                :dev="true"
            >
                {{-- strang.trunk --}}
                <x-translation-workbench::ui.tw-graph.strang.trunk
                    color="sky"
                    :path-count="11"
                    :start-label="['text' => ['Trunk', 'start']]"
                    :end-label="['text' => ['Trunk', 'end']]"
                    :pathLengths="[
                        7 => '4rem',
                        9 => '4rem',
                        11 => '5rem',
                    ]"
                    :node-labels="[
                        3 => ['left' => 'left node', 'right' => 'right node'],
                        8 => ['right' => 'review point'],
                    ]"
                />
                {{-- strang.merge-left --}}
                <x-translation-workbench::ui.tw-graph.strang.merge-left
                    color="amber"
                    attach-to="strang.trunk.node.3"
                    bridge-length="11rem"
                    :node-labels="[
                        1 => [
                            'right' => ['merge', 'left start'],
                            'connectorLength' => '4.5rem',
                        ],
                        5 => ['left' => ['trunk attach', 'strang.merge-left']],
                    ]"
                    {{-- strang.merge-left Extensions --}}
                    :extension-count="2"
                    :extension-bridge-lengths="[
                        1 => '12rem',
                        2 => '14rem',
                    ]"
                    :extension-stem-heights="[
                        1 => '5rem',
                        2 => '3.5rem',
                    ]"
                    :extension-node-labels="[
                        1 => [
                            1 => ['right' => ['left extension', 'start']],
                            4 => ['top' => ['left extension', 'end']],
                        ],
                        2 => [
                            1 => ['right' => ['left extension 2', 'start']],
                            4 => ['top' => ['left extension 2', 'end']],
                        ],
                    ]"
                >
                </x-translation-workbench::ui.tw-graph.strang.merge-left>
                {{-- merge-right --}}
                <x-translation-workbench::ui.tw-graph.strang.merge-right
                    color="green"
                    attach-to="strang.trunk.node.3"
                    bridge-length="10rem"
                    stem-height="2rem"
                    :node-labels="[
                        1 => ['left' => 'merge right start'],
                        5 => ['right' => ['trunk', 'attach']],
                    ]"
                    {{-- strang.merge-right Extensions --}}
                    :extension-count="2"
                    :extension-bridge-lengths="[
                        1 => '12rem',
                        2 => '14rem',
                    ]"
                    :extension-stem-heights="[
                        2 => '3.5rem',
                    ]"
                    :extension-node-labels="[
                        1 => [
                            1 => ['left' => ['right extension', 'start']],
                            4 => ['top' => ['right extension', 'end']],
                        ],
                        2 => [
                            1 => ['left' => ['right extension 2', 'start']],
                            4 => ['top' => ['right extension 2', 'end']],
                        ],
                    ]"
                >
                </x-translation-workbench::ui.tw-graph.strang.merge-right>
                {{-- strang.branch-left --}}
                <x-translation-workbench::ui.tw-graph.strang.branch-left
                    color="fuchsia"
                    attach-to="strang.trunk.node.5"
                    bridge-length="8rem"
                    :node-labels="[
                        3 => ['left' => ['branch left', 'node 3']],
                    ]"
                    :continuation-stem="[
                        1 => ['4rem', 'left' => 'branch left', 'continuation'],
                        2 => ['3rem'],
                        3 => ['3rem'],
                        4 => ['3rem'],
                        5 => ['3rem'],
                        6 => ['5rem', 'left' => ['Left label', '2te Zeile'], 'right' => 'Right label'],
                    ]"
                />
                {{-- strang.branch-right --}}
                <x-translation-workbench::ui.tw-graph.strang.branch-right
                    color="violet"
                    attach-to="strang.trunk.node.6"
                    bridge-length="8rem"
                    :node-labels="[
                        3 => ['right' => ['branch right', 'node 3']],
                    ]"
                    :continuation-stem="[
                        1 => ['4rem'],
                        2 => ['3rem'],
                    ]"
                />
            </x-translation-workbench::ui.tw-graph>
        </div>
    </flux:callout>
</flux:callout>
