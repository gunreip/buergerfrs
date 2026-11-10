<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess;
use Gunreip\TranslationWorkbench\Generation\ScanRunCoordinator;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ScanProgress extends Component
{
    #[Locked]
    public string $scanWorkflowId;

    #[Locked]
    public array $scanWorkflow;

    public function refreshProgress(ScanRunCoordinator $coordinator, TranslationWorkbenchEditorAccess $access): void
    {
        $access->authorize();
        try {
            $status = $coordinator->status($this->scanWorkflowId, (int) auth()->id());
        } catch (\RuntimeException) {
            // The parent handles expired runs and displays the scanner error.
            $status = ['state' => 'failed'];
        }

        $next = ['state' => $status['state'], 'progress' => ['steps' => $status['progress']['steps'] ?? []]];
        if ($next === $this->scanWorkflow) {
            $this->skipRender();
            return;
        }

        $this->scanWorkflow = $next;
        if (! $this->running()) {
            $this->dispatch('scan-phase-ended');
        }
    }

    private function running(): bool
    {
        return in_array($this->scanWorkflow['state'], ['queued_preview', 'scanning', 'queued_apply', 'applying'], true);
    }

    public function render()
    {
        return view('translation-workbench::livewire.translation-workbench.scan.progress', [
            'scanState' => $this->scanWorkflow['state'],
            'scanRunning' => $this->running(),
        ]);
    }
}
