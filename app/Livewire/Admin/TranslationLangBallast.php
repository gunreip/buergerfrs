<?php

// app/Livewire/Admin/TranslationLangBallast.php

namespace App\Livewire\Admin;

use App\Models\TranslationLangBallastDecision;
use App\Models\TranslationLanguage;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithPagination;

class TranslationLangBallast extends Component
{
    use WithPagination;

    public array $summary = [];

    public ?string $generatedAt = null;

    public array $removeActionEntries = [];

    public array $addActionEntries = [];

    public array $reviewActionEntries = [];

    public array $baseDuplicateEntries = [];

    public array $allActionEntries = [];

    public array $namespaceOptions = [];

    public array $groupOptions = [];

    public array $localeOptions = [];

    public string $activeActionFilter = 'action_files';

    public string $search = '';

    public string $namespaceFilter = 'all';

    public string $groupFilter = 'all';

    public string $localeFilter = 'all';

    public string $decisionFilter = 'all';

    public int $perPage = 25;

    public string $sortField = 'file';

    public string $sortDirection = 'asc';

    /**
     * Mount the translation lang ballast audit page.
     */
    public function mount(): void
    {
        $this->loadAuditFiles();
    }

    /**
     * Render the translation lang ballast audit page.
     */
    public function render(): View
    {
        return view('components.admin.⚡translation-lang-ballast', [
            'summary' => $this->summary,
            'generatedAt' => $this->generatedAt,
            'actionFileRows' => $this->paginateRows($this->filteredActionFileRows()),
            'actionRows' => $this->paginateRows($this->filteredActionEntries()->values()->all()),
            'activeActionFilter' => $this->activeActionFilter,
            'search' => $this->search,
            'namespaceFilter' => $this->namespaceFilter,
            'groupFilter' => $this->groupFilter,
            'localeFilter' => $this->localeFilter,
            'decisionFilter' => $this->decisionFilter,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'namespaceOptions' => $this->namespaceOptions,
            'groupOptions' => $this->groupOptions,
            'localeOptions' => $this->localeOptions,
            'activeLocaleSummary' => $this->activeLocaleSummary(),
        ]);
    }

    public function setActionFilter(string $actionFilter): void
    {
        if (! in_array($actionFilter, ['action_files', 'remove', 'add', 'review', 'base_duplicates'], true)) {
            return;
        }

        if ($this->activeActionFilter === $actionFilter) {
            return;
        }

        $this->activeActionFilter = $actionFilter;
        $this->normalizeSortFieldForActiveFilter();
        $this->resetPage();
    }

    public function setDecisionFilter(string $decisionFilter): void
    {
        if (! in_array($decisionFilter, ['all', 'open', 'reviewed', 'approved', 'ignored'], true)) {
            return;
        }

        if ($this->decisionFilter === $decisionFilter) {
            return;
        }

        $this->decisionFilter = $decisionFilter;
        $this->resetPage();
    }

    public function setDecisionStatus(string $candidateHash, string $decisionStatus): void
    {
        $candidateHash = trim($candidateHash);

        if ($candidateHash === '') {
            return;
        }

        if (! in_array($decisionStatus, ['open', 'reviewed', 'approved', 'ignored'], true)) {
            return;
        }

        $entry = collect($this->allActionEntries)
            ->first(fn(array $actionEntry): bool => (string) ($actionEntry['candidate_hash'] ?? '') === $candidateHash);

        if (! is_array($entry)) {
            return;
        }

        $decision = TranslationLangBallastDecision::query()->updateOrCreate(
            [
                'candidate_hash' => $candidateHash,
            ],
            [
                'locale' => trim((string) ($entry['locale'] ?? '')),
                'namespace' => trim((string) ($entry['namespace'] ?? '')),
                'group' => trim((string) ($entry['group'] ?? '')) ?: null,
                'key' => trim((string) ($entry['key'] ?? '')),
                'file' => trim((string) ($entry['file'] ?? '')),
                'file_key' => trim((string) ($entry['file_key'] ?? '')),
                'value_hash' => trim((string) ($entry['value_hash'] ?? '')),
                'translation_key_id' => (int) ($entry['translation_key_id'] ?? 0) > 0
                    ? (int) ($entry['translation_key_id'] ?? 0)
                    : null,
                'translation_value_id' => (int) ($entry['translation_value_id'] ?? 0) > 0
                    ? (int) ($entry['translation_value_id'] ?? 0)
                    : null,
                'action_candidate' => trim((string) ($entry['action_candidate'] ?? $entry['action'] ?? 'review')),
                'decision_status' => $decisionStatus,
                'reason_detail' => trim((string) ($entry['reason_detail'] ?? '')) ?: null,
                'lang_file_action_reason' => trim((string) ($entry['lang_file_action_reason'] ?? '')) ?: null,
                'reviewed_at' => now(),
                'reviewed_by_user_id' => Auth::id(),
            ],
        );

        $this->applyDecisionToLoadedEntries($candidateHash, $decision);
        $this->refreshDecisionSummaryFromLoadedEntries();
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->activeActionFilter = 'action_files';
        $this->search = '';
        $this->namespaceFilter = 'all';
        $this->groupFilter = 'all';
        $this->localeFilter = 'all';
        $this->decisionFilter = 'all';
        $this->sortField = 'file';
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedNamespaceFilter(): void
    {
        $this->resetPage();
    }

    public function updatedGroupFilter(): void
    {
        $this->resetPage();
    }

    public function updatedLocaleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDecisionFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $allowedPerPageValues = [10, 25, 50, 100];

        if (! in_array((int) $this->perPage, $allowedPerPageValues, true)) {
            $this->perPage = 25;
        }

        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, $this->allowedSortFields(), true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    /**
     * Load the latest lang ballast audit output files.
     */
    private function loadAuditFiles(): void
    {
        $summaryPayload = $this->readJsonFile(storage_path('audits/translations/lang-ballast/summary.json'));
        $previewPayload = $this->readJsonFile(storage_path('audits/translations/lang-ballast/preview.json'));
        $fullPayload = $this->readJsonFile(storage_path('audits/translations/lang-ballast/full.json'));
        $actionPayload = $fullPayload !== [] ? $fullPayload : $previewPayload;

        $this->summary = $summaryPayload !== []
            ? $summaryPayload
            : (array) data_get($previewPayload, 'summary', []);

        $this->generatedAt = trim((string) data_get($previewPayload, 'generated_at', '')) ?: null;

        $this->removeActionEntries = (array) data_get($actionPayload, 'actions.remove_from_lang', []);
        $this->addActionEntries = (array) data_get($actionPayload, 'actions.add_to_lang', []);
        $this->reviewActionEntries = (array) data_get($actionPayload, 'actions.review', []);
        $this->baseDuplicateEntries = collect((array) data_get($actionPayload, 'items.sub_language_base_duplicates', []))
            ->map(fn(array $entry): array => [
                ...$entry,
                'action' => 'base_duplicate',
                'action_candidate' => 'base_duplicate',
            ])
            ->values()
            ->all();

        $this->allActionEntries = collect([
            ...$this->removeActionEntries,
            ...$this->addActionEntries,
            ...$this->reviewActionEntries,
        ])
            ->values()
            ->all();

        $this->syncLoadedDecisionStates();

        $this->namespaceOptions = $this->optionValuesFromVisibleEntries('namespace');
        $this->groupOptions = $this->optionValuesFromVisibleEntries('group');
        $this->localeOptions = $this->optionValuesFromVisibleEntries('locale');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function filteredActionFileRows(): array
    {
        return $this->filteredActionEntries()
            ->groupBy(fn(array $entry): string => trim((string) ($entry['file'] ?? '')) ?: 'unknown')
            ->map(function (Collection $entries, string $file): array {
                return [
                    'file' => $file,
                    'entries' => $entries->count(),
                    'locales' => $entries
                        ->pluck('locale')
                        ->filter()
                        ->unique()
                        ->sort()
                        ->values()
                        ->all(),
                    'namespaces' => $entries
                        ->pluck('namespace')
                        ->filter()
                        ->unique()
                        ->sort()
                        ->values()
                        ->all(),
                    'groups' => $entries
                        ->pluck('group')
                        ->filter()
                        ->unique()
                        ->sort()
                        ->values()
                        ->all(),
                    'translation_key_ids' => $entries
                        ->pluck('translation_key_id')
                        ->filter()
                        ->unique()
                        ->sort()
                        ->values()
                        ->all(),
                    'translation_value_ids' => $entries
                        ->pluck('translation_value_id')
                        ->filter()
                        ->unique()
                        ->sort()
                        ->values()
                        ->all(),
                    'actions' => $entries
                        ->pluck('action')
                        ->filter()
                        ->countBy()
                        ->sortKeys()
                        ->all(),
                    'decision_statuses' => $entries
                        ->pluck('decision_status')
                        ->map(static fn(mixed $value): string => trim((string) $value) ?: 'open')
                        ->countBy()
                        ->sortKeys()
                        ->all(),
                    'reason_details' => $entries
                        ->pluck('reason_detail')
                        ->filter()
                        ->countBy()
                        ->sortKeys()
                        ->all(),
                    'keys_sample' => $entries
                        ->pluck('key')
                        ->filter()
                        ->unique()
                        ->take(20)
                        ->values()
                        ->all(),
                ];
            })
            ->pipe(fn(Collection $rows): Collection => $this->sortActionFileRows($rows))
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function filteredActionEntries(): Collection
    {
        $entries = match ($this->activeActionFilter) {
            'remove' => collect($this->removeActionEntries),
            'add' => collect($this->addActionEntries),
            'review' => collect($this->reviewActionEntries),
            'base_duplicates' => collect($this->baseDuplicateEntries),
            default => collect($this->allActionEntries),
        };

        if ($this->namespaceFilter !== 'all') {
            $entries = $entries->filter(
                fn(array $entry): bool => (string) ($entry['namespace'] ?? '') === $this->namespaceFilter,
            );
        }

        if ($this->groupFilter !== 'all') {
            $entries = $entries->filter(
                fn(array $entry): bool => (string) ($entry['group'] ?? '') === $this->groupFilter,
            );
        }

        if ($this->localeFilter !== 'all') {
            $entries = $entries->filter(
                fn(array $entry): bool => (string) ($entry['locale'] ?? '') === $this->localeFilter,
            );
        }

        if ($this->decisionFilter !== 'all') {
            $entries = $entries->filter(
                fn(array $entry): bool => (string) ($entry['decision_status'] ?? 'open') === $this->decisionFilter,
            );
        }

        $search = mb_strtolower(trim($this->search));

        if ($search !== '') {
            $entries = $entries->filter(function (array $entry) use ($search): bool {
                $haystack = mb_strtolower(implode(' ', array_filter([
                    $entry['file'] ?? null,
                    $entry['key'] ?? null,
                    $entry['suggested_key'] ?? null,
                    $entry['file_key'] ?? null,
                    $entry['namespace'] ?? null,
                    $entry['group'] ?? null,
                    $entry['locale'] ?? null,
                    $entry['value'] ?? null,
                    $entry['reason_detail'] ?? null,
                    $entry['lang_file_action_reason'] ?? null,
                    $entry['decision_status'] ?? null,
                    $entry['candidate_hash'] ?? null,
                ])));

                return str_contains($haystack, $search);
            });
        }

        return $this->sortActionEntries($entries)->values();
    }

    /**
     * @param Collection<int, array<string, mixed>> $rows
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function sortActionFileRows(Collection $rows): Collection
    {
        $sortField = $this->sortField;
        $descending = $this->sortDirection === 'desc';

        return $rows->sortBy(function (array $row) use ($sortField): mixed {
            return match ($sortField) {
                'entries' => (int) ($row['entries'] ?? 0),
                'locales' => $this->sortableListValue($row['locales'] ?? []),
                'namespaces' => $this->sortableListValue($row['namespaces'] ?? []),
                'groups' => $this->sortableListValue($row['groups'] ?? []),
                'decisions' => $this->sortableDecisionStatusValue((array) ($row['decision_statuses'] ?? [])),
                'reasons' => $this->sortableListValue(array_keys((array) ($row['reason_details'] ?? []))),
                default => mb_strtolower(trim((string) ($row['file'] ?? ''))),
            };
        }, SORT_NATURAL | SORT_FLAG_CASE, $descending);
    }

    /**
     * @param Collection<int, array<string, mixed>> $rows
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function sortActionEntries(Collection $rows): Collection
    {
        $sortField = $this->sortField;
        $descending = $this->sortDirection === 'desc';

        return $rows->sortBy(function (array $row) use ($sortField): mixed {
            return match ($sortField) {
                'id' => (int) ($row['translation_key_id'] ?? 0),
                'locale' => mb_strtolower(trim((string) ($row['locale'] ?? ''))),
                'namespace' => mb_strtolower(trim((string) ($row['namespace'] ?? ''))),
                'group' => mb_strtolower(trim((string) ($row['group'] ?? ''))),
                'source' => mb_strtolower(trim((string) ($row['key'] ?? $row['file_key'] ?? $row['file'] ?? ''))),
                'reason' => mb_strtolower(trim((string) ($row['reason_detail'] ?? $row['lang_file_action_reason'] ?? ''))),
                default => mb_strtolower(trim((string) ($row['locale'] ?? ''))),
            };
        }, SORT_NATURAL | SORT_FLAG_CASE, $descending);
    }

    /**
     * @return array<int, string>
     */
    private function allowedSortFields(): array
    {
        if ($this->activeActionFilter === 'action_files') {
            return ['file', 'entries', 'locales', 'namespaces', 'groups', 'decisions', 'reasons'];
        }

        return ['id', 'locale', 'namespace', 'group', 'source', 'reason'];
    }

    private function normalizeSortFieldForActiveFilter(): void
    {
        if (in_array($this->sortField, $this->allowedSortFields(), true)) {
            return;
        }

        $this->sortField = $this->activeActionFilter === 'action_files'
            ? 'file'
            : 'locale';
        $this->sortDirection = 'asc';
    }

    /**
     * @param array<int, mixed>|mixed $value
     */
    private function sortableListValue(mixed $value): string
    {
        return collect((array) $value)
            ->map(static fn(mixed $item): string => mb_strtolower(trim((string) $item)))
            ->filter()
            ->sort()
            ->implode('|');
    }

    /**
     * @param array<string, mixed> $decisionStatuses
     */
    private function sortableDecisionStatusValue(array $decisionStatuses): string
    {
        return collect(['open', 'reviewed', 'approved', 'ignored'])
            ->map(static fn(string $status): string => str_pad(
                (string) (int) ($decisionStatuses[$status] ?? 0),
                8,
                '0',
                STR_PAD_LEFT,
            ))
            ->implode('|');
    }

    /**
     * @param array<int, array<string, mixed>> $entries
     *
     * @return array<int, array<string, mixed>>
     */
    private function applyDecisionIndexToEntries(array $entries, Collection $decisionIndex): array
    {
        return collect($entries)
            ->map(function (array $entry) use ($decisionIndex): array {
                $candidateHash = trim((string) ($entry['candidate_hash'] ?? ''));
                $decision = $candidateHash !== '' ? $decisionIndex->get($candidateHash) : null;

                if (! $decision instanceof TranslationLangBallastDecision) {
                    return [
                        ...$entry,
                        'decision_exists' => false,
                        'decision_id' => null,
                        'decision_status' => 'open',
                        'decision_note' => null,
                        'decision_reviewed_at' => null,
                        'decision_reviewed_by_user_id' => null,
                    ];
                }

                return $this->entryWithDecision($entry, $decision);
            })
            ->values()
            ->all();
    }

    private function applyDecisionToLoadedEntries(string $candidateHash, TranslationLangBallastDecision $decision): void
    {
        $this->removeActionEntries = $this->replaceDecisionInEntries($this->removeActionEntries, $candidateHash, $decision);
        $this->addActionEntries = $this->replaceDecisionInEntries($this->addActionEntries, $candidateHash, $decision);
        $this->reviewActionEntries = $this->replaceDecisionInEntries($this->reviewActionEntries, $candidateHash, $decision);

        $this->allActionEntries = collect([
            ...$this->removeActionEntries,
            ...$this->addActionEntries,
            ...$this->reviewActionEntries,
        ])
            ->values()
            ->all();
    }

    /**
     * @param array<int, array<string, mixed>> $entries
     *
     * @return array<int, array<string, mixed>>
     */
    private function replaceDecisionInEntries(array $entries, string $candidateHash, TranslationLangBallastDecision $decision): array
    {
        return collect($entries)
            ->map(function (array $entry) use ($candidateHash, $decision): array {
                if ((string) ($entry['candidate_hash'] ?? '') !== $candidateHash) {
                    return $entry;
                }

                return $this->entryWithDecision($entry, $decision);
            })
            ->values()
            ->all();
    }

    /**
     * @param array<string, mixed> $entry
     *
     * @return array<string, mixed>
     */
    private function entryWithDecision(array $entry, TranslationLangBallastDecision $decision): array
    {
        return [
            ...$entry,
            'decision_exists' => true,
            'decision_id' => $decision->id,
            'decision_status' => $decision->decision_status,
            'decision_note' => $decision->decision_note,
            'decision_reviewed_at' => $decision->reviewed_at?->toISOString(),
            'decision_reviewed_by_user_id' => $decision->reviewed_by_user_id,
        ];
    }

    private function syncLoadedDecisionStates(): void
    {
        $candidateHashes = collect($this->allActionEntries)
            ->pluck('candidate_hash')
            ->map(static fn(mixed $value): string => trim((string) $value))
            ->filter()
            ->unique()
            ->values();

        if ($candidateHashes->isEmpty()) {
            $this->refreshDecisionSummaryFromLoadedEntries();

            return;
        }

        $decisionIndex = TranslationLangBallastDecision::query()
            ->whereIn('candidate_hash', $candidateHashes->all())
            ->get()
            ->keyBy('candidate_hash');

        $this->removeActionEntries = $this->applyDecisionIndexToEntries($this->removeActionEntries, $decisionIndex);
        $this->addActionEntries = $this->applyDecisionIndexToEntries($this->addActionEntries, $decisionIndex);
        $this->reviewActionEntries = $this->applyDecisionIndexToEntries($this->reviewActionEntries, $decisionIndex);

        $this->allActionEntries = collect([
            ...$this->removeActionEntries,
            ...$this->addActionEntries,
            ...$this->reviewActionEntries,
        ])
            ->values()
            ->all();

        $this->refreshDecisionSummaryFromLoadedEntries();
    }

    private function refreshDecisionSummaryFromLoadedEntries(): void
    {
        $entries = collect($this->allActionEntries);
        $decisionSummary = [
            'total_entries' => $entries->count(),
            'with_decision_entries' => $entries
                ->where('decision_exists', true)
                ->count(),
            'without_decision_entries' => $entries
                ->where('decision_exists', false)
                ->count(),
            'open_entries' => $entries
                ->where('decision_status', 'open')
                ->count(),
            'reviewed_entries' => $entries
                ->where('decision_status', 'reviewed')
                ->count(),
            'approved_entries' => $entries
                ->where('decision_status', 'approved')
                ->count(),
            'ignored_entries' => $entries
                ->where('decision_status', 'ignored')
                ->count(),
        ];

        data_set($this->summary, 'decisions', $decisionSummary);
        data_set($this->summary, 'decision_open_entries', $decisionSummary['open_entries']);
        data_set($this->summary, 'decision_reviewed_entries', $decisionSummary['reviewed_entries']);
        data_set($this->summary, 'decision_approved_entries', $decisionSummary['approved_entries']);
        data_set($this->summary, 'decision_ignored_entries', $decisionSummary['ignored_entries']);
        data_set($this->summary, 'decision_with_existing_decision_entries', $decisionSummary['with_decision_entries']);
        data_set($this->summary, 'decision_without_existing_decision_entries', $decisionSummary['without_decision_entries']);
    }

    /**
     * @return array<int, string>
     */
    private function optionValuesFromVisibleEntries(string $field): array
    {
        return collect([
            ...$this->allActionEntries,
            ...$this->baseDuplicateEntries,
        ])
            ->pluck($field)
            ->map(static fn(mixed $value): string => trim((string) $value))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private function paginateRows(array $rows): LengthAwarePaginator
    {
        $items = collect($rows);
        $page = $this->getPage();
        $perPage = max(1, (int) $this->perPage);

        return new LengthAwarePaginator(
            items: $items->forPage($page, $perPage)->values(),
            total: $items->count(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => request()->url(),
                'pageName' => 'page',
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function activeLocaleSummary(): array
    {
        $activeLocales = TranslationLanguage::query()
            ->where('is_enabled_for_translation', true)
            ->pluck('locale')
            ->map(fn(string $locale): string => $this->normalizeLocale($locale))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $auditLocales = collect((array) data_get($this->summary, 'by_locale', []))
            ->keys()
            ->map(fn(string $locale): string => $this->normalizeLocale($locale))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $withTranslations = $activeLocales
            ->filter(fn(string $locale): bool => $auditLocales->contains($locale))
            ->values();

        $withoutTranslations = $activeLocales
            ->reject(fn(string $locale): bool => $auditLocales->contains($locale))
            ->values();

        return [
            'total' => $activeLocales->count(),
            'with_translations' => $withTranslations->count(),
            'without_translations' => $withoutTranslations->count(),
            'without_translation_locales' => $withoutTranslations->all(),
        ];
    }

    private function normalizeLocale(string $locale): string
    {
        return strtolower(str_replace('_', '-', trim($locale)));
    }

    /**
     * @return array<string, mixed>
     */
    private function readJsonFile(string $path): array
    {
        if (! File::exists($path)) {
            return [];
        }

        $payload = json_decode((string) File::get($path), true);

        return is_array($payload) ? $payload : [];
    }
}
