<?php

namespace Gunreip\TranslationWorkbench\View\Components;

use Gunreip\TranslationWorkbench\Support\TwGraph\CanvasDiagnostics;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class TwGraph extends Component
{
    public readonly CanvasDiagnostics $twGraphDiagnostics;

    public function __construct(
        public mixed $protocol = [],
        public mixed $graphId = null,
        public mixed $dev = false,
        public mixed $coordinates = false,
        public mixed $color = null,
        public mixed $pathTone = true,
        public mixed $lineLength = null,
        public mixed $lineWidth = null,
        public mixed $nodeSize = null,
        public mixed $arcRadius = null,
        public mixed $capLength = null,
        public mixed $bridgeLength = null,
        public mixed $stemLength = null,
        public mixed $connectorLength = null,
        public mixed $connectorGap = null,
        public mixed $labelGap = null,
        public mixed $horizontalPadding = null,
        public mixed $minWidth = null,
        public mixed $minHeight = null,
    ) {
        // Available while Blade renders the slot, before the canvas view itself.
        $this->twGraphDiagnostics = new CanvasDiagnostics($dev, $coordinates);
    }

    public function render(): View
    {
        return view('translation-workbench::components.ui.tw-graph');
    }
}
