<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use Gunreip\TranslationWorkbench\Support\CurrentScanPreview;
use Gunreip\TranslationWorkbench\Support\RescanReviewPreview;
use Gunreip\TranslationWorkbench\Support\RuntimeOptionPreview;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class CurrentSourceReview extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filter = 'review';

    public string $selected = '';

    public string $rescanSelected = '';

    public string $rescanSearch = '';

    public function updatedRescanSearch(): void
    {
        $this->rescanSelected = '';
        $this->resetPage('rescanFilesPage');
        $this->resetPage('rescanIssuesPage');
    }

    public function updatedRescanSelected(): void
    {
        $this->resetPage('rescanIssuesPage');
    }

    public function updatedSearch(): void
    {
        $this->resetSelection();
    }

    public function updatedFilter(): void
    {
        $this->resetSelection();
    }

    public function updatedSelected(): void
    {
        $this->resetPage('scanSourcesPage');
    }

    private function resetSelection(): void
    {
        $this->selected = '';
        $this->resetPage('currentScanPage');
        $this->resetPage('scanSourcesPage');
    }

    public function render()
    {
        $reader = app(CurrentScanPreview::class);
        $snapshot = $reader->load($this->filter);
        $detail = $reader->detail($snapshot, $this->selected);
        $search = mb_strtolower(trim($this->search));
        $groups = collect($snapshot['groups'] ?? [])->filter(fn ($row) => ($this->filter === 'all' || ($this->filter === 'review' ? in_array($row['state'], ['key_review', 'expression_review']) : $row['state'] === $this->filter))
            && ($search === '' || str_contains(mb_strtolower($row['title']), $search)))->values();
        $paginate = function ($items, $name) {
            $items = collect($items);
            $page = max(1, min((int) $this->getPage($name), max(1, (int) ceil($items->count() / 20))));

            return new LengthAwarePaginator($items->slice(($page - 1) * 20, 20), $items->count(), 20, $page, ['pageName' => $name]);
        };

        $rescanReader = app(RescanReviewPreview::class);
        $rescan = $rescanReader->load();
        $rescanDetail = $rescanReader->detail($rescan, $this->rescanSelected);
        $rescanFiles = collect($rescan['files'] ?? [])->filter(fn ($file) => str_contains(mb_strtolower($file['path']), mb_strtolower(trim($this->rescanSearch))));

        return view('translation-workbench::livewire.entries.current-source-review', [
            'runtimeOptions' => app(RuntimeOptionPreview::class)->load(),
            'rescan' => $rescan, 'rescanDetail' => $rescanDetail, 'rescanFiles' => $paginate($rescanFiles, 'rescanFilesPage'),
            'rescanIssues' => $paginate($rescanDetail['issues'] ?? [], 'rescanIssuesPage'),
            'snapshot' => $snapshot, 'detail' => $detail, 'groups' => $paginate($groups, 'currentScanPage'),
            'sources' => $paginate($detail['findings'] ?? [], 'scanSourcesPage'),
        ]);
    }
}
