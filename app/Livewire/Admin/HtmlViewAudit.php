<?php

// app/Livewire/Admin/HtmlViewAudit.php

namespace App\Livewire\Admin;

use App\Models\HtmlViewAuditFinding;
use Carbon\CarbonImmutable;
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

    public bool $showFindingDetailsModal = false;

    public ?int $selectedFindingId = null;

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
            $this->resetPage();

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = $field === 'id' ? 'asc' : 'desc';
        $this->resetPage();
    }

    public function showFindingDetails(int $findingId): void
    {
        $this->selectedFindingId = $findingId;
        $this->showFindingDetailsModal = true;
    }

    public function closeFindingDetailsModal(): void
    {
        $this->showFindingDetailsModal = false;
        $this->selectedFindingId = null;
    }

    public function render(): View
    {
        $audit = $this->audit();
        $problems = $this->filteredFindings();

        return view('components.admin.⚡html-view-audit', [
            'audit' => $audit,
            'nativeReferenceFile' => $this->nativeReferenceFile(),
            'usageAudit' => $this->usageAudit(),
            'historyCounts' => $this->historyCounts(),
            'statistics' => $this->statistics(),
            'problems' => $problems,
            'filteredProblemCount' => $problems->total(),
            'hasActiveFilters' => $this->hasActiveFilters(),
            'tableLegend' => $this->tableLegend(),
            'legendTexts' => $this->legendTexts(),
            'selectedFinding' => $this->selectedFinding(),
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
     * @return array<string, mixed>
     */
    private function usageAudit(): array
    {
        $path = storage_path('audits/html/view-html-used.json');
        $relativePath = 'storage/audits/html/view-html-used.json';

        if (! File::exists($path)) {
            return [
                'exists' => false,
                'path' => $relativePath,
                'generated_at' => null,
                'generated_at_formatted' => null,
                'scan' => [
                    'files_scanned' => 0,
                    'excluded_files' => [],
                ],
                'components' => [
                    'source_paths' => [],
                    'skipped_paths' => [],
                ],
                'native' => [
                    'counts' => [
                        'available' => 0,
                        'used' => 0,
                        'unused' => 0,
                        'unknown' => 0,
                    ],
                    'top_used' => [],
                    'unused' => [],
                    'unknown' => [],
                ],
                'flux' => [
                    'counts' => [
                        'available' => 0,
                        'used' => 0,
                        'unused' => 0,
                        'used_unknown' => 0,
                    ],
                    'top_used' => [],
                    'unused' => [],
                    'used_unknown' => [],
                ],
                'custom' => [
                    'counts' => [
                        'available' => 0,
                        'used' => 0,
                        'unused' => 0,
                        'used_unknown' => 0,
                    ],
                    'top_used' => [],
                    'unused' => [],
                    'used_unknown' => [],
                ],
                'includes' => [
                    'counts' => [
                        'used' => 0,
                    ],
                    'used' => [],
                ],
                'livewire' => [
                    'counts' => [
                        'used' => 0,
                    ],
                    'used' => [],
                ],
                'note' => 'Usage audit file missing. Run php artisan html:check-view-html-used.',
            ];
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            return [
                'exists' => false,
                'path' => $relativePath,
                'generated_at' => null,
                'scan' => [
                    'files_scanned' => 0,
                    'excluded_files' => [],
                ],
                'components' => [
                    'source_paths' => [],
                    'skipped_paths' => [],
                ],
                'native' => [
                    'counts' => [
                        'available' => 0,
                        'used' => 0,
                        'unused' => 0,
                        'unknown' => 0,
                    ],
                    'top_used' => [],
                    'unused' => [],
                    'unknown' => [],
                ],
                'flux' => [
                    'counts' => [
                        'available' => 0,
                        'used' => 0,
                        'unused' => 0,
                        'used_unknown' => 0,
                    ],
                    'top_used' => [],
                    'unused' => [],
                    'used_unknown' => [],
                ],
                'custom' => [
                    'counts' => [
                        'available' => 0,
                        'used' => 0,
                        'unused' => 0,
                        'used_unknown' => 0,
                    ],
                    'top_used' => [],
                    'unused' => [],
                    'used_unknown' => [],
                ],
                'includes' => [
                    'counts' => [
                        'used' => 0,
                    ],
                    'used' => [],
                ],
                'livewire' => [
                    'counts' => [
                        'used' => 0,
                    ],
                    'used' => [],
                ],
                'note' => 'Usage audit file is not valid JSON. Run php artisan html:check-view-html-used again.',
            ];
        }

        $generatedAt = is_string($payload['generated_at'] ?? null)
            ? $payload['generated_at']
            : null;

        return [
            'exists' => true,
            'path' => $relativePath,
            'generated_at' => $generatedAt,
            'generated_at_formatted' => $this->formatAuditDateTime($generatedAt),
            'scan' => is_array($payload['scan'] ?? null) ? $payload['scan'] : [],
            'components' => is_array($payload['components'] ?? null) ? $payload['components'] : [],
            'native' => is_array($payload['native'] ?? null) ? $payload['native'] : [],
            'flux' => is_array($payload['flux'] ?? null) ? $payload['flux'] : [],
            'custom' => is_array($payload['custom'] ?? null) ? $payload['custom'] : [],
            'includes' => is_array($payload['includes'] ?? null) ? $payload['includes'] : [],
            'livewire' => is_array($payload['livewire'] ?? null) ? $payload['livewire'] : [],
            'note' => null,
        ];
    }

    private function formatAuditDateTime(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value)
                ->timezone(config('app.timezone'))
                ->locale(app()->getLocale())
                ->translatedFormat('d. F Y, H:i:s');
        } catch (\Throwable) {
            return $value;
        }
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

    private function selectedFinding(): ?HtmlViewAuditFinding
    {
        if ($this->selectedFindingId === null) {
            return null;
        }

        return HtmlViewAuditFinding::query()
            ->with('previousFinding:id,status,section,type,file,tag,opened_line,closing_line')
            ->find($this->selectedFindingId);
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
     * @return array<string, mixed>
     */
    private function statistics(): array
    {
        $baseQuery = $this->findingsQuery();

        $statusCounts = (clone $baseQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $sectionCounts = (clone $baseQuery)
            ->selectRaw('section, count(*) as total')
            ->groupBy('section')
            ->pluck('total', 'section')
            ->all();

        $typeCounts = (clone $baseQuery)
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->all();

        $topTags = (clone $baseQuery)
            ->whereNotNull('tag')
            ->where('tag', '!=', '')
            ->selectRaw('tag, count(*) as total')
            ->groupBy('tag')
            ->orderByDesc('total')
            ->orderBy('tag')
            ->limit(8)
            ->get()
            ->map(fn(HtmlViewAuditFinding $finding): array => [
                'tag' => (string) $finding->tag,
                'total' => (int) $finding->total,
            ])
            ->all();

        $topFiles = (clone $baseQuery)
            ->whereNotNull('file')
            ->where('file', '!=', '')
            ->selectRaw('file, count(*) as total')
            ->groupBy('file')
            ->orderByDesc('total')
            ->orderBy('file')
            ->limit(8)
            ->get()
            ->map(fn(HtmlViewAuditFinding $finding): array => [
                'file' => (string) $finding->file,
                'total' => (int) $finding->total,
            ])
            ->all();

        return [
            'total' => (clone $baseQuery)->count(),

            'by_status' => [
                'open' => (int) ($statusCounts['open'] ?? 0),
                'changed' => (int) ($statusCounts['changed'] ?? 0),
                'resolved' => (int) ($statusCounts['resolved'] ?? 0),
                'ignored' => (int) ($statusCounts['ignored'] ?? 0),
            ],

            'by_section' => [
                'native_html' => (int) ($sectionCounts['native_html'] ?? 0),
                'custom_components' => (int) ($sectionCounts['custom_components'] ?? 0),
            ],

            'by_type' => [
                'unclosed' => (int) ($typeCounts['unclosed'] ?? 0),
                'mismatched' => (int) ($typeCounts['mismatched'] ?? 0),
                'unexpected_closing' => (int) ($typeCounts['unexpected_closing'] ?? 0),
            ],

            'top_tags' => $topTags,
            'top_files' => $topFiles,
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
