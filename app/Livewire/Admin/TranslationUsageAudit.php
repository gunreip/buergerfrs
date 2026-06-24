<?php

// app/Livewire/Admin/TranslationUsageAudit.php

namespace App\Livewire\Admin;

use App\Models\TranslationKey;
use App\Models\TranslationUsageAuditDecision;
use App\Support\Audit\TranslationActivity;
use App\Support\Locale\LocaleCode;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

/**
 * Livewire administration component for reviewing repeated translation usage literals.
 *
 * The component reads generated duplicate and frequent usage audit reports, renders the
 * in-memory candidate lists with filters, sorting and pagination, and provides review/edit
 * modals for deciding how repeated source-language literals should be handled.
 *
 * Usage decisions are stored in translation_usage_audit_decisions and linked usage rows. The
 * component itself does not rewrite source files. Code changes are delegated to the dedicated
 * preview/apply console commands and the project translation pipeline.
 *
 * Main responsibilities:
 * - load duplicate and frequent usage literal reports from storage/audits/translations;
 * - filter, sort and paginate audit candidates without modifying the source reports;
 * - display current keys, values, UI-key candidates and usage locations for review;
 * - persist workflow decisions such as unify, skip and needs-key follow-ups;
 * - prepare usage-row metadata for later command-based preview/apply processing.
 */
class TranslationUsageAudit extends Component
{
    use WithoutUrlPagination;
    use WithPagination;

    /**
     * Active audit source tab.
     *
     * Supported values are duplicate and frequent. The active tab determines which generated JSON
     * report is loaded and which audit_type is used for persisted usage decisions.
     */
    public string $activeTab = 'duplicate';

    /**
     * Free-text search over source value, normalized value, suggested UI key and existing UI keys.
     */
    public string $search = '';

    /**
     * Focus the list on candidates without an existing UI-key candidate.
     */
    public bool $onlyWithoutUiCandidate = false;

    /**
     * Focus the list on candidates that still contain stale usage locations from earlier audits.
     */
    public bool $onlyWithStaleUsages = false;

    /**
     * Decision-state filter for the usage-audit workflow table.
     *
     * This filter is backed by persisted TranslationUsageAuditDecision rows and is independent from
     * the generated JSON source reports.
     */
    public string $decisionFilter = 'all';

    /**
     * Minimum number of current, non-stale usage locations required for a candidate to remain visible.
     */
    public int $minCurrentUsages = 0;

    public int $perPage = 25;

    /**
     * Current table sort field.
     */
    public string $sortField = 'usage_count_current';

    /**
     * Current table sort direction.
     */
    public string $sortDirection = 'desc';

    /**
     * Selected audit type for the currently open review/edit modal.
     */
    public ?string $selectedAuditType = null;

    /**
     * MD5 hash of the selected candidate's normalized source value.
     *
     * The hash is used as a stable bridge between generated JSON report rows and persisted decision
     * rows in the database.
     */
    public ?string $selectedNormalizedValue = null;

    public bool $usageAuditModalOpen = false;

    public bool $usageAuditEditModalOpen = false;

    public ?int $editDecisionId = null;

    public string $editDecisionAction = 'undecided';

    public string $editDecisionStatus = 'draft';

    public string $editTargetTranslationKey = '';

    /**
     * Existing UI translation keys that may be used as valid unify targets.
     *
     * Suggested UI keys are intentionally not included here until they exist as real keys.
     *
     * @var array<int, string>
     */
    public array $editTargetTranslationKeyOptions = [];

    public string $editReviewNote = '';

    public bool $createTranslationKeyPanelOpen = false;

    public string $createTranslationKeyInput = '';

    /**
     * Editable usage rows displayed in the decision modal.
     *
     * Each row mirrors one concrete usage location and stores the future apply intent for that
     * location.
     *
     * @var array<int, array<string, mixed>>
     */
    public array $editUsageRows = [];

    public function setActiveTab(string $tab): void
    {
        if (! in_array($tab, ['duplicate', 'frequent', 'manual_needs_key'], true)) {
            $tab = 'duplicate';
        }

        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedOnlyWithoutUiCandidate(): void
    {
        $this->resetPage();
    }

    public function updatedOnlyWithStaleUsages(): void
    {
        $this->resetPage();
    }

    public function updatedDecisionFilter(): void
    {
        if (! in_array($this->decisionFilter, ['all', 'new', 'saved', 'draft', 'ready', 'applied', 'needs_key', 'skipped'], true)) {
            $this->decisionFilter = 'all';
        }

        $this->resetPage();
    }

    /**
     * Toggle UI-candidate filter.
     */
    public function toggleOnlyWithoutUiCandidate(): void
    {
        $this->onlyWithoutUiCandidate = ! $this->onlyWithoutUiCandidate;

        $this->resetPage();
    }

    /**
     * Toggle stale-usage filter.
     */
    public function toggleOnlyWithStaleUsages(): void
    {
        $this->onlyWithStaleUsages = ! $this->onlyWithStaleUsages;

        $this->resetPage();
    }

    public function updatedMinCurrentUsages(): void
    {
        $this->minCurrentUsages = max(0, (int) $this->minCurrentUsages);
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = in_array((int) $this->perPage, [10, 25, 50, 100], true)
            ? (int) $this->perPage
            : 25;

        $this->resetPage();
    }

    /**
     * Sort the usage audit table by the selected field.
     */
    public function sortBy(string $field): void
    {
        $field = $this->normalizeSortField($field);

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = in_array($field, ['value', 'suggested_ui_key', 'decision'], true) ? 'asc' : 'desc';
        }

        $this->resetPage();
    }

    public function openUsageAuditModal(string $auditType, string $normalizedValueHash): void
    {
        if (! in_array($auditType, ['duplicate', 'frequent', 'manual_needs_key'], true)) {
            return;
        }

        if (trim($normalizedValueHash) === '') {
            return;
        }

        $this->selectedAuditType = $auditType;
        $this->selectedNormalizedValue = $normalizedValueHash;
        $this->usageAuditModalOpen = true;
    }

    public function closeUsageAuditModal(): void
    {
        $this->usageAuditModalOpen = false;
        $this->selectedAuditType = null;
        $this->selectedNormalizedValue = null;
    }

    public function openUsageAuditEditModal(string $auditType, string $normalizedValueHash): void
    {
        if (! in_array($auditType, ['duplicate', 'frequent', 'manual_needs_key'], true)) {
            return;
        }

        if (trim($normalizedValueHash) === '') {
            return;
        }

        $selectedItem = $this->selectedUsageAuditItemFor($auditType, $normalizedValueHash);

        if (! $selectedItem) {
            return;
        }

        $this->selectedAuditType = $auditType;
        $this->selectedNormalizedValue = $normalizedValueHash;

        $this->fillUsageAuditEditForm($auditType, $normalizedValueHash, $selectedItem);

        $this->usageAuditEditModalOpen = true;
    }

    /**
     * Close the decision edit modal and clear all temporary edit state.
     *
     * Persisted decisions remain untouched. The next modal opening rebuilds the edit form from the
     * current generated audit item plus the stored decision, if one exists.
     */
    public function closeUsageAuditEditModal(): void
    {
        $this->usageAuditEditModalOpen = false;
        $this->resetUsageAuditEditForm();
    }

    public function openCreateTranslationKeyPanel(): void
    {
        $this->resetErrorBag('createTranslationKeyInput');

        $selectedItem = $this->selectedAuditType && $this->selectedNormalizedValue
            ? $this->selectedUsageAuditItemFor($this->selectedAuditType, $this->selectedNormalizedValue)
            : null;

        $suggestedTranslationKey = trim((string) ($selectedItem['suggested_ui_key'] ?? ''));

        $this->createTranslationKeyInput = $suggestedTranslationKey;
        $this->createTranslationKeyPanelOpen = true;
    }

    public function closeCreateTranslationKeyPanel(): void
    {
        $this->createTranslationKeyPanelOpen = false;
        $this->createTranslationKeyInput = '';

        $this->resetErrorBag('createTranslationKeyInput');
    }

    public function createTranslationKeyFromUsageAudit(TranslationActivity $translationActivity): void
    {
        if (! $this->selectedAuditType || ! $this->selectedNormalizedValue) {
            return;
        }

        $selectedItem = $this->selectedUsageAuditItemFor($this->selectedAuditType, $this->selectedNormalizedValue);

        if (! $selectedItem) {
            return;
        }

        $this->createTranslationKeyInput = trim($this->createTranslationKeyInput);

        $this->validate(
            [
                'createTranslationKeyInput' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[A-Za-z0-9_.-]+$/',
                    Rule::unique('translation_keys', 'key'),
                ],
            ],
            [
                'createTranslationKeyInput.regex' => __('Use only letters, numbers, dots, underscores and hyphens.'),
            ],
        );

        $newTranslationKeyValue = $this->createTranslationKeyInput;

        $translationKey = DB::transaction(function () use ($newTranslationKeyValue, $selectedItem): TranslationKey {
            return TranslationKey::query()->create([
                'fingerprint' => 'usage-audit-created-'.md5($newTranslationKeyValue),
                'key' => $newTranslationKeyValue,
                'namespace' => $this->namespaceFromTranslationKey($newTranslationKeyValue),
                'group' => $this->groupFromTranslationKey($newTranslationKeyValue),
                'status' => 'missing',
                'workflow_status' => 'open',
                'classification' => 'key',
                'source' => 'usage_audit_create_new_key',
                'suggested_key' => trim((string) ($selectedItem['suggested_ui_key'] ?? '')) ?: null,
                'native_text' => trim((string) ($selectedItem['value'] ?? '')) ?: null,
                'first_seen_at' => now(),
                'last_seen_at' => now(),
            ]);
        });

        $translationActivity->record(
            event: 'translations.admin.usage_audit.translation_key_created',
            description: __('Translation key created from usage audit'),
            subject: $translationKey,
            after: [
                'key' => $translationKey->key,
                'status' => $translationKey->status,
                'workflow_status' => $translationKey->workflow_status,
            ],
            properties: [
                'audit_type' => $this->selectedAuditType,
                'normalized_value_hash' => $this->selectedNormalizedValue,
            ],
        );

        $this->editTargetTranslationKeyOptions = collect($this->editTargetTranslationKeyOptions)
            ->push((string) $translationKey->key)
            ->map(static fn (mixed $targetTranslationKey): string => trim((string) $targetTranslationKey))
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        $this->editTargetTranslationKey = (string) $translationKey->key;
        $this->editDecisionAction = 'unify_to_target_key';

        $this->updatedEditTargetTranslationKey();

        $this->editDecisionStatus = $this->computedUsageAuditDecisionStatus();
        $this->createTranslationKeyPanelOpen = false;
        $this->createTranslationKeyInput = '';

        Flux::toast(
            heading: __('Translation key created'),
            text: __('The new translation key has been created and selected as target key.'),
            variant: 'success',
            duration: 5000,
        );
    }

    /**
     * Persist the current usage-audit decision and its per-usage change rows.
     *
     * The method validates the selected workflow action, computes the stored decision status, writes
     * a snapshot of the current generated candidate, and replaces the linked usage-decision rows.
     * It does not modify source files; later console commands consume the saved rows for preview and
     * apply steps.
     */
    public function saveUsageAuditDecision(TranslationActivity $translationActivity): void
    {
        if (! $this->selectedAuditType || ! $this->selectedNormalizedValue) {
            return;
        }

        $selectedItem = $this->selectedUsageAuditItemFor($this->selectedAuditType, $this->selectedNormalizedValue);

        if (! $selectedItem) {
            return;
        }

        $this->validate([
            'editDecisionAction' => ['required', 'string', 'in:undecided,unify_to_target_key,skip,create_new_key'],
            'editTargetTranslationKey' => ['nullable', 'string', 'max:255'],
            'editTargetTranslationKeyOptions' => ['array'],
            'editReviewNote' => ['nullable', 'string'],
            'editUsageRows' => ['array'],
            'editUsageRows.*.include_in_change' => ['boolean'],
            'editUsageRows.*.change_status' => ['required', 'string', 'in:pending,ready,skipped,applied,already_target,needs_key'],
        ]);

        if (
            $this->editDecisionAction === 'unify_to_target_key' &&
            ! $this->hasUsageAuditTargetTranslationKeyOptions()
        ) {
            $this->addError('editDecisionAction', __('Unify is not available because no existing target translation key is available.'));

            return;
        }

        if (
            $this->editDecisionAction === 'unify_to_target_key' &&
            trim($this->editTargetTranslationKey) === ''
        ) {
            $this->addError('editTargetTranslationKey', __('Please select a target translation key.'));

            return;
        }

        if (! $this->canSaveUsageAuditDecision()) {
            $this->addError('editDecisionAction', __('Please select a valid decision before saving.'));

            return;
        }

        $this->editDecisionStatus = $this->computedUsageAuditDecisionStatus();

        $existingDecision = TranslationUsageAuditDecision::query()
            ->where('audit_type', $this->selectedAuditType)
            ->where('normalized_value_hash', $this->selectedNormalizedValue)
            ->first();
        $before = $existingDecision === null ? [] : [
            'decision_action' => $existingDecision->decision_action,
            'decision_status' => $existingDecision->decision_status,
            'target_translation_key' => $existingDecision->target_translation_key,
        ];

        $decision = DB::transaction(function () use ($selectedItem): TranslationUsageAuditDecision {
            $decision = TranslationUsageAuditDecision::query()->updateOrCreate(
                [
                    'audit_type' => $this->selectedAuditType,
                    'normalized_value_hash' => $this->selectedNormalizedValue,
                ],
                [
                    'normalized_value' => $selectedItem['normalized_value'] ?? null,
                    'source_locale' => $selectedItem['locale'] ?? 'en',
                    'source_value' => $selectedItem['value'] ?? null,
                    'suggested_translation_key' => $selectedItem['suggested_ui_key'] ?? null,
                    'target_translation_key' => $this->editDecisionAction === 'unify_to_target_key' && trim($this->editTargetTranslationKey) !== ''
                        ? trim($this->editTargetTranslationKey)
                        : null,
                    'decision_action' => $this->editDecisionAction,
                    'decision_status' => $this->computedUsageAuditDecisionStatus(),
                    'review_note' => trim($this->editReviewNote) !== ''
                        ? trim($this->editReviewNote)
                        : null,
                    'snapshot' => $selectedItem,
                    'reviewed_by_user_id' => auth()->id(),
                    'reviewed_at' => now(),
                ],
            );

            $decision->usages()->delete();

            foreach ($this->editUsageRows as $usageRow) {
                $includeInChange = $this->editDecisionAction === 'unify_to_target_key'
                    && (bool) ($usageRow['include_in_change'] ?? false);

                $decision->usages()->create([
                    'translation_key_id' => $usageRow['translation_key_id'] ?? null,
                    'current_translation_key' => $usageRow['current_translation_key'] ?? null,
                    'target_translation_key' => $this->editDecisionAction === 'unify_to_target_key' && trim((string) ($usageRow['target_translation_key'] ?? '')) !== ''
                        ? trim((string) ($usageRow['target_translation_key'] ?? ''))
                        : null,
                    'file' => $usageRow['file'] ?? null,
                    'line' => $usageRow['line'] ?? null,
                    'detected_function' => $usageRow['detected_function'] ?? null,
                    'classification' => $usageRow['classification'] ?? null,
                    'reason' => $usageRow['reason'] ?? null,
                    'is_stale' => (bool) ($usageRow['is_stale'] ?? false),
                    'raw' => $usageRow['raw'] ?? null,
                    'original_raw' => $usageRow['original_raw'] ?? null,
                    'include_in_change' => $includeInChange,
                    'change_status' => $usageRow['change_status'] ?? 'pending',
                ]);
            }

            return $decision;
        });

        $this->editDecisionId = $decision->id;

        $translationActivity->record(
            event: 'translations.admin.usage_audit.decision_saved',
            description: __('Translation usage audit decision saved'),
            subject: $decision,
            before: $before,
            after: [
                'decision_action' => $decision->decision_action,
                'decision_status' => $decision->decision_status,
                'target_translation_key' => $decision->target_translation_key,
            ],
            properties: [
                'audit_type' => $decision->audit_type,
                'normalized_value_hash' => $decision->normalized_value_hash,
                'usage_rows' => count($this->editUsageRows),
            ],
        );
    }

    public function updatedEditDecisionAction(): void
    {
        if (
            $this->editDecisionAction === 'unify_to_target_key' &&
            ! $this->hasUsageAuditTargetTranslationKeyOptions()
        ) {
            $this->editDecisionAction = 'create_new_key';
            $this->updatedEditDecisionAction();

            return;
        }

        if ($this->editDecisionAction === 'create_new_key') {
            $this->editTargetTranslationKey = '';

            foreach ($this->editUsageRows as $usageIndex => $usageRow) {
                $this->editUsageRows[$usageIndex]['target_translation_key'] = null;
                $this->editUsageRows[$usageIndex]['include_in_change'] = false;
                $this->editUsageRows[$usageIndex]['change_status'] = 'needs_key';
            }

            $this->editDecisionStatus = $this->computedUsageAuditDecisionStatus();

            return;
        }

        if ($this->editDecisionAction === 'skip') {
            $this->editTargetTranslationKey = '';

            foreach ($this->editUsageRows as $usageIndex => $usageRow) {
                $this->editUsageRows[$usageIndex]['target_translation_key'] = null;
                $this->editUsageRows[$usageIndex]['include_in_change'] = false;
                $this->editUsageRows[$usageIndex]['change_status'] = 'skipped';
            }

            $this->editDecisionStatus = $this->computedUsageAuditDecisionStatus();

            return;
        }

        if ($this->editDecisionAction === 'unify_to_target_key') {
            if (trim($this->editTargetTranslationKey) === '') {
                $this->resetUsageAuditReplacementRows();
            } else {
                $this->updatedEditTargetTranslationKey();
            }
        }

        $this->editDecisionStatus = $this->computedUsageAuditDecisionStatus();
    }

    public function updatedEditTargetTranslationKey(): void
    {
        if ($this->editDecisionAction !== 'unify_to_target_key') {
            $this->editDecisionStatus = $this->computedUsageAuditDecisionStatus();

            return;
        }

        $targetTranslationKey = trim($this->editTargetTranslationKey);

        foreach ($this->editUsageRows as $usageIndex => $usageRow) {
            $currentTranslationKey = trim((string) ($usageRow['current_translation_key'] ?? ''));

            $this->editUsageRows[$usageIndex]['target_translation_key'] = $targetTranslationKey !== ''
                ? $targetTranslationKey
                : null;

            if ($targetTranslationKey !== '' && $currentTranslationKey === $targetTranslationKey) {
                $this->editUsageRows[$usageIndex]['include_in_change'] = false;
                $this->editUsageRows[$usageIndex]['change_status'] = 'already_target';

                continue;
            }

            if ($targetTranslationKey === '') {
                $this->editUsageRows[$usageIndex]['include_in_change'] = false;
                $this->editUsageRows[$usageIndex]['change_status'] = 'pending';

                continue;
            }

            $isStale = (bool) ($usageRow['is_stale'] ?? false);

            $this->editUsageRows[$usageIndex]['include_in_change'] = ! $isStale;

            if (in_array(($usageRow['change_status'] ?? null), ['already_target', 'needs_key', 'skipped'], true)) {
                $this->editUsageRows[$usageIndex]['change_status'] = 'pending';
            }
        }

        $this->editDecisionStatus = $this->computedUsageAuditDecisionStatus();
    }

    private function hasUsageAuditTargetTranslationKeyOptions(): bool
    {
        return collect($this->editTargetTranslationKeyOptions)
            ->filter(static fn (mixed $targetTranslationKey): bool => trim((string) $targetTranslationKey) !== '')
            ->isNotEmpty();
    }

    private function resetUsageAuditReplacementRows(): void
    {
        foreach ($this->editUsageRows as $usageIndex => $usageRow) {
            $this->editUsageRows[$usageIndex]['target_translation_key'] = null;
            $this->editUsageRows[$usageIndex]['include_in_change'] = false;
            $this->editUsageRows[$usageIndex]['change_status'] = 'pending';
        }

        $this->editDecisionStatus = $this->computedUsageAuditDecisionStatus();
    }

    public function updatedEditUsageRows(): void
    {
        $this->editDecisionStatus = $this->computedUsageAuditDecisionStatus();
    }

    public function computedUsageAuditDecisionStatus(): string
    {
        if ($this->editDecisionAction === 'create_new_key') {
            return 'needs_key';
        }

        if ($this->editDecisionAction === 'skip') {
            return 'ready';
        }

        if ($this->editDecisionAction !== 'unify_to_target_key') {
            return 'draft';
        }

        if (trim($this->editTargetTranslationKey) === '') {
            return 'draft';
        }

        $includedUsageRows = collect($this->editUsageRows)
            ->filter(static fn (array $usageRow): bool => (bool) ($usageRow['include_in_change'] ?? false));

        if ($includedUsageRows->isEmpty()) {
            return 'draft';
        }

        return 'ready';
    }

    public function computedUsageAuditDecisionStatusLabel(): string
    {
        return match ($this->computedUsageAuditDecisionStatus()) {
            'ready' => __('Ready'),
            'needs_key' => __('Needs key'),
            'applied' => __('Applied'),
            default => __('Draft'),
        };
    }

    public function computedUsageAuditDecisionStatusColor(): string
    {
        return match ($this->computedUsageAuditDecisionStatus()) {
            'ready' => 'emerald',
            'needs_key' => 'amber',
            'applied' => 'sky',
            default => 'zinc',
        };
    }

    public function usageAuditDecisionActionLabel(?string $action): string
    {
        return match ($action) {
            'unify_to_target_key' => __('Unify'),
            'skip' => __('Skip'),
            'create_new_key' => __('Needs key'),
            'undecided' => __('Undecided'),
            default => __('New'),
        };
    }

    public function usageAuditDecisionActionColor(?string $action): string
    {
        return match ($action) {
            'unify_to_target_key' => 'emerald',
            'skip' => 'zinc',
            'create_new_key' => 'amber',
            'undecided' => 'zinc',
            default => 'sky',
        };
    }

    public function usageAuditDecisionStatusLabel(?string $status): string
    {
        return match ($status) {
            'ready' => __('Ready'),
            'needs_key' => __('Needs key'),
            'applied' => __('Applied'),
            'draft' => __('Draft'),
            default => __('New'),
        };
    }

    public function usageAuditDecisionStatusColor(?string $status): string
    {
        return match ($status) {
            'ready' => 'emerald',
            'needs_key' => 'amber',
            'applied' => 'sky',
            'draft' => 'zinc',
            default => 'zinc',
        };
    }

    /**
     * Determine whether the current decision action can be saved.
     *
     * Skip and Needs-Key decisions are valid without usage rows. Unify decisions require an existing
     * target key and at least one included usage row.
     */
    public function canSaveUsageAuditDecision(): bool
    {
        if (! $this->selectedAuditType || ! $this->selectedNormalizedValue) {
            return false;
        }

        if ($this->editDecisionAction === 'skip') {
            return true;
        }

        if ($this->editDecisionAction === 'create_new_key') {
            return true;
        }

        if ($this->editDecisionAction !== 'unify_to_target_key') {
            return false;
        }

        if (! $this->hasUsageAuditTargetTranslationKeyOptions()) {
            return false;
        }

        if (trim($this->editTargetTranslationKey) === '') {
            return false;
        }

        return collect($this->editUsageRows)
            ->contains(static fn (array $usageRow): bool => (bool) ($usageRow['include_in_change'] ?? false));
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->onlyWithoutUiCandidate = false;
        $this->onlyWithStaleUsages = false;
        $this->decisionFilter = 'all';
        $this->minCurrentUsages = 0;
        $this->perPage = 25;
        $this->sortField = 'usage_count_current';
        $this->sortDirection = 'desc';

        $this->resetPage();
    }

    /**
     * Build all data required by the usage-audit page and its modal partials.
     *
     * The duplicate/frequent audit reports are loaded from JSON files, then filtered and sorted in
     * memory. Persisted decisions are indexed by normalized_value_hash to avoid per-row database
     * lookups while rendering filters, badges and decision sorting.
     */
    public function render(): View
    {
        $duplicateSummary = $this->readJsonFile(
            storage_path('audits/translations/duplicate-usage-literals/summary.json'),
            [],
        );

        $frequentSummary = $this->readJsonFile(
            storage_path('audits/translations/frequent-usage-literals/summary.json'),
            [],
        );

        $duplicateItems = $this->withUsageAuditType(
            items: $this->readJsonCollection(
                storage_path('audits/translations/duplicate-usage-literals/candidates.json'),
            ),
            auditType: 'duplicate',
        );

        $frequentItems = $this->withUsageAuditType(
            items: $this->readJsonCollection(
                storage_path('audits/translations/frequent-usage-literals/literals.json'),
            ),
            auditType: 'frequent',
        );

        $manualNeedsKeyItems = $this->manualNeedsKeyItems();

        $allSourceItems = $duplicateItems
            ->merge($frequentItems)
            ->merge($manualNeedsKeyItems)
            ->values();

        $activeSourceItems = $this->activeSourceItems(
            duplicateItems: $duplicateItems,
            frequentItems: $frequentItems,
            manualNeedsKeyItems: $manualNeedsKeyItems,
            allSourceItems: $allSourceItems,
        );

        $allUsageAuditDecisionIndex = $this->usageAuditDecisionIndexFor($allSourceItems);
        $activeUsageAuditDecisionIndex = $this->usageAuditDecisionIndexFor($activeSourceItems);

        $activeItems = $this->filteredItems(
            items: $activeSourceItems,
            usageAuditDecisionIndex: $activeUsageAuditDecisionIndex,
        );

        $translationUsageItems = $this->paginateItems($activeItems);

        $usageAuditDecisionIndex = $this->usageAuditDecisionPageIndexFor(
            usageAuditDecisionIndex: $activeUsageAuditDecisionIndex,
            items: collect($translationUsageItems->items()),
        );

        $selectedItem = $this->selectedUsageAuditItem(
            duplicateItems: $duplicateItems,
            frequentItems: $frequentItems,
            manualNeedsKeyItems: $manualNeedsKeyItems,
        );

        $selectedModalData = $this->selectedUsageAuditModalData($selectedItem);

        return view('components.admin.⚡translation-usage-audit', [
            'duplicateSummary' => $duplicateSummary,
            'frequentSummary' => $frequentSummary,
            'duplicateItems' => $duplicateItems,
            'frequentItems' => $frequentItems,
            'manualNeedsKeyItems' => $manualNeedsKeyItems,
            'activeItems' => $activeItems,
            'activeUsageAuditDecisionIndex' => $activeUsageAuditDecisionIndex,
            'allUsageAuditDecisionIndex' => $allUsageAuditDecisionIndex,
            'translationUsageItems' => $translationUsageItems,
            'usageAuditDecisionIndex' => $usageAuditDecisionIndex,
            'selectedItem' => $selectedItem,
            'selectedKeys' => $selectedModalData['selectedKeys'],
            'selectedUiKeys' => $selectedModalData['selectedUiKeys'],
            'selectedSuggestedUiKey' => $selectedModalData['selectedSuggestedUiKey'],
            'selectedValues' => $selectedModalData['selectedValues'],
            'selectedMainLanguageValueGroups' => $selectedModalData['selectedMainLanguageValueGroups'],
            'selectedUsageLocations' => $selectedModalData['selectedUsageLocations'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function readJsonFile(string $path, array $fallback): array
    {
        if (! File::isFile($path)) {
            return $fallback;
        }

        $payload = json_decode((string) File::get($path), true);

        return is_array($payload) ? $payload : $fallback;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function readJsonCollection(string $path): Collection
    {
        $payload = $this->readJsonFile($path, []);

        return collect($payload)
            ->filter(static fn (mixed $item): bool => is_array($item))
            ->values();
    }

    /**
     * Attach a stable audit type to generated audit rows.
     *
     * This is required when the Needs-Key decision focus combines duplicate, frequent and manual
     * sources in one table result.
     *
     * @param  Collection<int, array<string, mixed>>  $items
     * @return Collection<int, array<string, mixed>>
     */
    private function withUsageAuditType(Collection $items, string $auditType): Collection
    {
        return $items
            ->map(static fn (array $item): array => [
                ...$item,
                'audit_type' => (string) ($item['audit_type'] ?? $auditType),
            ])
            ->values();
    }

    /**
     * Resolve the source collection for the current UI state.
     *
     * Needs-Key is a cross-source decision focus unless the user explicitly selects the manual source
     * tab. That makes Decision → Needs key show audit-generated and manually marked entries together.
     *
     * @param  Collection<int, array<string, mixed>>  $duplicateItems
     * @param  Collection<int, array<string, mixed>>  $frequentItems
     * @param  Collection<int, array<string, mixed>>  $manualNeedsKeyItems
     * @param  Collection<int, array<string, mixed>>  $allSourceItems
     * @return Collection<int, array<string, mixed>>
     */
    private function activeSourceItems(
        Collection $duplicateItems,
        Collection $frequentItems,
        Collection $manualNeedsKeyItems,
        Collection $allSourceItems,
    ): Collection {
        if ($this->decisionFilter === 'needs_key' && $this->activeTab !== 'manual_needs_key') {
            return $allSourceItems;
        }

        return match ($this->activeTab) {
            'manual_needs_key' => $manualNeedsKeyItems,
            'frequent' => $frequentItems,
            default => $duplicateItems,
        };
    }

    /**
     * Build synthetic Usage-Audit items from TranslationKeys manually marked as Needs-New-Key.
     *
     * These entries are not generated by duplicate/frequent audit JSON files. They intentionally use
     * the same item shape so the existing review/edit modals can handle them without a separate UI.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function manualNeedsKeyItems(): Collection
    {
        $translationKeys = TranslationKey::query()
            ->with([
                'values' => fn ($query) => $query->orderBy('locale'),
                'usages' => fn ($query) => $query->orderBy('file')->orderBy('line')->orderBy('id'),
            ])
            ->whereNotNull('needs_new_key_marked_at')
            ->whereNull('needs_new_key_resolved_at')
            ->orderBy('needs_new_key_marked_at')
            ->orderBy('id')
            ->get();

        return $translationKeys
            ->map(function (TranslationKey $translationKey): array {
                $sourceValue = $this->manualNeedsKeySourceValueFor($translationKey);
                $normalizedValue = $this->normalizeManualNeedsKeyValue($sourceValue);

                return [
                    'translation_key' => $translationKey,
                    'source_value' => $sourceValue,
                    'normalized_value' => $normalizedValue,
                ];
            })
            ->filter(static fn (array $row): bool => trim((string) ($row['normalized_value'] ?? '')) !== '')
            ->groupBy(static fn (array $row): string => (string) $row['normalized_value'])
            ->map(function (Collection $rows, string $normalizedValue): array {
                $keys = $rows
                    ->pluck('translation_key')
                    ->filter(static fn (mixed $translationKey): bool => $translationKey instanceof TranslationKey)
                    ->values();

                $sourceValue = trim((string) ($rows->first()['source_value'] ?? $normalizedValue));
                $uiKeys = $keys
                    ->pluck('key')
                    ->map(static fn (mixed $key): string => trim((string) $key))
                    ->filter(static fn (string $key): bool => str_starts_with($key, 'ui.'))
                    ->unique()
                    ->values();

                $usageRows = $keys
                    ->flatMap(fn (TranslationKey $translationKey): Collection => $translationKey->usages)
                    ->values();

                $staleUsageRows = $usageRows->filter(
                    static fn ($usage): bool => (string) ($usage->reason ?? '') === 'stale_audit_usage_not_seen_in_latest_sync',
                );

                $suggestedUiKey = $keys
                    ->pluck('suggested_key')
                    ->map(static fn (mixed $suggestedKey): string => trim((string) $suggestedKey))
                    ->filter()
                    ->first() ?? '';

                return [
                    'audit_type' => 'manual_needs_key',
                    'locale' => 'en',
                    'value' => $sourceValue,
                    'normalized_value' => $normalizedValue,
                    'translation_key_count' => $keys->count(),
                    'usage_count_total' => $usageRows->count(),
                    'usage_count_current' => $usageRows->count() - $staleUsageRows->count(),
                    'usage_count_stale' => $staleUsageRows->count(),
                    'has_stale_usages' => $staleUsageRows->isNotEmpty(),
                    'already_has_ui_candidate' => $uiKeys->isNotEmpty(),
                    'ui_keys' => $uiKeys->all(),
                    'suggested_ui_key' => $suggestedUiKey,
                    'non_ui_translation_key_count' => $keys
                        ->pluck('key')
                        ->map(static fn (mixed $key): string => trim((string) $key))
                        ->filter(static fn (string $key): bool => $key !== '' && ! str_starts_with($key, 'ui.'))
                        ->count(),
                    'keys' => $keys
                        ->map(fn (TranslationKey $translationKey): array => $this->manualNeedsKeyItemKeyPayload($translationKey))
                        ->values()
                        ->all(),
                ];
            })
            ->sortBy('value', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    private function manualNeedsKeySourceValueFor(TranslationKey $translationKey): string
    {
        $nativeText = trim((string) ($translationKey->native_text ?? ''));

        if ($nativeText !== '') {
            return $nativeText;
        }

        $englishValue = $translationKey->values
            ->first(static fn ($value): bool => LocaleCode::normalize((string) ($value->locale ?? '')) === 'en');

        $englishText = trim((string) ($englishValue?->value ?? ''));

        if ($englishText !== '') {
            return $englishText;
        }

        $key = trim((string) ($translationKey->key ?? ''));

        if ($key !== '') {
            return $key;
        }

        $suggestedKey = trim((string) ($translationKey->suggested_key ?? ''));

        return $suggestedKey !== '' ? $suggestedKey : '#'.$translationKey->id;
    }

    private function normalizeManualNeedsKeyValue(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        return mb_strtolower((string) preg_replace('/\s+/u', ' ', $value));
    }

    /**
     * @return array<string, mixed>
     */
    private function manualNeedsKeyItemKeyPayload(TranslationKey $translationKey): array
    {
        return [
            'translation_key_id' => $translationKey->id,
            'full_key' => $translationKey->key,
            'key' => $translationKey->key,
            'classification' => $translationKey->classification,
            'values' => $translationKey->values
                ->mapWithKeys(static fn ($value): array => [
                    (string) ($value->locale ?? '') => [
                        'locale' => $value->locale,
                        'value' => $value->value,
                        'status' => $value->status,
                        'source' => $value->source,
                        'is_base_duplicate' => $value->is_base_duplicate,
                    ],
                ])
                ->all(),
            'usages' => $translationKey->usages
                ->map(static fn ($usage): array => [
                    'file' => $usage->file,
                    'line' => $usage->line,
                    'function' => $usage->function,
                    'classification' => $usage->classification,
                    'reason' => $usage->reason,
                    'raw' => $usage->raw,
                    'original_raw' => $usage->original_raw,
                    'is_stale' => (string) ($usage->reason ?? '') === 'stale_audit_usage_not_seen_in_latest_sync',
                ])
                ->all(),
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $items
     * @return Collection<int, array<string, mixed>>
     */
    private function filteredItems(Collection $items, ?Collection $usageAuditDecisionIndex = null): Collection
    {
        $usageAuditDecisionIndex ??= collect();

        $search = mb_strtolower(trim($this->search));

        return $items
            ->filter(function (array $item) use ($search): bool {
                if ($search === '') {
                    return true;
                }

                $haystack = mb_strtolower(implode(' ', [
                    (string) ($item['value'] ?? ''),
                    (string) ($item['normalized_value'] ?? ''),
                    implode(' ', (array) ($item['ui_keys'] ?? [])),
                    (string) ($item['suggested_ui_key'] ?? ''),
                ]));

                return str_contains($haystack, $search);
            })
            ->filter(function (array $item): bool {
                if (! $this->onlyWithoutUiCandidate) {
                    return true;
                }

                return ! (bool) ($item['already_has_ui_candidate'] ?? false);
            })
            ->filter(function (array $item): bool {
                if (! $this->onlyWithStaleUsages) {
                    return true;
                }

                return (bool) ($item['has_stale_usages'] ?? false);
            })
            ->filter(function (array $item) use ($usageAuditDecisionIndex): bool {
                if ($this->decisionFilter === 'all') {
                    return true;
                }

                $itemAuditType = (string) ($item['audit_type'] ?? $this->activeTab);
                $normalizedValueHash = md5((string) ($item['normalized_value'] ?? ''));
                $decision = $usageAuditDecisionIndex->get(
                    $this->usageAuditDecisionIndexKey($itemAuditType, $normalizedValueHash),
                );

                $isManualNeedsKeyItem = $itemAuditType === 'manual_needs_key';

                return match ($this->decisionFilter) {
                    'new' => ! $decision && ! $isManualNeedsKeyItem,
                    'saved' => (bool) $decision,
                    'draft' => $decision?->decision_status === 'draft',
                    'ready' => $decision?->decision_status === 'ready',
                    'applied' => $decision?->decision_status === 'applied',
                    'needs_key' => $isManualNeedsKeyItem
                        || $decision?->decision_status === 'needs_key'
                        || $decision?->decision_action === 'create_new_key',
                    'skipped' => $decision?->decision_action === 'skip',
                    default => true,
                };
            })
            ->filter(function (array $item): bool {
                if ($this->minCurrentUsages <= 0) {
                    return true;
                }

                return (int) ($item['usage_count_current'] ?? 0) >= $this->minCurrentUsages;
            })
            ->pipe(fn (Collection $items): Collection => $this->sortItems($items, $usageAuditDecisionIndex))
            ->values();
    }

    /**
     * Normalize sortable table field names.
     */
    private function normalizeSortField(mixed $field): string
    {
        $field = is_string($field) ? trim($field) : '';

        return in_array($field, [
            'id',
            'value',
            'translation_key_count',
            'usage_count_total',
            'usage_count_current',
            'usage_count_stale',
            'already_has_ui_candidate',
            'suggested_ui_key',
            'decision',
        ], true)
            ? $field
            : 'usage_count_current';
    }

    /**
     * Sort filtered usage audit items.
     *
     * @param  Collection<int, array<string, mixed>>  $items
     * @return Collection<int, array<string, mixed>>
     */
    private function sortItems(Collection $items, Collection $usageAuditDecisionIndex): Collection
    {
        $field = $this->normalizeSortField($this->sortField);
        $descending = $this->sortDirection !== 'asc';

        return $items
            ->sortBy(
                fn (array $item): mixed => $this->sortValue($item, $field, $usageAuditDecisionIndex),
                SORT_NATURAL | SORT_FLAG_CASE,
                $descending,
            )
            ->values();
    }

    /**
     * Resolve comparable sort values from one audit item.
     *
     * @param  array<string, mixed>  $item
     * @param  Collection<string, TranslationUsageAuditDecision>  $usageAuditDecisionIndex
     */
    private function sortValue(array $item, string $field, Collection $usageAuditDecisionIndex): mixed
    {
        return match ($field) {
            'id' => (int) data_get($item, 'keys.0.translation_key_id', 0),
            'value' => mb_strtolower((string) ($item['value'] ?? '')),
            'translation_key_count' => (int) ($item['translation_key_count'] ?? 0),
            'usage_count_total' => (int) ($item['usage_count_total'] ?? $item['usage_count'] ?? 0),
            'usage_count_current' => (int) ($item['usage_count_current'] ?? 0),
            'usage_count_stale' => (int) ($item['usage_count_stale'] ?? 0),
            'already_has_ui_candidate' => (int) ((bool) ($item['already_has_ui_candidate'] ?? false)),
            'suggested_ui_key' => mb_strtolower((string) ($item['suggested_ui_key'] ?? implode(' ', (array) ($item['ui_keys'] ?? [])))),
            'decision' => $this->usageAuditDecisionSortValue($item, $usageAuditDecisionIndex),
            default => (int) ($item['usage_count_current'] ?? 0),
        };
    }

    /**
     * Resolve sortable decision state for one audit item.
     *
     * @param  array<string, mixed>  $item
     */
    private function usageAuditDecisionSortValue(array $item, Collection $usageAuditDecisionIndex): string
    {
        $normalizedValueHash = md5((string) ($item['normalized_value'] ?? ''));

        if ($normalizedValueHash === '') {
            return '9-new';
        }

        $itemAuditType = (string) ($item['audit_type'] ?? $this->activeTab);
        $decision = $usageAuditDecisionIndex->get(
            $this->usageAuditDecisionIndexKey($itemAuditType, $normalizedValueHash),
        );

        if ($itemAuditType === 'manual_needs_key' && ! $decision) {
            return '1-needs-key-manual';
        }

        if (! $decision) {
            return '9-new';
        }

        return match ($decision->decision_status) {
            'needs_key' => '1-needs-key',
            'ready' => '2-ready',
            'draft' => '3-draft',
            'applied' => '4-applied',
            default => '8-'.(string) $decision->decision_status,
        };
    }

    /**
     * Paginate the already filtered and sorted in-memory audit items.
     *
     * @param  Collection<int, array<string, mixed>>  $items
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    private function paginateItems(Collection $items): LengthAwarePaginator
    {
        $page = $this->getPage();
        $perPage = in_array((int) $this->perPage, [10, 25, 50, 100], true)
            ? (int) $this->perPage
            : 25;

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
     * Load persisted decisions for all currently known audit candidates.
     *
     * Only the columns required for filtering, sorting and table badges are selected.
     *
     * @param  Collection<int, array<string, mixed>>  $items
     * @return Collection<string, TranslationUsageAuditDecision>
     */
    private function usageAuditDecisionIndexFor(Collection $items): Collection
    {
        $itemsByAuditType = $items
            ->map(function (array $item): array {
                $auditType = trim((string) ($item['audit_type'] ?? $this->activeTab));
                $normalizedValueHash = md5((string) ($item['normalized_value'] ?? ''));

                return [
                    'audit_type' => $auditType,
                    'normalized_value_hash' => $normalizedValueHash,
                ];
            })
            ->filter(static fn (array $item): bool => $item['audit_type'] !== '' && $item['normalized_value_hash'] !== '')
            ->groupBy('audit_type');

        if ($itemsByAuditType->isEmpty()) {
            return collect();
        }

        return TranslationUsageAuditDecision::query()
            ->select([
                'id',
                'audit_type',
                'normalized_value_hash',
                'decision_action',
                'decision_status',
                'target_translation_key',
            ])
            ->where(function ($query) use ($itemsByAuditType): void {
                foreach ($itemsByAuditType as $auditType => $items) {
                    $query->orWhere(function ($query) use ($auditType, $items): void {
                        $query
                            ->where('audit_type', $auditType)
                            ->whereIn(
                                'normalized_value_hash',
                                $items
                                    ->pluck('normalized_value_hash')
                                    ->unique()
                                    ->values()
                                    ->all(),
                            );
                    });
                }
            })
            ->get()
            ->keyBy(fn (TranslationUsageAuditDecision $decision): string => $this->usageAuditDecisionIndexKey(
                auditType: (string) $decision->audit_type,
                normalizedValueHash: (string) $decision->normalized_value_hash,
            ));
    }

    private function usageAuditDecisionIndexKey(string $auditType, string $normalizedValueHash): string
    {
        return trim($auditType).'|'.trim($normalizedValueHash);
    }

    /**
     * @param  Collection<string, TranslationUsageAuditDecision>  $usageAuditDecisionIndex
     * @param  Collection<int, array<string, mixed>>  $items
     * @return Collection<string, TranslationUsageAuditDecision>
     */
    private function usageAuditDecisionPageIndexFor(Collection $usageAuditDecisionIndex, Collection $items): Collection
    {
        $pageDecisionKeys = $items
            ->map(fn (array $item): string => $this->usageAuditDecisionIndexKey(
                auditType: (string) ($item['audit_type'] ?? $this->activeTab),
                normalizedValueHash: md5((string) ($item['normalized_value'] ?? '')),
            ))
            ->filter()
            ->unique()
            ->values();

        if ($pageDecisionKeys->isEmpty()) {
            return collect();
        }

        return $usageAuditDecisionIndex
            ->filter(static fn (TranslationUsageAuditDecision $decision, string $decisionKey): bool => $pageDecisionKeys->contains($decisionKey));
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $duplicateItems
     * @param  Collection<int, array<string, mixed>>  $frequentItems
     * @return array<string, mixed>|null
     */
    private function selectedUsageAuditItem(
        Collection $duplicateItems,
        Collection $frequentItems,
        Collection $manualNeedsKeyItems,
    ): ?array {
        if (! $this->selectedAuditType || ! $this->selectedNormalizedValue) {
            return null;
        }

        return $this->selectedUsageAuditItemFromCollections(
            auditType: $this->selectedAuditType,
            normalizedValueHash: $this->selectedNormalizedValue,
            duplicateItems: $duplicateItems,
            frequentItems: $frequentItems,
            manualNeedsKeyItems: $manualNeedsKeyItems,
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    private function selectedUsageAuditItemFor(string $auditType, string $normalizedValueHash): ?array
    {
        $duplicateItems = $this->readJsonCollection(
            storage_path('audits/translations/duplicate-usage-literals/candidates.json'),
        );

        $frequentItems = $this->readJsonCollection(
            storage_path('audits/translations/frequent-usage-literals/literals.json'),
        );

        $manualNeedsKeyItems = $this->manualNeedsKeyItems();

        return $this->selectedUsageAuditItemFromCollections(
            auditType: $auditType,
            normalizedValueHash: $normalizedValueHash,
            duplicateItems: $duplicateItems,
            frequentItems: $frequentItems,
            manualNeedsKeyItems: $manualNeedsKeyItems,
        );
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $duplicateItems
     * @param  Collection<int, array<string, mixed>>  $frequentItems
     * @return array<string, mixed>|null
     */
    private function selectedUsageAuditItemFromCollections(
        string $auditType,
        string $normalizedValueHash,
        Collection $duplicateItems,
        Collection $frequentItems,
        Collection $manualNeedsKeyItems,
    ): ?array {
        $items = match ($auditType) {
            'manual_needs_key' => $manualNeedsKeyItems,
            'frequent' => $frequentItems,
            default => $duplicateItems,
        };

        $item = $items->first(
            fn (array $item): bool => md5((string) ($item['normalized_value'] ?? '')) === $normalizedValueHash,
        );

        return is_array($item) ? $item : null;
    }

    /**
     * Prepare all derived data used by the usage-audit review modal.
     *
     * @param  array<string, mixed>|null  $selectedItem
     * @return array{
     *     selectedKeys: Collection<int, array<string, mixed>>,
     *     selectedUiKeys: Collection<int, mixed>,
     *     selectedSuggestedUiKey: string,
     *     selectedValues: Collection<int, array<string, mixed>>,
     *     selectedMainLanguageValueGroups: Collection<int, array<string, mixed>>,
     *     selectedUsageLocations: Collection<int, array<string, mixed>>
     * }
     */
    private function selectedUsageAuditModalData(?array $selectedItem): array
    {
        $selectedKeys = collect($selectedItem['keys'] ?? [])
            ->filter(static fn (mixed $key): bool => is_array($key))
            ->values();

        $selectedUiKeys = collect($selectedItem['ui_keys'] ?? [])
            ->filter()
            ->values();

        $selectedSuggestedUiKey = trim((string) ($selectedItem['suggested_ui_key'] ?? ''));

        $selectedValues = $this->selectedUsageAuditValues($selectedKeys);

        return [
            'selectedKeys' => $selectedKeys,
            'selectedUiKeys' => $selectedUiKeys,
            'selectedSuggestedUiKey' => $selectedSuggestedUiKey,
            'selectedValues' => $selectedValues,
            'selectedMainLanguageValueGroups' => $this->selectedUsageAuditMainLanguageValueGroups($selectedValues),
            'selectedUsageLocations' => $this->selectedUsageAuditLocations($selectedKeys),
        ];
    }

    private function fillUsageAuditEditForm(string $auditType, string $normalizedValueHash, array $selectedItem): void
    {
        $decision = TranslationUsageAuditDecision::query()
            ->with('usages')
            ->where('audit_type', $auditType)
            ->where('normalized_value_hash', $normalizedValueHash)
            ->first();

        $this->editTargetTranslationKeyOptions = $this->targetTranslationKeyOptionsFor($selectedItem);

        $this->editDecisionId = $decision?->id;
        $this->editDecisionAction = $decision?->decision_action ?? 'undecided';
        $this->editDecisionStatus = $decision?->decision_status ?? 'draft';
        $this->editTargetTranslationKey = $decision?->target_translation_key ?? '';

        if ($this->editDecisionAction === 'unify_to_target_key' && $this->editTargetTranslationKey === '') {
            $this->resetUsageAuditReplacementRows();
        }

        $this->editReviewNote = $decision?->review_note ?? '';

        $existingUsages = collect($decision?->usages ?? [])
            ->keyBy(fn ($usage): string => $this->usageAuditDecisionUsageFingerprint([
                'translation_key_id' => $usage->translation_key_id,
                'file' => $usage->file,
                'line' => $usage->line,
                'raw' => $usage->raw,
            ]));

        $selectedKeys = collect($selectedItem['keys'] ?? [])
            ->filter(static fn (mixed $key): bool => is_array($key))
            ->values();

        $this->editUsageRows = $this->selectedUsageAuditLocations($selectedKeys)
            ->map(function (array $usage) use ($existingUsages): array {
                $fingerprint = $this->usageAuditDecisionUsageFingerprint([
                    'translation_key_id' => $usage['translation_key_id'] ?? null,
                    'file' => $usage['view_path'] ?? null,
                    'line' => $usage['line'] ?? null,
                    'raw' => $usage['raw'] ?? null,
                ]);

                $existingUsage = $existingUsages->get($fingerprint);
                $isStale = (bool) ($usage['is_stale'] ?? false);
                $currentTranslationKey = trim((string) ($usage['full_key'] ?? ''));
                $targetTranslationKey = $this->editDecisionAction === 'unify_to_target_key'
                    ? ($existingUsage?->target_translation_key ?? null)
                    : null;
                $isAlreadyTarget = $targetTranslationKey !== null && $targetTranslationKey !== '' && $currentTranslationKey === $targetTranslationKey;

                $includeInChange = match ($this->editDecisionAction) {
                    'unify_to_target_key' => $existingUsage
                        ? (bool) $existingUsage->include_in_change
                        : (! $isStale && ! $isAlreadyTarget),
                    default => false,
                };

                if ($isAlreadyTarget) {
                    $includeInChange = false;
                }

                $changeStatus = match ($this->editDecisionAction) {
                    'create_new_key' => 'needs_key',
                    'skip' => 'skipped',
                    'unify_to_target_key' => $existingUsage?->change_status ?? ($isAlreadyTarget ? 'already_target' : 'pending'),
                    default => $existingUsage?->change_status ?? 'pending',
                };

                return [
                    'translation_key_id' => $usage['translation_key_id'] ?? null,
                    'current_translation_key' => $currentTranslationKey !== '' ? $currentTranslationKey : null,
                    'target_translation_key' => $targetTranslationKey !== null && $targetTranslationKey !== '' ? $targetTranslationKey : null,
                    'file' => $usage['view_path'] ?? null,
                    'line' => $usage['line'] ?? null,
                    'detected_function' => $usage['function'] ?? null,
                    'classification' => $usage['classification'] ?? null,
                    'reason' => $usage['reason'] ?? null,
                    'is_stale' => $isStale,
                    'raw' => $usage['raw'] ?? null,
                    'original_raw' => $usage['original_raw'] ?? null,
                    'include_in_change' => $includeInChange,
                    'change_status' => $changeStatus,
                ];
            })
            ->values()
            ->all();
    }

    private function resetUsageAuditEditForm(): void
    {
        $this->editDecisionId = null;
        $this->editDecisionAction = 'undecided';
        $this->editDecisionStatus = 'draft';
        $this->editTargetTranslationKey = '';
        $this->editTargetTranslationKeyOptions = [];
        $this->editReviewNote = '';
        $this->createTranslationKeyPanelOpen = false;
        $this->createTranslationKeyInput = '';
        $this->editUsageRows = [];
        $this->selectedAuditType = null;
        $this->selectedNormalizedValue = null;

        $this->resetErrorBag('createTranslationKeyInput');
    }

    /**
     * @param  array<string, mixed>  $selectedItem
     * @return array<int, string>
     */
    private function targetTranslationKeyOptionsFor(array $selectedItem): array
    {
        return collect($selectedItem['ui_keys'] ?? [])
            ->map(static fn (mixed $uiKey): string => trim((string) $uiKey))
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
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
     * @param  array<string, mixed>  $usage
     */
    private function usageAuditDecisionUsageFingerprint(array $usage): string
    {
        return md5(implode('|', [
            (string) ($usage['translation_key_id'] ?? ''),
            (string) ($usage['file'] ?? ''),
            (string) ($usage['line'] ?? ''),
            (string) ($usage['raw'] ?? ''),
        ]));
    }

    /**
     * Flatten translation values from all selected keys.
     *
     * @param  Collection<int, array<string, mixed>>  $selectedKeys
     * @return Collection<int, array<string, mixed>>
     */
    private function selectedUsageAuditValues(Collection $selectedKeys): Collection
    {
        return $selectedKeys
            ->flatMap(static function (array $key): array {
                $values = $key['values'] ?? [];

                if (! is_array($values)) {
                    return [];
                }

                return collect($values)
                    ->map(static function (mixed $value, string $locale) use ($key): array {
                        $value = is_array($value) ? $value : [];

                        return [
                            'translation_key_id' => $key['translation_key_id'] ?? null,
                            'full_key' => $key['full_key'] ?? null,
                            'locale' => $value['locale'] ?? $locale,
                            'value' => $value['value'] ?? null,
                            'status' => $value['status'] ?? null,
                            'source' => $value['source'] ?? null,
                            'is_base_duplicate' => $value['is_base_duplicate'] ?? null,
                        ];
                    })
                    ->values()
                    ->all();
            })
            ->values();
    }

    /**
     * Flatten and normalize usage locations from all selected keys.
     *
     * @param  Collection<int, array<string, mixed>>  $selectedKeys
     * @return Collection<int, array<string, mixed>>
     */
    private function selectedUsageAuditLocations(Collection $selectedKeys): Collection
    {
        return $selectedKeys
            ->flatMap(static function (array $key): array {
                $usages = $key['usages'] ?? [];

                if (! is_array($usages)) {
                    return [];
                }

                return collect($usages)
                    ->filter(static fn (mixed $usage): bool => is_array($usage))
                    ->map(static function (array $usage) use ($key): array {
                        return [
                            'translation_key_id' => $key['translation_key_id'] ?? null,
                            'full_key' => $key['full_key'] ?? ($key['key'] ?? null),
                            'view_path' => $usage['file'] ?? ($usage['view_path'] ?? ($usage['path'] ?? null)),
                            'line' => $usage['line'] ?? null,
                            'function' => $usage['function'] ?? null,
                            'classification' => $usage['classification'] ?? ($key['classification'] ?? null),
                            'reason' => $usage['reason'] ?? null,
                            'raw' => $usage['raw'] ?? null,
                            'original_raw' => $usage['original_raw'] ?? null,
                            'is_stale' => (bool) ($usage['is_stale'] ?? false),
                        ];
                    })
                    ->all();
            })
            ->sortBy([
                static fn (array $usage): string => (string) ($usage['view_path'] ?? ''),
                static fn (array $usage): int => (int) ($usage['line'] ?? 0),
                static fn (array $usage): int => (int) ($usage['translation_key_id'] ?? 0),
            ])
            ->values();
    }

    /**
     * Group selected values into source language, target main languages, and sub languages.
     *
     * @param  Collection<int, array<string, mixed>>  $selectedValues
     * @return Collection<int, array<string, mixed>>
     */
    private function selectedUsageAuditMainLanguageValueGroups(Collection $selectedValues): Collection
    {
        return $selectedValues
            ->map(static function (array $value): array {
                $locale = LocaleCode::normalize((string) ($value['locale'] ?? ''));

                $value['locale'] = $locale;
                $value['is_source_locale'] = $locale === 'en';
                $value['is_main_language_locale'] = $locale !== '' && $locale !== 'en' && ! str_contains($locale, '-');
                $value['is_sub_language_locale'] = $locale !== '' && str_contains($locale, '-');

                return $value;
            })
            ->groupBy(static fn (array $value): string => (string) ($value['translation_key_id'] ?? ''))
            ->map(static function (Collection $values): array {
                $sourceValue = $values->first(
                    static fn (array $value): bool => (bool) ($value['is_source_locale'] ?? false),
                );

                $targetValues = $values
                    ->filter(static fn (array $value): bool => (bool) ($value['is_main_language_locale'] ?? false))
                    ->sortBy(
                        static fn (array $value): string => (string) ($value['locale'] ?? ''),
                        SORT_NATURAL | SORT_FLAG_CASE,
                    )
                    ->values();

                $subLanguageValues = $values
                    ->filter(static fn (array $value): bool => (bool) ($value['is_sub_language_locale'] ?? false))
                    ->sortBy(
                        static fn (array $value): string => (string) ($value['locale'] ?? ''),
                        SORT_NATURAL | SORT_FLAG_CASE,
                    )
                    ->values();

                return [
                    'translation_key_id' => $values->first()['translation_key_id'] ?? null,
                    'full_key' => $values->first()['full_key'] ?? null,
                    'source_value' => $sourceValue,
                    'target_values' => $targetValues,
                    'sub_language_values' => $subLanguageValues,
                ];
            })
            ->values();
    }
}
