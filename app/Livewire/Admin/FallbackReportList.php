<?php

// app/Livewire/Admin/FallbackReportList.php

namespace App\Livewire\Admin;

use App\Models\FallbackReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Administrative list for fallback reports with review workflow controls.
 */
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

    /**
     * Reset pagination when search changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when status filter changes.
     */
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Normalize page size and reset pagination.
     */
    public function updatedPerPage(): void
    {
        $this->perPage = $this->normalizedPerPage();

        $this->resetPage();
    }

    /**
     * Sort by a whitelisted pseudo-field and toggle direction on repeat selection.
     */
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

    /**
     * Restore default filters and pagination size.
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'open';
        $this->perPage = 10;

        $this->resetPage();
    }

    /**
     * Mark a fallback report as reviewed.
     */
    public function markReviewed(int $reportId): void
    {
        $report = FallbackReport::query()->findOrFail($reportId);

        $report->markReviewed(
            userId: Auth::id(),
            note: 'Reviewed in fallback report list.',
        );
    }

    /**
     * Mark a fallback report as open/unreviewed.
     */
    public function markUnreviewed(int $reportId): void
    {
        $report = FallbackReport::query()->findOrFail($reportId);

        $report->markUnreviewed();
    }

    /**
     * Normalize selectable pagination size.
     */
    private function normalizedPerPage(): int
    {
        return in_array($this->perPage, [10, 25, 50, 100], true)
            ? $this->perPage
            : 10;
    }

    /**
     * Build filtered list and summary metrics for rendering.
     *
     * @return \Illuminate\Contracts\View\View
     */
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
                'open' => FallbackReport::query()->open()->count('*'),
                'reviewed' => FallbackReport::query()->reviewed()->count('*'),
                'total' => FallbackReport::query()->count('*'),
            ],
        ]);
    }

    /**
     * Apply full-text-like search across key fallback report fields.
     */
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
