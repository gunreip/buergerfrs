{{-- Flow section: flow-decision. Code example and rendered preview. --}}

@if ($sectionContent === 'code')
<p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
    {{ __('Flow decision marks the first explicit branch point in a handmade process graph. It does not continue the center line; it renders one decision anchor and splits the flow into left and right sideways parts.') }}
</p>
<div
    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
    <pre><code>...

&lt;x-translation-workbench::ui.tw-graph.strang.flow-decision
    id="literature.flow.1.paper-process.decision-1"
    attach-to="literature.flow.1.paper-process.step-1.anchorNode-end"
    <span class="text-lime-300">bridge-length="9rem"
    :decision-label="[
        'text' => ['Peer review', 'decision'],
        'width' => 'halfLong',
        'align' => 'center',
        'connectorLength' => '4rem',
    ]"
    :node-labels="[
        'end' => [
            'left' => [
                'text' => ['Revise', 'major comments'],
                'width' => 'default',
                'align' => 'right',
            ],
            'right' => [
                'text' => ['Accept', 'minor edits'],
                'width' => 'default',
                'align' => 'left',
            ],
        ],
    ]"</span>
/&gt;</code></pre>
</div>
@elseif ($sectionContent === 'preview')
{{-- Flow Decision --}}
<x-translation-workbench::ui.tw-graph
    class="px-20 py-12"
    graph-id="idea-to-paper-step-08-flow-decision"
    :dev="$dev"
    :coordinates="$coordinates"
    color="zinc"
    slot-min-height="58rem"
    horizontal-padding="34rem"
    min-width="64rem"
    min-height="58rem"
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

        <x-translation-workbench::ui.tw-graph.strang.flow-step
            id="literature.flow.1.paper-process.step-1"
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
    </div>

    <x-translation-workbench::ui.tw-graph.strang.flow-decision
        id="literature.flow.1.paper-process.decision-1"
        color="cyan"
        attach-to="literature.flow.1.paper-process.step-1.anchorNode-end"
        bridge-length="9rem"
        :decision-label="[
            'text' => ['Peer review', 'decision'],
            'width' => 'halfLong',
            'align' => 'center',
            'connectorLength' => '4rem',
        ]"
    />
</x-translation-workbench::ui.tw-graph>
<flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
    {{ '.../tw-graph/samples/documentation/idea-to-paper/05-strang-flow/sections/flow-decision.blade.php' }}
</flux:field>
@endif
