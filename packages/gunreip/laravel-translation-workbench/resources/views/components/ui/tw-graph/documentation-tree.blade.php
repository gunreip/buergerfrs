@props(['tree', 'graphId'])
{{-- SubTabs are sibling groups, regardless of whether they currently have children. --}}
@php
    $spineAttachTo = $tree['attachTo'];
@endphp
@foreach ($tree['children'] as $branch)
    @php
        $groupLayout = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewEntryLayout::group($tree, $branch);
        $groupLayout['color'] = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewEntryLayout::connectedColor($graphId, $spineAttachTo, $branch['color'] ?? null);
        if ($spineAttachTo === $tree['attachTo'] && empty($branch['color']) && filled($tree['levels']['subtabs']['color'])) {
            $groupLayout['color'] = $tree['levels']['subtabs']['color'];
        }
    @endphp
    <x-translation-workbench::ui.tw-graph.strang.flow-step
        :id="$branch['id'] . '-stem'"
        :attach-to="$spineAttachTo"
        :direction="$groupLayout['direction']"
        :color="$groupLayout['color']"
        :node-end="$groupLayout['nodeEnd']"
        :node-end-dot="$groupLayout['nodeEndDot']"
        :before-length="$groupLayout['stemLength']"
        label-gap="0rem"
        after-length="0rem"
        :step-caps="false"
    />

    <x-translation-workbench::ui.tw-graph.parts.sideways
        :id="$branch['id'] . '-branch'"
        :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
            $graphId,
            $branch['id'] . '-stem.anchorNode-end',
        )"
        :side="$groupLayout['side']"
        :direction="$groupLayout['direction']"
        :bridge-length="$groupLayout['bridgeLength']"
        :extension-length="$groupLayout['sideways']['extensionLength']"
        :node-end="$groupLayout['sideways']['nodeEnd']"
        :node-end-dot="$groupLayout['sideways']['nodeEndDot']"
        :extension-end="$groupLayout['sideways']['extensionEnd']"
        :color="$groupLayout['color']"
    />

    @php

        $previousId = $branch['id'] . '-branch';

    @endphp
    <x-translation-workbench::ui.tw-graph.strang.flow-step
        :id="$branch['id']"
        :attach-to="$previousId . '.anchorNode-end'"
        :direction="$groupLayout['direction']"
        :color="$groupLayout['color']"
        :node-end="$groupLayout['nodeEnd']"
        :node-end-dot="$groupLayout['nodeEndDot']"
        :before-length="$groupLayout['label']['beforeLength']"
        :after-length="$groupLayout['label']['afterLength']"
        :step-caps="false"
        :step-label="[
            'text' => [$branch['text']],
            'width' => $groupLayout['label']['width'],
            'align' => $groupLayout['label']['align'],
        ]"
    />
    @php
        $previousId = $branch['id'];
        $nodeColor = $groupLayout['nodes']['color'] ?? $groupLayout['color'];
    @endphp
    @foreach ($branch['children'] as $key => $child)
        @php
            $childId = $branch['id'] . '.' . $key;
            $nodeLayout = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewEntryLayout::node($groupLayout['nodes'], $groupLayout, $child);
            $nodeColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($child['color'] ?? null, $nodeColor, $groupLayout['color']);
            $nodeLayout['color'] = $nodeColor;
        @endphp
        <x-translation-workbench::ui.tw-graph.strang.flow-step
            :id="$childId"
            :attach-to="$previousId . '.anchorNode-end'"
            :direction="$nodeLayout['direction']"
            :color="$nodeLayout['color']"
            :node-end="$nodeLayout['nodeEnd']"
            :node-end-dot="$nodeLayout['nodeEndDot']"
            :before-length="$nodeLayout['beforeLength']"
            :label-gap="$nodeLayout['labelGap']"
            :after-length="$nodeLayout['stemLength']"
            :step-caps="false"
            :node-labels="[
                $nodeLayout['side'] => [
                    'text' => [$child['text']],
                    'nodeEnd' => $child['labelNodeEnd'] ?? true,
                    'width' => $nodeLayout['width'],
                    'align' => $nodeLayout['align'],
                ],
            ]"
        />
        @php
            $previousId = $childId;
        @endphp
    @endforeach

    @php

        $endLayout = $groupLayout['endCap'];

    @endphp
    <x-translation-workbench::ui.tw-graph.parts.end
        :id="$branch['id'] . '.end-cap'"
        :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
            $graphId,
            $previousId . '.anchorNode-end',
        )"
        :direction="$groupLayout['direction']"
        :length="$endLayout['length']"
        :cap-length="$endLayout['capLength']"
        :color="$nodeColor"
    />
    @php
        $spineAttachTo = $branch['id'] . '-stem.anchorNode-end';
    @endphp
@endforeach
