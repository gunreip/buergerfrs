<?php

namespace App\Livewire\Management\People;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Models\Country;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class PersonOverview extends Component
{
    use InteractsWithUserSettings;
    use WithoutUrlPagination;
    use WithPagination;

    private const UI_STATE_SETTING_KEY = 'ui.pages.management_people_overview';

    public string $search = '';

    public string $testDataFilter = '';

    public string $userFilter = '';

    public string $clientFilter = '';

    public string $birthCountryFilter = '';

    public int $perPage = 25;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public function mount(): void
    {
        $state = $this->userSetting(self::UI_STATE_SETTING_KEY, []);

        if (is_array($state)) {
            $this->search = trim((string) ($state['search'] ?? $this->search));
            $this->testDataFilter = trim((string) ($state['testDataFilter'] ?? $this->testDataFilter));
            $this->userFilter = trim((string) ($state['userFilter'] ?? $this->userFilter));
            $this->clientFilter = trim((string) ($state['clientFilter'] ?? $this->clientFilter));
            $this->birthCountryFilter = trim((string) ($state['birthCountryFilter'] ?? $this->birthCountryFilter));
            $this->perPage = $this->normalizePerPage($state['perPage'] ?? $this->perPage);
            $this->sortField = $this->normalizeSortField($state['sortField'] ?? $this->sortField);
            $this->sortDirection = $this->normalizeSortDirection($state['sortDirection'] ?? $this->sortDirection);
        }

        $this->setPage(1);
    }

    public function updatedSearch(): void
    {
        $this->resetListPage();
    }

    public function updatedTestDataFilter(): void
    {
        $this->resetListPage();
    }

    public function updatedUserFilter(): void
    {
        $this->resetListPage();
    }

    public function updatedClientFilter(): void
    {
        $this->resetListPage();
    }

    public function updatedBirthCountryFilter(): void
    {
        $this->resetListPage();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = $this->normalizePerPage($this->perPage);
        $this->resetListPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->testDataFilter = '';
        $this->userFilter = '';
        $this->clientFilter = '';
        $this->birthCountryFilter = '';
        $this->perPage = 25;

        $this->resetListPage();
    }

    public function sortBy(string $field): void
    {
        $field = $this->normalizeSortField($field);

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            $this->resetListPage();

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';

        $this->resetListPage();
    }

    public function render()
    {
        $peopleQuery = $this->filteredPeopleQuery()
            ->with([
                'birthCountry:id,iso2,name,native_name',
                'user:id,person_id,name,email',
            ])
            ->withCount('clients');

        $people = $peopleQuery
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate($this->normalizePerPage($this->perPage));

        return view('components.management.people.⚡person-overview', [
            'people' => $people,
            'summary' => $this->summary(),
            'birthCountryOptions' => $this->birthCountryOptions(),
        ]);
    }

    private function filteredPeopleQuery(): Builder
    {
        return Person::query()
            ->when($this->search !== '', function (Builder $query): void {
                $needle = '%'.mb_strtolower($this->search).'%';

                $query->where(function (Builder $query) use ($needle): void {
                    $this->whereLowerLike($query, 'person_number', $needle)
                        ->orWhere(fn (Builder $query) => $this->whereLowerLike($query, 'first_name', $needle))
                        ->orWhere(fn (Builder $query) => $this->whereLowerLike($query, 'last_name', $needle))
                        ->orWhere(fn (Builder $query) => $this->whereLowerLike($query, 'birth_place_text', $needle))
                        ->orWhere(fn (Builder $query) => $this->whereLowerLike($query, 'email_private', $needle))
                        ->orWhere(fn (Builder $query) => $this->whereLowerLike($query, 'email_work', $needle))
                        ->orWhereHas('user', function (Builder $query) use ($needle): void {
                            $this->whereLowerLike($query, 'name', $needle)
                                ->orWhere(fn (Builder $query) => $this->whereLowerLike($query, 'email', $needle));
                        });
                });
            })
            ->when($this->testDataFilter === 'test', fn (Builder $query) => $query->where('is_test_data', true))
            ->when($this->testDataFilter === 'real', fn (Builder $query) => $query->where('is_test_data', false))
            ->when($this->userFilter === 'with_user', fn (Builder $query) => $query->has('user'))
            ->when($this->userFilter === 'without_user', fn (Builder $query) => $query->doesntHave('user'))
            ->when($this->clientFilter === 'with_client', fn (Builder $query) => $query->has('clients'))
            ->when($this->clientFilter === 'without_client', fn (Builder $query) => $query->doesntHave('clients'))
            ->when($this->birthCountryFilter !== '', function (Builder $query): void {
                $query->where('birth_country_id', (int) $this->birthCountryFilter);
            });
    }

    private function whereLowerLike(Builder $query, string $column, string $needle): Builder
    {
        return $query->whereRaw('LOWER('.DB::getQueryGrammar()->wrap($column).') LIKE ?', [$needle]);
    }

    /**
     * @return array<string, int>
     */
    private function summary(): array
    {
        $filteredQuery = $this->filteredPeopleQuery();

        return [
            'totalPeople' => Person::query()->count(),
            'filteredPeople' => (clone $filteredQuery)->count(),
            'testPeople' => Person::query()->where('is_test_data', true)->count(),
            'realPeople' => Person::query()->where('is_test_data', false)->count(),
            'peopleWithUser' => Person::query()->has('user')->count(),
            'peopleWithoutUser' => Person::query()->doesntHave('user')->count(),
            'peopleWithClients' => Person::query()->has('clients')->count(),
            'peopleWithoutClients' => Person::query()->doesntHave('clients')->count(),
        ];
    }

    private function birthCountryOptions()
    {
        return Country::query()
            ->whereIn('id', Person::query()
                ->whereNotNull('birth_country_id')
                ->select('birth_country_id')
                ->distinct())
            ->ordered()
            ->get(['id', 'iso2', 'name', 'native_name']);
    }

    private function resetListPage(): void
    {
        $this->setPage(1);
        $this->persistUiState();
    }

    private function persistUiState(): void
    {
        $this->setUserSetting(self::UI_STATE_SETTING_KEY, [
            'search' => $this->search,
            'testDataFilter' => $this->testDataFilter,
            'userFilter' => $this->userFilter,
            'clientFilter' => $this->clientFilter,
            'birthCountryFilter' => $this->birthCountryFilter,
            'perPage' => $this->perPage,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }

    private function normalizePerPage(mixed $value): int
    {
        $value = (int) $value;

        return in_array($value, [10, 25, 50, 100], true) ? $value : 25;
    }

    private function normalizeSortField(mixed $field): string
    {
        $field = trim((string) $field);

        return in_array($field, [
            'person_number',
            'first_name',
            'last_name',
            'date_of_birth',
            'birth_place_text',
            'created_at',
            'clients_count',
        ], true) ? $field : 'created_at';
    }

    private function normalizeSortDirection(mixed $direction): string
    {
        $direction = trim((string) $direction);

        return in_array($direction, ['asc', 'desc'], true) ? $direction : 'desc';
    }
}
