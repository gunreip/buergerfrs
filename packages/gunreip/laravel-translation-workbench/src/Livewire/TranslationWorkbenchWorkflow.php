<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class TranslationWorkbenchWorkflow extends Component
{
    public string $activeTab = 'legacy';

    public function updatedActiveTab(): void
    {
        if (! in_array($this->activeTab, ['legacy', 'workbench', 'target', 'write-contract'], true)) {
            $this->activeTab = 'legacy';
        }
    }

    public function render(): View
    {
        return view('translation-workbench::livewire.workflow.index');
    }
}
