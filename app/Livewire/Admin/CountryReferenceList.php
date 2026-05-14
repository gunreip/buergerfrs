<?php

// app/Livewire/Admin/CountryReferenceList.php

namespace App\Livewire\Admin;

use App\Models\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class CountryReferenceList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $regionFilter = '';

    public string $statusFilter = '';

    public string $membershipFilter = '';

    public string $dataFilter = '';

    public int $perPage = 25;

    public string $sortField = 'iso2';

    public string $sortDirection = 'asc';

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

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';

        $this->resetPage();
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
    private function countryQuery(): Builder
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
            ->orderBy('iso2');
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
            ->whereNotNull('region')
            ->distinct()
            ->orderBy('region')
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
            'total' => Country::query()->count(),
            'active' => Country::query()->where('is_active', true)->count(),
            'with_address_format' => Country::query()->whereNotNull('address_format_key')->count(),
            'with_subdivisions' => Country::query()->has('subdivisions')->count(),
            'missing_capital' => Country::query()->whereNull('capital')->count(),
            'missing_phone_code' => Country::query()->whereNull('phone_code')->count(),
            'eu' => Country::query()->where('is_eu_member', true)->count(),
            'eea' => Country::query()->where('is_eea_member', true)->count(),
            'schengen' => Country::query()->where('is_schengen_member', true)->count(),
        ];
    }
}
