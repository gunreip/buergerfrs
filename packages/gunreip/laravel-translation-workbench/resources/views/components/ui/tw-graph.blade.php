{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph.blade.php --}}
{{--
    Authoring graph renderer baseline.

    Package rule:
    tw-graph is the new authoring family for the translation-workbench package.
    It was copied from tw-graph-protocol as a working baseline; future graph API
    changes belong here while tw-graph-protocol remains the frozen reference.

    Usage:
    <x-translation-workbench::ui.tw-graph graph-id="example" :coordinates="false">
        <x-translation-workbench::ui.tw-graph.strang.trunk />
    </x-translation-workbench::ui.tw-graph>
--}}

@props([
    'protocol' => [],
    'graphId' => null,
    'dev' => false,
    'coordinates' => false,
    'color' => null,
    'pathTone' => true,
    'lineLength' => null,
    'lineWidth' => null,
    'nodeSize' => null,
    'arcSize' => null,
    'capLength' => null,
    'bridgeLength' => null,
    'stemLength' => null,
    'connectorLength' => null,
    'connectorGap' => null,
    'labelGap' => null,
    'horizontalPadding' => null,
    'minWidth' => null,
    'minHeight' => null,
])

@php
    $dev = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($dev);
    $surfacePaths = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($pathTone, true);
    $showCoordinates = filter_var($coordinates, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $coordinates;
    $lineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($lineLength, null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('line_length', '4rem'));
    $lineWidth = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($lineWidth, null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('line_width', '0.25rem'));
    $nodeSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($nodeSize, null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('node_size', '0.95rem'));
    $arcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($arcSize, null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('arc_size', '2.75rem'));
    $capLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($capLength, null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('cap_length', '1.75rem'));
    $bridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($bridgeLength, null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('bridge_length', $lineLength));
    $stemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($stemLength, null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('stem_length', $lineLength));
    $connectorLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($connectorLength, null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('connector_length', '2rem'));
    $connectorGap = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($connectorGap, null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('connector_gap', '0.25rem'));
    $minHeight = $slot->isNotEmpty()
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($minHeight, null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('min_height', '52rem'))
        : $minHeight;
    $horizontalPadding = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($horizontalPadding, null, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('horizontal_padding', '12rem'));
    $context = \Gunreip\TranslationWorkbench\Support\TwGraph\RenderContext::make(
        (array) $protocol,
        $graphId,
        $color,
        $lineWidth,
        $nodeSize,
        $arcSize,
        $minWidth,
        $minHeight,
    );
@endphp

<div class="tw-graph-protocol-viewport">
    <div
        data-tw-graph-dev="{{ $dev ? 'true' : 'false' }}"
        data-tw-graph-direction="{{ $context['direction'] }}"
        data-tw-graph-path-tone="{{ $surfacePaths ? 'surface' : 'line' }}"
        {{ $attributes->merge(['id' => $context['graphId']])->class(['tw-graph-protocol', 'tw-graph-protocol-coordinates-disabled' => !$showCoordinates])->style([
                '--tw-graph-protocol-color-rgb: ' . $context['colorRgb'],
                '--tw-graph-protocol-color-alpha: ' . ($dev ? '0.5' : '1'),
                '--tw-graph-protocol-min-width: ' . $context['minWidth'],
                '--tw-graph-protocol-min-height: ' . $context['minHeight'],
                '--tw-graph-protocol-path-width: ' . $context['pathWidth'],
                '--tw-graph-protocol-node-size: ' . $context['nodeSize'],
                '--tw-graph-protocol-arc-size: ' . $context['arcSize'],
            ]) }}
>
	    @if ($slot->isNotEmpty())
	        <div class="tw-graph-protocol-canvas tw-graph-protocol-canvas-slot content-center">
	            {{ $slot }}
                <x-translation-workbench::ui.tw-graph.line-jump-templates />
                <x-translation-workbench::ui.tw-graph.return-colors :graph-id="$context['graphId']" />
                @if ($dev && $showCoordinates)
                    <x-translation-workbench::ui.tw-graph.canvas-dimensions
                        :min-width="$context['minWidth']"
                        :min-height="$context['minHeight']"
                        :horizontal-padding="$horizontalPadding"
                    />
                @endif

                @foreach (\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::forcedNodes($context['graphId']) as $forcedNode)
                    <x-translation-workbench::ui.tw-graph.primitives.node
                        :id="$forcedNode['key'] . '.forced-node'"
                        :anchor-x="$forcedNode['x']"
                        :anchor-y="$forcedNode['y']"
                        :color="data_get($forcedNode, 'color', $color ?? 'zinc')"
                        :z-index="data_get($forcedNode, 'zIndex', 30)"
                    />
                @endforeach

	                <x-translation-workbench::ui.tw-graph.canvas-metrics
                    :graph-id="$context['graphId']"
                    :dev="$dev"
                    :coordinates="$showCoordinates"
                    :horizontal-padding="$horizontalPadding"
            />
        </div>
    @else
        <x-translation-workbench::ui.tw-graph.canvas
                :protocol="$protocol"
                :direction="$context['direction']"
                :dev="$dev"
                :coordinates="$showCoordinates"
                :min-width="$context['minWidth']"
                :min-height="$context['minHeight']"
            />
        @endif
    </div>
</div>
