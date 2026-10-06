<x-translation-workbench::ui.tw-graph.file-region view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs" layout="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/overview/data/main-tabs.php">
{{-- Main tabs / left --}}
@php
    $merge = $structure['merges']['left'];
@endphp
<x-translation-workbench::ui.tw-graph.strang.merge-left
    :id="$merge['id']"
    :direction="$structure['root']['direction']"
    :attach-to="$merge['attachTo']"
    :color="\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewEntryLayout::connectedColor($structure['canvas']['graphId'], $merge['attachTo'], $merge['color'])"
    :start-length="$merge['startLength']"
    :bridge-length="$merge['bridgeLength']"
    :arc-radius="$merge['arcRadius'] ?? null"
    :stem-lengths="$merge['stemLengths']"
    :node-labels="$merge['nodeLabels']"
    :extension-count="$merge['extensionCount']"
    :extension-colors="$merge['extensionColors'] ?? []"
    :extension-start-length="$merge['extensionStartLength']"
    :extension-stem-length="$merge['extensionStemLength']"
    :extension-bridge-length="$merge['extensionBridgeLength']"
    :extension-stem-lengths="$merge['extensionStemLengths'] ?? []"
    :extension-bridge-continuations="$merge['extensionBridgeContinuations'] ?? []"
    :extension-arc-radiuss="$merge['extensionArcRadiuss'] ?? []"
    :extension-node-labels="$merge['extensionNodeLabels']"
    :extension-end-labels="$merge['extensionEndLabels']"
/>

{{-- Main tabs / right --}}
@php
    $merge = $structure['merges']['right'];
@endphp
<x-translation-workbench::ui.tw-graph.strang.merge-right
    :id="$merge['id']"
    :direction="$structure['root']['direction']"
    :attach-to="$merge['attachTo']"
    :color="\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewEntryLayout::connectedColor($structure['canvas']['graphId'], $merge['attachTo'], $merge['color'])"
    :start-length="$merge['startLength']"
    :bridge-length="$merge['bridgeLength']"
    :arc-radius="$merge['arcRadius'] ?? null"
    :stem-lengths="$merge['stemLengths']"
    :node-labels="$merge['nodeLabels']"
    :extension-count="$merge['extensionCount']"
    :extension-colors="$merge['extensionColors'] ?? []"
    :extension-start-length="$merge['extensionStartLength']"
    :extension-stem-length="$merge['extensionStemLength']"
    :extension-stem-lengths="$merge['extensionStemLengths'] ?? []"
    :extension-bridge-continuations="$merge['extensionBridgeContinuations'] ?? []"
    :extension-arc-radiuss="$merge['extensionArcRadiuss'] ?? []"
    :extension-bridge-length="$merge['extensionBridgeLength']"
    :extension-node-labels="$merge['extensionNodeLabels']"
/>

@foreach ($structure['tabs'] as $key => $tab)
    @php
        $tab = array_replace($structure['tabLayout'], $tab);
    @endphp
    <x-translation-workbench::ui.tw-graph.strang.flow-step
        :id="'literature.overview.' . $key"
        :attach-to="$tab['attachTo']"
        :direction="$tab['direction']"
        :node-end="$tab['nodeEnd']"
        :node-end-dot="$tab['nodeEndDot']"
        :color="\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewEntryLayout::connectedColor($structure['canvas']['graphId'], $tab['attachTo'], $tab['color'])"
        :before-length="$tab['beforeLength']"
        :after-length="$tab['afterLength']"
        :step-caps="false"
        :step-label="[
            'text' => [$tab['text']],
            'width' => $tab['width'],
            'align' => $tab['align'],
        ]"
    />
@endforeach

</x-translation-workbench::ui.tw-graph.file-region>
