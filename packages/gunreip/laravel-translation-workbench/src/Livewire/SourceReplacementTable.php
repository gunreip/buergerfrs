<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess;
use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Gunreip\TranslationWorkbench\Support\WorkbenchTestEnvironment;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class SourceReplacementTable extends Component
{
    use WithPagination;

    #[Locked]
    public string $testToken = '';

    #[Reactive]
    public string $search = '';

    #[Reactive]
    public int $perPage = 25;

    // Scalar reactive props let Livewire skip the child on unchanged parent updates.
    #[Reactive]
    public string $selectedIds = '';

    // Refresh the displayed checks after a recorded change or an explicit group check.
    #[Reactive]
    public int $lastEvent = 0;

    #[Reactive]
    public int $checkVersion = 0;

    #[Locked]
    public string $sortColumn = 'finding';

    #[Locked]
    public string $sortDirection = 'asc';

    public function sortBy(string $column): void
    {
        if (! in_array($column, ['finding', 'key_id', 'key', 'source', 'expression'], true)) {
            return;
        }
        $this->sortDirection = $this->sortColumn === $column && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->sortColumn = $column;
        $this->resetPage('sourceReplacementPage');
    }

    public function boot(TranslationWorkbenchEditorAccess $access): void
    {
        $access->authorize();
    }

    public function hydrate(): void
    {
        app(WorkbenchTestEnvironment::class)->activate($this->testToken);
    }

    public function updatedSearch(): void
    {
        $this->resetPage('sourceReplacementPage');
    }

    public function updatedPerPage(): void
    {
        $this->resetPage('sourceReplacementPage');
    }

    public function render()
    {
        return view('translation-workbench::livewire.translation-workbench.tabs.source-replacement.table', [
            'selectedFindings' => $this->selectedIds === '' ? [] : array_flip(explode(',', $this->selectedIds)),
            'sourceReplacementTasks' => app(GenerationEditor::class)->sourceReplacementPage(
                $this->search, (int) $this->getPage('sourceReplacementPage'), $this->perPage, $this->sortColumn, $this->sortDirection
            ),
        ]);
    }
}
