<?php

// app/Livewire/Admin/ClientList.php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

/**
 * Administrative client list with filtering, sorting and pagination.
 */
class ClientList extends Component
{
    use InteractsWithUserSettings;
    use WithoutUrlPagination;
    use WithPagination;

    private const UI_STATE_SETTING_KEY = 'ui.pages.admin_client_list';

    public string $search = '';

    public string $typeFilter = '';

    public string $statusFilter = '';

    public string $peopleFilter = '';

    public int $perPage = 50;

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public function mount(): void
    {
        $state = $this->userSetting(self::UI_STATE_SETTING_KEY, []);

        if (is_array($state)) {
            $this->search = trim((string) ($state['search'] ?? $this->search));
            $this->typeFilter = trim((string) ($state['typeFilter'] ?? $this->typeFilter));
            $this->statusFilter = trim((string) ($state['statusFilter'] ?? $this->statusFilter));
            $this->peopleFilter = trim((string) ($state['peopleFilter'] ?? $this->peopleFilter));
            $this->perPage = $this->normalizePerPage($state['perPage'] ?? $this->perPage);

            $sortField = trim((string) ($state['sortField'] ?? $this->sortField));
            $this->sortField = in_array($sortField, [
                'client_number',
                'name',
                'legal_name',
                'type',
                'status',
                'people_count',
                'created_at',
            ], true) ? $sortField : 'name';

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
     * Reset pagination when client type filter changes.
     */
    public function updatedTypeFilter(): void
    {
        $this->setPage(1);
        $this->persistUiState();
    }

    /**
     * Reset pagination when status filter changes.
     */
    public function updatedStatusFilter(): void
    {
        $this->setPage(1);
        $this->persistUiState();
    }

    /**
     * Reset pagination when people-relation filter changes.
     */
    public function updatedPeopleFilter(): void
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
     * Reset all filter controls to default values.
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->statusFilter = '';
        $this->peopleFilter = '';
        $this->perPage = 50;

        $this->setPage(1);
        $this->persistUiState();
    }

    /**
     * Sort by a whitelisted column and toggle direction on repeated selection.
     */
    public function sortBy(string $field): void
    {
        $allowedFields = [
            'client_number',
            'name',
            'legal_name',
            'type',
            'status',
            'people_count',
            'created_at',
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
            'typeFilter' => $this->typeFilter,
            'statusFilter' => $this->statusFilter,
            'peopleFilter' => $this->peopleFilter,
            'perPage' => $this->perPage,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }

    /**
     * Build list data, summary metrics and option lists for rendering.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        $clientsQuery = Client::query()
            ->withCount('people');

        $clientsQuery
            ->when(trim($this->search) !== '', function ($query): void {
                $search = trim($this->search);

                $query->where(function ($query) use ($search): void {
                    $query->where('client_number', 'ilike', "%{$search}%")
                        ->orWhere('name', 'ilike', "%{$search}%")
                        ->orWhere('legal_name', 'ilike', "%{$search}%")
                        ->orWhere('description', 'ilike', "%{$search}%");
                });
            })
            ->when($this->typeFilter !== '', function ($query): void {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->statusFilter !== '', function ($query): void {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->peopleFilter === 'with_people', function ($query): void {
                $query->has('people');
            })
            ->when($this->peopleFilter === 'without_people', function ($query): void {
                $query->doesntHave('people');
            });

        $clients = $clientsQuery
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('name', 'asc')
            ->paginate($this->normalizePerPage($this->perPage));

        $summary = [
            'totalClients' => Client::query()->count('*'),
            'pendingClients' => Client::query()->where('status', Client::STATUS_PENDING)->count('*'),
            'activeClients' => Client::query()->where('status', Client::STATUS_ACTIVE)->count('*'),
            'clientsWithPeople' => Client::query()->has('people')->count('*'),
            'clientsWithoutPeople' => Client::query()->doesntHave('people')->count('*'),
        ];

        $typeOptions = Client::query()
            ->whereNotNull('type', 'and')
            ->select('type')
            ->distinct()
            ->orderBy('type', 'asc')
            ->pluck('type')
            ->all();

        $statusOptions = Client::query()
            ->whereNotNull('status', 'and')
            ->select('status')
            ->distinct()
            ->orderBy('status', 'asc')
            ->pluck('status')
            ->all();

        return view('components.admin.⚡client-list', [
            'clients' => $clients,
            'summary' => $summary,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
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
