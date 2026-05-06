<?php

// app/Livewire/Admin/ClientList.php

namespace App\Livewire\Admin;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class ClientList extends Component
{
    use WithPagination;
    use WithoutUrlPagination;

    public string $search = '';
    public string $typeFilter = '';
    public string $statusFilter = '';
    public string $peopleFilter = '';
    public int $perPage = 50;

    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    public function updatedSearch(): void
    {
        $this->setPage(1);
    }

    public function updatedTypeFilter(): void
    {
        $this->setPage(1);
    }

    public function updatedStatusFilter(): void
    {
        $this->setPage(1);
    }

    public function updatedPeopleFilter(): void
    {
        $this->setPage(1);
    }

    public function updatedPerPage(): void
    {
        $this->perPage = $this->normalizePerPage($this->perPage);

        $this->setPage(1);
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->statusFilter = '';
        $this->peopleFilter = '';
        $this->perPage = 50;

        $this->setPage(1);
    }

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

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }

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
            ->orderBy('name')
            ->paginate($this->normalizePerPage($this->perPage));

        $summary = [
            'totalClients' => Client::query()->count(),
            'pendingClients' => Client::query()->where('status', Client::STATUS_PENDING)->count(),
            'activeClients' => Client::query()->where('status', Client::STATUS_ACTIVE)->count(),
            'clientsWithPeople' => Client::query()->has('people')->count(),
            'clientsWithoutPeople' => Client::query()->doesntHave('people')->count(),
        ];

        $typeOptions = Client::query()
            ->whereNotNull('type')
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->all();

        $statusOptions = Client::query()
            ->whereNotNull('status')
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->all();

        return view('components.admin.⚡client-list', [
            'clients' => $clients,
            'summary' => $summary,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
        ]);
    }

    private function normalizePerPage(mixed $value): int
    {
        $value = (int) $value;

        if (! in_array($value, [10, 25, 50, 100], true)) {
            return 50;
        }

        return $value;
    }
}
