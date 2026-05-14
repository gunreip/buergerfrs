<?php

// app/Livewire/Admin/FallbackReportList.php

namespace App\Livewire\Admin;

use App\Models\FallbackReport;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class FallbackReportList extends Component
{
    use WithPagination;

    private const SORT_FIELDS = [
        'id' => 'id',
        'status' => 'reviewed',
        'type' => 'type',
        'key' => 'key',
        'fallback' => 'fallback',
        'count' => 'count',
        'last_seen_at' => 'last_seen_at',
    ];

    public string $search = '';

    public string $statusFilter = 'open';

    public string $sortField = 'last_seen_at';

    public string $sortDirection = 'desc';

    public int $perPage = 25;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = $this->normalizedPerPage();

        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! array_key_exists($field, self::SORT_FIELDS)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            $this->resetPage();

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'open';
        $this->perPage = 10;

        $this->resetPage();
    }

    public function markReviewed(int $reportId): void
    {
        $report = FallbackReport::query()->findOrFail($reportId);

        $report->markReviewed(
            userId: auth()->id(),
            note: 'Reviewed in fallback report list.',
        );
    }

    public function markUnreviewed(int $reportId): void
    {
        $report = FallbackReport::query()->findOrFail($reportId);

        $report->markUnreviewed();
    }

    private function normalizedPerPage(): int
    {
        return in_array($this->perPage, [10, 25, 50, 100], true)
            ? $this->perPage
            : 10;
    }

    public function render()
    {
        $query = FallbackReport::query()
            ->when($this->statusFilter === 'open', fn(Builder $query): Builder => $query->open())
            ->when($this->statusFilter === 'reviewed', fn(Builder $query): Builder => $query->reviewed())
            ->when(trim($this->search) !== '', fn(Builder $query): Builder => $this->applySearch($query));

        $sortColumn = self::SORT_FIELDS[$this->sortField] ?? 'last_seen_at';

        $reports = $query
            ->orderBy($sortColumn, $this->sortDirection)
            ->orderByDesc('id')
            ->paginate($this->normalizedPerPage());

        return view('components.admin.⚡fallback-report-list', [
            'reports' => $reports,
            'summary' => [
                'open' => FallbackReport::query()->open()->count(),
                'reviewed' => FallbackReport::query()->reviewed()->count(),
                'total' => FallbackReport::query()->count(),
            ],
        ]);
    }

    private function applySearch(Builder $query): Builder
    {
        $search = trim($this->search);

        return $query->where(function (Builder $query) use ($search): void {
            if (is_numeric($search)) {
                $query->orWhere('id', (int) $search)
                    ->orWhere('count', (int) $search);
            }

            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';

            $query->orWhere('type', 'ILIKE', $like)
                ->orWhere('key', 'ILIKE', $like)
                ->orWhere('fallback', 'ILIKE', $like)
                ->orWhere('fingerprint', 'ILIKE', $like)
                ->orWhereRaw('context::text ILIKE ?', [$like]);
        });
    }
}
