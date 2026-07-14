<?php

// packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchEntries.php

namespace Gunreip\TranslationWorkbench\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class TranslationWorkbenchEntries extends Component
{
    use WithPagination;

    public string $findingSearch = '';

    public string $findingStatus = 'active';

    public string $findingKind = 'all';

    public string $findingCandidateType = 'all';

    public string $findingNamespace = 'all';

    public string $findingGroup = 'all';

    public string $findingKeyRelation = 'all';

    public string $findingSourceValue = 'all';

    public int $perPage = 25;

    public string $findingSortField = 'last_seen';

    public string $findingSortDirection = 'desc';

    public function render(): View
    {
        return view('translation-workbench::livewire.entries', [
            'findings' => $this->findings(),
            'findingStatusOptions' => $this->distinctOptions('translation_workbench_findings', 'status'),
            'findingKindOptions' => $this->distinctOptions('translation_workbench_findings', 'kind'),
            'findingCandidateTypeOptions' => $this->distinctOptions('translation_workbench_findings', 'candidate_type'),
            'findingNamespaceOptions' => $this->distinctOptions('translation_workbench_findings', 'namespace'),
            'findingGroupOptions' => $this->findingGroupOptions(),
            'databaseTableCallouts' => $this->databaseTableCallouts(),
            'healthCallouts' => $this->healthCallouts(),
            'sourceMainCoverageCallouts' => $this->sourceMainCoverageCallouts(),
            'langFilesHealthCallouts' => $this->langFilesHealthCallouts(),
            'keyCoverageCallouts' => $this->keyCoverageCallouts(),
            'localeCoverageRows' => $this->localeCoverageRows(),
            'scannerRunCallouts' => $this->scannerRunCallouts(),
            'scannerReportRows' => $this->scannerReportRows(),
            'timelineHealthCallouts' => $this->timelineHealthCallouts(),
            'dynamicValuesHealthCallouts' => $this->dynamicValuesHealthCallouts(),
            'sourceMainLocale' => $this->sourceMainLocale(),
            'findingKindCounts' => $this->distribution('translation_workbench_findings', 'kind'),
            'keyTypeCounts' => $this->distribution('translation_workbench_keys', 'key_type'),
            'localeRoleCounts' => $this->distribution('translation_workbench_lang_values', 'locale_role'),
            'localeCounts' => $this->distribution('translation_workbench_lang_values', 'locale'),
            'timelineEventCounts' => $this->distribution('translation_workbench_timeline_events', 'event_type'),
        ]);
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'findingSearch',
            'findingStatus',
            'findingKind',
            'findingCandidateType',
            'findingNamespace',
            'findingGroup',
            'findingKeyRelation',
            'findingSourceValue',
            'perPage',
        ], true)) {
            $this->perPage = $this->normalizedPerPage();
            $this->resetPage();
        }
    }

    public function updatedFindingNamespace(): void
    {
        if ($this->findingGroup !== 'all' && ! in_array($this->findingGroup, $this->findingGroupOptions(), true)) {
            $this->findingGroup = 'all';
        }
    }

    public function resetFindingFilters(): void
    {
        $this->findingSearch = '';
        $this->findingStatus = 'active';
        $this->findingKind = 'all';
        $this->findingCandidateType = 'all';
        $this->findingNamespace = 'all';
        $this->findingGroup = 'all';
        $this->findingKeyRelation = 'all';
        $this->findingSourceValue = 'all';

        $this->resetPage();
    }

    public function sortFindingsBy(string $field): void
    {
        if (! in_array($field, ['source', 'literal', 'keys'], true)) {
            return;
        }

        if ($this->findingSortField === $field) {
            $this->findingSortDirection = $this->findingSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->findingSortField = $field;
            $this->findingSortDirection = 'asc';
        }

        $this->resetPage();
    }

    private function findings(): LengthAwarePaginator
    {
        if (! $this->hasTables([
            'translation_workbench_findings',
            'translation_workbench_source_files',
            'translation_workbench_key_findings',
            'translation_workbench_keys',
            'translation_workbench_lang_values',
        ])) {
            return new LengthAwarePaginator([], 0, $this->normalizedPerPage());
        }

        $sourceLocale = $this->sourceMainLocale();
        $keyLinks = DB::table('translation_workbench_key_findings')
            ->selectRaw('finding_id, MIN(key_id) as key_id')
            ->where('status', 'active')
            ->groupBy('finding_id');

        $query = DB::table('translation_workbench_findings as findings')
            ->join('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->leftJoinSub($keyLinks, 'key_links', function ($join): void {
                $join->on('key_links.finding_id', '=', 'findings.id');
            })
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'key_links.key_id')
            ->select([
                'findings.id',
                'findings.source_line',
                'findings.kind',
                DB::raw("NULLIF(findings.function_name, '-') as function_name"),
                'findings.literal_text',
                'findings.literal_text_suggested',
                'findings.found_translation_key',
                'findings.existing_key',
                'findings.suggested_key',
                'findings.candidate_type',
                'findings.candidate_reason',
                'findings.status',
                'findings.last_seen_at',
                'source_files.path as source_path',
                'keys.id as key_id',
                'keys.translation_key',
                'keys.suggested_key as key_suggested_key',
                'keys.review_status',
                'keys.key_type',
                'keys.is_ui_key',
                'keys.is_dynamic_key',
                'keys.is_dynamic_multi',
            ])
            ->selectRaw(
                'CASE WHEN EXISTS (
                    SELECT 1
                    FROM translation_workbench_lang_values as source_values
                    WHERE source_values.locale = ?
                        AND source_values.status = ?
                        AND (
                            source_values.translation_key = keys.translation_key
                            OR source_values.translation_key = keys.suggested_key
                            OR source_values.translation_key = findings.suggested_key
                            OR source_values.translation_key = findings.found_translation_key
                        )
                ) THEN 1 ELSE 0 END as has_source_value',
                [$sourceLocale, 'active'],
            );

        $this->applyFindingFilters($query, $sourceLocale);

        $this->applyFindingSort($query);

        return $query->paginate($this->normalizedPerPage());
    }

    private function applyFindingFilters($query, string $sourceLocale): void
    {
        if ($this->findingStatus !== 'all') {
            $query->where('findings.status', $this->findingStatus);
        }

        if ($this->findingKind !== 'all') {
            $query->where('findings.kind', $this->findingKind);
        }

        if ($this->findingCandidateType !== 'all') {
            if ($this->findingCandidateType === 'NULL') {
                $query->whereNull('findings.candidate_type');
            } else {
                $query->where('findings.candidate_type', $this->findingCandidateType);
            }
        }

        if ($this->findingNamespace !== 'all') {
            if ($this->findingNamespace === 'NULL') {
                $query->whereNull('findings.namespace');
            } else {
                $query->where('findings.namespace', $this->findingNamespace);
            }
        }

        if ($this->findingGroup !== 'all') {
            if ($this->findingGroup === 'NULL') {
                $query->whereNull('findings.group');
            } else {
                $query->where('findings.group', $this->findingGroup);
            }
        }

        if ($this->findingKeyRelation === 'linked') {
            $query->whereNotNull('keys.id');
        }

        if ($this->findingKeyRelation === 'missing') {
            $query->whereNull('keys.id');
        }

        if ($this->findingSourceValue === 'yes') {
            $this->applySourceValueExistsFilter($query, $sourceLocale, true);
        }

        if ($this->findingSourceValue === 'no') {
            $this->applySourceValueExistsFilter($query, $sourceLocale, false);
        }

        $search = trim($this->findingSearch);

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';

                $query
                    ->where('source_files.path', 'like', $like)
                    ->orWhere('findings.literal_text', 'like', $like)
                    ->orWhere('findings.literal_text_suggested', 'like', $like)
                    ->orWhere('findings.found_translation_key', 'like', $like)
                    ->orWhere('findings.existing_key', 'like', $like)
                    ->orWhere('findings.suggested_key', 'like', $like)
                    ->orWhere('keys.translation_key', 'like', $like);
            });
        }
    }

    private function applySourceValueExistsFilter($query, string $sourceLocale, bool $exists): void
    {
        $method = $exists ? 'whereExists' : 'whereNotExists';

        $query->{$method}(function ($query) use ($sourceLocale): void {
            $query
                ->selectRaw('1')
                ->from('translation_workbench_lang_values as source_values')
                ->where('source_values.locale', $sourceLocale)
                ->where('source_values.status', 'active')
                ->where(function ($query): void {
                    $query
                        ->whereColumn('source_values.translation_key', 'keys.translation_key')
                        ->orWhereColumn('source_values.translation_key', 'keys.suggested_key')
                        ->orWhereColumn('source_values.translation_key', 'findings.suggested_key')
                        ->orWhereColumn('source_values.translation_key', 'findings.found_translation_key');
                });
        });
    }

    private function applyFindingSort($query): void
    {
        $direction = $this->findingSortDirection === 'asc' ? 'asc' : 'desc';

        match ($this->findingSortField) {
            'source' => $query
                ->orderBy('source_files.path', $direction)
                ->orderBy('findings.source_line', $direction),
            'literal' => $query->orderByRaw(
                'LOWER(COALESCE(findings.literal_text, findings.literal_text_suggested, ?)) ' . $direction,
                [''],
            ),
            'keys' => $query
                ->orderByRaw('CASE WHEN keys.translation_key IS NULL OR keys.translation_key = ? THEN 1 ELSE 0 END ' . $direction, [''])
                ->orderByRaw(
                    'LOWER(COALESCE(keys.translation_key, keys.suggested_key, findings.suggested_key, findings.existing_key, findings.found_translation_key, ?)) ' . $direction,
                    [''],
                ),
            default => $query->orderBy('findings.last_seen_at', $direction),
        };

        $query->orderBy('findings.id', $direction);
    }

    /**
     * @return array<int, array{table: string, count: int, color: string, icon: string, text: string}>
     */
    private function databaseTableCallouts(): array
    {
        return collect([
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_source_files',
                'color' => 'sky',
                'icon' => 'file-code',
                'text' => __('Scanned source files.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_findings',
                'color' => 'blue',
                'icon' => 'scan-search',
                'text' => __('Translation-capable code findings.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_keys',
                'color' => 'indigo',
                'icon' => 'key-round',
                'text' => __('Candidate and reviewed translation keys.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_key_findings',
                'color' => 'violet',
                'icon' => 'git-branch',
                'text' => __('Relations between keys and findings.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_lang_values',
                'color' => 'emerald',
                'icon' => 'languages',
                'text' => __('Imported language file values.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_key_values',
                'color' => 'green',
                'icon' => 'square-pen',
                'text' => __('Workbench-managed static key values.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_dynamic_key_values',
                'color' => 'teal',
                'icon' => 'list-tree',
                'text' => __('Workbench-managed dynamic option values.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_reviews',
                'color' => 'amber',
                'icon' => 'badge-check',
                'text' => __('Review decisions and review context.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_event_types',
                'color' => 'orange',
                'icon' => 'tags',
                'text' => __('Normalized timeline event definitions.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_timeline_events',
                'color' => 'rose',
                'icon' => 'activity',
                'text' => __('Concrete timeline and audit events.'),
            ],
        ])
            ->map(fn(array $callout): array => [
                ...$callout,
                'count' => $this->tableCount($callout['table']),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function distinctOptions(string $table, string $column): array
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return [];
        }

        $wrappedColumn = DB::getQueryGrammar()->wrap($column);

        return DB::table($table)
            ->selectRaw("COALESCE(CAST({$wrappedColumn} AS TEXT), 'NULL') as value")
            ->distinct()
            ->orderBy('value')
            ->pluck('value')
            ->map(static fn($value): string => (string) $value)
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function findingGroupOptions(): array
    {
        if (! Schema::hasTable('translation_workbench_findings') || ! Schema::hasColumn('translation_workbench_findings', 'group')) {
            return [];
        }

        $query = DB::table('translation_workbench_findings');

        if ($this->findingNamespace !== 'all') {
            if ($this->findingNamespace === 'NULL') {
                $query->whereNull('namespace');
            } else {
                $query->where('namespace', $this->findingNamespace);
            }
        }

        return $query
            ->selectRaw("COALESCE(CAST(\"group\" AS TEXT), 'NULL') as value")
            ->distinct()
            ->orderBy('value')
            ->pluck('value')
            ->map(static fn($value): string => (string) $value)
            ->all();
    }

    private function normalizedPerPage(): int
    {
        return in_array($this->perPage, [10, 25, 50, 100], true)
            ? $this->perPage
            : 25;
    }

    /**
     * @return array<int, array{title: string, count: int, color: string, icon: string, text: string}>
     */
    private function healthCallouts(): array
    {
        return [
            $this->healthCallout(
                title: __('Findings without key'),
                count: $this->findingsWithoutKeyRelations(),
                icon: 'scan-search',
                text: __('Findings that currently have no key relation.'),
            ),
            $this->healthCallout(
                title: __('Keys without finding'),
                count: $this->keysWithoutFindings(),
                icon: 'key-round',
                text: __('Keys that currently have no finding relation.'),
            ),
            $this->healthCallout(
                title: __('Lang values without key'),
                count: $this->langValuesWithoutKeyCandidate(),
                icon: 'languages',
                text: __('Imported lang values that do not match a key candidate.'),
            ),
            $this->healthCallout(
                title: __('Keys without source value'),
                count: $this->keysWithoutSourceMainValue(),
                icon: 'file-question-mark',
                text: __('Keys without matching SourceMainLanguage value.'),
            ),
            $this->healthCallout(
                title: __('Obsolete lang values'),
                count: $this->obsoleteLangValues(),
                icon: 'archive-x',
                text: __('Imported lang values no longer present in the current lang files.'),
            ),
            $this->healthCallout(
                title: __('Timeline without type'),
                count: $this->timelineEventsWithoutEventType(),
                icon: 'activity',
                text: __('Timeline events without a normalized event type relation.'),
            ),
            $this->healthCallout(
                title: __('Source files without findings'),
                count: $this->sourceFilesWithoutFindings(),
                icon: 'file-x',
                text: __('Scanned source files that currently have no findings.'),
            ),
        ];
    }

    /**
     * @return array{title: string, count: int, color: string, icon: string, text: string}
     */
    private function healthCallout(string $title, int $count, string $icon, string $text): array
    {
        return [
            'title' => $title,
            'count' => $count,
            'color' => $count > 0 ? 'amber' : 'green',
            'icon' => $count > 0 ? $icon : 'check-circle',
            'text' => $text,
        ];
    }

    /**
     * @return array<int, array{title: string, count: int, color: string, icon: string, text: string}>
     */
    private function sourceMainCoverageCallouts(): array
    {
        $sourceLocale = $this->sourceMainLocale();
        $unmatchedSourceValues = $this->unmatchedSourceMainLangValues();
        $keysWithoutSourceValue = $this->keysWithoutSourceMainValue();
        $obsoleteSourceValues = $this->obsoleteSourceMainLangValues();

        return [
            [
                'title' => __('Source values'),
                'count' => $this->sourceMainLangValues(),
                'color' => 'sky',
                'icon' => 'languages',
                'text' => __('Active SourceMainLanguage values imported from lang/:locale.', ['locale' => $sourceLocale]),
            ],
            [
                'title' => __('Source namespaces'),
                'count' => $this->sourceMainNamespaces(),
                'color' => 'blue',
                'icon' => 'folder-tree',
                'text' => __('Namespaces present in the SourceMainLanguage lang files.'),
            ],
            [
                'title' => __('Matched source values'),
                'count' => $this->matchedSourceMainLangValues(),
                'color' => 'green',
                'icon' => 'link',
                'text' => __('SourceMainLanguage values matching a current key candidate.'),
            ],
            [
                'title' => __('Unmatched source values'),
                'count' => $unmatchedSourceValues,
                'color' => $unmatchedSourceValues > 0 ? 'amber' : 'green',
                'icon' => $unmatchedSourceValues > 0 ? 'unlink' : 'check-circle',
                'text' => __('SourceMainLanguage values without a current key candidate.'),
            ],
            [
                'title' => __('Keys with source value'),
                'count' => $this->keysWithSourceMainValue(),
                'color' => 'emerald',
                'icon' => 'key-round',
                'text' => __('Keys that already have a matching SourceMainLanguage value.'),
            ],
            [
                'title' => __('Keys without source value'),
                'count' => $keysWithoutSourceValue,
                'color' => $keysWithoutSourceValue > 0 ? 'amber' : 'green',
                'icon' => $keysWithoutSourceValue > 0 ? 'file-question-mark' : 'check-circle',
                'text' => __('Keys that still have no matching SourceMainLanguage value.'),
            ],
            [
                'title' => __('Obsolete source values'),
                'count' => $obsoleteSourceValues,
                'color' => $obsoleteSourceValues > 0 ? 'amber' : 'green',
                'icon' => $obsoleteSourceValues > 0 ? 'archive-x' : 'check-circle',
                'text' => __('Obsolete values in the SourceMainLanguage inventory.'),
            ],
        ];
    }

    /**
     * @return array<int, array{title: string, count: int, color: string, icon: string, text: string}>
     */
    private function langFilesHealthCallouts(): array
    {
        $obsoleteValues = $this->langValueCountWhere('status', 'obsolete');
        $filesWithObsoleteValues = $this->langFilesWithStatus('obsolete');

        return [
            [
                'title' => __('Lang files'),
                'count' => $this->distinctLangValueCount('source_path'),
                'color' => 'sky',
                'icon' => 'folder-tree',
                'text' => __('Distinct lang files imported into the Workbench inventory.'),
            ],
            [
                'title' => __('Source files'),
                'count' => $this->sourceMainLangFiles(),
                'color' => 'blue',
                'icon' => 'file-code',
                'text' => __('Lang files for the SourceMainLanguage.'),
            ],
            [
                'title' => __('Target files'),
                'count' => $this->targetLangFiles(),
                'color' => 'emerald',
                'icon' => 'files',
                'text' => __('Lang files for target main and sub languages.'),
            ],
            [
                'title' => __('Locales'),
                'count' => $this->distinctLangValueCount('locale'),
                'color' => 'green',
                'icon' => 'globe',
                'text' => __('Locales currently represented in imported lang files.'),
            ],
            [
                'title' => __('Namespaces'),
                'count' => $this->distinctLangValueCount('namespace'),
                'color' => 'indigo',
                'icon' => 'package',
                'text' => __('Namespaces currently represented in imported lang files.'),
            ],
            [
                'title' => __('Active values'),
                'count' => $this->langValueCountWhere('status', 'active'),
                'color' => 'teal',
                'icon' => 'check-circle',
                'text' => __('Active values currently present in the imported lang files.'),
            ],
            [
                'title' => __('Obsolete values'),
                'count' => $obsoleteValues,
                'color' => $obsoleteValues > 0 ? 'amber' : 'green',
                'icon' => $obsoleteValues > 0 ? 'archive-x' : 'check-circle',
                'text' => __('Imported lang values no longer present in the current lang files.'),
            ],
            [
                'title' => __('Files with obsolete values'),
                'count' => $filesWithObsoleteValues,
                'color' => $filesWithObsoleteValues > 0 ? 'amber' : 'green',
                'icon' => $filesWithObsoleteValues > 0 ? 'file-exclamation-point' : 'check-circle',
                'text' => __('Lang files that still contain obsolete inventory entries.'),
            ],
        ];
    }

    /**
     * @return array<int, array{title: string, count: int, color: string, icon: string, text: string}>
     */
    private function keyCoverageCallouts(): array
    {
        $keysWithoutFinding = $this->keysWithoutFindings();
        $keysWithoutSourceValue = $this->keysWithoutSourceMainValue();

        return [
            [
                'title' => __('Keys total'),
                'count' => $this->tableCount('translation_workbench_keys'),
                'color' => 'indigo',
                'icon' => 'key-round',
                'text' => __('All key candidates in the new Workbench key inventory.'),
            ],
            [
                'title' => __('Open keys'),
                'count' => $this->keyCountWhere('status', 'open'),
                'color' => 'amber',
                'icon' => 'circle-dot',
                'text' => __('Keys that are still open in the review workflow.'),
            ],
            [
                'title' => __('Reviewed keys'),
                'count' => $this->keyCountWhere('review_status', 'reviewed'),
                'color' => 'green',
                'icon' => 'badge-check',
                'text' => __('Keys that already have a reviewed status.'),
            ],
            [
                'title' => __('UI keys'),
                'count' => $this->booleanKeyCount('is_ui_key'),
                'color' => 'sky',
                'icon' => 'layout-panel-top',
                'text' => __('Keys explicitly classified as reusable UI translations.'),
            ],
            [
                'title' => __('Dynamic keys'),
                'count' => $this->booleanKeyCount('is_dynamic_key'),
                'color' => 'teal',
                'icon' => 'braces',
                'text' => __('Keys explicitly classified as dynamic translations.'),
            ],
            [
                'title' => __('Dynamic multi keys'),
                'count' => $this->booleanKeyCount('is_dynamic_multi'),
                'color' => 'cyan',
                'icon' => 'list-tree',
                'text' => __('Dynamic keys that can own multiple option values.'),
            ],
            [
                'title' => __('Keys with finding'),
                'count' => $this->keysWithFindings(),
                'color' => 'violet',
                'icon' => 'git-branch',
                'text' => __('Keys that are linked to at least one active code finding.'),
            ],
            [
                'title' => __('Keys without finding'),
                'count' => $keysWithoutFinding,
                'color' => $keysWithoutFinding > 0 ? 'amber' : 'green',
                'icon' => $keysWithoutFinding > 0 ? 'unlink' : 'check-circle',
                'text' => __('Keys that currently have no active code finding relation.'),
            ],
            [
                'title' => __('Keys with source value'),
                'count' => $this->keysWithSourceMainValue(),
                'color' => 'emerald',
                'icon' => 'languages',
                'text' => __('Keys that already match an active SourceMainLanguage value.'),
            ],
            [
                'title' => __('Keys without source value'),
                'count' => $keysWithoutSourceValue,
                'color' => $keysWithoutSourceValue > 0 ? 'amber' : 'green',
                'icon' => $keysWithoutSourceValue > 0 ? 'file-question-mark' : 'check-circle',
                'text' => __('Keys that still have no active SourceMainLanguage value.'),
            ],
        ];
    }

    /**
     * @return array<int, array{title: string, count: int, color: string, icon: string, text: string}>
     */
    private function scannerRunCallouts(): array
    {
        $rows = $this->scannerReportRows();
        $latestGeneratedAt = collect($rows)
            ->pluck('generated_at')
            ->filter()
            ->sortDesc()
            ->first();

        return [
            [
                'title' => __('Reports'),
                'count' => count($rows),
                'color' => 'sky',
                'icon' => 'clipboard-list',
                'text' => __('JSON reports currently available in storage/translation-workbench.'),
            ],
            [
                'title' => __('Commands'),
                'count' => collect($rows)->pluck('command')->filter()->unique()->count(),
                'color' => 'blue',
                'icon' => 'terminal',
                'text' => __('Distinct Workbench commands represented by the available reports.'),
            ],
            [
                'title' => __('Last scanned files'),
                'count' => (int) (collect($rows)->firstWhere('command', 'translation-workbench:scan')['files'] ?? 0),
                'color' => 'indigo',
                'icon' => 'files',
                'text' => __('Files from the latest translation-workbench:scan report.'),
            ],
            [
                'title' => __('Last findings'),
                'count' => (int) (collect($rows)->firstWhere('command', 'translation-workbench:scan')['found'] ?? 0),
                'color' => 'violet',
                'icon' => 'scan-search',
                'text' => __('Findings from the latest translation-workbench:scan report.'),
            ],
            [
                'title' => __('Active source files'),
                'count' => $this->sourceFileCountWhere('status', 'active'),
                'color' => 'emerald',
                'icon' => 'file-code',
                'text' => __('Active source files currently stored in the Workbench inventory.'),
            ],
            [
                'title' => __('Stale source files'),
                'count' => $this->sourceFileCountWhere('status', 'stale'),
                'color' => 'amber',
                'icon' => 'file-exclamation-point',
                'text' => __('Source files marked stale by scanner synchronization.'),
            ],
            [
                'title' => __('Obsolete source files'),
                'count' => $this->sourceFileCountWhere('status', 'obsolete'),
                'color' => 'rose',
                'icon' => 'archive-x',
                'text' => __('Source files marked obsolete by scanner synchronization.'),
            ],
            [
                'title' => __('Latest report'),
                'count' => $latestGeneratedAt ? 1 : 0,
                'color' => $latestGeneratedAt ? 'green' : 'amber',
                'icon' => $latestGeneratedAt ? 'clock-check' : 'clock-alert',
                'text' => $latestGeneratedAt
                    ? __('Latest report generated at :timestamp.', ['timestamp' => $latestGeneratedAt])
                    : __('No Workbench report timestamp found.'),
            ],
        ];
    }

    /**
     * @return array<int, array{file: string, command: string, generated_at: string|null, files: int, found: int, files_found: int, scanned_paths: int, file_patterns: int, size_kb: int, modified_at: string|null}>
     */
    private function scannerReportRows(): array
    {
        $reportPath = storage_path('translation-workbench');

        if (! File::isDirectory($reportPath)) {
            return [];
        }

        return collect(File::files($reportPath))
            ->filter(static fn($file): bool => $file->getExtension() === 'json')
            ->map(function ($file): array {
                $data = json_decode(File::get($file->getPathname()), true) ?: [];
                $rawData = is_array($data['raw_data'] ?? null) ? $data['raw_data'] : [];

                return [
                    'file' => $file->getFilename(),
                    'command' => (string) ($data['command'] ?? '-'),
                    'generated_at' => isset($data['generated_at']) ? (string) $data['generated_at'] : null,
                    'files' => (int) ($rawData['files'] ?? 0),
                    'found' => (int) ($rawData['found'] ?? 0),
                    'files_found' => is_array($rawData['files_found'] ?? null) ? count($rawData['files_found']) : 0,
                    'scanned_paths' => is_array($rawData['scanned_paths'] ?? null) ? count($rawData['scanned_paths']) : 0,
                    'file_patterns' => is_array($rawData['file_patterns'] ?? null) ? count($rawData['file_patterns']) : 0,
                    'size_kb' => (int) ceil($file->getSize() / 1024),
                    'modified_at' => $file->getMTime() ? date('Y-m-d H:i:s', $file->getMTime()) : null,
                ];
            })
            ->sortBy(static fn(array $row): string => $row['generated_at'] ?? $row['modified_at'] ?? '')
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{title: string, count: int, color: string, icon: string, text: string}>
     */
    private function timelineHealthCallouts(): array
    {
        $eventsWithoutType = $this->timelineEventsWithoutEventType();
        $orphanEvents = $this->orphanTimelineEvents();
        $reviewsWithoutSubject = $this->reviewsWithoutSubject();

        return [
            [
                'title' => __('Timeline events'),
                'count' => $this->tableCount('translation_workbench_timeline_events'),
                'color' => 'rose',
                'icon' => 'activity',
                'text' => __('Concrete timeline events written by scanner and review workflows.'),
            ],
            [
                'title' => __('Event types'),
                'count' => $this->tableCount('translation_workbench_event_types'),
                'color' => 'orange',
                'icon' => 'tags',
                'text' => __('Normalized event type definitions referenced by timeline events.'),
            ],
            [
                'title' => __('Events without type'),
                'count' => $eventsWithoutType,
                'color' => $eventsWithoutType > 0 ? 'amber' : 'green',
                'icon' => $eventsWithoutType > 0 ? 'unlink' : 'check-circle',
                'text' => __('Timeline events without a valid normalized event type relation.'),
            ],
            [
                'title' => __('Orphan events'),
                'count' => $orphanEvents,
                'color' => $orphanEvents > 0 ? 'amber' : 'green',
                'icon' => $orphanEvents > 0 ? 'circle-alert' : 'check-circle',
                'text' => __('Timeline events without key, finding or review context.'),
            ],
            [
                'title' => __('Reviews'),
                'count' => $this->tableCount('translation_workbench_reviews'),
                'color' => 'sky',
                'icon' => 'badge-check',
                'text' => __('Review decisions stored for keys and findings.'),
            ],
            [
                'title' => __('Reviews without subject'),
                'count' => $reviewsWithoutSubject,
                'color' => $reviewsWithoutSubject > 0 ? 'amber' : 'green',
                'icon' => $reviewsWithoutSubject > 0 ? 'badge-alert' : 'check-circle',
                'text' => __('Review records without key or finding context.'),
            ],
        ];
    }

    /**
     * @return array<int, array{title: string, count: int, color: string, icon: string, text: string}>
     */
    private function dynamicValuesHealthCallouts(): array
    {
        $multiKeysWithoutValues = $this->dynamicMultiKeysWithoutValues();
        $valuesWithoutDynamicKey = $this->dynamicValuesWithoutDynamicKey();

        return [
            [
                'title' => __('Dynamic keys'),
                'count' => $this->booleanKeyCount('is_dynamic_key'),
                'color' => 'teal',
                'icon' => 'braces',
                'text' => __('Keys explicitly marked as dynamic translations.'),
            ],
            [
                'title' => __('Dynamic multi keys'),
                'count' => $this->booleanKeyCount('is_dynamic_multi'),
                'color' => 'cyan',
                'icon' => 'list-tree',
                'text' => __('Dynamic keys that can own multiple option values.'),
            ],
            [
                'title' => __('Dynamic values'),
                'count' => $this->tableCount('translation_workbench_dynamic_key_values'),
                'color' => 'indigo',
                'icon' => 'list-plus',
                'text' => __('Stored dynamic option values across value keys and locales.'),
            ],
            [
                'title' => __('Value keys'),
                'count' => $this->distinctDynamicValueCount('value_key'),
                'color' => 'blue',
                'icon' => 'key-round',
                'text' => __('Distinct dynamic option value keys currently stored.'),
            ],
            [
                'title' => __('Dynamic locales'),
                'count' => $this->distinctDynamicValueCount('locale'),
                'color' => 'emerald',
                'icon' => 'globe',
                'text' => __('Locales currently represented by dynamic option values.'),
            ],
            [
                'title' => __('Missing dynamic values'),
                'count' => $this->dynamicValueCountWhere('status', 'missing'),
                'color' => 'amber',
                'icon' => 'file-question-mark',
                'text' => __('Dynamic option values still marked as missing.'),
            ],
            [
                'title' => __('Multi keys without values'),
                'count' => $multiKeysWithoutValues,
                'color' => $multiKeysWithoutValues > 0 ? 'amber' : 'green',
                'icon' => $multiKeysWithoutValues > 0 ? 'list-x' : 'check-circle',
                'text' => __('Dynamic multi keys without stored dynamic option values.'),
            ],
            [
                'title' => __('Values without dynamic key'),
                'count' => $valuesWithoutDynamicKey,
                'color' => $valuesWithoutDynamicKey > 0 ? 'amber' : 'green',
                'icon' => $valuesWithoutDynamicKey > 0 ? 'unlink' : 'check-circle',
                'text' => __('Dynamic values linked to keys not marked as dynamic.'),
            ],
        ];
    }

    /**
     * @return array<int, array{locale: string, locale_role: string, main_locale: string|null, parent_locale: string|null, values: int, matched: int, missing: int, extra: int, coverage: float, color: string}>
     */
    private function localeCoverageRows(): array
    {
        if (! $this->hasLangValueCoverageColumns()) {
            return [];
        }

        $sourceLocale = $this->sourceMainLocale();
        $sourceTotal = $this->activeLangValueCountForLocale($sourceLocale);

        return DB::table('translation_workbench_lang_values')
            ->selectRaw('locale, locale_role, main_locale, parent_locale, COUNT(DISTINCT translation_key) as values_count')
            ->where('status', 'active')
            ->groupBy('locale', 'locale_role', 'main_locale', 'parent_locale')
            ->orderByRaw("CASE WHEN locale = ? THEN 0 ELSE 1 END", [$sourceLocale])
            ->orderBy('main_locale')
            ->orderBy('locale')
            ->get()
            ->map(function ($row) use ($sourceLocale, $sourceTotal): array {
                $locale = (string) $row->locale;
                $values = (int) $row->values_count;
                $matched = $locale === $sourceLocale
                    ? $sourceTotal
                    : $this->matchedLangValueCountForLocale($locale, $sourceLocale);
                $missing = max(0, $sourceTotal - $matched);
                $extra = $locale === $sourceLocale
                    ? 0
                    : $this->extraLangValueCountForLocale($locale, $sourceLocale);
                $coverage = $sourceTotal > 0
                    ? round(($matched / $sourceTotal) * 100, 1)
                    : 0.0;

                return [
                    'locale' => $locale,
                    'locale_role' => (string) $row->locale_role,
                    'main_locale' => $row->main_locale !== null ? (string) $row->main_locale : null,
                    'parent_locale' => $row->parent_locale !== null ? (string) $row->parent_locale : null,
                    'values' => $values,
                    'matched' => $matched,
                    'missing' => $missing,
                    'extra' => $extra,
                    'coverage' => $coverage,
                    'color' => $this->coverageColor($coverage, $missing),
                ];
            })
            ->all();
    }

    private function activeLangValueCountForLocale(string $locale): int
    {
        return (int) DB::table('translation_workbench_lang_values')
            ->where('locale', $locale)
            ->where('status', 'active')
            ->distinct()
            ->count('translation_key');
    }

    private function matchedLangValueCountForLocale(string $locale, string $sourceLocale): int
    {
        return (int) DB::table('translation_workbench_lang_values as target_values')
            ->where('target_values.locale', $locale)
            ->where('target_values.status', 'active')
            ->whereExists(function ($query) use ($sourceLocale): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_lang_values as source_values')
                    ->where('source_values.locale', $sourceLocale)
                    ->where('source_values.status', 'active')
                    ->whereColumn('source_values.translation_key', 'target_values.translation_key');
            })
            ->distinct()
            ->count('target_values.translation_key');
    }

    private function extraLangValueCountForLocale(string $locale, string $sourceLocale): int
    {
        return (int) DB::table('translation_workbench_lang_values as target_values')
            ->where('target_values.locale', $locale)
            ->where('target_values.status', 'active')
            ->whereNotExists(function ($query) use ($sourceLocale): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_lang_values as source_values')
                    ->where('source_values.locale', $sourceLocale)
                    ->where('source_values.status', 'active')
                    ->whereColumn('source_values.translation_key', 'target_values.translation_key');
            })
            ->distinct()
            ->count('target_values.translation_key');
    }

    private function coverageColor(float $coverage, int $missing): string
    {
        if ($missing === 0 && $coverage >= 100.0) {
            return 'green';
        }

        if ($coverage >= 80.0) {
            return 'amber';
        }

        return 'red';
    }

    private function tableCount(string $table): int
    {
        return Schema::hasTable($table)
            ? (int) DB::table($table)->count()
            : 0;
    }

    /**
     * @return array<int, array{label: string, count: int}>
     */
    private function distribution(string $table, string $column): array
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return [];
        }

        $wrappedColumn = DB::getQueryGrammar()->wrap($column);

        return DB::table($table)
            ->selectRaw("COALESCE(CAST({$wrappedColumn} AS TEXT), 'NULL') as label, COUNT(*) as total")
            ->groupByRaw("COALESCE(CAST({$wrappedColumn} AS TEXT), 'NULL')")
            ->orderByDesc('total')
            ->orderBy('label')
            ->limit(12)
            ->get()
            ->map(static fn($row): array => [
                'label' => (string) $row->label,
                'count' => (int) $row->total,
            ])
            ->all();
    }

    private function findingsWithoutKeyRelations(): int
    {
        if (! $this->hasTables(['translation_workbench_findings', 'translation_workbench_key_findings'])) {
            return 0;
        }

        return (int) DB::table('translation_workbench_findings')
            ->leftJoin('translation_workbench_key_findings', function ($join): void {
                $join
                    ->on('translation_workbench_key_findings.finding_id', '=', 'translation_workbench_findings.id')
                    ->where('translation_workbench_key_findings.status', '=', 'active');
            })
            ->whereNull('translation_workbench_key_findings.id')
            ->count();
    }

    private function keysWithoutFindings(): int
    {
        if (! $this->hasTables(['translation_workbench_keys', 'translation_workbench_key_findings'])) {
            return 0;
        }

        return (int) DB::table('translation_workbench_keys')
            ->leftJoin('translation_workbench_key_findings', function ($join): void {
                $join
                    ->on('translation_workbench_key_findings.key_id', '=', 'translation_workbench_keys.id')
                    ->where('translation_workbench_key_findings.status', '=', 'active');
            })
            ->whereNull('translation_workbench_key_findings.id')
            ->count();
    }

    private function keysWithFindings(): int
    {
        if (! $this->hasTables(['translation_workbench_keys', 'translation_workbench_key_findings'])) {
            return 0;
        }

        return (int) DB::table('translation_workbench_keys')
            ->whereExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_key_findings')
                    ->whereColumn('translation_workbench_key_findings.key_id', 'translation_workbench_keys.id')
                    ->where('translation_workbench_key_findings.status', 'active');
            })
            ->count();
    }

    private function keyCountWhere(string $column, string $value): int
    {
        if (! Schema::hasTable('translation_workbench_keys') || ! Schema::hasColumn('translation_workbench_keys', $column)) {
            return 0;
        }

        return (int) DB::table('translation_workbench_keys')
            ->where($column, $value)
            ->count();
    }

    private function booleanKeyCount(string $column): int
    {
        if (! Schema::hasTable('translation_workbench_keys') || ! Schema::hasColumn('translation_workbench_keys', $column)) {
            return 0;
        }

        return (int) DB::table('translation_workbench_keys')
            ->where($column, true)
            ->count();
    }

    private function sourceFileCountWhere(string $column, string $value): int
    {
        if (! Schema::hasTable('translation_workbench_source_files') || ! Schema::hasColumn('translation_workbench_source_files', $column)) {
            return 0;
        }

        return (int) DB::table('translation_workbench_source_files')
            ->where($column, $value)
            ->count();
    }

    private function langValuesWithoutKeyCandidate(): int
    {
        if (! $this->hasTables(['translation_workbench_lang_values', 'translation_workbench_keys'])) {
            return 0;
        }

        return (int) DB::table('translation_workbench_lang_values')
            ->where('translation_workbench_lang_values.status', 'active')
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_keys')
                    ->where(function ($query): void {
                        $query
                            ->whereColumn('translation_workbench_keys.translation_key', 'translation_workbench_lang_values.translation_key')
                            ->orWhereColumn('translation_workbench_keys.suggested_key', 'translation_workbench_lang_values.translation_key');
                    });
            })
            ->count();
    }

    private function keysWithoutSourceMainValue(): int
    {
        if (! $this->hasTables(['translation_workbench_keys', 'translation_workbench_lang_values'])) {
            return 0;
        }

        $sourceLocale = (string) config('translation-workbench.source_locale', 'en');

        return (int) DB::table('translation_workbench_keys')
            ->whereNotExists(function ($query) use ($sourceLocale): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_lang_values')
                    ->where('translation_workbench_lang_values.locale', '=', $sourceLocale)
                    ->where('translation_workbench_lang_values.status', '=', 'active')
                    ->where(function ($query): void {
                        $query
                            ->whereColumn('translation_workbench_lang_values.translation_key', 'translation_workbench_keys.translation_key')
                            ->orWhereColumn('translation_workbench_lang_values.translation_key', 'translation_workbench_keys.suggested_key');
                    });
            })
            ->count();
    }

    private function sourceMainLangValues(): int
    {
        if (! Schema::hasTable('translation_workbench_lang_values')) {
            return 0;
        }

        return (int) DB::table('translation_workbench_lang_values')
            ->where('locale', $this->sourceMainLocale())
            ->where('status', 'active')
            ->count();
    }

    private function sourceMainNamespaces(): int
    {
        if (! Schema::hasTable('translation_workbench_lang_values')) {
            return 0;
        }

        return (int) DB::table('translation_workbench_lang_values')
            ->where('locale', $this->sourceMainLocale())
            ->where('status', 'active')
            ->distinct()
            ->count('namespace');
    }

    private function sourceMainLangFiles(): int
    {
        if (! Schema::hasTable('translation_workbench_lang_values')) {
            return 0;
        }

        return (int) DB::table('translation_workbench_lang_values')
            ->where('locale', $this->sourceMainLocale())
            ->distinct()
            ->count('source_path');
    }

    private function targetLangFiles(): int
    {
        if (! Schema::hasTable('translation_workbench_lang_values')) {
            return 0;
        }

        return (int) DB::table('translation_workbench_lang_values')
            ->where('locale', '!=', $this->sourceMainLocale())
            ->distinct()
            ->count('source_path');
    }

    private function langValueCountWhere(string $column, string $value): int
    {
        if (! Schema::hasTable('translation_workbench_lang_values') || ! Schema::hasColumn('translation_workbench_lang_values', $column)) {
            return 0;
        }

        return (int) DB::table('translation_workbench_lang_values')
            ->where($column, $value)
            ->count();
    }

    private function distinctLangValueCount(string $column): int
    {
        if (! Schema::hasTable('translation_workbench_lang_values') || ! Schema::hasColumn('translation_workbench_lang_values', $column)) {
            return 0;
        }

        return (int) DB::table('translation_workbench_lang_values')
            ->distinct()
            ->count($column);
    }

    private function langFilesWithStatus(string $status): int
    {
        if (! Schema::hasTable('translation_workbench_lang_values')) {
            return 0;
        }

        return (int) DB::table('translation_workbench_lang_values')
            ->where('status', $status)
            ->distinct()
            ->count('source_path');
    }

    private function matchedSourceMainLangValues(): int
    {
        if (! $this->hasTables(['translation_workbench_lang_values', 'translation_workbench_keys'])) {
            return 0;
        }

        return (int) DB::table('translation_workbench_lang_values')
            ->where('translation_workbench_lang_values.locale', $this->sourceMainLocale())
            ->where('translation_workbench_lang_values.status', 'active')
            ->whereExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_keys')
                    ->where(function ($query): void {
                        $query
                            ->whereColumn('translation_workbench_keys.translation_key', 'translation_workbench_lang_values.translation_key')
                            ->orWhereColumn('translation_workbench_keys.suggested_key', 'translation_workbench_lang_values.translation_key');
                    });
            })
            ->count();
    }

    private function unmatchedSourceMainLangValues(): int
    {
        if (! $this->hasTables(['translation_workbench_lang_values', 'translation_workbench_keys'])) {
            return 0;
        }

        return (int) DB::table('translation_workbench_lang_values')
            ->where('translation_workbench_lang_values.locale', $this->sourceMainLocale())
            ->where('translation_workbench_lang_values.status', 'active')
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_keys')
                    ->where(function ($query): void {
                        $query
                            ->whereColumn('translation_workbench_keys.translation_key', 'translation_workbench_lang_values.translation_key')
                            ->orWhereColumn('translation_workbench_keys.suggested_key', 'translation_workbench_lang_values.translation_key');
                    });
            })
            ->count();
    }

    private function keysWithSourceMainValue(): int
    {
        if (! $this->hasTables(['translation_workbench_keys', 'translation_workbench_lang_values'])) {
            return 0;
        }

        $sourceLocale = $this->sourceMainLocale();

        return (int) DB::table('translation_workbench_keys')
            ->whereExists(function ($query) use ($sourceLocale): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_lang_values')
                    ->where('translation_workbench_lang_values.locale', '=', $sourceLocale)
                    ->where('translation_workbench_lang_values.status', '=', 'active')
                    ->where(function ($query): void {
                        $query
                            ->whereColumn('translation_workbench_lang_values.translation_key', 'translation_workbench_keys.translation_key')
                            ->orWhereColumn('translation_workbench_lang_values.translation_key', 'translation_workbench_keys.suggested_key');
                    });
            })
            ->count();
    }

    private function obsoleteSourceMainLangValues(): int
    {
        if (! Schema::hasTable('translation_workbench_lang_values')) {
            return 0;
        }

        return (int) DB::table('translation_workbench_lang_values')
            ->where('locale', $this->sourceMainLocale())
            ->where('status', 'obsolete')
            ->count();
    }

    private function sourceMainLocale(): string
    {
        return (string) config('translation-workbench.source_locale', 'en');
    }

    private function obsoleteLangValues(): int
    {
        if (! Schema::hasTable('translation_workbench_lang_values')) {
            return 0;
        }

        return (int) DB::table('translation_workbench_lang_values')
            ->where('status', 'obsolete')
            ->count();
    }

    private function timelineEventsWithoutEventType(): int
    {
        if (! $this->hasTables(['translation_workbench_timeline_events', 'translation_workbench_event_types'])) {
            return 0;
        }

        return (int) DB::table('translation_workbench_timeline_events')
            ->leftJoin('translation_workbench_event_types', 'translation_workbench_event_types.id', '=', 'translation_workbench_timeline_events.event_type_id')
            ->where(function ($query): void {
                $query
                    ->whereNull('translation_workbench_timeline_events.event_type_id')
                    ->orWhereNull('translation_workbench_event_types.id');
            })
            ->count();
    }

    private function orphanTimelineEvents(): int
    {
        if (! Schema::hasTable('translation_workbench_timeline_events')) {
            return 0;
        }

        return (int) DB::table('translation_workbench_timeline_events')
            ->whereNull('key_id')
            ->whereNull('finding_id')
            ->whereNull('review_id')
            ->count();
    }

    private function reviewsWithoutSubject(): int
    {
        if (! Schema::hasTable('translation_workbench_reviews')) {
            return 0;
        }

        return (int) DB::table('translation_workbench_reviews')
            ->whereNull('key_id')
            ->whereNull('finding_id')
            ->count();
    }

    private function dynamicMultiKeysWithoutValues(): int
    {
        if (! $this->hasTables(['translation_workbench_keys', 'translation_workbench_dynamic_key_values'])) {
            return 0;
        }

        return (int) DB::table('translation_workbench_keys')
            ->where('is_dynamic_multi', true)
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_dynamic_key_values')
                    ->whereColumn('translation_workbench_dynamic_key_values.key_id', 'translation_workbench_keys.id');
            })
            ->count();
    }

    private function dynamicValuesWithoutDynamicKey(): int
    {
        if (! $this->hasTables(['translation_workbench_dynamic_key_values', 'translation_workbench_keys'])) {
            return 0;
        }

        return (int) DB::table('translation_workbench_dynamic_key_values')
            ->join('translation_workbench_keys', 'translation_workbench_keys.id', '=', 'translation_workbench_dynamic_key_values.key_id')
            ->where('translation_workbench_keys.is_dynamic_key', false)
            ->count();
    }

    private function dynamicValueCountWhere(string $column, string $value): int
    {
        if (! Schema::hasTable('translation_workbench_dynamic_key_values') || ! Schema::hasColumn('translation_workbench_dynamic_key_values', $column)) {
            return 0;
        }

        return (int) DB::table('translation_workbench_dynamic_key_values')
            ->where($column, $value)
            ->count();
    }

    private function distinctDynamicValueCount(string $column): int
    {
        if (! Schema::hasTable('translation_workbench_dynamic_key_values') || ! Schema::hasColumn('translation_workbench_dynamic_key_values', $column)) {
            return 0;
        }

        return (int) DB::table('translation_workbench_dynamic_key_values')
            ->distinct()
            ->count($column);
    }

    private function sourceFilesWithoutFindings(): int
    {
        if (! $this->hasTables(['translation_workbench_source_files', 'translation_workbench_findings'])) {
            return 0;
        }

        return (int) DB::table('translation_workbench_source_files')
            ->leftJoin('translation_workbench_findings', 'translation_workbench_findings.source_file_id', '=', 'translation_workbench_source_files.id')
            ->whereNull('translation_workbench_findings.id')
            ->count();
    }

    private function hasLangValueCoverageColumns(): bool
    {
        return Schema::hasTable('translation_workbench_lang_values')
            && collect([
                'locale',
                'locale_role',
                'main_locale',
                'parent_locale',
                'translation_key',
                'status',
            ])->every(static fn(string $column): bool => Schema::hasColumn('translation_workbench_lang_values', $column));
    }

    /**
     * @param  array<int, string>  $tables
     */
    private function hasTables(array $tables): bool
    {
        return collect($tables)->every(static fn(string $table): bool => Schema::hasTable($table));
    }
}
