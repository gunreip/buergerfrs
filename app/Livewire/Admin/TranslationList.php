<?php

// app/Livewire/Admin/TranslationList.php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Models\TranslationKey;
use App\Models\TranslationLanguage;
use App\Models\TranslationUsage;
use App\Models\TranslationValue;
use App\Settings\AppGeneralSettings;
use App\Support\Audit\TranslationActivity;
use App\Support\Locale\LocaleCode;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

/**
 * Livewire administration component for the translation list and review workflow.
 *
 * This component is the central UI controller for browsing, filtering, reviewing,
 * editing and auditing translation keys in the administration area. It combines
 * persisted list filters, workflow scopes, language and namespace filters, status
 * counters, paginated table rendering and the review/edit/history modal workflows.
 *
 * The component intentionally separates database-level translation metadata from
 * code-rewrite operations. UI actions may update translation values, suggested-key
 * decisions or workflow metadata, but source-code rewrites are handled by the
 * dedicated translation audit and project translation console commands.
 *
 * Main responsibilities:
 * - maintain persisted filter, sorting and pagination state for the translation list;
 * - provide workflow counters for open, reviewed, completed, problem and archive states;
 * - render review, edit and history modal data for selected translation keys;
 * - persist manual translation value changes and write audit events;
 * - expose Usage-Audit follow-up filters such as the Needs-Key focus without directly
 *   applying source-code changes.
 */
class TranslationList extends Component
{
    use InteractsWithUserSettings;
    use WithoutUrlPagination;
    use WithPagination;

    /**
     * User-settings key used to persist this page's list state per authenticated user.
     *
     * The persisted payload is intentionally limited to UI state such as filters, sorting and
     * pagination. Modal state, selected rows and editable form values are kept ephemeral.
     */
    private const UI_STATE_SETTING_KEY = 'ui.pages.admin_translation_list';

    /**
     * Translation usage reason written by the audit sync when a previously known usage was not
     * found in the latest scan anymore.
     *
     * Active list scopes ignore these stale usages so the default workflow focuses on current
     * code-relevant translation entries, while archived/review contexts can still inspect them.
     */
    private const STALE_AUDIT_USAGE_REASON = 'stale_audit_usage_not_seen_in_latest_sync';

    /**
     * Livewire public properties that are persisted as page UI state.
     *
     * Each property listed here resets pagination when changed and is written through the
     * InteractsWithUserSettings concern. Keeping this allow-list explicit prevents modal-specific
     * or transient editing state from leaking into the user's saved page preferences.
     *
     * @var array<int, string>
     */
    private const PERSISTED_STATE_PROPERTIES = [
        'search',
        'status',
        'workflowStatus',
        'classification',
        'dynamicFilter',
        'showArchived',
        'onlyProblems',
        'onlyBaseDuplicates',
        'onlyNeedsKey',
        'languageFilter',
        'namespaceFilter',
        'groupFilter',
        'perPage',
        'sortField',
        'sortDirection',
    ];

    /**
     * Translation-key statuses considered actionable problems by the quick-focus filter.
     *
     * Obsolete and invalid entries are handled by their own workflow/status controls, so this
     * problem set intentionally stays focused on missing values and dynamic keys that need review.
     *
     * @var array<int, string>
     */
    private const PROBLEM_STATUSES = [
        'missing',
        'dynamic',
    ];

    /**
     * Free-text search over keys, suggested keys, native text, values and usage metadata.
     */
    public string $search = '';

    public string $status = 'all';

    public string $workflowStatus = 'open';

    public string $classification = 'all';

    public string $dynamicFilter = 'none';

    public bool $showArchived = false;

    public bool $onlyProblems = false;

    public bool $onlyBaseDuplicates = false;

    /**
     * Focus the list on translation keys that have a Usage-Audit follow-up marked as Needs-Key.
     *
     * This is a review/navigation filter only. It does not create keys and does not rewrite source
     * code; those operations remain part of the translation command pipeline.
     */
    public bool $onlyNeedsKey = false;

    public string $languageFilter = '';

    public string $namespaceFilter = '';

    public string $groupFilter = '';

    public int $perPage = 25;

    public ?int $selectedTranslationKeyId = null;

    public bool $translationKeyModalOpen = false;

    public bool $selectedTranslationKeyDynamicMulti = false;

    public ?int $editingTranslationKeyId = null;

    public bool $translationEditModalOpen = false;

    public array $translationEditValues = [];

    public array $translationEditSubLanguageLocales = [];

    public ?int $focusedTranslationKeyId = null;

    public ?int $selectedHistoryTranslationKeyId = null;

    public bool $translationHistoryModalOpen = false;

    public int $historyEventLimit = 50;

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
    ];

    /**
     * @var array<int, string>
     */
    public array $dynamicFilterOptions = [
        'none',
        'all',
        'candidate',
        'multi',
        'without_suggested_key',
        'reactivated_stale',
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
     *
     * Stored values are normalized before assignment so stale settings, unsupported option values
     * or outdated page-size values cannot put the component into an invalid filter state.
     */
    public function mount(): void
    {
        $state = $this->userSetting(self::UI_STATE_SETTING_KEY, []);

        if (! is_array($state)) {
            return;
        }

        $rawStatus = $state['status'] ?? $this->status;
        $rawClassification = $state['classification'] ?? $this->classification;

        $this->search = trim((string) ($state['search'] ?? $this->search));
        $this->status = $this->normalizeStatusFilter($rawStatus);
        $this->workflowStatus = $this->normalizeWorkflowStatusFilter($state['workflowStatus'] ?? $this->workflowStatus);
        $this->classification = $this->normalizeClassificationFilter($rawClassification);
        $this->dynamicFilter = $this->normalizeDynamicFilter($state['dynamicFilter'] ?? $this->dynamicFilter);

        if ($this->dynamicFilter === 'none' && ($rawStatus === 'dynamic' || $rawClassification === 'dynamic')) {
            $this->dynamicFilter = 'all';
        }

        $this->showArchived = (bool) ($state['showArchived'] ?? $this->showArchived);
        $this->onlyProblems = (bool) ($state['onlyProblems'] ?? $this->onlyProblems);
        $this->onlyBaseDuplicates = (bool) ($state['onlyBaseDuplicates'] ?? $this->onlyBaseDuplicates);
        $this->onlyNeedsKey = (bool) ($state['onlyNeedsKey'] ?? $this->onlyNeedsKey);
        $this->languageFilter = trim((string) ($state['languageFilter'] ?? $this->languageFilter));
        $this->namespaceFilter = trim((string) ($state['namespaceFilter'] ?? $this->namespaceFilter));
        $this->groupFilter = trim((string) ($state['groupFilter'] ?? $this->groupFilter));
        $this->perPage = $this->normalizedPerPage($state['perPage'] ?? $this->perPage);
        $this->sortField = $this->normalizeSortField($state['sortField'] ?? $this->sortField);
        $this->sortDirection = $this->normalizeSortDirection($state['sortDirection'] ?? $this->sortDirection);

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
     * Keep the group filter valid when the namespace filter changes.
     *
     * Groups are namespace-dependent in the UI. If the currently selected group is no longer
     * available after changing the namespace, the group filter is cleared to avoid an empty result
     * set caused by an impossible namespace/group combination.
     */
    public function updatedNamespaceFilter(): void
    {
        if ($this->groupFilter === '') {
            return;
        }

        $availableGroups = $this->filteredTranslationKeyQuery(['groupFilter'])
            ->whereNotNull('group')
            ->distinct()
            ->pluck('group')
            ->filter(static fn (mixed $group): bool => is_string($group) && trim($group) !== '')
            ->values()
            ->all();

        if (! in_array($this->groupFilter, $availableGroups, true)) {
            $this->groupFilter = '';
        }
    }

    /**
     * Normalize selectable page size when changed from UI.
     */
    public function updatedPerPage(): void
    {
        $this->perPage = $this->normalizedPerPage($this->perPage);
        $this->setPage(1);
        $this->persistUiState();
    }

    /**
     * Explicitly set page size from segmented controls that do not reliably hydrate wire:model.
     */
    public function setPerPage(int $perPage): void
    {
        $this->perPage = $this->normalizedPerPage($perPage);
        $this->setPage(1);
        $this->persistUiState();
    }

    /**
     * Sort the translation list by the selected field.
     */
    public function sortBy(string $field): void
    {
        $field = $this->normalizeSortField($field);

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = $field === 'updated_at' ? 'desc' : 'asc';
        }

        $this->resetPage();
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
        $this->dynamicFilter = 'none';
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
        $this->dynamicFilter = 'none';
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
        $this->dynamicFilter = 'none';
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
        $this->dynamicFilter = 'none';
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
        $this->dynamicFilter = 'none';
        $this->resetPage();
        $this->persistUiState();
    }

    /**
     * Focus the list on dynamic translation work without mixing it into status/type filters.
     */
    public function setDynamicFilter(string $dynamicFilter): void
    {
        $this->dynamicFilter = $this->normalizeDynamicFilter($dynamicFilter);
        $this->showArchived = false;
        $this->workflowStatus = 'all';
        $this->status = 'all';
        $this->classification = 'all';
        $this->onlyProblems = false;
        $this->onlyBaseDuplicates = false;
        $this->onlyNeedsKey = false;
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
        $this->status = 'all';
        $this->classification = $classification;
        $this->dynamicFilter = 'none';
        $this->onlyProblems = false;
        $this->onlyBaseDuplicates = false;
        $this->onlyNeedsKey = false;
        $this->search = '';
        $this->namespaceFilter = '';
        $this->groupFilter = '';
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
        $this->dynamicFilter = 'none';
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
     * Toggle the Usage-Audit Needs-Key follow-up filter and reset pagination.
     *
     * This filter intentionally uses the decision tables as its source of truth instead of the
     * regular translation-key status. That keeps Usage-Audit follow-up work separate from general
     * missing/obsolete/dynamic translation status handling.
     */
    public function toggleOnlyNeedsKey(): void
    {
        $this->onlyNeedsKey = ! $this->onlyNeedsKey;
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
        $this->dynamicFilter = 'none';
        $this->showArchived = false;
        $this->onlyProblems = false;
        $this->onlyBaseDuplicates = false;
        $this->onlyNeedsKey = false;
        $this->languageFilter = '';
        $this->namespaceFilter = '';
        $this->groupFilter = '';
        $this->perPage = 25;
        $this->sortField = 'updated_at';
        $this->sortDirection = 'desc';

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
        $this->selectedTranslationKeyDynamicMulti = (bool) TranslationKey::query()
            ->whereKey($translationKeyId)
            ->value('is_dynamic_multi');
        $this->translationKeyModalOpen = true;
    }

    public function closeTranslationKey(): void
    {
        $this->translationKeyModalOpen = false;
        $this->selectedTranslationKeyId = null;
        $this->selectedTranslationKeyDynamicMulti = false;
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
                'values' => fn ($query) => $query->orderBy('locale', 'asc'),
            ])
            ->find($translationKeyId);

        if (! $translationKey || trim((string) ($translationKey->key ?? '')) === '') {
            return;
        }

        $translationLanguages = $this->resolveTranslationEditLanguages();

        $this->translationEditValues = $translationLanguages
            ->mapWithKeys(function (object $translationLanguage) use ($translationKey): array {
                $locale = LocaleCode::normalize((string) ($translationLanguage->locale ?? ''));
                $translationValue = $translationKey->values->firstWhere('locale', $locale);

                return [
                    $locale => (string) ($translationValue?->value ?? ''),
                ];
            })
            ->all();

        $this->focusedTranslationKeyId = $translationKey->id;
        $this->editingTranslationKeyId = $translationKey->id;
        $this->translationEditSubLanguageLocales = $this->defaultTranslationEditSubLanguageLocales();
        $this->translationEditModalOpen = true;
    }

    /**
     * Close translation edit modal and clear edit state.
     */
    public function closeTranslationEdit(): void
    {
        $hasUnsavedChanges = $this->hasUnsavedTranslationEditChanges();

        $this->translationEditModalOpen = false;
        $this->editingTranslationKeyId = null;
        $this->translationEditValues = [];
        $this->translationEditSubLanguageLocales = [];

        if ($hasUnsavedChanges) {
            Flux::toast(
                heading: __('admin.translation_list.changes_discarded'),
                text: __('admin.translation_list.unsaved_translation_value_changes_have_been_discarded'),
                variant: 'warning',
                duration: 5000,
            );
        }
    }

    /**
     * Determine whether the current edit modal contains unsaved value changes.
     */
    private function hasUnsavedTranslationEditChanges(): bool
    {
        if (! $this->editingTranslationKeyId) {
            return false;
        }

        if ($this->translationEditValues === []) {
            return false;
        }

        $storedValues = TranslationValue::query()
            ->where('translation_key_id', $this->editingTranslationKeyId)
            ->whereIn(
                'locale',
                collect($this->translationEditValues)
                    ->keys()
                    ->map(static fn (mixed $locale): string => LocaleCode::normalize((string) $locale))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all(),
            )
            ->get(['locale', 'value'])
            ->mapWithKeys(static fn (TranslationValue $translationValue): array => [
                LocaleCode::normalize((string) ($translationValue->locale ?? '')) => (string) ($translationValue->value ?? ''),
            ]);

        foreach ($this->translationEditValues as $locale => $currentValue) {
            $normalizedLocale = LocaleCode::normalize((string) $locale);

            if ($normalizedLocale === '') {
                continue;
            }

            $currentValue = trim((string) $currentValue);
            $storedValue = trim((string) ($storedValues->get($normalizedLocale) ?? ''));

            if ($currentValue !== $storedValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * Toggle one active sub-language in the edit modal to reveal its textarea.
     */
    public function selectTranslationEditSubLanguage(string $locale): void
    {
        $normalizedLocale = LocaleCode::normalize($locale);

        if ($normalizedLocale === '') {
            return;
        }

        $allowedLocales = $this->resolveActiveSubLanguagesForCurrentTargetFilter()
            ->pluck('locale')
            ->map(static fn (mixed $item): string => LocaleCode::normalize((string) $item))
            ->filter()
            ->values()
            ->all();

        if (! in_array($normalizedLocale, $allowedLocales, true)) {
            return;
        }

        $selectedLocales = collect($this->translationEditSubLanguageLocales)
            ->map(static fn (mixed $item): string => LocaleCode::normalize((string) $item))
            ->filter()
            ->unique()
            ->values();

        $this->translationEditSubLanguageLocales = $selectedLocales->contains($normalizedLocale)
            ? $selectedLocales->reject(static fn (string $locale): bool => $locale === $normalizedLocale)->values()->all()
            : $selectedLocales->push($normalizedLocale)->unique()->values()->all();
    }

    /**
     * Copy the first selected sub-language value to all other selected sub-language values.
     */
    public function copyFirstSelectedSubLanguageValueToAllSelectedSubLanguages(): void
    {
        if (! $this->editingTranslationKeyId) {
            return;
        }

        $selectedLocales = collect($this->translationEditSubLanguageLocales)
            ->map(static fn (mixed $locale): string => LocaleCode::normalize((string) $locale))
            ->filter()
            ->unique()
            ->values();

        if ($selectedLocales->count() < 2) {
            return;
        }

        $sourceLocale = (string) $selectedLocales->first();
        $sourceValue = (string) ($this->translationEditValues[$sourceLocale] ?? '');

        foreach ($selectedLocales->skip(1) as $targetLocale) {
            $this->translationEditValues[$targetLocale] = $sourceValue;
        }

        Flux::toast(
            heading: __('admin.translation_list.copied'),
            text: __('admin.translation_list.the_first_language_variation_value_was_copied_to_all_visible_language_variations'),
            variant: 'info',
            duration: 4000,
        );
    }

    /**
     * Clear all selected sub-language values currently visible in the edit modal.
     */
    public function clearSelectedSubLanguageValues(): void
    {
        if (! $this->editingTranslationKeyId) {
            return;
        }

        $selectedLocales = collect($this->translationEditSubLanguageLocales)
            ->map(static fn (mixed $locale): string => LocaleCode::normalize((string) $locale))
            ->filter()
            ->unique()
            ->values();

        if ($selectedLocales->isEmpty()) {
            return;
        }

        foreach ($selectedLocales as $locale) {
            $this->translationEditValues[$locale] = '';
        }

        Flux::toast(
            heading: __('admin.translation_list.cleared'),
            text: __('admin.translation_list.all_visible_language_variation_values_have_been_cleared'),
            variant: 'warning',
            duration: 4000,
        );
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
     * Open the history modal for a translation key, including an empty history state.
     */
    public function openTranslationHistory(int $translationKeyId): void
    {
        if ($this->selectedHistoryTranslationKeyId !== $translationKeyId) {
            $this->historyEventLimit = 50;
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

    public function loadOlderTranslationHistoryEvents(): void
    {
        if (! $this->selectedHistoryTranslationKeyId) {
            return;
        }

        $this->historyEventLimit += 50;
    }

    public function openNextTranslationHistoryFromList(): void
    {
        if (! $this->selectedHistoryTranslationKeyId) {
            return;
        }

        $query = $this->translationKeyQuery();

        $paginator = (clone $query)->paginate(
            $this->normalizedPerPage(),
            ['*'],
            'page',
            $this->getPage(),
        );

        $nextHistoryTranslationKeyId = $this->resolveNextHistoryTranslationKeyId(
            query: $query,
            paginator: $paginator,
            selectedTranslationKeyId: $this->selectedHistoryTranslationKeyId,
        );

        if ($nextHistoryTranslationKeyId === null) {
            return;
        }

        $this->openTranslationHistory($nextHistoryTranslationKeyId);
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

    public function updatedSelectedTranslationKeyDynamicMulti(mixed $value): void
    {
        if (! $this->selectedTranslationKeyId) {
            return;
        }

        $this->setDynamicMulti(
            translationKeyId: $this->selectedTranslationKeyId,
            isDynamicMulti: filter_var($value, FILTER_VALIDATE_BOOLEAN),
            translationActivity: app(TranslationActivity::class),
        );
    }

    public function setDynamicMulti(
        int $translationKeyId,
        bool $isDynamicMulti,
        TranslationActivity $translationActivity,
    ): void
    {
        $translationKey = TranslationKey::query()->find($translationKeyId);

        if (! $translationKey) {
            $this->selectedTranslationKeyDynamicMulti = false;

            return;
        }

        $wasDynamicMulti = (bool) ($translationKey->is_dynamic_multi ?? false);
        $this->selectedTranslationKeyDynamicMulti = $wasDynamicMulti;

        if (($translationKey->classification ?? null) !== 'dynamic') {
            Flux::toast(
                heading: __('Dynamic multi not applicable'),
                text: __('Only dynamic translation candidates can be marked as dynamic multi.'),
                variant: 'warning',
                duration: 5000,
            );

            return;
        }

        $currentKey = trim((string) ($translationKey->key ?? ''));

        if ($currentKey === '') {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Set a translation key before changing the dynamic multi workflow state.'),
                variant: 'warning',
                duration: 5000,
            );

            return;
        }

        if ($wasDynamicMulti === $isDynamicMulti) {
            return;
        }

        $translationKey->forceFill([
            'is_dynamic_multi' => $isDynamicMulti,
            'namespace' => $this->namespaceFromTranslationKey($currentKey),
            'group' => $this->groupFromTranslationKey($currentKey),
            'classification' => 'dynamic',
            'status' => 'dynamic',
            'source' => 'dynamic_audit',
        ])->save();

        $this->selectedTranslationKeyDynamicMulti = $isDynamicMulti;

        $this->createTranslationWorkflowAuditEvent(
            translationKey: $translationKey,
            oldWorkflowStatus: (string) ($translationKey->workflow_status ?? 'open'),
            newWorkflowStatus: (string) ($translationKey->workflow_status ?? 'open'),
            reason: $isDynamicMulti
                ? 'dynamic_candidate_marked_as_multi'
                : 'dynamic_candidate_unmarked_as_multi',
            context: [
                'source' => 'translation_review_modal',
                'was_dynamic_multi' => $wasDynamicMulti,
                'is_dynamic_multi' => $isDynamicMulti,
            ],
        );

        $translationActivity->record(
            event: 'translations.admin.key.dynamic_multi_toggled',
            description: $isDynamicMulti
                ? __('Dynamic translation candidate marked as multi')
                : __('Dynamic translation candidate removed from multi workflow'),
            subject: $translationKey,
            before: ['is_dynamic_multi' => $wasDynamicMulti],
            after: ['is_dynamic_multi' => $isDynamicMulti],
        );

        $this->focusedTranslationKeyId = $translationKey->id;

        Flux::toast(
            heading: $isDynamicMulti ? __('Dynamic multi marked') : __('Dynamic multi removed'),
            text: $isDynamicMulti
                ? __('The translation candidate now uses the dynamic multi workflow.')
                : __('The translation candidate no longer uses the dynamic multi workflow.'),
            variant: 'success',
            duration: 5000,
        );
    }

    /**
     * Apply the suggested key as the active translation key from the review modal.
     */
    public function applySuggestedKey(int $translationKeyId, TranslationActivity $translationActivity): void
    {
        $translationKey = TranslationKey::query()->find($translationKeyId);

        if (! $translationKey) {
            return;
        }

        $suggestedKey = trim((string) ($translationKey->suggested_key ?? ''));
        $currentKey = trim((string) ($translationKey->key ?? ''));

        if ($suggestedKey === '') {
            Flux::toast(
                heading: __('admin.translation_list.no_suggested_key_available'),
                text: __('admin.translation_list.there_is_no_suggested_key_to_apply_for_this_entry'),
                variant: 'warning',
                duration: 5000,
            );

            return;
        }

        if ($currentKey === $suggestedKey) {
            Flux::toast(
                heading: __('admin.translation_list.suggested_key_already_applied'),
                text: __('admin.translation_list.the_translation_key_already_matches_the_suggested_key'),
                variant: 'info',
                duration: 4000,
            );

            return;
        }

        $currentStatus = trim((string) ($translationKey->status ?? ''));
        $isDynamicTranslationKey =
            ($translationKey->classification ?? null) === 'dynamic'
            || str_starts_with($suggestedKey, 'dynamic.');

        $resolvedStatus = $isDynamicTranslationKey
            ? ($currentStatus !== '' ? $currentStatus : 'dynamic')
            : (in_array($currentStatus, ['ok', 'missing', 'obsolete'], true)
            ? $currentStatus
            : 'missing');

        $translationKey->forceFill([
            'key' => $suggestedKey,
            'namespace' => $this->namespaceFromTranslationKey($suggestedKey),
            'group' => $this->groupFromTranslationKey($suggestedKey),
            'classification' => $isDynamicTranslationKey ? 'dynamic' : 'key',
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

        $translationActivity->record(
            event: 'translations.admin.key.suggested_key_applied',
            description: __('Suggested translation key applied'),
            subject: $translationKey,
            before: ['key' => $currentKey !== '' ? $currentKey : null],
            after: ['key' => $suggestedKey],
        );

        $this->focusedTranslationKeyId = $translationKey->id;

        Flux::toast(
            heading: __('admin.translation_list.suggested_key_applied'),
            text: __('admin.translation_list.the_suggested_key_has_been_copied_to_the_translation_key'),
            variant: 'success',
            duration: 5000,
        );
    }

    /**
     * Mark an obsolete key as reviewed so it is hidden from the default open workflow.
     */
    public function markObsoleteAsReviewed(int $translationKeyId, TranslationActivity $translationActivity): void
    {
        $translationKey = TranslationKey::query()->find($translationKeyId);

        if (! $translationKey) {
            return;
        }

        if (($translationKey->status ?? '') !== 'obsolete') {
            Flux::toast(
                heading: __('admin.translation_list.review_not_applicable'),
                text: __('admin.translation_list.only_obsolete_entries_can_be_marked_as_reviewed'),
                variant: 'warning',
                duration: 5000,
            );

            return;
        }

        if (($translationKey->workflow_status ?? 'open') === 'reviewed') {
            Flux::toast(
                heading: __('admin.translation_list.already_reviewed'),
                text: __('admin.translation_list.this_obsolete_entry_is_already_marked_as_reviewed'),
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

        $translationActivity->record(
            event: 'translations.admin.key.obsolete_reviewed',
            description: __('Obsolete translation key reviewed'),
            subject: $translationKey,
            before: ['workflow_status' => $oldWorkflowStatus],
            after: ['workflow_status' => 'reviewed'],
        );

        $this->focusedTranslationKeyId = $translationKey->id;

        Flux::toast(
            heading: __('admin.translation_list.obsolete_entry_reviewed'),
            text: __('admin.translation_list.the_obsolete_entry_has_been_marked_as_reviewed_and_leaves_the_default_workflow_l'),
            variant: 'success',
            duration: 5000,
        );
    }

    /**
     * Mark a translation key as manually requiring a new translation key.
     *
     * This state is intentionally stored directly on translation_keys and not in Usage-Audit decision
     * rows, so it remains independent from generated audit reports and usage-decision commands.
     */
    public function markNeedsNewKeyManually(int $translationKeyId, TranslationActivity $translationActivity): void
    {
        $translationKey = TranslationKey::query()->find($translationKeyId);

        if (! $translationKey) {
            return;
        }

        if (trim((string) ($translationKey->key ?? '')) === '') {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Set a translation key before marking this entry as needing a new key.'),
                variant: 'warning',
                duration: 5000,
            );

            return;
        }

        $wasActive = $translationKey->needs_new_key_marked_at !== null
            && $translationKey->needs_new_key_resolved_at === null;

        if ($wasActive) {
            Flux::toast(
                heading: __('Needs new key already marked'),
                text: __('This translation key is already marked as needing a new key.'),
                variant: 'info',
                duration: 4000,
            );

            return;
        }

        $translationKey->forceFill([
            'needs_new_key_marked_at' => now(),
            'needs_new_key_marked_by_user_id' => Auth::id(),
            'needs_new_key_note' => 'marked_manually_from_translation_list',
            'needs_new_key_resolved_at' => null,
        ])->save();

        $this->createTranslationManualNeedsNewKeyAuditEvent(
            translationKey: $translationKey,
            wasActive: false,
            isActive: true,
            reason: 'manual_needs_new_key_marked_from_translation_list',
            context: [
                'source' => 'translation_list',
            ],
        );

        $translationActivity->record(
            event: 'translations.admin.key.needs_new_key_marked',
            description: __('Translation key marked as needing a new key'),
            subject: $translationKey,
            before: ['needs_new_key' => false],
            after: ['needs_new_key' => true],
        );

        $this->focusedTranslationKeyId = $translationKey->id;

        Flux::toast(
            heading: __('Needs new key marked'),
            text: __('This translation key has been manually marked as needing a new key.'),
            variant: 'success',
            duration: 5000,
        );
    }

    /**
     * Resolve the manual Needs-New-Key marker without touching audit-generated follow-ups.
     */
    public function clearNeedsNewKeyManually(int $translationKeyId, TranslationActivity $translationActivity): void
    {
        $translationKey = TranslationKey::query()->find($translationKeyId);

        if (! $translationKey) {
            return;
        }

        if (trim((string) ($translationKey->key ?? '')) === '') {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Set a translation key before resolving the Needs-New-Key marker.'),
                variant: 'warning',
                duration: 5000,
            );

            return;
        }

        $wasActive = $translationKey->needs_new_key_marked_at !== null
            && $translationKey->needs_new_key_resolved_at === null;

        if (! $wasActive) {
            Flux::toast(
                heading: __('Needs new key is not active'),
                text: __('This translation key is not manually marked as needing a new key.'),
                variant: 'info',
                duration: 4000,
            );

            return;
        }

        $translationKey->forceFill([
            'needs_new_key_resolved_at' => now(),
        ])->save();

        $this->createTranslationManualNeedsNewKeyAuditEvent(
            translationKey: $translationKey,
            wasActive: true,
            isActive: false,
            reason: 'manual_needs_new_key_resolved_from_translation_list',
            context: [
                'source' => 'translation_list',
            ],
        );

        $translationActivity->record(
            event: 'translations.admin.key.needs_new_key_resolved',
            description: __('Translation key no longer needs a new key'),
            subject: $translationKey,
            before: ['needs_new_key' => true],
            after: ['needs_new_key' => false],
        );

        $this->focusedTranslationKeyId = $translationKey->id;

        Flux::toast(
            heading: __('Needs new key resolved'),
            text: __('The manual Needs-New-Key marker has been resolved.'),
            variant: 'success',
            duration: 5000,
        );
    }

    /**
     * Persist editable translation values and record audit events for changes.
     */
    public function saveTranslationEdit(TranslationActivity $translationActivity): void
    {
        if (! $this->editingTranslationKeyId) {
            return;
        }

        $translationKey = TranslationKey::query()->find($this->editingTranslationKeyId);

        if (! $translationKey || trim((string) ($translationKey->key ?? '')) === '') {
            $this->closeTranslationEdit();

            return;
        }

        $translationLanguages = $this->resolveTranslationEditLanguages();

        $this->validate(
            $translationLanguages
                ->mapWithKeys(fn (object $translationLanguage): array => [
                    'translationEditValues.'.LocaleCode::normalize((string) ($translationLanguage->locale ?? '')) => [
                        'nullable',
                        'string',
                        'max:10000',
                    ],
                ])
                ->all()
        );

        $activityBefore = [];
        $activityAfter = [];

        foreach ($translationLanguages as $translationLanguage) {
            $locale = LocaleCode::normalize((string) ($translationLanguage->locale ?? ''));
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
                $activityBefore[$locale] = [
                    'status' => $oldStatus,
                    'has_value' => trim((string) $oldValue) !== '',
                ];
                $activityAfter[$locale] = [
                    'status' => $newStatus,
                    'has_value' => trim($value) !== '',
                ];

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

        if ($activityAfter !== []) {
            $translationActivity->record(
                event: 'translations.admin.key.values_updated',
                description: __('Translation values updated'),
                subject: $translationKey,
                before: $activityBefore,
                after: $activityAfter,
                properties: [
                    'translation_key' => $translationKey->key,
                    'changed_locales' => array_keys($activityAfter),
                ],
            );
        }

        $this->focusedTranslationKeyId = $translationKey->id;

        Flux::toast(
            heading: __('admin.translation_list.translation_values_saved'),
            text: __('admin.translation_list.the_translation_values_have_been_saved_successfully'),
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
            || $this->dynamicFilter !== 'none'
            || $this->showArchived
            || $this->onlyProblems
            || $this->onlyBaseDuplicates
            || $this->onlyNeedsKey
            || $this->languageFilter !== ''
            || $this->namespaceFilter !== ''
            || $this->groupFilter !== ''
            || $this->perPage !== 25;
    }

    /**
     * Normalize sortable table field names.
     */
    private function normalizeSortField(mixed $field): string
    {
        $field = is_string($field) ? trim($field) : '';

        return in_array($field, [
            'id',
            'status',
            'key',
            'native_text',
            'usages_count',
            'updated_at',
        ], true)
            ? $field
            : 'updated_at';
    }

    /**
     * Normalize sortable table direction.
     */
    private function normalizeSortDirection(mixed $direction): string
    {
        return $direction === 'asc' ? 'asc' : 'desc';
    }

    /**
     * Apply the active table sorting to the translation key query.
     */
    private function applyTranslationKeySort(Builder $query): Builder
    {
        $direction = $this->normalizeSortDirection($this->sortDirection);

        return match ($this->normalizeSortField($this->sortField)) {
            'id' => $query
                ->orderBy('translation_keys.id', $direction),

            'status' => $query
                ->orderBy('translation_keys.status', $direction)
                ->orderBy('translation_keys.id', 'asc'),

            'key' => $query
                ->orderByRaw("LOWER(COALESCE(NULLIF(translation_keys.key, ''), translation_keys.suggested_key, '')) {$direction}")
                ->orderBy('translation_keys.id', 'asc'),

            'native_text' => $query
                ->orderByRaw("LOWER(COALESCE(translation_keys.native_text, '')) {$direction}")
                ->orderBy('translation_keys.id', 'asc'),

            'usages_count' => $query
                ->orderBy('usages_count', $direction)
                ->orderBy('translation_keys.id', 'asc'),

            default => $query
                ->orderBy('translation_keys.updated_at', $direction)
                ->orderBy('translation_keys.id', 'asc'),
        };
    }

    /**
     * Build the TranslationKey-id query for Usage-Audit Needs-Key follow-ups.
     *
     * The subquery links decision usages back to translation_keys.id and is reused for filtering
     * the list. A TranslationKey qualifies when either the parent decision or at least one linked
     * usage row was explicitly marked as needing a real key.
     */
    private function needsKeyUsageAuditTranslationKeyIdQuery()
    {
        return DB::table('translation_usage_audit_decision_usages')
            ->select('translation_usage_audit_decision_usages.translation_key_id')
            ->join(
                'translation_usage_audit_decisions',
                'translation_usage_audit_decisions.id',
                '=',
                'translation_usage_audit_decision_usages.translation_usage_audit_decision_id',
            )
            ->whereNotNull('translation_usage_audit_decision_usages.translation_key_id')
            ->where(function ($query): void {
                $query
                    ->where('translation_usage_audit_decisions.decision_status', 'needs_key')
                    ->orWhere('translation_usage_audit_decisions.decision_action', 'create_new_key')
                    ->orWhere('translation_usage_audit_decision_usages.change_status', 'needs_key');
            })
            ->distinct();
    }

    /**
     * Build a per-TranslationKey count query for Usage-Audit Needs-Key follow-ups.
     *
     * The result is selected as needs_key_usage_audit_follow_up_count for table badges and modal
     * context. Keeping this as a correlated subquery avoids eager-loading decision models for each
     * visible row.
     */
    private function needsKeyUsageAuditFollowUpCountQuery()
    {
        return DB::table('translation_usage_audit_decision_usages')
            ->selectRaw('count(*)')
            ->join(
                'translation_usage_audit_decisions',
                'translation_usage_audit_decisions.id',
                '=',
                'translation_usage_audit_decision_usages.translation_usage_audit_decision_id',
            )
            ->whereColumn('translation_usage_audit_decision_usages.translation_key_id', 'translation_keys.id')
            ->where(function ($query): void {
                $query
                    ->where('translation_usage_audit_decisions.decision_status', 'needs_key')
                    ->orWhere('translation_usage_audit_decisions.decision_action', 'create_new_key')
                    ->orWhere('translation_usage_audit_decision_usages.change_status', 'needs_key');
            });
    }

    /**
     * Resolve filters that must not narrow the list when the Usage-Audit needs-key focus is active.
     *
     * @return array<int, string>
     */
    private function onlyNeedsKeyListFilterExceptions(): array
    {
        if (! $this->onlyNeedsKey) {
            return [];
        }

        return [
            'workflowStatus',
            'status',
            'classification',
            'dynamicFilter',
            'onlyProblems',
            'onlyBaseDuplicates',
            'search',
            'languageFilter',
            'namespaceFilter',
            'groupFilter',
        ];
    }

    /**
     * Build the base translation key query used by the visible table and list-navigation actions.
     *
     * The query includes row metadata required by the UI, such as usage counts, history-event counts
     * and Usage-Audit follow-up counts. Sorting is applied last so all list consumers share the same
     * row order.
     */
    private function translationKeyQuery(): Builder
    {
        $query = $this->filteredTranslationKeyQuery($this->onlyNeedsKeyListFilterExceptions())
            ->select('translation_keys.*')
            ->with([
                'values' => fn ($query) => $query->orderBy('locale'),
            ])
            ->withCount('usages')
            ->selectSub(
                $this->needsKeyUsageAuditFollowUpCountQuery(),
                'needs_key_usage_audit_follow_up_count',
            );

        return $this->applyTranslationKeySort($query);
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

        if (! in_array('obsoleteStatus', $exceptFilters, true) && $this->status !== 'obsolete') {
            $query = $this->excludeObsoleteStatus($query);
        }

        return $query
            ->when(
                ! in_array('status', $exceptFilters, true) && $this->status !== 'all',
                fn (Builder $query): Builder => $this->applyStatusFilter($query, $this->status),
            )
            ->when(
                ! in_array('classification', $exceptFilters, true) && $this->classification !== 'all',
                fn (Builder $query): Builder => $query->where('classification', $this->classification),
            )
            ->when(
                ! in_array('dynamicFilter', $exceptFilters, true) && $this->dynamicFilter !== 'none',
                fn (Builder $query): Builder => $this->applyDynamicFilter($query, $this->dynamicFilter),
            )
            ->when(
                ! in_array('onlyProblems', $exceptFilters, true) && $this->onlyProblems,
                fn (Builder $query): Builder => $query->whereIn('status', self::PROBLEM_STATUSES),
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
                ! in_array('onlyNeedsKey', $exceptFilters, true) && $this->onlyNeedsKey,
                fn (Builder $query): Builder => $this->applyNeedsKeyUsageDecisionFilter($query),
            )
            ->when(
                ! in_array('namespaceFilter', $exceptFilters, true) && $this->namespaceFilter !== '',
                fn (Builder $query): Builder => $query->where('namespace', $this->namespaceFilter),
            )
            ->when(
                ! in_array('groupFilter', $exceptFilters, true) && $this->groupFilter !== '',
                fn (Builder $query): Builder => $query->where('group', $this->groupFilter),
            )
            ->when(
                ! in_array('search', $exceptFilters, true) && $this->search !== '',
                function (Builder $query): Builder {
                    $search = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $this->search).'%';

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

    private function applyStatusFilter(Builder $query, string $status): Builder
    {
        if ($status !== 'dynamic') {
            return $query->where('status', $status);
        }

        return $query->where(function (Builder $query): void {
            $query
                ->where('status', 'dynamic')
                ->orWhere('is_dynamic_multi', true);
        });
    }

    private function applyDynamicFilter(Builder $query, string $dynamicFilter): Builder
    {
        return match ($dynamicFilter) {
            'all' => $this->applyDynamicTranslationScope($query),
            'candidate' => $this->applyDynamicTranslationScope($query)
                ->where('is_dynamic_multi', false),
            'multi' => $query->where('is_dynamic_multi', true),
            'without_suggested_key' => $this->applyDynamicTranslationScope($query)
                ->where('is_dynamic_multi', false)
                ->where(function (Builder $query): void {
                    $query
                        ->whereNull('suggested_key')
                        ->orWhere('suggested_key', '');
                }),
            'reactivated_stale' => $this->applyDynamicTranslationScope($query)
                ->whereHas('auditEvents', function (Builder $query): void {
                    $query
                        ->where('entity_type', 'translation_key')
                        ->where('event_type', 'legacy_dynamic_stale_reactivated');
                }),
            default => $query,
        };
    }

    private function applyDynamicTranslationScope(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query
                ->where('classification', 'dynamic')
                ->orWhere('is_dynamic_multi', true);
        });
    }

    private function excludeObsoleteStatus(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query
                ->whereNull('status')
                ->orWhere('status', '!=', 'obsolete');
        });
    }

    /**
     * Apply the combined "needs key" follow-up filter.
     *
     * A row matches when either a Usage-Audit decision/usage row requires a key or the translation key
     * was manually marked as Needs-New-Key from the translation list.
     */
    private function applyNeedsKeyUsageDecisionFilter(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query
                ->whereIn(
                    'translation_keys.id',
                    $this->needsKeyUsageAuditTranslationKeyIdQuery(),
                )
                ->orWhere(function (Builder $query): void {
                    $query
                        ->whereNotNull('translation_keys.needs_new_key_marked_at')
                        ->whereNull('translation_keys.needs_new_key_resolved_at');
                });
        });
    }

    /**
     * Resolve the next key from the current paginated list context for history navigation.
     *
     * This intentionally avoids loading all matching translation key IDs into memory.
     */
    private function resolveNextHistoryTranslationKeyId(
        Builder $query,
        LengthAwarePaginator $paginator,
        ?int $selectedTranslationKeyId,
    ): ?int {
        if (! $selectedTranslationKeyId) {
            return null;
        }

        $currentPageIds = $paginator
            ->getCollection()
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->values();

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

        $nextPageFirstId = $nextPagePaginator
            ->getCollection()
            ->first()?->id;

        return is_numeric($nextPageFirstId) ? (int) $nextPageFirstId : null;
    }

    /**
     * Build translation query scoped to entries that can be opened in edit mode.
     */
    private function editableTranslationKeyQuery(): Builder
    {
        return $this->translationKeyQuery()
            ->whereNotNull('translation_keys.key')
            ->where('translation_keys.key', '!=', '');
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
        return $query->where(function (Builder $query): void {
            $query
                ->where('source', 'dynamic_audit')
                ->orWhere('is_dynamic_multi', true)
                ->orWhereHas('usages', function (Builder $query): void {
                    $query->where(function (Builder $query): void {
                        $query
                            ->whereNull('reason')
                            ->orWhere('reason', '!=', self::STALE_AUDIT_USAGE_REASON);
                    });
                });
        });
    }

    private function applyArchivedRelevanceScope(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $query): void {
                $query
                    ->where(function (Builder $query): void {
                        $query
                            ->whereNull('source')
                            ->orWhere('source', '!=', 'dynamic_audit');
                    })
                    ->where('is_dynamic_multi', false);
            })
            ->whereDoesntHave('usages', function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query
                        ->whereNull('reason')
                        ->orWhere('reason', '!=', self::STALE_AUDIT_USAGE_REASON);
                });
            });
    }

    /**
     * Build data for list, counters and modal partials.
     *
     * This method intentionally prepares all counters from scoped query builders instead of deriving
     * them from the current page collection. That keeps filter counters stable across pagination and
     * allows focus toggles such as Only Problems, Only Duplicates and Only Needs-Key to show their
     * own independent totals.
     */
    public function render(): View
    {
        $workflowCounterBaseQuery = $this->queryForRelevanceScope(false, ['workflowStatus', 'status', 'classification', 'dynamicFilter', 'onlyProblems', 'onlyBaseDuplicates', 'onlyNeedsKey']);

        $workflowCounterCounts = (clone $workflowCounterBaseQuery)
            ->selectRaw('workflow_status, count(*) as total')
            ->groupBy('workflow_status')
            ->pluck('total', 'workflow_status')
            ->map(fn ($value) => (int) $value)
            ->all();

        $workflowRelevantTotal = array_sum($workflowCounterCounts);

        $workflowOpenTotal = $workflowCounterCounts['open'] ?? 0;

        $workflowReviewedTotal = $workflowCounterCounts['reviewed'] ?? 0;

        $workflowHistoryTotal = (int) $this->filteredTranslationKeyQuery(['workflowStatus', 'status', 'classification', 'dynamicFilter', 'onlyProblems', 'onlyBaseDuplicates', 'onlyNeedsKey', 'relevanceScope'])
            ->where('workflow_status', 'reviewed')
            ->count();

        $workflowCompletedTotal = (clone $workflowCounterBaseQuery)
            ->where('status', 'ok')
            ->count();

        $statusCounts = $this->filteredTranslationKeyQuery(['status', 'obsoleteStatus'])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($value) => (int) $value)
            ->all();

        $dynamicMultiStatusRepairCount = (int) $this->filteredTranslationKeyQuery(['status'])
            ->where('is_dynamic_multi', true)
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('status')
                    ->orWhere('status', '!=', 'dynamic');
            })
            ->count();

        if ($dynamicMultiStatusRepairCount > 0) {
            $statusCounts['dynamic'] = ($statusCounts['dynamic'] ?? 0) + $dynamicMultiStatusRepairCount;
        }

        $classificationCounts = $this->filteredTranslationKeyQuery(['classification'])
            ->selectRaw('classification, count(*) as total')
            ->groupBy('classification')
            ->pluck('total', 'classification')
            ->mapWithKeys(fn ($value, $key): array => [(string) $key => (int) $value])
            ->all();

        $activeClassificationCounts = $this->queryForRelevanceScope(false, [
            'workflowStatus',
            'classification',
            'status',
            'dynamicFilter',
            'onlyProblems',
            'onlyBaseDuplicates',
            'onlyNeedsKey',
            'search',
            'namespaceFilter',
            'groupFilter',
        ])
            ->selectRaw('classification, count(*) as total')
            ->groupBy('classification')
            ->pluck('total', 'classification')
            ->mapWithKeys(fn ($value, $key): array => [(string) $key => (int) $value])
            ->all();

        $dynamicFilterBaseQuery = $this->queryForRelevanceScope(false, [
            'workflowStatus',
            'status',
            'classification',
            'dynamicFilter',
            'onlyProblems',
            'onlyBaseDuplicates',
            'onlyNeedsKey',
        ]);

        $dynamicAggregateCounts = $this->applyDynamicTranslationScope((clone $dynamicFilterBaseQuery))
            ->selectRaw('
                COUNT(*) as all_count,
                SUM(CASE WHEN is_dynamic_multi = false THEN 1 ELSE 0 END) as candidate_count,
                SUM(CASE WHEN is_dynamic_multi = true THEN 1 ELSE 0 END) as multi_count,
                SUM(CASE WHEN is_dynamic_multi = false AND (suggested_key IS NULL OR suggested_key = \'\') THEN 1 ELSE 0 END) as without_suggested_key_count
            ')
            ->first();

        $dynamicFilterCounts = [
            'none' => (int) (clone $dynamicFilterBaseQuery)->count(),
            'all' => (int) ($dynamicAggregateCounts?->all_count ?? 0),
            'candidate' => (int) ($dynamicAggregateCounts?->candidate_count ?? 0),
            'multi' => (int) ($dynamicAggregateCounts?->multi_count ?? 0),
            'without_suggested_key' => (int) ($dynamicAggregateCounts?->without_suggested_key_count ?? 0),
            'reactivated_stale' => (int) $this->applyDynamicFilter((clone $dynamicFilterBaseQuery), 'reactivated_stale')->count(),
        ];

        $total = array_sum($statusCounts);

        $activeTypeTotal = array_sum($activeClassificationCounts);

        $archiveCount = (int) $this->queryForRelevanceScope(true, ['workflowStatus', 'classification', 'status', 'dynamicFilter', 'onlyProblems', 'onlyBaseDuplicates', 'onlyNeedsKey'])
            ->count();

        $problemCount = (int) $this->filteredTranslationKeyQuery(['onlyProblems'])
            ->whereIn('status', self::PROBLEM_STATUSES)
            ->count();

        $duplicateCount = (int) $this->filteredTranslationKeyQuery(['onlyBaseDuplicates'])
            ->whereHas('values', function (Builder $query): void {
                $query->where('is_base_duplicate', true);
            })
            ->count();

        $needsKeyCount = (int) $this->applyNeedsKeyUsageDecisionFilter(
            $this->queryForRelevanceScope(false, [
                'workflowStatus',
                'status',
                'classification',
                'dynamicFilter',
                'onlyProblems',
                'onlyBaseDuplicates',
                'onlyNeedsKey',
                'search',
                'namespaceFilter',
                'groupFilter',
            ])
        )->count();

        $query = $this->translationKeyQuery();

        $translationKeys = (clone $query)->paginate($this->normalizedPerPage());

        $filteredTotal = $translationKeys->total();

        $nextReviewTranslationKeyId = $this->resolveNextReviewTranslationKeyId(
            query: $query,
            paginator: $translationKeys,
            selectedTranslationKeyId: $this->selectedTranslationKeyId,
        );

        $editQuery = $this->editableTranslationKeyQuery();

        $nextEditTranslationKeyId = $this->resolveNextTranslationKeyId(
            query: $editQuery,
            selectedTranslationKeyId: $this->editingTranslationKeyId,
        );

        $nextHistoryTranslationKeyId = $this->resolveNextHistoryTranslationKeyId(
            query: $query,
            paginator: $translationKeys,
            selectedTranslationKeyId: $this->selectedHistoryTranslationKeyId,
        );

        $selectedTranslationKey = $this->selectedTranslationKeyId
            ? TranslationKey::query()
                ->select('translation_keys.*')
                ->with([
                    'values' => fn ($query) => $query->orderBy('locale'),
                    'usages' => fn ($query) => $query->orderBy('file')->orderBy('id'),
                ])
                ->selectSub(
                    $this->needsKeyUsageAuditFollowUpCountQuery(),
                    'needs_key_usage_audit_follow_up_count',
                )
                ->find($this->selectedTranslationKeyId)
            : null;

        $editingTranslationKey = $this->editingTranslationKeyId
            ? TranslationKey::query()
                ->with([
                    'values' => fn ($query) => $query->orderBy('locale', 'asc'),
                    'usages' => fn ($query) => $query->orderBy('file', 'asc')->orderBy('id', 'asc'),
                ])
                ->find($this->editingTranslationKeyId)
            : null;

        $historyTranslationKey = $this->selectedHistoryTranslationKeyId
            ? TranslationKey::query()
                ->with([
                    'usages' => fn ($query) => $query->orderBy('file')->orderBy('line')->orderBy('id'),
                ])
                ->find($this->selectedHistoryTranslationKeyId)
            : null;

        $historyEventTotal = $this->selectedHistoryTranslationKeyId
            ? DB::table('translation_audit_events')
                ->where('translation_key_id', $this->selectedHistoryTranslationKeyId)
                ->count()
            : 0;

        $historyDiscoveredEvent = $this->selectedHistoryTranslationKeyId
            ? DB::table('translation_audit_events')
                ->where('translation_key_id', $this->selectedHistoryTranslationKeyId)
                ->where('event_type', 'discovered')
                ->orderBy('created_at')
                ->first(['context'])
            : null;

        $historyHasDiscoveredEvent = $historyDiscoveredEvent !== null;
        $historyDiscoveredContext = $historyDiscoveredEvent?->context
            ? json_decode($historyDiscoveredEvent->context, true)
            : [];
        $historyHasBackfilledBaseline = is_array($historyDiscoveredContext)
            && ($historyDiscoveredContext['backfilled'] ?? false) === true;

        $historyEvents = $this->selectedHistoryTranslationKeyId
            ? DB::table('translation_audit_events')
                ->where('translation_key_id', $this->selectedHistoryTranslationKeyId)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit($this->historyEventLimit)
                ->get()
            : collect();

        $historyHasMoreEvents = $historyEvents->count() < $historyEventTotal;

        $translationLanguages = $this->resolveTranslationEditLanguages();

        $targetLanguages = $this->resolveTargetLanguagesFromAppSettings();
        $activeTargetSubLanguages = $this->resolveActiveSubLanguagesForCurrentTargetFilter();

        $targetLocales = $targetLanguages->pluck('locale')->all();

        $translationCoverage = DB::table('translation_values')
            ->selectRaw('locale, COUNT(*) as row_count, COUNT(CASE WHEN value IS NOT NULL AND value <> \'\' THEN 1 END) as translated_count')
            ->whereIn('locale', $targetLocales)
            ->groupBy('locale')
            ->get()
            ->mapWithKeys(fn (object $row): array => [$row->locale => $row]);

        $selectedMainLanguageFileStats = $this->resolveSelectedMainLanguageFileStats($translationCoverage);
        $fileObsoleteEntryCount = $this->resolveFileObsoleteEntryCount();

        $locales = $translationLanguages
            ->pluck('locale')
            ->all();

        $translationNamespaces = $this->filteredTranslationKeyQuery(['namespaceFilter', 'groupFilter'])
            ->whereNotNull('namespace')
            ->distinct()
            ->orderBy('namespace', 'asc')
            ->pluck('namespace')
            ->filter(static fn (mixed $namespace): bool => is_string($namespace) && trim($namespace) !== '')
            ->values()
            ->all();

        $translationGroups = $this->filteredTranslationKeyQuery(['groupFilter'])
            ->whereNotNull('group')
            ->distinct()
            ->orderBy('group', 'asc')
            ->pluck('group')
            ->filter(static fn (mixed $group): bool => is_string($group) && trim($group) !== '')
            ->values()
            ->all();

        return view('components.admin.⚡translation-list', [
            'translationKeys' => $translationKeys,
            'workflowRelevantTotal' => $workflowRelevantTotal,
            'workflowOpenTotal' => $workflowOpenTotal,
            'workflowReviewedTotal' => $workflowReviewedTotal,
            'workflowHistoryTotal' => $workflowHistoryTotal,
            'workflowCompletedTotal' => $workflowCompletedTotal,
            'statusCounts' => $statusCounts,
            'classificationCounts' => $classificationCounts,
            'activeClassificationCounts' => $activeClassificationCounts,
            'dynamicFilterCounts' => $dynamicFilterCounts,
            'total' => $total,
            'activeTypeTotal' => $activeTypeTotal,
            'archiveCount' => $archiveCount,
            'filteredTotal' => $filteredTotal,
            'problemStatuses' => self::PROBLEM_STATUSES,
            'problemCount' => $problemCount,
            'duplicateCount' => $duplicateCount,
            'needsKeyCount' => $needsKeyCount,
            'hasActiveFilters' => $this->hasActiveFilters(),
            'nextReviewTranslationKeyId' => $nextReviewTranslationKeyId,
            'nextEditTranslationKeyId' => $nextEditTranslationKeyId,
            'nextHistoryTranslationKeyId' => $nextHistoryTranslationKeyId,
            'locales' => $locales,
            'translationLanguages' => $translationLanguages,
            'targetLanguages' => $targetLanguages,
            'activeTargetSubLanguages' => $activeTargetSubLanguages,
            'translationCoverage' => $translationCoverage,
            'selectedMainLanguageFileStats' => $selectedMainLanguageFileStats,
            'fileObsoleteEntryCount' => $fileObsoleteEntryCount,
            'translationNamespaces' => $translationNamespaces,
            'translationGroups' => $translationGroups,
            'selectedTranslationKey' => $selectedTranslationKey,
            'editingTranslationKey' => $editingTranslationKey,
            'historyTranslationKey' => $historyTranslationKey,
            'historyEvents' => $historyEvents,
            'historyEventTotal' => $historyEventTotal,
            'historyHasDiscoveredEvent' => $historyHasDiscoveredEvent,
            'historyHasBackfilledBaseline' => $historyHasBackfilledBaseline,
            'historyHasMoreEvents' => $historyHasMoreEvents,
        ]);
    }

    /**
     * Resolve app-target languages from app settings (app_general.availableLocales).
     *
     * These locales represent the currently configured application output languages and are merged
     * into the edit/review context even when they are not part of the translation-language
     * maintenance table yet.
     */
    private function resolveTargetLanguagesFromAppSettings()
    {
        $appGeneralSettings = app(AppGeneralSettings::class);

        $availableLocales = collect($appGeneralSettings->availableLocales ?? [])
            ->map(static fn (mixed $locale): string => is_string($locale) ? LocaleCode::normalize($locale) : '')
            ->filter(static fn (string $locale): bool => $locale !== '')
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
            ->map(static function (string $locale, int $index) use ($languageByCode): object {
                $languageRow = $languageByCode->get($locale);
                $fallbackLabel = strtoupper($locale);

                return (object) [
                    'locale' => $locale,
                    'name' => (string) ($languageRow->name ?? $fallbackLabel),
                    'native_name' => (string) ($languageRow->native_name ?? $fallbackLabel),
                    'is_default' => false,
                    'is_enabled_for_app' => true,
                    'sort_order' => $index,
                ];
            })
            ->values();
    }

    /**
     * Resolve active sub-languages for the currently selected target base locale.
     */
    private function resolveActiveSubLanguagesForCurrentTargetFilter()
    {
        $selectedLocale = LocaleCode::normalize((string) $this->languageFilter);

        if ($selectedLocale === '') {
            return collect();
        }

        $baseLocale = LocaleCode::normalize((string) (LocaleCode::parts($selectedLocale)['language'] ?? ''));

        if ($baseLocale === '') {
            return collect();
        }

        $languageId = DB::table('locales')
            ->whereRaw('LOWER(code) = ?', [strtolower($baseLocale)])
            ->value('language_id');

        if (! is_int($languageId) && ! is_numeric($languageId)) {
            return collect();
        }

        return DB::table('locales')
            ->where('language_id', (int) $languageId)
            ->where('is_active', true)
            ->whereRaw('LOWER(code) <> ?', [strtolower($baseLocale)])
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get([
                'code',
                'display_name',
                'native_display_name',
            ])
            ->map(static function (object $row): ?object {
                $locale = LocaleCode::normalize((string) ($row->code ?? ''));

                if ($locale === '') {
                    return null;
                }

                $fallbackLabel = strtoupper($locale);

                return (object) [
                    'locale' => $locale,
                    'name' => (string) ($row->display_name ?? $fallbackLabel),
                    'native_name' => (string) ($row->native_display_name ?? $fallbackLabel),
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * Translation locales available in the edit modal for the current work context.
     */
    private function resolveTranslationEditLanguages(): Collection
    {
        $enabledTranslationLanguages = TranslationLanguage::query()
            ->where('is_enabled_for_translation', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('locale', 'asc')
            ->get([
                'locale',
                'name',
                'native_name',
                'is_default',
                'is_enabled_for_app',
                'sort_order',
            ])
            ->map(static function (TranslationLanguage $translationLanguage): object {
                $locale = LocaleCode::normalize((string) ($translationLanguage->locale ?? ''));

                return (object) [
                    'locale' => $locale,
                    'name' => (string) ($translationLanguage->name ?? strtoupper($locale)),
                    'native_name' => (string) ($translationLanguage->native_name ?? strtoupper($locale)),
                    'is_default' => (bool) ($translationLanguage->is_default ?? false),
                    'is_enabled_for_app' => (bool) ($translationLanguage->is_enabled_for_app ?? false),
                    'sort_order' => (int) ($translationLanguage->sort_order ?? 0),
                ];
            })
            ->filter(static fn (object $translationLanguage): bool => $translationLanguage->locale !== '')
            ->values();

        $targetLanguages = $this->resolveTargetLanguagesFromAppSettings()
            ->map(static function (object $translationLanguage): object {
                return (object) [
                    'locale' => LocaleCode::normalize((string) ($translationLanguage->locale ?? '')),
                    'name' => (string) ($translationLanguage->name ?? ''),
                    'native_name' => (string) ($translationLanguage->native_name ?? ''),
                    'is_default' => (bool) ($translationLanguage->is_default ?? false),
                    'is_enabled_for_app' => (bool) ($translationLanguage->is_enabled_for_app ?? true),
                    'sort_order' => (int) ($translationLanguage->sort_order ?? 0),
                ];
            })
            ->filter(static fn (object $translationLanguage): bool => $translationLanguage->locale !== '')
            ->values();

        $activeSubLanguages = $this->resolveActiveSubLanguagesForCurrentTargetFilter()
            ->map(static function (object $translationLanguage): object {
                $locale = LocaleCode::normalize((string) ($translationLanguage->locale ?? ''));

                return (object) [
                    'locale' => $locale,
                    'name' => (string) ($translationLanguage->name ?? strtoupper($locale)),
                    'native_name' => (string) ($translationLanguage->native_name ?? strtoupper($locale)),
                    'is_default' => false,
                    'is_enabled_for_app' => true,
                    'sort_order' => 1000,
                ];
            })
            ->filter(static fn (object $translationLanguage): bool => $translationLanguage->locale !== '')
            ->values();

        $selectedLocale = LocaleCode::normalize((string) $this->languageFilter);
        $selectedMainLocale = LocaleCode::normalize((string) (LocaleCode::parts($selectedLocale)['language'] ?? ''));

        $allKnownLanguages = $enabledTranslationLanguages
            ->concat($targetLanguages)
            ->concat($activeSubLanguages)
            ->mapWithKeys(static fn (object $translationLanguage): array => [
                $translationLanguage->locale => $translationLanguage,
            ]);

        return collect(['en', $selectedMainLocale, $selectedLocale])
            ->merge($enabledTranslationLanguages->pluck('locale'))
            ->merge($targetLanguages->pluck('locale'))
            ->merge($activeSubLanguages->pluck('locale'))
            ->map(static fn (mixed $locale): string => LocaleCode::normalize((string) $locale))
            ->filter()
            ->unique()
            ->map(static function (string $locale) use ($allKnownLanguages): object {
                $translationLanguage = $allKnownLanguages->get($locale);
                $fallbackLabel = strtoupper($locale);

                return (object) [
                    'locale' => $locale,
                    'name' => (string) ($translationLanguage->name ?? $fallbackLabel),
                    'native_name' => (string) ($translationLanguage->native_name ?? $fallbackLabel),
                    'is_default' => (bool) ($translationLanguage->is_default ?? false),
                    'is_enabled_for_app' => (bool) ($translationLanguage->is_enabled_for_app ?? true),
                    'sort_order' => (int) ($translationLanguage->sort_order ?? 1000),
                ];
            })
            ->values();
    }

    /**
     * File and entry counts for the currently selected main language in lang/*.
     */
    private function resolveSelectedMainLanguageFileStats(Collection $translationCoverage): ?object
    {
        $selectedLocale = LocaleCode::normalize((string) $this->languageFilter);

        if ($selectedLocale === '') {
            return null;
        }

        $mainLocale = LocaleCode::normalize((string) (LocaleCode::parts($selectedLocale)['language'] ?? ''));

        if ($mainLocale === '') {
            return null;
        }

        $fileStats = $this->countLangFilesAndEntriesForLocale($mainLocale);
        $dbEntryCount = $this->resolveExportableTranslationEntryCountForLocale($mainLocale, $translationCoverage);

        return (object) [
            'locale' => $mainLocale,
            'file_count' => $fileStats['file_count'],
            'entry_count' => $fileStats['entry_count'],
            'db_entry_count' => $dbEntryCount,
            'in_sync' => $fileStats['entry_count'] === $dbEntryCount,
        ];
    }

    private function resolveExportableTranslationEntryCountForLocale(string $locale, Collection $translationCoverage): int
    {
        $coverageRow = $translationCoverage->get($locale);

        return (int) ($coverageRow->translated_count ?? 0);
    }

    private function resolveFileObsoleteEntryCount(): int
    {
        $path = storage_path('audits/translations/compare/summary.json');

        if (! File::isFile($path)) {
            return 0;
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            return 0;
        }

        return (int) ($payload['file_obsolete_entries'] ?? 0);
    }

    /**
     * @return array{file_count:int, entry_count:int}
     */
    private function countLangFilesAndEntriesForLocale(string $locale): array
    {
        $normalizedLocale = LocaleCode::normalize($locale);

        if ($normalizedLocale === '') {
            return ['file_count' => 0, 'entry_count' => 0];
        }

        $fileCount = 0;
        $entryCount = 0;
        $phpDirectory = lang_path($normalizedLocale);

        if (File::isDirectory($phpDirectory)) {
            foreach (File::allFiles($phpDirectory) as $file) {
                $path = $file->getRealPath();

                if (! is_string($path) || ! File::isFile($path) || pathinfo($path, PATHINFO_EXTENSION) !== 'php') {
                    continue;
                }

                if ($this->isParkedLangFile($path)) {
                    continue;
                }

                $payload = require $path;

                if (! is_array($payload)) {
                    continue;
                }

                $fileCount++;
                $entryCount += count($this->flattenLangPayload($payload));
            }
        }

        $jsonPath = lang_path($normalizedLocale.'.json');

        if (File::isFile($jsonPath) && ! $this->isParkedLangFile($jsonPath)) {
            $payload = json_decode(File::get($jsonPath), true);

            if (is_array($payload)) {
                $fileCount++;
                $entryCount += count($payload);
            }
        }

        return [
            'file_count' => $fileCount,
            'entry_count' => $entryCount,
        ];
    }

    private function isParkedLangFile(string $path): bool
    {
        $filename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        return str_contains($filename, 'xxx')
            || str_contains($filename, 'yyy')
            || str_contains($filename, 'zzz');
    }

    /**
     * @return array<string, mixed>
     */
    private function flattenLangPayload(array $items, string $prefix = ''): array
    {
        $result = [];

        foreach ($items as $key => $value) {
            $segment = (string) $key;
            $fullKey = $prefix !== '' ? $prefix.'.'.$segment : $segment;

            if (is_array($value)) {
                $result += $this->flattenLangPayload($value, $fullKey);

                continue;
            }

            $result[$fullKey] = $value;
        }

        return $result;
    }

    /**
     * Preselect the filtered sub-language in edit mode when the target filter already points to one.
     */
    private function defaultTranslationEditSubLanguageLocales(): array
    {
        $selectedLocale = LocaleCode::normalize((string) $this->languageFilter);

        if ($selectedLocale === '') {
            return [];
        }

        $activeSubLocales = $this->resolveActiveSubLanguagesForCurrentTargetFilter()
            ->pluck('locale')
            ->map(static fn (mixed $item): string => LocaleCode::normalize((string) $item))
            ->filter()
            ->values()
            ->all();

        return in_array($selectedLocale, $activeSubLocales, true) ? [$selectedLocale] : [];
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

    private function normalizeDynamicFilter(mixed $value): string
    {
        $dynamicFilter = trim((string) $value);

        return in_array($dynamicFilter, $this->dynamicFilterOptions, true) ? $dynamicFilter : 'none';
    }

    private function persistUiState(): void
    {
        $this->setUserSetting(self::UI_STATE_SETTING_KEY, [
            'search' => $this->search,
            'status' => $this->status,
            'workflowStatus' => $this->workflowStatus,
            'classification' => $this->classification,
            'dynamicFilter' => $this->dynamicFilter,
            'showArchived' => $this->showArchived,
            'onlyProblems' => $this->onlyProblems,
            'onlyBaseDuplicates' => $this->onlyBaseDuplicates,
            'languageFilter' => $this->languageFilter,
            'namespaceFilter' => $this->namespaceFilter,
            'groupFilter' => $this->groupFilter,
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

    /**
     * Resolve the next key ID from the full current filtered/sorted query context.
     */
    private function resolveNextTranslationKeyId(
        Builder $query,
        ?int $selectedTranslationKeyId,
    ): ?int {
        if (! $selectedTranslationKeyId) {
            return null;
        }

        $ids = (clone $query)
            ->pluck('translation_keys.id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->values();

        $currentIndex = $ids->search($selectedTranslationKeyId);

        if ($currentIndex === false) {
            return null;
        }

        $nextId = $ids->get((int) $currentIndex + 1);

        return is_numeric($nextId) ? (int) $nextId : null;
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
            'context' => json_encode($this->translationAuditContextWithUsageSnapshot($translationKey->id, [
                'locale' => $locale,
                'source' => 'translation_edit_modal',
            ]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
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
            'context' => json_encode(
                $this->translationAuditContextWithUsageSnapshot($translationKey->id, $context),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            ),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Insert an audit event for manual Needs-New-Key marker changes on translation keys.
     */
    private function createTranslationManualNeedsNewKeyAuditEvent(
        TranslationKey $translationKey,
        bool $wasActive,
        bool $isActive,
        string $reason,
        array $context = [],
    ): void {
        DB::table('translation_audit_events')->insert([
            'translation_key_id' => $translationKey->id,
            'translation_usage_id' => null,
            'entity_type' => 'translation_key',
            'event_type' => 'manual_needs_new_key_changed',
            'old_fingerprint' => $translationKey->fingerprint,
            'new_fingerprint' => $translationKey->fingerprint,
            'old_file' => null,
            'new_file' => null,
            'old_line' => null,
            'new_line' => null,
            'old_key' => $translationKey->key,
            'new_key' => $translationKey->key,
            'old_value' => $wasActive ? 'active' : 'inactive',
            'new_value' => $isActive ? 'active' : 'inactive',
            'old_status' => $translationKey->status,
            'new_status' => $translationKey->status,
            'reason' => $reason,
            'context' => json_encode($this->translationAuditContextWithUsageSnapshot($translationKey->id, [
                ...$context,
                'manual_needs_new_key_old_active' => $wasActive,
                'manual_needs_new_key_new_active' => $isActive,
                'needs_new_key_note' => $translationKey->needs_new_key_note,
            ]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
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
            'context' => json_encode($this->translationAuditContextWithUsageSnapshot($translationKey->id, [
                ...$context,
                'old_workflow_status' => $oldWorkflowStatus,
                'new_workflow_status' => $newWorkflowStatus,
                'reviewed_by_user_id' => $translationKey->reviewed_by_user_id,
                'reviewed_at' => $translationKey->reviewed_at?->toDateTimeString(),
            ]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Attach the current usage rows as an immutable audit-event snapshot.
     */
    private function translationAuditContextWithUsageSnapshot(int $translationKeyId, array $context): array
    {
        $context['affected_usages'] = TranslationUsage::query()
            ->where('translation_key_id', $translationKeyId)
            ->orderBy('file')
            ->orderBy('line')
            ->orderBy('id')
            ->get(['id', 'file', 'line', 'function', 'classification', 'raw', 'original_raw'])
            ->map(static fn (TranslationUsage $usage): array => [
                'id' => $usage->id,
                'file' => $usage->file,
                'line' => $usage->line,
                'function' => $usage->function,
                'classification' => $usage->classification,
                'raw' => $usage->raw,
                'original_raw' => $usage->original_raw,
            ])
            ->all();
        $context['affected_usages_snapshot_complete'] = true;

        return $context;
    }
}
