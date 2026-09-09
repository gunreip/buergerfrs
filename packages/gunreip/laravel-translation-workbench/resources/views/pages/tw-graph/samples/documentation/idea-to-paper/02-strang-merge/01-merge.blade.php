{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/02-strang-merge/01-merge.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
    $graphId = $ideaToPaperGraphId ?? 'idea-to-paper-step-05-merge';
    $renderMode = $renderMode ?? 'documentation';
    $mergeProps = [
        [
            'name' => 'attach-to / :anchor-start',
            'default' => 'registered anchor or 0/0',
            'effect' => 'Places the merge start. In a full graph this usually comes from a trunk anchor.',
        ],
        [
            'name' => 'bridge-length',
            'default' => 'bridge-length',
            'effect' => 'Controls the horizontal bridge between arc-in and arc-out.',
        ],
        [
            'name' => 'stem-length',
            'default' => 'stem-length',
            'effect' => 'Controls the primary vertical stem before the merge bends into the bridge.',
        ],
        [
            'name' => ':node-labels',
            'default' => '[]',
            'effect' =>
                'Adds labels to concrete merge anchor nodes. Use named label arrays for text, width, align, color, and justify.',
        ],
    ];
@endphp

@if ($renderMode === 'documentation')
    <section class="grid gap-4 lg:grid-cols-2">
        <flux:callout
            color="amber"
            icon="git-merge"
        >
            <flux:callout.heading>
                {{ __('5. Merge') }}
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('A merge strand collects a side path and leads it back toward the central chain. This default example only places left and right merge strands at a trunk anchor; all merge geometry and labels stay on their defaults.') }}
            </flux:callout.text>

            <div
                class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                <pre><code>&lt;x-translation-workbench::ui.tw-graph ...&gt;
    ...
    &lt;x-translation-workbench::ui.tw-graph.strang.merge-left
        id="literature.left.1.source-note"
        attach-to="strang.trunk.node.2"
    /&gt;

    &lt;x-translation-workbench::ui.tw-graph.strang.merge-right
        id="literature.right.1.source-note"
        attach-to="strang.trunk.node.2"
    /&gt;
    ...
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
            </div>

            <div class="mt-4 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <flux:table container:class="max-h-80">
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-900"
                        sticky
                    >
                        <flux:table.column class="w-40">{{ __('Prop') }}</flux:table.column>
                        <flux:table.column class="w-40">{{ __('Default') }}</flux:table.column>
                        <flux:table.column class="min-w-0">{{ __('Purpose') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($mergeProps as $prop)
                            <flux:table.row>
                                <flux:table.cell class="align-top">
                                    <code class="break-words text-xs">{{ $prop['name'] }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="align-top">
                                    <code class="break-words text-xs">{{ $prop['default'] }}</code>
                                </flux:table.cell>
                                <flux:table.cell
                                    class="min-w-0 whitespace-normal break-words text-xs leading-5 text-zinc-600 dark:text-zinc-300"
                                >
                                    {{ $prop['effect'] }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:callout>

        <flux:callout
            color="zinc"
            icon="square-dashed-text"
        >
            <flux:callout.heading>
                {{ __('Step 5 preview') }}
            </flux:callout.heading>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
@endif

<x-translation-workbench::ui.tw-graph
    class="px-24 py-16"
    :graph-id="$graphId"
    :dev="$dev"
    :coordinates="$coordinates"
    color="amber"
    line-length="4rem"
    line-width="0.25rem"
    node-size="0.95rem"
    arc-size="2.75rem"
    bridge-length="18rem"
    stem-length="5rem"
    connector-length="2rem"
    connector-gap="0.25rem"
    slot-min-height="34rem"
    horizontal-padding="42rem"
    min-width="72rem"
    min-height="34rem"
>
    <div class="pointer-events-none opacity-25">
        <x-translation-workbench::ui.tw-graph.strang.trunk
            id="literature.center.1.merge-reference"
            color="zinc"
            :stem-count="3"
            start-length="4rem"
            :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem']"
            end-length="3rem"
            :dev-mode="false"
            :start-label="[
                'text' => ['reference trunk'],
                'width' => 'default',
                'align' => 'center',
            ]"
        />
    </div>

    <x-translation-workbench::ui.tw-graph.strang.merge-left
        id="literature.left.1.source-note"
        attach-to="strang.trunk.node.2"
    />
    <x-translation-workbench::ui.tw-graph.strang.merge-right
        id="literature.right.1.source-note"
        attach-to="strang.trunk.node.2"
    />

</x-translation-workbench::ui.tw-graph>
<flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
    .../tw-graph/samples/documentation/idea-to-paper/02-strang-merge/01-merge.blade.php
</flux:field>

@if ($renderMode === 'documentation')
    </div>
    </flux:callout>
    </section>
@endif
