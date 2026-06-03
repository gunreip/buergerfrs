<?php

// app/Livewire/Admin/CountryReferenceList.php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Models\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Administrative country reference list with audit-oriented filters and metrics.
 */
class CountryReferenceList extends Component
{
    use InteractsWithUserSettings;
    use WithPagination;

    private const UI_STATE_SETTING_KEY = 'ui.pages.admin_country_reference_list';

    public string $search = '';

    public string $regionFilter = '';

    public string $statusFilter = '';

    public string $membershipFilter = '';

    public string $dataFilter = '';

    public int $perPage = 25;

    public string $sortField = 'iso2';

    public string $sortDirection = 'asc';

    public function mount(): void
    {
        $state = $this->userSetting(self::UI_STATE_SETTING_KEY, []);

        if (is_array($state)) {
            $this->search = trim((string) ($state['search'] ?? $this->search));
            $this->regionFilter = trim((string) ($state['regionFilter'] ?? $this->regionFilter));
            $this->statusFilter = trim((string) ($state['statusFilter'] ?? $this->statusFilter));
            $this->membershipFilter = trim((string) ($state['membershipFilter'] ?? $this->membershipFilter));
            $this->dataFilter = trim((string) ($state['dataFilter'] ?? $this->dataFilter));
            $this->perPage = (int) ($state['perPage'] ?? $this->perPage);
            $this->perPage = $this->normalizedPerPage();

            $sortField = trim((string) ($state['sortField'] ?? $this->sortField));
            $this->sortField = in_array($sortField, [
                'id',
                'iso2',
                'iso3',
                'name',
                'official_name',
                'phone_code',
                'region',
                'subregion',
                'capital',
                'subdivisions_count',
                'is_active',
            ], true) ? $sortField : 'iso2';

            $sortDirection = trim((string) ($state['sortDirection'] ?? $this->sortDirection));
            $this->sortDirection = in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'asc';
        }

        $this->setPage(1);
    }

    /**
     * Reset pagination when a filter changes.
     */
    public function updating(string $property): void
    {
        if (in_array($property, [
            'search',
            'regionFilter',
            'statusFilter',
            'membershipFilter',
            'dataFilter',
            'perPage',
        ], true)) {
            $this->resetPage();
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'search',
            'regionFilter',
            'statusFilter',
            'membershipFilter',
            'dataFilter',
        ], true)) {
            $this->persistUiState();
        }
    }

    public function updatedPerPage(): void
    {
        $this->perPage = $this->normalizedPerPage();
        $this->persistUiState();
    }

    /**
     * Sort the country list by an allowed field.
     */
    public function sortBy(string $field): void
    {
        if (! in_array($field, [
            'id',
            'iso2',
            'iso3',
            'name',
            'official_name',
            'phone_code',
            'region',
            'subregion',
            'capital',
            'subdivisions_count',
            'is_active',
        ], true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            $this->resetPage();
            $this->persistUiState();

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';

        $this->resetPage();
        $this->persistUiState();
    }

    private function persistUiState(): void
    {
        $this->setUserSetting(self::UI_STATE_SETTING_KEY, [
            'search' => $this->search,
            'regionFilter' => $this->regionFilter,
            'statusFilter' => $this->statusFilter,
            'membershipFilter' => $this->membershipFilter,
            'dataFilter' => $this->dataFilter,
            'perPage' => $this->perPage,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }

    /**
     * Jump to first paginated page.
     */
    public function goToFirstPage(): void
    {
        $this->setPage(1);
    }

    /**
     * Jump to previous paginated page.
     */
    public function goToPreviousPage(): void
    {
        $this->previousPage();
    }

    /**
     * Jump to next paginated page.
     */
    public function goToNextPage(): void
    {
        $this->nextPage();
    }

    /**
     * Jump to last available paginated page.
     */
    public function goToLastPage(): void
    {
        $this->setPage($this->getPageCount());
    }

    /**
     * Jump to a bounded paginated page index.
     */
    public function goToPage(int $page): void
    {
        $this->setPage(max(1, min($page, $this->getPageCount())));
    }

    /**
     * Render the country reference audit list.
     */
    public function render(): View
    {
        $query = $this->countryQuery();

        return view('components.admin.⚡country-reference-list', [
            'countries' => $query->paginate($this->normalizedPerPage()),
            'regions' => $this->regions(),
            'summary' => $this->summary(),
        ]);
    }

    /**
     * Build the filtered country query.
     */
    private function countryQuery()
    {
        return Country::query()
            ->with('addressFormat')
            ->withCount('subdivisions')
            ->when($this->search !== '', function (Builder $query): void {
                $search = '%' . $this->search . '%';

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('iso2', 'ilike', $search)
                        ->orWhere('iso3', 'ilike', $search)
                        ->orWhere('iso_numeric', 'ilike', $search)
                        ->orWhere('name', 'ilike', $search)
                        ->orWhere('official_name', 'ilike', $search)
                        ->orWhere('common_name', 'ilike', $search)
                        ->orWhere('native_name', 'ilike', $search)
                        ->orWhere('phone_code', 'ilike', $search)
                        ->orWhere('capital', 'ilike', $search)
                        ->orWhere('region', 'ilike', $search)
                        ->orWhere('subregion', 'ilike', $search);
                });
            })
            ->when($this->regionFilter !== '', function (Builder $query): void {
                $query->where('region', $this->regionFilter);
            })
            ->when($this->statusFilter === 'active', function (Builder $query): void {
                $query->where('is_active', true);
            })
            ->when($this->statusFilter === 'inactive', function (Builder $query): void {
                $query->where('is_active', false);
            })
            ->when($this->membershipFilter === 'eu', function (Builder $query): void {
                $query->where('is_eu_member', true);
            })
            ->when($this->membershipFilter === 'eea', function (Builder $query): void {
                $query->where('is_eea_member', true);
            })
            ->when($this->membershipFilter === 'schengen', function (Builder $query): void {
                $query->where('is_schengen_member', true);
            })
            ->when($this->dataFilter === 'missing_capital', function (Builder $query): void {
                $query->whereNull('capital');
            })
            ->when($this->dataFilter === 'missing_phone_code', function (Builder $query): void {
                $query->whereNull('phone_code');
            })
            ->when($this->dataFilter === 'missing_address_format', function (Builder $query): void {
                $query->whereNull('address_format_key');
            })
            ->when($this->dataFilter === 'with_subdivisions', function (Builder $query): void {
                $query->has('subdivisions');
            })
            ->when($this->dataFilter === 'without_subdivisions', function (Builder $query): void {
                $query->doesntHave('subdivisions');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('iso2', 'asc');
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
     * Available country regions.
     *
     * @return array<int, string>
     */
    private function regions(): array
    {
        return Country::query()
            ->whereNotNull('region', 'and')
            ->distinct()
            ->orderBy('region', 'asc')
            ->pluck('region')
            ->all();
    }

    /**
     * Summary metrics for the audit cards.
     *
     * @return array<string, int>
     */
    private function summary(): array
    {
        return [
            'total' => Country::query()->count('*'),
            'active' => Country::query()->where('is_active', true)->count('*'),
            'with_address_format' => Country::query()->whereNotNull('address_format_key', 'and')->count('*'),
            'with_subdivisions' => Country::query()->has('subdivisions')->count('*'),
            'missing_capital' => Country::query()->whereNull('capital', 'and', false)->count('*'),
            'missing_phone_code' => Country::query()->whereNull('phone_code', 'and', false)->count('*'),
            'eu' => Country::query()->where('is_eu_member', true)->count('*'),
            'eea' => Country::query()->where('is_eea_member', true)->count('*'),
            'schengen' => Country::query()->where('is_schengen_member', true)->count('*'),
        ];
    }

    /**
     * Resolve current total page count for the active filter/sort state.
     */
    private function getPageCount(): int
    {
        $perPage = $this->normalizedPerPage();

        return max(1, (int) $this->countryQuery()->paginate($perPage)->lastPage());
    }
}
