{{-- Flow section: flow-if-else-endif. Code example and rendered preview. --}}

@if ($sectionContent === 'code')
    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
        {{ __('The reusable IF / ELSE / ENDIF wrapper starts from an existing flow anchor. It keeps the handmade graph readable by wiring the intro label, IF condition, ELSE fallback, and ENDIF label as one visible section while still exposing the relevant lengths and labels directly. The separate right-facing block shares the same starting anchor as the left-facing block.') }}
    </p>
    <div
        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
        <pre><code>...

&lt;x-translation-workbench::ui.tw-graph.strang.if-else-endif
    id="literature.flow.1.paper-process.review-if-else"
    <span class="text-lime-300">attach-to="literature.flow.1.paper-process.review-step.anchorNode-end"</span>
    <span class="text-amber-300">start-bridge-length="9.75rem"
    condition-bridge-length="1.75rem"
    end-bridge-length="1.75rem"</span>
    <span class="text-lime-300">:intro-label="[
        'text' =&gt; ['IF / ELSE'],
        'width' =&gt; 'half',
        'align' =&gt; 'center',
    ]"
    :if-condition-label="[
        'text' = & gt;
        ['IF review passes', 'THEN publish draft'],
        'width' = & gt;
        'halfLong',
        'align' = & gt;
        'left',
    ]"
    :elseif-conditions="[
        [
            'key' = & gt;
            'else',
            'leftStemLength' = & gt;
            '0rem',
            'thenContinuation' = & gt;
            'arc-east-north',
            'label' = & gt;
            [
                'text' = & gt;
                ['ELSE revise draft', 'THEN return to review'],
                'width' = & gt;
                'halfLong',
                'align' = & gt;
                'left',
            ],
        ],
    ]"
    :end-label="[
        'text' = & gt;
        ['ENDIF'],
        'width' = & gt;
        'half',
        'align' = & gt;
        'center',
    ]"</span>
/&gt;

&lt;x-translation-workbench::ui.tw-graph.strang.if-else-endif
    id="literature.flow.1.paper-process.review-if-else-right"
    <span class="text-lime-300">side="right"</span>
    <span class="text-lime-300">attach-to="literature.flow.1.paper-process.review-step.anchorNode-end"</span>
    start-bridge-length="9.75rem"
    condition-bridge-length="1.75rem"
    end-bridge-length="1.75rem"
    :intro-label="[
        'text' = & gt;
        ['IF / ELSE'],
        'width' = & gt;
        'half',
        'align' = & gt;
        'center',
    ]"
    :if-condition-label="[
        'text' = & gt;
        ['IF review passes', 'THEN publish draft'],
        'width' = & gt;
        'halfLong',
        'align' = & gt;
        'left',
    ]"
    :elseif-conditions="[
        [
            'key' = & gt;
            'else',
            'leftStemLength' = & gt;
            '0rem',
            'thenContinuation' = & gt;
            'arc-west-north',
            'label' = & gt;
            [
                'text' = & gt;
                ['ELSE revise draft', 'THEN return to review'],
                'width' = & gt;
                'halfLong',
                'align' = & gt;
                'left',
            ],
        ],
    ]"
    :end-label="[
        'text' = & gt;
        ['ENDIF'],
        'width' = & gt;
        'half',
        'align' = & gt;
        'center',
    ]"
/&gt;</code></pre>
    </div>
@elseif ($sectionContent === 'preview')
    {{-- Flow IF ELSE ENDIF --}}
    <x-translation-workbench::ui.tw-graph
        class="px-20 py-12"
        graph-id="idea-to-paper-step-08-flow-if-else-endif"
        :dev="$dev"
        :coordinates="$coordinates"
        color="cyan"
        slot-min-height="58rem"
        horizontal-padding="34rem"
        min-width="68rem"
        min-height="58rem"
    >
        <div class="pointer-events-none opacity-25">
            <x-translation-workbench::ui.tw-graph.strang.flow-start
                id="literature.flow.1.paper-process.review"
                start-length="7rem"
                :node-end-dot="false"
                :start-label="[
                    'text' => ['Paper process', 'start'],
                    'width' => 'halfLong',
                    'align' => 'center',
                ]"
            />

            <x-translation-workbench::ui.tw-graph.strang.flow-step
                id="literature.flow.1.paper-process.review-step"
                attach-to="literature.flow.1.paper-process.review.anchorNode-end"
                before-length="2rem"
                after-length="3rem"
                :step-label="[
                    'text' => ['Review result', 'ready to branch'],
                    'width' => 'halfLong',
                    'align' => 'center',
                ]"
            />
        </div>

        <x-translation-workbench::ui.tw-graph.strang.if-else-endif
            id="literature.flow.1.paper-process.review-if-else"
            attach-to="literature.flow.1.paper-process.review-step.anchorNode-end"
            start-bridge-length="6.75rem"
            condition-bridge-length="1.75rem"
            end-bridge-length="1.75rem"
            :intro-label="[
                'text' => ['IF / ELSE'],
                'width' => 'half',
                'align' => 'center',
            ]"
            :if-condition-label="[
                'text' => ['IF review passes', 'THEN publish draft'],
                'width' => 'halfLong',
                'align' => 'left',
            ]"
            :elseif-conditions="[
                [
                    'key' => 'else',
                    'leftStemLength' => '0rem',
                    'thenContinuation' => 'arc-east-north',
                    'label' => [
                        'text' => ['ELSE revise draft', 'THEN return to review'],
                        'width' => 'halfLong',
                        'align' => 'left',
                    ],
                ],
            ]"
            :end-label="[
                'text' => ['ENDIF'],
                'width' => 'half',
                'align' => 'center',
            ]"
        />

        <x-translation-workbench::ui.tw-graph.strang.if-else-endif
            id="literature.flow.1.paper-process.review-if-else-right"
            side="right"
            attach-to="literature.flow.1.paper-process.review-step.anchorNode-end"
            start-bridge-length="12.75rem"
            condition-bridge-length="1.75rem"
            end-bridge-length="3.75rem"
            :intro-label="[
                'text' => ['IF / ELSE'],
                'width' => 'half',
                'align' => 'center',
            ]"
            :if-condition-label="[
                'text' => ['IF review passes', 'THEN publish draft'],
                'width' => 'halfLong',
                'align' => 'left',
            ]"
            :elseif-conditions="[
                [
                    'key' => 'else',
                    'leftStemLength' => '0rem',
                    'thenContinuation' => 'arc-west-north',
                    'label' => [
                        'text' => ['ELSE revise draft', 'THEN return to review'],
                        'width' => 'halfLong',
                        'align' => 'left',
                    ],
                ],
            ]"
            :end-label="[
                'text' => ['ENDIF'],
                'width' => 'half',
                'align' => 'center',
            ]"
        />
    </x-translation-workbench::ui.tw-graph>
    <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
        {{ '.../tw-graph/samples/documentation/idea-to-paper/05-strang-flow/sections/flow-if-else-endif.blade.php' }}
    </flux:field>
@endif
