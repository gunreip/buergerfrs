<?php

namespace Gunreip\TranslationWorkbench\View\Components;

use Gunreip\TranslationWorkbench\Support\HeadingCounter;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class HeadingCounterGroup extends Component
{
    public HeadingCounter $headingCounter;

    public function __construct(string $group)
    {
        // Owned by this component instance, never shared across renders or requests.
        $this->headingCounter = new HeadingCounter($group);
    }

    public function render(): View
    {
        return view('translation-workbench::components.ui.common.heading-counter-group');
    }
}
