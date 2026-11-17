<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess;
use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Gunreip\TranslationWorkbench\Support\WorkbenchTestEnvironment;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorLocales;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class SourceReplacementTable extends Component
{
    use WithPagination;

    #[Locked]
    public string $testToken = '';

    #[Locked]
    public bool $completed = false;

    #[Reactive]
    public string $search = '';

    #[Reactive]
    public bool $caseSensitive = false;

    #[Reactive]
    public bool $wholeWord = false;

    #[Reactive]
    public string $findingId = '';

    #[Reactive]
    public string $keyId = '';

    #[Locked]
    public array $previousFilters = [];

    #[Reactive]
    public int $perPage = 25;

    // Refresh the displayed checks after a recorded change or an explicit group check.
    #[Reactive]
    public int $lastEvent = 0;

    #[Reactive]
    public int $checkVersion = 0;

    #[Locked]
    public string $sortColumn = 'finding';

    #[Locked]
    public string $sortDirection = 'asc';

    public bool $includeAwaitingScan = false;

    public function updatedIncludeAwaitingScan(): void
    {
        $this->resetPage('sourceReplacementPage');
    }

    public function sortBy(string $column): void
    {
        if (! in_array($column, ['finding', 'key_id', 'key', 'source', 'expression'], true)) {
            return;
        }
        $this->sortDirection = $this->sortColumn === $column && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->sortColumn = $column;
        $this->resetPage($this->completed ? 'completedPage' : 'sourceReplacementPage');
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
        $this->resetPage($this->completed ? 'completedPage' : 'sourceReplacementPage');
    }

    public function updatedPerPage(): void
    {
        $this->resetPage($this->completed ? 'completedPage' : 'sourceReplacementPage');
    }

    public function render()
    {
        $filters = [$this->search, $this->perPage, $this->caseSensitive, $this->wholeWord, $this->findingId, $this->keyId];
        if ($this->previousFilters !== [] && $this->previousFilters !== $filters) {
            $this->resetPage($this->completed ? 'completedPage' : 'sourceReplacementPage');
        }
        $this->previousFilters = $filters;

        return view($this->completed ? 'translation-workbench::livewire.translation-workbench.tabs.completed.table' : 'translation-workbench::livewire.translation-workbench.tabs.source-replacement.table', [
            'sourceReplacementTasks' => app(GenerationEditor::class)->sourceReplacementPage(
                $this->search, (int) $this->getPage($this->completed ? 'completedPage' : 'sourceReplacementPage'), $this->perPage, $this->sortColumn, $this->sortDirection, $this->caseSensitive, $this->wholeWord, $this->findingId, $this->keyId, $this->completed, app(TranslationWorkbenchEditorLocales::class)->resolve()['active'], $this->includeAwaitingScan
            ),
        ]);
    }
}
