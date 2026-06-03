<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\InteractsWithUserSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Flag reference list based on generated audit reports.
 */
class FlagReferenceList extends Component
{
    use InteractsWithUserSettings;
    use WithPagination;

    private const UI_STATE_SETTING_KEY = 'ui.pages.admin_flag_reference_list';

    private const SORT_FIELDS = [
        'code',
        'type',
        'status',
        'resolved',
        'candidates',
        'sources',
        'comment',
    ];

    public string $search = '';

    public string $typeFilter = '';

    public string $statusFilter = '';

    public string $sortField = 'code';

    public string $sortDirection = 'asc';

    public int $perPage = 25;

    /**
     * @var array<string, string>
     */
    public array $comments = [];

    public function mount(): void
    {
        $state = $this->userSetting(self::UI_STATE_SETTING_KEY, []);

        if (is_array($state)) {
            $this->search = trim((string) ($state['search'] ?? $this->search));
            $this->typeFilter = trim((string) ($state['typeFilter'] ?? $this->typeFilter));
            $this->statusFilter = trim((string) ($state['statusFilter'] ?? $this->statusFilter));
            $this->sortField = in_array((string) ($state['sortField'] ?? ''), self::SORT_FIELDS, true)
                ? (string) $state['sortField']
                : $this->sortField;
            $this->sortDirection = in_array((string) ($state['sortDirection'] ?? ''), ['asc', 'desc'], true)
                ? (string) $state['sortDirection']
                : $this->sortDirection;
            $this->perPage = (int) ($state['perPage'] ?? $this->perPage);
        }

        $this->perPage = $this->normalizedPerPage();
        $this->comments = $this->loadComments();
        $this->setPage(1);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = $this->normalizedPerPage();
        $this->resetPage();
        $this->persistUiState();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, self::SORT_FIELDS, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
        $this->persistUiState();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->statusFilter = '';
        $this->sortField = 'code';
        $this->sortDirection = 'asc';
        $this->perPage = 25;
        $this->resetPage();
        $this->persistUiState();
    }

    public function saveComment(string $code, string $value): void
    {
        $normalizedCode = trim($code);

        if ($normalizedCode === '') {
            return;
        }

        $comment = trim($value);

        if ($comment === '') {
            unset($this->comments[$normalizedCode]);
        } else {
            $this->comments[$normalizedCode] = $comment;
        }

        $this->storeComments($this->comments);
    }

    public function render(): View
    {
        $report = $this->loadLatestReport();
        $entries = $this->buildEntries($report);
        $filtered = $this->applyFilters($entries);
        $sorted = $this->applySorting($filtered);

        $paginator = $this->paginateCollection($sorted);

        $summary = [
            'total' => count((array) ($report['analysis'] ?? [])),
            'resolved' => count(array_filter((array) ($report['analysis'] ?? []), static fn (array $row): bool => ! ((bool) ($row['needs_review'] ?? false)))),
            'needs_review' => count(array_filter((array) ($report['analysis'] ?? []), static fn (array $row): bool => (bool) ($row['needs_review'] ?? false))),
            'filtered' => $sorted->count(),
        ];

        return view('components.admin.⚡flag-reference-list', [
            'entries' => $paginator,
            'summary' => $summary,
            'reportPath' => $this->latestReportRelativePath(),
        ]);
    }

    private function persistUiState(): void
    {
        $this->setUserSetting(self::UI_STATE_SETTING_KEY, [
            'search' => $this->search,
            'typeFilter' => $this->typeFilter,
            'statusFilter' => $this->statusFilter,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'perPage' => $this->perPage,
        ]);
    }

    private function normalizedPerPage(): int
    {
        return in_array($this->perPage, [10, 25, 50, 100], true)
            ? $this->perPage
            : 25;
    }

    /**
     * @return array<string, mixed>
     */
    private function loadLatestReport(): array
    {
        $path = base_path((string) config('buergerfrs-flags.audit_report_path', 'docs/reports/flag-audit-2026-06-01.json'));

        if (! is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function latestReportRelativePath(): ?string
    {
        return (string) config('buergerfrs-flags.audit_report_path', 'docs/reports/flag-audit-2026-06-01.json');
    }

    /**
     * @param array<string, mixed> $report
     * @return Collection<int, array<string, mixed>>
     */
    private function buildEntries(array $report): Collection
    {
        $analysis = array_values(array_filter((array) ($report['analysis'] ?? []), static fn ($row): bool => is_array($row)));
        $dbSources = (array) ($report['db_sources'] ?? []);

        $sourcesByCode = [];

        foreach ($dbSources as $sourceName => $codes) {
            if (! is_array($codes)) {
                continue;
            }

            foreach ($codes as $code) {
                $normalizedCode = trim((string) $code);

                if ($normalizedCode === '') {
                    continue;
                }

                $sourcesByCode[$normalizedCode] ??= [];
                $sourcesByCode[$normalizedCode][] = (string) $sourceName;
            }
        }

        return collect($analysis)->map(function (array $row) use ($sourcesByCode): array {
            $code = trim((string) ($row['code'] ?? ''));
            $needsReview = (bool) ($row['needs_review'] ?? false);
            $sources = array_values(array_unique($sourcesByCode[$code] ?? []));
            sort($sources);

            $comment = (string) ($this->comments[$code] ?? '');

            return [
                'code' => $code,
                'type' => (string) ($row['type'] ?? 'other'),
                'status' => $needsReview ? 'needs_review' : 'resolved',
                'resolved' => trim((string) ($row['resolved'] ?? '')),
                'needs_review' => $needsReview,
                'candidates' => array_values(array_filter(array_map(static fn ($v): string => trim((string) $v), (array) ($row['candidates'] ?? [])), static fn (string $v): bool => $v !== '')),
                'sources' => $sources,
                'comment' => $comment,
            ];
        })->filter(static fn (array $row): bool => $row['code'] !== '')->values();
    }

    /**
     * @param Collection<int, array<string, mixed>> $entries
     * @return Collection<int, array<string, mixed>>
     */
    private function applyFilters(Collection $entries): Collection
    {
        $search = trim($this->search);

        return $entries
            ->when($this->typeFilter !== '', fn (Collection $c): Collection => $c->filter(fn (array $row): bool => (string) $row['type'] === $this->typeFilter))
            ->when($this->statusFilter !== '', fn (Collection $c): Collection => $c->filter(fn (array $row): bool => (string) $row['status'] === $this->statusFilter))
            ->when($search !== '', function (Collection $c) use ($search): Collection {
                $needle = mb_strtolower($search);

                return $c->filter(function (array $row) use ($needle): bool {
                    $haystackParts = [
                        (string) ($row['code'] ?? ''),
                        (string) ($row['type'] ?? ''),
                        (string) ($row['status'] ?? ''),
                        (string) ($row['resolved'] ?? ''),
                        implode(' ', (array) ($row['candidates'] ?? [])),
                        (string) ($row['comment'] ?? ''),
                    ];

                    $haystack = mb_strtolower(implode(' ', $haystackParts));

                    return str_contains($haystack, $needle);
                });
            })
            ->values();
    }

    /**
     * @param Collection<int, array<string, mixed>> $entries
     * @return Collection<int, array<string, mixed>>
     */
    private function applySorting(Collection $entries): Collection
    {
        $field = in_array($this->sortField, self::SORT_FIELDS, true)
            ? $this->sortField
            : 'code';

        $sortValue = static function (array $row, string $selectedField): string {
            return match ($selectedField) {
                'candidates' => mb_strtolower(trim((string) (($row['candidates'][0] ?? '') ?: ''))),
                'sources' => mb_strtolower(trim((string) (($row['sources'][0] ?? '') ?: ''))),
                'comment' => mb_strtolower(trim((string) ($row['comment'] ?? ''))),
                default => mb_strtolower(trim((string) ($row[$selectedField] ?? ''))),
            };
        };

        $sorted = $entries->sortBy(static function (array $row) use ($field, $sortValue): string {
            $primary = $sortValue($row, $field);
            $fallback = mb_strtolower(trim((string) ($row['code'] ?? '')));

            return $primary . "\n" . $fallback;
        });

        if ($this->sortDirection === 'desc') {
            $sorted = $sorted->reverse();
        }

        return $sorted->values();
    }

    /**
     * @return array<string, string>
     */
    private function loadComments(): array
    {
        $path = $this->commentsPath();

        if (! is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        if (! is_array($decoded)) {
            return [];
        }

        $comments = [];

        foreach ($decoded as $code => $comment) {
            $normalizedCode = trim((string) $code);
            $normalizedComment = trim((string) $comment);

            if ($normalizedCode === '' || $normalizedComment === '') {
                continue;
            }

            $comments[$normalizedCode] = $normalizedComment;
        }

        return $comments;
    }

    /**
     * @param array<string, string> $comments
     */
    private function storeComments(array $comments): void
    {
        ksort($comments);

        $path = $this->commentsPath();
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents(
            $path,
            json_encode($comments, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }

    private function commentsPath(): string
    {
        return storage_path((string) config('buergerfrs-flags.comments_path', 'app/reference/flag-reference-comments.json'));
    }

    /**
     * @param Collection<int, array<string, mixed>> $rows
     */
    private function paginateCollection(Collection $rows): LengthAwarePaginator
    {
        $page = max(1, (int) $this->getPage());
        $total = $rows->count();
        $lastPage = max(1, (int) ceil($total / $this->perPage));

        if ($page > $lastPage) {
            $page = $lastPage;
            $this->setPage($page);
        }

        $items = $rows->forPage($page, $this->perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $total,
            $this->perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }
}
