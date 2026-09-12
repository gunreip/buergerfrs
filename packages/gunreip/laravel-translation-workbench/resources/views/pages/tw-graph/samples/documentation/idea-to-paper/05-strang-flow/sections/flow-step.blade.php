{{-- Flow section: flow-step. Code example and rendered preview. --}}

@if ($sectionContent === 'code')
<p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
    {{ __('Flow step adds a labeled process step after an existing flow anchor. The centered step label describes the shared process state; optional node labels at the end anchor carry concrete facts about the next hand-authored decision point.') }}
</p>
<div
    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
    <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.flow-step
    id="literature.flow.1.paper-process.step-1"
    attach-to="literature.flow.1.paper-process.anchorNode-end"
    <span class="text-lime-300">before-length="2rem"
    after-length="3rem"
    :step-label="[
        'text' => ['Draft prepared', 'structure exists'],
        'width' => 'halfLong',
        'align' => 'center',
    ]"
    :node-labels="[
        'end' => [
            'right' => [
                'text' => ['Next', 'review choice'],
                'width' => 'default',
                'align' => 'left',
            ],
        ],
    ]"</span>
/&gt;</code></pre>
</div>
@elseif ($sectionContent === 'preview')
{{-- Flow Step --}}
<x-translation-workbench::ui.tw-graph
    class="px-20 py-12"
    graph-id="idea-to-paper-step-08-flow-step"
    :dev="$dev"
    :coordinates="$coordinates"
    color="zinc"
    slot-min-height="44rem"
    horizontal-padding="32rem"
    min-width="58rem"
    min-height="44rem"
>
    <div class="pointer-events-none opacity-25">
        <x-translation-workbench::ui.tw-graph.strang.flow-start
            id="literature.flow.1.paper-process"
            start-length="7rem"
            :node-end-dot="false"
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
            ]"
        />
    </div>

    <x-translation-workbench::ui.tw-graph.strang.flow-step
        id="literature.flow.1.paper-process.step-1"
        color="cyan"
        attach-to="literature.flow.1.paper-process.anchorNode-end"
        before-length="2rem"
        after-length="3rem"
        :step-label="[
            'text' => ['Draft prepared', 'structure exists'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :node-labels="[
            'end' => [
                'right' => [
                    'text' => ['Next', 'review choice'],
                    'width' => 'default',
                    'align' => 'left',
                ],
            ],
        ]"
    />
</x-translation-workbench::ui.tw-graph>
<flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
    {{ '.../tw-graph/samples/documentation/idea-to-paper/05-strang-flow/sections/flow-step.blade.php' }}
</flux:field>
@endif
