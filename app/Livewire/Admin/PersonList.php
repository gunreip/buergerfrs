<?php

// app/Livewire/Admin/PersonList.php

namespace App\Livewire\Admin;

use App\Models\Person;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class PersonList extends Component
{
    use WithoutUrlPagination;
    use WithPagination;

    public string $search = '';

    public string $userFilter = '';

    public string $clientFilter = '';

    public int $perPage = 50;

    public string $sortField = 'last_name';

    public string $sortDirection = 'asc';

    public function updatedSearch(): void
    {
        $this->setPage(1);
    }

    public function updatedUserFilter(): void
    {
        $this->setPage(1);
    }

    public function updatedClientFilter(): void
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
        $this->userFilter = '';
        $this->clientFilter = '';
        $this->perPage = 50;

        $this->setPage(1);
    }

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

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';

        $this->setPage(1);
    }

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
            'totalPeople' => Person::query()->count(),
            'peopleWithUser' => Person::query()->has('user')->count(),
            'peopleWithoutUser' => Person::query()->doesntHave('user')->count(),
            'peopleWithClients' => Person::query()->has('clients')->count(),
            'peopleWithoutClients' => Person::query()->doesntHave('clients')->count(),
        ];

        return view('components.admin.⚡person-list', [
            'people' => $people,
            'summary' => $summary,
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
