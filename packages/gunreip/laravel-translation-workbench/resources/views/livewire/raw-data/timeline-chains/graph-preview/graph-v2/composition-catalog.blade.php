{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/graph-v2/composition-catalog.blade.php --}}

@php
    $componentChain = ['primitive', 'segment', 'path', 'strang', 'canvas', 'renderer'];

    $catalogRows = [
        [
            'level' => 'Segment',
            'pattern' => 'segments.trunk-path',
            'purpose' => 'Wraps one primitive and applies graph-specific defaults.',
            'sets' => [
                'segment="trunk-path"',
                'type="path"',
                'direction="bottom-top"',
                'nodeStart=false',
                'nodeEnd=true',
            ],
            'defaults' => ['length="4rem"', 'color="green"', 'anchorStart/anchorEnd from geometry cache'],
            'passes' => [
                'id',
                'direction',
                'length',
                'anchorStart{x,y}',
                'anchorEnd{x,y}',
                'nodeStart',
                'nodeEnd',
                'color',
            ],
        ],
        [
            'level' => 'Path',
            'pattern' => 'paths.trunk',
            'purpose' => 'Orders 1-N segments into one continuous trunk path.',
            'sets' => ['segments.start', 'segments.paths[]', 'segments.end'],
            'defaults' => ['start/end may be null', 'paths[] may contain any number of path segments'],
            'passes' => [
                'segments.start -> segments.trunk-start',
                'segments.paths[] -> segments.trunk-path',
                'segments.end -> segments.trunk-end',
            ],
        ],
        [
            'level' => 'Strang',
            'pattern' => 'strangs.trunk',
            'purpose' => 'Keeps the trunk layer explicit even though there is only one trunk.',
            'sets' => ['twGraph.strang.trunk.trunk'],
            'defaults' => ['one trunk strang', 'merge/branch may later contain left/right strangs'],
            'passes' => ['trunk -> paths.trunk'],
        ],
        [
            'level' => 'Canvas',
            'pattern' => 'tw-graph-protocol.canvas',
            'purpose' => 'Renders the trunk composition and flattens non-trunk segments.',
            'sets' => ['canvas coordinate space', 'non-trunk flattening order'],
            'defaults' => ['node type is skipped', 'arc type -> segment.arc', 'everything else -> segment.path'],
            'passes' => [
                'twGraph.strang.trunk.trunk -> strangs.trunk',
                'merge/branch segments -> segment.path or segment.arc',
            ],
        ],
        [
            'level' => 'Renderer',
            'pattern' => 'tw-graph-protocol',
            'purpose' => 'Applies global geometry variables and calculates canvas bounds.',
            'sets' => ['graphId', 'dev opacity', 'CSS geometry vars', 'min canvas width/height'],
            'defaults' => [
                'color from protocol.geometry.color or cyan',
                'pathWidth="0.25rem"',
                'nodeSize="1rem"',
                'arcSize="2.75rem"',
            ],
            'passes' => ['protocol -> canvas'],
        ],
        [
            'level' => 'Plan',
            'pattern' => 'graph-v2.plan',
            'purpose' => 'Defines component-authored graph intent before geometry is resolved.',
            'sets' => ['trunkStartLength', 'trunkPathLengths[]', 'trunkEndLength', 'trunkColor'],
            'defaults' => ['segment ids are stable', 'path fingerprint changes when dimensions/order change'],
            'passes' => [
                'plan -> GeometryResolver',
                'resolved protocol -> GeometryCache',
                'graphV2 -> renderer/status/json',
            ],
        ],
    ];
@endphp

<div class="w-full">
    <flux:accordion>
        <flux:accordion.item>
            <flux:accordion.heading class="rounded rounded-b-md bg-sky-800 p-2">
                <span class="inline-flex flex-wrap items-center gap-2">
                    <span>{{ __('Composition catalogue') }}</span>
                    <flux:badge
                        {{-- size="sm" --}}
                        color="sky"
                    >
                        {{ __('Component chain') }}:
                        {{ implode('->', $componentChain) }}
                    </flux:badge>
                </span>
            </flux:accordion.heading>
            <flux:accordion.content>
                <div class="mt-3 overflow-x-auto">
                    <div
                        class="grid min-w-[78rem] grid-cols-[9rem_14rem_18rem_repeat(3,minmax(14rem,1fr))] overflow-hidden rounded-lg text-xs">
                        <div
                            class="bg-zinc-100 px-3 py-2 font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            {{ __('Level') }}
                        </div>
                        <div
                            class="bg-zinc-100 px-3 py-2 font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            {{ __('Pattern') }}
                        </div>
                        <div
                            class="bg-zinc-100 px-3 py-2 font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            {{ __('Purpose') }}
                        </div>
                        <div
                            class="bg-zinc-100 px-3 py-2 font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            {{ __('Props set here') }}
                        </div>
                        <div
                            class="bg-zinc-100 px-3 py-2 font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            {{ __('Possibilities / defaults') }}
                        </div>
                        <div
                            class="bg-zinc-100 px-3 py-2 font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            {{ __('Passed through') }}
                        </div>

                        @foreach ($catalogRows as $row)
                            <div
                                class="border-t border-zinc-200 bg-white px-3 py-3 font-medium text-zinc-800 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                                {{ $row['level'] }}
                            </div>
                            <div
                                class="border-t border-zinc-200 bg-white px-3 py-3 font-mono text-[0.72rem] text-sky-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-sky-300">
                                {{ $row['pattern'] }}
                            </div>
                            <div
                                class="border-t border-zinc-200 bg-white px-3 py-3 text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                                {{ __($row['purpose']) }}
                            </div>

                            @foreach (['sets', 'defaults', 'passes'] as $column)
                                <div
                                    class="border-t border-zinc-200 bg-white px-3 py-3 font-mono text-[0.7rem] text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                                    <div class="space-y-1">
                                        @foreach ($row[$column] as $item)
                                            <div class="leading-tight">
                                                {{ $item }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</div>
