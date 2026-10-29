<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess;
use Gunreip\TranslationWorkbench\Support\GenerationCaseDecisions;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;
use Gunreip\TranslationWorkbench\Support\WorkbenchTestEnvironment;

class SourceCaseReview extends Component
{
    use WithPagination;
    use Concerns\HasWorkflowReasons;

    #[Locked]
    public array $savedUsageReasons = [];

    protected function workflowReasonFields(): array
    {
        return ['reason' => 'usage'];
    }

    protected function savedWorkflowReasons(string $field, string $kind): array
    {
        return $this->savedUsageReasons;
    }


    #[Locked]
    public string $testToken = '';

    public function mount(string $testToken = ''): void
    {
        $this->testToken = $testToken;
        app(WorkbenchTestEnvironment::class)->activate($testToken);
    }

    public function hydrate(): void
    {
        app(WorkbenchTestEnvironment::class)->activate($this->testToken);
    }

    public string $search = '';

    public string $filter = 'open';

    #[Locked]
    public string $sortColumn = 'source';

    #[Locked]
    public string $sortDirection = 'asc';

    public function sortBy(string $column): void
    {
        if (! in_array($column, ['source', 'expression', 'status'], true)) {
            return;
        }
        $this->sortDirection = $this->sortColumn === $column && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->sortColumn = $column;
        $this->resetPage('sourceCasesPage');
    }


    public bool $modalOpen = false;

    public string $decision = '';

    public string $reason = '';

    public string $notice = '';

    #[Locked]
    public string $selected = '';

    #[Locked]
    public int $expectedId = 0;

    public int $perPage = 10;

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, [10, 25, 50, 100], true)) {
            $this->perPage = 10;
        }
        $this->resetPage('sourceCasesPage');
    }

    public function updatedSearch(): void
    {
        $this->resetPage('sourceCasesPage');
    }

    public function updatedFilter(): void
    {
        $this->resetPage('sourceCasesPage');
    }

    public function open(string $id): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        $service = app(GenerationCaseDecisions::class);
        $snapshot = $service->snapshot();
        abort_unless(isset($snapshot['cases'][$id]), 404);
        $decisions = $service->decisions($service->connection($snapshot));
        $this->savedUsageReasons = array_values(array_unique(array_column($decisions, 'reason')));
        $previous = $decisions[$id] ?? null;
        $this->selected = $id;
        $this->expectedId = (int) ($previous['id'] ?? 0);
        $this->decision = $previous['decision'] ?? '';
        $this->reason = $previous['reason'] ?? '';
        $this->resetValidation();
        $this->modalOpen = true;
    }

    public function save(): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        $this->validate(['decision' => 'required|in:translate,database_value', 'reason' => 'required|string|min:3|max:2000']);
        $service = app(GenerationCaseDecisions::class);
        try {
            $snapshot = $service->snapshot();
            if (! isset($snapshot['cases'][$this->selected])) {
                throw new \RuntimeException('The case is no longer available');
            }
            $service->save($service->connection($snapshot), $snapshot['cases'][$this->selected], $this->decision, $this->reason, (int) auth()->id(), $this->expectedId);
        } catch (\RuntimeException $error) {
            $this->addError('decision', $error->getMessage());

            return;
        }
        $this->modalOpen = false;
        $this->notice = __('Decision saved. No translation or source file was changed.');
    }

    public function render()
    {
        $service = app(GenerationCaseDecisions::class);
        $snapshot = null;
        $decisions = [];
        try {
            $snapshot = $service->snapshot();
            $decisions = $service->decisions($service->connection($snapshot));
        } catch (\Throwable $error) {
            report($error);
            $snapshot = null;
        }
        $all = collect($snapshot['cases'] ?? []);
        $rows = $all->filter(fn ($case, $id) => ($this->filter !== 'open' || ! isset($decisions[$id]))
            && str_contains(mb_strtolower($case['source_path'].' '.$case['raw_expression']), mb_strtolower(trim($this->search))));
        // Sort the full filtered collection before pagination; retain capture IDs as keys.
        $sortKeys = [];
        foreach ($rows as $id => $case) {
            $source = [$case['source_path'], (int) $case['source_line'], (int) ($case['source_column'] ?? 0)];
            $sortKeys[$id] = match ($this->sortColumn) {
                'expression' => [$case['raw_expression'], ...$source, $id],
                'status' => [match ($decisions[$id]['decision'] ?? '') {
                    'translate' => 1, 'database_value' => 2, default => 0,
                }, ...$source, $id],
                default => [...$source, $id],
            };
        }
        $direction = $this->sortDirection === 'desc' ? -1 : 1;
        $ordered = $rows->all();
        uksort($ordered, fn ($left, $right) => ($sortKeys[$left] <=> $sortKeys[$right]) * $direction);
        $rows = collect($ordered);
        $page = max(1, min((int) $this->getPage('sourceCasesPage'), max(1, (int) ceil($rows->count() / $this->perPage))));
        $selectedCase = $snapshot['cases'][$this->selected] ?? null;
        $sourceChanged = $selectedCase !== null && @hash_file('sha256', app(WorkbenchTestEnvironment::class)->root().'/'.$selectedCase['source_path']) !== ($selectedCase['source_sha256'] ?? null);

        return view('translation-workbench::livewire.translation-workbench.tabs.source-usage.content', [
            'ready' => $snapshot !== null, 'decisions' => $decisions,
            'databaseName' => $snapshot['database'] ?? '',
            'schemaName' => $snapshot['generation']['schema'] ?? '',
            'openCount' => $all->filter(fn ($case, $id) => ! isset($decisions[$id]))->count(),
            'rows' => new LengthAwarePaginator($rows->slice(($page - 1) * $this->perPage, $this->perPage), $rows->count(), $this->perPage, $page, ['pageName' => 'sourceCasesPage']),
            'selectedCase' => $selectedCase, 'sourceChanged' => $sourceChanged,
        ]);
    }
}
