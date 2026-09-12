{{-- Flow section: flow-if-test. Code example and rendered preview. --}}

@if ($sectionContent === 'code')
<p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
    {{ __('Two individually authored IF / ELSEIF / ENDIF components share the same starting anchor. Each block has its own labels, conditions, lengths, and direction settings.') }}
</p>
<div class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
    <pre><code>&lt;x-translation-workbench::ui.tw-graph
    class="px-20 py-12"
    graph-id="idea-to-paper-step-08-flow-if-test"
    :dev="$dev"
    :coordinates="$coordinates"
    color="cyan"
    slot-min-height="52rem"
    horizontal-padding="32rem"
    min-width="65rem"
    min-height="52rem"
&gt;
    &lt;x-translation-workbench::ui.tw-graph.strang.flow-start
        id="literature.flow.1.if-test-origin"
        :anchor-start="['x' =&gt; '0rem', 'y' =&gt; '0rem']"
        :start-label="['text' =&gt; ['Flow start'], 'side' =&gt; 'bottom', 'width' =&gt; 'half', 'align' =&gt; 'center']"
    /&gt;

    &lt;x-translation-workbench::ui.tw-graph.strang.if-else-endif
        id="literature.flow.1.if-test"
        <span class="text-lime-300">side="left"</span>
        attach-to="literature.flow.1.if-test-origin.anchorNode-end"
        start-bridge-length="12.75rem"
        condition-bridge-length="1.75rem"
        end-bridge-length="1.75rem"
        :intro-label="['text' =&gt; ['IF / ELSE flow'], 'width' =&gt; 'half', 'align' =&gt; 'center']"
        :if-condition-label="[
            'text' =&gt; ['IF color=red', 'THEN RGB(240, 62, 62)'],
            'width' =&gt; 'default',
            'align' =&gt; 'left',
        ]"
        :elseif-conditions="[
            [
                'key' =&gt; '1',
                'color' =&gt; 'green',
                'label' =&gt; [
                    'text' =&gt; ['ELSEIF color=green', 'THEN RGB(12, 166, 120)'],
                    'width' =&gt; 'default',
                    'align' =&gt; 'left',
                ],
            ],
            [
                'key' =&gt; '2',
                'label' =&gt; [
                    'text' =&gt; ['ELSEIF color=blue', 'THEN RGB(59, 130, 246)'],
                    'width' =&gt; 'halfLong',
                    'align' =&gt; 'left',
                ],
            ],
            [
                'key' =&gt; '3',
                'color' =&gt; 'amber',
                'label' =&gt; [
                    'text' =&gt; ['ELSEIF color=amber', 'THEN RGB(245, 158, 11)'],
                    'width' =&gt; 'long',
                    'align' =&gt; 'left',
                ],
            ],
            [
                'key' =&gt; '4',
                'leftStemLength' =&gt; '0rem',
                'thenContinuation' =&gt; 'arc-east-north',
                'label' =&gt; [
                    'text' =&gt; ['ELSE fallback', 'THEN use default palette'],
                    'width' =&gt; 'halfLong',
                    'align' =&gt; 'left',
                ],
            ],
        ]"
        :end-label="['text' =&gt; ['ENDIF'], 'width' =&gt; 'half', 'align' =&gt; 'center']"
    /&gt;

    &lt;x-translation-workbench::ui.tw-graph.strang.if-else-endif
        id="literature.flow.1.if-test-right"
        <span class="text-lime-300">side="right"</span>
        attach-to="literature.flow.1.if-test-origin.anchorNode-end"
        start-bridge-length="12.75rem"
        condition-bridge-length="1.75rem"
        end-bridge-length="1.75rem"
        :intro-label="['text' =&gt; ['IF / ELSE flow'], 'width' =&gt; 'half', 'align' =&gt; 'center']"
        :if-condition-label="[
            'text' =&gt; ['IF color=red', 'THEN RGB(240, 62, 62)'],
            'width' =&gt; 'default',
            'align' =&gt; 'left',
        ]"
        :elseif-conditions="[
            [
                'key' =&gt; '1',
                'color' =&gt; 'green',
                'label' =&gt; [
                    'text' =&gt; ['ELSEIF color=green', 'THEN RGB(12, 166, 120)'],
                    'width' =&gt; 'default',
                    'align' =&gt; 'left',
                ],
            ],
            [
                'key' =&gt; '2',
                'label' =&gt; [
                    'text' =&gt; ['ELSEIF color=blue', 'THEN RGB(59, 130, 246)'],
                    'width' =&gt; 'halfLong',
                    'align' =&gt; 'left',
                ],
            ],
            [
                'key' =&gt; '3',
                'color' =&gt; 'amber',
                'label' =&gt; [
                    'text' =&gt; ['ELSEIF color=amber', 'THEN RGB(245, 158, 11)'],
                    'width' =&gt; 'long',
                    'align' =&gt; 'left',
                ],
            ],
            [
                'key' =&gt; '4',
                'leftStemLength' =&gt; '0rem',
                'thenContinuation' =&gt; 'arc-west-north',
                'label' =&gt; [
                    'text' =&gt; ['ELSE fallback', 'THEN use default palette'],
                    'width' =&gt; 'halfLong',
                    'align' =&gt; 'left',
                ],
            ],
        ]"
        :end-label="['text' =&gt; ['ENDIF'], 'width' =&gt; 'half', 'align' =&gt; 'center']"
    /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
</div>
@elseif ($sectionContent === 'preview')
{{-- Flow IF Test: compare the same content in both directions. --}}
<x-translation-workbench::ui.tw-graph
    class="px-20 py-12"
    graph-id="idea-to-paper-step-08-flow-if-test"
    :dev="$dev"
    :coordinates="$coordinates"
    color="cyan"
    slot-min-height="52rem"
    horizontal-padding="32rem"
    min-width="65rem"
    min-height="52rem"
>
    <x-translation-workbench::ui.tw-graph.strang.flow-start
        id="literature.flow.1.if-test-origin"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        :start-label="['text' => ['Flow start'], 'side' => 'bottom', 'width' => 'half', 'align' => 'center']"
    />

    <x-translation-workbench::ui.tw-graph.strang.if-else-endif
        id="literature.flow.1.if-test"
        side="left"
        attach-to="literature.flow.1.if-test-origin.anchorNode-end"
        start-bridge-length="12.75rem"
        condition-bridge-length="1.75rem"
        end-bridge-length="1.75rem"
        :intro-label="['text' => ['IF / ELSE flow'], 'width' => 'half', 'align' => 'center']"
        :if-condition-label="[
            'text' => ['IF color=red', 'THEN RGB(240, 62, 62)'],
            'width' => 'default',
            'align' => 'left',
        ]"
        :elseif-conditions="[
            [
                'key' => '1',
                'color' => 'green',
                'label' => [
                    'text' => ['ELSEIF color=green', 'THEN RGB(12, 166, 120)'],
                    'width' => 'default',
                    'align' => 'left',
                ],
            ],
            [
                'key' => '2',
                'label' => [
                    'text' => ['ELSEIF color=blue', 'THEN RGB(59, 130, 246)'],
                    'width' => 'halfLong',
                    'align' => 'left',
                ],
            ],
            [
                'key' => '3',
                'color' => 'amber',
                'label' => [
                    'text' => ['ELSEIF color=amber', 'THEN RGB(245, 158, 11)'],
                    'width' => 'long',
                    'align' => 'left',
                ],
            ],
            [
                'key' => '4',
                'leftStemLength' => '0rem',
                'thenContinuation' => 'arc-east-north',
                'label' => [
                    'text' => ['ELSE fallback', 'THEN use default palette'],
                    'width' => 'halfLong',
                    'align' => 'left',
                ],
            ],
        ]"
        :end-label="['text' => ['ENDIF'], 'width' => 'half', 'align' => 'center']"
    />

    <x-translation-workbench::ui.tw-graph.strang.if-else-endif
        id="literature.flow.1.if-test-right"
        side="right"
        attach-to="literature.flow.1.if-test-origin.anchorNode-end"
        start-bridge-length="12.75rem"
        condition-bridge-length="1.75rem"
        end-bridge-length="1.75rem"
        :intro-label="['text' => ['IF / ELSE flow'], 'width' => 'half', 'align' => 'center']"
        :if-condition-label="[
            'text' => ['IF color=red', 'THEN RGB(240, 62, 62)'],
            'width' => 'default',
            'align' => 'left',
        ]"
        :elseif-conditions="[
            [
                'key' => '1',
                'color' => 'green',
                'label' => [
                    'text' => ['ELSEIF color=green', 'THEN RGB(12, 166, 120)'],
                    'width' => 'default',
                    'align' => 'left',
                ],
            ],
            [
                'key' => '2',
                'label' => [
                    'text' => ['ELSEIF color=blue', 'THEN RGB(59, 130, 246)'],
                    'width' => 'halfLong',
                    'align' => 'left',
                ],
            ],
            [
                'key' => '3',
                'color' => 'amber',
                'label' => [
                    'text' => ['ELSEIF color=amber', 'THEN RGB(245, 158, 11)'],
                    'width' => 'long',
                    'align' => 'left',
                ],
            ],
            [
                'key' => '4',
                'leftStemLength' => '0rem',
                'thenContinuation' => 'arc-west-north',
                'label' => [
                    'text' => ['ELSE fallback', 'THEN use default palette'],
                    'width' => 'halfLong',
                    'align' => 'left',
                ],
            ],
        ]"
        :end-label="['text' => ['ENDIF'], 'width' => 'half', 'align' => 'center']"
    />
</x-translation-workbench::ui.tw-graph>
<flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
    {{ '.../tw-graph/samples/documentation/idea-to-paper/05-strang-flow/sections/flow-if-test.blade.php' }}
</flux:field>
@endif
