{{-- Flow section: flow-start. Code example and rendered preview. --}}

@if ($sectionContent === 'code')
<p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
    {{ __('Flow start defines only the entry into an Ablaufdiagramm. The public API mirrors trunk-start: one start label, one first anchor node, and optional left/right labels. Internally it still delegates to parts.start, so the existing part and segment chain remains the single geometry source.') }}
</p>
<div
    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
    <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.flow-start
    id="literature.flow.1.paper-process"
    <span class="text-lime-300">start-length="7rem"
    :start-label="[
        'text' => ['Paper process', 'start'],
        'width' => 'halfLong',
        'align' => 'center',
    ]"
    :start-node-labels="[
        'left' => [
            'text' => ['Input', 'raw thought'],
            'width' => 'default',
            'align' => 'right',
        ],
        'right' => [
            'text' => ['Next', 'draft decision'],
            'width' => 'default',
            'align' => 'left',
        ],
    ]"</span>
/&gt;</code></pre>
</div>
@elseif ($sectionContent === 'preview')
{{-- Flow Start --}}
<x-translation-workbench::ui.tw-graph
    class="px-20 py-12"
    :graph-id="$graphId"
    :dev="$dev"
    :coordinates="$coordinates"
    color="cyan"
    slot-min-height="30rem"
    horizontal-padding="28rem"
    min-width="52rem"
    min-height="30rem"
>
    <x-translation-workbench::ui.tw-graph.strang.flow-start
        id="literature.flow.1.paper-process"
        start-length="7rem"
        :start-label="[
            'text' => ['Paper process', 'start'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :start-node-labels="[
            'left' => [
                'text' => ['Input', 'raw thought'],
                'width' => 'default',
                'align' => 'right',
            ],
            'right' => [
                'text' => ['Next', 'draft decision'],
                'width' => 'default',
                'align' => 'left',
            ],
        ]"
    />
</x-translation-workbench::ui.tw-graph>
<flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
    {{ '.../tw-graph/samples/documentation/idea-to-paper/05-strang-flow/sections/flow-start.blade.php' }}
</flux:field>
@endif
