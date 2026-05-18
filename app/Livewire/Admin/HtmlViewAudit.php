<?php

// app/Livewire/Admin/HtmlViewAudit.php

namespace App\Livewire\Admin;

use App\Models\HtmlViewAuditFinding;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

class HtmlViewAudit extends Component
{
    use WithPagination;

    public string $statusFilter = 'open';

    public string $sectionFilter = 'all';

    public string $typeFilter = 'all';

    public string $search = '';

    public int $perPage = 25;

    public string $sortField = 'last_seen_at';

    public string $sortDirection = 'desc';

    /**
     * Reset pagination when a filter changes.
     */
    public function updating(string $property): void
    {
        if (in_array($property, [
            'statusFilter',
            'sectionFilter',
            'typeFilter',
            'search',
            'perPage',
        ], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->statusFilter = 'open';
        $this->sectionFilter = 'all';
        $this->typeFilter = 'all';
        $this->search = '';
        $this->perPage = 25;

        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        $allowedFields = [
            'id',
            'status',
            'section',
            'type',
            'file',
            'tag',
            'opened_line',
            'closing_line',
            'expected_closing',
            'actual_closing',
            'last_seen_at',
        ];

        if (! in_array($field, $allowedFields, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = $field === 'id' ? 'asc' : 'desc';
    }

    public function render(): View
    {
        $audit = $this->audit();
        $problems = $this->filteredFindings();

        return view('components.admin.⚡html-view-audit', [
            'audit' => $audit,
            'nativeReferenceFile' => $this->nativeReferenceFile(),
            'historyCounts' => $this->historyCounts(),
            'problems' => $problems,
            'filteredProblemCount' => $problems->total(),
            'hasActiveFilters' => $this->hasActiveFilters(),
            'tableLegend' => $this->tableLegend(),
            'legendTexts' => $this->legendTexts(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function audit(): array
    {
        $path = storage_path('audits/html/view-html-check.json');
        $relativePath = 'storage/audits/html/view-html-check.json';

        if (! File::exists($path)) {
            return $this->emptyAudit(
                path: $relativePath,
                note: 'Audit file missing. Run php artisan html:check.',
            );
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            return $this->emptyAudit(
                path: $relativePath,
                note: 'Audit file is not valid JSON. Run php artisan html:check again.',
            );
        }

        return [
            'exists' => true,
            'path' => $relativePath,
            'generated_at' => $payload['generated_at'] ?? null,
            'files_scanned' => (int) ($payload['files_scanned'] ?? 0),
            'problem_count' => (int) ($payload['problem_count'] ?? 0),
            'note' => $payload['note'] ?? null,
            'references' => is_array($payload['references'] ?? null) ? $payload['references'] : [],
            'sections' => is_array($payload['sections'] ?? null) ? $payload['sections'] : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyAudit(string $path, string $note): array
    {
        return [
            'exists' => false,
            'path' => $path,
            'generated_at' => null,
            'files_scanned' => 0,
            'problem_count' => 0,
            'note' => $note,
            'references' => [],
            'sections' => [
                'native_html' => [
                    'problem_count' => 0,
                    'problems' => [],
                ],
                'custom_components' => [
                    'problem_count' => 0,
                    'problems' => [],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function nativeReferenceFile(): array
    {
        $path = storage_path('audits/html/native-html-tags.json');
        $relativePath = 'storage/audits/html/native-html-tags.json';

        if (! File::exists($path)) {
            return [
                'exists' => false,
                'path' => $relativePath,
                'file_written_at' => null,
                'generated_at' => null,
                'source_name' => null,
                'source_url' => null,
                'normal_count' => 0,
                'void_count' => 0,
                'total_count' => 0,
                'hint' => 'Run php artisan html:sync-native-tags to refresh the WHATWG native HTML reference.',
            ];
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            return [
                'exists' => false,
                'path' => $relativePath,
                'file_written_at' => date('c', File::lastModified($path)),
                'generated_at' => null,
                'source_name' => null,
                'source_url' => null,
                'normal_count' => 0,
                'void_count' => 0,
                'total_count' => 0,
                'hint' => 'Native HTML reference file is invalid JSON. Run php artisan html:sync-native-tags again.',
            ];
        }

        return [
            'exists' => true,
            'path' => $relativePath,
            'file_written_at' => date('c', File::lastModified($path)),
            'generated_at' => $payload['generated_at'] ?? null,
            'source_name' => $payload['source']['name'] ?? null,
            'source_url' => $payload['source']['url'] ?? null,
            'normal_count' => (int) ($payload['counts']['normal'] ?? count($payload['tags']['normal'] ?? [])),
            'void_count' => (int) ($payload['counts']['void'] ?? count($payload['tags']['void'] ?? [])),
            'total_count' => (int) ($payload['counts']['total'] ?? 0),
            'hint' => 'Refresh occasionally with php artisan html:sync-native-tags.',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function filteredFindings(): LengthAwarePaginator
    {
        $findings = $this->findingsQuery()
            ->with('previousFinding:id,opened_line,closing_line')
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderByDesc('id')
            ->paginate($this->normalizedPerPage());

        $findings->getCollection()->transform(fn(HtmlViewAuditFinding $finding): array => [
            'id' => $finding->id,
            'status' => $finding->status,
            'section' => $finding->section,
            'type' => $finding->type,
            'file' => $finding->file,
            'tag' => $finding->tag,
            'closing_tag' => $finding->closing_tag,
            'opened_line' => $finding->opened_line,
            'closing_line' => $finding->closing_line,
            'expected_closing' => $finding->expected_closing,
            'actual_closing' => $finding->actual_closing,
            'first_seen_at' => $finding->first_seen_at?->toDateTimeString(),
            'last_seen_at' => $finding->last_seen_at?->toDateTimeString(),
            'resolved_at' => $finding->resolved_at?->toDateTimeString(),
            'resolved_source' => $finding->resolved_source,
            'previous_finding_id' => $finding->previous_finding_id,
            'previous_opened_line' => $finding->previousFinding?->opened_line,
            'previous_closing_line' => $finding->previousFinding?->closing_line,
        ]);

        return $findings;
    }

    private function findingsQuery(): Builder
    {
        $search = Str::lower(trim($this->search));

        return HtmlViewAuditFinding::query()
            ->when($this->statusFilter !== 'all', function (Builder $query): void {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->sectionFilter !== 'all', function (Builder $query): void {
                $query->where('section', $this->sectionFilter);
            })
            ->when($this->typeFilter !== 'all', function (Builder $query): void {
                $query->where('type', $this->typeFilter);
            })
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->whereRaw('lower(file) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('lower(coalesce(tag, \'\')) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('lower(coalesce(closing_tag, \'\')) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('lower(coalesce(expected_closing, \'\')) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('lower(coalesce(actual_closing, \'\')) like ?', ['%' . $search . '%']);
                });
            });
    }

    /**
     * Normalize selectable pagination size.
     */
    private function normalizedPerPage(): int
    {
        return in_array($this->perPage, [10, 25, 50, 100], true)
            ? $this->perPage
            : 25;
    }

    /**
     * @return array<string, int>
     */
    private function historyCounts(): array
    {
        $counts = HtmlViewAuditFinding::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        return [
            'open' => (int) ($counts['open'] ?? 0),
            'changed' => (int) ($counts['changed'] ?? 0),
            'resolved' => (int) ($counts['resolved'] ?? 0),
            'ignored' => (int) ($counts['ignored'] ?? 0),
            'total' => array_sum(array_map('intval', $counts)),
        ];
    }

    /**
     * @return array<string, array<string, array<string, string>>>
     */
    private function tableLegend(): array
    {
        $legend = config('html-audit.table_legend', []);

        return is_array($legend) ? $legend : [];
    }

    /**
     * @return array<string, string>
     */
    private function legendTexts(): array
    {
        return collect($this->tableLegend())
            ->mapWithKeys(fn(array $items, string $group): array => [
                $group => collect($items)
                    ->map(function (array $item, string $key): string {
                        $label = (string) ($item['label'] ?? $key);
                        $description = (string) ($item['description'] ?? '');

                        $symbol = (string) ($item['symbol'] ?? '');
                        $color = (string) ($item['color'] ?? 'zinc');

                        return implode('|', [
                            $symbol,
                            $color,
                            $label,
                            $description,
                        ]);
                    })
                    ->implode(PHP_EOL),
            ])
            ->all();
    }

    private function hasActiveFilters(): bool
    {
        return $this->statusFilter !== 'open'
            || $this->sectionFilter !== 'all'
            || $this->typeFilter !== 'all'
            || trim($this->search) !== '';
    }
}
