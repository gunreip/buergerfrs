{{-- resources/views/components/ui/tw-graph-v2/anchor-points.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-v2.anchor-points
        start-length="5rem"
        :path-lengths="['4rem', '4rem', '6rem']"
    />

    Optional:
    start-length="5rem" Distance from graph bottom to trunk.start.node center.
    path-lengths="['4rem', ...]" Segment lengths between visible trunk nodes.
    path-width="0.25rem" Used only to calculate node/path overlap.
    node-size="1rem" Visible node diameter.
    color="cyan|amber|rose|..." Debug axis color.
    arc-color="lime|..." Calculated arc anchor axis color.
    show-axes="0|0.1rem" Render center point X/Y helper axes. 0 disables axes; a CSS length enables axes and sets line width.
    show-labels="true|false" Render center point number labels.
    show-arc-anchors="true|false" Render calculated arc attachment points.
    show-arc-axes="0|0.1rem" Render calculated arc attachment X/Y helper axes. 0 disables axes; a CSS length enables axes and sets line width.
    show-arc-labels="true|false" Render arc attachment labels.
    show-arc-ne="true|false" Render a real arc-ne primitive on each NE anchor.
    show-arc-nw="true|false" Render a real arc-nw primitive on each NW anchor.
    show-arc-se="true|false" Render a real arc-se primitive on each SE anchor.
    show-arc-sw="true|false" Render a real arc-sw primitive on each SW anchor.
    show-horizontal-path="true|false" Render short horizontal paths on arc N/S points.
    show-horizontal-end-arcs="true|false" Render matching arc at the horizontal path end.
    horizontal-path-length="6rem" Test connector length.
    show-extension-path="true|false" Render an additional extension from the horizontal test path.
    extension-test-path="ne-left|nw-right|se-left|sw-right|merge|branch|all" Which extension path renders markers and geometry. merge renders se-left and sw-right together. branch renders ne-left and nw-right together. all renders both families.
    extension-color="sky|..." Fallback extension test path color.
    merge-extension-color="sky|..." Optional merge extension test path color.
    branch-extension-color="rose|..." Optional branch extension test path color.
    extension-parent-offset="null" Optional manual distance from the horizontal path start to the extension join. Defaults to horizontal-path-length, so e1 attaches to point 3.
    extension-horizontal-length="4rem" Extension connector length from parent path to extension end arc.
    extension-vertical-length="2rem" Vertical connector length from the extension end arc.
    extension-count="1" Number of extension test paths rendered along the selected extension path.
    show-bend-paths="true|false" Render arc-path-arc bend connector tests.
    bend-path-length="3rem" Test vertical bend connector length.
    show-anchor-chain="true|false" Render calculated nextAnchorPointChain markers.
    anchor-points="true|false" Render or disable this debug layer completely.
    test-node-index="1" Default visible trunk node counter for test elements.
    merge-node-index="1" Optional visible trunk node counter for merge.* test elements and their calculated chain.
    branch-node-index="1" Optional visible trunk node counter for branch.* test elements.

    Geometry rule:
    This component is independent from the existing trunk-anchor-* CSS vars.
    It renders calculated visible trunk node center points only. anchor-index
    is a 1-based visible node counter: 1 = trunk.start.node, 2 = trunk.main-1.node,
    3 = trunk.main-2.node. There is no index 0 and no trunk border/path edge
    anchor.

    Arc anchor rule:
    The lime arc anchors are calculated from the visible node center point,
    not from existing merge/branch CSS. They mark the four node-corner attach
    points where arc-ne/nw/se/sw can later be aligned:
    center +/- (node-half - path-half).

    Arc primitive contract:
    Arc primitives are neutral border/radius shapes. They must not define their
    own left/right/top/bottom defaults. The caller places them by their relevant
    box corner and then compensates for visible line thickness:
    arc-ne = bottom/right, offset left + down by path-width.
    arc-nw = bottom/left, offset right + down by path-width.
    arc-se = top/right, offset left + up by path-width.
    arc-sw = top/left, offset right + up by path-width.

    Horizontal path rule:
    Render a simple connector at each visible arc's N/S point:
    arc-ne = from N point to left.
    arc-nw = from N point to right.
    arc-se = from S point to left.
    arc-sw = from S point to right.
    Optional end arcs close the connector with the matching opposite arc:
    ne-left -> sw, nw-right -> se, se-left -> nw, sw-right -> ne.

    Bend path rule:
    The bend path tests render these explicit contracts:
    arc-se-path-arc-nw-left, arc-sw-path-arc-ne-right,
    arc-nw-path-arc-se-right, arc-ne-path-arc-sw-left.

    nextAnchorPointChain rule:
    The chain markers are calculated independently from rendered elements.
    Each element consumes the current anchor and returns a next anchor. Named
    anchors, such as extensionAnchorPoint, can be stored while the chain keeps
    moving.
--}}

@props([
    'startLength' => '5rem',
    'pathLengths' => ['4rem'],
    'pathWidth' => '0.25rem',
    'nodeSize' => '1rem',
    'color' => 'cyan',
    'arcColor' => 'lime',
    'showAxes' => '0.1rem',
    'showLabels' => true,
    'showArcAnchors' => true,
    'showArcAxes' => '0.1rem',
    'showArcLabels' => true,
    'showArcNe' => true,
    'showArcNw' => false,
    'showArcSe' => false,
    'showArcSw' => false,
    'showHorizontalPath' => false,
    'showHorizontalEndArcs' => false,
    'horizontalPathLength' => '6rem',
    'showExtensionPath' => false,
    'extensionTestPath' => 'ne-left',
    'extensionColor' => 'sky',
    'mergeExtensionColor' => null,
    'branchExtensionColor' => null,
    'extensionParentOffset' => null,
    'extensionHorizontalLength' => '4rem',
    'extensionVerticalLength' => '2rem',
    'extensionCount' => 1,
    'showBendPaths' => false,
    'bendPathLength' => '3rem',
    'showAnchorChain' => false,
    'anchorPoints' => true,
    'testNodeIndex' => null,
    'mergeNodeIndex' => null,
    'branchNodeIndex' => null,
])

@php
    $renderAnchorPoints = filter_var($anchorPoints, FILTER_VALIDATE_BOOLEAN);
    $resolveAxisWidth = function (mixed $value) use ($pathWidth): ?string {
        if (is_bool($value)) {
            return $value ? $pathWidth : null;
        }

        if (is_numeric($value)) {
            return (float) $value > 0 ? (string) $value : null;
        }

        $normalized = trim((string) $value);

        if ($normalized === '' || $normalized === '0' || strtolower($normalized) === 'false') {
            return null;
        }

        if (strtolower($normalized) === 'true') {
            return $pathWidth;
        }

        return $normalized;
    };
    $axisLineWidth = $resolveAxisWidth($showAxes);
    $arcAxisLineWidth = $resolveAxisWidth($showArcAxes);
    $lengths = collect(is_iterable($pathLengths) && ! is_string($pathLengths) ? $pathLengths : [$pathLengths])
        ->filter(fn ($length) => filled($length))
        ->values();

    $axisColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '6 182 212');
    $arcAxisColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($arcColor, '132 204 22');
    $resolvedMergeExtensionColor = filled($mergeExtensionColor) ? $mergeExtensionColor : $extensionColor;
    $resolvedBranchExtensionColor = filled($branchExtensionColor) ? $branchExtensionColor : $extensionColor;
    $extensionColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($extensionColor, '14 165 233');
    $mergeExtensionColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($resolvedMergeExtensionColor, '14 165 233');
    $branchExtensionColorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($resolvedBranchExtensionColor, '244 63 94');
    $resolvedExtensionTestPath = str_replace('_', '-', (string) $extensionTestPath);
    $resolvedExtensionParentOffset = filled($extensionParentOffset) ? $extensionParentOffset : $horizontalPathLength;
    $resolvedExtensionCount = max(1, min((int) $extensionCount, 8));
    $extensionRows = collect(range(1, $resolvedExtensionCount))
        ->map(function (int $number) use ($resolvedExtensionParentOffset, $extensionHorizontalLength) {
            $additionalOffset = $number - 1;
            $firstCounter = (($number - 1) * 3) + 1;

            return [
                'number' => $number,
                'connectorCounter' => $firstCounter,
                'arcCounter' => $firstCounter + 1,
                'verticalCounter' => $firstCounter + 2,
                'offset' => $additionalOffset === 0
                    ? $resolvedExtensionParentOffset
                    : "calc({$resolvedExtensionParentOffset} + ({$additionalOffset} * {$extensionHorizontalLength}))",
            ];
        });
    $nodeFlowHeight = "calc({$nodeSize} - ({$pathWidth} * 1))";
    $arcAnchorOffset = "calc(({$nodeSize} / 2) - ({$pathWidth} / 2))";
    $anchorExpressions = collect([$startLength]);
    $currentExpression = $startLength;

    foreach ($lengths as $length) {
        $currentExpression = "calc({$currentExpression} + {$nodeFlowHeight} + {$length})";
        $anchorExpressions->push($currentExpression);
    }

    $defaultTestAnchorIndex = max(1, min((int) ($testNodeIndex ?? 1), $anchorExpressions->count()));
    $mergeAnchorIndex = max(1, min((int) ($mergeNodeIndex ?? $defaultTestAnchorIndex), $anchorExpressions->count()));
    $branchAnchorIndex = max(1, min((int) ($branchNodeIndex ?? $defaultTestAnchorIndex), $anchorExpressions->count()));
    $mergeStartY = $anchorExpressions->get($mergeAnchorIndex - 1, $anchorExpressions->first());
    $chainArcDelta = "calc(var(--tw-graph-v2-arc-size) - var(--tw-graph-v2-path-width))";
    $chainPoints = collect();

    $mergeLeftStartX = "var(--tw-graph-v2-anchor-arc-offset)";
    $mergeStartPointY = "calc({$mergeStartY} - var(--tw-graph-v2-anchor-arc-offset))";

    $chainPoints->push([
        'number' => 1,
        'label' => 'merge left arc-se start',
        'name' => "merge.left.{$mergeAnchorIndex}.1.arc-se.start-e",
        'x' => $mergeLeftStartX,
        'y' => $mergeStartPointY,
    ]);

    $mergeLeftAfterArcX = "calc({$mergeLeftStartX} - {$chainArcDelta})";
    $mergeAfterArcY = "calc({$mergeStartPointY} - {$chainArcDelta})";

    $chainPoints->push([
        'number' => 2,
        'label' => 'merge left arc-se end',
        'name' => "merge.left.{$mergeAnchorIndex}.1.arc-se.end-s",
        'x' => $mergeLeftAfterArcX,
        'y' => $mergeAfterArcY,
    ]);

    $mergeLeftAfterPathX = "calc({$mergeLeftAfterArcX} - {$horizontalPathLength})";

    $chainPoints->push([
        'number' => 3,
        'label' => 'merge left horizontal end',
        'name' => "merge.left.{$mergeAnchorIndex}.2.right-left.end",
        'x' => $mergeLeftAfterPathX,
        'y' => $mergeAfterArcY,
    ]);

    $mergeLeftAfterEndArcX = "calc({$mergeLeftAfterPathX} - {$chainArcDelta})";
    $mergeAfterEndArcY = "calc({$mergeAfterArcY} - {$chainArcDelta})";

    $chainPoints->push([
        'number' => 4,
        'label' => 'merge left arc-nw end',
        'name' => "merge.left.{$mergeAnchorIndex}.3.arc-nw.end-w",
        'x' => $mergeLeftAfterEndArcX,
        'y' => $mergeAfterEndArcY,
        'class' => 'tw-graph-v2-anchor-chain-point-after-arc-nw',
    ]);

    $mergeRightStartX = "calc(var(--tw-graph-v2-anchor-arc-offset) * -1)";

    $chainPoints->push([
        'number' => 1,
        'label' => 'merge right arc-sw start',
        'name' => "merge.right.{$mergeAnchorIndex}.1.arc-sw.start-w",
        'x' => $mergeRightStartX,
        'y' => $mergeStartPointY,
    ]);

    $mergeRightAfterArcX = "calc({$mergeRightStartX} + {$chainArcDelta})";

    $chainPoints->push([
        'number' => 2,
        'label' => 'merge right arc-sw end',
        'name' => "merge.right.{$mergeAnchorIndex}.1.arc-sw.end-s",
        'x' => $mergeRightAfterArcX,
        'y' => $mergeAfterArcY,
    ]);

    $mergeRightAfterPathX = "calc({$mergeRightAfterArcX} + {$horizontalPathLength})";

    $chainPoints->push([
        'number' => 3,
        'label' => 'merge right horizontal end',
        'name' => "merge.right.{$mergeAnchorIndex}.2.left-right.end",
        'x' => $mergeRightAfterPathX,
        'y' => $mergeAfterArcY,
    ]);

    $mergeRightAfterEndArcX = "calc({$mergeRightAfterPathX} + {$chainArcDelta})";

    $chainPoints->push([
        'number' => 4,
        'label' => 'merge right arc-ne end',
        'name' => "merge.right.{$mergeAnchorIndex}.3.arc-ne.end-e",
        'x' => $mergeRightAfterEndArcX,
        'y' => $mergeAfterEndArcY,
        'class' => 'tw-graph-v2-anchor-chain-point-after-arc-ne',
    ]);
@endphp

@if ($renderAnchorPoints)
    <div
        {{ $attributes->class('tw-graph-v2-anchor-points')->style([
            '--tw-graph-v2-anchor-axis-rgb: ' . $axisColorRgb,
            '--tw-graph-v2-anchor-arc-axis-rgb: ' . $arcAxisColorRgb,
            '--tw-graph-v2-anchor-axis-line-width: ' . $axisLineWidth => filled($axisLineWidth),
            '--tw-graph-v2-anchor-arc-axis-line-width: ' . $arcAxisLineWidth => filled($arcAxisLineWidth),
            '--tw-graph-v2-anchor-extension-rgb: ' . $extensionColorRgb,
            '--tw-graph-v2-anchor-node-size: ' . $nodeSize,
            '--tw-graph-v2-path-width: ' . $pathWidth,
            '--tw-graph-v2-path-half: calc(' . $pathWidth . ' / 2)',
            '--tw-graph-v2-anchor-arc-offset: ' . $arcAnchorOffset,
            '--tw-graph-v2-local-horizontal-path-length: ' . $horizontalPathLength,
            '--tw-graph-v2-local-extension-parent-offset: ' . $resolvedExtensionParentOffset,
            '--tw-graph-v2-local-extension-horizontal-length: ' . $extensionHorizontalLength,
            '--tw-graph-v2-local-extension-vertical-length: ' . $extensionVerticalLength,
        ]) }}
    >
    @if (filter_var($showAnchorChain, FILTER_VALIDATE_BOOLEAN))
        <div class="tw-graph-v2-anchor-chain">
            @foreach ($chainPoints as $chainPoint)
                <span
                    class="tw-graph-v2-anchor-chain-point {{ $chainPoint['class'] ?? '' }}"
                    style="--tw-graph-v2-anchor-chain-x: {{ $chainPoint['x'] }}; --tw-graph-v2-anchor-chain-y: {{ $chainPoint['y'] }};"
                    title="{{ $chainPoint['name'] }} | {{ $chainPoint['label'] }}"
                    data-tw-graph-path="{{ $chainPoint['name'] }}"
                    data-tw-graph-path-full="{{ $chainPoint['name'] }} | {{ $chainPoint['label'] }}"
                    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPathFull || $el.dataset.twGraphPath)"
                >
                    <span class="tw-graph-v2-anchor-chain-dot"></span>
                    <span class="tw-graph-v2-anchor-chain-label">
                        {{ $chainPoint['number'] ?? $loop->iteration }}
                    </span>
                </span>
            @endforeach
        </div>
    @endif

    @foreach ($anchorExpressions as $index => $anchorExpression)
        @php
            $visibleAnchorIndex = $index + 1;
            $renderMergeTestElements = $visibleAnchorIndex === $mergeAnchorIndex;
            $renderBranchTestElements = $visibleAnchorIndex === $branchAnchorIndex;
        @endphp

        <div
            class="tw-graph-v2-anchor-point"
            style="--tw-graph-v2-anchor-y: {{ $anchorExpression }};"
            title="trunk.center.{{ $visibleAnchorIndex }}.anchor-point"
            data-tw-graph-path="trunk.center.{{ $visibleAnchorIndex }}.anchor-point"
            data-tw-graph-path-full="trunk.center.{{ $visibleAnchorIndex }}.anchor-point"
            x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPathFull || $el.dataset.twGraphPath)"
        >
            @if (filled($axisLineWidth))
                <span class="tw-graph-v2-anchor-point-axis-x"></span>
                <span class="tw-graph-v2-anchor-point-axis-y"></span>
            @endif
            <span class="tw-graph-v2-anchor-point-dot"></span>
            @if (filter_var($showLabels, FILTER_VALIDATE_BOOLEAN))
                <span class="tw-graph-v2-anchor-point-label">
                    {{ $index + 1 }}
                </span>
            @endif

            @if (filter_var($showArcAnchors, FILTER_VALIDATE_BOOLEAN))
                @foreach (['nw', 'ne', 'sw', 'se'] as $arcAnchor)
                    <span
                        class="tw-graph-v2-anchor-arc-point tw-graph-v2-anchor-arc-point-{{ $arcAnchor }}"
                        title="trunk.center.{{ $visibleAnchorIndex }}.anchor-point.arc-{{ $arcAnchor }}"
                        data-tw-graph-path="trunk.center.{{ $visibleAnchorIndex }}.anchor-point.arc-{{ $arcAnchor }}"
                        data-tw-graph-path-full="trunk.center.{{ $visibleAnchorIndex }}.anchor-point.arc-{{ $arcAnchor }}"
                        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPathFull || $el.dataset.twGraphPath)"
                    >
                        @if (filled($arcAxisLineWidth))
                            <span class="tw-graph-v2-anchor-arc-axis-x"></span>
                            <span class="tw-graph-v2-anchor-arc-axis-y"></span>
                        @endif
                        @if (filter_var($showArcLabels, FILTER_VALIDATE_BOOLEAN))
                            <span class="tw-graph-v2-anchor-arc-label">
                                {{ $arcAnchor }}
                            </span>
                        @endif

                        @if ($renderBranchTestElements && $arcAnchor === 'ne' && filter_var($showArcNe, FILTER_VALIDATE_BOOLEAN))
                            <x-ui.tw-graph-v2.elements.arc.ne
                                class="tw-graph-v2-anchor-test-arc tw-graph-v2-anchor-test-arc-ne"
                                color="{{ $arcColor }}"
                                :dev="true"
                                dev-path="tw-graph-v2.branch.left.{{ $visibleAnchorIndex }}.1.arc-ne.start-e.end-n"
                            />

                            @if (filter_var($showHorizontalPath, FILTER_VALIDATE_BOOLEAN))
                                <x-ui.tw-graph-v2.elements.path
                                    class="tw-graph-v2-anchor-test-horizontal-path tw-graph-v2-anchor-test-horizontal-path-ne"
                                    direction="horizontal"
                                    length="{{ $horizontalPathLength }}"
                                    color="{{ $arcColor }}"
                                    :dev="true"
                                    dev-path="tw-graph-v2.branch.left.{{ $visibleAnchorIndex }}.2.right-left"
                                />

                                @if (filter_var($showHorizontalEndArcs, FILTER_VALIDATE_BOOLEAN))
                                    <x-ui.tw-graph-v2.elements.arc.sw
                                        class="tw-graph-v2-anchor-test-horizontal-end-arc tw-graph-v2-anchor-test-horizontal-end-arc-ne"
                                        color="{{ $arcColor }}"
                                        :dev="true"
                                        dev-path="tw-graph-v2.branch.left.{{ $visibleAnchorIndex }}.3.arc-sw.start-e.end-s"
                                    />

                                    <x-ui.tw-graph-v2.elements.path
                                        class="tw-graph-v2-anchor-test-horizontal-end-vertical-path tw-graph-v2-anchor-test-horizontal-end-vertical-path-ne"
                                        direction="vertical"
                                        length="{{ $extensionVerticalLength }}"
                                        color="{{ $arcColor }}"
                                        :dev="true"
                                        dev-path="tw-graph-v2.branch.left.{{ $visibleAnchorIndex }}.4.bottom-top"
                                    />
                                @endif

                                @if (filter_var($showExtensionPath, FILTER_VALIDATE_BOOLEAN) && in_array($resolvedExtensionTestPath, ['ne-left', 'branch', 'all'], true))
                                    @foreach ($extensionRows as $extensionRow)
                                        @php($extensionBasePath = "branch.extension.left.{$visibleAnchorIndex}")
                                        <span
                                            class="tw-graph-v2-anchor-test-extension tw-graph-v2-anchor-test-extension-ne-left"
                                            style="--tw-graph-v2-local-extension-parent-offset: {{ $extensionRow['offset'] }}; --tw-graph-v2-anchor-extension-rgb: {{ $branchExtensionColorRgb }};"
                                        >
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-join"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.right-left.start"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.right-left.start"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>
                                            <x-ui.tw-graph-v2.elements.path
                                                class="tw-graph-v2-anchor-test-extension-horizontal-path"
                                                direction="horizontal"
                                                length="{{ $extensionHorizontalLength }}"
                                                color="{{ $resolvedBranchExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.right-left"
                                            />
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-after-horizontal"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.right-left.end"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.right-left.end"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>

                                            <x-ui.tw-graph-v2.elements.arc.sw
                                                class="tw-graph-v2-anchor-test-extension-end-arc"
                                                color="{{ $resolvedBranchExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['arcCounter'] }}.arc-sw.start-e.end-s"
                                            />
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-after-arc"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['arcCounter'] }}.arc-sw.end-s"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['arcCounter'] }}.arc-sw.end-s"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>

                                            <x-ui.tw-graph-v2.elements.path
                                                class="tw-graph-v2-anchor-test-extension-vertical-path"
                                                direction="vertical"
                                                length="{{ $extensionVerticalLength }}"
                                                color="{{ $resolvedBranchExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] }}.bottom-top"
                                            />
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-after-vertical"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] }}.bottom-top.end"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] }}.bottom-top.end"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>
                                        </span>
                                    @endforeach
                                @endif
                            @endif

                            @if (filter_var($showBendPaths, FILTER_VALIDATE_BOOLEAN))
                                <span
                                    class="tw-graph-v2-anchor-test-bend tw-graph-v2-anchor-test-bend-ne-sw-left"
                                    style="--tw-graph-v2-local-bend-path-length: {{ $bendPathLength }};"
                                >
                                    <x-ui.tw-graph-v2.elements.path
                                        class="tw-graph-v2-anchor-test-bend-path"
                                        length="{{ $bendPathLength }}"
                                        color="{{ $arcColor }}"
                                    />

                                    <x-ui.tw-graph-v2.elements.arc.sw
                                        class="tw-graph-v2-anchor-test-bend-end-arc"
                                        color="{{ $arcColor }}"
                                    />
                                </span>
                            @endif
                        @endif

                        @if ($renderBranchTestElements && $arcAnchor === 'nw' && filter_var($showArcNw, FILTER_VALIDATE_BOOLEAN))
                            <x-ui.tw-graph-v2.elements.arc.nw
                                class="tw-graph-v2-anchor-test-arc tw-graph-v2-anchor-test-arc-nw"
                                color="{{ $arcColor }}"
                                :dev="true"
                                dev-path="tw-graph-v2.branch.right.{{ $visibleAnchorIndex }}.1.arc-nw.start-w.end-n"
                            />

                            @if (filter_var($showHorizontalPath, FILTER_VALIDATE_BOOLEAN))
                                <x-ui.tw-graph-v2.elements.path
                                    class="tw-graph-v2-anchor-test-horizontal-path tw-graph-v2-anchor-test-horizontal-path-nw"
                                    direction="horizontal"
                                    length="{{ $horizontalPathLength }}"
                                    color="{{ $arcColor }}"
                                    :dev="true"
                                    dev-path="tw-graph-v2.branch.right.{{ $visibleAnchorIndex }}.2.left-right"
                                />

                                @if (filter_var($showHorizontalEndArcs, FILTER_VALIDATE_BOOLEAN))
                                    <x-ui.tw-graph-v2.elements.arc.se
                                        class="tw-graph-v2-anchor-test-horizontal-end-arc tw-graph-v2-anchor-test-horizontal-end-arc-nw"
                                        color="{{ $arcColor }}"
                                        :dev="true"
                                        dev-path="tw-graph-v2.branch.right.{{ $visibleAnchorIndex }}.3.arc-se.start-w.end-s"
                                    />

                                    <x-ui.tw-graph-v2.elements.path
                                        class="tw-graph-v2-anchor-test-horizontal-end-vertical-path tw-graph-v2-anchor-test-horizontal-end-vertical-path-nw"
                                        direction="vertical"
                                        length="{{ $extensionVerticalLength }}"
                                        color="{{ $arcColor }}"
                                        :dev="true"
                                        dev-path="tw-graph-v2.branch.right.{{ $visibleAnchorIndex }}.4.bottom-top"
                                    />
                                @endif

                                @if (filter_var($showExtensionPath, FILTER_VALIDATE_BOOLEAN) && in_array($resolvedExtensionTestPath, ['nw-right', 'branch', 'all'], true))
                                    @foreach ($extensionRows as $extensionRow)
                                        @php($extensionBasePath = "branch.extension.right.{$visibleAnchorIndex}")
                                        <span
                                            class="tw-graph-v2-anchor-test-extension tw-graph-v2-anchor-test-extension-nw-right"
                                            style="--tw-graph-v2-local-extension-parent-offset: {{ $extensionRow['offset'] }}; --tw-graph-v2-anchor-extension-rgb: {{ $branchExtensionColorRgb }};"
                                        >
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-join"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.left-right.start"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.left-right.start"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>
                                            <x-ui.tw-graph-v2.elements.path
                                                class="tw-graph-v2-anchor-test-extension-horizontal-path"
                                                direction="horizontal"
                                                length="{{ $extensionHorizontalLength }}"
                                                color="{{ $resolvedBranchExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.left-right"
                                            />
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-after-horizontal"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.left-right.end"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.left-right.end"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>

                                            <x-ui.tw-graph-v2.elements.arc.se
                                                class="tw-graph-v2-anchor-test-extension-end-arc"
                                                color="{{ $resolvedBranchExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['arcCounter'] }}.arc-se.start-w.end-s"
                                            />
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-after-arc"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['arcCounter'] }}.arc-se.end-s"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['arcCounter'] }}.arc-se.end-s"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>

                                            <x-ui.tw-graph-v2.elements.path
                                                class="tw-graph-v2-anchor-test-extension-vertical-path"
                                                direction="vertical"
                                                length="{{ $extensionVerticalLength }}"
                                                color="{{ $resolvedBranchExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] }}.bottom-top"
                                            />
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-after-vertical"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] }}.bottom-top.end"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] }}.bottom-top.end"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>
                                        </span>
                                    @endforeach
                                @endif
                            @endif

                            @if (filter_var($showBendPaths, FILTER_VALIDATE_BOOLEAN))
                                <span
                                    class="tw-graph-v2-anchor-test-bend tw-graph-v2-anchor-test-bend-nw-se-right"
                                    style="--tw-graph-v2-local-bend-path-length: {{ $bendPathLength }};"
                                >
                                    <x-ui.tw-graph-v2.elements.path
                                        class="tw-graph-v2-anchor-test-bend-path"
                                        length="{{ $bendPathLength }}"
                                        color="{{ $arcColor }}"
                                    />

                                    <x-ui.tw-graph-v2.elements.arc.se
                                        class="tw-graph-v2-anchor-test-bend-end-arc"
                                        color="{{ $arcColor }}"
                                    />
                                </span>
                            @endif
                        @endif

                        @if ($renderMergeTestElements && $arcAnchor === 'se' && filter_var($showArcSe, FILTER_VALIDATE_BOOLEAN))
                            <x-ui.tw-graph-v2.elements.arc.se
                                class="tw-graph-v2-anchor-test-arc tw-graph-v2-anchor-test-arc-se"
                                color="{{ $arcColor }}"
                                :dev="true"
                                dev-path="tw-graph-v2.merge.left.{{ $visibleAnchorIndex }}.1.arc-se.start-e.end-s"
                            />

                            @if (filter_var($showHorizontalPath, FILTER_VALIDATE_BOOLEAN))
                                <x-ui.tw-graph-v2.elements.path
                                    class="tw-graph-v2-anchor-test-horizontal-path tw-graph-v2-anchor-test-horizontal-path-se"
                                    direction="horizontal"
                                    length="{{ $horizontalPathLength }}"
                                    color="{{ $arcColor }}"
                                    :dev="true"
                                    dev-path="tw-graph-v2.merge.left.{{ $visibleAnchorIndex }}.2.right-left"
                                />

                                @if (filter_var($showHorizontalEndArcs, FILTER_VALIDATE_BOOLEAN))
                                    <x-ui.tw-graph-v2.elements.arc.nw
                                        class="tw-graph-v2-anchor-test-horizontal-end-arc tw-graph-v2-anchor-test-horizontal-end-arc-se"
                                        color="{{ $arcColor }}"
                                        :dev="true"
                                        dev-path="tw-graph-v2.merge.left.{{ $visibleAnchorIndex }}.3.arc-nw.start-n.end-w"
                                    />

                                    <x-ui.tw-graph-v2.elements.path
                                        class="tw-graph-v2-anchor-test-horizontal-end-vertical-path tw-graph-v2-anchor-test-horizontal-end-vertical-path-se"
                                        direction="vertical"
                                        length="{{ $extensionVerticalLength }}"
                                        color="{{ $arcColor }}"
                                        :dev="true"
                                        dev-path="tw-graph-v2.merge.left.{{ $visibleAnchorIndex }}.4.top-bottom"
                                    />

                                    <span
                                        class="tw-graph-v2-anchor-test-horizontal-end-vertical-point tw-graph-v2-anchor-test-horizontal-end-vertical-point-se"
                                        title="merge.left.{{ $visibleAnchorIndex }}.5.top-bottom.end"
                                        data-tw-graph-path="merge.left.{{ $visibleAnchorIndex }}.5.top-bottom.end"
                                        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                    ></span>

                                    <x-ui.tw-graph-v2.elements.path
                                        class="tw-graph-v2-anchor-test-merge-start-path tw-graph-v2-anchor-test-merge-start-path-se"
                                        direction="vertical"
                                        length="{{ $extensionVerticalLength }}"
                                        color="{{ $arcColor }}"
                                        :dev="true"
                                        dev-path="tw-graph-v2.merge.left.{{ $visibleAnchorIndex }}.5.start-south"
                                    />

                                @endif

                                @if (filter_var($showExtensionPath, FILTER_VALIDATE_BOOLEAN) && in_array($resolvedExtensionTestPath, ['se-left', 'merge', 'all'], true))
                                    @foreach ($extensionRows as $extensionRow)
                                        @php($extensionBasePath = "merge.extension.left.{$visibleAnchorIndex}")
                                        <span
                                            class="tw-graph-v2-anchor-test-extension tw-graph-v2-anchor-test-extension-se-left"
                                            style="--tw-graph-v2-local-extension-parent-offset: {{ $extensionRow['offset'] }}; --tw-graph-v2-anchor-extension-rgb: {{ $mergeExtensionColorRgb }};"
                                        >
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-join"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.right-left.start"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.right-left.start"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>
                                            <x-ui.tw-graph-v2.elements.path
                                                class="tw-graph-v2-anchor-test-extension-horizontal-path"
                                                direction="horizontal"
                                                length="{{ $extensionHorizontalLength }}"
                                                color="{{ $resolvedMergeExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.right-left"
                                            />
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-after-horizontal"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.right-left.end"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.right-left.end"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>

                                            <x-ui.tw-graph-v2.elements.arc.nw
                                                class="tw-graph-v2-anchor-test-extension-end-arc"
                                                color="{{ $resolvedMergeExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['arcCounter'] }}.arc-nw.start-n.end-w"
                                            />
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-after-arc"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['arcCounter'] }}.arc-nw.end-w"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['arcCounter'] }}.arc-nw.end-w"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>

                                            <x-ui.tw-graph-v2.elements.path
                                                class="tw-graph-v2-anchor-test-extension-vertical-path"
                                                direction="vertical"
                                                length="{{ $extensionVerticalLength }}"
                                                color="{{ $resolvedMergeExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] }}.top-bottom"
                                            />
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-after-vertical"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] }}.top-bottom.end"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] }}.top-bottom.end"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>

                                            <x-ui.tw-graph-v2.elements.path
                                                class="tw-graph-v2-anchor-test-extension-start-path"
                                                direction="vertical"
                                                length="{{ $extensionVerticalLength }}"
                                                color="{{ $resolvedMergeExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] + 1 }}.start-south"
                                            />
                                        </span>
                                    @endforeach
                                @endif
                            @endif

                            @if (filter_var($showBendPaths, FILTER_VALIDATE_BOOLEAN))
                                <span
                                    class="tw-graph-v2-anchor-test-bend tw-graph-v2-anchor-test-bend-se-nw-left"
                                    style="--tw-graph-v2-local-bend-path-length: {{ $bendPathLength }};"
                                >
                                    <x-ui.tw-graph-v2.elements.path
                                        class="tw-graph-v2-anchor-test-bend-path"
                                        length="{{ $bendPathLength }}"
                                        color="{{ $arcColor }}"
                                    />

                                    <x-ui.tw-graph-v2.elements.arc.nw
                                        class="tw-graph-v2-anchor-test-bend-end-arc"
                                        color="{{ $arcColor }}"
                                    />
                                </span>
                            @endif
                        @endif

                        @if ($renderMergeTestElements && $arcAnchor === 'sw' && filter_var($showArcSw, FILTER_VALIDATE_BOOLEAN))
                            <x-ui.tw-graph-v2.elements.arc.sw
                                class="tw-graph-v2-anchor-test-arc tw-graph-v2-anchor-test-arc-sw"
                                color="{{ $arcColor }}"
                                :dev="true"
                                dev-path="tw-graph-v2.merge.right.{{ $visibleAnchorIndex }}.1.arc-sw.start-w.end-s"
                            />

                            @if (filter_var($showHorizontalPath, FILTER_VALIDATE_BOOLEAN))
                                <x-ui.tw-graph-v2.elements.path
                                    class="tw-graph-v2-anchor-test-horizontal-path tw-graph-v2-anchor-test-horizontal-path-sw"
                                    direction="horizontal"
                                    length="{{ $horizontalPathLength }}"
                                    color="{{ $arcColor }}"
                                    :dev="true"
                                    dev-path="tw-graph-v2.merge.right.{{ $visibleAnchorIndex }}.2.left-right"
                                />

                                @if (filter_var($showHorizontalEndArcs, FILTER_VALIDATE_BOOLEAN))
                                    <x-ui.tw-graph-v2.elements.arc.ne
                                        class="tw-graph-v2-anchor-test-horizontal-end-arc tw-graph-v2-anchor-test-horizontal-end-arc-sw"
                                        color="{{ $arcColor }}"
                                        :dev="true"
                                        dev-path="tw-graph-v2.merge.right.{{ $visibleAnchorIndex }}.3.arc-ne.start-n.end-e"
                                    />

                                    <x-ui.tw-graph-v2.elements.path
                                        class="tw-graph-v2-anchor-test-horizontal-end-vertical-path tw-graph-v2-anchor-test-horizontal-end-vertical-path-sw"
                                        direction="vertical"
                                        length="{{ $extensionVerticalLength }}"
                                        color="{{ $arcColor }}"
                                        :dev="true"
                                        dev-path="tw-graph-v2.merge.right.{{ $visibleAnchorIndex }}.4.top-bottom"
                                    />

                                    <span
                                        class="tw-graph-v2-anchor-test-horizontal-end-vertical-point tw-graph-v2-anchor-test-horizontal-end-vertical-point-sw"
                                        title="merge.right.{{ $visibleAnchorIndex }}.5.top-bottom.end"
                                        data-tw-graph-path="merge.right.{{ $visibleAnchorIndex }}.5.top-bottom.end"
                                        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                    ></span>

                                    <x-ui.tw-graph-v2.elements.path
                                        class="tw-graph-v2-anchor-test-merge-start-path tw-graph-v2-anchor-test-merge-start-path-sw"
                                        direction="vertical"
                                        length="{{ $extensionVerticalLength }}"
                                        color="{{ $arcColor }}"
                                        :dev="true"
                                        dev-path="tw-graph-v2.merge.right.{{ $visibleAnchorIndex }}.5.start-south"
                                    />

                                @endif

                                @if (filter_var($showExtensionPath, FILTER_VALIDATE_BOOLEAN) && in_array($resolvedExtensionTestPath, ['sw-right', 'merge', 'all'], true))
                                    @foreach ($extensionRows as $extensionRow)
                                        @php($extensionBasePath = "merge.extension.right.{$visibleAnchorIndex}")
                                        <span
                                            class="tw-graph-v2-anchor-test-extension tw-graph-v2-anchor-test-extension-sw-right"
                                            style="--tw-graph-v2-local-extension-parent-offset: {{ $extensionRow['offset'] }}; --tw-graph-v2-anchor-extension-rgb: {{ $mergeExtensionColorRgb }};"
                                        >
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-join"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.left-right.start"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.left-right.start"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>
                                            <x-ui.tw-graph-v2.elements.path
                                                class="tw-graph-v2-anchor-test-extension-horizontal-path"
                                                direction="horizontal"
                                                length="{{ $extensionHorizontalLength }}"
                                                color="{{ $resolvedMergeExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.left-right"
                                            />
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-after-horizontal"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.left-right.end"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['connectorCounter'] }}.left-right.end"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>

                                            <x-ui.tw-graph-v2.elements.arc.ne
                                                class="tw-graph-v2-anchor-test-extension-end-arc"
                                                color="{{ $resolvedMergeExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['arcCounter'] }}.arc-ne.start-n.end-e"
                                            />
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-after-arc"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['arcCounter'] }}.arc-ne.end-e"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['arcCounter'] }}.arc-ne.end-e"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>

                                            <x-ui.tw-graph-v2.elements.path
                                                class="tw-graph-v2-anchor-test-extension-vertical-path"
                                                direction="vertical"
                                                length="{{ $extensionVerticalLength }}"
                                                color="{{ $resolvedMergeExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] }}.top-bottom"
                                            />
                                            <span
                                                class="tw-graph-v2-anchor-test-extension-point tw-graph-v2-anchor-test-extension-point-after-vertical"
                                                title="{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] }}.top-bottom.end"
                                                data-tw-graph-path="{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] }}.top-bottom.end"
                                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                            ></span>

                                            <x-ui.tw-graph-v2.elements.path
                                                class="tw-graph-v2-anchor-test-extension-start-path"
                                                direction="vertical"
                                                length="{{ $extensionVerticalLength }}"
                                                color="{{ $resolvedMergeExtensionColor }}"
                                                :dev="true"
                                                dev-path="tw-graph-v2.{{ $extensionBasePath }}.{{ $extensionRow['verticalCounter'] + 1 }}.start-south"
                                            />
                                        </span>
                                    @endforeach
                                @endif
                            @endif

                            @if (filter_var($showBendPaths, FILTER_VALIDATE_BOOLEAN))
                                <span
                                    class="tw-graph-v2-anchor-test-bend tw-graph-v2-anchor-test-bend-sw-ne-right"
                                    style="--tw-graph-v2-local-bend-path-length: {{ $bendPathLength }};"
                                >
                                    <x-ui.tw-graph-v2.elements.path
                                        class="tw-graph-v2-anchor-test-bend-path"
                                        length="{{ $bendPathLength }}"
                                        color="{{ $arcColor }}"
                                    />

                                    <x-ui.tw-graph-v2.elements.arc.ne
                                        class="tw-graph-v2-anchor-test-bend-end-arc"
                                        color="{{ $arcColor }}"
                                    />
                                </span>
                            @endif
                        @endif
                    </span>
                @endforeach
            @endif
        </div>
    @endforeach
    </div>
@endif
