<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Lazy;
use Livewire\Component;

/** The connected Overview graph renders in its own request, independently of page navigation. */
#[Lazy]
class TwGraphOverview extends Component
{
    public bool $dev = true;

    public bool $coordinates = false;

    public function placeholder(): View
    {
        return view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.placeholder');
    }

    public function render(): View
    {
        return view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.livewire');
    }
}
