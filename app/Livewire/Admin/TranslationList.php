<?php

// app/Livewire/Admin/TranslationList.php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Models\TranslationKey;
use App\Models\TranslationLanguage;
use App\Models\TranslationValue;
use App\Settings\AppGeneralSettings;
use App\Support\Locale\LocaleCode;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

/**
 * Translation administration list with filtering, pagination and modal workflows.
 */
class TranslationList extends Component
{
    use InteractsWithUserSettings;
    use WithoutUrlPagination;
    use WithPagination;

    private const UI_STATE_SETTING_KEY = 'ui.pages.admin_translation_list';

    private const STALE_AUDIT_USAGE_REASON = 'stale_audit_usage_not_seen_in_latest_sync';

    /**
     * @var array<int, string>
     */
    private const PERSISTED_STATE_PROPERTIES = [
        'search',
        'status',
        'workflowStatus',
        'classification',
        'showArchived',
        'onlyProblems',
        'onlyBaseDuplicates',
        'languageFilter',
        'fileFilter',
        'perPage',
    ];

    private const PROBLEM_STATUSES = [
        'missing',
        'dynamic',
    ];

    public string $search = '';

    public string $status = 'all';

    public string $workflowStatus = 'open';

    public string $classification = 'all';

    public bool $showArchived = false;

    public bool $onlyProblems = false;

    public bool $onlyBaseDuplicates = false;

    public string $languageFilter = '';

    public string $fileFilter = '';

    public int $perPage = 25;

    public ?int $selectedTranslationKeyId = null;

    public bool $translationKeyModalOpen = false;

    public ?int $editingTranslationKeyId = null;

    public bool $translationEditModalOpen = false;

    public array $translationEditValues = [];

    public ?int $focusedTranslationKeyId = null;

    public ?int $selectedHistoryTranslationKeyId = null;

    public bool $translationHistoryModalOpen = false;

    public string $sortField = 'updated_at';

    public string $sortDirection = 'desc';

    /**
     * @var array<int, string>
     */
    public array $statusOptions = [
        'all',
        'ok',
        'missing',
        'native',
        'dynamic',
        'obsolete',
        'invalid',
    ];

    /**
     * @var array<int, string>
     */
    public array $workflowStatusOptions = [
        'all',
        'open',
        'reviewed',
    ];

    /**
     * @var array<int, string>
     */
    public array $classificationOptions = [
        'all',
        'key',
        'vendor',
        'backfill_by_translation',
        'native',
        'dynamic',
    ];

    /**
     * Reset pagination when filter-like properties are updated.
     */
    public function updating(string $property): void
    {
        if (in_array($property, self::PERSISTED_STATE_PROPERTIES, true)) {
            $this->resetPage();
        }
    }

    /**
     * Restore persisted UI state for this page.
     */
    public function mount(): void
    {
        $state = $this->userSetting(self::UI_STATE_SETTING_KEY, []);

        if (! is_array($state)) {
            return;
        }

        $this->search = trim((string) ($state['search'] ?? $this->search));
        $this->status = $this->normalizeStatusFilter($state['status'] ?? $this->status);
        $this->workflowStatus = $this->normalizeWorkflowStatusFilter($state['workflowStatus'] ?? $this->workflowStatus);
        $this->classification = $this->normalizeClassificationFilter($state['classification'] ?? $this->classification);
        $this->showArchived = (bool) ($state['showArchived'] ?? $this->showArchived);
        $this->onlyProblems = (bool) ($state['onlyProblems'] ?? $this->onlyProblems);
        $this->onlyBaseDuplicates = (bool) ($state['onlyBaseDuplicates'] ?? $this->onlyBaseDuplicates);
        $this->languageFilter = trim((string) ($state['languageFilter'] ?? $this->languageFilter));
        $this->fileFilter = trim((string) ($state['fileFilter'] ?? $this->fileFilter));
        $this->perPage = $this->normalizedPerPage($state['perPage'] ?? $this->perPage);

        $this->setPage(1);
    }

    /**
     * Persist filter state after UI updates.
     */
    public function updated(string $property): void
    {
        if (! in_array($property, self::PERSISTED_STATE_PROPERTIES, true)) {
            return;
        }

        $this->persistUiState();
    }

    /**
     * Normalize selectable page size when changed from UI.
     */
    public function updatedPerPage(): void
    {
        $this->perPage = $this->normalizedPerPage($this->perPage);
        $this->persistUiState();
    }

    /**
     * Set status filter and reset pagination.
     */
    public function setStatus(string $status): void
    {
        if (! in_array($status, $this->statusOptions, true)) {
            $status = 'all';
        }

        $this->status = $status;
        $this->resetPage();
        $this->persistUiState();
    }

    /**
     * Set workflow status filter and reset pagination.
     */
    public function setWorkflowStatus(string $workflowStatus): void
    {
        if (! in_array($workflowStatus, $this->workflowStatusOptions, true)) {
            $workflowStatus = 'open';
        }

        $this->showArchived = false;
        $this->workflowStatus = $workflowStatus;
        $this->resetPage();
        $this->persistUiState();
    }

    /**
     * Show all current code-relevant translation entries.
     */
    public function showAllRelevantTranslations(): void
    {
        $this->showArchived = false;
        $this->workflowStatus = 'all';
        $this->status = 'all';
        $this->onlyProblems = false;
        $this->onlyBaseDuplicates = false;
        $this->resetPage();
        $this->persistUiState();
    }

    /**
     * Show completed current code-relevant translations.
     */
    public function showCompletedTranslations(): void
    {
        $this->showArchived = false;
        $this->workflowStatus = 'all';
        $this->status = 'ok';
        $this->onlyProblems = false;
        $this->onlyBaseDuplicates = false;
        $this->resetPage();
        $this->persistUiState();
    }

    /**
     * Show reviewed translation history entries.
     */
    public function showHistoryTranslations(): void
    {
        $this->showArchived = true;
        $this->workflowStatus = 'reviewed';
        $this->status = 'all';
        $this->onlyProblems = false;
        $this->onlyBaseDuplicates = false;
        $this->resetPage();
        $this->persistUiState();
    }

    /**
     * Set classification filter and reset pagination.
     */
    public function setClassification(string $classification): void
    {
        if (! in_array($classification, $this->classificationOptions, true)) {
            $classification = 'all';
        }

        $this->classification = $classification;
        $this->resetPage();
        $this->persistUiState();
    }

    /**
     * Focus the list on active translation work for the selected type.
     */
    public function setActiveClassification(string $classification): void
    {
        if (! in_array($classification, $this->classificationOptions, true)) {
            $classification = 'all';
        }

        $this->showArchived = false;
        $this->workflowStatus = 'all';
        $this->classification = $classification;
        $this->resetPage();
        $this->persistUiState();
    }

    /**
     * Focus the list on reviewed/archive translation entries.
     */
    public function showArchivedTranslations(): void
    {
        $this->showArchived = true;
        $this->workflowStatus = 'all';
        $this->status = 'all';
        $this->classification = 'all';
        $this->onlyProblems = false;
        $this->onlyBaseDuplicates = false;
        $this->resetPage();
        $this->persistUiState();
    }

    /**
     * Toggle problem-only filter and reset pagination.
     */
    public function toggleOnlyProblems(): void
    {
        $this->onlyProblems = ! $this->onlyProblems;
        $this->resetPage();
        $this->persistUiState();
    }

    /**
     * Toggle duplicate-only filter and reset pagination.
     */
    public function toggleOnlyBaseDuplicates(): void
    {
        $this->onlyBaseDuplicates = ! $this->onlyBaseDuplicates;
        $this->resetPage();
        $this->persistUiState();
    }

    /**
     * Restore default filter and pagination state.
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = 'all';
        $this->workflowStatus = 'open';
        $this->classification = 'all';
        $this->showArchived = false;
        $this->onlyProblems = false;
        $this->onlyBaseDuplicates = false;
        $this->languageFilter = '';
        $this->fileFilter = '';
        $this->perPage = 25;

        $this->resetPage();
        $this->persistUiState();
    }

    /**
     * Open detail modal for a translation key.
     */
    public function openTranslationKey(int $translationKeyId): void
    {
        $this->focusedTranslationKeyId = $translationKeyId;
        $this->selectedTranslationKeyId = $translationKeyId;
        $this->translationKeyModalOpen = true;
    }

    public function closeTranslationKey(): void
    {
        $this->translationKeyModalOpen = false;
        $this->selectedTranslationKeyId = null;
    }

    /**
     * Open the next translation key from the current filtered/sorted list context.
     */
    public function openNextTranslationKeyFromList(): void
    {
        if (! $this->selectedTranslationKeyId) {
            return;
        }

        $query = $this->translationKeyQuery();

        $currentPage = $this->getPage();
        $perPage = $this->normalizedPerPage();

        $paginator = (clone $query)->paginate($perPage, ['*'], 'page', $currentPage);

        $currentPageIds = $paginator->getCollection()->pluck('id')->values();
        $currentIndex = $currentPageIds->search($this->selectedTranslationKeyId);

        if ($currentIndex === false) {
            $fallbackId = $currentPageIds->first();

            if (is_numeric($fallbackId)) {
                $this->openTranslationKey((int) $fallbackId);
            }

            return;
        }

        $nextOnPageId = $currentPageIds->get((int) $currentIndex + 1);

        if (is_numeric($nextOnPageId)) {
            $this->openTranslationKey((int) $nextOnPageId);

            return;
        }

        if (! $paginator->hasMorePages()) {
            return;
        }

        $nextPage = $paginator->currentPage() + 1;

        $nextPagePaginator = (clone $query)->paginate($perPage, ['*'], 'page', $nextPage);
        $nextPageFirstId = $nextPagePaginator->getCollection()->pluck('id')->first();

        if (! is_numeric($nextPageFirstId)) {
            return;
        }

        $this->setPage($nextPage);
        $this->openTranslationKey((int) $nextPageFirstId);
    }

    /**
     * Open the next editable translation key from the current filtered/sorted list context.
     */
    public function openNextTranslationEditFromList(): void
    {
        if (! $this->editingTranslationKeyId) {
            return;
        }

        $query = $this->editableTranslationKeyQuery();

        $currentPage = $this->getPage();
        $perPage = $this->normalizedPerPage();

        $paginator = (clone $query)->paginate($perPage, ['*'], 'page', $currentPage);

        $currentPageIds = $paginator->getCollection()->pluck('id')->values();
        $currentIndex = $currentPageIds->search($this->editingTranslationKeyId);

        if ($currentIndex === false) {
            $fallbackId = $currentPageIds->first();

            if (is_numeric($fallbackId)) {
                $this->openTranslationEdit((int) $fallbackId);
            }

            return;
        }

        $nextOnPageId = $currentPageIds->get((int) $currentIndex + 1);

        if (is_numeric($nextOnPageId)) {
            $this->openTranslationEdit((int) $nextOnPageId);

            return;
        }

        if (! $paginator->hasMorePages()) {
            return;
        }

        $nextPage = $paginator->currentPage() + 1;

        $nextPagePaginator = (clone $query)->paginate($perPage, ['*'], 'page', $nextPage);
        $nextPageFirstId = $nextPagePaginator->getCollection()->pluck('id')->first();

        if (! is_numeric($nextPageFirstId)) {
            return;
        }

        $this->setPage($nextPage);
        $this->openTranslationEdit((int) $nextPageFirstId);
    }

    /**
     * Open translation edit modal and preload editable values.
     */
    public function openTranslationEdit(int $translationKeyId): void
    {
        $translationKey = TranslationKey::query()
            ->with([
                'values' => fn($query) => $query->orderBy('locale', 'asc'),
            ])
            ->find($translationKeyId);

        if (! $translationKey || trim((string) ($translationKey->key ?? '')) === '') {
            return;
        }

        $translationLanguages = TranslationLanguage::query()
            ->where('is_enabled_for_translation', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('locale', 'asc')
            ->get(['locale']);

        $this->translationEditValues = $translationLanguages
            ->mapWithKeys(function (TranslationLanguage $translationLanguage) use ($translationKey): array {
                $translationValue = $translationKey->values->firstWhere('locale', $translationLanguage->locale);

                return [
                    $translationLanguage->locale => (string) ($translationValue?->value ?? ''),
                ];
            })
            ->all();

        $this->focusedTranslationKeyId = $translationKey->id;
        $this->editingTranslationKeyId = $translationKey->id;
        $this->translationEditModalOpen = true;
    }

    /**
     * Close translation edit modal and clear edit state.
     */
    public function closeTranslationEdit(): void
    {
        $this->translationEditModalOpen = false;
        $this->editingTranslationKeyId = null;
        $this->translationEditValues = [];
    }

    /**
     * Copy the current native text into the editable English value and focus the field.
     */
    public function copyNativeTextToEnglishValue(): void
    {
        if (! $this->editingTranslationKeyId) {
            return;
        }

        $translationKey = TranslationKey::query()->find($this->editingTranslationKeyId);

        if (! $translationKey) {
            return;
        }

        $nativeText = trim((string) ($translationKey->native_text ?? ''));

        if ($nativeText === '') {
            return;
        }

        $this->translationEditValues['en'] = $nativeText;

        $this->dispatch('buergerfrs:focus-field-and-select', inputId: 'translation-edit-value-en');
    }

    /**
     * Open history modal for a translation key if audit data exists.
     */
    public function openTranslationHistory(int $translationKeyId): void
    {
        $hasHistoryEvents = DB::table('translation_audit_events')
            ->where('translation_key_id', $translationKeyId)
            ->exists();

        if (! $hasHistoryEvents) {
            return;
        }

        $this->focusedTranslationKeyId = $translationKeyId;
        $this->selectedHistoryTranslationKeyId = $translationKeyId;
        $this->translationHistoryModalOpen = true;
    }

    public function closeTranslationHistory(): void
    {
        $this->translationHistoryModalOpen = false;
        $this->selectedHistoryTranslationKeyId = null;
    }

    /**
     * Close review modal and directly open edit modal for the same key.
     */
    public function openTranslationEditFromReview(int $translationKeyId): void
    {
        $this->translationKeyModalOpen = false;
        $this->selectedTranslationKeyId = null;

        $this->openTranslationEdit($translationKeyId);
    }

    /**
     * Apply the suggested key as the active translation key from the review modal.
     */
    public function applySuggestedKey(int $translationKeyId): void
    {
        $translationKey = TranslationKey::query()->find($translationKeyId);

        if (! $translationKey) {
            return;
        }

        $suggestedKey = trim((string) ($translationKey->suggested_key ?? ''));
        $currentKey = trim((string) ($translationKey->key ?? ''));

        if ($suggestedKey === '') {
            Flux::toast(
                heading: __('No suggested key available'),
                text: __('There is no suggested key to apply for this entry.'),
                variant: 'warning',
                duration: 5000,
            );

            return;
        }

        if ($currentKey === $suggestedKey) {
            Flux::toast(
                heading: __('Suggested key already applied'),
                text: __('The translation key already matches the suggested key.'),
                variant: 'info',
                duration: 4000,
            );

            return;
        }

        $currentStatus = trim((string) ($translationKey->status ?? ''));
        $resolvedStatus = in_array($currentStatus, ['ok', 'missing', 'obsolete'], true)
            ? $currentStatus
            : 'missing';

        $translationKey->forceFill([
            'key' => $suggestedKey,
            'namespace' => $this->namespaceFromTranslationKey($suggestedKey),
            'group' => $this->groupFromTranslationKey($suggestedKey),
            'classification' => 'key',
            'status' => $resolvedStatus,
        ])->save();

        $this->createTranslationKeyAuditEvent(
            translationKey: $translationKey,
            oldKey: $currentKey !== '' ? $currentKey : null,
            newKey: $suggestedKey,
            reason: 'suggested_key_applied_from_review_modal',
            context: [
                'source' => 'translation_review_modal',
                'suggested_key' => $suggestedKey,
            ],
        );

        $this->focusedTranslationKeyId = $translationKey->id;

        Flux::toast(
            heading: __('Suggested key applied'),
            text: __('The suggested key has been copied to the translation key.'),
            variant: 'success',
            duration: 5000,
        );
    }

    /**
     * Mark an obsolete key as reviewed so it is hidden from the default open workflow.
     */
    public function markObsoleteAsReviewed(int $translationKeyId): void
    {
        $translationKey = TranslationKey::query()->find($translationKeyId);

        if (! $translationKey) {
            return;
        }

        if (($translationKey->status ?? '') !== 'obsolete') {
            Flux::toast(
                heading: __('Review not applicable'),
                text: __('Only obsolete entries can be marked as reviewed.'),
                variant: 'warning',
                duration: 5000,
            );

            return;
        }

        if (($translationKey->workflow_status ?? 'open') === 'reviewed') {
            Flux::toast(
                heading: __('Already reviewed'),
                text: __('This obsolete entry is already marked as reviewed.'),
                variant: 'info',
                duration: 4000,
            );

            return;
        }

        $oldWorkflowStatus = (string) ($translationKey->workflow_status ?? 'open');

        $translationKey->forceFill([
            'workflow_status' => 'reviewed',
            'reviewed_at' => now(),
            'reviewed_by_user_id' => Auth::id(),
            'review_note' => 'marked_reviewed_from_obsolete_tooltip',
        ])->save();

        $this->createTranslationWorkflowAuditEvent(
            translationKey: $translationKey,
            oldWorkflowStatus: $oldWorkflowStatus,
            newWorkflowStatus: 'reviewed',
            reason: 'obsolete_marked_reviewed_from_tooltip',
            context: [
                'source' => 'tooltip_action',
            ],
        );

        $this->focusedTranslationKeyId = $translationKey->id;

        Flux::toast(
            heading: __('Obsolete entry reviewed'),
            text: __('The obsolete entry has been marked as reviewed and leaves the default workflow list.'),
            variant: 'success',
            duration: 5000,
        );
    }

    /**
     * Persist editable translation values and record audit events for changes.
     */
    public function saveTranslationEdit(): void
    {
        if (! $this->editingTranslationKeyId) {
            return;
        }

        $translationKey = TranslationKey::query()->find($this->editingTranslationKeyId);

        if (! $translationKey || trim((string) ($translationKey->key ?? '')) === '') {
            $this->closeTranslationEdit();

            return;
        }

        $translationLanguages = TranslationLanguage::query()
            ->where('is_enabled_for_translation', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('locale', 'asc')
            ->get(['locale']);

        $this->validate(
            $translationLanguages
                ->mapWithKeys(fn(TranslationLanguage $translationLanguage): array => [
                    'translationEditValues.' . $translationLanguage->locale => [
                        'nullable',
                        'string',
                        'max:10000',
                    ],
                ])
                ->all()
        );

        foreach ($translationLanguages as $translationLanguage) {
            $locale = $translationLanguage->locale;
            $value = (string) ($this->translationEditValues[$locale] ?? '');

            $existingTranslationValue = TranslationValue::query()
                ->where('translation_key_id', $translationKey->id)
                ->where('locale', $locale)
                ->first();

            if (! $existingTranslationValue && trim($value) === '') {
                continue;
            }

            $oldValue = $existingTranslationValue?->value;
            $oldStatus = $existingTranslationValue?->status;
            $newStatus = trim($value) === '' ? 'missing' : 'ok';

            $translationValue = TranslationValue::query()->updateOrCreate(
                [
                    'translation_key_id' => $translationKey->id,
                    'locale' => $locale,
                ],
                [
                    'value' => $value,
                    'status' => $newStatus,
                    'source' => 'manual',
                    'reviewed_at' => now(),
                    'reviewed_by_user_id' => Auth::id(),
                ],
            );

            if ($oldValue !== $value || $oldStatus !== $newStatus) {
                $this->createTranslationValueAuditEvent(
                    translationKey: $translationKey,
                    locale: $locale,
                    oldValue: $oldValue,
                    newValue: $value,
                    oldStatus: $oldStatus,
                    newStatus: $newStatus,
                    wasCreated: ! $existingTranslationValue,
                );
            }
        }

        $this->focusedTranslationKeyId = $translationKey->id;

        Flux::toast(
            heading: __('Translation values saved'),
            text: __('The translation values have been saved successfully.'),
            variant: 'success',
            duration: 5000,
        );
    }

    /**
     * Determine whether non-default filters are currently active.
     */
    public function hasActiveFilters(): bool
    {
        return trim($this->search) !== ''
            || $this->status !== 'all'
            || $this->workflowStatus !== 'open'
            || $this->classification !== 'all'
            || $this->showArchived
            || $this->onlyProblems
            || $this->onlyBaseDuplicates
            || $this->languageFilter !== ''
            || $this->fileFilter !== ''
            || $this->perPage !== 25;
    }

    /**
     * Build the base translation key query with all active filters applied.
     */
    private function translationKeyQuery(): Builder
    {
        return $this->filteredTranslationKeyQuery()
            ->with([
                'values' => fn($query) => $query->orderBy('locale'),
            ])
            ->withCount('usages')
            ->selectSub(
                DB::table('translation_audit_events')
                    ->selectRaw('count(*)')
                    ->whereColumn('translation_audit_events.translation_key_id', 'translation_keys.id'),
                'history_events_count'
            )
            ->latest('updated_at');
    }

    /**
     * Build a translation key query with all active filters.
     *
     * @param  array<int, string>  $exceptFilters
     */
    private function filteredTranslationKeyQuery(array $exceptFilters = []): Builder
    {
        $query = in_array('relevanceScope', $exceptFilters, true)
            ? TranslationKey::query()
            : $this->relevanceScopedTranslationKeyQuery();

        if (! in_array('workflowStatus', $exceptFilters, true) && $this->workflowStatus !== 'all') {
            $query = $query->where('workflow_status', $this->workflowStatus);
        }

        return $query
            ->when(
                ! in_array('status', $exceptFilters, true) && $this->status !== 'all',
                fn(Builder $query): Builder => $query->where('status', $this->status),
            )
            ->when(
                ! in_array('classification', $exceptFilters, true) && $this->classification !== 'all',
                fn(Builder $query): Builder => $query->where('classification', $this->classification),
            )
            ->when(
                ! in_array('onlyProblems', $exceptFilters, true) && $this->onlyProblems,
                fn(Builder $query): Builder => $query->whereIn('status', self::PROBLEM_STATUSES),
            )
            ->when(
                ! in_array('onlyBaseDuplicates', $exceptFilters, true) && $this->onlyBaseDuplicates,
                function (Builder $query): Builder {
                    return $query->whereHas('values', function (Builder $query): void {
                        $query->where('is_base_duplicate', true);
                    });
                },
            )
            ->when(
                ! in_array('fileFilter', $exceptFilters, true) && $this->fileFilter !== '',
                fn(Builder $query): Builder => $query->where('group', $this->fileFilter),
            )
            ->when(
                ! in_array('search', $exceptFilters, true) && $this->search !== '',
                function (Builder $query): Builder {
                    $search = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $this->search) . '%';

                    return $query->where(function (Builder $query) use ($search): void {
                        $query
                            ->where('key', 'ilike', $search)
                            ->orWhere('suggested_key', 'ilike', $search)
                            ->orWhere('native_text', 'ilike', $search)
                            ->orWhereHas('values', function (Builder $query) use ($search): void {
                                $query->where('value', 'ilike', $search);
                            })
                            ->orWhereHas('usages', function (Builder $query) use ($search): void {
                                $query
                                    ->where('file', 'ilike', $search)
                                    ->orWhere('raw', 'ilike', $search);
                            });
                    });
                },
            );
    }

    /**
     * Build translation query scoped to entries that can be opened in edit mode.
     */
    private function editableTranslationKeyQuery(): Builder
    {
        return $this->translationKeyQuery()
            ->whereNotNull('key', 'and')
            ->where('key', '!=', '');
    }

    /**
     * Apply workflow-level list scoping before regular filters are evaluated.
     */
    private function relevanceScopedTranslationKeyQuery(): Builder
    {
        $query = TranslationKey::query();

        return $this->showArchived
            ? $this->applyArchivedRelevanceScope($query)
            : $this->applyActiveRelevanceScope($query);
    }

    private function queryForRelevanceScope(bool $archived, array $exceptFilters = []): Builder
    {
        $query = $this->filteredTranslationKeyQuery(array_merge($exceptFilters, ['relevanceScope']));

        return $archived
            ? $this->applyArchivedRelevanceScope($query)
            : $this->applyActiveRelevanceScope($query);
    }

    private function applyActiveRelevanceScope(Builder $query): Builder
    {
        return $query->whereHas('usages', function (Builder $query): void {
            $query->where(function (Builder $query): void {
                $query
                    ->whereNull('reason')
                    ->orWhere('reason', '!=', self::STALE_AUDIT_USAGE_REASON);
            });
        });
    }

    private function applyArchivedRelevanceScope(Builder $query): Builder
    {
        return $query->whereDoesntHave('usages', function (Builder $query): void {
            $query->where(function (Builder $query): void {
                $query
                    ->whereNull('reason')
                    ->orWhere('reason', '!=', self::STALE_AUDIT_USAGE_REASON);
            });
        });
    }

    /**
     * Build data for list, counters and modals.
     */
    public function render(): View
    {
        $workflowStatusCounts = $this->filteredTranslationKeyQuery(['workflowStatus'])
            ->selectRaw('workflow_status, count(*) as total')
            ->groupBy('workflow_status')
            ->pluck('total', 'workflow_status')
            ->map(fn($value) => (int) $value)
            ->all();

        $workflowCounterBaseQuery = $this->queryForRelevanceScope(false, ['workflowStatus', 'status', 'classification', 'onlyProblems', 'onlyBaseDuplicates']);

        $workflowRelevantTotal = (clone $workflowCounterBaseQuery)->count();

        $workflowOpenTotal = (clone $workflowCounterBaseQuery)
            ->where('workflow_status', 'open')
            ->count();

        $workflowReviewedTotal = (clone $workflowCounterBaseQuery)
            ->where('workflow_status', 'reviewed')
            ->count();

        $workflowHistoryTotal = (int) $this->filteredTranslationKeyQuery(['workflowStatus', 'status', 'classification', 'onlyProblems', 'onlyBaseDuplicates', 'relevanceScope'])
            ->where('workflow_status', 'reviewed')
            ->count();

        $workflowCompletedTotal = (clone $workflowCounterBaseQuery)
            ->where('status', 'ok')
            ->count();

        $statusCounts = $this->filteredTranslationKeyQuery(['status'])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn($value) => (int) $value)
            ->all();

        $classificationCounts = $this->filteredTranslationKeyQuery(['classification'])
            ->selectRaw('classification, count(*) as total')
            ->groupBy('classification')
            ->pluck('total', 'classification')
            ->mapWithKeys(fn($value, $key): array => [(string) $key => (int) $value])
            ->all();

        $activeClassificationCounts = $this->queryForRelevanceScope(false, ['workflowStatus', 'classification', 'status', 'onlyProblems', 'onlyBaseDuplicates'])
            ->selectRaw('classification, count(*) as total')
            ->groupBy('classification')
            ->pluck('total', 'classification')
            ->mapWithKeys(fn($value, $key): array => [(string) $key => (int) $value])
            ->all();

        $total = array_sum($statusCounts);

        $activeTypeTotal = array_sum($activeClassificationCounts);

        $archiveCount = (int) $this->queryForRelevanceScope(true, ['workflowStatus', 'classification', 'status', 'onlyProblems', 'onlyBaseDuplicates'])
            ->count();

        $problemCount = (int) $this->filteredTranslationKeyQuery(['onlyProblems'])
            ->whereIn('status', self::PROBLEM_STATUSES)
            ->count();

        $duplicateCount = (int) $this->filteredTranslationKeyQuery(['onlyBaseDuplicates'])
            ->whereHas('values', function (Builder $query): void {
                $query->where('is_base_duplicate', true);
            })
            ->count();

        $query = $this->translationKeyQuery();

        $filteredTotal = (clone $query)->count();

        $translationKeys = (clone $query)->paginate($this->normalizedPerPage());

        $nextReviewTranslationKeyId = $this->resolveNextReviewTranslationKeyId(
            query: $query,
            paginator: $translationKeys,
            selectedTranslationKeyId: $this->selectedTranslationKeyId,
        );

        $editQuery = $this->editableTranslationKeyQuery();
        $editPaginator = (clone $editQuery)->paginate(
            $this->normalizedPerPage(),
            ['*'],
            'page',
            $this->getPage(),
        );

        $nextEditTranslationKeyId = $this->resolveNextReviewTranslationKeyId(
            query: $editQuery,
            paginator: $editPaginator,
            selectedTranslationKeyId: $this->editingTranslationKeyId,
        );

        $selectedTranslationKey = $this->selectedTranslationKeyId
            ? TranslationKey::query()
            ->with([
                'values' => fn($query) => $query->orderBy('locale'),
                'usages' => fn($query) => $query->orderBy('file')->orderBy('id'),
            ])
            ->find($this->selectedTranslationKeyId)
            : null;

        $editingTranslationKey = $this->editingTranslationKeyId
            ? TranslationKey::query()
            ->with([
                'values' => fn($query) => $query->orderBy('locale', 'asc'),
                'usages' => fn($query) => $query->orderBy('file', 'asc')->orderBy('id', 'asc'),
            ])
            ->find($this->editingTranslationKeyId)
            : null;

        $historyTranslationKey = $this->selectedHistoryTranslationKeyId
            ? TranslationKey::query()
            ->find($this->selectedHistoryTranslationKeyId)
            : null;

        $historyEvents = $this->selectedHistoryTranslationKeyId
            ? DB::table('translation_audit_events')
            ->where('translation_key_id', $this->selectedHistoryTranslationKeyId)
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            : collect();

        $translationLanguages = TranslationLanguage::query()
            ->where('is_enabled_for_translation', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('locale', 'asc')
            ->get([
                'locale',
                'name',
                'native_name',
                'is_default',
                'is_enabled_for_app',
            ]);

        $targetLanguages = $this->resolveTargetLanguagesFromAppSettings();

        $targetLocales = $targetLanguages->pluck('locale')->all();

        $translationCoverage = DB::table('translation_values')
            ->selectRaw('locale, COUNT(*) as row_count, COUNT(CASE WHEN value IS NOT NULL AND value <> \'\' THEN 1 END) as translated_count')
            ->whereIn('locale', $targetLocales)
            ->groupBy('locale')
            ->get()
            ->mapWithKeys(fn(object $row): array => [$row->locale => $row]);

        $locales = $translationLanguages
            ->pluck('locale')
            ->all();

        $translationFiles = TranslationKey::query()
            ->whereNotNull('group', 'and')
            ->distinct()
            ->orderBy('group', 'asc')
            ->pluck('group')
            ->all();

        return view('components.admin.⚡translation-list', [
            'translationKeys' => $translationKeys,
            'workflowStatusCounts' => $workflowStatusCounts,
            'workflowRelevantTotal' => $workflowRelevantTotal,
            'workflowOpenTotal' => $workflowOpenTotal,
            'workflowReviewedTotal' => $workflowReviewedTotal,
            'workflowHistoryTotal' => $workflowHistoryTotal,
            'workflowCompletedTotal' => $workflowCompletedTotal,
            'statusCounts' => $statusCounts,
            'classificationCounts' => $classificationCounts,
            'activeClassificationCounts' => $activeClassificationCounts,
            'total' => $total,
            'activeTypeTotal' => $activeTypeTotal,
            'archiveCount' => $archiveCount,
            'filteredTotal' => $filteredTotal,
            'problemStatuses' => self::PROBLEM_STATUSES,
            'problemCount' => $problemCount,
            'duplicateCount' => $duplicateCount,
            'hasActiveFilters' => $this->hasActiveFilters(),
            'nextReviewTranslationKeyId' => $nextReviewTranslationKeyId,
            'nextEditTranslationKeyId' => $nextEditTranslationKeyId,
            'locales' => $locales,
            'translationLanguages' => $translationLanguages,
            'targetLanguages' => $targetLanguages,
            'translationCoverage' => $translationCoverage,
            'translationFiles' => $translationFiles,
            'selectedTranslationKey' => $selectedTranslationKey,
            'editingTranslationKey' => $editingTranslationKey,
            'historyTranslationKey' => $historyTranslationKey,
            'historyEvents' => $historyEvents,
        ]);
    }

    /**
     * Resolve app-target languages from app settings (app_general.availableLocales).
     */
    private function resolveTargetLanguagesFromAppSettings()
    {
        $appGeneralSettings = app(AppGeneralSettings::class);
        $defaultLocale = LocaleCode::normalize((string) ($appGeneralSettings->locale ?? ''));

        $availableLocales = collect($appGeneralSettings->availableLocales ?? [])
            ->map(static fn(mixed $locale): string => is_string($locale) ? LocaleCode::normalize($locale) : '')
            ->filter(static fn(string $locale): bool => $locale !== '')
            ->values();

        if ($availableLocales->isEmpty()) {
            return collect();
        }

        $languageRows = DB::table('languages')
            ->where('is_active', true)
            ->whereRaw('COALESCE(iso639_1, iso639_3) IS NOT NULL')
            ->get([
                DB::raw('COALESCE(iso639_1, iso639_3) as code'),
                'name',
                'native_name',
            ]);

        $languageByCode = $languageRows
            ->mapWithKeys(static function (object $row): array {
                $code = LocaleCode::normalize((string) ($row->code ?? ''));

                if ($code === '') {
                    return [];
                }

                return [$code => $row];
            });

        return $availableLocales
            ->values()
            ->map(static function (string $locale, int $index) use ($languageByCode, $defaultLocale): object {
                $languageRow = $languageByCode->get($locale);
                $fallbackLabel = strtoupper($locale);

                return (object) [
                    'locale' => $locale,
                    'name' => (string) ($languageRow->name ?? $fallbackLabel),
                    'native_name' => (string) ($languageRow->native_name ?? $fallbackLabel),
                    'is_default' => $defaultLocale !== '' && $locale === $defaultLocale,
                    'is_enabled_for_app' => true,
                    'sort_order' => $index,
                ];
            })
            ->filter(static fn(object $lang): bool => ! $lang->is_default)
            ->values();
    }

    /**
     * Normalize page size to allowed values.
     */
    private function normalizedPerPage(mixed $value = null): int
    {
        $normalizedValue = (int) ($value ?? $this->perPage);

        return in_array($normalizedValue, [10, 25, 50, 100], true)
            ? $normalizedValue
            : 10;
    }

    private function normalizeStatusFilter(mixed $value): string
    {
        $status = trim((string) $value);

        return in_array($status, $this->statusOptions, true) ? $status : 'all';
    }

    private function normalizeWorkflowStatusFilter(mixed $value): string
    {
        $workflowStatus = trim((string) $value);

        return in_array($workflowStatus, $this->workflowStatusOptions, true) ? $workflowStatus : 'open';
    }

    private function normalizeClassificationFilter(mixed $value): string
    {
        $classification = trim((string) $value);

        return in_array($classification, $this->classificationOptions, true) ? $classification : 'all';
    }

    private function persistUiState(): void
    {
        $this->setUserSetting(self::UI_STATE_SETTING_KEY, [
            'search' => $this->search,
            'status' => $this->status,
            'workflowStatus' => $this->workflowStatus,
            'classification' => $this->classification,
            'showArchived' => $this->showArchived,
            'onlyProblems' => $this->onlyProblems,
            'onlyBaseDuplicates' => $this->onlyBaseDuplicates,
            'languageFilter' => $this->languageFilter,
            'fileFilter' => $this->fileFilter,
            'perPage' => $this->perPage,
        ]);
    }

    /**
     * Resolve the next key ID from the same list context used for rendering.
     */
    private function resolveNextReviewTranslationKeyId(
        Builder $query,
        LengthAwarePaginator $paginator,
        ?int $selectedTranslationKeyId,
    ): ?int {
        if (! $selectedTranslationKeyId) {
            return null;
        }

        $currentPageIds = $paginator->getCollection()->pluck('id')->values();
        $currentIndex = $currentPageIds->search($selectedTranslationKeyId);

        if ($currentIndex === false) {
            $fallbackId = $currentPageIds->first();

            return is_numeric($fallbackId) ? (int) $fallbackId : null;
        }

        $nextOnPageId = $currentPageIds->get((int) $currentIndex + 1);

        if (is_numeric($nextOnPageId)) {
            return (int) $nextOnPageId;
        }

        if (! $paginator->hasMorePages()) {
            return null;
        }

        $nextPagePaginator = (clone $query)->paginate(
            $this->normalizedPerPage(),
            ['*'],
            'page',
            $paginator->currentPage() + 1,
        );

        $nextPageFirstId = $nextPagePaginator->getCollection()->pluck('id')->first();

        return is_numeric($nextPageFirstId) ? (int) $nextPageFirstId : null;
    }

    private function namespaceFromTranslationKey(?string $key): ?string
    {
        $key = trim((string) $key);

        if ($key === '') {
            return null;
        }

        if (! str_contains($key, '.')) {
            return null;
        }

        $segments = explode('.', $key);
        $namespace = $segments[0] ?? null;

        return is_string($namespace) && $namespace !== '' ? $namespace : null;
    }

    private function groupFromTranslationKey(?string $key): ?string
    {
        $key = trim((string) $key);

        if ($key === '') {
            return null;
        }

        if (! str_contains($key, '.')) {
            return null;
        }

        $segments = explode('.', $key);

        return $segments[1] ?? null;
    }

    /**
     * Insert an audit event for translation value changes made in the edit modal.
     */
    private function createTranslationValueAuditEvent(
        TranslationKey $translationKey,
        string $locale,
        ?string $oldValue,
        ?string $newValue,
        ?string $oldStatus,
        ?string $newStatus,
        bool $wasCreated,
    ): void {
        DB::table('translation_audit_events')->insert([
            'translation_key_id' => $translationKey->id,
            'translation_usage_id' => null,
            'entity_type' => 'translation_value',
            'event_type' => $wasCreated ? 'created' : 'value_changed',
            'old_fingerprint' => $translationKey->fingerprint,
            'new_fingerprint' => $translationKey->fingerprint,
            'old_file' => null,
            'new_file' => null,
            'old_line' => null,
            'new_line' => null,
            'old_key' => $translationKey->key,
            'new_key' => $translationKey->key,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reason' => 'translation_value_saved_from_edit_modal',
            'context' => json_encode([
                'locale' => $locale,
                'source' => 'translation_edit_modal',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Insert an audit event for translation key changes made from review workflows.
     */
    private function createTranslationKeyAuditEvent(
        TranslationKey $translationKey,
        ?string $oldKey,
        ?string $newKey,
        string $reason,
        array $context = [],
    ): void {
        DB::table('translation_audit_events')->insert([
            'translation_key_id' => $translationKey->id,
            'translation_usage_id' => null,
            'entity_type' => 'translation_key',
            'event_type' => 'key_changed',
            'old_fingerprint' => $translationKey->fingerprint,
            'new_fingerprint' => $translationKey->fingerprint,
            'old_file' => null,
            'new_file' => null,
            'old_line' => null,
            'new_line' => null,
            'old_key' => $oldKey,
            'new_key' => $newKey,
            'old_value' => $translationKey->native_text,
            'new_value' => $translationKey->native_text,
            'old_status' => $translationKey->status,
            'new_status' => $translationKey->status,
            'reason' => $reason,
            'context' => json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Insert an audit event for workflow status changes on translation keys.
     */
    private function createTranslationWorkflowAuditEvent(
        TranslationKey $translationKey,
        string $oldWorkflowStatus,
        string $newWorkflowStatus,
        string $reason,
        array $context = [],
    ): void {
        DB::table('translation_audit_events')->insert([
            'translation_key_id' => $translationKey->id,
            'translation_usage_id' => null,
            'entity_type' => 'translation_key',
            'event_type' => 'workflow_status_changed',
            'old_fingerprint' => $translationKey->fingerprint,
            'new_fingerprint' => $translationKey->fingerprint,
            'old_file' => null,
            'new_file' => null,
            'old_line' => null,
            'new_line' => null,
            'old_key' => $translationKey->key,
            'new_key' => $translationKey->key,
            'old_value' => $translationKey->review_note,
            'new_value' => $translationKey->review_note,
            'old_status' => $translationKey->status,
            'new_status' => $translationKey->status,
            'reason' => $reason,
            'context' => json_encode([
                ...$context,
                'old_workflow_status' => $oldWorkflowStatus,
                'new_workflow_status' => $newWorkflowStatus,
                'reviewed_by_user_id' => $translationKey->reviewed_by_user_id,
                'reviewed_at' => $translationKey->reviewed_at?->toDateTimeString(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
