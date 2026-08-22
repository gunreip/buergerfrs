{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/graph-v2/path-catalog.blade.php --}}

@php
    $tab = '__tw_indent__';
    $tree = '|-- ';
    $treeNested = '|   |-- ';
    $pathRows = [
        [
            'name' => 'trunk bottom-top',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.trunk',
            'structure' => [
                'paths.trunk',
                $tree . 'calculates anchor chain',
                $tree . 'segments.start',
                $tree . 'segments.path[]',
                $treeNested . 'segments.path from pathLengths[0]',
                $treeNested . 'segments.path from pathLengths[1]',
                $tree . 'segments.end',
            ],
            'composition' => [
                'one trunk chain in direction bottom-top',
                'pathLengths[] decides how many segments.path are rendered',
                'each next anchorStart is previous anchorEnd',
            ],
            'props' => [
                'direction=bottom-top',
                $tab . 'anchorStart{x,y}',
                $tab . 'startLength',
                $tab . 'pathLengths[]',
                $tab . 'pathLengths[] item: "3rem"',
                $tab . 'pathLengths[] item: ["3rem", ["Label|left", null]]',
                $tab . 'endLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-trunk-bottom-top',
        ],
        [
            'name' => 'trunk left-right',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.trunk',
            'structure' => [
                'paths.trunk',
                $tree . 'calculates anchor chain',
                $tree . 'segments.start',
                $tree . 'segments.path[]',
                $treeNested . 'segments.path from pathLengths[0]',
                $treeNested . 'segments.path from pathLengths[1]',
                $tree . 'segments.end',
            ],
            'composition' => [
                'one trunk chain in direction left-right',
                'pathLengths[] decides how many segments.path are rendered',
                'each next anchorStart is previous anchorEnd',
            ],
            'props' => [
                'direction=left-right',
                $tab . 'anchorStart{x,y}',
                $tab . 'startLength',
                $tab . 'pathLengths[]',
                $tab . 'pathLengths[] item: "3rem"',
                $tab . 'pathLengths[] item: ["3rem", ["Label|top", null]]',
                $tab . 'endLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-trunk-left-right',
        ],
        [
            'name' => 'merge left',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.merge side="left"',
            'structure' => [
                'paths.merge.left',
                $tree . 'calculates anchor chain',
                $tree . 'segments.start',
                $tree . 'segments.path bottom-top',
                $tree . 'segments.arc west-north',
                $tree . 'segments.path left-right',
                $tree . 'segments.arc south-east',
            ],
            'composition' => [
                'one inbound merge chain on the left side',
                'final arc end anchor is the trunk attach point',
                'verticalLength controls the vertical path after start',
                'connectorLength controls the horizontal segment',
            ],
            'props' => [
                'side=left',
                $tab . 'anchorStart{x,y}',
                $tab . 'startLength',
                $tab . 'verticalLength',
                $tab . 'arcSize',
                $tab . 'connectorLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-merge-left',
        ],
        [
            'name' => 'merge right',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.merge side="right"',
            'structure' => [
                'paths.merge.right',
                $tree . 'calculates anchor chain',
                $tree . 'segments.start',
                $tree . 'segments.path bottom-top',
                $tree . 'segments.arc east-north',
                $tree . 'segments.path right-left',
                $tree . 'segments.arc south-west',
            ],
            'composition' => [
                'one inbound merge chain on the right side',
                'final arc end anchor is the trunk attach point',
                'verticalLength controls the vertical path after start',
                'connectorLength controls the horizontal segment',
            ],
            'props' => [
                'side=right',
                $tab . 'anchorStart{x,y}',
                $tab . 'startLength',
                $tab . 'verticalLength',
                $tab . 'arcSize',
                $tab . 'connectorLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-merge-right',
        ],
        [
            'name' => 'merge-extension left',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.merge-extension side="left"',
            'structure' => [
                'paths.merge-extension.left',
                $tree . 'calculates anchor chain',
                $tree . 'segments.start',
                $tree . 'segments.path bottom-top',
                $tree . 'segments.arc west-north',
                $tree . 'segments.path left-right',
            ],
            'composition' => [
                'one outward merge extension on the left side',
                'arc end anchor becomes horizontal path start',
                'connectorLength controls the horizontal extension',
            ],
            'props' => [
                'side=left',
                $tab . 'anchorStart{x,y}',
                $tab . 'startLength',
                $tab . 'verticalLength',
                $tab . 'arcSize',
                $tab . 'connectorLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-merge-extension-left',
        ],
        [
            'name' => 'merge-extension right',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.merge-extension side="right"',
            'structure' => [
                'paths.merge-extension.right',
                $tree . 'calculates anchor chain',
                $tree . 'segments.start',
                $tree . 'segments.path bottom-top',
                $tree . 'segments.arc east-north',
                $tree . 'segments.path right-left',
            ],
            'composition' => [
                'one outward merge extension on the right side',
                'arc end anchor becomes horizontal path start',
                'connectorLength controls the horizontal extension',
            ],
            'props' => [
                'side=right',
                $tab . 'anchorStart{x,y}',
                $tab . 'startLength',
                $tab . 'verticalLength',
                $tab . 'arcSize',
                $tab . 'connectorLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-merge-extension-right',
        ],
        [
            'name' => 'branch left',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.branch side="left"',
            'structure' => [
                'paths.branch.left',
                $tree . 'calculates anchor chain',
                $tree . 'segments.arc east-north',
                $tree . 'segments.path right-left',
                $tree . 'segments.arc south-west',
                $tree . 'segments.path bottom-top',
            ],
            'composition' => [
                'one outbound branch chain',
                'each next anchorStart is previous anchorEnd',
                'connectorLength controls the horizontal segment',
                'verticalLength controls the final vertical segment',
            ],
            'props' => [
                'side=left',
                $tab . 'anchorStart{x,y}',
                $tab . 'arcSize',
                $tab . 'connectorLength',
                $tab . 'verticalLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-branch-left',
        ],
        [
            'name' => 'branch right',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.branch side="right"',
            'structure' => [
                'paths.branch.right',
                $tree . 'calculates anchor chain',
                $tree . 'segments.arc west-north',
                $tree . 'segments.path left-right',
                $tree . 'segments.arc south-east',
                $tree . 'segments.path bottom-top',
            ],
            'composition' => [
                'one outbound branch chain on the right side',
                'each next anchorStart is previous anchorEnd',
                'connectorLength controls the horizontal segment',
                'verticalLength controls the final vertical segment',
            ],
            'props' => [
                'side=right',
                $tab . 'anchorStart{x,y}',
                $tab . 'arcSize',
                $tab . 'connectorLength',
                $tab . 'verticalLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-branch-right',
        ],
        [
            'name' => 'branch-extension left',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.branch-extension side="left"',
            'structure' => [
                'paths.branch-extension.left',
                $tree . 'calculates anchor chain',
                $tree . 'segments.path right-left',
                $tree . 'segments.arc south-west',
                $tree . 'segments.path bottom-top',
            ],
            'composition' => [
                'one outward branch extension on the left side',
                'each next anchorStart is previous anchorEnd',
                'connectorLength controls the horizontal segment',
                'verticalLength controls the final vertical segment',
            ],
            'props' => [
                'side=left',
                $tab . 'anchorStart{x,y}',
                $tab . 'arcSize',
                $tab . 'connectorLength',
                $tab . 'verticalLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-branch-extension-left',
        ],
        [
            'name' => 'branch-extension right',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.branch-extension side="right"',
            'structure' => [
                'paths.branch-extension.right',
                $tree . 'calculates anchor chain',
                $tree . 'segments.path left-right',
                $tree . 'segments.arc south-east',
                $tree . 'segments.path bottom-top',
            ],
            'composition' => [
                'one outward branch extension on the right side',
                'each next anchorStart is previous anchorEnd',
                'connectorLength controls the horizontal segment',
                'verticalLength controls the final vertical segment',
            ],
            'props' => [
                'side=right',
                $tab . 'anchorStart{x,y}',
                $tab . 'arcSize',
                $tab . 'connectorLength',
                $tab . 'verticalLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-branch-extension-right',
        ],
        [
            'name' => 'branch-return left',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.branch-return side="left"',
            'structure' => [
                'paths.branch-return.left',
                $tree . 'calculates anchor chain',
                $tree . 'segments.path bottom-top',
                $tree . 'segments.arc west-north',
                $tree . 'segments.path left-right',
                $tree . 'segments.arc south-east',
            ],
            'composition' => [
                'one branch return chain on the left side',
                'each next anchorStart is previous anchorEnd',
                'verticalLength controls the first vertical segment',
                'connectorLength controls the horizontal return segment',
            ],
            'props' => [
                'side=left',
                $tab . 'anchorStart{x,y}',
                $tab . 'arcSize',
                $tab . 'verticalLength',
                $tab . 'connectorLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-branch-return-left',
        ],
        [
            'name' => 'branch-return right',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.branch-return side="right"',
            'structure' => [
                'paths.branch-return.right',
                $tree . 'calculates anchor chain',
                $tree . 'segments.path bottom-top',
                $tree . 'segments.arc east-north',
                $tree . 'segments.path right-left',
                $tree . 'segments.arc south-west',
            ],
            'composition' => [
                'one branch return chain on the right side',
                'each next anchorStart is previous anchorEnd',
                'verticalLength controls the first vertical segment',
                'connectorLength controls the horizontal return segment',
            ],
            'props' => [
                'side=right',
                $tab . 'anchorStart{x,y}',
                $tab . 'arcSize',
                $tab . 'verticalLength',
                $tab . 'connectorLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-branch-return-right',
        ],
        [
            'name' => 'branch-return-extension left',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.branch-return-extension side="left"',
            'structure' => [
                'paths.branch-return-extension.left',
                $tree . 'calculates anchor chain',
                $tree . 'segments.path bottom-top',
                $tree . 'segments.arc west-north',
                $tree . 'segments.path left-right',
            ],
            'composition' => [
                'one outward branch-return extension on the left side',
                'each next anchorStart is previous anchorEnd',
                'verticalLength controls the first vertical segment',
                'connectorLength controls the horizontal return segment',
            ],
            'props' => [
                'side=left',
                $tab . 'anchorStart{x,y}',
                $tab . 'arcSize',
                $tab . 'verticalLength',
                $tab . 'connectorLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-branch-return-extension-left',
        ],
        [
            'name' => 'branch-return-extension right',
            'component' => '<x-translation-workbench::ui.tw-graph.paths.branch-return-extension side="right"',
            'structure' => [
                'paths.branch-return-extension.right',
                $tree . 'calculates anchor chain',
                $tree . 'segments.path bottom-top',
                $tree . 'segments.arc east-north',
                $tree . 'segments.path right-left',
            ],
            'composition' => [
                'one outward branch-return extension on the right side',
                'each next anchorStart is previous anchorEnd',
                'verticalLength controls the first vertical segment',
                'connectorLength controls the horizontal return segment',
            ],
            'props' => [
                'side=right',
                $tab . 'anchorStart{x,y}',
                $tab . 'arcSize',
                $tab . 'verticalLength',
                $tab . 'connectorLength',
                $tab . 'color',
                $tab . 'dev',
            ],
            'view' => 'path-branch-return-extension-right',
        ],
    ];
@endphp

<div
    class="w-full"
    x-data="{ twGraphDev: true }"
>
    <flux:accordion>
        <flux:accordion.item>
            <flux:accordion.heading class="rounded rounded-b-md bg-sky-800 p-2">
                <span class="inline-flex flex-wrap items-center gap-2">
                    <span class="w-32">{{ __('Path catalog') }}</span>
                    <flux:field
                        class="items-center gap-2"
                        variant="inline"
                        x-on:click.stop
                    >
                        <flux:switch
                            class="switch-colored hover:cursor-pointer"
                            x-model="twGraphDev"
                        />
                        <flux:label class="text-xs opacity-70 hover:cursor-pointer">
                            {{ __('DEV') }}
                        </flux:label>
                    </flux:field>
                    <flux:badge
                        class="w-xs inline-block"
                        color="sky"
                    >
                        <flux:breadcrumbs>
                            <flux:breadcrumbs.item href="#">{{ __('primitives') }}</flux:breadcrumbs.item>
                            <flux:breadcrumbs.item href="#">{{ __('segments') }}</flux:breadcrumbs.item>
                            <flux:breadcrumbs.item href="#">{{ __('paths') }}</flux:breadcrumbs.item>
                        </flux:breadcrumbs>
                    </flux:badge>
                    <flux:badge
                        class="w-48"
                        color="amber"
                    >
                        {{ __('anchor chain calculation') }}
                    </flux:badge>
                    <flux:badge color="red">
                        {{ __('paths.trunk') }}
                    </flux:badge>
                    <flux:badge color="red">
                        {{ __('paths.merge') }}
                    </flux:badge>
                    <flux:badge color="red">
                        {{ __('paths.merge-extension') }}
                    </flux:badge>
                    <flux:badge color="red">
                        {{ __('paths.branch') }}
                    </flux:badge>
                    <flux:badge color="red">
                        {{ __('paths.branch-extension') }}
                    </flux:badge>
                    <flux:badge color="red">
                        {{ __('paths.branch-return') }}
                    </flux:badge>
                    <flux:badge color="red">
                        {{ __('paths.branch-return-extension') }}
                    </flux:badge>
                </span>
            </flux:accordion.heading>
            <flux:accordion.content>
                <div
                    class="mt-3 w-full"
                    x-bind:class="{ 'tw-graph-protocol-dev-disabled': !twGraphDev }"
                >
                    <flux:tab.group>
                        <flux:tabs>
                            @foreach ($pathRows as $row)
                                <flux:tab name="{{ $row['view'] }}">
                                    {{ $row['name'] }}
                                </flux:tab>
                            @endforeach
                        </flux:tabs>

                        @foreach ($pathRows as $row)
                            <flux:tab.panel name="{{ $row['view'] }}">
                                <div class="mt-3">
                                    @include(
                                        'translation-workbench::livewire.raw-data.timeline-chains.graph-preview.graph-v2.path-catalog-card',
                                        ['row' => $row]
                                    )
                                </div>
                            </flux:tab.panel>
                        @endforeach
                    </flux:tab.group>
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</div>
