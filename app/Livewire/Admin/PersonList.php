<?php

// app/Livewire/Admin/PersonList.php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Models\Person;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

/**
 * Administrative people list with relation-aware filters, sorting and pagination.
 */
class PersonList extends Component
{
    use InteractsWithUserSettings;
    use WithoutUrlPagination;
    use WithPagination;

    private const UI_STATE_SETTING_KEY = 'ui.pages.admin_person_list';

    public string $search = '';

    public string $userFilter = '';

    public string $clientFilter = '';

    public int $perPage = 25;

    public string $sortField = 'last_name';

    public string $sortDirection = 'asc';

    public function mount(): void
    {
        $state = $this->userSetting(self::UI_STATE_SETTING_KEY, []);

        if (is_array($state)) {
            $this->search = trim((string) ($state['search'] ?? $this->search));
            $this->userFilter = trim((string) ($state['userFilter'] ?? $this->userFilter));
            $this->clientFilter = trim((string) ($state['clientFilter'] ?? $this->clientFilter));
            $this->perPage = $this->normalizePerPage($state['perPage'] ?? $this->perPage);

            $sortField = trim((string) ($state['sortField'] ?? $this->sortField));
            $this->sortField = in_array($sortField, [
                'person_number',
                'first_name',
                'last_name',
                'date_of_birth',
                'created_at',
                'clients_count',
            ], true) ? $sortField : 'last_name';

            $sortDirection = trim((string) ($state['sortDirection'] ?? $this->sortDirection));
            $this->sortDirection = in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'asc';
        }

        $this->setPage(1);
    }

    /**
     * Reset pagination when search input changes.
     */
    public function updatedSearch(): void
    {
        $this->setPage(1);
        $this->persistUiState();
    }

    /**
     * Reset pagination when user relation filter changes.
     */
    public function updatedUserFilter(): void
    {
        $this->setPage(1);
        $this->persistUiState();
    }

    /**
     * Reset pagination when client relation filter changes.
     */
    public function updatedClientFilter(): void
    {
        $this->setPage(1);
        $this->persistUiState();
    }

    /**
     * Normalize page size and reset pagination.
     */
    public function updatedPerPage(): void
    {
        $this->perPage = $this->normalizePerPage($this->perPage);

        $this->setPage(1);
        $this->persistUiState();
    }

    /**
     * Reset all active filters to defaults.
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->userFilter = '';
        $this->clientFilter = '';
        $this->perPage = 50;

        $this->setPage(1);
        $this->persistUiState();
    }

    /**
     * Sort by a whitelisted column and toggle direction when selected repeatedly.
     */
    public function sortBy(string $field): void
    {
        $allowedFields = [
            'person_number',
            'first_name',
            'last_name',
            'date_of_birth',
            'created_at',
            'clients_count',
        ];

        if (! in_array($field, $allowedFields, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            $this->setPage(1);
            $this->persistUiState();

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';

        $this->setPage(1);
        $this->persistUiState();
    }

    private function persistUiState(): void
    {
        $this->setUserSetting(self::UI_STATE_SETTING_KEY, [
            'search' => $this->search,
            'userFilter' => $this->userFilter,
            'clientFilter' => $this->clientFilter,
            'perPage' => $this->perPage,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }

    /**
     * Build paginated list and summary data for rendering.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        $peopleQuery = Person::query()
            ->with([
                'user:id,name,email,person_id',
            ])
            ->withCount([
                'clients',
            ]);

        $peopleQuery
            ->when(trim($this->search) !== '', function ($query): void {
                $search = trim($this->search);

                $query->where(function ($query) use ($search): void {
                    $query->where('person_number', 'ilike', "%{$search}%")
                        ->orWhere('first_name', 'ilike', "%{$search}%")
                        ->orWhere('last_name', 'ilike', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search): void {
                            $query->where('name', 'ilike', "%{$search}%")
                                ->orWhere('email', 'ilike', "%{$search}%");
                        });
                });
            })
            ->when($this->userFilter === 'with_user', function ($query): void {
                $query->has('user');
            })
            ->when($this->userFilter === 'without_user', function ($query): void {
                $query->doesntHave('user');
            })
            ->when($this->clientFilter === 'with_client', function ($query): void {
                $query->has('clients');
            })
            ->when($this->clientFilter === 'without_client', function ($query): void {
                $query->doesntHave('clients');
            });

        $people = $peopleQuery
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate($this->normalizePerPage($this->perPage));

        $summary = [
            'totalPeople' => Person::query()->count('*'),
            'peopleWithUser' => Person::query()->has('user')->count('*'),
            'peopleWithoutUser' => Person::query()->doesntHave('user')->count('*'),
            'peopleWithClients' => Person::query()->has('clients')->count('*'),
            'peopleWithoutClients' => Person::query()->doesntHave('clients')->count('*'),
        ];

        return view('components.admin.⚡person-list', [
            'people' => $people,
            'summary' => $summary,
        ]);
    }

    /**
     * Normalize selectable pagination size.
     */
    private function normalizePerPage(mixed $value): int
    {
        $value = (int) $value;

        if (! in_array($value, [10, 25, 50, 100], true)) {
            return 50;
        }

        return $value;
    }
}
