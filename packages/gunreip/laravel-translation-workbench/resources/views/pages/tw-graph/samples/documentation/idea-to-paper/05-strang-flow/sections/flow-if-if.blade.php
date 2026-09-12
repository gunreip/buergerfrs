{{-- Flow section: flow-if-if. Code example and rendered preview. --}}

@if ($sectionContent === 'code')
<div
    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
    <pre><code>...

&lt;x-translation-workbench::ui.tw-graph.strang.flow-decision
    id="literature.flow.1.paper-process.revision-if"
    <span class="text-lime-300">attach-to="literature.flow.1.paper-process.revision-step.anchorNode-end"
    bridge-length="5rem"
    :decision-label="[
        'text' => ['IF', 'review changes required'],
        'width' => 'default',
        'align' => 'center',
        'connectorLength' => '3rem',
    ]"</span>
/&gt;

&lt;x-translation-workbench::ui.tw-graph.strang.flow-decision
    id="literature.flow.1.paper-process.accepted-if"
    <span class="text-lime-300">attach-to="literature.flow.1.paper-process.accepted-step.anchorNode-end"
    bridge-length="5rem"
    :decision-label="[
        'text' => ['IF', 'publication ready'],
        'width' => 'default',
        'align' => 'center',
        'connectorLength' => '3rem',
    ]"</span>
/&gt;</code></pre>
</div>
@elseif ($sectionContent === 'preview')
{{-- Flow IF --}}
<x-translation-workbench::ui.tw-graph
    class="px-20 py-12"
    graph-id="idea-to-paper-step-08-flow-if"
    :dev="$dev"
    :coordinates="$coordinates"
    color="zinc"
    slot-min-height="74rem"
    horizontal-padding="38rem"
    min-width="74rem"
    min-height="74rem"
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

    <div class="pointer-events-none opacity-35">
        <x-translation-workbench::ui.tw-graph.strang.flow-decision
            id="literature.flow.1.paper-process.decision-1"
            attach-to="literature.flow.1.paper-process.step-1.anchorNode-end"
            bridge-length="19rem"
            :decision-label="[
                'text' => ['Peer review', 'decision'],
                'width' => 'halfLong',
                'align' => 'center',
                'connectorLength' => '3.5rem',
            ]"
        />

        <x-translation-workbench::ui.tw-graph.strang.flow-step
            id="literature.flow.1.paper-process.revision-step"
            attach-to="literature.flow.1.paper-process.decision-1.left.anchorNode-end"
            before-length="1.5rem"
            after-length="2.5rem"
            :step-label="[
                'text' => ['Revision loop', 'comments resolved'],
                'width' => 'halfLong',
                'align' => 'center',
            ]"
        />

        <x-translation-workbench::ui.tw-graph.strang.flow-step
            id="literature.flow.1.paper-process.accepted-step"
            attach-to="literature.flow.1.paper-process.decision-1.right.anchorNode-end"
            before-length="1.5rem"
            after-length="2.5rem"
            :step-label="[
                'text' => ['Accepted path', 'publication prep'],
                'width' => 'halfLong',
                'align' => 'center',
            ]"
        />
    </div>

    <x-translation-workbench::ui.tw-graph.strang.flow-decision
        id="literature.flow.1.paper-process.revision-if"
        color="cyan"
        attach-to="literature.flow.1.paper-process.revision-step.anchorNode-end"
        bridge-length="5rem"
        :decision-label="[
            'text' => ['IF', 'review changes required'],
            'width' => 'default',
            'align' => 'center',
            'connectorLength' => '3rem',
        ]"
    />

    <x-translation-workbench::ui.tw-graph.strang.flow-decision
        id="literature.flow.1.paper-process.accepted-if"
        color="cyan"
        attach-to="literature.flow.1.paper-process.accepted-step.anchorNode-end"
        bridge-length="5rem"
        :decision-label="[
            'text' => ['IF', 'publication ready'],
            'width' => 'default',
            'align' => 'center',
            'connectorLength' => '3rem',
        ]"
    />
</x-translation-workbench::ui.tw-graph>
<flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
    {{ '.../tw-graph/samples/documentation/idea-to-paper/05-strang-flow/sections/flow-if-if.blade.php' }}
</flux:field>
@endif
