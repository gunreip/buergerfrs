<?php

// app/Livewire/Admin/TranslationList.php

namespace App\Livewire\Admin;

use App\Models\TranslationKey;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class TranslationList extends Component
{
    use WithoutUrlPagination;
    use WithPagination;

    private const PROBLEM_STATUSES = [
        'missing',
        'dynamic',
    ];

    public string $search = '';

    public string $status = 'all';

    public bool $onlyProblems = false;

    public string $languageFilter = '';

    public string $fileFilter = '';

    public int $perPage = 25;

    public ?int $selectedTranslationKeyId = null;

    public bool $translationKeyModalOpen = false;

    public array $statusOptions = [
        'all',
        'ok',
        'missing',
        'native',
        'dynamic',
        'obsolete',
        'invalid',
    ];

    public function updating(string $property): void
    {
        if (in_array($property, [
            'search',
            'status',
            'onlyProblems',
            'languageFilter',
            'fileFilter',
            'perPage',
        ], true)) {
            $this->resetPage();
        }
    }

    public function goToPage(int|string $page): void
    {
        $page = (int) $page;

        $this->setPage(max(1, min($page, $this->getPageCount())));
    }

    public function goToFirstPage(): void
    {
        $this->setPage(1);
    }

    public function goToPreviousPage(): void
    {
        $this->previousPage();
    }

    public function goToNextPage(): void
    {
        $this->nextPage();
    }

    public function goToLastPage(): void
    {
        $this->setPage($this->getPageCount());
    }

    public function setStatus(string $status): void
    {
        if (! in_array($status, $this->statusOptions, true)) {
            $status = 'all';
        }

        $this->status = $status;
        $this->resetPage();
    }

    public function toggleOnlyProblems(): void
    {
        $this->onlyProblems = ! $this->onlyProblems;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = 'all';
        $this->onlyProblems = false;
        $this->languageFilter = '';
        $this->fileFilter = '';
        $this->perPage = 25;

        $this->resetPage();
    }

    public function openTranslationKey(int $translationKeyId): void
    {
        $this->selectedTranslationKeyId = $translationKeyId;
        $this->translationKeyModalOpen = true;
    }

    public function closeTranslationKey(): void
    {
        $this->translationKeyModalOpen = false;
        $this->selectedTranslationKeyId = null;
    }

    public function hasActiveFilters(): bool
    {
        return trim($this->search) !== ''
            || $this->status !== 'all'
            || $this->onlyProblems
            || $this->languageFilter !== ''
            || $this->fileFilter !== ''
            || $this->perPage !== 25;
    }

    private function translationKeyQuery(): Builder
    {
        return TranslationKey::query()
            ->with([
                'values' => fn($query) => $query->orderBy('locale'),
            ])
            ->withCount('usages')
            ->when($this->status !== 'all', fn(Builder $query): Builder => $query->where('status', $this->status))
            ->when($this->onlyProblems, fn(Builder $query): Builder => $query->whereIn('status', self::PROBLEM_STATUSES))
            ->when($this->languageFilter !== '', function (Builder $query): Builder {
                return $query->whereHas('values', function (Builder $query): void {
                    $query->where('locale', $this->languageFilter);
                });
            })
            ->when($this->fileFilter !== '', fn(Builder $query): Builder => $query->where('group', $this->fileFilter))
            ->when($this->search !== '', function (Builder $query): Builder {
                $search = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $this->search) . '%';

                return $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('key', 'ilike', $search)
                        ->orWhere('suggested_key', 'ilike', $search)
                        ->orWhere('native_text', 'ilike', $search)
                        ->orWhereHas('values', function (Builder $query) use ($search): void {
                            $query->where('value', 'ilike', $search);
                        })
                        ->orWhereHas('usages', function (Builder $query) use ($search): void {
                            $query
                                ->where('file', 'ilike', $search)
                                ->orWhere('raw', 'ilike', $search);
                        });
                });
            })
            ->latest('updated_at');
    }

    private function getPageCount(): int
    {
        $total = $this->translationKeyQuery()->count();

        return max(1, (int) ceil($total / $this->normalizedPerPage()));
    }

    public function render(): View
    {
        $statusCounts = TranslationKey::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn($value) => (int) $value)
            ->all();

        $total = array_sum($statusCounts);

        $problemCount = collect(self::PROBLEM_STATUSES)
            ->sum(fn(string $status): int => (int) ($statusCounts[$status] ?? 0));

        $query = $this->translationKeyQuery();

        $filteredTotal = (clone $query)->count();

        $selectedTranslationKey = $this->selectedTranslationKeyId
            ? TranslationKey::query()
            ->with([
                'values' => fn($query) => $query->orderBy('locale'),
                'usages' => fn($query) => $query->orderBy('file')->orderBy('id'),
            ])
            ->find($this->selectedTranslationKeyId)
            : null;

        $locales = \App\Models\TranslationValue::query()
            ->select('locale')
            ->distinct()
            ->orderBy('locale')
            ->pluck('locale')
            ->all();

        $translationFiles = TranslationKey::query()
            ->whereNotNull('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->all();

        return view('components.admin.⚡translation-list', [
            'translationKeys' => $query->paginate($this->normalizedPerPage()),
            'statusCounts' => $statusCounts,
            'total' => $total,
            'filteredTotal' => $filteredTotal,
            'problemStatuses' => self::PROBLEM_STATUSES,
            'problemCount' => $problemCount,
            'hasActiveFilters' => $this->hasActiveFilters(),
            'locales' => $locales,
            'translationFiles' => $translationFiles,
            'selectedTranslationKey' => $selectedTranslationKey,
        ]);
    }

    private function normalizedPerPage(): int
    {
        return in_array($this->perPage, [10, 25, 50, 100], true)
            ? $this->perPage
            : 10;
    }
}
