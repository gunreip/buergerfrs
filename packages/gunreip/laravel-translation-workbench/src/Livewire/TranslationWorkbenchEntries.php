<?php

// packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchEntries.php

namespace Gunreip\TranslationWorkbench\Livewire;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Models\Locale;
use App\Settings\AppGeneralSettings;
use App\Support\Locale\LocaleCode;
use Flux\Flux;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangFileExporter;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangNodeClassifier;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTimelineRecorder;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Gunreip\TranslationWorkbench\Scanner\TranslationFingerprintFactory;
use Gunreip\TranslationWorkbench\Scanner\TranslationKeyPartsFactory;
use Gunreip\TranslationWorkbench\Support\TranslationKeySegmentFactory;
use Gunreip\TranslationWorkbench\Support\TranslationWorkbenchVersion;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class TranslationWorkbenchEntries extends Component
{
    use InteractsWithUserSettings;
    use WithPagination;

    /**
     * User-facing page state that is safe to persist.
     *
     * Modal state and editable translation values intentionally stay transient.
     *
     * @var array<int, string>
     */
    private const PERSISTED_STATE_PROPERTIES = [
        'findingSearch',
        'findingSearchExact',
        'findingSearchCaseSensitive',
        'findingStatus',
        'findingKind',
        'findingCandidateType',
        'findingNamespace',
        'findingGroup',
        'findingKeyRelation',
        'findingLiteralState',
        'perPage',
        'findingSortField',
        'findingSortDirection',
        'showOverviewTabs',
        'showObsoleteFindings',
        'editModalAutoCloseAfterSave',
    ];

    /**
     * @var array<string, bool>
     */
    private array $schemaTableCache = [];

    /**
     * @var array<string, bool>
     */
    private array $schemaColumnCache = [];

    public string $findingSearch = '';

    public bool $findingSearchExact = false;

    public bool $findingSearchCaseSensitive = false;

    public string $findingStatus = 'all';

    public string $findingKind = 'all';

    public string $findingCandidateType = 'all';

    public string $findingNamespace = 'all';

    public string $findingGroup = 'all';

    public string $findingKeyRelation = 'all';

    public string $findingLiteralState = 'all';

    public string $findingsActiveTab = 'work-findings';

    public string $codeUpdatePlanState = 'all';

    public string $codeUpdatePlanSearch = '';

    public int $perPage = 25;

    public string $findingSortField = 'last_seen';

    public string $findingSortDirection = 'desc';

    public bool $showOverviewTabs = true;

    public bool $showObsoleteFindings = false;

    public bool $reviewModalOpen = false;

    public bool $editModalOpen = false;

    public bool $dynamicEditModalOpen = false;

    public bool $dynamicMultiEditModalOpen = false;

    public bool $dynamicReviewModalOpen = false;

    public bool $dynamicSourceLinkConfirmModalOpen = false;

    public bool $obsoleteSourceValueReviewModalOpen = false;

    public bool $timelineModalOpen = false;

    public bool $translationKeyModalOpen = false;

    public bool $codeUpdateConflictReviewModalOpen = false;

    public bool $bulkEqualizeTranslationKeyModalOpen = false;

    public bool $exportConflictResolveModalOpen = false;

    public ?int $reviewFindingId = null;

    public ?int $editFindingId = null;

    public ?int $dynamicReviewFindingId = null;

    public ?int $dynamicSourceLinkRelatedSourceId = null;

    public ?string $obsoleteSourceValueTranslationKey = null;

    public ?int $timelineFindingId = null;

    public ?int $translationKeyFindingId = null;

    public ?int $codeUpdateConflictFindingId = null;

    public ?int $codeUpdateConflictKeyId = null;

    public string $exportConflictLocale = '';

    public string $exportConflictNamespace = '';

    public string $exportConflictLangKey = '';

    public string $exportConflictTranslationKey = '';

    public string $exportConflictKey = '';

    public string $codeUpdateConflictDecision = 'ignore_for_now';

    public string $codeUpdateConflictNote = '';

    /**
     * @var array<int, int>
     */
    public array $bulkEqualizeSelectedFindingIds = [];

    public ?string $bulkEqualizeTranslationKey = null;

    public ?string $translationKeyValue = null;

    public ?string $translationKeySegmentBaseValue = null;

    public ?string $targetTranslationValue = null;

    public ?string $sourceTranslationValue = null;

    public bool $sourceTranslationEditable = false;

    public bool $editModalAutoCloseAfterSave = true;

    public int $editModalAutoCloseCountdown = 0;

    public bool $bulkEqualizeReminderPending = false;

    /**
     * @var array<int, string>
     */
    public array $selectedTargetSubLocales = [];

    /**
     * @var array<string, ?string>
     */
    public array $targetSubTranslationValues = [];

    /**
     * @var array<string, ?string>
     */
    public array $dynamicMultiTargetValues = [];

    /**
     * @var array<string, ?string>
     */
    public array $dynamicMultiSourceValues = [];

    /**
     * @var array<string, string>
     */
    public array $dynamicMultiValueKeyMap = [];

    /**
     * @var array<string, bool>
     */
    public array $dynamicMultiSourceEqualsTargetOverrides = [];

    /**
     * @var array<string, bool>
     */
    public array $dynamicMultiEditableTargetFields = [];

    /**
     * @var array<string, bool>
     */
    public array $dynamicMultiEditableSourceFields = [];

    /**
     * @var array<int, string>
     */
    public array $translationKeyDeletedSegments = [];

    public function render(): View
    {
        $editFinding = $this->selectedFinding($this->editFindingId);
        $dynamicReviewFinding = $this->selectedFinding($this->dynamicReviewFindingId);
        $editLocales = $this->editLocales();

        return view('translation-workbench::livewire.entries', [
            'workbenchVersion' => app(TranslationWorkbenchVersion::class)->toArray(),
            'findings' => $this->findings(),
            'lastEditedTranslationRows' => $this->lastEditedTranslationRows(),
            'sharedKeyCandidateRows' => $this->sharedKeyCandidateRows(),
            'reviewFinding' => $this->selectedFinding($this->reviewFindingId),
            'editFinding' => $editFinding,
            'dynamicReviewFinding' => $dynamicReviewFinding,
            'dynamicReviewReady' => $dynamicReviewFinding ? $this->isDynamicReviewReady($dynamicReviewFinding) : false,
            'dynamicReviewSources' => $this->dynamicReviewSources($this->dynamicReviewFindingId),
            'dynamicSourceLinkPreview' => $this->dynamicSourceLinkPreview(),
            'obsoleteSourceValueReview' => $this->obsoleteSourceValueReview(),
            'dynamicEditFinding' => $this->selectedFinding($this->editFindingId),
            'dynamicMultiEditFinding' => $this->selectedFinding($this->editFindingId),
            'editLocales' => $editLocales,
            'editValues' => $this->editValues($editFinding, $editLocales),
            'dynamicMultiRows' => $this->dynamicMultiRows($this->editFindingId, $editLocales),
            'timelineFinding' => $this->selectedFinding($this->timelineFindingId),
            'timelineRows' => $this->timelineRows($this->timelineFindingId),
            'translationKeyFinding' => $this->selectedFinding($this->translationKeyFindingId),
            'codeUpdateConflictReview' => $this->codeUpdateConflictReview(),
            'exportConflictResolveContext' => $this->exportConflictResolveContext(),
            'translationKeySegmentStats' => $this->translationKeySegmentStats($this->translationKeyFindingId),
            'translationKeySegmentControls' => $this->translationKeySegmentControls(),
            'translationKeyCandidateReview' => $this->translationKeyCandidateReview(),
            'previousReviewFindingId' => $this->reviewAdjacentFindingId('previous'),
            'nextReviewFindingId' => $this->reviewAdjacentFindingId('next'),
            'findingStatusOptions' => $this->distinctOptions('translation_workbench_findings', 'status'),
            'findingKindOptions' => $this->distinctOptions('translation_workbench_findings', 'kind'),
            'findingCandidateTypeOptions' => $this->findingCandidateTypeOptions(),
            'findingNamespaceOptions' => $this->findingNamespaceOptions(),
            'findingGroupOptions' => $this->findingGroupOptions(),
            'databaseTableCallouts' => $this->databaseTableCallouts(),
            'healthCallouts' => $this->healthCallouts(),
            'sourceMainCoverageCallouts' => $this->sourceMainCoverageCallouts(),
            'langFilesHealthCallouts' => $this->langFilesHealthCallouts(),
            'keyCoverageCallouts' => $this->keyCoverageCallouts(),
            'localeCoverageRows' => $this->localeCoverageRows(),
            'scannerRunCallouts' => $this->scannerRunCallouts(),
            'scannerReportRows' => $this->scannerReportRows(),
            'pipelineRunReport' => $this->pipelineRunReport(),
            'langFileExportReport' => $this->langFileExportReport(),
            'codeUpdatePlanReport' => $this->codeUpdatePlanReport(),
            'codeUpdateApplyReport' => $this->codeUpdateApplyReport(),
            'timelineHealthCallouts' => $this->timelineHealthCallouts(),
            'dynamicValuesHealthCallouts' => $this->dynamicValuesHealthCallouts(),
            'sourceMainLocale' => $this->sourceMainLocale(),
            'targetMainLocale' => (string) ($editLocales['active'] ?? app()->getLocale()),
            'findingKindCounts' => $this->distribution('translation_workbench_findings', 'kind'),
            'keyTypeCounts' => $this->distribution('translation_workbench_keys', 'key_type'),
            'localeRoleCounts' => $this->distribution('translation_workbench_lang_values', 'locale_role'),
            'activeLocaleCounts' => $this->distribution('translation_workbench_lang_values', 'locale'),
            'supportedLocaleCounts' => $this->supportedLocaleSummaryRows(),
            'timelineEventCounts' => $this->distribution('translation_workbench_timeline_events', 'event_type'),
            'findingFiltersActive' => $this->findingFiltersActive(),
            'bulkEqualizeContext' => $this->bulkEqualizeContext(),
        ]);
    }

    public function mount(): void
    {
        $defaults = $this->uiStateDefaults();
        $state = $this->userSetting($this->uiStateSettingKey(), []);

        if (! is_array($state)) {
            $state = [];
        }

        $this->findingSearch = trim((string) ($state['findingSearch'] ?? $defaults['findingSearch'] ?? $this->findingSearch));
        $this->findingSearchExact = (bool) ($state['findingSearchExact'] ?? $defaults['findingSearchExact'] ?? $this->findingSearchExact);
        $this->findingSearchCaseSensitive = (bool) ($state['findingSearchCaseSensitive'] ?? $defaults['findingSearchCaseSensitive'] ?? $this->findingSearchCaseSensitive);
        $this->findingStatus = $this->normalizeOptionState($state['findingStatus'] ?? $defaults['findingStatus'] ?? $this->findingStatus);
        $this->findingKind = $this->normalizeOptionState($state['findingKind'] ?? $defaults['findingKind'] ?? $this->findingKind);
        $this->findingCandidateType = $this->normalizeOptionState($state['findingCandidateType'] ?? $defaults['findingCandidateType'] ?? $this->findingCandidateType);
        $this->findingNamespace = $this->normalizeOptionState($state['findingNamespace'] ?? $defaults['findingNamespace'] ?? $this->findingNamespace);
        $this->findingGroup = $this->normalizeOptionState($state['findingGroup'] ?? $defaults['findingGroup'] ?? $this->findingGroup);
        $this->findingKeyRelation = $this->normalizeOptionState($state['findingKeyRelation'] ?? $defaults['findingKeyRelation'] ?? $this->findingKeyRelation);
        $this->findingLiteralState = $this->normalizeOptionState(
            $state['findingLiteralState']
                ?? $defaults['findingLiteralState']
                ?? $this->legacyFindingLiteralState($state['findingSourceValue'] ?? $defaults['findingSourceValue'] ?? null),
        );
        $this->perPage = $this->normalizedPerPage($state['perPage'] ?? $defaults['perPage'] ?? $this->perPage);
        $this->findingSortField = $this->normalizeFindingSortField($state['findingSortField'] ?? $defaults['findingSortField'] ?? $this->findingSortField);
        $this->findingSortDirection = $this->normalizeSortDirection($state['findingSortDirection'] ?? $defaults['findingSortDirection'] ?? $this->findingSortDirection);
        $this->showOverviewTabs = (bool) ($state['showOverviewTabs'] ?? $defaults['showOverviewTabs'] ?? $this->showOverviewTabs);
        $this->showObsoleteFindings = (bool) ($state['showObsoleteFindings'] ?? $defaults['showObsoleteFindings'] ?? $this->showObsoleteFindings);
        $this->editModalAutoCloseAfterSave = (bool) ($state['editModalAutoCloseAfterSave'] ?? $defaults['editModalAutoCloseAfterSave'] ?? true);

        $this->setPage(1);
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'findingSearch',
            'findingSearchExact',
            'findingSearchCaseSensitive',
            'findingStatus',
            'findingKind',
            'findingCandidateType',
            'findingNamespace',
            'findingGroup',
            'findingKeyRelation',
            'findingLiteralState',
            'perPage',
            'showObsoleteFindings',
        ], true)) {
            $this->perPage = $this->normalizedPerPage();
            $this->resetPage();
        }

        if (in_array($property, self::PERSISTED_STATE_PROPERTIES, true)) {
            $this->persistUiState();
        }
    }

    public function updatedFindingNamespace(): void
    {
        if ($this->findingGroup !== 'all' && ! in_array($this->findingGroup, $this->findingGroupOptions(), true)) {
            $this->findingGroup = 'all';
            $this->persistUiState();
        }
    }

    public function updatedDynamicMultiTargetValues(mixed $value, string $fieldKey): void
    {
        $row = collect($this->dynamicMultiRows($this->editFindingId, $this->editLocales()))
            ->firstWhere('field_key', $fieldKey);

        if (! is_array($row)) {
            unset($this->dynamicMultiSourceEqualsTargetOverrides[$fieldKey]);

            return;
        }

        $sourceValue = $this->nullableString($row['source'] ?? $row['native_label'] ?? null);
        $targetValue = $this->nullableString($value);

        if ($sourceValue === null || $targetValue === null || $sourceValue !== $targetValue) {
            $this->dynamicMultiSourceEqualsTargetOverrides[$fieldKey] = false;
        }
    }

    public function updatedBulkEqualizeSelectedFindingIds(): void
    {
        $this->bulkEqualizeSelectedFindingIds = collect($this->bulkEqualizeSelectedFindingIds)
            ->map(static fn(mixed $id): int => (int) $id)
            ->filter(static fn(int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    public function resetFindingFilters(): void
    {
        $this->findingSearch = '';
        $this->findingSearchExact = false;
        $this->findingSearchCaseSensitive = false;
        $this->findingStatus = 'all';
        $this->findingKind = 'all';
        $this->findingCandidateType = 'all';
        $this->findingNamespace = 'all';
        $this->findingGroup = 'all';
        $this->findingKeyRelation = 'all';
        $this->findingLiteralState = 'all';
        $this->showObsoleteFindings = false;

        $this->resetPage();
        $this->persistUiState();
    }

    public function resetCodeUpdatePlanFilters(): void
    {
        $this->codeUpdatePlanState = 'all';
        $this->codeUpdatePlanSearch = '';
    }

    public function refreshFindingsCurrentTab(): void
    {
        $this->refreshFindingsTab($this->findingsActiveTab);
    }

    public function refreshFindingsAllTabs(): void
    {
        $results = [
            'export' => $this->refreshLangFileExportReport(),
            'code_plan' => $this->refreshCodeUpdatePlanReport(),
            'code_apply' => $this->refreshCodeUpdateApplyReportFile(),
            'shared_key_candidates' => $this->refreshSharedKeyCandidates(),
        ];
        $failed = collect($results)->filter(static fn(bool $success): bool => ! $success)->keys();

        if ($failed->isNotEmpty()) {
            Flux::toast(
                heading: __('Refresh incomplete'),
                text: __('Some report files could not be refreshed. Check file permissions or run the related artisan command.'),
                variant: 'warning',
            );

            return;
        }

        $this->resetPage();

        Flux::toast(
            heading: __('Findings refreshed'),
            text: __('The findings data and related report files have been refreshed.'),
            variant: 'success',
        );
    }

    private function refreshFindingsTab(string $tab): void
    {
        $success = match ($tab) {
            'shared-key-candidates' => $this->refreshSharedKeyCandidates(),
            'export-report' => $this->refreshLangFileExportReport(),
            'code-update-plan' => $this->refreshCodeUpdatePlanReport()
                && $this->refreshCodeUpdateApplyReportFile(),
            default => true,
        };

        $this->resetPage();

        Flux::toast(
            heading: $success ? __('Current tab refreshed') : __('Refresh failed'),
            text: $success
                ? __('The current findings tab has been reloaded.')
                : __('The current report file could not be refreshed. Check file permissions or run the related artisan command.'),
            variant: $success ? 'success' : 'warning',
        );
    }

    public function refreshCodeUpdateApplyReport(): void
    {
        if (! $this->refreshCodeUpdateApplyReportFile()) {
            Flux::toast(
                heading: __('Patch dry-run failed'),
                text: __('The code update dry-run report could not be regenerated.'),
                variant: 'danger',
            );

            return;
        }

        Flux::toast(
            heading: __('Patch dry-run refreshed'),
            text: __('The code update dry-run report and patch preview have been regenerated.'),
            variant: 'success',
        );
    }

    public function openBulkEqualizeTranslationKeyModal(): void
    {
        $context = $this->bulkEqualizeContext();

        if ($context['selected_count'] < 2) {
            Flux::toast(
                heading: __('Selection incomplete'),
                text: __('Select at least two findings with the same literal before equalizing their translation key.'),
                variant: 'warning',
            );

            return;
        }

        if (! $context['can_confirm']) {
            Flux::toast(
                heading: __('Selection needs review'),
                text: __('The selected findings need linked keys and exactly one shared literal before they can be equalized.'),
                variant: 'warning',
            );

            return;
        }

        $this->bulkEqualizeTranslationKey = $context['suggested_target_key'];
        $this->bulkEqualizeTranslationKeyModalOpen = true;
    }

    public function closeBulkEqualizeTranslationKeyModal(): void
    {
        $this->bulkEqualizeTranslationKeyModalOpen = false;
        $this->bulkEqualizeTranslationKey = null;
    }

    public function clearBulkEqualizeSelection(): void
    {
        $this->bulkEqualizeSelectedFindingIds = [];
        $this->closeBulkEqualizeTranslationKeyModal();
    }

    public function selectAllBulkEqualizeSelection(): void
    {
        $selectedLiteral = $this->selectedBulkEqualizeLiteral();

        if ($selectedLiteral === null) {
            Flux::toast(
                heading: __('No shared literal selected'),
                text: __('Select one shared-literal finding first, then use select all to add the matching findings.'),
                variant: 'warning',
            );

            return;
        }

        $selectedIds = $this->bulkEqualizeSelectableFindingIds($selectedLiteral);

        if ($selectedIds === []) {
            Flux::toast(
                heading: __('No matching findings found'),
                text: __('No additional selectable findings match the current shared literal and filters.'),
                variant: 'warning',
            );

            return;
        }

        $this->bulkEqualizeSelectedFindingIds = $selectedIds;

        Flux::toast(
            heading: __('Bulk selection updated'),
            text: __('All currently matching shared-literal findings have been selected.'),
            variant: 'success',
        );
    }

    public function confirmBulkEqualizeTranslationKey(): void
    {
        $context = $this->bulkEqualizeContext();

        if (! $context['can_confirm']) {
            Flux::toast(
                heading: __('Selection needs review'),
                text: __('Only findings with linked keys and exactly one shared literal can be equalized.'),
                variant: 'warning',
            );

            return;
        }

        $this->bulkEqualizeTranslationKey = $this->nullableString($this->bulkEqualizeTranslationKey);

        $this->validate([
            'bulkEqualizeTranslationKey' => [
                'required',
                'string',
                'regex:/^[a-z0-9][a-z0-9_-]*(\.[a-z0-9][a-z0-9_-]*)+$/',
            ],
        ]);

        $translationKey = (string) $this->bulkEqualizeTranslationKey;
        $keyIds = collect($context['rows'])
            ->pluck('key_id')
            ->map(static fn(mixed $id): int => (int) $id)
            ->filter(static fn(int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($keyIds->isEmpty() && ! ($context['equalize_to_existing_shared_key'] ?? false)) {
            return;
        }

        $operationId = (string) Str::uuid();
        $updated = 0;

        DB::transaction(function () use ($keyIds, $translationKey, $context, $operationId, &$updated): void {
            $keys = TranslationWorkbenchKey::query()
                ->whereIn('id', $keyIds->all())
                ->get()
                ->keyBy('id');
            $findings = TranslationWorkbenchFinding::query()
                ->whereIn('id', collect($context['rows'])->pluck('id')->all())
                ->get()
                ->keyBy('id');

            foreach ($context['rows'] as $row) {
                $finding = $findings->get((int) $row['id']);
                $key = $keys->get((int) $row['key_id'])
                    ?? $this->keyForBulkEqualizeRow($row, $finding, $translationKey, $operationId);

                if (! $key || ! $finding) {
                    continue;
                }

                $attributes = [
                    ...$this->keyStructureFromTranslationKey($translationKey, $key),
                    'review_status' => 'reviewed',
                    'reviewed_at' => now(),
                    'reviewed_by_user_id' => Auth::id(),
                ];
                $trackedAttributes = array_values(array_unique(array_keys($attributes)));
                $oldValues = $key->only([
                    ...$trackedAttributes,
                    'review_status',
                    'reviewed_at',
                    'reviewed_by_user_id',
                ]);
                $changedValues = collect($attributes)
                    ->filter(static fn(mixed $value, string $attribute): bool => ($oldValues[$attribute] ?? null) != $value)
                    ->all();

                if ($changedValues === []) {
                    continue;
                }

                $key->forceFill($changedValues)->save();
                $newValues = $key->only([
                    ...$trackedAttributes,
                    'review_status',
                    'reviewed_at',
                    'reviewed_by_user_id',
                ]);
                $review = TranslationWorkbenchReview::query()->create([
                    'key_id' => $key->id,
                    'finding_id' => $finding->id,
                    'review_type' => 'translation_key',
                    'decision' => 'translation_key_bulk_equalized',
                    'old_values' => $oldValues,
                    'new_values' => $newValues,
                    'meta' => [
                        'source' => 'translation-workbench:bulk-equalize-translation-key',
                        'operation_id' => $operationId,
                        'literal' => $context['literal'],
                        'normalized_literal' => $context['normalized_literal'],
                        'selected_finding_ids' => $context['selected_ids'],
                        'target_translation_key' => $translationKey,
                    ],
                    'reviewed_by_user_id' => Auth::id(),
                    'reviewed_at' => now(),
                ]);

                app(TranslationWorkbenchTimelineRecorder::class)->recordReviewEvent(
                    review: $review,
                    eventType: 'translation_key_bulk_equalized',
                    oldValues: $oldValues,
                    newValues: $newValues,
                    context: [
                        'source' => 'translation-workbench:bulk-equalize-translation-key',
                        'operation_id' => $operationId,
                        'literal' => $context['literal'],
                        'normalized_literal' => $context['normalized_literal'],
                        'selected_count' => $context['selected_count'],
                        'target_translation_key' => $translationKey,
                    ],
                );

                $updated++;
            }
        });

        $this->bulkEqualizeSelectedFindingIds = [];
        $this->closeBulkEqualizeTranslationKeyModal();
        $this->resetPage();
        $this->persistUiState();

        Flux::toast(
            heading: $updated > 0 ? __('Translation keys equalized') : __('No changes'),
            text: $updated > 0
                ? __('The selected findings now use the same translation key.')
                : __('The selected findings already used the selected translation key.'),
            variant: $updated > 0 ? 'success' : 'info',
        );
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function keyForBulkEqualizeRow(
        array $row,
        ?TranslationWorkbenchFinding $finding,
        string $translationKey,
        string $operationId,
    ): ?TranslationWorkbenchKey {
        if (! $finding) {
            return null;
        }

        $suggestedKey = $this->nullableString($row['key_suggested_key'] ?? null)
            ?? $this->nullableString($row['finding_suggested_key'] ?? null)
            ?? $this->nullableString($finding->suggested_key ?? null)
            ?? $translationKey;
        $fingerprint = app(TranslationFingerprintFactory::class)->signature([
            'foundation-key',
            '',
            $suggestedKey,
        ]);
        $key = TranslationWorkbenchKey::query()
            ->where('fingerprint', $fingerprint)
            ->first()
            ?? TranslationWorkbenchKey::query()
                ->where('suggested_key', $suggestedKey)
                ->where(function ($query): void {
                    $query
                        ->whereNull('translation_key')
                        ->orWhereRaw("NULLIF(BTRIM(translation_key), '') IS NULL");
                })
                ->first();
        $timelineRecorder = app(TranslationWorkbenchTimelineRecorder::class);

        if (! $key) {
            $keyParts = app(TranslationKeyPartsFactory::class)->fromKey($suggestedKey);
            $keySegments = app(TranslationKeySegmentFactory::class)->fromKey($suggestedKey);
            $key = TranslationWorkbenchKey::query()->create([
                'fingerprint' => $fingerprint,
                'translation_key' => null,
                'suggested_key' => $suggestedKey,
                'namespace' => $keyParts['namespace'],
                'group' => $keyParts['group'],
                'path_key' => $keyParts['path_key'],
                'scope' => $keyParts['scope'],
                ...$keySegments,
                'key_type' => str_starts_with((string) $finding->kind, 'dynamic') ? 'dynamic_candidate' : 'static_candidate',
                'status' => 'open',
                'review_status' => 'pending',
                'meta' => [
                    'source' => 'translation-workbench:bulk-equalize-missing-key',
                    'operation_id' => $operationId,
                    'finding_id' => $finding->id,
                ],
            ]);

            $timelineRecorder->recordKeyEvent(
                key: $key,
                eventType: 'key_candidate_discovered',
                newValues: $key->only([
                    'id',
                    'fingerprint',
                    'translation_key',
                    'suggested_key',
                    'namespace',
                    'group',
                    'path_key',
                    'scope',
                    'key_type',
                    'status',
                    'review_status',
                ]),
                context: [
                    'source' => 'translation-workbench:bulk-equalize-missing-key',
                    'operation_id' => $operationId,
                    'finding_id' => $finding->id,
                ],
            );
        }

        $relation = TranslationWorkbenchKeyFinding::query()
            ->where('key_id', $key->id)
            ->where('finding_id', $finding->id)
            ->where('relation_type', 'candidate')
            ->first();

        if (! $relation) {
            $relation = TranslationWorkbenchKeyFinding::query()->create([
                'key_id' => $key->id,
                'finding_id' => $finding->id,
                'relation_type' => 'candidate',
                'status' => 'active',
                'meta' => [
                    'source' => 'translation-workbench:bulk-equalize-missing-key',
                    'operation_id' => $operationId,
                ],
            ]);

            $timelineRecorder->recordKeyFindingEvent(
                key: $key,
                finding: $finding,
                eventType: 'key_finding_relation_created',
                newValues: $relation->only(['id', 'key_id', 'finding_id', 'relation_type', 'status']),
                context: [
                    'source' => 'translation-workbench:bulk-equalize-missing-key',
                    'operation_id' => $operationId,
                ],
            );

            return $key;
        }

        if ($relation->status !== 'active') {
            $oldValues = $relation->only(['status', 'meta']);
            $relation->forceFill([
                'status' => 'active',
                'meta' => [
                    ...($relation->meta ?? []),
                    'source' => 'translation-workbench:bulk-equalize-missing-key',
                    'operation_id' => $operationId,
                ],
            ])->save();

            $timelineRecorder->recordKeyFindingEvent(
                key: $key,
                finding: $finding,
                eventType: 'key_finding_relation_updated',
                oldValues: $oldValues,
                newValues: $relation->only(['status', 'meta']),
                context: [
                    'source' => 'translation-workbench:bulk-equalize-missing-key',
                    'operation_id' => $operationId,
                ],
            );
        }

        return $key;
    }

    public function openCodeUpdateConflictReview(int $findingId, int $keyId): void
    {
        $this->codeUpdateConflictFindingId = $findingId;
        $this->codeUpdateConflictKeyId = $keyId;

        $latestReview = $this->latestCodeUpdateConflictReview($findingId, $keyId);
        $this->codeUpdateConflictDecision = $latestReview?->decision ?: 'ignore_for_now';
        $this->codeUpdateConflictNote = (string) ($latestReview?->meta['note'] ?? '');
        $this->codeUpdateConflictReviewModalOpen = true;
    }

    public function closeCodeUpdateConflictReview(): void
    {
        $this->codeUpdateConflictReviewModalOpen = false;
        $this->codeUpdateConflictFindingId = null;
        $this->codeUpdateConflictKeyId = null;
        $this->codeUpdateConflictDecision = 'ignore_for_now';
        $this->codeUpdateConflictNote = '';
    }

    public function openExportConflictResolve(string $locale, string $namespace, string $langKey, string $translationKey): void
    {
        $this->exportConflictLocale = $locale;
        $this->exportConflictNamespace = $namespace;
        $this->exportConflictLangKey = $langKey;
        $this->exportConflictTranslationKey = $translationKey;
        $this->exportConflictKey = $this->exportConflictKey($locale, $namespace, $langKey, $translationKey);
        $this->exportConflictResolveModalOpen = true;
    }

    public function openExportConflictResolveByKey(string $conflictKey): void
    {
        $report = $this->langFileExportReport();
        $conflict = collect($report['conflicts'] ?? [])
            ->first(fn(array $conflict): bool => (string) ($conflict['conflict_key'] ?? '') === $conflictKey);

        if (! is_array($conflict)) {
            Flux::toast(
                heading: __('Conflict context unavailable'),
                text: __('Refresh the export report and open the conflict again.'),
                variant: 'warning',
            );

            return;
        }

        $this->exportConflictLocale = (string) ($conflict['locale'] ?? '');
        $this->exportConflictNamespace = (string) ($conflict['namespace'] ?? '');
        $this->exportConflictLangKey = (string) ($conflict['lang_key'] ?? '');
        $this->exportConflictTranslationKey = (string) ($conflict['translation_key'] ?? '');
        $this->exportConflictKey = $conflictKey;
        $this->exportConflictResolveModalOpen = true;
    }

    public function closeExportConflictResolve(): void
    {
        $this->exportConflictResolveModalOpen = false;
        $this->exportConflictLocale = '';
        $this->exportConflictNamespace = '';
        $this->exportConflictLangKey = '';
        $this->exportConflictTranslationKey = '';
        $this->exportConflictKey = '';
    }

    public function openBlockingFindingReviewFromExportConflict(int $findingId): void
    {
        $this->closeExportConflictResolve();
        $this->openReviewModal($findingId);
    }

    public function saveCodeUpdateConflictReview(): void
    {
        if (! $this->hasTables(['translation_workbench_reviews', 'translation_workbench_timeline_events'])) {
            Flux::toast(
                heading: __('Migration missing'),
                text: __('Run the Translation Workbench migrations before reviewing code update conflicts.'),
                variant: 'warning',
            );

            return;
        }

        $decision = $this->normalizedCodeUpdateConflictDecision($this->codeUpdateConflictDecision);
        $finding = $this->codeUpdateConflictFindingId
            ? TranslationWorkbenchFinding::query()->find($this->codeUpdateConflictFindingId)
            : null;
        $key = $this->codeUpdateConflictKeyId
            ? TranslationWorkbenchKey::query()->find($this->codeUpdateConflictKeyId)
            : null;

        if (! $finding || ! $key) {
            Flux::toast(
                heading: __('Conflict review unavailable'),
                text: __('The finding or key no longer exists. Refresh the apply report and try again.'),
                variant: 'warning',
            );

            $this->closeCodeUpdateConflictReview();

            return;
        }

        $oldReview = $this->latestCodeUpdateConflictReview($finding->id, $key->id);
        $oldValues = [
            'decision' => $oldReview?->decision,
            'note' => $oldReview?->meta['note'] ?? null,
        ];
        $newValues = [
            'decision' => $decision,
            'note' => trim($this->codeUpdateConflictNote),
        ];

        if ($oldValues == $newValues) {
            Flux::toast(
                heading: __('No change'),
                text: __('The code update conflict review is already set.'),
                variant: 'warning',
            );

            return;
        }

        DB::transaction(function () use ($finding, $key, $decision, $oldValues, $newValues): void {
            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key->id,
                'finding_id' => $finding->id,
                'review_type' => 'code_update_conflict',
                'decision' => $decision,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'meta' => [
                    'source' => 'translation-workbench:code-update-plan',
                    'note' => trim($this->codeUpdateConflictNote),
                    'finding_id' => $finding->id,
                    'key_id' => $key->id,
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            app(TranslationWorkbenchTimelineRecorder::class)->recordReviewEvent(
                review: $review,
                eventType: 'code_update_conflict_reviewed',
                oldValues: $oldValues,
                newValues: $newValues,
                context: [
                    'source' => 'translation-workbench:code-update-plan',
                    'decision' => $decision,
                ],
            );
        });

        $this->closeCodeUpdateConflictReview();

        Flux::toast(
            heading: __('Conflict review saved'),
            text: __('The code update conflict decision has been stored.'),
            variant: 'success',
        );
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
        $this->persistUiState();
    }

    public function toggleOverviewTabs(): void
    {
        $this->showOverviewTabs = ! $this->showOverviewTabs;
        $this->persistUiState();
    }

    public function toggleObsoleteFindings(): void
    {
        $this->showObsoleteFindings = ! $this->showObsoleteFindings;
        $this->resetPage();
        $this->persistUiState();
    }

    public function toggleFindingSearchExact(): void
    {
        $this->findingSearchExact = ! $this->findingSearchExact;
        $this->resetPage();
        $this->persistUiState();
    }

    public function toggleFindingSearchCaseSensitive(): void
    {
        $this->findingSearchCaseSensitive = ! $this->findingSearchCaseSensitive;
        $this->resetPage();
        $this->persistUiState();
    }

    public function reduceFindingSearchFirstSegment(): void
    {
        $search = trim($this->findingSearch);

        if ($search === '' || ! str_contains($search, '.')) {
            return;
        }

        $segments = collect(explode('.', $search))
            ->map(static fn(string $segment): string => trim($segment))
            ->filter(static fn(string $segment): bool => $segment !== '')
            ->values();

        if ($segments->count() <= 1) {
            return;
        }

        $this->findingSearch = $segments->skip(1)->implode('.');
        $this->findingSearchExact = false;
        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedShowOverviewTabs(): void
    {
        $this->persistUiState();
    }

    public function openReviewModal(int $findingId): void
    {
        $this->reviewFindingId = $findingId;
        $this->reviewModalOpen = true;
    }

    public function showExportReportKeyInWorkFindings(string $translationKey): void
    {
        $translationKey = trim($translationKey);

        if ($translationKey === '') {
            return;
        }

        $this->findingsActiveTab = 'work-findings';
        $this->findingSearch = $translationKey;
        $this->findingSearchExact = false;
        $this->findingSearchCaseSensitive = false;
        $this->findingStatus = 'all';
        $this->findingKind = 'all';
        $this->findingCandidateType = 'all';
        $this->findingNamespace = 'all';
        $this->findingGroup = 'all';
        $this->findingKeyRelation = 'all';
        $this->findingLiteralState = 'all';
        $this->showObsoleteFindings = true;

        $this->resetPage();
        $this->persistUiState();

        Flux::toast(
            heading: __('Work findings filtered'),
            text: __('Showing findings matching :key.', ['key' => $translationKey]),
            variant: 'success',
        );
    }

    public function openObsoleteSourceValueReview(string $translationKey): void
    {
        $translationKey = trim($translationKey);

        if ($translationKey === '') {
            return;
        }

        $this->obsoleteSourceValueTranslationKey = $translationKey;
        $this->obsoleteSourceValueReviewModalOpen = true;
    }

    public function closeObsoleteSourceValueReview(): void
    {
        $this->obsoleteSourceValueReviewModalOpen = false;
        $this->obsoleteSourceValueTranslationKey = null;
    }

    public function confirmObsoleteSourceValue(): void
    {
        $translationKey = trim((string) $this->obsoleteSourceValueTranslationKey);

        if ($translationKey === '') {
            $this->closeObsoleteSourceValueReview();

            return;
        }

        $sourceLocale = $this->sourceMainLocale();
        $langValue = TranslationWorkbenchLangValue::query()
            ->where('locale', $sourceLocale)
            ->where('translation_key', $translationKey)
            ->first();

        if (! $langValue) {
            Flux::toast(
                heading: __('Source value not found'),
                text: __('No source-language value exists for this translation key anymore.'),
                variant: 'warning',
            );

            $this->closeObsoleteSourceValueReview();

            return;
        }

        if ($langValue->status === 'obsolete') {
            Flux::toast(
                heading: __('Already obsolete'),
                text: __('This source-language value is already marked obsolete.'),
                variant: 'warning',
            );

            $this->closeObsoleteSourceValueReview();

            return;
        }

        $key = TranslationWorkbenchKey::query()
            ->where('translation_key', $translationKey)
            ->orWhere('suggested_key', $translationKey)
            ->first();

        DB::transaction(function () use ($langValue, $key, $translationKey, $sourceLocale): void {
            $oldValues = $langValue->only([
                'id',
                'locale',
                'namespace',
                'lang_key',
                'translation_key',
                'value',
                'status',
            ]);

            $langValue->forceFill([
                'status' => 'obsolete',
                'last_seen_at' => now(),
            ])->save();

            $newValues = $langValue->only([
                'id',
                'locale',
                'namespace',
                'lang_key',
                'translation_key',
                'value',
                'status',
            ]);

            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key?->id,
                'finding_id' => null,
                'review_type' => 'lang_value_obsolete',
                'decision' => 'source_value_marked_obsolete',
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'meta' => [
                    'source' => 'translation-workbench:export-report',
                    'locale' => $sourceLocale,
                    'translation_key' => $translationKey,
                    'lang_value_id' => $langValue->id,
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            app(TranslationWorkbenchTimelineRecorder::class)->recordReviewEvent(
                review: $review,
                eventType: 'source_lang_value_marked_obsolete',
                oldValues: $oldValues,
                newValues: $newValues,
                context: [
                    'source' => 'translation-workbench:export-report',
                    'locale' => $sourceLocale,
                    'translation_key' => $translationKey,
                    'lang_value_id' => $langValue->id,
                ],
            );
        });

        $reportRefreshed = $this->refreshLangFileExportReport();

        $this->closeObsoleteSourceValueReview();

        Flux::toast(
            heading: __('Source value marked obsolete'),
            text: $reportRefreshed
                ? __('The export report has been refreshed.')
                : __('The source value was updated, but the export report file could not be refreshed because it is not writable by the web process. Run the export dry-run command once to refresh it.'),
            variant: 'success',
        );
    }

    public function openPreviousReviewFinding(): void
    {
        $previousFindingId = $this->reviewAdjacentFindingId('previous');

        if ($previousFindingId !== null) {
            $this->openReviewModal($previousFindingId);
        }
    }

    public function openNextReviewFinding(): void
    {
        $nextFindingId = $this->reviewAdjacentFindingId('next');

        if ($nextFindingId !== null) {
            $this->openReviewModal($nextFindingId);
        }
    }

    public function openEditModal(int $findingId): void
    {
        $selectedFinding = $this->selectedFinding($findingId);

        if (! $selectedFinding || ! $selectedFinding->key_id) {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Review this finding and link a translation key before editing translation values.'),
                variant: 'warning',
            );

            return;
        }

        if (blank($selectedFinding->translation_key) || $selectedFinding->review_status !== 'reviewed') {
            Flux::toast(
                heading: __('Review incomplete'),
                text: __('Complete the review and set a translation key before editing translation values.'),
                variant: 'warning',
            );

            return;
        }

        if ($this->isDynamicFinding($selectedFinding)) {
            $this->openDynamicReviewModal($findingId);

            return;
        }

        $this->bootstrapEditState($selectedFinding);
        $this->reviewModalOpen = false;
        $this->editModalOpen = false;
        $this->dynamicEditModalOpen = false;
        $this->dynamicMultiEditModalOpen = false;
        $this->dynamicReviewModalOpen = false;

        $this->editModalOpen = true;
    }

    public function openDynamicReviewModal(int $findingId): void
    {
        $selectedFinding = $this->selectedFinding($findingId);

        if (! $selectedFinding) {
            return;
        }

        $this->dynamicReviewFindingId = $findingId;
        $this->reviewModalOpen = false;
        $this->editModalOpen = false;
        $this->dynamicEditModalOpen = false;
        $this->dynamicMultiEditModalOpen = false;
        $this->dynamicReviewModalOpen = true;
    }

    public function continueDynamicEdit(): void
    {
        $selectedFinding = $this->selectedFinding($this->dynamicReviewFindingId);

        if (! $selectedFinding) {
            return;
        }

        if (! $this->isDynamicReviewReady($selectedFinding)) {
            Flux::toast(
                heading: __('Dynamic review incomplete'),
                text: __('Resolve the dynamic source classification before editing dynamic translation values.'),
                variant: 'warning',
            );

            return;
        }

        if (! $selectedFinding->key_id || blank($selectedFinding->translation_key) || $selectedFinding->review_status !== 'reviewed') {
            Flux::toast(
                heading: __('Review incomplete'),
                text: __('Complete the review and set a translation key before editing translation values.'),
                variant: 'warning',
            );

            return;
        }

        $this->bootstrapEditState($selectedFinding);
        $this->editFindingId = (int) $selectedFinding->id;
        $this->dynamicReviewModalOpen = false;
        $this->editModalOpen = false;
        $this->dynamicEditModalOpen = false;
        $this->dynamicMultiEditModalOpen = false;
        $this->dynamicMultiEditModalOpen = true;
    }

    public function setDynamicReviewMode(int $findingId, string $mode): void
    {
        if (! in_array($mode, ['single', 'multi'], true)) {
            return;
        }

        $context = $this->reviewContext($findingId);

        if (! $context) {
            return;
        }

        [$key, $finding, $selectedFinding] = $context;
        $isMulti = $mode === 'multi';

        $this->saveKeyReviewDecision(
            key: $key,
            finding: $finding,
            selectedFinding: $selectedFinding,
            attributes: [
                'reviewed_is_dynamic_candidate' => true,
                'reviewed_is_ui_candidate' => false,
                'reviewed_is_dynamic_multi' => $isMulti,
                'is_dynamic_key' => true,
                'is_dynamic_candidate_rejected' => false,
                'is_dynamic_multi' => $isMulti,
                'is_ui_key' => false,
                'is_ui_candidate_rejected' => false,
                ...$this->dynamicDataStateAttributes(true),
                'key_type' => 'dynamic',
            ],
            reviewType: 'dynamic_classification',
            decision: $isMulti ? 'dynamic_multi_confirmed' : 'dynamic_single_confirmed',
            toastHeading: __('Dynamic classification updated'),
            toastText: $isMulti
                ? __('This finding is now treated as a dynamic option list.')
                : __('This finding is now treated as a single dynamic translation.'),
        );
    }

    public function openDynamicSourceLinkConfirm(int $relatedSourceId): void
    {
        $sources = collect($this->dynamicReviewSources($this->dynamicReviewFindingId));
        $relatedSource = $sources->firstWhere('id', $relatedSourceId);

        if (! $relatedSource || (bool) ($relatedSource['is_runtime_options'] ?? false)) {
            Flux::toast(
                heading: __('Link unavailable'),
                text: __('Select a related dynamic finding that should be linked to the runtime options.'),
                variant: 'warning',
            );

            return;
        }

        if ($sources->where('is_runtime_options', true)->isEmpty()) {
            Flux::toast(
                heading: __('Runtime options missing'),
                text: __('There are no runtime option rows available for this finding.'),
                variant: 'warning',
            );

            return;
        }

        $this->dynamicSourceLinkRelatedSourceId = $relatedSourceId;
        $this->dynamicSourceLinkConfirmModalOpen = true;
    }

    public function closeDynamicSourceLinkConfirm(): void
    {
        $this->dynamicSourceLinkConfirmModalOpen = false;
        $this->dynamicSourceLinkRelatedSourceId = null;
    }

    public function confirmDynamicSourceLink(): void
    {
        if (! $this->hasTables([
            'translation_workbench_dynamic_source_candidates',
            'translation_workbench_dynamic_sources',
            'translation_workbench_keys',
            'translation_workbench_findings',
        ])) {
            Flux::toast(
                heading: __('Migration missing'),
                text: __('Run the Translation Workbench migrations before saving dynamic source links.'),
                variant: 'warning',
            );

            return;
        }

        $selectedFinding = $this->selectedFinding($this->dynamicReviewFindingId);
        $preview = $this->dynamicSourceLinkPreview();

        if (! $selectedFinding || ! $preview) {
            Flux::toast(
                heading: __('Link unavailable'),
                text: __('The dynamic source link context is no longer available.'),
                variant: 'warning',
            );

            return;
        }

        $runtimeSources = collect($preview['runtime_sources'] ?? []);
        $relatedSource = $preview['related_source'] ?? null;

        if ($runtimeSources->isEmpty() || ! is_array($relatedSource)) {
            Flux::toast(
                heading: __('Link incomplete'),
                text: __('Runtime options and related dynamic finding must both be present.'),
                variant: 'warning',
            );

            return;
        }

        $key = $selectedFinding->key_id
            ? TranslationWorkbenchKey::query()->find($selectedFinding->key_id)
            : null;
        $finding = TranslationWorkbenchFinding::query()->find($selectedFinding->id);

        $createdOrUpdated = [];
        $oldValues = [];

        DB::transaction(function () use ($runtimeSources, $relatedSource, $selectedFinding, $key, $finding, &$createdOrUpdated, &$oldValues): void {
            foreach ($runtimeSources as $runtimeSource) {
                $candidateReference = 'dynamic_source:' . $relatedSource['id'];
                $candidateValues = collect($relatedSource['values'] ?? [])
                    ->mapWithKeys(static fn(array $value): array => [
                        $value['value_key'] => $value['native_label'],
                    ])
                    ->all();
                $attributes = [
                    'key_id' => $runtimeSource['key_id'] ?? $selectedFinding->key_id,
                    'finding_id' => $relatedSource['finding_id'] ?? $selectedFinding->id,
                    'suggested_key' => $relatedSource['suggested_key'] ?? $selectedFinding->suggested_key,
                    'dynamic_scope' => $relatedSource['dynamic_scope'] ?? $selectedFinding->dynamic_scope,
                    'source_expression' => $relatedSource['source_expression'] ?? null,
                    'candidate_source_type' => 'related_dynamic_source',
                    'candidate_reference' => $candidateReference,
                    'candidate_values_count' => (int) ($relatedSource['values_count'] ?? 0),
                    'candidate_values' => json_encode($candidateValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'confidence' => 'manual',
                    'review_status' => 'confirmed',
                    'status' => 'active',
                    'meta' => json_encode([
                        'source' => 'translation-workbench:dynamic-review-modal',
                        'runtime_source_id' => $runtimeSource['id'],
                        'runtime_suggested_key' => $runtimeSource['suggested_key'] ?? null,
                        'related_source_id' => $relatedSource['id'],
                        'related_suggested_key' => $relatedSource['suggested_key'] ?? null,
                        'review_finding_id' => $selectedFinding->id,
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ];

                $existing = DB::table('translation_workbench_dynamic_source_candidates')
                    ->where('dynamic_source_id', $runtimeSource['id'])
                    ->where('candidate_source_type', 'related_dynamic_source')
                    ->where('candidate_reference', $candidateReference)
                    ->first();

                if (! $existing) {
                    $candidateId = DB::table('translation_workbench_dynamic_source_candidates')->insertGetId([
                        'dynamic_source_id' => $runtimeSource['id'],
                        ...$attributes,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $createdOrUpdated[] = [
                        'id' => $candidateId,
                        'runtime_source_id' => $runtimeSource['id'],
                        'related_source_id' => $relatedSource['id'],
                        'review_status' => 'confirmed',
                    ];

                    continue;
                }

                $oldValues[] = [
                    'id' => $existing->id,
                    'review_status' => $existing->review_status,
                    'status' => $existing->status,
                    'candidate_reference' => $existing->candidate_reference,
                ];

                DB::table('translation_workbench_dynamic_source_candidates')
                    ->where('id', $existing->id)
                    ->update([
                        ...$attributes,
                        'updated_at' => now(),
                    ]);

                $createdOrUpdated[] = [
                    'id' => (int) $existing->id,
                    'runtime_source_id' => $runtimeSource['id'],
                    'related_source_id' => $relatedSource['id'],
                    'review_status' => 'confirmed',
                ];
            }

            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key?->id,
                'finding_id' => $finding?->id,
                'review_type' => 'dynamic_source_link',
                'decision' => 'runtime_options_link_confirmed',
                'old_values' => $oldValues,
                'new_values' => $createdOrUpdated,
                'meta' => [
                    'source' => 'translation-workbench:dynamic-review-modal',
                    'runtime_source_count' => $runtimeSources->count(),
                    'related_source_id' => $relatedSource['id'],
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            app(TranslationWorkbenchTimelineRecorder::class)->recordReviewEvent(
                review: $review,
                eventType: 'dynamic_source_link_confirmed',
                oldValues: $oldValues,
                newValues: $createdOrUpdated,
                context: [
                    'source' => 'translation-workbench:dynamic-review-modal',
                    'runtime_source_count' => $runtimeSources->count(),
                    'related_source_id' => $relatedSource['id'],
                ],
            );
        });

        $this->closeDynamicSourceLinkConfirm();

        Flux::toast(
            heading: __('Dynamic source link confirmed'),
            text: __('The runtime options have been linked to the related dynamic finding.'),
            variant: 'success',
        );
    }

    public function unlinkDynamicSourceLink(): void
    {
        if (! $this->hasTables([
            'translation_workbench_dynamic_source_candidates',
            'translation_workbench_dynamic_sources',
            'translation_workbench_keys',
            'translation_workbench_findings',
        ])) {
            Flux::toast(
                heading: __('Migration missing'),
                text: __('Run the Translation Workbench migrations before changing dynamic source links.'),
                variant: 'warning',
            );

            return;
        }

        $selectedFinding = $this->selectedFinding($this->dynamicReviewFindingId);
        $preview = $this->dynamicSourceLinkPreview();

        if (! $selectedFinding || ! $preview) {
            Flux::toast(
                heading: __('Link unavailable'),
                text: __('The dynamic source link context is no longer available.'),
                variant: 'warning',
            );

            return;
        }

        $runtimeSources = collect($preview['runtime_sources'] ?? []);
        $relatedSource = $preview['related_source'] ?? null;

        if ($runtimeSources->isEmpty() || ! is_array($relatedSource)) {
            Flux::toast(
                heading: __('Link incomplete'),
                text: __('Runtime options and related dynamic finding must both be present.'),
                variant: 'warning',
            );

            return;
        }

        $key = $selectedFinding->key_id
            ? TranslationWorkbenchKey::query()->find($selectedFinding->key_id)
            : null;
        $finding = TranslationWorkbenchFinding::query()->find($selectedFinding->id);
        $candidateReference = 'dynamic_source:' . $relatedSource['id'];
        $runtimeSourceIds = $runtimeSources
            ->pluck('id')
            ->map(static fn(mixed $id): int => (int) $id)
            ->all();
        $oldValues = [];
        $newValues = [];

        DB::transaction(function () use ($runtimeSourceIds, $candidateReference, $relatedSource, $runtimeSources, $key, $finding, &$oldValues, &$newValues): void {
            $candidates = DB::table('translation_workbench_dynamic_source_candidates')
                ->whereIn('dynamic_source_id', $runtimeSourceIds)
                ->where('candidate_source_type', 'related_dynamic_source')
                ->where('candidate_reference', $candidateReference)
                ->where('status', 'active')
                ->get();

            if ($candidates->isEmpty()) {
                return;
            }

            $oldValues = $candidates
                ->map(static fn(object $candidate): array => [
                    'id' => (int) $candidate->id,
                    'runtime_source_id' => (int) $candidate->dynamic_source_id,
                    'related_source_id' => (int) $relatedSource['id'],
                    'review_status' => (string) $candidate->review_status,
                    'status' => (string) $candidate->status,
                ])
                ->all();

            DB::table('translation_workbench_dynamic_source_candidates')
                ->whereIn('id', $candidates->pluck('id')->all())
                ->update([
                    'review_status' => 'unlinked',
                    'status' => 'inactive',
                    'updated_at' => now(),
                ]);

            $newValues = $candidates
                ->map(static fn(object $candidate): array => [
                    'id' => (int) $candidate->id,
                    'runtime_source_id' => (int) $candidate->dynamic_source_id,
                    'related_source_id' => (int) $relatedSource['id'],
                    'review_status' => 'unlinked',
                    'status' => 'inactive',
                ])
                ->all();

            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key?->id,
                'finding_id' => $finding?->id,
                'review_type' => 'dynamic_source_link',
                'decision' => 'runtime_options_link_unlinked',
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'meta' => [
                    'source' => 'translation-workbench:dynamic-review-modal',
                    'runtime_source_count' => $runtimeSources->count(),
                    'related_source_id' => $relatedSource['id'],
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            app(TranslationWorkbenchTimelineRecorder::class)->recordReviewEvent(
                review: $review,
                eventType: 'dynamic_source_link_unlinked',
                oldValues: $oldValues,
                newValues: $newValues,
                context: [
                    'source' => 'translation-workbench:dynamic-review-modal',
                    'runtime_source_count' => $runtimeSources->count(),
                    'related_source_id' => $relatedSource['id'],
                ],
            );
        });

        if ($newValues === []) {
            Flux::toast(
                heading: __('No active link'),
                text: __('There is no active link to remove.'),
                variant: 'warning',
            );

            return;
        }

        $this->closeDynamicSourceLinkConfirm();

        Flux::toast(
            heading: __('Dynamic source link removed'),
            text: __('The runtime options are no longer linked to the related dynamic finding.'),
            variant: 'success',
        );
    }

    public function saveDynamicTranslationValue(): void
    {
        $this->saveTranslationValue();
    }

    public function saveDynamicMultiTranslationValues(): void
    {
        if (! Schema::hasTable('translation_workbench_dynamic_key_values')) {
            Flux::toast(
                heading: __('Migration missing'),
                text: __('Run the Translation Workbench migrations before saving dynamic option translations.'),
                variant: 'warning',
            );

            return;
        }

        $selectedFinding = $this->selectedFinding($this->editFindingId);

        if (! $selectedFinding || ! $selectedFinding->key_id) {
            return;
        }

        $key = TranslationWorkbenchKey::query()->find($selectedFinding->key_id);
        $finding = TranslationWorkbenchFinding::query()->find($selectedFinding->id);

        if (! $key || ! $finding) {
            return;
        }

        $editLocales = $this->editLocales();
        $sourceLocale = LocaleCode::normalize((string) ($editLocales['source'] ?? $this->sourceMainLocale()));
        $targetLocale = LocaleCode::normalize((string) ($editLocales['active'] ?? app()->getLocale()));
        $rows = collect($this->dynamicMultiRows((int) $selectedFinding->id, $editLocales));
        $changes = [];

        DB::transaction(function () use ($selectedFinding, $rows, $sourceLocale, $targetLocale, &$changes): void {
            foreach ($rows as $row) {
                $valueKey = (string) $row['value_key'];
                $fieldKey = (string) $row['field_key'];
                $sourceValue = $this->nullableString(
                    $this->dynamicMultiSourceValues[$fieldKey] ?? $row['source'] ?? $row['native_label'] ?? null,
                );

                if ($sourceValue !== null) {
                    $sourceChange = $this->saveDynamicKeyValueForLocale(
                        selectedFinding: $selectedFinding,
                        valueKey: $valueKey,
                        locale: $sourceLocale,
                        value: $sourceValue,
                        nativeLabel: $this->nullableString($row['native_label'] ?? $sourceValue),
                        source: 'runtime_source',
                    );

                    if ($sourceChange !== null) {
                        $changes[] = $sourceChange;
                    }
                }

                $targetChange = $this->saveDynamicKeyValueForLocale(
                    selectedFinding: $selectedFinding,
                    valueKey: $valueKey,
                    locale: $targetLocale,
                    value: $this->dynamicMultiTargetValues[$fieldKey] ?? null,
                    nativeLabel: $this->nullableString($row['native_label'] ?? $sourceValue),
                    source: 'translation_workbench_modal',
                );

                if ($targetChange !== null) {
                    $changes[] = $targetChange;
                }
            }
        });

        if ($changes === []) {
            Flux::toast(
                heading: __('No changes'),
                text: __('The dynamic option translations have not changed.'),
                variant: 'info',
            );

            return;
        }

        $this->bootstrapDynamicMultiEditState($selectedFinding, $editLocales);

        app(TranslationWorkbenchTimelineRecorder::class)->recordKeyFindingEvent(
            key: $key,
            finding: $finding,
            eventType: 'dynamic_multi_values_saved',
            oldValues: ['values' => collect($changes)->mapWithKeys(static fn(array $change): array => [
                $change['value_key'] . ':' . $change['locale'] => $change['old'],
            ])->all()],
            newValues: ['values' => collect($changes)->mapWithKeys(static fn(array $change): array => [
                $change['value_key'] . ':' . $change['locale'] => $change['new'],
            ])->all()],
            context: [
                'source' => 'translation-workbench:modal-edit-dynamic-multi',
                'locales' => collect($changes)->pluck('locale')->unique()->values()->all(),
                'value_keys' => collect($changes)->pluck('value_key')->unique()->values()->all(),
            ],
        );

        Flux::toast(
            heading: __('Dynamic options saved'),
            text: __('The dynamic option translations have been saved.'),
            variant: 'success',
        );
    }

    public function saveTranslationValue(): void
    {
        $selectedFinding = $this->selectedFinding($this->editFindingId);

        if (! $selectedFinding || ! $selectedFinding->key_id) {
            return;
        }

        $translationKey = $this->nullableString($selectedFinding->translation_key);

        if ($translationKey === null) {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Review this finding and set a translation key before editing translation values.'),
                variant: 'warning',
            );

            return;
        }

        $key = TranslationWorkbenchKey::query()->find($selectedFinding->key_id);
        $finding = TranslationWorkbenchFinding::query()->find($selectedFinding->id);

        if (! $key || ! $finding) {
            return;
        }

        $editLocales = $this->editLocales();
        $sourceLocale = (string) ($editLocales['source'] ?? $this->sourceMainLocale());
        $targetLocale = (string) ($editLocales['active'] ?? app()->getLocale());
        $editValues = $this->editValues($selectedFinding, $editLocales);
        $changes = [];

        DB::transaction(function () use ($selectedFinding, $translationKey, $sourceLocale, $targetLocale, $editValues, &$changes): void {
            $sourceChange = $this->saveTranslationValueForLocale(
                selectedFinding: $selectedFinding,
                translationKey: $translationKey,
                locale: $sourceLocale,
                value: $this->sourceTranslationValue,
            );

            if ($sourceChange !== null) {
                $changes[] = $sourceChange;
            }

            $targetChange = $this->saveTranslationValueForLocale(
                selectedFinding: $selectedFinding,
                translationKey: $translationKey,
                locale: $targetLocale,
                value: $this->targetTranslationValue,
            );

            if ($targetChange !== null) {
                $changes[] = $targetChange;
            }

            $availableSubLocales = collect((array) ($this->editLocales()['sub'] ?? []))
                ->filter(static fn(mixed $locale): bool => is_string($locale) && trim($locale) !== '')
                ->map(static fn(string $locale): string => LocaleCode::normalize($locale))
                ->values();

            foreach ($this->selectedTargetSubLocales as $subLocale) {
                $subLocale = LocaleCode::normalize((string) $subLocale);

                if (! $availableSubLocales->contains($subLocale)) {
                    continue;
                }

                $subChange = $this->saveTranslationValueForLocale(
                    selectedFinding: $selectedFinding,
                    translationKey: $translationKey,
                    locale: $subLocale,
                    value: $this->targetSubTranslationValues[$subLocale] ?? null,
                );

                if ($subChange !== null) {
                    $changes[] = $subChange;
                }
            }
        });

        if ($changes === []) {
            Flux::toast(
                heading: __('No changes'),
                text: __('The translation values have not changed.'),
                variant: 'info',
            );

            return;
        }

        app(TranslationWorkbenchTimelineRecorder::class)->recordKeyFindingEvent(
            key: $key,
            finding: $finding,
            eventType: 'translation_values_saved',
            oldValues: ['values' => collect($changes)->pluck('old', 'locale')->all()],
            newValues: ['values' => collect($changes)->pluck('new', 'locale')->all()],
            context: [
                'source' => 'translation-workbench:modal-edit',
                'locales' => collect($changes)->pluck('locale')->values()->all(),
            ],
        );

        Flux::toast(
            heading: __('Translations saved'),
            text: __('The translation values have been saved.'),
            variant: 'success',
        );

        $this->bulkEqualizeReminderPending = $this->shouldRemindBulkEqualizeAfterTranslationSave((int) $selectedFinding->id);
        $this->editModalAutoCloseCountdown = $this->editModalAutoCloseAfterSave ? 3 : 0;
    }

    public function toggleTargetSubLocale(string $locale): void
    {
        $locale = LocaleCode::normalize($locale);

        if ($locale === '') {
            return;
        }

        $availableSubLocales = collect((array) ($this->editLocales()['sub'] ?? []))
            ->filter(static fn(mixed $subLocale): bool => is_string($subLocale) && trim($subLocale) !== '')
            ->map(static fn(string $subLocale): string => LocaleCode::normalize($subLocale));

        if (! $availableSubLocales->contains($locale)) {
            return;
        }

        if (in_array($locale, $this->selectedTargetSubLocales, true)) {
            $this->selectedTargetSubLocales = array_values(array_filter(
                $this->selectedTargetSubLocales,
                static fn(string $selectedLocale): bool => $selectedLocale !== $locale,
            ));

            return;
        }

        $this->selectedTargetSubLocales[] = $locale;
        $this->selectedTargetSubLocales = array_values(array_unique($this->selectedTargetSubLocales));
        $this->targetSubTranslationValues[$locale] ??= $this->targetSubTranslationValues(
            $this->selectedFinding($this->editFindingId),
            $this->editLocales(),
        )[$locale] ?? null;
    }

    public function copySourceToTargetValue(): void
    {
        $selectedFinding = $this->selectedFinding($this->editFindingId);

        if (! $selectedFinding) {
            return;
        }

        $sourceValue = $this->nullableString($this->sourceTranslationValue);

        if ($sourceValue === null) {
            Flux::toast(
                heading: __('No source value'),
                text: __('There is no source value to copy.'),
                variant: 'warning',
            );

            return;
        }

        $this->targetTranslationValue = $sourceValue;
        $this->sourceTranslationEditable = false;

        $this->dispatch('buergerfrs:focus-field-and-select', inputId: 'translation-workbench-target-translation-value');
    }

    public function copyDynamicMultiSourceToTarget(string $fieldKey): void
    {
        $row = collect($this->dynamicMultiRows($this->editFindingId, $this->editLocales()))
            ->firstWhere('field_key', $fieldKey);

        if (! is_array($row)) {
            return;
        }

        $sourceValue = $this->nullableString(
            $this->dynamicMultiSourceValues[$fieldKey] ?? $row['source'] ?? $row['native_label'] ?? null,
        );

        if ($sourceValue === null) {
            Flux::toast(
                heading: __('No source value'),
                text: __('There is no source value to copy for this option.'),
                variant: 'warning',
            );

            return;
        }

        $this->dynamicMultiTargetValues[$fieldKey] = $sourceValue;
        $this->dynamicMultiSourceEqualsTargetOverrides[$fieldKey] = false;
        $this->dynamicMultiEditableTargetFields[$fieldKey] = true;

        $this->dispatch(
            'buergerfrs:focus-field-and-select',
            inputId: 'translation-workbench-dynamic-multi-target-' . $fieldKey,
        );
    }

    public function copyAllDynamicMultiSourceToTarget(): void
    {
        $copied = 0;
        $skippedExistingTargets = 0;

        foreach ($this->dynamicMultiRows($this->editFindingId, $this->editLocales()) as $row) {
            $fieldKey = (string) ($row['field_key'] ?? '');
            $sourceValue = $this->nullableString(
                $this->dynamicMultiSourceValues[$fieldKey] ?? $row['source'] ?? $row['native_label'] ?? null,
            );

            if ($fieldKey === '' || $sourceValue === null) {
                continue;
            }

            if ($this->nullableString($this->dynamicMultiTargetValues[$fieldKey] ?? $row['target'] ?? null) !== null) {
                $skippedExistingTargets++;

                continue;
            }

            $this->dynamicMultiTargetValues[$fieldKey] = $sourceValue;
            $this->dynamicMultiSourceEqualsTargetOverrides[$fieldKey] = false;
            $this->dynamicMultiEditableTargetFields[$fieldKey] = true;
            $copied++;
        }

        if ($copied === 0) {
            if ($skippedExistingTargets > 0) {
                Flux::toast(
                    heading: __('No empty target values'),
                    text: __('Existing target translations were kept unchanged.'),
                    variant: 'info',
                );

                return;
            }

            Flux::toast(
                heading: __('No source values'),
                text: __('There are no source values to copy into the target language.'),
                variant: 'warning',
            );
        }
    }

    public function overrideDynamicMultiSourceEqualsTarget(string $fieldKey): void
    {
        if (! array_key_exists($fieldKey, $this->dynamicMultiTargetValues)) {
            return;
        }

        $this->dynamicMultiSourceEqualsTargetOverrides[$fieldKey] = true;
    }

    public function overrideAllDynamicMultiSourceEqualsTarget(): void
    {
        $overridden = 0;

        foreach ($this->dynamicMultiRows($this->editFindingId, $this->editLocales()) as $row) {
            $fieldKey = (string) ($row['field_key'] ?? '');

            if ($fieldKey === '' || ! array_key_exists($fieldKey, $this->dynamicMultiTargetValues)) {
                continue;
            }

            $sourceValue = $this->nullableString(
                $this->dynamicMultiSourceValues[$fieldKey] ?? $row['source'] ?? $row['native_label'] ?? null,
            );
            $targetValue = $this->nullableString($this->dynamicMultiTargetValues[$fieldKey] ?? $row['target'] ?? null);

            if ($sourceValue === null || $targetValue === null || $sourceValue !== $targetValue) {
                continue;
            }

            if ((bool) ($this->dynamicMultiSourceEqualsTargetOverrides[$fieldKey] ?? false)) {
                continue;
            }

            $this->dynamicMultiSourceEqualsTargetOverrides[$fieldKey] = true;
            $overridden++;
        }

        Flux::toast(
            heading: $overridden > 0 ? __('Overrides accepted') : __('No overrides needed'),
            text: $overridden > 0
                ? trans_choice('{1} One matching source/target value was accepted.|[2,*] :count matching source/target values were accepted.', $overridden, ['count' => $overridden])
                : __('There are no unresolved source/target matches to accept.'),
            variant: $overridden > 0 ? 'success' : 'info',
        );
    }

    public function editDynamicMultiTargetValue(string $fieldKey): void
    {
        if (! array_key_exists($fieldKey, $this->dynamicMultiTargetValues)) {
            return;
        }

        $this->dynamicMultiEditableTargetFields[$fieldKey] = true;

        $this->dispatch(
            'buergerfrs:focus-field-and-select',
            inputId: 'translation-workbench-dynamic-multi-target-' . $fieldKey,
        );
    }

    public function editDynamicMultiSourceValue(string $fieldKey): void
    {
        if (! array_key_exists($fieldKey, $this->dynamicMultiSourceValues)) {
            return;
        }

        $this->dynamicMultiEditableSourceFields[$fieldKey] = true;

        $this->dispatch(
            'buergerfrs:focus-field-and-select',
            inputId: 'translation-workbench-dynamic-multi-source-' . $fieldKey,
        );
    }

    public function saveDynamicMultiSourceValue(string $fieldKey): void
    {
        if (! Schema::hasTable('translation_workbench_dynamic_key_values')) {
            Flux::toast(
                heading: __('Migration missing'),
                text: __('Run the Translation Workbench migrations before saving dynamic option source values.'),
                variant: 'warning',
            );

            return;
        }

        $selectedFinding = $this->selectedFinding($this->editFindingId);

        if (! $selectedFinding || ! $selectedFinding->key_id || ! array_key_exists($fieldKey, $this->dynamicMultiSourceValues)) {
            return;
        }

        $row = collect($this->dynamicMultiRows($this->editFindingId, $this->editLocales()))
            ->firstWhere('field_key', $fieldKey);

        if (! is_array($row)) {
            return;
        }

        $key = TranslationWorkbenchKey::query()->find($selectedFinding->key_id);
        $finding = TranslationWorkbenchFinding::query()->find($selectedFinding->id);

        if (! $key || ! $finding) {
            return;
        }

        $editLocales = $this->editLocales();
        $sourceLocale = LocaleCode::normalize((string) ($editLocales['source'] ?? $this->sourceMainLocale()));
        $valueKey = (string) ($row['value_key'] ?? $this->dynamicMultiValueKeyMap[$fieldKey] ?? '');
        $sourceValue = $this->nullableString($this->dynamicMultiSourceValues[$fieldKey] ?? null);
        $change = $this->saveDynamicKeyValueForLocale(
            selectedFinding: $selectedFinding,
            valueKey: $valueKey,
            locale: $sourceLocale,
            value: $sourceValue,
            nativeLabel: $sourceValue ?? $this->nullableString($row['native_label'] ?? null),
            source: 'translation_workbench_source_correction',
        );

        if ($change === null) {
            $this->dynamicMultiEditableSourceFields[$fieldKey] = false;
            Flux::toast(
                heading: __('No changes'),
                text: __('The source option value has not changed.'),
                variant: 'info',
            );

            return;
        }

        app(TranslationWorkbenchTimelineRecorder::class)->recordKeyFindingEvent(
            key: $key,
            finding: $finding,
            eventType: 'dynamic_multi_source_value_saved',
            oldValues: ['values' => [$change['value_key'] . ':' . $change['locale'] => $change['old']]],
            newValues: ['values' => [$change['value_key'] . ':' . $change['locale'] => $change['new']]],
            context: [
                'source' => 'translation-workbench:modal-edit-dynamic-multi',
                'locale' => $sourceLocale,
                'value_key' => $valueKey,
            ],
        );

        $this->bootstrapDynamicMultiEditState($selectedFinding, $editLocales);

        Flux::toast(
            heading: __('Source value saved'),
            text: __('The source option value was updated.'),
            variant: 'success',
        );
    }

    public function editSourceTranslationValue(): void
    {
        $selectedFinding = $this->selectedFinding($this->editFindingId);

        if (! $selectedFinding) {
            return;
        }

        $this->sourceTranslationValue ??= $this->editValues($selectedFinding, $this->editLocales())['source'];
        $this->sourceTranslationEditable = true;

        $this->dispatch('buergerfrs:focus-field-and-select', inputId: 'translation-workbench-source-translation-value');
    }

    public function openTimelineModal(int $findingId): void
    {
        $this->timelineFindingId = $findingId;
        $this->timelineModalOpen = true;
    }

    /**
     * @return array<int, array{
     *     id: int,
     *     event_type: string,
     *     label: string,
     *     category: string,
     *     severity: string,
     *     color: string,
     *     key_id: int|null,
     *     finding_id: int|null,
     *     review_id: int|null,
     *     created_by: string|null,
     *     created_at: mixed,
     *     created_at_human: string,
     *     created_at_ago: string,
     *     origin_label: string,
     *     origin_icon: string,
     *     context_label: string|null,
     *     change_rows: array<int, array{key: string, label: string, old: string, new: string, old_title: string|null, new_title: string|null, importance: string, key_class: string, old_class: string, new_class: string}>,
     *     relationship_rows: array<int, array{key: string, label: string, old: string, new: string, old_title: string|null, new_title: string|null, importance: string, key_class: string, old_class: string, new_class: string}>,
     *     relationship_count: int,
     *     hidden_change_rows: array<int, array{key: string, label: string, old: string, new: string, old_title: string|null, new_title: string|null, importance: string, key_class: string, old_class: string, new_class: string}>,
     *     hidden_change_count: int
     * }>
     */
    private function timelineRows(?int $findingId): array
    {
        if ($findingId === null || ! $this->hasTables([
            'translation_workbench_timeline_events',
            'translation_workbench_findings',
        ])) {
            return [];
        }

        $selectedFinding = $this->selectedFinding($findingId);

        if (! $selectedFinding) {
            return [];
        }

        $query = DB::table('translation_workbench_timeline_events as timeline_events')
            ->leftJoin('translation_workbench_event_types as event_types', 'event_types.id', '=', 'timeline_events.event_type_id')
            ->leftJoin('users', 'users.id', '=', 'timeline_events.created_by_user_id')
            ->where(function ($query) use ($selectedFinding): void {
                $query->where('timeline_events.finding_id', $selectedFinding->id);

                if ($selectedFinding->key_id !== null) {
                    $query->orWhere('timeline_events.key_id', $selectedFinding->key_id);
                }
            })
            ->orderByDesc('timeline_events.created_at')
            ->orderByDesc('timeline_events.id')
            ->limit(100)
            ->select([
                'timeline_events.id',
                'timeline_events.event_type',
                'timeline_events.key_id',
                'timeline_events.finding_id',
                'timeline_events.review_id',
                'timeline_events.old_values',
                'timeline_events.new_values',
                'timeline_events.context',
                'timeline_events.created_at',
                'event_types.label as event_label',
                'event_types.category as event_category',
                'event_types.severity as event_severity',
                'event_types.color as event_color',
                'users.name as created_by_name',
                'users.email as created_by_email',
            ]);

        if (! $this->schemaHasTable('translation_workbench_event_types')) {
            $query = DB::table('translation_workbench_timeline_events as timeline_events')
                ->leftJoin('users', 'users.id', '=', 'timeline_events.created_by_user_id')
                ->where(function ($query) use ($selectedFinding): void {
                    $query->where('timeline_events.finding_id', $selectedFinding->id);

                    if ($selectedFinding->key_id !== null) {
                        $query->orWhere('timeline_events.key_id', $selectedFinding->key_id);
                    }
                })
                ->orderByDesc('timeline_events.created_at')
                ->orderByDesc('timeline_events.id')
                ->limit(100)
                ->select([
                    'timeline_events.id',
                    'timeline_events.event_type',
                    'timeline_events.key_id',
                    'timeline_events.finding_id',
                    'timeline_events.review_id',
                    'timeline_events.old_values',
                    'timeline_events.new_values',
                    'timeline_events.context',
                    'timeline_events.created_at',
                    DB::raw('null as event_label'),
                    DB::raw('null as event_category'),
                    DB::raw('null as event_severity'),
                    DB::raw('null as event_color'),
                    'users.name as created_by_name',
                    'users.email as created_by_email',
                ]);
        }

        return $query
            ->get()
            ->map(fn(object $row): array => $this->timelineRow($row))
            ->all();
    }

    /**
     * @return array{
     *     id: int,
     *     event_type: string,
     *     label: string,
     *     category: string,
     *     severity: string,
     *     color: string,
     *     key_id: int|null,
     *     finding_id: int|null,
     *     review_id: int|null,
     *     created_by: string|null,
     *     created_at: mixed,
     *     created_at_human: string,
     *     created_at_ago: string,
     *     origin_label: string,
     *     origin_icon: string,
     *     context_label: string|null,
     *     change_rows: array<int, array{key: string, label: string, old: string, new: string, old_title: string|null, new_title: string|null, importance: string, key_class: string, old_class: string, new_class: string}>,
     *     hidden_change_rows: array<int, array{key: string, label: string, old: string, new: string, old_title: string|null, new_title: string|null, importance: string, key_class: string, old_class: string, new_class: string}>,
     *     hidden_change_count: int
     * }
     */
    private function timelineRow(object $row): array
    {
        $eventType = (string) ($row->event_type ?? '');
        $createdBy = $this->nullableString($row->created_by_name ?? null)
            ?? $this->nullableString($row->created_by_email ?? null);
        $createdAt = filled($row->created_at) ? Carbon::parse($row->created_at) : null;
        $context = $this->timelineDecodeValues($row->context ?? null);
        $origin = $this->timelineOrigin($eventType, $row->event_category ?? null, $createdBy, $context);
        $changeRows = collect($this->timelineChangeRows($eventType, $row->old_values ?? null, $row->new_values ?? null));
        $relationshipRows = $changeRows
            ->filter(fn(array $change): bool => $this->timelineRelationshipKey((string) $change['key']))
            ->map(fn(array $change): array => $this->timelineRelationshipRow($change))
            ->values();

        return [
            'id' => (int) $row->id,
            'event_type' => $eventType,
            'label' => $this->nullableString($row->event_label ?? null)
                ?? str($eventType)->replace('_', ' ')->title()->toString(),
            'category' => $this->nullableString($row->event_category ?? null) ?? 'system',
            'severity' => $this->nullableString($row->event_severity ?? null) ?? 'info',
            'color' => $this->timelineColor($row->event_color ?? null, $row->event_severity ?? null),
            'key_id' => $row->key_id !== null ? (int) $row->key_id : null,
            'finding_id' => $row->finding_id !== null ? (int) $row->finding_id : null,
            'review_id' => $row->review_id !== null ? (int) $row->review_id : null,
            'created_by' => $createdBy,
            'created_at' => $row->created_at,
            'created_at_human' => $createdAt?->toDateTimeString() ?? '',
            'created_at_ago' => $createdAt?->diffForHumans() ?? '',
            'origin_label' => $origin['label'],
            'origin_icon' => $origin['icon'],
            'context_label' => $this->timelineContextLabel($context),
            'change_rows' => $changeRows
                ->where('importance', 'primary')
                ->reject(fn(array $change): bool => $this->timelineRelationshipKey((string) $change['key']))
                ->values()
                ->all(),
            'relationship_rows' => $relationshipRows->all(),
            'relationship_count' => $relationshipRows->count(),
            'hidden_change_rows' => $changeRows
                ->where('importance', '!=', 'primary')
                ->reject(fn(array $change): bool => $this->timelineRelationshipKey((string) $change['key']))
                ->values()
                ->all(),
            'hidden_change_count' => $changeRows
                ->where('importance', '!=', 'primary')
                ->reject(fn(array $change): bool => $this->timelineRelationshipKey((string) $change['key']))
                ->count(),
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{label: string, icon: string}
     */
    private function timelineOrigin(string $eventType, mixed $category, ?string $createdBy, array $context): array
    {
        if ($createdBy !== null) {
            return ['label' => __('UI'), 'icon' => 'square-pen'];
        }

        $category = $this->nullableString($category);
        $source = $this->nullableString($context['source'] ?? null);

        if (
            in_array($category, ['scanner', 'system'], true)
            || str_contains($eventType, 'scan')
            || str_contains($eventType, 'sync')
            || str_contains($eventType, 'classified')
            || str_contains((string) $source, 'scanner')
            || str_contains((string) $source, 'command')
        ) {
            return ['label' => __('Scanner'), 'icon' => 'scan-search'];
        }

        if ($category === 'translation' || str_contains($eventType, 'value')) {
            return ['label' => __('Translation'), 'icon' => 'languages'];
        }

        if ($category === 'review' || str_contains($eventType, 'review')) {
            return ['label' => __('Review'), 'icon' => 'badge-check'];
        }

        return ['label' => __('System'), 'icon' => 'activity'];
    }

    /**
     * @return array<int, array{key: string, label: string, old: string, new: string, old_title: string|null, new_title: string|null, importance: string, key_class: string, old_class: string, new_class: string}>
     */
    private function timelineChangeRows(string $eventType, mixed $oldValues, mixed $newValues): array
    {
        $oldValues = $this->timelineDecodeValues($oldValues);
        $newValues = $this->timelineDecodeValues($newValues);
        $technicalKeys = ['created_at', 'updated_at', 'first_seen_at', 'last_seen_at'];
        $presentation = $this->timelinePresentation($eventType);

        return collect(array_unique([...array_keys($oldValues), ...array_keys($newValues)]))
            ->reject(static fn(string $key): bool => in_array($key, $technicalKeys, true))
            ->map(function (string $key) use ($oldValues, $newValues, $presentation): array {
                $hasOld = array_key_exists($key, $oldValues);
                $hasNew = array_key_exists($key, $newValues);
                $importance = $this->timelineChangeImportance($key, $presentation);

                return [
                    'key' => $key,
                    'label' => str($key)->replace('_', ' ')->title()->toString(),
                    'old' => $hasOld ? $this->timelineDisplayValue($oldValues[$key], $key) : 'N.i.V.',
                    'new' => $hasNew ? $this->timelineDisplayValue($newValues[$key], $key) : __('Cleared'),
                    'old_title' => $hasOld ? null : __('No initial value. This field did not have a stored value before this event.'),
                    'new_title' => $hasNew ? null : __('The value was removed by this event.'),
                    'importance' => $importance,
                    'key_class' => $this->timelineChangeKeyClass($importance),
                    'old_class' => $this->timelineChangeValueClass($importance, oldValue: true),
                    'new_class' => $this->timelineChangeValueClass($importance, oldValue: false),
                ];
            })
            ->sortBy(fn(array $row): array => $this->timelineChangeSortValue($row, $presentation))
            ->values()
            ->all();
    }

    /**
     * @return array{primary: array<int, string>, secondary: array<int, string>}
     */
    private function timelinePresentation(string $eventType): array
    {
        $events = (array) config('translation-workbench.timeline_presentation.events', []);
        $eventRules = (array) ($events[$eventType] ?? []);

        return [
            'primary' => $this->timelinePresentationList($eventRules['primary'] ?? []),
            'secondary' => $this->timelinePresentationList($eventRules['secondary'] ?? []),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function timelinePresentationList(mixed $fields): array
    {
        return collect(is_array($fields) ? $fields : [])
            ->map(fn(mixed $field): string => (string) $field)
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  array{primary: array<int, string>, secondary: array<int, string>}  $presentation
     */
    private function timelineChangeImportance(string $key, array $presentation): string
    {
        return match (true) {
            in_array($key, $presentation['primary'], true) => 'primary',
            in_array($key, $presentation['secondary'], true) => 'secondary',
            default => 'muted',
        };
    }

    private function timelineChangeImportanceRank(string $importance): int
    {
        return match ($importance) {
            'primary' => 0,
            'secondary' => 1,
            'muted' => 2,
            default => 3,
        };
    }

    /**
     * @param  array{key: string, label: string, old: string, new: string, old_title: string|null, new_title: string|null, importance: string, key_class: string, old_class: string, new_class: string}  $row
     * @param  array{primary: array<int, string>, secondary: array<int, string>}  $presentation
     * @return array{0: int, 1: int, 2: string}
     */
    private function timelineChangeSortValue(array $row, array $presentation): array
    {
        $importance = (string) $row['importance'];
        $configuredOrder = match ($importance) {
            'primary' => array_search($row['key'], $presentation['primary'], true),
            'secondary' => array_search($row['key'], $presentation['secondary'], true),
            default => false,
        };

        return [
            $this->timelineChangeImportanceRank($importance),
            $configuredOrder === false ? 999 : (int) $configuredOrder,
            (string) $row['label'],
        ];
    }

    private function timelineChangeKeyClass(string $importance): string
    {
        return match ($importance) {
            'primary' => 'wrap-anywhere col-span-2 min-w-0 font-semibold text-red-700 dark:text-red-300',
            'secondary' => 'wrap-anywhere col-span-2 min-w-0 font-medium text-amber-700 dark:text-amber-300',
            'muted' => 'wrap-anywhere col-span-2 min-w-0 text-zinc-400 dark:text-zinc-500',
            default => 'wrap-anywhere col-span-2 min-w-0 text-zinc-500 dark:text-zinc-400',
        };
    }

    private function timelineChangeValueClass(string $importance, bool $oldValue): string
    {
        return match (true) {
            $importance === 'primary' && ! $oldValue => 'wrap-anywhere col-span-4 min-w-0 font-mono font-semibold text-red-800 dark:text-red-200',
            $importance === 'primary' => 'wrap-anywhere col-span-3 min-w-0 font-mono font-medium text-zinc-600 dark:text-zinc-300',
            $importance === 'secondary' && ! $oldValue => 'wrap-anywhere col-span-4 min-w-0 font-mono text-amber-800 dark:text-amber-200',
            $importance === 'secondary' => 'wrap-anywhere col-span-3 min-w-0 font-mono text-amber-700 dark:text-amber-300',
            $importance === 'muted' && ! $oldValue => 'wrap-anywhere col-span-4 min-w-0 font-mono text-zinc-500 dark:text-zinc-400',
            $importance === 'muted' => 'wrap-anywhere col-span-3 min-w-0 font-mono text-zinc-400 dark:text-zinc-500',
            ! $oldValue => 'wrap-anywhere col-span-4 min-w-0 font-mono text-zinc-800 dark:text-zinc-200',
            default => 'wrap-anywhere col-span-3 min-w-0 font-mono text-zinc-500 dark:text-zinc-400',
        };
    }

    private function timelineRelationshipKey(string $key): bool
    {
        return $key === 'id'
            || str_ends_with($key, '_id')
            || in_array($key, ['lang_key', 'translation_key'], true);
    }

    /**
     * @param  array{key: string, label: string, old: string, new: string, old_title: string|null, new_title: string|null, importance: string, key_class: string, old_class: string, new_class: string}  $row
     * @return array{key: string, label: string, old: string, new: string, old_title: string|null, new_title: string|null, importance: string, key_class: string, old_class: string, new_class: string}
     */
    private function timelineRelationshipRow(array $row): array
    {
        return [
            ...$row,
            'key_class' => 'wrap-anywhere col-span-2 min-w-0 font-semibold text-sky-700 dark:text-sky-300',
            'old_class' => 'wrap-anywhere col-span-3 min-w-0 font-mono text-sky-600 dark:text-sky-400',
            'new_class' => 'wrap-anywhere col-span-4 min-w-0 font-mono font-medium text-sky-800 dark:text-sky-200',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function timelineDecodeValues(mixed $values): array
    {
        if (is_string($values)) {
            $decoded = json_decode($values, true);
            $values = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return is_array($values) ? $values : [];
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function timelineContextLabel(array $context): ?string
    {
        $sourcePath = $this->nullableString($context['source_path'] ?? null);
        $sourceLine = $this->nullableString($context['source_line'] ?? null);

        if ($sourcePath !== null) {
            return $sourcePath . ($sourceLine !== null ? ':' . $sourceLine : '');
        }

        return $this->nullableString($context['source'] ?? null);
    }

    private function timelineDisplayValue(mixed $value, ?string $key = null): string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            $value = (string) $value;

            return $this->timelineKeepsValueEnd((string) $key)
                ? $this->timelineDisplayTailValue($value)
                : str($value)->limit(80)->toString();
        }

        return str(json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '')->limit(80)->toString();
    }

    private function timelineKeepsValueEnd(string $key): bool
    {
        return str_ends_with($key, '_key')
            || str_ends_with($key, '_path')
            || str_ends_with($key, '_signature')
            || $key === 'path'
            || $key === 'fingerprint'
            || $key === 'raw_expression';
    }

    private function timelineDisplayTailValue(string $value): string
    {
        if (mb_strlen($value) <= 96) {
            return $value;
        }

        return '...' . mb_substr($value, -93);
    }

    private function timelineColor(mixed $color, mixed $severity): string
    {
        $color = $this->nullableString($color);

        if ($color !== null) {
            return $color === 'red' ? 'rose' : $color;
        }

        return match ($this->nullableString($severity)) {
            'danger', 'error' => 'rose',
            'warning' => 'amber',
            'success' => 'green',
            default => 'sky',
        };
    }

    public function openTranslationKeyModal(int $findingId): void
    {
        $selectedFinding = $this->selectedFinding($findingId);

        if (! $selectedFinding || ! $selectedFinding->key_id) {
            Flux::toast(
                heading: __('No linked key'),
                text: __('This finding needs a key relation before its translation key can be edited.'),
                variant: 'warning',
            );

            return;
        }

        $this->translationKeyFindingId = $findingId;
        $this->translationKeyValue = $this->nullableString($selectedFinding->translation_key);
        $this->translationKeySegmentBaseValue = $this->translationKeyValue;
        $this->translationKeyDeletedSegments = [];
        $this->translationKeyModalOpen = true;
    }

    public function copySuggestedKeyToTranslationKeyModal(): void
    {
        $selectedFinding = $this->selectedFinding($this->translationKeyFindingId);

        if (! $selectedFinding) {
            return;
        }

        $suggestedKey = $this->nullableString($selectedFinding->key_suggested_key ?: $selectedFinding->suggested_key);

        if ($suggestedKey === null) {
            Flux::toast(
                heading: __('No suggested key'),
                text: __('There is no suggested key available for this finding.'),
                variant: 'warning',
            );

            return;
        }

        $this->translationKeyValue = $suggestedKey;
        $this->translationKeySegmentBaseValue = $suggestedKey;
        $this->translationKeyDeletedSegments = [];
    }

    public function transformSuggestedKeyToUiTranslationKeyModal(): void
    {
        $selectedFinding = $this->selectedFinding($this->translationKeyFindingId);

        if (! $selectedFinding) {
            return;
        }

        if ($this->isDynamicTranslationFinding($selectedFinding)) {
            Flux::toast(
                heading: __('Dynamic translation key'),
                text: __('Dynamic translation keys cannot be transformed into UI keys.'),
                variant: 'warning',
            );

            return;
        }

        $suggestedKey = $this->nullableString($selectedFinding->key_suggested_key ?: $selectedFinding->suggested_key);

        if ($suggestedKey === null) {
            Flux::toast(
                heading: __('No suggested key'),
                text: __('There is no suggested key available for this finding.'),
                variant: 'warning',
            );

            return;
        }

        $segments = $this->translationKeySegments($suggestedKey);
        $lastSegment = array_pop($segments);

        if ($lastSegment === null) {
            return;
        }

        $this->translationKeyValue = 'ui.'.str_replace('_', '-', $lastSegment);
        $this->translationKeySegmentBaseValue = $suggestedKey;
        $this->translationKeyDeletedSegments = $segments;
    }

    private function isDynamicTranslationFinding(object $selectedFinding): bool
    {
        $isDynamicConfirmed = (bool) ($selectedFinding->is_dynamic_key ?? false);
        $isDynamicCandidate = (($selectedFinding->reviewed_is_dynamic_candidate ?? null) !== null)
            ? (bool) $selectedFinding->reviewed_is_dynamic_candidate
            : (($selectedFinding->candidate_type ?? null) === 'dynamic'
                || ($selectedFinding->entry_type ?? null) === 'dynamic'
                || ($selectedFinding->kind ?? null) === 'dynamic_multi');

        return $isDynamicConfirmed
            || $isDynamicCandidate
            || (bool) ($selectedFinding->is_dynamic_multi ?? false)
            || (bool) ($selectedFinding->reviewed_is_dynamic_multi ?? false);
    }

    public function deleteFirstTranslationKeySegmentModal(): void
    {
        if ($this->translationKeySegmentControls()['disable_segment_buttons']) {
            return;
        }

        $segments = $this->translationKeySegments($this->translationKeyValue);

        if (count($segments) <= 1) {
            return;
        }

        $deletedSegment = array_shift($segments);

        if ($deletedSegment === null) {
            return;
        }

        $this->translationKeyDeletedSegments[] = $deletedSegment;
        $this->translationKeyValue = $segments !== [] ? implode('.', $segments) : null;
    }

    public function restoreFirstTranslationKeySegmentModal(): void
    {
        if ($this->translationKeySegmentControls()['disable_segment_buttons']) {
            return;
        }

        $restoredSegment = array_pop($this->translationKeyDeletedSegments);

        if ($restoredSegment === null) {
            return;
        }

        $segments = $this->translationKeySegments($this->translationKeyValue);
        array_unshift($segments, $restoredSegment);

        $this->translationKeyValue = implode('.', $segments);
    }

    public function removeTranslationKeyHashSuffixModal(): void
    {
        $segments = $this->translationKeySegments($this->translationKeyValue);
        $lastIndex = array_key_last($segments);
        $lastSegment = $lastIndex !== null ? ($segments[$lastIndex] ?? null) : null;

        if ($lastIndex === null || ! $this->isTranslationKeyHashSuffix($lastSegment)) {
            Flux::toast(
                heading: __('No hash suffix'),
                text: __('The current translation key does not end with a removable hash suffix.'),
                variant: 'warning',
            );

            return;
        }

        if (preg_match('/^[a-f0-9]{8,64}$/', (string) $lastSegment) === 1) {
            array_pop($segments);
        } else {
            $segments[$lastIndex] = (string) preg_replace('/[_-][a-f0-9]{8,64}$/', '', (string) $lastSegment);
        }

        $this->translationKeyValue = implode('.', $segments);
        $this->translationKeySegmentBaseValue = $this->translationKeyValue;
        $this->translationKeyDeletedSegments = [];

        Flux::toast(
            heading: __('Hash suffix removed'),
            text: __('The technical hash suffix has been removed from the editable translation key. Save the key to persist this review decision.'),
            variant: 'success',
        );
    }

    public function useProposedLeafTranslationKeyModal(): void
    {
        $candidateReview = $this->translationKeyCandidateReview();
        $proposedLeafKey = $this->nullableString($candidateReview['proposed_leaf_key'] ?? null);

        if ($proposedLeafKey === null) {
            Flux::toast(
                heading: __('No proposed leaf key'),
                text: __('There is no leaf-key proposal for the current translation key.'),
                variant: 'warning',
            );

            return;
        }

        $this->translationKeyValue = $proposedLeafKey;
        $this->translationKeySegmentBaseValue = $proposedLeafKey;
        $this->translationKeyDeletedSegments = [];
    }

    public function saveTranslationKeyModal(): void
    {
        $selectedFinding = $this->selectedFinding($this->translationKeyFindingId);

        if (! $selectedFinding || ! $selectedFinding->key_id) {
            Flux::toast(
                heading: __('No linked key'),
                text: __('This finding needs a key relation before its translation key can be saved.'),
                variant: 'warning',
            );

            return;
        }

        $key = TranslationWorkbenchKey::query()->find($selectedFinding->key_id);
        $finding = TranslationWorkbenchFinding::query()->find($selectedFinding->id);

        if (! $key || ! $finding) {
            Flux::toast(
                heading: __('Review failed'),
                text: __('The selected finding or key no longer exists.'),
                variant: 'danger',
            );

            return;
        }

        $this->translationKeyValue = $this->nullableString($this->translationKeyValue);

        $this->validate([
            'translationKeyValue' => [
                'required',
                'string',
                'regex:/^[a-z0-9][a-z0-9_-]*(\.[a-z0-9][a-z0-9_-]*)+$/',
            ],
        ]);

        $translationKey = $this->nullableString($this->translationKeyValue);
        $candidateReview = $this->translationKeyCandidateReview($translationKey, (int) $key->id);

        if ($candidateReview['is_blocked']) {
            Flux::toast(
                heading: __('Translation key blocked'),
                text: __('The edited translation key is a container path and cannot be saved as a scalar translation key. Add a leaf segment first.'),
                variant: 'danger',
            );

            return;
        }

        $saved = $this->saveKeyReviewDecision(
            key: $key,
            finding: $finding,
            selectedFinding: $selectedFinding,
            attributes: $this->keyStructureFromTranslationKey($translationKey, $key),
            reviewType: 'translation_key',
            decision: $translationKey === null ? 'translation_key_cleared' : 'translation_key_updated',
            toastHeading: __('Translation key saved'),
            toastText: __('The translation key has been updated.'),
        );

        $this->translationKeyValue = $translationKey;
        $this->reviewFindingId = $finding->id;
        $this->reviewModalOpen = true;

        if ($saved) {
            $this->closeTranslationKeyModal();
        }
    }

    public function acceptSuggestedTranslationKey(int $findingId): void
    {
        $selectedFinding = $this->selectedFinding($findingId);

        if (! $selectedFinding || ! $selectedFinding->key_id) {
            Flux::toast(
                heading: __('No linked key'),
                text: __('This finding needs a key relation before the suggested key can be accepted.'),
                variant: 'warning',
            );

            return;
        }

        $suggestedKey = trim((string) ($selectedFinding->key_suggested_key ?: $selectedFinding->suggested_key));

        if ($suggestedKey === '') {
            Flux::toast(
                heading: __('No suggested key'),
                text: __('There is no suggested key available for this finding.'),
                variant: 'warning',
            );

            return;
        }

        $key = TranslationWorkbenchKey::query()->find($selectedFinding->key_id);
        $finding = TranslationWorkbenchFinding::query()->find($findingId);

        if (! $key || ! $finding) {
            Flux::toast(
                heading: __('Review failed'),
                text: __('The selected finding or key no longer exists.'),
                variant: 'danger',
            );

            return;
        }

        if (trim((string) $key->translation_key) === $suggestedKey && $key->review_status === 'reviewed') {
            Flux::toast(
                heading: __('No change'),
                text: __('The suggested key is already accepted.'),
                variant: 'warning',
            );

            return;
        }

        $attributes = [
            ...$this->keyStructureFromTranslationKey($suggestedKey, $key),
            'review_status' => 'reviewed',
            'reviewed_at' => now(),
            'reviewed_by_user_id' => Auth::id(),
        ];
        $trackedAttributes = array_values(array_unique([
            ...array_keys($attributes),
        ]));

        DB::transaction(function () use ($key, $finding, $suggestedKey, $attributes, $trackedAttributes): void {
            $oldValues = $key->only([
                ...$trackedAttributes,
                'review_status',
                'reviewed_at',
                'reviewed_by_user_id',
            ]);

            $key->forceFill($attributes)->save();

            $newValues = $key->only([
                ...$trackedAttributes,
                'review_status',
                'reviewed_at',
                'reviewed_by_user_id',
            ]);

            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key->id,
                'finding_id' => $finding->id,
                'review_type' => 'translation_key',
                'decision' => 'suggested_key_accepted',
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'meta' => [
                    'source' => 'translation-workbench:review-modal',
                    'suggested_key' => $suggestedKey,
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            app(TranslationWorkbenchTimelineRecorder::class)->recordReviewEvent(
                review: $review,
                eventType: 'suggested_key_accepted',
                oldValues: $oldValues,
                newValues: $newValues,
                context: [
                    'source' => 'translation-workbench:review-modal',
                ],
            );
        });

        Flux::toast(
            heading: __('Suggested key accepted'),
            text: __('The suggested key has been saved as translation key.'),
            variant: 'success',
        );
    }

    public function setUiKeyReview(int $findingId, bool $checked): void
    {
        $context = $this->reviewContext($findingId);

        if (! $context) {
            return;
        }

        [$key, $finding, $selectedFinding] = $context;

        if ($checked && blank($key->translation_key)) {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Set or accept a translation key before confirming this entry as UI.'),
                variant: 'warning',
            );

            return;
        }

        $translationKey = $checked
            ? $this->withTranslationKeyNamespace($key->translation_key, 'ui', ['dynamic'])
            : $this->withoutTranslationKeyNamespace($key->translation_key, 'ui');

        $attributes = [
            'is_ui_key' => $checked,
            'is_ui_candidate_rejected' => false,
            'is_dynamic_key' => false,
            'is_dynamic_candidate_rejected' => false,
            'is_dynamic_multi' => false,
            ...$this->dynamicDataStateAttributes(false),
            'key_type' => $checked ? 'ui' : 'static',
            ...$this->keyStructureFromTranslationKey($translationKey),
        ];

        $this->saveKeyReviewDecision(
            key: $key,
            finding: $finding,
            selectedFinding: $selectedFinding,
            attributes: $attributes,
            reviewType: 'classification',
            decision: $checked ? 'ui_key_confirmed' : 'ui_key_unconfirmed',
            toastHeading: __('UI review updated'),
            toastText: $checked
                ? __('This key has been confirmed as UI translation.')
                : __('The explicit UI marker has been removed.'),
        );
    }

    public function setUiKeyRejected(int $findingId, bool $checked): void
    {
        $context = $this->reviewContext($findingId);

        if (! $context) {
            return;
        }

        [$key, $finding, $selectedFinding] = $context;
        $translationKey = $checked
            ? $this->withoutTranslationKeyNamespace($key->translation_key, 'ui')
            : $key->translation_key;

        $attributes = [
            'is_ui_key' => false,
            'is_ui_candidate_rejected' => $checked,
            'is_dynamic_key' => false,
            'is_dynamic_multi' => false,
            ...$this->dynamicDataStateAttributes(false),
            'key_type' => $checked ? 'static' : $key->key_type,
            ...$this->keyStructureFromTranslationKey($translationKey),
        ];

        $this->saveKeyReviewDecision(
            key: $key,
            finding: $finding,
            selectedFinding: $selectedFinding,
            attributes: $attributes,
            reviewType: 'classification',
            decision: $checked ? 'ui_candidate_rejected' : 'ui_candidate_rejection_removed',
            toastHeading: __('UI candidate updated'),
            toastText: $checked
                ? __('This UI candidate has been explicitly rejected.')
                : __('The explicit UI rejection has been removed.'),
        );
    }

    public function setUiCandidateClassification(int $findingId, bool $isCandidate): void
    {
        $context = $this->reviewContext($findingId);

        if (! $context) {
            return;
        }

        [$key, $finding, $selectedFinding] = $context;

        if ($isCandidate && blank($key->translation_key)) {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Set a translation key before confirming this entry as UI.'),
                variant: 'warning',
            );

            return;
        }

        $this->saveKeyReviewDecision(
            key: $key,
            finding: $finding,
            selectedFinding: $selectedFinding,
            attributes: [
                'reviewed_is_ui_candidate' => $isCandidate,
                'reviewed_is_dynamic_candidate' => $isCandidate ? false : $key->reviewed_is_dynamic_candidate,
                'reviewed_is_dynamic_multi' => $isCandidate ? false : $key->reviewed_is_dynamic_multi,
                'is_ui_key' => $isCandidate,
                'is_ui_candidate_rejected' => false,
                'is_dynamic_key' => $isCandidate ? false : (bool) $key->is_dynamic_key,
                'is_dynamic_multi' => $isCandidate ? false : (bool) $key->is_dynamic_multi,
                ...$this->dynamicDataStateAttributes(! $isCandidate && ((bool) $key->is_dynamic_key || (bool) $key->is_dynamic_multi)),
                'key_type' => $isCandidate
                    ? 'ui'
                    : ((bool) $key->is_dynamic_key ? 'dynamic' : 'static'),
            ],
            reviewType: 'classification',
            decision: $isCandidate ? 'ui_key_confirmed' : 'ui_key_unconfirmed',
            toastHeading: __('UI candidate updated'),
            toastText: $isCandidate
                ? __('This key has been confirmed as UI translation.')
                : __('The explicit UI marker has been removed.'),
        );
    }

    public function setDynamicKeyReview(int $findingId, bool $checked): void
    {
        $context = $this->reviewContext($findingId);

        if (! $context) {
            return;
        }

        [$key, $finding, $selectedFinding] = $context;

        if ($checked && blank($key->translation_key)) {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Set or accept a translation key before confirming this entry as dynamic.'),
                variant: 'warning',
            );

            return;
        }

        $translationKey = $checked
            ? $this->withTranslationKeyNamespace($key->translation_key, 'dynamic', ['ui'])
            : $this->withoutTranslationKeyNamespace($key->translation_key, 'dynamic');

        $attributes = [
            'is_ui_key' => false,
            'is_ui_candidate_rejected' => false,
            'is_dynamic_key' => $checked,
            'is_dynamic_candidate_rejected' => false,
            'is_dynamic_multi' => $checked ? (bool) $key->is_dynamic_multi : false,
            ...$this->dynamicDataStateAttributes($checked),
            'key_type' => $checked ? 'dynamic' : 'static',
            ...$this->keyStructureFromTranslationKey($translationKey),
        ];

        $this->saveKeyReviewDecision(
            key: $key,
            finding: $finding,
            selectedFinding: $selectedFinding,
            attributes: $attributes,
            reviewType: 'classification',
            decision: $checked ? 'dynamic_key_confirmed' : 'dynamic_key_unconfirmed',
            toastHeading: __('Dynamic review updated'),
            toastText: $checked
                ? __('This key has been confirmed as dynamic translation.')
                : __('The explicit dynamic marker has been removed.'),
        );
    }

    public function setDynamicKeyRejected(int $findingId, bool $checked): void
    {
        $context = $this->reviewContext($findingId);

        if (! $context) {
            return;
        }

        [$key, $finding, $selectedFinding] = $context;
        $translationKey = $checked
            ? $this->withoutTranslationKeyNamespace($key->translation_key, 'dynamic')
            : $key->translation_key;

        $attributes = [
            'is_dynamic_key' => false,
            'is_dynamic_candidate_rejected' => $checked,
            'is_dynamic_multi' => false,
            'is_ui_key' => false,
            ...$this->dynamicDataStateAttributes(false),
            'key_type' => $checked ? 'static' : $key->key_type,
            ...$this->keyStructureFromTranslationKey($translationKey),
        ];

        $this->saveKeyReviewDecision(
            key: $key,
            finding: $finding,
            selectedFinding: $selectedFinding,
            attributes: $attributes,
            reviewType: 'classification',
            decision: $checked ? 'dynamic_candidate_rejected' : 'dynamic_candidate_rejection_removed',
            toastHeading: __('Dynamic candidate updated'),
            toastText: $checked
                ? __('This dynamic candidate has been explicitly rejected.')
                : __('The explicit dynamic rejection has been removed.'),
        );
    }

    public function setDynamicCandidateClassification(int $findingId, bool $isCandidate): void
    {
        $context = $this->reviewContext($findingId);

        if (! $context) {
            return;
        }

        [$key, $finding, $selectedFinding] = $context;

        if ($isCandidate && blank($key->translation_key)) {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Set a translation key before confirming this entry as dynamic.'),
                variant: 'warning',
            );

            return;
        }

        $this->saveKeyReviewDecision(
            key: $key,
            finding: $finding,
            selectedFinding: $selectedFinding,
            attributes: [
                'reviewed_is_dynamic_candidate' => $isCandidate,
                'reviewed_is_ui_candidate' => $isCandidate ? false : $key->reviewed_is_ui_candidate,
                'reviewed_is_dynamic_multi' => $isCandidate ? (bool) $key->reviewed_is_dynamic_multi : false,
                'is_dynamic_key' => $isCandidate,
                'is_dynamic_candidate_rejected' => false,
                'is_dynamic_multi' => $isCandidate ? (bool) $key->reviewed_is_dynamic_multi : false,
                'is_ui_key' => $isCandidate ? false : (bool) $key->is_ui_key,
                ...$this->dynamicDataStateAttributes($isCandidate),
                'key_type' => $isCandidate
                    ? 'dynamic'
                    : ((bool) $key->is_ui_key ? 'ui' : 'static'),
            ],
            reviewType: 'classification',
            decision: $isCandidate ? 'dynamic_key_confirmed' : 'dynamic_key_unconfirmed',
            toastHeading: __('Dynamic candidate updated'),
            toastText: $isCandidate
                ? __('This key has been confirmed as dynamic translation.')
                : __('The explicit dynamic marker has been removed.'),
        );
    }

    public function setDynamicMultiKeyReview(int $findingId, bool $checked): void
    {
        $context = $this->reviewContext($findingId);

        if (! $context) {
            return;
        }

        [$key, $finding, $selectedFinding] = $context;

        $scannerDynamicCandidate = $selectedFinding->candidate_type === 'dynamic'
            || $selectedFinding->entry_type === 'dynamic'
            || $selectedFinding->kind === 'dynamic_multi';
        $isDynamicCandidate = $key->reviewed_is_dynamic_candidate ?? $scannerDynamicCandidate;

        if ($checked && ! (bool) $isDynamicCandidate) {
            Flux::toast(
                heading: __('Dynamic missing'),
                text: __('Confirm this entry as dynamic candidate before marking it as dynamic multi.'),
                variant: 'warning',
            );

            return;
        }

        $this->saveKeyReviewDecision(
            key: $key,
            finding: $finding,
            selectedFinding: $selectedFinding,
            attributes: [
                'reviewed_is_dynamic_multi' => $checked,
                ...$this->dynamicDataStateAttributes($checked || (bool) $key->is_dynamic_key),
            ],
            reviewType: 'candidate_classification',
            decision: $checked ? 'dynamic_multi_confirmed' : 'dynamic_multi_unconfirmed',
            toastHeading: __('Dynamic multi updated'),
            toastText: $checked
                ? __('This key has been marked as dynamic multi.')
                : __('The explicit dynamic multi marker has been removed.'),
        );
    }

    public function closeReviewModal(): void
    {
        $this->reviewModalOpen = false;
        $this->reviewFindingId = null;
    }

    public function closeEditModal(): void
    {
        $shouldRemindBulkEqualize = $this->bulkEqualizeReminderPending
            && $this->bulkEqualizeContext()['can_confirm'];

        $this->editModalOpen = false;
        $this->editFindingId = null;
        $this->editModalAutoCloseCountdown = 0;
        $this->bulkEqualizeReminderPending = false;

        if ($shouldRemindBulkEqualize) {
            Flux::toast(
                heading: __('Shared translation key pending'),
                text: __('Do not miss equalizing the selected matching translation keys.'),
                variant: 'warning',
            );
        }
    }

    public function toggleEditModalAutoCloseAfterSave(): void
    {
        $this->editModalAutoCloseAfterSave = ! $this->editModalAutoCloseAfterSave;

        if (! $this->editModalAutoCloseAfterSave) {
            $this->editModalAutoCloseCountdown = 0;
        }

        $this->persistUiState();
    }

    public function tickEditModalAutoClose(): void
    {
        if (! $this->editModalOpen || ! $this->editModalAutoCloseAfterSave) {
            $this->editModalAutoCloseCountdown = 0;

            return;
        }

        if ($this->editModalAutoCloseCountdown <= 1) {
            $this->closeEditModal();

            return;
        }

        $this->editModalAutoCloseCountdown--;
    }

    public function closeDynamicReviewModal(): void
    {
        $this->dynamicReviewModalOpen = false;
        $this->dynamicReviewFindingId = null;
    }

    public function closeTimelineModal(): void
    {
        $this->timelineModalOpen = false;
        $this->timelineFindingId = null;
    }

    public function closeTranslationKeyModal(): void
    {
        $this->translationKeyModalOpen = false;
        $this->translationKeyFindingId = null;
        $this->translationKeyValue = null;
        $this->translationKeySegmentBaseValue = null;
        $this->translationKeyDeletedSegments = [];
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
        $targetLocale = (string) ($this->editLocales()['active'] ?? app()->getLocale());
        $findingBulkLiteralExpression = $this->bulkLiteralSqlExpression('findings');
        $keyLinks = DB::table('translation_workbench_key_findings')
            ->selectRaw('finding_id, MIN(key_id) as key_id')
            ->where('status', 'active')
            ->groupBy('finding_id');
        $needsBulkLiteralCounts = $this->needsBulkLiteralCounts();
        $bulkLiteralCounts = $needsBulkLiteralCounts
            ? $this->bulkLiteralCountsQuery($sourceLocale, $targetLocale)
            : null;

        $query = DB::table('translation_workbench_findings as findings')
            ->join('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->leftJoinSub($keyLinks, 'key_links', function ($join): void {
                $join->on('key_links.finding_id', '=', 'findings.id');
            })
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'key_links.key_id');

        if ($needsBulkLiteralCounts && $bulkLiteralCounts !== null) {
            $query->leftJoinSub($bulkLiteralCounts, 'bulk_literals', function ($join) use ($findingBulkLiteralExpression): void {
                $join->on('bulk_literals.bulk_literal', '=', DB::raw($findingBulkLiteralExpression));
            });
        }

        $query
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
                'findings.dynamic_scope',
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
                DB::raw($findingBulkLiteralExpression . ' as bulk_literal'),
                DB::raw($needsBulkLiteralCounts
                    ? 'COALESCE(bulk_literals.bulk_literal_count, 0) as bulk_literal_count'
                    : '0 as bulk_literal_count'),
            ])
            ->addSelect([
                $this->schemaHasColumn('translation_workbench_findings', 'dynamic_data_state')
                    ? 'findings.dynamic_data_state'
                    : DB::raw('null as dynamic_data_state'),
                $this->schemaHasColumn('translation_workbench_keys', 'dynamic_data_state')
                    ? 'keys.dynamic_data_state as key_dynamic_data_state'
                    : DB::raw('null as key_dynamic_data_state'),
                $this->schemaHasColumn('translation_workbench_keys', 'lang_node_type')
                    ? 'keys.lang_node_type'
                    : DB::raw("'unknown' as lang_node_type"),
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
            )
            ->selectRaw(
                'CASE WHEN EXISTS (
                    SELECT 1
                    FROM translation_workbench_lang_values as target_values
                    WHERE target_values.locale = ?
                        AND target_values.status = ?
                        AND (
                            target_values.translation_key = keys.translation_key
                            OR target_values.translation_key = keys.suggested_key
                            OR target_values.translation_key = findings.suggested_key
                            OR target_values.translation_key = findings.found_translation_key
                        )
                ) THEN 1 ELSE 0 END as has_target_value',
                [$targetLocale, 'active'],
            )
            ->selectRaw(
                '(SELECT source_values.value
                    FROM translation_workbench_lang_values as source_values
                    WHERE source_values.locale = ?
                        AND source_values.status = ?
                        AND (
                            source_values.translation_key = keys.translation_key
                            OR source_values.translation_key = keys.suggested_key
                            OR source_values.translation_key = findings.suggested_key
                            OR source_values.translation_key = findings.found_translation_key
                        )
                    ORDER BY CASE
                        WHEN source_values.translation_key = keys.translation_key THEN 0
                        WHEN source_values.translation_key = keys.suggested_key THEN 1
                        WHEN source_values.translation_key = findings.suggested_key THEN 2
                        ELSE 3
                    END, source_values.id DESC
                    LIMIT 1) as source_translation_value',
                [$sourceLocale, 'active'],
            )
            ->selectRaw(
                '(SELECT source_values.meta->>\'source\'
                    FROM translation_workbench_lang_values as source_values
                    WHERE source_values.locale = ?
                        AND source_values.status = ?
                        AND (
                            source_values.translation_key = keys.translation_key
                            OR source_values.translation_key = keys.suggested_key
                            OR source_values.translation_key = findings.suggested_key
                            OR source_values.translation_key = findings.found_translation_key
                        )
                    ORDER BY CASE
                        WHEN source_values.translation_key = keys.translation_key THEN 0
                        WHEN source_values.translation_key = keys.suggested_key THEN 1
                        WHEN source_values.translation_key = findings.suggested_key THEN 2
                        ELSE 3
                    END, source_values.id DESC
                    LIMIT 1) as source_translation_origin',
                [$sourceLocale, 'active'],
            )
            ->selectRaw(
                '(SELECT target_values.value
                    FROM translation_workbench_lang_values as target_values
                    WHERE target_values.locale = ?
                        AND target_values.status = ?
                        AND (
                            target_values.translation_key = keys.translation_key
                            OR target_values.translation_key = keys.suggested_key
                            OR target_values.translation_key = findings.suggested_key
                            OR target_values.translation_key = findings.found_translation_key
                        )
                    ORDER BY CASE
                        WHEN target_values.translation_key = keys.translation_key THEN 0
                        WHEN target_values.translation_key = keys.suggested_key THEN 1
                        WHEN target_values.translation_key = findings.suggested_key THEN 2
                        ELSE 3
                    END, target_values.id DESC
                    LIMIT 1) as target_translation_value',
                [$targetLocale, 'active'],
            )
            ->selectRaw(
                '(SELECT target_values.meta->>\'source\'
                    FROM translation_workbench_lang_values as target_values
                    WHERE target_values.locale = ?
                        AND target_values.status = ?
                        AND (
                            target_values.translation_key = keys.translation_key
                            OR target_values.translation_key = keys.suggested_key
                            OR target_values.translation_key = findings.suggested_key
                            OR target_values.translation_key = findings.found_translation_key
                        )
                    ORDER BY CASE
                        WHEN target_values.translation_key = keys.translation_key THEN 0
                        WHEN target_values.translation_key = keys.suggested_key THEN 1
                        WHEN target_values.translation_key = findings.suggested_key THEN 2
                        ELSE 3
                    END, target_values.id DESC
                    LIMIT 1) as target_translation_origin',
                [$targetLocale, 'active'],
            )
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
                        AND NULLIF(BTRIM(source_values.value), ?) IS NOT NULL
                        AND NULLIF(BTRIM(findings.literal_text), ?) IS NOT NULL
                        AND BTRIM(source_values.value) <> BTRIM(findings.literal_text)
                ) THEN 1 ELSE 0 END as source_value_differs',
                [$sourceLocale, 'active', '', ''],
            );
        $this->addKeyCandidateReviewSelects($query);
        $this->addFindingBulkEqualizedSelect($query);
        $this->addFindingHistorySelect($query);
        $this->addFindingDynamicContextSelect($query, $targetLocale);
        $this->addFindingDynamicSourceSelects($query);

        $this->applyFindingFilters($query, $sourceLocale, $targetLocale);

        $this->applyFindingSort($query);

        return $query->paginate($this->normalizedPerPage());
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function lastEditedTranslationRows(): array
    {
        if (! $this->hasTables(['translation_workbench_lang_values', 'translation_workbench_keys'])) {
            return [];
        }

        $sourceLocale = LocaleCode::normalize($this->sourceMainLocale());
        $targetLocale = LocaleCode::normalize((string) ($this->editLocales()['active'] ?? app()->getLocale()));

        if ($sourceLocale === '' || $targetLocale === '' || $sourceLocale === $targetLocale) {
            return [];
        }

        $activeKeyFindings = DB::table('translation_workbench_key_findings')
            ->selectRaw('key_id, MIN(finding_id) as finding_id, COUNT(*) as finding_count')
            ->where('status', 'active')
            ->groupBy('key_id');
        $bulkReviews = DB::table('translation_workbench_reviews')
            ->selectRaw('key_id, MIN(id) as bulk_review_id, MIN(finding_id) as bulk_finding_id, COUNT(DISTINCT finding_id) as bulk_finding_count')
            ->where('decision', 'translation_key_bulk_equalized')
            ->whereNotNull('key_id')
            ->groupBy('key_id');

        return DB::table('translation_workbench_lang_values as target_values')
            ->join('translation_workbench_lang_values as source_values', function ($join) use ($sourceLocale): void {
                $join
                    ->on('source_values.translation_key', '=', 'target_values.translation_key')
                    ->where('source_values.locale', '=', $sourceLocale)
                    ->where('source_values.status', '=', 'active');
            })
            ->leftJoin('translation_workbench_keys as keys', 'keys.translation_key', '=', 'target_values.translation_key')
            ->leftJoinSub($activeKeyFindings, 'active_key_findings', function ($join): void {
                $join->on('active_key_findings.key_id', '=', 'keys.id');
            })
            ->leftJoinSub($bulkReviews, 'bulk_reviews', function ($join): void {
                $join->on('bulk_reviews.key_id', '=', 'keys.id');
            })
            ->where('target_values.locale', $targetLocale)
            ->where('target_values.status', 'active')
            ->whereRaw("NULLIF(BTRIM(source_values.value), '') IS NOT NULL")
            ->whereRaw("NULLIF(BTRIM(target_values.value), '') IS NOT NULL")
            ->orderByDesc('target_values.updated_at')
            ->orderByDesc('target_values.id')
            ->limit(25)
            ->get([
                'target_values.translation_key',
                'target_values.namespace',
                'target_values.lang_key',
                'target_values.value as target_value',
                'target_values.updated_at',
                'target_values.meta',
                'source_values.value as source_value',
                'keys.id as key_id',
                'keys.is_dynamic_key',
                'keys.is_dynamic_multi',
                'active_key_findings.finding_id as relation_finding_id',
                'active_key_findings.finding_count',
                'bulk_reviews.bulk_review_id',
                'bulk_reviews.bulk_finding_id',
                'bulk_reviews.bulk_finding_count',
            ])
            ->map(function ($row) use ($sourceLocale, $targetLocale): array {
                $langKeySegments = collect(explode('.', (string) $row->lang_key))
                    ->map(static fn(string $segment): string => trim($segment))
                    ->filter(static fn(string $segment): bool => $segment !== '')
                    ->values();
                $meta = is_string($row->meta) ? json_decode($row->meta, true) : (array) $row->meta;

                $metaFindingId = isset($meta['finding_id']) ? (int) $meta['finding_id'] : null;
                $relationFindingId = $row->relation_finding_id !== null ? (int) $row->relation_finding_id : null;
                $bulkFindingId = $row->bulk_finding_id !== null ? (int) $row->bulk_finding_id : null;
                $findingId = $metaFindingId ?: $relationFindingId ?: $bulkFindingId;
                $bulkReviewId = $row->bulk_review_id !== null ? (int) $row->bulk_review_id : null;
                $relationCount = (int) ($row->finding_count ?? 0);
                $bulkCount = (int) ($row->bulk_finding_count ?? 0);

                return [
                    'translation_key' => (string) $row->translation_key,
                    'namespace' => (string) $row->namespace,
                    'group' => $langKeySegments->first(),
                    'key_id' => $row->key_id !== null ? (int) $row->key_id : null,
                    'source_locale' => $sourceLocale,
                    'target_locale' => $targetLocale,
                    'source_value' => (string) $row->source_value,
                    'target_value' => (string) $row->target_value,
                    'updated_at' => $row->updated_at,
                    'finding_id' => $findingId,
                    'meta_finding_id' => $metaFindingId,
                    'relation_finding_id' => $relationFindingId,
                    'bulk_finding_id' => $bulkFindingId,
                    'is_bulk' => $bulkReviewId !== null,
                    'bulk_id' => $bulkReviewId,
                    'bulk_entry_count' => max($bulkCount, $relationCount),
                    'is_dynamic' => (bool) ($row->is_dynamic_key ?? false) || (bool) ($row->is_dynamic_multi ?? false),
                    'is_dynamic_multi' => (bool) ($row->is_dynamic_multi ?? false),
                ];
            })
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sharedKeyCandidateRows(): array
    {
        if (! $this->hasTables([
            'translation_workbench_shared_key_candidates',
            'translation_workbench_findings',
            'translation_workbench_source_files',
        ])) {
            return [];
        }

        return DB::table('translation_workbench_shared_key_candidates as candidates')
            ->join('translation_workbench_findings as findings', 'findings.id', '=', 'candidates.finding_id')
            ->join('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'candidates.key_id')
            ->leftJoin('translation_workbench_keys as matched_keys', 'matched_keys.id', '=', 'candidates.matched_key_id')
            ->where('candidates.status', 'pending')
            ->where('findings.status', 'active')
            ->orderByDesc('candidates.last_seen_at')
            ->orderBy('candidates.normalized_literal')
            ->limit(100)
            ->get([
                'candidates.id',
                'candidates.finding_id',
                'candidates.key_id',
                'candidates.matched_key_id',
                'candidates.literal_text',
                'candidates.normalized_literal',
                'candidates.current_translation_key',
                'candidates.suggested_shared_translation_key',
                'candidates.matched_review_count',
                'candidates.matched_finding_count',
                'candidates.confidence',
                'candidates.status',
                'candidates.last_seen_at',
                'findings.source_line',
                'findings.suggested_key as finding_suggested_key',
                'source_files.path as source_path',
                'keys.suggested_key as current_suggested_key',
                'matched_keys.review_status as matched_review_status',
            ])
            ->map(function (object $row): array {
                return [
                    'id' => (int) $row->id,
                    'finding_id' => (int) $row->finding_id,
                    'key_id' => $row->key_id !== null ? (int) $row->key_id : null,
                    'matched_key_id' => $row->matched_key_id !== null ? (int) $row->matched_key_id : null,
                    'literal_text' => $this->nullableString($row->literal_text ?? null),
                    'normalized_literal' => (string) $row->normalized_literal,
                    'current_translation_key' => $this->nullableString($row->current_translation_key ?? null),
                    'current_suggested_key' => $this->nullableString($row->current_suggested_key ?? null),
                    'finding_suggested_key' => $this->nullableString($row->finding_suggested_key ?? null),
                    'suggested_shared_translation_key' => (string) $row->suggested_shared_translation_key,
                    'matched_review_count' => (int) $row->matched_review_count,
                    'matched_finding_count' => (int) $row->matched_finding_count,
                    'confidence' => (string) $row->confidence,
                    'status' => (string) $row->status,
                    'matched_review_status' => $this->nullableString($row->matched_review_status ?? null),
                    'source_path' => (string) $row->source_path,
                    'source_line' => $row->source_line !== null ? (int) $row->source_line : null,
                    'last_seen_at' => $row->last_seen_at,
                ];
            })
            ->all();
    }

    private function reviewAdjacentFindingId(string $direction): ?int
    {
        if ($this->reviewFindingId === null || ! $this->reviewModalOpen) {
            return null;
        }

        $findingIds = $this->filteredFindingIds();

        if ($findingIds === []) {
            return null;
        }

        $currentIndex = array_search($this->reviewFindingId, $findingIds, true);

        if ($currentIndex === false) {
            return $direction === 'previous'
                ? $findingIds[array_key_last($findingIds)]
                : $findingIds[0];
        }

        if (count($findingIds) < 2) {
            return null;
        }

        if ($direction === 'previous') {
            return $findingIds[$currentIndex - 1] ?? $findingIds[array_key_last($findingIds)];
        }

        return $findingIds[$currentIndex + 1] ?? $findingIds[0];
    }

    /**
     * @return array<int, int>
     */
    private function filteredFindingIds(): array
    {
        if (! $this->hasTables([
            'translation_workbench_findings',
            'translation_workbench_source_files',
            'translation_workbench_key_findings',
            'translation_workbench_keys',
            'translation_workbench_lang_values',
        ])) {
            return [];
        }

        $sourceLocale = $this->sourceMainLocale();
        $targetLocale = (string) ($this->editLocales()['active'] ?? app()->getLocale());
        $findingBulkLiteralExpression = $this->bulkLiteralSqlExpression('findings');
        $keyLinks = DB::table('translation_workbench_key_findings')
            ->selectRaw('finding_id, MIN(key_id) as key_id')
            ->where('status', 'active')
            ->groupBy('finding_id');
        $needsBulkLiteralCounts = $this->needsBulkLiteralCounts();
        $bulkLiteralCounts = $needsBulkLiteralCounts
            ? $this->bulkLiteralCountsQuery($sourceLocale, $targetLocale)
            : null;

        $query = DB::table('translation_workbench_findings as findings')
            ->join('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->leftJoinSub($keyLinks, 'key_links', function ($join): void {
                $join->on('key_links.finding_id', '=', 'findings.id');
            })
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'key_links.key_id');

        if ($needsBulkLiteralCounts && $bulkLiteralCounts !== null) {
            $query->leftJoinSub($bulkLiteralCounts, 'bulk_literals', function ($join) use ($findingBulkLiteralExpression): void {
                $join->on('bulk_literals.bulk_literal', '=', DB::raw($findingBulkLiteralExpression));
            });
        }

        $query->select('findings.id');

        $this->applyFindingFilters($query, $sourceLocale, $targetLocale);
        $this->applyFindingSort($query);

        return $query
            ->pluck('findings.id')
            ->map(static fn($id): int => (int) $id)
            ->values()
            ->all();
    }

    private function selectedFinding(?int $findingId): ?object
    {
        if ($findingId === null || ! $this->hasTables([
            'translation_workbench_findings',
            'translation_workbench_source_files',
            'translation_workbench_key_findings',
            'translation_workbench_keys',
        ])) {
            return null;
        }

        $targetLocale = (string) ($this->editLocales()['active'] ?? app()->getLocale());
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
            ->where('findings.id', $findingId)
            ->select([
                'findings.id',
                'findings.source_line',
                'findings.kind',
                DB::raw("NULLIF(findings.function_name, '-') as function_name"),
                'findings.raw_expression',
                'findings.literal_text',
                'findings.literal_text_suggested',
                'findings.found_translation_key',
                'findings.existing_key',
                'findings.suggested_key',
                'findings.namespace',
                'findings.group',
                'findings.path_key',
                'findings.scope',
                'findings.dynamic_scope',
                'findings.entry_type',
                'findings.candidate_type',
                'findings.candidate_reason',
                'findings.status',
                'findings.first_seen_at',
                'findings.last_seen_at',
                'source_files.path as source_path',
                'keys.id as key_id',
                'keys.translation_key',
                'keys.suggested_key as key_suggested_key',
                'keys.namespace as key_namespace',
                'keys.group as key_group',
                'keys.path_key as key_path_key',
                'keys.scope as key_scope',
                'keys.status as key_status',
                'keys.review_status',
                'keys.key_type',
                'keys.is_ui_key',
                'keys.is_dynamic_key',
                'keys.is_dynamic_multi',
            ]);
        $query->addSelect([
            $this->schemaHasColumn('translation_workbench_findings', 'dynamic_data_state')
                ? 'findings.dynamic_data_state'
                : DB::raw('null as dynamic_data_state'),
            $this->schemaHasColumn('translation_workbench_keys', 'dynamic_data_state')
                ? 'keys.dynamic_data_state as key_dynamic_data_state'
                : DB::raw('null as key_dynamic_data_state'),
        ]);
        $this->addKeyCandidateReviewSelects($query);
        $this->addFindingDynamicContextSelect($query, $targetLocale);
        $this->addFindingDynamicSourceSelects($query);

        return $query
            ->first();
    }

    /**
     * @return array<int, array{label: string, key: string, segment: string, distinct_full_key_count: int, translation_key_count: int, suggested_key_count: int, existing_key_count: int}>
     */
    private function translationKeySegmentStats(?int $findingId): array
    {
        if ($findingId === null || ! $this->hasTables([
            'translation_workbench_findings',
            'translation_workbench_keys',
        ])) {
            return [];
        }

        $selectedFinding = $this->selectedFinding($findingId);

        if (! $selectedFinding) {
            return [];
        }

        $keys = [
            'current' => [
                'label' => __('Current key'),
                'key' => $this->nullableString($selectedFinding->translation_key),
            ],
            'existing' => [
                'label' => __('Existing key'),
                'key' => $this->nullableString($selectedFinding->existing_key),
            ],
            'suggested' => [
                'label' => __('Suggested key'),
                'key' => $this->nullableString($selectedFinding->key_suggested_key ?: $selectedFinding->suggested_key),
            ],
        ];

        return collect($keys)
            ->map(function (array $keyContext): ?array {
                $key = $keyContext['key'];
                $segment = $this->lastTranslationKeySegment($key);

                if ($key === null || $segment === null) {
                    return null;
                }

                $translationKeys = $this->distinctKeySegmentMatches('translation_workbench_keys', 'translation_key', $segment);
                $suggestedKeys = $this->distinctKeySegmentMatches('translation_workbench_keys', 'suggested_key', $segment)
                    ->merge($this->distinctKeySegmentMatches('translation_workbench_findings', 'suggested_key', $segment))
                    ->unique()
                    ->values();
                $existingKeys = $this->distinctKeySegmentMatches('translation_workbench_findings', 'existing_key', $segment);
                $distinctFullKeys = $translationKeys
                    ->merge($suggestedKeys)
                    ->merge($existingKeys)
                    ->unique()
                    ->values();

                return [
                    'label' => $keyContext['label'],
                    'key' => $key,
                    'segment' => $segment,
                    'distinct_full_key_count' => $distinctFullKeys->count(),
                    'translation_key_count' => $translationKeys->count(),
                    'suggested_key_count' => $suggestedKeys->count(),
                    'existing_key_count' => $existingKeys->count(),
                ];
            })
            ->filter()
            ->unique(static fn(array $row): string => $row['label'] . ':' . $row['key'])
            ->values()
            ->all();
    }

    /**
     * @return array{disable_segment_buttons: bool, can_delete: bool, can_restore: bool, next_delete_segment: ?string, next_restore_segment: ?string}
     */
    private function translationKeySegmentControls(): array
    {
        $segments = $this->translationKeySegments($this->translationKeyValue);
        $expectedValue = $this->translationKeyValueAfterDeletedSegments();
        $currentValue = $this->nullableString($this->translationKeyValue);
        $disableSegmentButtons = $expectedValue !== $currentValue;

        return [
            'disable_segment_buttons' => $disableSegmentButtons,
            'can_delete' => ! $disableSegmentButtons && count($segments) > 1,
            'can_restore' => ! $disableSegmentButtons && $this->translationKeyDeletedSegments !== [],
            'next_delete_segment' => count($segments) > 1 ? $segments[0] : null,
            'next_restore_segment' => $this->translationKeyDeletedSegments[array_key_last($this->translationKeyDeletedSegments)] ?? null,
        ];
    }

    /**
     * @return array{
     *     translation_key: ?string,
     *     node_type: string,
     *     has_leaf_value: bool,
     *     has_children: bool,
     *     is_blocked: bool,
     *     proposed_leaf_key: ?string,
     *     child_keys: array<int, string>
     * }
     */
    private function translationKeyCandidateReview(?string $translationKey = null, ?int $currentKeyId = null): array
    {
        $selectedFinding = $this->selectedFinding($this->translationKeyFindingId);
        $translationKey ??= $this->translationKeyValue;
        $currentKeyId ??= $selectedFinding?->key_id !== null ? (int) $selectedFinding->key_id : null;

        return app(TranslationWorkbenchLangNodeClassifier::class)
            ->reviewCandidate($translationKey, $currentKeyId);
    }

    private function translationKeyValueAfterDeletedSegments(): ?string
    {
        $segments = $this->translationKeySegments($this->translationKeySegmentBaseValue);

        foreach ($this->translationKeyDeletedSegments as $deletedSegment) {
            if (($segments[0] ?? null) !== $deletedSegment) {
                return $this->nullableString($this->translationKeyValue);
            }

            array_shift($segments);
        }

        return $segments !== [] ? implode('.', $segments) : null;
    }

    /**
     * @return array<int, string>
     */
    private function translationKeySegments(?string $translationKey): array
    {
        return collect(explode('.', trim((string) $translationKey, '.')))
            ->map(static fn(string $segment): string => trim($segment))
            ->filter(static fn(string $segment): bool => $segment !== '')
            ->values()
            ->all();
    }

    private function lastTranslationKeySegment(?string $translationKey): ?string
    {
        $translationKey = $this->nullableString($translationKey);

        if ($translationKey === null) {
            return null;
        }

        $segments = collect(explode('.', trim($translationKey, '.')))
            ->map(static fn(string $segment): string => trim($segment))
            ->filter(static fn(string $segment): bool => $segment !== '')
            ->values();

        return $segments->last();
    }

    private function isTranslationKeyHashSuffix(?string $segment): bool
    {
        $segment = trim((string) $segment);

        return preg_match('/^[a-f0-9]{8,64}$/', $segment) === 1
            || preg_match('/[_-][a-f0-9]{8,64}$/', $segment) === 1;
    }

    private function distinctKeySegmentMatches(string $table, string $column, string $segment): \Illuminate\Support\Collection
    {
        $segmentPattern = '(^|\\.)'.preg_quote($segment, '/').'$';

        return DB::table($table)
            ->distinct()
            ->whereNotNull($column)
            ->whereRaw(
                sprintf('(%s = ? OR %s ~ ?)', $column, $column),
                [$segment, $segmentPattern],
            )
            ->pluck($column)
            ->map(static fn(mixed $key): string => trim((string) $key))
            ->filter(static fn(string $key): bool => $key !== '')
            ->unique()
            ->values();
    }

    /**
     * @return array{source: string, active: string, sub: array<int, string>}
     */
    private function editLocales(): array
    {
        $configuredLocale = LocaleCode::normalize((string) (app(AppGeneralSettings::class)->locale ?? app()->getLocale()));
        $configuredLocale = $configuredLocale !== '' ? $configuredLocale : 'de';
        $activeLanguage = (string) (LocaleCode::parts($configuredLocale)['language'] ?? $configuredLocale);
        $activeLocale = $activeLanguage !== '' ? $activeLanguage : $configuredLocale;
        $activeSubLocales = Locale::query()
            ->where('is_active', true)
            ->ordered()
            ->get(['code', 'normalized_code'])
            ->map(static fn(Locale $locale): string => LocaleCode::normalize((string) ($locale->normalized_code ?: $locale->code)))
            ->filter(static fn(string $locale): bool => $locale !== '')
            ->filter(static function (string $locale) use ($activeLanguage): bool {
                return (string) (LocaleCode::parts($locale)['language'] ?? '') === $activeLanguage
                    && $locale !== $activeLanguage;
            })
            ->unique()
            ->values()
            ->all();

        return [
            'source' => $this->sourceMainLocale(),
            'active' => $activeLocale,
            'sub' => $activeSubLocales,
        ];
    }

    /**
     * @param  array{source: string, active: string, sub: array<int, string>}  $editLocales
     * @return array{source: ?string, target: ?string, source_exists: bool, target_exists: bool, source_origin: string}
     */
    private function editValues(?object $selectedFinding, array $editLocales): array
    {
        if (! $selectedFinding) {
            return [
                'source' => null,
                'target' => null,
                'source_exists' => false,
                'target_exists' => false,
                'source_origin' => 'missing',
            ];
        }

        $sourceLocale = (string) ($editLocales['source'] ?? $this->sourceMainLocale());
        $activeLocale = (string) ($editLocales['active'] ?? app()->getLocale());
        $translationKey = $this->nullableString($selectedFinding->translation_key);
        $sourceTranslationValue = $translationKey !== null
            ? $this->translationValueForKeyAndLocale($translationKey, $sourceLocale)
            : null;
        $targetTranslationValue = $translationKey !== null
            ? $this->translationValueForKeyAndLocale($translationKey, $activeLocale)
            : null;
        $literalText = $this->nullableString($selectedFinding->literal_text);
        $literalTextSuggested = $this->nullableString($selectedFinding->literal_text_suggested);
        $sourceOrigin = match (true) {
            $sourceTranslationValue !== null => 'translation_value',
            $literalText !== null => 'literal_text',
            $literalTextSuggested !== null => 'literal_text_suggested',
            default => 'missing',
        };

        return [
            'source' => $sourceTranslationValue ?? $literalText ?? $literalTextSuggested,
            'target' => $targetTranslationValue,
            'source_exists' => $sourceTranslationValue !== null,
            'target_exists' => $targetTranslationValue !== null,
            'source_origin' => $sourceOrigin,
        ];
    }

    /**
     * @param  array{source: string, active: string, sub: array<int, string>}  $editLocales
     * @return array<string, ?string>
     */
    private function targetSubTranslationValues(?object $selectedFinding, array $editLocales): array
    {
        $subLocales = collect((array) ($editLocales['sub'] ?? []))
            ->filter(static fn(mixed $locale): bool => is_string($locale) && trim($locale) !== '')
            ->map(static fn(string $locale): string => LocaleCode::normalize($locale))
            ->filter(static fn(string $locale): bool => $locale !== '')
            ->unique()
            ->values();

        if (! $selectedFinding) {
            return $subLocales
                ->mapWithKeys(static fn(string $locale): array => [$locale => null])
                ->all();
        }

        $translationKey = $this->nullableString($selectedFinding->translation_key);

        return $subLocales
            ->mapWithKeys(function (string $locale) use ($translationKey): array {
                return [
                    $locale => $translationKey !== null
                        ? $this->translationValueForKeyAndLocale($translationKey, $locale)
                        : null,
                ];
            })
            ->all();
    }

    private function bootstrapEditState(object $selectedFinding): void
    {
        $editLocales = $this->editLocales();
        $editValues = $this->editValues($selectedFinding, $editLocales);
        $this->editFindingId = (int) $selectedFinding->id;
        $this->sourceTranslationValue = $editValues['source'];
        $this->targetTranslationValue = $editValues['target'];
        $this->targetSubTranslationValues = $this->targetSubTranslationValues($selectedFinding, $editLocales);
        $this->selectedTargetSubLocales = collect($this->targetSubTranslationValues)
            ->filter(fn(mixed $value): bool => $this->nullableString($value) !== null)
            ->keys()
            ->values()
            ->all();
        $this->bootstrapDynamicMultiEditState($selectedFinding, $editLocales);
        $this->sourceTranslationEditable = false;
    }

    /**
     * @param  array{source: string, active: string, sub: array<int, string>}  $editLocales
     */
    private function bootstrapDynamicMultiEditState(object $selectedFinding, array $editLocales): void
    {
        $rows = collect($this->dynamicMultiRows((int) $selectedFinding->id, $editLocales));

        $this->dynamicMultiValueKeyMap = $rows
            ->mapWithKeys(static fn(array $row): array => [
                $row['field_key'] => $row['value_key'],
            ])
            ->all();

        $this->dynamicMultiTargetValues = $rows
            ->mapWithKeys(static fn(array $row): array => [
                $row['field_key'] => $row['target'],
            ])
            ->all();

        $this->dynamicMultiSourceValues = $rows
            ->mapWithKeys(function (array $row): array {
                return [
                    $row['field_key'] => $this->nullableString($row['source'] ?? $row['native_label'] ?? null),
                ];
            })
            ->all();

        $this->dynamicMultiSourceEqualsTargetOverrides = $rows
            ->mapWithKeys(function (array $row): array {
                $sourceValue = $this->nullableString($row['source'] ?? $row['native_label'] ?? null);
                $targetValue = $this->nullableString($row['target'] ?? null);

                return [
                    $row['field_key'] => $sourceValue !== null && $targetValue !== null && $sourceValue === $targetValue,
                ];
            })
            ->all();

        $this->dynamicMultiEditableTargetFields = $rows
            ->mapWithKeys(function (array $row): array {
                return [
                    $row['field_key'] => $this->nullableString($row['target'] ?? null) === null,
                ];
            })
            ->all();

        $this->dynamicMultiEditableSourceFields = $rows
            ->mapWithKeys(static fn(array $row): array => [
                $row['field_key'] => false,
            ])
            ->all();
    }

    /**
     * @param  array{source: string, active: string, sub: array<int, string>}  $editLocales
     * @return array<int, array{field_key: string, value_key: string, source: ?string, target: ?string, native_label: ?string, status: ?string}>
     */
    private function dynamicMultiRows(?int $findingId, array $editLocales): array
    {
        $selectedFinding = $this->selectedFinding($findingId);

        if (! $selectedFinding || ! $selectedFinding->key_id) {
            return [];
        }

        $sourceLocale = LocaleCode::normalize((string) ($editLocales['source'] ?? $this->sourceMainLocale()));
        $targetLocale = LocaleCode::normalize((string) ($editLocales['active'] ?? app()->getLocale()));
        $keyExists = TranslationWorkbenchKey::query()
            ->whereKey($selectedFinding->key_id)
            ->exists();

        if (! $keyExists) {
            return [];
        }

        $storedValues = collect();

        if ($this->schemaHasTable('translation_workbench_dynamic_key_values')) {
            $storedValues = DB::table('translation_workbench_dynamic_key_values')
                ->where('key_id', $selectedFinding->key_id)
                ->whereIn('locale', [$sourceLocale, $targetLocale])
                ->orderBy('value_key')
                ->get(['value_key', 'locale', 'value', 'native_label', 'status'])
                ->groupBy('value_key');
        }

        $sourceRows = collect();

        if (! $this->hasTables([
            'translation_workbench_dynamic_sources',
            'translation_workbench_dynamic_source_values',
        ])) {
            $sourceRows = collect();
        } else {
            $linkedRuntimeSourceIds = $this->linkedRuntimeSourceIdsForFinding($selectedFinding);

            $sourceRows = DB::table('translation_workbench_dynamic_source_values as source_values')
                ->join('translation_workbench_dynamic_sources as sources', 'sources.id', '=', 'source_values.dynamic_source_id')
                ->where('sources.status', '<>', 'obsolete')
                ->where('source_values.status', 'active')
                ->where(function ($query) use ($selectedFinding, $linkedRuntimeSourceIds): void {
                    $query->where('sources.finding_id', $selectedFinding->id)
                        ->orWhere('sources.key_id', $selectedFinding->key_id);

                    if ($linkedRuntimeSourceIds !== []) {
                        $query->orWhereIn('sources.id', $linkedRuntimeSourceIds);
                    }
                })
                ->orderBy('source_values.value_key')
                ->get(['source_values.value_key', 'source_values.native_label', 'source_values.status'])
                ->groupBy('value_key')
                ->map(static fn($values) => $values->first());
        }

        $valueKeys = $sourceRows
            ->keys()
            ->merge($storedValues->keys())
            ->map(static fn(mixed $valueKey): string => (string) $valueKey)
            ->filter(static fn(string $valueKey): bool => $valueKey !== '')
            ->unique()
            ->sort()
            ->values()
            ->all();

        return collect($valueKeys)
            ->map(function (string $valueKey) use ($sourceRows, $storedValues, $sourceLocale, $targetLocale): array {
                $sourceValueRow = $sourceRows->get($valueKey);
                $localeRows = $storedValues->get($valueKey, collect());
                $sourceRow = $localeRows->firstWhere('locale', $sourceLocale);
                $targetRow = $localeRows->firstWhere('locale', $targetLocale);

                return [
                    'field_key' => $this->dynamicMultiFieldKey($valueKey),
                    'value_key' => $valueKey,
                    'source' => $this->nullableString($sourceRow?->value ?? $sourceValueRow?->native_label),
                    'target' => $this->nullableString($targetRow?->value),
                    'native_label' => $this->nullableString($sourceValueRow?->native_label ?? $sourceRow?->native_label ?? $targetRow?->native_label),
                    'status' => $this->nullableString($targetRow?->status ?? $sourceRow?->status ?? $sourceValueRow?->status),
                ];
            })
            ->values()
            ->all();
    }

    private function dynamicMultiFieldKey(string $valueKey): string
    {
        return 'v_' . sha1($valueKey);
    }

    private function translationValueForKeyAndLocale(string $translationKey, string $locale): ?string
    {
        if (! Schema::hasTable('translation_workbench_lang_values')) {
            return null;
        }

        return $this->nullableString(
            TranslationWorkbenchLangValue::query()
                ->where('translation_key', $translationKey)
                ->where('locale', LocaleCode::normalize($locale))
                ->where('status', 'active')
                ->value('value'),
        );
    }

    /**
     * @return array{value_key: string, locale: string, old: ?string, new: ?string}|null
     */
    private function saveDynamicKeyValueForLocale(
        object $selectedFinding,
        string $valueKey,
        string $locale,
        mixed $value,
        ?string $nativeLabel,
        string $source,
    ): ?array {
        if (! Schema::hasTable('translation_workbench_dynamic_key_values') || ! $selectedFinding->key_id) {
            return null;
        }

        $locale = LocaleCode::normalize($locale);
        $valueKey = trim($valueKey);

        if ($locale === '' || $valueKey === '') {
            return null;
        }

        $newValue = $this->nullableString($value);
        $dynamicValue = DB::table('translation_workbench_dynamic_key_values')
            ->where('key_id', $selectedFinding->key_id)
            ->where('value_key', $valueKey)
            ->where('locale', $locale)
            ->first();
        $oldValue = $this->nullableString($dynamicValue?->value);

        if (! $dynamicValue && $newValue === null && $source !== 'runtime_source') {
            return null;
        }

        if ($oldValue === $newValue && $this->nullableString($dynamicValue?->native_label) === $nativeLabel) {
            return null;
        }

        $attributes = [
            'value' => $newValue,
            'native_label' => $nativeLabel,
            'status' => $newValue !== null ? 'active' : 'missing',
            'source' => $source,
            'reviewed_at' => now(),
            'reviewed_by_user_id' => Auth::id(),
            'meta' => json_encode([
                'source' => 'translation-workbench:modal-edit-dynamic-multi',
                'finding_id' => $selectedFinding->id,
                'key_id' => $selectedFinding->key_id,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ];

        if ($dynamicValue) {
            DB::table('translation_workbench_dynamic_key_values')
                ->where('id', $dynamicValue->id)
                ->update($attributes);
        } else {
            DB::table('translation_workbench_dynamic_key_values')
                ->insert([
                    'key_id' => $selectedFinding->key_id,
                    'value_key' => $valueKey,
                    'locale' => $locale,
                    ...$attributes,
                    'created_at' => now(),
                ]);
        }

        return [
            'value_key' => $valueKey,
            'locale' => $locale,
            'old' => $oldValue,
            'new' => $newValue,
        ];
    }

    /**
     * @return array{locale: string, old: ?string, new: ?string}|null
     */
    private function saveTranslationValueForLocale(
        object $selectedFinding,
        string $translationKey,
        string $locale,
        mixed $value,
    ): ?array {
        if (! Schema::hasTable('translation_workbench_lang_values')) {
            return null;
        }

        $locale = LocaleCode::normalize($locale);

        if ($locale === '') {
            return null;
        }

        $newValue = $this->nullableString($value);
        $keyParts = $this->translationKeyLangParts($translationKey);
        $langValue = TranslationWorkbenchLangValue::query()
            ->where('locale', $locale)
            ->where('namespace', $keyParts['namespace'])
            ->where('lang_key', $keyParts['lang_key'])
            ->first();
        $oldValue = $this->nullableString($langValue?->value);
        $newStatus = $newValue !== null ? 'active' : 'missing';

        if (! $langValue && $newValue === null) {
            return null;
        }

        $now = now();
        $localeAttributes = $this->langValueLocaleAttributes($locale);
        $attributes = [
            ...$localeAttributes,
            'translation_key' => $translationKey,
            'value' => $newValue,
            'value_type' => 'string',
            'source_path' => 'lang/' . $locale . '/' . $keyParts['namespace'] . '.php',
            'source_hash' => $newValue !== null ? sha1($newValue) : null,
            'status' => $newStatus,
            'last_seen_at' => $now,
            'meta' => [
                'source' => 'translation-workbench:modal-edit',
                'finding_id' => $selectedFinding->id,
                'key_id' => $selectedFinding->key_id,
            ],
        ];

        if ($langValue) {
            $trackedAttributeKeys = [
                'locale_main',
                'locale_region',
                'is_source_locale',
                'is_target_main_locale',
                'is_target_sub_locale',
                'translation_key',
                'value',
                'value_type',
                'source_path',
                'source_hash',
                'status',
            ];
            $oldAttributes = $langValue->only($trackedAttributeKeys);
            $changedAttributes = collect($attributes)
                ->only($trackedAttributeKeys)
                ->filter(static fn(mixed $value, string $key): bool => $oldAttributes[$key] !== $value)
                ->all();

            if ($oldValue === $newValue && $changedAttributes === []) {
                return null;
            }

            $langValue->forceFill($attributes)->save();
        } else {
            TranslationWorkbenchLangValue::query()->create([
                'locale' => $locale,
                'namespace' => $keyParts['namespace'],
                'lang_key' => $keyParts['lang_key'],
                ...$attributes,
                'first_seen_at' => $now,
                'scan_count' => 0,
            ]);
        }

        return [
            'locale' => $locale,
            'old' => $oldValue,
            'new' => $newValue,
        ];
    }

    /**
     * @return array{namespace: string, lang_key: string}
     */
    private function translationKeyLangParts(string $translationKey): array
    {
        $segments = collect(explode('.', $translationKey))
            ->map(static fn(string $segment): string => trim($segment))
            ->filter(static fn(string $segment): bool => $segment !== '')
            ->values();
        $namespace = (string) ($segments->first() ?: 'messages');
        $langKey = $segments->count() > 1
            ? $segments->slice(1)->implode('.')
            : $namespace;

        return [
            'namespace' => $namespace,
            'lang_key' => $langKey,
        ];
    }

    /**
     * @return array{is_source_locale: bool, locale_role: string, main_locale: string, parent_locale: ?string}
     */
    private function langValueLocaleAttributes(string $locale): array
    {
        $mainLocale = (string) (LocaleCode::parts($locale)['language'] ?? $locale);
        $isSourceLocale = $locale === $this->sourceMainLocale();
        $isSubLocale = str_contains($locale, '-');

        return [
            'is_source_locale' => $isSourceLocale,
            'locale_role' => $isSourceLocale ? 'source_main' : ($isSubLocale ? 'target_sub' : 'target_main'),
            'main_locale' => $isSourceLocale ? $this->sourceMainLocale() : $mainLocale,
            'parent_locale' => $isSubLocale ? $mainLocale : null,
        ];
    }

    /**
     * @return array{0: TranslationWorkbenchKey, 1: TranslationWorkbenchFinding, 2: object}|null
     */
    private function reviewContext(int $findingId): ?array
    {
        if (! $this->hasKeyCandidateReviewColumns()) {
            Flux::toast(
                heading: __('Migration missing'),
                text: __('Run the Translation Workbench migrations before saving UI or dynamic review decisions.'),
                variant: 'warning',
            );

            return null;
        }

        $selectedFinding = $this->selectedFinding($findingId);

        if (! $selectedFinding || ! $selectedFinding->key_id) {
            Flux::toast(
                heading: __('No linked key'),
                text: __('This finding needs a key relation before it can be reviewed as UI or dynamic.'),
                variant: 'warning',
            );

            return null;
        }

        $key = TranslationWorkbenchKey::query()->find($selectedFinding->key_id);
        $finding = TranslationWorkbenchFinding::query()->find($findingId);

        if (! $key || ! $finding) {
            Flux::toast(
                heading: __('Review failed'),
                text: __('The selected finding or key no longer exists.'),
                variant: 'danger',
            );

            return null;
        }

        return [$key, $finding, $selectedFinding];
    }

    private function addKeyCandidateReviewSelects($query): void
    {
        $query->addSelect([
            $this->schemaHasColumn('translation_workbench_keys', 'is_ui_candidate_rejected')
                ? 'keys.is_ui_candidate_rejected'
                : DB::raw('false as is_ui_candidate_rejected'),
            $this->schemaHasColumn('translation_workbench_keys', 'is_dynamic_candidate_rejected')
                ? 'keys.is_dynamic_candidate_rejected'
                : DB::raw('false as is_dynamic_candidate_rejected'),
            $this->schemaHasColumn('translation_workbench_keys', 'reviewed_is_ui_candidate')
                ? 'keys.reviewed_is_ui_candidate'
                : DB::raw('null as reviewed_is_ui_candidate'),
            $this->schemaHasColumn('translation_workbench_keys', 'reviewed_is_dynamic_candidate')
                ? 'keys.reviewed_is_dynamic_candidate'
                : DB::raw('null as reviewed_is_dynamic_candidate'),
            $this->schemaHasColumn('translation_workbench_keys', 'reviewed_is_dynamic_multi')
                ? 'keys.reviewed_is_dynamic_multi'
                : DB::raw('null as reviewed_is_dynamic_multi'),
        ]);
    }

    private function addFindingHistorySelect($query): void
    {
        $historyChecks = [];

        if ($this->schemaHasTable('translation_workbench_timeline_events')) {
            $historyChecks[] = 'EXISTS (
                SELECT 1
                FROM translation_workbench_timeline_events as timeline_events
                WHERE timeline_events.finding_id = findings.id
            )';
            $historyChecks[] = 'EXISTS (
                SELECT 1
                FROM translation_workbench_timeline_events as timeline_events
                WHERE keys.id IS NOT NULL
                    AND timeline_events.key_id = keys.id
            )';
        }

        if ($this->schemaHasTable('translation_workbench_reviews')) {
            $historyChecks[] = 'EXISTS (
                SELECT 1
                FROM translation_workbench_reviews as reviews
                WHERE reviews.finding_id = findings.id
            )';
            $historyChecks[] = 'EXISTS (
                SELECT 1
                FROM translation_workbench_reviews as reviews
                WHERE keys.id IS NOT NULL
                    AND reviews.key_id = keys.id
            )';
        }

        if ($historyChecks === []) {
            $query->addSelect(DB::raw('0 as has_history'));

            return;
        }

        $query->selectRaw(
            'CASE WHEN ' . implode(' OR ', $historyChecks) . ' THEN 1 ELSE 0 END as has_history',
        );
    }

    private function addFindingDynamicContextSelect($query, string $targetLocale): void
    {
        if (Schema::hasTable('translation_workbench_dynamic_key_values')) {
            $query
                ->selectRaw(
                    '(
                        SELECT COUNT(DISTINCT dynamic_values.value_key)
                        FROM translation_workbench_dynamic_key_values as dynamic_values
                        WHERE dynamic_values.key_id = keys.id
                    ) as dynamic_value_count',
                )
                ->selectRaw(
                    '(
                        SELECT COUNT(DISTINCT dynamic_values.value_key)
                        FROM translation_workbench_dynamic_key_values as dynamic_values
                        WHERE dynamic_values.key_id = keys.id
                            AND dynamic_values.locale = ?
                            AND dynamic_values.status = ?
                            AND NULLIF(BTRIM(dynamic_values.value), ?) IS NOT NULL
                    ) as dynamic_target_value_count',
                    [$targetLocale, 'active', ''],
                );
        } else {
            $query->addSelect([
                DB::raw('0 as dynamic_value_count'),
                DB::raw('0 as dynamic_target_value_count'),
            ]);
        }

        if ($this->schemaHasTable('translation_workbench_option_discoveries')) {
            $query
                ->selectRaw(
                    '(
                        SELECT COUNT(*)
                        FROM translation_workbench_option_discoveries as option_discoveries
                        WHERE option_discoveries.status <> ?
                            AND (
                                option_discoveries.suggested_key = keys.translation_key
                                OR option_discoveries.suggested_key = keys.suggested_key
                                OR option_discoveries.suggested_key = findings.suggested_key
                                OR option_discoveries.workbench_suggested_key = keys.translation_key
                                OR option_discoveries.workbench_suggested_key = keys.suggested_key
                                OR option_discoveries.workbench_suggested_key = findings.suggested_key
                                OR option_discoveries.suggested_dynamic_key = keys.translation_key
                                OR option_discoveries.suggested_dynamic_key = keys.suggested_key
                                OR option_discoveries.suggested_dynamic_key = findings.suggested_key
                                OR (
                                    option_discoveries.source_path = source_files.path
                                    AND option_discoveries.source_line = findings.source_line
                                )
                            )
                    ) as dynamic_discovery_count',
                    ['obsolete'],
                )
                ->selectRaw(
                    '(
                        SELECT MAX(option_discoveries.options_count)
                        FROM translation_workbench_option_discoveries as option_discoveries
                        WHERE option_discoveries.status <> ?
                            AND (
                                option_discoveries.suggested_key = keys.translation_key
                                OR option_discoveries.suggested_key = keys.suggested_key
                                OR option_discoveries.suggested_key = findings.suggested_key
                                OR option_discoveries.workbench_suggested_key = keys.translation_key
                                OR option_discoveries.workbench_suggested_key = keys.suggested_key
                                OR option_discoveries.workbench_suggested_key = findings.suggested_key
                                OR option_discoveries.suggested_dynamic_key = keys.translation_key
                                OR option_discoveries.suggested_dynamic_key = keys.suggested_key
                                OR option_discoveries.suggested_dynamic_key = findings.suggested_key
                                OR (
                                    option_discoveries.source_path = source_files.path
                                    AND option_discoveries.source_line = findings.source_line
                                )
                            )
                    ) as dynamic_options_count',
                    ['obsolete'],
                )
                ->selectRaw(
                    '(
                        SELECT STRING_AGG(DISTINCT option_discoveries.source_type, \', \')
                        FROM translation_workbench_option_discoveries as option_discoveries
                        WHERE option_discoveries.status <> ?
                            AND option_discoveries.source_type IS NOT NULL
                            AND option_discoveries.source_type <> ?
                            AND (
                                option_discoveries.suggested_key = keys.translation_key
                                OR option_discoveries.suggested_key = keys.suggested_key
                                OR option_discoveries.suggested_key = findings.suggested_key
                                OR option_discoveries.workbench_suggested_key = keys.translation_key
                                OR option_discoveries.workbench_suggested_key = keys.suggested_key
                                OR option_discoveries.workbench_suggested_key = findings.suggested_key
                                OR option_discoveries.suggested_dynamic_key = keys.translation_key
                                OR option_discoveries.suggested_dynamic_key = keys.suggested_key
                                OR option_discoveries.suggested_dynamic_key = findings.suggested_key
                                OR (
                                    option_discoveries.source_path = source_files.path
                                    AND option_discoveries.source_line = findings.source_line
                                )
                            )
                    ) as dynamic_source_types',
                    ['obsolete', ''],
                );
        } else {
            $query->addSelect([
                DB::raw('0 as dynamic_discovery_count'),
                DB::raw('null as dynamic_options_count'),
                DB::raw('null as dynamic_source_types'),
            ]);
        }
    }

    private function addFindingDynamicSourceSelects($query): void
    {
        if (! $this->schemaHasTable('translation_workbench_dynamic_sources')) {
            $query->addSelect([
                DB::raw('0 as dynamic_source_count'),
                DB::raw('0 as dynamic_multi_source_count'),
                DB::raw('0 as dynamic_source_value_count'),
                DB::raw('0 as dynamic_unresolved_source_count'),
            ]);

            return;
        }

        $sourceMatch = '(
            dynamic_sources.finding_id = findings.id
            OR (
                keys.id IS NOT NULL
                AND dynamic_sources.key_id = keys.id
            )
        )';

        $query
            ->selectRaw(
                "(
                    SELECT COUNT(*)
                    FROM translation_workbench_dynamic_sources as dynamic_sources
                    WHERE dynamic_sources.status <> ?
                        AND {$sourceMatch}
                ) as dynamic_source_count",
                ['obsolete'],
            )
            ->selectRaw(
                "(
                    SELECT COUNT(*)
                    FROM translation_workbench_dynamic_sources as dynamic_sources
                    WHERE dynamic_sources.status <> ?
                        AND {$sourceMatch}
                        AND (
                            dynamic_sources.cardinality = ?
                            OR dynamic_sources.classification LIKE ?
                            OR dynamic_sources.values_count > 1
                        )
                ) as dynamic_multi_source_count",
                ['obsolete', 'multi', 'multi_%'],
            )
            ->selectRaw(
                "(
                    SELECT COALESCE(SUM(dynamic_sources.values_count), 0)
                    FROM translation_workbench_dynamic_sources as dynamic_sources
                    WHERE dynamic_sources.status <> ?
                        AND {$sourceMatch}
                ) as dynamic_source_value_count",
                ['obsolete'],
            )
            ->selectRaw(
                "(
                    SELECT COUNT(*)
                    FROM translation_workbench_dynamic_sources as dynamic_sources
                    WHERE dynamic_sources.status <> ?
                        AND {$sourceMatch}
                        AND (
                            dynamic_sources.status IN (?, ?)
                            OR dynamic_sources.classification = ?
                            OR dynamic_sources.cardinality = ?
                            OR dynamic_sources.origin = ?
                        )
                ) as dynamic_unresolved_source_count",
                ['obsolete', 'needs_review', 'unresolved', 'unknown', 'unknown', 'unknown'],
            );
    }

    /**
     * @return array<int, array{id: int, key_id: int|null, finding_id: int|null, suggested_key: ?string, dynamic_scope: ?string, classification: string, cardinality: string, origin: string, source_type: ?string, source_reference: ?string, source_path: ?string, source_line: ?int, source_expression: ?string, values_count: int, confidence: string, status: string, link_review_status: ?string, is_runtime_options: bool, values: array<int, array{value_key: string, native_label: ?string, status: string}>}>
     */
    private function dynamicReviewSources(?int $findingId): array
    {
        $selectedFinding = $this->selectedFinding($findingId);

        if (! $selectedFinding || ! $this->hasTables([
            'translation_workbench_dynamic_sources',
            'translation_workbench_dynamic_source_values',
        ])) {
            return [];
        }

        $sources = DB::table('translation_workbench_dynamic_sources as sources')
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'sources.key_id')
            ->where('sources.status', '<>', 'obsolete')
            ->where(function ($query) use ($selectedFinding): void {
                $query->where('sources.finding_id', $selectedFinding->id);

                if ($selectedFinding->key_id) {
                    $query->orWhere('sources.key_id', $selectedFinding->key_id);
                }

                $dynamicScopeCandidates = $this->dynamicScopeCandidates($selectedFinding->dynamic_scope ?? null);

                if ($dynamicScopeCandidates !== []) {
                    $query->orWhere(function ($query) use ($dynamicScopeCandidates): void {
                        $query
                            ->whereIn('sources.dynamic_scope', $dynamicScopeCandidates)
                            ->whereIn('sources.source_type', $this->dynamicRuntimeSourceTypes())
                            ->where('sources.values_count', '>', 0);
                    });
                }
            })
            ->orderByRaw("CASE WHEN sources.source_type IN ('runtime_options', 'runtime_db_options') THEN 0 ELSE 1 END")
            ->orderBy('keys.suggested_key')
            ->orderBy('sources.classification')
            ->orderBy('sources.id')
            ->get([
                'sources.id',
                'sources.key_id',
                'sources.finding_id',
                'keys.suggested_key',
                'sources.dynamic_scope',
                'sources.classification',
                'sources.cardinality',
                'sources.origin',
                'sources.source_type',
                'sources.source_reference',
                'sources.source_path',
                'sources.source_line',
                'sources.source_expression',
                'sources.values_count',
                'sources.confidence',
                'sources.status',
            ]);

        if ($sources->isEmpty()) {
            return [];
        }

        $values = DB::table('translation_workbench_dynamic_source_values')
            ->whereIn('dynamic_source_id', $sources->pluck('id')->all())
            ->where('status', '<>', 'obsolete')
            ->orderBy('value_key')
            ->get(['dynamic_source_id', 'value_key', 'native_label', 'status'])
            ->groupBy('dynamic_source_id');

        $runtimeSourceTypes = $this->dynamicRuntimeSourceTypes();
        $runtimeSourceIds = $sources
            ->filter(static fn(object $source): bool => in_array($source->source_type, $runtimeSourceTypes, true))
            ->pluck('id')
            ->all();
        $linkStatuses = collect();

        if ($runtimeSourceIds !== [] && $this->hasTables(['translation_workbench_dynamic_source_candidates'])) {
            $linkStatuses = DB::table('translation_workbench_dynamic_source_candidates')
                ->whereIn('dynamic_source_id', $runtimeSourceIds)
                ->where('candidate_source_type', 'related_dynamic_source')
                ->where('status', 'active')
                ->get(['candidate_reference', 'review_status'])
                ->groupBy(function (object $candidate): int {
                    return (int) str($candidate->candidate_reference)->after('dynamic_source:')->toString();
                })
                ->map(static fn($candidates): string => (string) $candidates->first()->review_status);
        }

        return $sources
            ->map(fn(object $source): array => [
                'id' => (int) $source->id,
                'key_id' => $source->key_id !== null ? (int) $source->key_id : null,
                'finding_id' => $source->finding_id !== null ? (int) $source->finding_id : null,
                'suggested_key' => $this->nullableString($source->suggested_key),
                'dynamic_scope' => $this->nullableString($source->dynamic_scope),
                'classification' => (string) $source->classification,
                'cardinality' => (string) $source->cardinality,
                'origin' => (string) $source->origin,
                'source_type' => $this->nullableString($source->source_type),
                'source_reference' => $this->nullableString($source->source_reference),
                'source_path' => $this->nullableString($source->source_path),
                'source_line' => $source->source_line !== null ? (int) $source->source_line : null,
                'source_expression' => $this->nullableString($source->source_expression),
                'values_count' => (int) $source->values_count,
                'confidence' => (string) $source->confidence,
                'status' => (string) $source->status,
                'link_review_status' => $this->nullableString($linkStatuses->get((int) $source->id)),
                'is_runtime_options' => in_array($source->source_type, $runtimeSourceTypes, true),
                'values' => $values
                    ->get($source->id, collect())
                    ->map(fn(object $value): array => [
                        'value_key' => (string) $value->value_key,
                        'native_label' => $this->nullableString($value->native_label),
                        'status' => (string) $value->status,
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{finding: object, runtime_sources: array<int, array<string, mixed>>, related_source: array<string, mixed>}|null
     */
    private function dynamicSourceLinkPreview(): ?array
    {
        if ($this->dynamicReviewFindingId === null || $this->dynamicSourceLinkRelatedSourceId === null) {
            return null;
        }

        $selectedFinding = $this->selectedFinding($this->dynamicReviewFindingId);

        if (! $selectedFinding) {
            return null;
        }

        $sources = collect($this->dynamicReviewSources($this->dynamicReviewFindingId));
        $runtimeSources = $sources
            ->filter(static fn(array $source): bool => (bool) ($source['is_runtime_options'] ?? false))
            ->values();
        $relatedSource = $sources
            ->firstWhere('id', $this->dynamicSourceLinkRelatedSourceId);

        if ($runtimeSources->isEmpty() || ! is_array($relatedSource)) {
            return null;
        }

        return [
            'finding' => $selectedFinding,
            'runtime_sources' => $runtimeSources->all(),
            'related_source' => $relatedSource,
        ];
    }

    private function isDynamicReviewReady(object $selectedFinding): bool
    {
        if (! $this->isDynamicFinding($selectedFinding)) {
            return false;
        }

        if ((int) ($selectedFinding->dynamic_value_count ?? 0) > 0) {
            return true;
        }

        if ($this->isReviewedDynamicSingleFinding($selectedFinding)) {
            return true;
        }

        if ($this->hasResolvedDynamicSourceValuesForFinding($selectedFinding)) {
            return true;
        }

        if ($this->hasDynamicConsumerSourceForFinding($selectedFinding)) {
            return $this->hasConfirmedDynamicSourceLink($selectedFinding);
        }

        return $this->hasRuntimeOptionSourceForFinding($selectedFinding)
            && (int) ($selectedFinding->dynamic_unresolved_source_count ?? 0) === 0;
    }

    private function isReviewedDynamicSingleFinding(object $selectedFinding): bool
    {
        if ($this->isDynamicMultiFinding($selectedFinding)) {
            return false;
        }

        if (blank($selectedFinding->translation_key ?? null) || ($selectedFinding->review_status ?? null) !== 'reviewed') {
            return false;
        }

        return (bool) ($selectedFinding->is_dynamic_key ?? false)
            || (bool) ($selectedFinding->reviewed_is_dynamic_candidate ?? false)
            || $this->nullableString($selectedFinding->entry_type ?? null) === 'dynamic'
            || $this->nullableString($selectedFinding->candidate_type ?? null) === 'dynamic'
            || $this->nullableString($selectedFinding->kind ?? null) === 'dynamic';
    }

    private function hasResolvedDynamicSourceValuesForFinding(object $selectedFinding): bool
    {
        if ((int) ($selectedFinding->dynamic_unresolved_source_count ?? 0) > 0) {
            return false;
        }

        if (! $this->hasTables([
            'translation_workbench_dynamic_sources',
            'translation_workbench_dynamic_source_values',
        ])) {
            return false;
        }

        return DB::table('translation_workbench_dynamic_source_values as source_values')
            ->join('translation_workbench_dynamic_sources as sources', 'sources.id', '=', 'source_values.dynamic_source_id')
            ->where('sources.status', '<>', 'obsolete')
            ->where('source_values.status', 'active')
            ->where(function ($query) use ($selectedFinding): void {
                $query->where('sources.finding_id', $selectedFinding->id);

                if ($selectedFinding->key_id) {
                    $query->orWhere('sources.key_id', $selectedFinding->key_id);
                }
            })
            ->exists();
    }

    private function hasConfirmedDynamicSourceLink(object $selectedFinding): bool
    {
        if (! $this->hasTables([
            'translation_workbench_dynamic_sources',
            'translation_workbench_dynamic_source_candidates',
        ])) {
            return false;
        }

        $consumerSourceReferences = collect($this->dynamicSourceIdsForFinding($selectedFinding, runtime: false))
            ->map(static fn(int $sourceId): string => 'dynamic_source:' . $sourceId)
            ->all();

        if ($consumerSourceReferences === []) {
            return false;
        }

        return DB::table('translation_workbench_dynamic_source_candidates as candidates')
            ->join('translation_workbench_dynamic_sources as runtime_sources', 'runtime_sources.id', '=', 'candidates.dynamic_source_id')
            ->where('candidates.candidate_source_type', 'related_dynamic_source')
            ->whereIn('candidates.candidate_reference', $consumerSourceReferences)
            ->where('candidates.review_status', 'confirmed')
            ->where('candidates.status', 'active')
            ->where('runtime_sources.status', '<>', 'obsolete')
            ->whereIn('runtime_sources.source_type', $this->dynamicRuntimeSourceTypes())
            ->exists();
    }

    private function hasRuntimeOptionSourceForFinding(object $selectedFinding): bool
    {
        if (! $this->hasTables(['translation_workbench_dynamic_sources'])) {
            return false;
        }

        return DB::table('translation_workbench_dynamic_sources as sources')
            ->where('sources.status', '<>', 'obsolete')
            ->whereIn('sources.source_type', $this->dynamicRuntimeSourceTypes())
            ->where('sources.values_count', '>', 0)
            ->where(function ($query) use ($selectedFinding): void {
                $query->where('sources.finding_id', $selectedFinding->id);

                if ($selectedFinding->key_id) {
                    $query->orWhere('sources.key_id', $selectedFinding->key_id);
                }
            })
            ->exists();
    }

    private function hasDynamicConsumerSourceForFinding(object $selectedFinding): bool
    {
        return $this->dynamicSourceIdsForFinding($selectedFinding, runtime: false) !== [];
    }

    /**
     * @return array<int, int>
     */
    private function dynamicSourceIdsForFinding(object $selectedFinding, bool $runtime): array
    {
        if (! $this->hasTables(['translation_workbench_dynamic_sources'])) {
            return [];
        }

        $runtimeSourceTypes = $this->dynamicRuntimeSourceTypes();

        return DB::table('translation_workbench_dynamic_sources as sources')
            ->where('sources.status', '<>', 'obsolete')
            ->where(function ($query) use ($selectedFinding): void {
                $query->where('sources.finding_id', $selectedFinding->id);

                if ($selectedFinding->key_id) {
                    $query->orWhere('sources.key_id', $selectedFinding->key_id);
                }
            })
            ->when(
                $runtime,
                static fn($query) => $query->whereIn('sources.source_type', $runtimeSourceTypes),
                static fn($query) => $query->where(function ($query) use ($runtimeSourceTypes): void {
                    $query->whereNull('sources.source_type')
                        ->orWhereNotIn('sources.source_type', $runtimeSourceTypes);
                }),
            )
            ->pluck('sources.id')
            ->map(static fn(mixed $sourceId): int => (int) $sourceId)
            ->all();
    }

    /**
     * @return array<int, int>
     */
    private function linkedRuntimeSourceIdsForFinding(object $selectedFinding): array
    {
        if (! $this->hasTables([
            'translation_workbench_dynamic_sources',
            'translation_workbench_dynamic_source_candidates',
        ])) {
            return [];
        }

        return DB::table('translation_workbench_dynamic_source_candidates as candidates')
            ->join('translation_workbench_dynamic_sources as runtime_sources', 'runtime_sources.id', '=', 'candidates.dynamic_source_id')
            ->where('candidates.candidate_source_type', 'related_dynamic_source')
            ->where('candidates.review_status', 'confirmed')
            ->where('candidates.status', 'active')
            ->where('runtime_sources.status', '<>', 'obsolete')
            ->whereIn('runtime_sources.source_type', $this->dynamicRuntimeSourceTypes())
            ->where(function ($query) use ($selectedFinding): void {
                $query->where('candidates.finding_id', $selectedFinding->id);

                if ($selectedFinding->key_id) {
                    $query->orWhere('candidates.key_id', $selectedFinding->key_id);
                }
            })
            ->pluck('runtime_sources.id')
            ->map(static fn(mixed $sourceId): int => (int) $sourceId)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function dynamicRuntimeSourceTypes(): array
    {
        return ['runtime_options', 'runtime_db_options'];
    }

    /**
     * Runtime collectors tend to use snake_case scopes while scanner metadata can retain the
     * original variable shape, for example documentTypeOptions. Treat these as the same scope
     * for review/link display, without changing the stored raw scanner value.
     *
     * @return array<int, string>
     */
    private function dynamicScopeCandidates(mixed $scope): array
    {
        $scope = trim((string) $scope);

        if ($scope === '') {
            return [];
        }

        return collect([
            $scope,
            Str::snake($scope),
            str_replace('-', '_', Str::kebab($scope)),
        ])
            ->map(static fn(string $candidate): string => trim($candidate))
            ->filter(static fn(string $candidate): bool => $candidate !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function isDynamicFinding(object $selectedFinding): bool
    {
        $dataState = $this->nullableString(
            $selectedFinding->key_dynamic_data_state ?? $selectedFinding->dynamic_data_state ?? null,
        );
        $candidateType = $this->nullableString($selectedFinding->candidate_type ?? null);
        $entryType = $this->nullableString($selectedFinding->entry_type ?? null);
        $kind = $this->nullableString($selectedFinding->kind ?? null);

        return $this->isDynamicMultiFinding($selectedFinding)
            || (bool) ($selectedFinding->is_dynamic_key ?? false)
            || (bool) ($selectedFinding->reviewed_is_dynamic_candidate ?? false)
            || $dataState !== null
            || (int) ($selectedFinding->dynamic_source_count ?? 0) > 0
            || $candidateType === 'dynamic'
            || $entryType === 'dynamic'
            || ($kind !== null && str_starts_with($kind, 'dynamic'));
    }

    private function isDynamicMultiFinding(object $selectedFinding): bool
    {
        return (bool) ($selectedFinding->is_dynamic_multi ?? false)
            || (bool) ($selectedFinding->reviewed_is_dynamic_multi ?? false)
            || (int) ($selectedFinding->dynamic_multi_source_count ?? 0) > 0
            || (int) ($selectedFinding->dynamic_value_count ?? 0) > 1
            || $this->hasLinkedRuntimeMultiSourceForFinding($selectedFinding);
    }

    private function hasLinkedRuntimeMultiSourceForFinding(object $selectedFinding): bool
    {
        if (! $this->hasTables([
            'translation_workbench_dynamic_sources',
            'translation_workbench_dynamic_source_candidates',
            'translation_workbench_dynamic_source_values',
        ])) {
            return false;
        }

        return DB::table('translation_workbench_dynamic_source_candidates as candidates')
            ->join('translation_workbench_dynamic_sources as runtime_sources', 'runtime_sources.id', '=', 'candidates.dynamic_source_id')
            ->join('translation_workbench_dynamic_source_values as source_values', 'source_values.dynamic_source_id', '=', 'runtime_sources.id')
            ->select('runtime_sources.id')
            ->where('candidates.candidate_source_type', 'related_dynamic_source')
            ->where('candidates.review_status', 'confirmed')
            ->where('candidates.status', 'active')
            ->where('runtime_sources.status', '<>', 'obsolete')
            ->whereIn('runtime_sources.source_type', $this->dynamicRuntimeSourceTypes())
            ->where('source_values.status', 'active')
            ->where(function ($query) use ($selectedFinding): void {
                $query->where('candidates.finding_id', $selectedFinding->id);

                if ($selectedFinding->key_id) {
                    $query->orWhere('candidates.key_id', $selectedFinding->key_id);
                }
            })
            ->groupBy('runtime_sources.id')
            ->havingRaw('COUNT(DISTINCT source_values.value_key) > 1')
            ->exists();
    }

    private function hasKeyCandidateReviewColumns(): bool
    {
        return $this->schemaHasColumn('translation_workbench_keys', 'is_ui_candidate_rejected')
            && $this->schemaHasColumn('translation_workbench_keys', 'is_dynamic_candidate_rejected')
            && $this->schemaHasColumn('translation_workbench_keys', 'reviewed_is_ui_candidate')
            && $this->schemaHasColumn('translation_workbench_keys', 'reviewed_is_dynamic_candidate')
            && $this->schemaHasColumn('translation_workbench_keys', 'reviewed_is_dynamic_multi');
    }

    private function hasDynamicDataStateColumns(): bool
    {
        return $this->schemaHasColumn('translation_workbench_findings', 'dynamic_data_state')
            && $this->schemaHasColumn('translation_workbench_keys', 'dynamic_data_state');
    }

    /**
     * @return array<string, string|null>
     */
    private function dynamicDataStateAttributes(bool $isDynamic): array
    {
        if (! $this->schemaHasColumn('translation_workbench_keys', 'dynamic_data_state')) {
            return [];
        }

        return [
            'dynamic_data_state' => $isDynamic ? 'unstructured' : null,
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function saveKeyReviewDecision(
        TranslationWorkbenchKey $key,
        TranslationWorkbenchFinding $finding,
        object $selectedFinding,
        array $attributes,
        string $reviewType,
        string $decision,
        string $toastHeading,
        string $toastText,
    ): bool {
        $trackedAttributes = array_values(array_unique([
            ...array_keys($attributes),
            'review_status',
            'reviewed_at',
            'reviewed_by_user_id',
        ]));

        $oldValues = $key->only($trackedAttributes);

        $attributes = [
            ...$attributes,
            'review_status' => 'reviewed',
            'reviewed_at' => now(),
            'reviewed_by_user_id' => Auth::id(),
        ];

        $changedValues = collect($attributes)
            ->filter(static fn(mixed $value, string $attribute): bool => ($oldValues[$attribute] ?? null) != $value)
            ->all();

        if ($changedValues === []) {
            Flux::toast(
                heading: __('No change'),
                text: __('The review decision was already set.'),
                variant: 'warning',
            );

            return false;
        }

        DB::transaction(function () use ($key, $finding, $selectedFinding, $oldValues, $changedValues, $reviewType, $decision): void {
            $key->forceFill($changedValues)->save();

            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key->id,
                'finding_id' => $finding->id,
                'review_type' => $reviewType,
                'decision' => $decision,
                'old_values' => collect($oldValues)->only(array_keys($changedValues))->all(),
                'new_values' => $changedValues,
                'meta' => [
                    'source' => 'translation-workbench:review-modal',
                    'candidate_type' => $selectedFinding->candidate_type,
                    'candidate_reason' => $selectedFinding->candidate_reason,
                    'finding_kind' => $selectedFinding->kind,
                    'finding_entry_type' => $selectedFinding->entry_type,
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            app(TranslationWorkbenchTimelineRecorder::class)->recordReviewEvent(
                review: $review,
                eventType: $decision,
                oldValues: collect($oldValues)->only(array_keys($changedValues))->all(),
                newValues: $changedValues,
                context: [
                    'source' => 'translation-workbench:review-modal',
                    'candidate_type' => $selectedFinding->candidate_type,
                    'candidate_reason' => $selectedFinding->candidate_reason,
                ],
            );

            if (array_key_exists('translation_key', $changedValues)) {
                $this->rekeyLangValuesForTranslationKey(
                    key: $key,
                    finding: $finding,
                    oldTranslationKey: $this->nullableString($oldValues['translation_key'] ?? null),
                    newTranslationKey: $this->nullableString($changedValues['translation_key'] ?? null),
                );
            }
        });

        Flux::toast(
            heading: $toastHeading,
            text: $toastText,
            variant: 'success',
        );

        return true;
    }

    /**
     * Keep already reviewed language values attached to a manually renamed
     * translation key. Without this, old scalar lang keys can keep blocking
     * newly nested keys until they are cleaned up by hand.
     */
    private function rekeyLangValuesForTranslationKey(
        TranslationWorkbenchKey $key,
        TranslationWorkbenchFinding $finding,
        ?string $oldTranslationKey,
        ?string $newTranslationKey,
    ): void {
        if (
            ! Schema::hasTable('translation_workbench_lang_values') ||
            $oldTranslationKey === null ||
            $newTranslationKey === null ||
            $oldTranslationKey === $newTranslationKey
        ) {
            return;
        }

        $newParts = $this->translationKeyLangParts($newTranslationKey);
        $rows = TranslationWorkbenchLangValue::query()
            ->where('translation_key', $oldTranslationKey)
            ->where('status', '<>', 'obsolete')
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            return;
        }

        $now = now();
        $migrated = [];
        $obsoleted = [];

        foreach ($rows as $row) {
            $target = TranslationWorkbenchLangValue::query()
                ->where('locale', $row->locale)
                ->where('namespace', $newParts['namespace'])
                ->where('lang_key', $newParts['lang_key'])
                ->first();

            $oldRowValues = $row->only(['id', 'locale', 'namespace', 'lang_key', 'translation_key', 'status']);

            if ($target && $target->id !== $row->id) {
                $row->forceFill([
                    'status' => 'obsolete',
                    'last_seen_at' => $now,
                    'meta' => [
                        ...(is_array($row->meta) ? $row->meta : []),
                        'obsolete_reason' => 'translation_key_rekey_target_exists',
                        'rekeyed_to_translation_key' => $newTranslationKey,
                        'rekeyed_to_lang_value_id' => $target->id,
                    ],
                ])->save();

                $obsoleted[] = [
                    'id' => $row->id,
                    'locale' => $row->locale,
                    'target_id' => $target->id,
                ];

                continue;
            }

            $row->forceFill([
                'namespace' => $newParts['namespace'],
                'lang_key' => $newParts['lang_key'],
                'translation_key' => $newTranslationKey,
                'source_path' => 'lang/' . $row->locale . '/' . $newParts['namespace'] . '.php',
                'last_seen_at' => $now,
                'meta' => [
                    ...(is_array($row->meta) ? $row->meta : []),
                    'source' => 'translation-workbench:translation-key-review',
                    'previous_translation_key' => $oldTranslationKey,
                    'previous_namespace' => $oldRowValues['namespace'] ?? null,
                    'previous_lang_key' => $oldRowValues['lang_key'] ?? null,
                ],
            ])->save();

            $migrated[] = [
                'id' => $row->id,
                'locale' => $row->locale,
                'old_namespace' => $oldRowValues['namespace'] ?? null,
                'old_lang_key' => $oldRowValues['lang_key'] ?? null,
                'new_namespace' => $newParts['namespace'],
                'new_lang_key' => $newParts['lang_key'],
            ];
        }

        app(TranslationWorkbenchTimelineRecorder::class)->recordKeyFindingEvent(
            key: $key,
            finding: $finding,
            eventType: 'translation_lang_values_rekeyed',
            oldValues: [
                'translation_key' => $oldTranslationKey,
            ],
            newValues: [
                'translation_key' => $newTranslationKey,
                'migrated' => $migrated,
                'obsoleted' => $obsoleted,
            ],
            context: [
                'source' => 'translation-workbench:translation-key-review',
                'migrated_count' => count($migrated),
                'obsoleted_count' => count($obsoleted),
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function keyStructureFromTranslationKey(?string $translationKey, ?TranslationWorkbenchKey $currentKey = null): array
    {
        $translationKey = $this->nullableString($translationKey);

        if ($translationKey === null) {
            return [
                'translation_key' => null,
                'namespace' => null,
                'group' => null,
                'path_key' => null,
                'scope' => null,
                'key_segment_domain' => null,
                'key_segment_section' => null,
                'key_segment_context' => null,
                'key_segment_extra' => null,
                'key_segment_name' => null,
                'is_ui_key' => false,
            ];
        }

        $isUiKey = $this->isUiTranslationKey($translationKey);

        return [
            'translation_key' => $translationKey,
            ...app(TranslationKeyPartsFactory::class)->fromKey($translationKey),
            ...app(TranslationKeySegmentFactory::class)->fromKey($translationKey),
            'is_ui_key' => $isUiKey,
            ...($isUiKey ? [
                'is_dynamic_key' => false,
                'is_dynamic_multi' => false,
                ...$this->dynamicDataStateAttributes(false),
                'key_type' => 'ui',
            ] : $this->nonUiClassificationAttributes($currentKey)),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function nonUiClassificationAttributes(?TranslationWorkbenchKey $currentKey): array
    {
        if (! $currentKey || $currentKey->key_type !== 'ui') {
            return [];
        }

        $isDynamic = (bool) $currentKey->is_dynamic_key || (bool) $currentKey->is_dynamic_multi;

        return [
            'key_type' => $isDynamic ? 'dynamic' : 'static',
        ];
    }

    private function isUiTranslationKey(?string $translationKey): bool
    {
        $translationKey = $this->nullableString($translationKey);

        return $translationKey !== null && str_starts_with($translationKey, 'ui.');
    }

    private function withTranslationKeyNamespace(?string $translationKey, string $namespace, array $removeNamespaces = []): ?string
    {
        $translationKey = $this->nullableString($translationKey);

        if ($translationKey === null) {
            return null;
        }

        foreach (array_unique([$namespace, ...$removeNamespaces]) as $removeNamespace) {
            $translationKey = $this->withoutTranslationKeyNamespace($translationKey, $removeNamespace);
        }

        return $namespace . '.' . trim((string) $translationKey, '.');
    }

    private function withoutTranslationKeyNamespace(?string $translationKey, string $namespace): ?string
    {
        $translationKey = $this->nullableString($translationKey);

        if ($translationKey === null) {
            return null;
        }

        $segments = collect(explode('.', trim($translationKey, '.')))
            ->filter(static fn(string $segment): bool => $segment !== '')
            ->values();

        if ($segments->first() === $namespace) {
            $segments = $segments->slice(1)->values();
        }

        $key = $segments->implode('.');

        return $key !== '' ? $key : null;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function applyFindingNamespaceFilter(
        $query,
        string $namespace,
        string $findingAlias = 'findings',
        string $keyAlias = 'keys',
    ): void {
        if ($namespace === 'NULL') {
            $query->where(function ($query) use ($findingAlias, $keyAlias): void {
                $query
                    ->whereNull($findingAlias . '.namespace')
                    ->whereNull($keyAlias . '.namespace');
            });

            return;
        }

        $query->where(function ($query) use ($namespace, $findingAlias, $keyAlias): void {
            $query
                ->where($findingAlias . '.namespace', $namespace)
                ->orWhere($keyAlias . '.namespace', $namespace);
        });
    }

    private function applyFindingGroupFilter(
        $query,
        string $group,
        string $findingAlias = 'findings',
        string $keyAlias = 'keys',
    ): void {
        if ($group === 'NULL') {
            $query->where(function ($query) use ($findingAlias, $keyAlias): void {
                $query
                    ->whereNull($findingAlias . '.group')
                    ->whereNull($keyAlias . '.group');
            });

            return;
        }

        $query->where(function ($query) use ($group, $findingAlias, $keyAlias): void {
            $query
                ->where($findingAlias . '.group', $group)
                ->orWhere($keyAlias . '.group', $group);
        });
    }

    private function applyFindingFilters($query, string $sourceLocale, string $targetLocale): void
    {
        if ($this->findingStatus !== 'all') {
            $query->where('findings.status', $this->findingStatus);
        } elseif (! $this->showObsoleteFindings) {
            $query->where('findings.status', '!=', 'obsolete');
        }

        if ($this->findingKind !== 'all') {
            $query->where('findings.kind', $this->findingKind);
        }

        if ($this->findingCandidateType !== 'all') {
            match ($this->findingCandidateType) {
                'NULL' => $query->whereNull('findings.candidate_type'),
                'is_ui' => $query->where('keys.is_ui_key', true),
                'is_dynamic' => $query
                    ->where('keys.is_dynamic_key', true)
                    ->where('keys.is_dynamic_multi', false),
                'dynamic_multi' => $query->where('keys.is_dynamic_multi', true),
                'dynamic_numeric' => $query->where('findings.kind', 'dynamic_numeric'),
                'dynamic_unstructured' => $this->hasDynamicDataStateColumns()
                    ? $query->where(function ($query): void {
                        $query
                            ->where('keys.dynamic_data_state', 'unstructured')
                            ->orWhere('findings.dynamic_data_state', 'unstructured');
                    })
                    : $query->whereRaw('1 = 0'),
                default => $query->where('findings.candidate_type', $this->findingCandidateType),
            };
        }

        if ($this->findingNamespace !== 'all') {
            $this->applyFindingNamespaceFilter($query, $this->findingNamespace);
        }

        if ($this->findingGroup !== 'all') {
            $this->applyFindingGroupFilter($query, $this->findingGroup);
        }

        if ($this->findingKeyRelation === 'linked') {
            $query->whereRaw("NULLIF(BTRIM(keys.translation_key), '') IS NOT NULL");
        }

        if ($this->findingKeyRelation === 'missing') {
            $query->whereRaw("NULLIF(BTRIM(keys.translation_key), '') IS NULL");
        }

        if (in_array($this->findingKeyRelation, ['translation_all', 'translation_open', 'translation_done'], true)) {
            $state = str_replace('translation_', '', $this->findingKeyRelation);

            $this->applyFindingTranslationWorkflowFilter($query, $sourceLocale, $targetLocale, $state);
        }

        if (in_array($this->findingKeyRelation, ['dynamic_all', 'dynamic_open', 'dynamic_done'], true)) {
            $state = str_replace('dynamic_', '', $this->findingKeyRelation);

            $this->applyFindingDynamicWorkflowFilter($query, $targetLocale, $state);
        }

        if (in_array($this->findingKeyRelation, ['shared_candidates', 'shared_candidates_open', 'shared_candidates_done'], true)) {
            $query
                ->whereRaw("NULLIF(BTRIM(COALESCE(findings.literal_text, findings.literal_text_suggested, ?)), ?) IS NOT NULL", ['', ''])
                ->where('bulk_literals.bulk_literal_count', '>', 1);

            if ($this->findingKeyRelation === 'shared_candidates_done') {
                $this->applyBulkEqualizedFilter($query, true);
            }

            if ($this->findingKeyRelation === 'shared_candidates_open') {
                $selectedBulkLiteral = $this->selectedBulkEqualizeLiteral();
                $selectedBulkIds = collect($this->bulkEqualizeSelectedFindingIds)
                    ->map(static fn(mixed $id): int => (int) $id)
                    ->filter(static fn(int $id): bool => $id > 0)
                    ->unique()
                    ->values();

                if ($selectedBulkLiteral !== null || $selectedBulkIds->isNotEmpty()) {
                    $query->where(function ($query) use ($selectedBulkLiteral, $selectedBulkIds): void {
                        $this->applyBulkEqualizedFilter($query, false);

                        if ($selectedBulkLiteral !== null) {
                            $query->orWhere('bulk_literals.bulk_literal', $selectedBulkLiteral);
                        }

                        if ($selectedBulkIds->isNotEmpty()) {
                            $query->orWhereIn('findings.id', $selectedBulkIds->all());
                        }
                    });
                } else {
                    $this->applyBulkEqualizedFilter($query, false);
                }
            }
        }

        match ($this->findingLiteralState) {
            'source_available' => $this->applyLanguageLiteralExistsFilter($query, $sourceLocale, true),
            'source_missing' => $this->applyLanguageLiteralExistsFilter($query, $sourceLocale, false),
            'target_available' => $this->applyLanguageLiteralExistsFilter($query, $targetLocale, true),
            'target_missing' => $this->applyLanguageLiteralExistsFilter($query, $targetLocale, false),
            default => null,
        };

        $search = trim($this->findingSearch);

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                foreach ($this->findingSearchColumns() as $column) {
                    $this->orWhereFindingSearchColumn($query, $column, $search);
                }
            });
        }
    }

    private function applyFindingTranslationWorkflowFilter($query, string $sourceLocale, string $targetLocale, string $state): void
    {
        $this->applyFindingStaticTranslationScope($query);

        if ($state === 'done') {
            $this->applyFindingTranslationDoneScope($query, $sourceLocale, $targetLocale);

            return;
        }

        if ($state === 'open') {
            $query->where(function ($query) use ($sourceLocale, $targetLocale): void {
                $query
                    ->whereNull('keys.id')
                    ->orWhere('keys.review_status', '!=', 'reviewed')
                    ->orWhereNull('keys.review_status')
                    ->orWhereRaw("NULLIF(BTRIM(keys.translation_key), '') IS NULL")
                    ->orWhereNotExists(function ($query) use ($sourceLocale): void {
                        $this->applyFindingLangValueExistsQuery($query, $sourceLocale, 'filter_source_values');
                    })
                    ->orWhereNotExists(function ($query) use ($targetLocale): void {
                        $this->applyFindingLangValueExistsQuery($query, $targetLocale, 'filter_target_values');
                    });
            });
        }
    }

    private function applyFindingDynamicWorkflowFilter($query, string $targetLocale, string $state): void
    {
        $this->applyFindingDynamicTranslationScope($query);

        if ($state === 'done') {
            $this->applyFindingDynamicDoneScope($query, $targetLocale);

            return;
        }

        if ($state === 'open') {
            [$totalSql, $targetSql] = $this->findingDynamicValueCountSql();

            $query->where(function ($query) use ($totalSql, $targetSql, $targetLocale): void {
                $query
                    ->whereNull('keys.id')
                    ->orWhere('keys.review_status', '!=', 'reviewed')
                    ->orWhereNull('keys.review_status')
                    ->orWhereRaw("NULLIF(BTRIM(keys.translation_key), '') IS NULL")
                    ->orWhereRaw($totalSql . ' = 0')
                    ->orWhereRaw($targetSql . ' < ' . $totalSql, [$targetLocale, 'active']);
            });
        }
    }

    private function applyFindingStaticTranslationScope($query): void
    {
        $query
            ->where(function ($query): void {
                $query
                    ->whereNull('keys.is_dynamic_key')
                    ->orWhere('keys.is_dynamic_key', false);
            })
            ->where(function ($query): void {
                $query
                    ->whereNull('keys.is_dynamic_multi')
                    ->orWhere('keys.is_dynamic_multi', false);
            })
            ->where(function ($query): void {
                $query
                    ->whereNull('findings.candidate_type')
                    ->orWhere('findings.candidate_type', '!=', 'dynamic');
            })
            ->where(function ($query): void {
                $query
                    ->whereNull('findings.entry_type')
                    ->orWhereNotIn('findings.entry_type', ['dynamic', 'dynamic_numeric']);
            })
            ->where(function ($query): void {
                $query
                    ->whereNull('findings.kind')
                    ->orWhere('findings.kind', 'not like', 'dynamic%');
            });
    }

    private function applyFindingDynamicTranslationScope($query): void
    {
        $query
            ->where(function ($query): void {
                $query
                    ->whereNull('findings.kind')
                    ->orWhere('findings.kind', '!=', 'dynamic_numeric');
            })
            ->where(function ($query): void {
                $query
                    ->where('keys.is_dynamic_key', true)
                    ->orWhere('keys.is_dynamic_multi', true)
                    ->orWhere('findings.candidate_type', 'dynamic')
                    ->orWhere('findings.entry_type', 'dynamic')
                    ->orWhere('findings.kind', 'like', 'dynamic%');

                if ($this->schemaHasColumn('translation_workbench_findings', 'dynamic_data_state')) {
                    $query->orWhereNotNull('findings.dynamic_data_state');
                }

                if ($this->schemaHasColumn('translation_workbench_keys', 'dynamic_data_state')) {
                    $query->orWhereNotNull('keys.dynamic_data_state');
                }
            });
    }

    private function applyFindingTranslationDoneScope($query, string $sourceLocale, string $targetLocale): void
    {
        $query
            ->where('keys.review_status', 'reviewed')
            ->whereRaw("NULLIF(BTRIM(keys.translation_key), '') IS NOT NULL")
            ->whereExists(function ($query) use ($sourceLocale): void {
                $this->applyFindingLangValueExistsQuery($query, $sourceLocale, 'filter_source_values');
            })
            ->whereExists(function ($query) use ($targetLocale): void {
                $this->applyFindingLangValueExistsQuery($query, $targetLocale, 'filter_target_values');
            });
    }

    private function applyFindingDynamicDoneScope($query, string $targetLocale): void
    {
        [$totalSql, $targetSql] = $this->findingDynamicValueCountSql();

        $query
            ->where('keys.review_status', 'reviewed')
            ->whereRaw("NULLIF(BTRIM(keys.translation_key), '') IS NOT NULL")
            ->whereRaw($totalSql . ' > 0')
            ->whereRaw($targetSql . ' >= ' . $totalSql, [$targetLocale, 'active']);
    }

    private function applyFindingLangValueExistsQuery($query, string $locale, string $alias): void
    {
        $query
            ->selectRaw('1')
            ->from('translation_workbench_lang_values as ' . $alias)
            ->where($alias . '.locale', $locale)
            ->where($alias . '.status', 'active')
            ->whereRaw('NULLIF(BTRIM(' . $alias . '.value), ?) IS NOT NULL', [''])
            ->where(function ($query) use ($alias): void {
                $query
                    ->whereColumn($alias . '.translation_key', 'keys.translation_key')
                    ->orWhereColumn($alias . '.translation_key', 'keys.suggested_key')
                    ->orWhereColumn($alias . '.translation_key', 'findings.suggested_key')
                    ->orWhereColumn($alias . '.translation_key', 'findings.found_translation_key');
            });
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function findingDynamicValueCountSql(): array
    {
        $totalSql = '(SELECT COUNT(DISTINCT dynamic_total_values.value_key)
            FROM translation_workbench_dynamic_key_values as dynamic_total_values
            WHERE dynamic_total_values.key_id = keys.id)';
        $targetSql = '(SELECT COUNT(DISTINCT dynamic_target_values.value_key)
            FROM translation_workbench_dynamic_key_values as dynamic_target_values
            WHERE dynamic_target_values.key_id = keys.id
                AND dynamic_target_values.locale = ?
                AND dynamic_target_values.status = ?
                AND NULLIF(BTRIM(dynamic_target_values.value), \'\') IS NOT NULL)';

        return [$totalSql, $targetSql];
    }

    private function bulkLiteralCountsQuery(string $sourceLocale, string $targetLocale)
    {
        $keyLinks = DB::table('translation_workbench_key_findings')
            ->selectRaw('finding_id, MIN(key_id) as key_id')
            ->where('status', 'active')
            ->groupBy('finding_id');
        $bulkLiteralExpression = $this->bulkLiteralSqlExpression('duplicate_findings');

        $query = DB::table('translation_workbench_findings as duplicate_findings')
            ->join('translation_workbench_source_files as duplicate_source_files', 'duplicate_source_files.id', '=', 'duplicate_findings.source_file_id')
            ->leftJoinSub($keyLinks, 'duplicate_key_links', function ($join): void {
                $join->on('duplicate_key_links.finding_id', '=', 'duplicate_findings.id');
            })
            ->leftJoin('translation_workbench_keys as duplicate_keys', 'duplicate_keys.id', '=', 'duplicate_key_links.key_id')
            ->selectRaw($bulkLiteralExpression . ' as bulk_literal')
            ->selectRaw('COUNT(*) as bulk_literal_count')
            ->whereRaw("NULLIF(BTRIM(COALESCE(duplicate_findings.literal_text, duplicate_findings.literal_text_suggested, ?)), ?) IS NOT NULL", ['', '']);

        $this->applyBulkLiteralCountScopeFilters($query, $sourceLocale, $targetLocale);

        return $query->groupByRaw($bulkLiteralExpression);
    }

    private function needsBulkLiteralCounts(): bool
    {
        return in_array($this->findingKeyRelation, [
            'shared_candidates',
            'shared_candidates_open',
            'shared_candidates_done',
        ], true) || $this->bulkEqualizeSelectedFindingIds !== [];
    }

    private function applyBulkLiteralCountScopeFilters($query, string $sourceLocale, string $targetLocale): void
    {
        if ($this->findingStatus !== 'all') {
            $query->where('duplicate_findings.status', $this->findingStatus);
        } elseif (! $this->showObsoleteFindings) {
            $query->where('duplicate_findings.status', '!=', 'obsolete');
        }

        if ($this->findingKind !== 'all') {
            $query->where('duplicate_findings.kind', $this->findingKind);
        }

        if ($this->findingCandidateType !== 'all') {
            match ($this->findingCandidateType) {
                'NULL' => $query->whereNull('duplicate_findings.candidate_type'),
                'is_ui' => $query->where('duplicate_keys.is_ui_key', true),
                'is_dynamic' => $query
                    ->where('duplicate_keys.is_dynamic_key', true)
                    ->where('duplicate_keys.is_dynamic_multi', false),
                'dynamic_multi' => $query->where('duplicate_keys.is_dynamic_multi', true),
                'dynamic_numeric' => $query->where('duplicate_findings.kind', 'dynamic_numeric'),
                'dynamic_unstructured' => $this->hasDynamicDataStateColumns()
                    ? $query->where(function ($query): void {
                        $query
                            ->where('duplicate_keys.dynamic_data_state', 'unstructured')
                            ->orWhere('duplicate_findings.dynamic_data_state', 'unstructured');
                    })
                    : $query->whereRaw('1 = 0'),
                default => $query->where('duplicate_findings.candidate_type', $this->findingCandidateType),
            };
        }

        if ($this->findingNamespace !== 'all') {
            $this->applyFindingNamespaceFilter($query, $this->findingNamespace, 'duplicate_findings', 'duplicate_keys');
        }

        if ($this->findingGroup !== 'all') {
            $this->applyFindingGroupFilter($query, $this->findingGroup, 'duplicate_findings', 'duplicate_keys');
        }

        match ($this->findingLiteralState) {
            'source_available' => $this->applyLanguageLiteralExistsFilter($query, $sourceLocale, true, 'duplicate_findings', 'duplicate_keys'),
            'source_missing' => $this->applyLanguageLiteralExistsFilter($query, $sourceLocale, false, 'duplicate_findings', 'duplicate_keys'),
            'target_available' => $this->applyLanguageLiteralExistsFilter($query, $targetLocale, true, 'duplicate_findings', 'duplicate_keys'),
            'target_missing' => $this->applyLanguageLiteralExistsFilter($query, $targetLocale, false, 'duplicate_findings', 'duplicate_keys'),
            default => null,
        };

        $search = trim($this->findingSearch);

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                foreach ($this->findingSearchColumns('duplicate_source_files', 'duplicate_findings', 'duplicate_keys') as $column) {
                    $this->orWhereFindingSearchColumn($query, $column, $search);
                }
            });
        }
    }

    private function addFindingBulkEqualizedSelect($query): void
    {
        if (! $this->hasTables(['translation_workbench_reviews'])) {
            $query->addSelect(DB::raw('0 as was_bulk_equalized'));

            return;
        }

        $query->selectRaw(
            'CASE WHEN EXISTS (
                SELECT 1
                FROM translation_workbench_reviews as bulk_reviews
                WHERE bulk_reviews.decision = ?
                    AND bulk_reviews.finding_id = findings.id
            ) OR EXISTS (
                SELECT 1
                FROM translation_workbench_reviews as bulk_reviews
                WHERE bulk_reviews.decision = ?
                    AND keys.id IS NOT NULL
                    AND bulk_reviews.key_id = keys.id
            ) THEN 1 ELSE 0 END as was_bulk_equalized',
            ['translation_key_bulk_equalized', 'translation_key_bulk_equalized'],
        );
    }

    private function applyBulkEqualizedFilter(
        $query,
        bool $equalized,
        string $findingAlias = 'findings',
        string $keyAlias = 'keys',
    ): void {
        if (! $this->hasTables(['translation_workbench_reviews'])) {
            $query->whereRaw($equalized ? '1 = 0' : '1 = 1');

            return;
        }

        if ($equalized) {
            $query->where(function ($query) use ($findingAlias, $keyAlias): void {
                $query
                    ->whereExists(function ($query) use ($findingAlias): void {
                        $query
                            ->selectRaw('1')
                            ->from('translation_workbench_reviews as bulk_reviews')
                            ->where('bulk_reviews.decision', 'translation_key_bulk_equalized')
                            ->whereColumn('bulk_reviews.finding_id', $findingAlias . '.id');
                    })
                    ->orWhereExists(function ($query) use ($keyAlias): void {
                        $query
                            ->selectRaw('1')
                            ->from('translation_workbench_reviews as bulk_reviews')
                            ->where('bulk_reviews.decision', 'translation_key_bulk_equalized')
                            ->whereNotNull($keyAlias . '.id')
                            ->whereColumn('bulk_reviews.key_id', $keyAlias . '.id');
                    });
            });

            return;
        }

        $query
            ->whereNotExists(function ($query) use ($findingAlias): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_reviews as bulk_reviews')
                    ->where('bulk_reviews.decision', 'translation_key_bulk_equalized')
                    ->whereColumn('bulk_reviews.finding_id', $findingAlias . '.id');
            })
            ->whereNotExists(function ($query) use ($keyAlias): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_reviews as bulk_reviews')
                    ->where('bulk_reviews.decision', 'translation_key_bulk_equalized')
                    ->whereNotNull($keyAlias . '.id')
                    ->whereColumn('bulk_reviews.key_id', $keyAlias . '.id');
            });
    }

    private function bulkLiteralSqlExpression(string $tableAlias): string
    {
        return "LOWER(REGEXP_REPLACE(BTRIM(COALESCE({$tableAlias}.literal_text, {$tableAlias}.literal_text_suggested, '')), '\\s+', ' ', 'g'))";
    }

    /**
     * @return array{
     *     selected_ids: array<int, int>,
     *     selected_count: int,
     *     rows: array<int, array<string, mixed>>,
     *     literal: ?string,
     *     normalized_literal: ?string,
     *     literal_count: int,
     *     missing_key_count: int,
     *     can_confirm: bool,
     *     suggested_target_key: ?string,
     *     translation_keys: array<int, string>,
     *     suggested_keys: array<int, string>
     * }
     */
    private function bulkEqualizeContext(): array
    {
        $selectedIds = collect($this->bulkEqualizeSelectedFindingIds)
            ->map(static fn(mixed $id): int => (int) $id)
            ->filter(static fn(int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($selectedIds->isEmpty() || ! $this->hasTables([
            'translation_workbench_findings',
            'translation_workbench_keys',
            'translation_workbench_key_findings',
        ])) {
            return $this->emptyBulkEqualizeContext($selectedIds->all());
        }

        $keyLinks = DB::table('translation_workbench_key_findings')
            ->selectRaw('finding_id, MIN(key_id) as key_id')
            ->where('status', 'active')
            ->groupBy('finding_id');

        $rows = DB::table('translation_workbench_findings as findings')
            ->leftJoinSub($keyLinks, 'key_links', function ($join): void {
                $join->on('key_links.finding_id', '=', 'findings.id');
            })
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'key_links.key_id')
            ->whereIn('findings.id', $selectedIds->all())
            ->select([
                'findings.id',
                'findings.literal_text',
                'findings.literal_text_suggested',
                'findings.suggested_key as finding_suggested_key',
                'findings.existing_key',
                'findings.found_translation_key',
                'keys.id as key_id',
                'keys.translation_key',
                'keys.suggested_key as key_suggested_key',
                'keys.review_status',
            ])
            ->orderBy('findings.id')
            ->get()
            ->map(function (object $row): array {
                $literal = $this->nullableString($row->literal_text ?? null)
                    ?? $this->nullableString($row->literal_text_suggested ?? null);

                return [
                    'id' => (int) $row->id,
                    'key_id' => $row->key_id !== null ? (int) $row->key_id : null,
                    'literal' => $literal,
                    'normalized_literal' => $this->normalizedBulkLiteral($literal),
                    'translation_key' => $this->nullableString($row->translation_key ?? null),
                    'key_suggested_key' => $this->nullableString($row->key_suggested_key ?? null),
                    'finding_suggested_key' => $this->nullableString($row->finding_suggested_key ?? null),
                    'existing_key' => $this->nullableString($row->existing_key ?? null),
                    'found_translation_key' => $this->nullableString($row->found_translation_key ?? null),
                    'review_status' => $this->nullableString($row->review_status ?? null),
                ];
            })
            ->all();

        $literalGroups = collect($rows)
            ->pluck('normalized_literal')
            ->filter()
            ->unique()
            ->values();
        $translationKeys = collect($rows)
            ->pluck('translation_key')
            ->filter()
            ->unique()
            ->values()
            ->all();
        $suggestedKeys = collect($rows)
            ->flatMap(static fn(array $row): array => [
                $row['key_suggested_key'],
                $row['finding_suggested_key'],
            ])
            ->filter()
            ->unique()
            ->values()
            ->all();
        $literal = collect($rows)
            ->pluck('literal')
            ->filter()
            ->first();
        $missingKeyCount = collect($rows)
            ->filter(static fn(array $row): bool => empty($row['key_id']))
            ->count();
        $existingSharedKey = $literalGroups->count() === 1
            ? $this->existingBulkSharedKeyForLiteral((string) $literalGroups->first(), $selectedIds->all())
            : null;
        $suggestedTargetKey = collect($translationKeys)->first()
            ?? ($existingSharedKey['translation_key'] ?? null)
            ?? collect($suggestedKeys)->first()
            ?? $this->bulkTranslationKeySuggestionFromLiteral($literal);
        $canConfirm = count($rows) >= 1
            && $literalGroups->count() === 1
            && ($missingKeyCount === 0 || $existingSharedKey !== null)
            && filled($suggestedTargetKey)
            && (count($rows) >= 2 || $existingSharedKey !== null);

        return [
            'selected_ids' => $selectedIds->all(),
            'selected_count' => count($rows),
            'rows' => $rows,
            'literal' => $literal,
            'normalized_literal' => $literalGroups->first(),
            'literal_count' => $literalGroups->count(),
            'missing_key_count' => $missingKeyCount,
            'can_confirm' => $canConfirm,
            'equalize_to_existing_shared_key' => $existingSharedKey !== null && collect($translationKeys)->isEmpty(),
            'existing_shared_key' => $existingSharedKey['translation_key'] ?? null,
            'existing_shared_key_count' => $existingSharedKey['finding_count'] ?? 0,
            'suggested_target_key' => $suggestedTargetKey,
            'translation_keys' => $translationKeys,
            'suggested_keys' => $suggestedKeys,
        ];
    }

    private function shouldRemindBulkEqualizeAfterTranslationSave(int $findingId): bool
    {
        $selectedIds = collect($this->bulkEqualizeSelectedFindingIds)
            ->map(static fn(mixed $id): int => (int) $id)
            ->filter(static fn(int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($selectedIds->count() < 1 || ! $selectedIds->contains($findingId)) {
            return false;
        }

        $context = $this->bulkEqualizeContext();

        return (bool) ($context['can_confirm'] ?? false);
    }

    /**
     * @param  array<int, int>  $selectedIds
     * @return array{translation_key: string, finding_count: int}|null
     */
    private function existingBulkSharedKeyForLiteral(string $normalizedLiteral, array $selectedIds): ?array
    {
        if ($normalizedLiteral === '' || ! $this->hasTables([
            'translation_workbench_findings',
            'translation_workbench_key_findings',
            'translation_workbench_keys',
            'translation_workbench_reviews',
        ])) {
            return null;
        }

        $literalExpression = $this->bulkLiteralSqlExpression('findings');
        $selectedIds = collect($selectedIds)
            ->map(static fn(mixed $id): int => (int) $id)
            ->filter(static fn(int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        $query = DB::table('translation_workbench_findings as findings')
            ->join('translation_workbench_key_findings as key_findings', function ($join): void {
                $join
                    ->on('key_findings.finding_id', '=', 'findings.id')
                    ->where('key_findings.status', '=', 'active');
            })
            ->join('translation_workbench_keys as keys', 'keys.id', '=', 'key_findings.key_id')
            ->where('findings.status', 'active')
            ->whereRaw($literalExpression . ' = ?', [$normalizedLiteral])
            ->whereRaw("NULLIF(BTRIM(keys.translation_key), '') IS NOT NULL")
            ->where(function ($query): void {
                $query
                    ->whereExists(function ($query): void {
                        $query
                            ->selectRaw('1')
                            ->from('translation_workbench_reviews as bulk_reviews')
                            ->where('bulk_reviews.decision', 'translation_key_bulk_equalized')
                            ->whereColumn('bulk_reviews.finding_id', 'findings.id');
                    })
                    ->orWhereExists(function ($query): void {
                        $query
                            ->selectRaw('1')
                            ->from('translation_workbench_reviews as bulk_reviews')
                            ->where('bulk_reviews.decision', 'translation_key_bulk_equalized')
                            ->whereColumn('bulk_reviews.key_id', 'keys.id');
                    });
            });

        if ($selectedIds !== []) {
            $query->whereNotIn('findings.id', $selectedIds);
        }

        $row = $query
            ->selectRaw('keys.translation_key, COUNT(DISTINCT findings.id) as finding_count')
            ->groupBy('keys.translation_key')
            ->orderByDesc('finding_count')
            ->orderBy('keys.translation_key')
            ->first();

        if (! $row || ! filled($row->translation_key ?? null)) {
            return null;
        }

        return [
            'translation_key' => (string) $row->translation_key,
            'finding_count' => (int) $row->finding_count,
        ];
    }

    /**
     * @return array<int, int>
     */
    private function bulkEqualizeSelectableFindingIds(string $selectedLiteral): array
    {
        if (! $this->hasTables([
            'translation_workbench_findings',
            'translation_workbench_source_files',
            'translation_workbench_key_findings',
            'translation_workbench_keys',
            'translation_workbench_lang_values',
        ])) {
            return [];
        }

        $sourceLocale = $this->sourceMainLocale();
        $targetLocale = (string) ($this->editLocales()['active'] ?? app()->getLocale());
        $findingBulkLiteralExpression = $this->bulkLiteralSqlExpression('findings');
        $keyLinks = DB::table('translation_workbench_key_findings')
            ->selectRaw('finding_id, MIN(key_id) as key_id')
            ->where('status', 'active')
            ->groupBy('finding_id');
        $bulkLiteralCounts = $this->bulkLiteralCountsQuery($sourceLocale, $targetLocale);

        $query = DB::table('translation_workbench_findings as findings')
            ->join('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->leftJoinSub($keyLinks, 'key_links', function ($join): void {
                $join->on('key_links.finding_id', '=', 'findings.id');
            })
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'key_links.key_id')
            ->leftJoinSub($bulkLiteralCounts, 'bulk_literals', function ($join) use ($findingBulkLiteralExpression): void {
                $join->on('bulk_literals.bulk_literal', '=', DB::raw($findingBulkLiteralExpression));
            })
            ->where('bulk_literals.bulk_literal', $selectedLiteral)
            ->where('bulk_literals.bulk_literal_count', '>', 1)
            ->whereNotNull('key_links.key_id');

        $this->applyFindingFilters($query, $sourceLocale, $targetLocale);

        return $query
            ->orderByRaw($findingBulkLiteralExpression)
            ->orderBy('findings.id')
            ->pluck('findings.id')
            ->map(static fn(mixed $id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int>  $selectedIds
     * @return array<string, mixed>
     */
    private function emptyBulkEqualizeContext(array $selectedIds = []): array
    {
        return [
            'selected_ids' => $selectedIds,
            'selected_count' => 0,
            'rows' => [],
            'literal' => null,
            'normalized_literal' => null,
            'literal_count' => 0,
            'missing_key_count' => 0,
            'can_confirm' => false,
            'suggested_target_key' => null,
            'translation_keys' => [],
            'suggested_keys' => [],
        ];
    }

    private function normalizedBulkLiteral(?string $literal): ?string
    {
        $literal = $this->nullableString($literal);

        return $literal === null
            ? null
            : Str::lower(preg_replace('/\s+/u', ' ', $literal) ?: $literal);
    }

    private function selectedBulkEqualizeLiteral(): ?string
    {
        if ($this->bulkEqualizeSelectedFindingIds === []) {
            return null;
        }

        return $this->bulkEqualizeContext()['normalized_literal'] ?? null;
    }

    private function bulkTranslationKeySuggestionFromLiteral(?string $literal): ?string
    {
        $literal = $this->nullableString($literal);

        if ($literal === null) {
            return null;
        }

        $lastSegment = Str::slug($literal, '_');

        return $lastSegment !== '' ? 'ui.' . $lastSegment : null;
    }

    /**
     * @return array<int, string>
     */
    private function findingSearchColumns(
        string $sourceFileAlias = 'source_files',
        string $findingAlias = 'findings',
        string $keyAlias = 'keys',
    ): array
    {
        return [
            $sourceFileAlias . '.path',
            $findingAlias . '.literal_text',
            $findingAlias . '.literal_text_suggested',
            $findingAlias . '.found_translation_key',
            $findingAlias . '.existing_key',
            $findingAlias . '.suggested_key',
            $keyAlias . '.translation_key',
        ];
    }

    private function orWhereFindingSearchColumn($query, string $column, string $search): void
    {
        if ($this->findingSearchExact) {
            if ($this->findingSearchCaseSensitive) {
                $query->orWhere($column, '=', $search);

                return;
            }

            $query->orWhereRaw('LOWER(' . $column . ') = ?', [Str::lower($search)]);

            return;
        }

        $like = '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search) . '%';

        if ($this->findingSearchCaseSensitive) {
            $query->orWhereRaw($column . ' LIKE ? ESCAPE \'!\'', [$like]);

            return;
        }

        $query->orWhereRaw('LOWER(' . $column . ') LIKE ? ESCAPE \'!\'', [Str::lower($like)]);
    }

    private function applyLanguageLiteralExistsFilter(
        $query,
        string $locale,
        bool $exists,
        string $findingAlias = 'findings',
        string $keyAlias = 'keys',
    ): void
    {
        $method = $exists ? 'whereExists' : 'whereNotExists';

        $query->{$method}(function ($query) use ($locale, $findingAlias, $keyAlias): void {
            $query
                ->selectRaw('1')
                ->from('translation_workbench_lang_values as source_values')
                ->where('source_values.locale', $locale)
                ->where('source_values.status', 'active')
                ->where(function ($query) use ($findingAlias, $keyAlias): void {
                    $query
                        ->whereColumn('source_values.translation_key', $keyAlias . '.translation_key')
                        ->orWhereColumn('source_values.translation_key', $keyAlias . '.suggested_key')
                        ->orWhereColumn('source_values.translation_key', $findingAlias . '.suggested_key')
                        ->orWhereColumn('source_values.translation_key', $findingAlias . '.found_translation_key');
                });
        });
    }

    private function applyFindingSort($query): void
    {
        $direction = $this->findingSortDirection === 'asc' ? 'asc' : 'desc';

        $this->applyBulkEqualizeSortPin($query);

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

    private function applyBulkEqualizeSortPin($query): void
    {
        $selectedIds = collect($this->bulkEqualizeSelectedFindingIds)
            ->map(static fn(mixed $id): int => (int) $id)
            ->filter(static fn(int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($selectedIds->isEmpty()) {
            return;
        }

        $selectedLiteral = $this->selectedBulkEqualizeLiteral();
        $selectedIdList = $selectedIds->implode(',');

        if ($selectedLiteral !== null) {
            $query->orderByRaw(
                "CASE
                    WHEN findings.id IN ({$selectedIdList}) THEN 0
                    WHEN bulk_literals.bulk_literal = ? THEN 1
                    ELSE 2
                END ASC",
                [$selectedLiteral],
            );

            return;
        }

        $query->orderByRaw(
            "CASE
                WHEN findings.id IN ({$selectedIdList}) THEN 0
                ELSE 1
            END ASC",
        );
    }

    /**
     * @return array<int, array{table: string, count: int, storage_size: array{bytes: int|null, pretty: string}, color: string, icon: string, text: string}>
     */
    private function databaseTableCallouts(): array
    {
        $knownCallouts = collect([
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_entries',
                'color' => 'zinc',
                'icon' => 'archive',
                'text' => __('Legacy entry records from the first workbench iteration.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_occurrences',
                'color' => 'zinc',
                'icon' => 'map-pin',
                'text' => __('Legacy source occurrences linked to legacy entries.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_events',
                'color' => 'zinc',
                'icon' => 'history',
                'text' => __('Legacy entry event records from the first workbench iteration.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_values',
                'color' => 'zinc',
                'icon' => 'scroll-text',
                'text' => __('Legacy static translation values linked to legacy entries.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_dynamic_values',
                'color' => 'zinc',
                'icon' => 'list',
                'text' => __('Legacy dynamic values linked to legacy entries.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_option_discoveries',
                'color' => 'cyan',
                'icon' => 'search-code',
                'text' => __('Discovered hard-coded dynamic option candidates.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_duplicate_candidates',
                'color' => 'amber',
                'icon' => 'copy',
                'text' => __('Duplicate-expression candidates found during code update planning.'),
            ],
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
                'table' => 'translation_workbench_dynamic_sources',
                'color' => 'cyan',
                'icon' => 'database-zap',
                'text' => __('Runtime dynamic sources resolved from application data.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_dynamic_source_values',
                'color' => 'teal',
                'icon' => 'list-checks',
                'text' => __('Runtime option values captured for dynamic sources.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_dynamic_source_candidates',
                'color' => 'cyan',
                'icon' => 'git-compare-arrows',
                'text' => __('Candidate links between dynamic sources, keys, and findings.'),
            ],
            [
                'class' => 'col-span-2',
                'table' => 'translation_workbench_shared_key_candidates',
                'color' => 'purple',
                'icon' => 'combine',
                'text' => __('Possible shared-key follow-up candidates.'),
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
        ]);

        $knownTables = $knownCallouts->pluck('table')->all();
        $unknownCallouts = collect($this->workbenchDatabaseTableNames())
            ->reject(fn(string $table): bool => in_array($table, $knownTables, true))
            ->map(fn(string $table): array => [
                'class' => 'col-span-2',
                'table' => $table,
                'color' => 'zinc',
                'icon' => 'table',
                'text' => __('Workbench database table not yet classified in the overview.'),
            ]);

        return $knownCallouts
            ->concat($unknownCallouts)
            ->filter(fn(array $callout): bool => Schema::hasTable($callout['table']))
            ->map(fn(array $callout): array => [
                ...$callout,
                'count' => $this->tableCount($callout['table']),
                'storage_size' => $this->tableStorageSize($callout['table']),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function workbenchDatabaseTableNames(): array
    {
        try {
            return collect(DB::select(
                'select tablename from pg_catalog.pg_tables where schemaname = current_schema() and tablename like ? order by tablename',
                ['translation_workbench_%'],
            ))
                ->pluck('tablename')
                ->map(fn($table): string => (string) $table)
                ->all();
        } catch (\Throwable) {
            return [];
        }
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
     * @return array<int, array{value: string, label: string}>
     */
    private function findingCandidateTypeOptions(): array
    {
        $scannerOptions = collect($this->distinctOptions('translation_workbench_findings', 'candidate_type'))
            ->map(static fn(string $option): array => [
                'value' => $option,
                'label' => match ($option) {
                    'NULL' => __('No scanner candidate'),
                    'ui' => __('UI candidate'),
                    'dynamic' => __('Dynamic values candidate'),
                    default => __('Scanner candidate: :type', ['type' => $option]),
                },
            ]);

        return $scannerOptions
            ->merge([
                [
                    'value' => 'is_ui',
                    'label' => __('UI translation'),
                ],
                [
                    'value' => 'is_dynamic',
                    'label' => __('Dynamic translation'),
                ],
                [
                    'value' => 'dynamic_multi',
                    'label' => __('Dynamic option list'),
                ],
                [
                    'value' => 'dynamic_numeric',
                    'label' => __('Numeric dynamic'),
                ],
                [
                    'value' => 'dynamic_unstructured',
                    'label' => __('Dynamic data unstructured'),
                ],
            ])
            ->unique('value')
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function findingNamespaceOptions(): array
    {
        if (! Schema::hasTable('translation_workbench_findings') || ! Schema::hasColumn('translation_workbench_findings', 'namespace')) {
            return [];
        }

        $namespaces = DB::table('translation_workbench_findings')
            ->selectRaw("COALESCE(CAST(namespace AS TEXT), 'NULL') as value")
            ->distinct()
            ->pluck('value')
            ->map(static fn($value): string => (string) $value);

        if (Schema::hasTable('translation_workbench_keys') && Schema::hasColumn('translation_workbench_keys', 'namespace')) {
            $namespaces = $namespaces->merge(
                DB::table('translation_workbench_keys')
                    ->selectRaw("COALESCE(CAST(namespace AS TEXT), 'NULL') as value")
                    ->distinct()
                    ->pluck('value')
                    ->map(static fn($value): string => (string) $value),
            );
        }

        return $namespaces
            ->unique()
            ->sort()
            ->values()
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

        $findingQuery = DB::table('translation_workbench_findings');

        if ($this->findingNamespace !== 'all') {
            if ($this->findingNamespace === 'NULL') {
                $findingQuery->whereNull('namespace');
            } else {
                $findingQuery->where('namespace', $this->findingNamespace);
            }
        }

        $groups = $findingQuery
            ->selectRaw("COALESCE(CAST(\"group\" AS TEXT), 'NULL') as value")
            ->distinct()
            ->pluck('value')
            ->map(static fn($value): string => (string) $value);

        if (Schema::hasTable('translation_workbench_keys') && Schema::hasColumn('translation_workbench_keys', 'group')) {
            $keyQuery = DB::table('translation_workbench_keys');

            if ($this->findingNamespace !== 'all') {
                if ($this->findingNamespace === 'NULL') {
                    $keyQuery->whereNull('namespace');
                } else {
                    $keyQuery->where('namespace', $this->findingNamespace);
                }
            }

            $groups = $groups->merge(
                $keyQuery
                    ->selectRaw("COALESCE(CAST(\"group\" AS TEXT), 'NULL') as value")
                    ->distinct()
                    ->pluck('value')
                    ->map(static fn($value): string => (string) $value),
            );
        }

        return $groups
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function normalizedPerPage(mixed $value = null): int
    {
        $value = (int) ($value ?? $this->perPage);

        return in_array($value, [10, 25, 50, 100], true)
            ? $value
            : 25;
    }

    private function normalizeOptionState(mixed $value, string $default = 'all'): string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : $default;
    }

    private function normalizeFindingSortField(mixed $value): string
    {
        $value = trim((string) $value);

        return in_array($value, ['last_seen', 'source', 'literal', 'keys'], true) ? $value : 'last_seen';
    }

    private function normalizeSortDirection(mixed $value): string
    {
        return trim((string) $value) === 'asc' ? 'asc' : 'desc';
    }

    private function legacyFindingLiteralState(mixed $value): string
    {
        return match ($this->normalizeOptionState($value)) {
            'yes' => 'source_available',
            'no' => 'source_missing',
            default => 'all',
        };
    }

    private function findingFiltersActive(): bool
    {
        return $this->findingSearch !== ''
            || $this->findingSearchExact
            || $this->findingSearchCaseSensitive
            || $this->findingStatus !== 'all'
            || $this->showObsoleteFindings
            || $this->findingKind !== 'all'
            || $this->findingCandidateType !== 'all'
            || $this->findingNamespace !== 'all'
            || $this->findingGroup !== 'all'
            || $this->findingKeyRelation !== 'all'
            || $this->findingLiteralState !== 'all';
    }

    /**
     * @return array<string, mixed>
     */
    private function uiStateDefaults(): array
    {
        $configDefaults = (array) config('translation-workbench.ui_state.defaults', []);
        $fileDefaults = $this->uiStateFileDefaults();

        return [
            ...$fileDefaults,
            ...$configDefaults,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function uiStateFileDefaults(): array
    {
        $path = base_path((string) config(
            'translation-workbench.ui_state.defaults_file',
            'packages/gunreip/laravel-translation-workbench/resources/ui-state/entries-defaults.json',
        ));

        if (! File::exists($path)) {
            return [];
        }

        $decoded = json_decode((string) File::get($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function uiStateSettingKey(): string
    {
        return (string) config('translation-workbench.ui_state.setting_key', 'ui.pages.translation_workbench.entries');
    }

    private function persistUiState(): void
    {
        $state = $this->currentUiState();

        $this->setUserSetting($this->uiStateSettingKey(), $state);
        $this->persistUiStateFile($state);
    }

    /**
     * @return array<string, mixed>
     */
    private function currentUiState(): array
    {
        return [
            'findingSearch' => $this->findingSearch,
            'findingSearchExact' => $this->findingSearchExact,
            'findingSearchCaseSensitive' => $this->findingSearchCaseSensitive,
            'findingStatus' => $this->findingStatus,
            'findingKind' => $this->findingKind,
            'findingCandidateType' => $this->findingCandidateType,
            'findingNamespace' => $this->findingNamespace,
            'findingGroup' => $this->findingGroup,
            'findingKeyRelation' => $this->findingKeyRelation,
            'findingLiteralState' => $this->findingLiteralState,
            'perPage' => $this->perPage,
            'findingSortField' => $this->findingSortField,
            'findingSortDirection' => $this->findingSortDirection,
            'showOverviewTabs' => $this->showOverviewTabs,
            'showObsoleteFindings' => $this->showObsoleteFindings,
            'editModalAutoCloseAfterSave' => $this->editModalAutoCloseAfterSave,
        ];
    }

    /**
     * Store the most recent non-user-specific workbench UI state as an inspectable file.
     *
     * This is intentionally separate from the user-specific database setting. The file can be
     * reviewed, copied into package defaults, or shared as a starting point for community setups.
     *
     * @param  array<string, mixed>  $state
     */
    private function persistUiStateFile(array $state): void
    {
        $path = storage_path((string) config(
            'translation-workbench.ui_state.export_file',
            'app/translation-workbench/ui-state/entries.json',
        ));

        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode([
            'page' => 'translation-workbench.entries',
            'updated_at' => now()->toISOString(),
            'state' => $state,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL);
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
     * @return array<string, mixed>|null
     */
    private function pipelineRunReport(): ?array
    {
        $path = $this->translationWorkbenchReportPath('translation:workbench');

        if (! File::exists($path)) {
            return null;
        }

        $data = json_decode((string) File::get($path), true);

        if (! is_array($data)) {
            return null;
        }

        $pipelineGeneratedAt = (string) ($data['generated_at'] ?? '');
        $pipelineTimestamp = $this->reportTimestamp($pipelineGeneratedAt);
        $storedSteps = collect(is_array($data['steps'] ?? null) ? $data['steps'] : []);
        $steps = $storedSteps->isNotEmpty()
            ? $storedSteps
                ->map(fn(array $step, int $index): array => $this->pipelineStoredStepReport($step, $index + 1))
                ->values()
            : collect($this->pipelineStepDefinitions())
                ->map(fn(array $definition, int $index): array => $this->pipelineStepReport($definition, $index + 1, $pipelineTimestamp))
                ->values();

        $blockers = $steps->filter(static fn(array $step): bool => (bool) ($step['blocks_complete'] ?? false))->count();
        $staleReports = $steps->where('is_stale', true)->count();

        return [
            'command' => (string) ($data['command'] ?? 'translation:workbench'),
            'generated_at' => $pipelineGeneratedAt,
            'duration_ms' => $data['duration_ms'] ?? null,
            'summary' => [
                'steps' => $steps->count(),
                'fresh_reports' => $steps->where('exists', true)->where('is_stale', false)->count(),
                'stale_reports' => $staleReports,
                'missing_reports' => $steps->where('exists', false)->count(),
                'blockers' => $blockers,
                'has_blockers' => $blockers > 0,
                'has_stale_reports' => $staleReports > 0,
            ],
            'steps' => $steps->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $step
     * @return array<string, mixed>
     */
    private function pipelineStoredStepReport(array $step, int $fallbackIndex): array
    {
        $report = is_array($step['report'] ?? null) ? $step['report'] : [];
        $arguments = is_array($step['arguments'] ?? null) ? $step['arguments'] : [];
        $command = (string) ($step['command'] ?? $report['command'] ?? '');
        $exitCode = (int) ($step['exit_code'] ?? 1);
        $reportData = [
            'summary' => is_array($report['summary'] ?? null) ? $report['summary'] : [],
            'plan_summary' => is_array($report['plan_summary'] ?? null) ? $report['plan_summary'] : [],
            'write' => (bool) (($report['write'] ?? false) || ($arguments['--write'] ?? false)),
            'dry_run' => (bool) ($report['dry_run'] ?? false),
            'exit_code' => $exitCode,
            'raw_data' => [
                'files' => $report['raw_summary']['files'] ?? 0,
                'found' => $report['raw_summary']['found'] ?? 0,
            ],
        ];
        $blocksComplete = $exitCode !== 0 || $this->pipelineStepBlocksComplete($command, $reportData);
        $stateLabel = match (true) {
            $exitCode !== 0 => 'Failed',
            $blocksComplete => 'Needs attention',
            default => 'Executed',
        };
        $stateColor = match (true) {
            $exitCode !== 0 => 'red',
            $blocksComplete => 'red',
            default => 'green',
        };

        return [
            'index' => (int) ($step['index'] ?? $fallbackIndex),
            'label' => (string) ($step['label'] ?? $command),
            'command' => $command,
            'exists' => (bool) ($report['exists'] ?? false),
            'generated_at' => $report['generated_at'] ?? null,
            'is_stale' => false,
            'state_label' => $stateLabel,
            'state_color' => $stateColor,
            'metrics' => $this->pipelineStepMetrics($command, $reportData),
            'notes' => $this->pipelineStepNotes($command, $reportData),
            'blocks_complete' => $blocksComplete,
            'write' => (bool) $reportData['write'],
            'dry_run' => (bool) $reportData['dry_run'],
            'duration_ms' => $step['duration_ms'] ?? null,
        ];
    }

    /**
     * @return array<int, array{label: string, command: string}>
     */
    private function pipelineStepDefinitions(): array
    {
        return [
            ['label' => 'Scan source code', 'command' => 'translation-workbench:scan'],
            ['label' => 'Sync foundation tables', 'command' => 'translation-workbench:sync-foundation'],
            ['label' => 'Import lang values', 'command' => 'translation-workbench:import-lang-values'],
            ['label' => 'Import source language values', 'command' => 'translation-workbench:import-existing'],
            ['label' => 'Discover dynamic options', 'command' => 'translation-workbench:discover-dynamic-options'],
            ['label' => 'Detect duplicates', 'command' => 'translation-workbench:detect-duplicates'],
            ['label' => 'Classify dynamic values', 'command' => 'translation-workbench:classify-dynamic-values'],
            ['label' => 'Resolve unknown dynamic sources', 'command' => 'translation-workbench:resolve-unknown-dynamic-sources'],
            ['label' => 'Discover dynamic source candidates', 'command' => 'translation-workbench:discover-dynamic-source-candidates'],
            ['label' => 'Export lang files', 'command' => 'translation-workbench:export-lang-files'],
            ['label' => 'Plan code updates', 'command' => 'translation-workbench:plan-code-updates'],
            ['label' => 'Apply code updates', 'command' => 'translation-workbench:apply-code-updates'],
        ];
    }

    /**
     * @param  array{label: string, command: string}  $definition
     * @return array<string, mixed>
     */
    private function pipelineStepReport(array $definition, int $index, ?int $pipelineTimestamp): array
    {
        $path = $this->translationWorkbenchReportPath($definition['command']);

        if (! File::exists($path)) {
            return [
                'index' => $index,
                'label' => $definition['label'],
                'command' => $definition['command'],
                'exists' => false,
                'generated_at' => null,
                'is_stale' => false,
                'state_label' => 'Missing',
                'state_color' => 'red',
                'metrics' => [],
                'blocks_complete' => true,
                'write' => false,
                'dry_run' => false,
            ];
        }

        $data = json_decode((string) File::get($path), true);
        $data = is_array($data) ? $data : [];
        $generatedAt = (string) ($data['generated_at'] ?? '');
        $generatedTimestamp = $this->reportTimestamp($generatedAt);
        $isStale = $pipelineTimestamp !== null
            && $generatedTimestamp !== null
            && $generatedTimestamp < ($pipelineTimestamp - 1800);
        $metrics = $this->pipelineStepMetrics($definition['command'], $data);
        $notes = $this->pipelineStepNotes($definition['command'], $data);
        $blocksComplete = $this->pipelineStepBlocksComplete($definition['command'], $data);
        $stateLabel = $isStale
            ? 'Stale'
            : ($blocksComplete ? 'Needs attention' : 'Fresh');
        $stateColor = $isStale
            ? 'amber'
            : ($blocksComplete ? 'red' : 'green');

        return [
            'index' => $index,
            'label' => $definition['label'],
            'command' => (string) ($data['command'] ?? $definition['command']),
            'exists' => true,
            'generated_at' => $generatedAt !== '' ? $generatedAt : null,
            'is_stale' => $isStale,
            'state_label' => $stateLabel,
            'state_color' => $stateColor,
            'metrics' => $metrics,
            'notes' => $notes,
            'blocks_complete' => $blocksComplete,
            'write' => (bool) ($data['write'] ?? false),
            'dry_run' => (bool) (($data['summary']['dry_run'] ?? false) || ($data['dry_run'] ?? false)),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array{label: string, value: string, color: string}>
     */
    private function pipelineStepMetrics(string $command, array $data): array
    {
        $summary = is_array($data['summary'] ?? null) ? $data['summary'] : [];
        $rawData = is_array($data['raw_data'] ?? null) ? $data['raw_data'] : [];
        $planSummary = is_array($data['plan_summary'] ?? null) ? $data['plan_summary'] : [];

        return match ($command) {
            'translation-workbench:export-lang-files' => $this->metricRows([
                ['Files', $summary['files'] ?? 0, 'sky'],
                ['Exportable', $summary['values_exportable'] ?? 0, 'sky'],
                ['New', $summary['values_new'] ?? 0, ((int) ($summary['values_new'] ?? 0)) > 0 ? 'cyan' : 'zinc'],
                ['Changed', $summary['values_changed'] ?? 0, ((int) ($summary['values_changed'] ?? 0)) > 0 ? 'amber' : 'zinc'],
                ['Pruned', $summary['values_pruned'] ?? 0, ((int) ($summary['values_pruned'] ?? 0)) > 0 ? 'amber' : 'zinc'],
                ['Conflicts', $summary['values_conflicted'] ?? 0, ((int) ($summary['values_conflicted'] ?? 0)) > 0 ? 'red' : 'green'],
                ['Written', $summary['files_written'] ?? 0, ((int) ($summary['files_written'] ?? 0)) > 0 ? 'green' : 'zinc'],
                ['Timeline', $summary['timeline_events_created'] ?? 0, ((int) ($summary['timeline_events_created'] ?? 0)) > 0 ? 'green' : 'zinc'],
            ]),
            'translation-workbench:plan-code-updates' => $this->metricRows([
                ['Reviewed', $summary['reviewed_findings'] ?? 0, 'sky'],
                ['Safe', $summary['safe_updates'] ?? 0, ((int) ($summary['safe_updates'] ?? 0)) > 0 ? 'amber' : 'green'],
                ['Current', $summary['already_current'] ?? 0, 'green'],
                ['Manual', $summary['manual_review'] ?? 0, ((int) ($summary['manual_review'] ?? 0)) > 0 ? 'amber' : 'zinc'],
                ['Missing lang', $summary['missing_lang_values'] ?? 0, ((int) ($summary['missing_lang_values'] ?? 0)) > 0 ? 'red' : 'green'],
                ['Stale', $summary['stale_source'] ?? 0, ((int) ($summary['stale_source'] ?? 0)) > 0 ? 'red' : 'green'],
            ]),
            'translation-workbench:apply-code-updates' => $this->metricRows([
                ['Planned safe', $summary['planned_safe_updates'] ?? 0, 'sky'],
                ['Applied', $summary['applied'] ?? 0, ((int) ($summary['applied'] ?? 0)) > 0 ? 'green' : 'zinc'],
                ['Would apply', $summary['would_apply'] ?? 0, ((int) ($summary['would_apply'] ?? 0)) > 0 ? 'amber' : 'green'],
                ['Duplicates', $summary['duplicate_expression'] ?? 0, ((int) ($summary['duplicate_expression'] ?? 0)) > 0 ? 'amber' : 'green'],
                ['Reviewed dup.', $summary['duplicate_reviewed'] ?? 0, ((int) ($summary['duplicate_reviewed'] ?? 0)) > 0 ? 'sky' : 'zinc'],
                ['Plan manual', $planSummary['manual_review'] ?? 0, ((int) ($planSummary['manual_review'] ?? 0)) > 0 ? 'amber' : 'zinc'],
                ['Timeline', $summary['timeline_events_created'] ?? 0, ((int) ($summary['timeline_events_created'] ?? 0)) > 0 ? 'green' : 'zinc'],
            ]),
            default => $this->metricRows([
                ['Files', $rawData['files'] ?? ($summary['files'] ?? 0), 'sky'],
                ['Found', $rawData['found'] ?? ($summary['found'] ?? 0), 'sky'],
            ]),
        };
    }

    /**
     * @param  array<int, array{0: string, 1: mixed, 2: string}>  $rows
     * @return array<int, array{label: string, value: string, color: string}>
     */
    private function metricRows(array $rows): array
    {
        return collect($rows)
            ->map(static fn(array $row): array => [
                'label' => $row[0],
                'value' => is_numeric($row[1]) ? number_format((int) $row[1]) : (string) $row[1],
                'color' => $row[2],
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array{color: string, text: string}>
     */
    private function pipelineStepNotes(string $command, array $data): array
    {
        $summary = is_array($data['summary'] ?? null) ? $data['summary'] : [];
        $notes = [];

        if ($command === 'translation-workbench:export-lang-files') {
            $conflicts = (int) ($summary['values_conflicted'] ?? 0);

            if ($conflicts > 0) {
                $notes[] = [
                    'color' => 'red',
                    'text' => __('The lang file export reported nested lang-key conflicts. Open the export report to review the conflicting keys before rerunning the pipeline.'),
                ];
            }

            if ((bool) ($data['write'] ?? false)) {
                $written = (int) ($summary['files_written'] ?? 0);
                $notes[] = [
                    'color' => $written > 0 ? 'green' : 'zinc',
                    'text' => $written > 0
                        ? __('This step writes reviewed translation values to lang/* files.')
                        : __('This write step found no changed lang files to write.'),
                ];
            } elseif ((bool) ($data['dry_run'] ?? false)) {
                $notes[] = [
                    'color' => 'sky',
                    'text' => __('This is only a refresh dry-run for the lang file export report. It does not write lang/* files.'),
                ];
            }
        }

        if ($command === 'translation-workbench:apply-code-updates') {
            $planned = (int) ($summary['planned_safe_updates'] ?? 0);
            $applied = (int) ($summary['applied'] ?? 0);
            $wouldApply = (int) ($summary['would_apply'] ?? 0);
            $duplicates = (int) ($summary['duplicate_expression'] ?? 0);
            $reviewedDuplicates = (int) ($summary['duplicate_reviewed'] ?? 0);
            $stale = (int) ($summary['stale_source'] ?? 0);
            $skipped = (int) ($summary['skipped'] ?? 0);

            $notes[] = [
                'color' => 'sky',
                'text' => __('This step replaces reviewed source code expressions with translation key calls. It does not write lang/* files.'),
            ];

            if ($planned > 0 && $applied === 0 && $wouldApply === 0 && ($duplicates > 0 || $reviewedDuplicates > 0)) {
                $notes[] = [
                    'color' => 'amber',
                    'text' => __('Nothing was applied because all planned code updates are duplicate expressions or already reviewed duplicate cases.'),
                ];
            }

            if ($duplicates > 0) {
                $notes[] = [
                    'color' => 'amber',
                    'text' => __('Duplicate expressions occur more than once in a source file and are not replaced automatically. Review them in Code update plan.'),
                ];
            }

            if ($reviewedDuplicates > 0) {
                $notes[] = [
                    'color' => 'sky',
                    'text' => __('Reviewed duplicate expressions stay excluded from automatic replacement unless their review decision allows a manual workflow.'),
                ];
            }

            if ($stale > 0 || $skipped > 0) {
                $notes[] = [
                    'color' => 'red',
                    'text' => __('Some planned code updates could not be matched in the current source files. Run a fresh translation:workbench cycle and review stale rows.'),
                ];
            }
        }

        if ((int) ($data['exit_code'] ?? 0) !== 0) {
            $notes[] = [
                'color' => 'red',
                'text' => __('This command exited with a non-zero status. The compact metrics above show the most likely blocker; the command-specific report contains the full details.'),
            ];
        }

        return $notes;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function pipelineStepBlocksComplete(string $command, array $data): bool
    {
        $summary = is_array($data['summary'] ?? null) ? $data['summary'] : [];

        return match ($command) {
            'translation-workbench:export-lang-files' => (int) ($summary['values_conflicted'] ?? 0) > 0,
            'translation-workbench:apply-code-updates' => (int) ($summary['stale_source'] ?? 0) > 0
                || (int) ($summary['skipped'] ?? 0) > 0,
            default => false,
        };
    }

    private function reportTimestamp(?string $value): ?int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $timestamp = strtotime($value);

        return $timestamp === false ? null : $timestamp;
    }

    private function translationWorkbenchReportPath(string $commandName): string
    {
        $filename = Str::of($commandName)
            ->replace(':', '-')
            ->replace('\\', '-')
            ->replace('/', '-')
            ->slug('-')
            ->append('.json')
            ->toString();

        return storage_path('translation-workbench' . DIRECTORY_SEPARATOR . $filename);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function langFileExportReport(): ?array
    {
        $path = storage_path('translation-workbench/translation-workbench-export-lang-files.json');

        if (! File::exists($path)) {
            return null;
        }

        $data = json_decode((string) File::get($path), true);

        if (! is_array($data)) {
            return null;
        }

        $summary = is_array($data['summary'] ?? null) ? $data['summary'] : [];
        $activeScope = is_array($summary['active_scope'] ?? null) ? $summary['active_scope'] : [];
        $conflicts = collect(is_array($data['plans'] ?? null) ? $data['plans'] : [])
            ->flatMap(function (array $plan): array {
                return collect(is_array($plan['conflicts'] ?? null) ? $plan['conflicts'] : [])
                    ->map(function (array $conflict) use ($plan): array {
                        $locale = (string) ($plan['locale'] ?? '');
                        $namespace = (string) ($plan['namespace'] ?? '');
                        $langKey = (string) ($conflict['lang_key'] ?? '');
                        $translationKey = (string) ($conflict['translation_key'] ?? '');

                        return [
                            ...$conflict,
                            'conflict_key' => $this->exportConflictKey($locale, $namespace, $langKey, $translationKey),
                            'locale' => $locale,
                            'namespace' => $namespace,
                            'path' => (string) ($plan['path'] ?? ''),
                            'lang_key' => $langKey,
                            'translation_key' => $translationKey,
                            'reason' => (string) ($conflict['reason'] ?? ''),
                        ];
                    })
                    ->all();
            })
            ->values()
            ->all();

        return [
            'command' => (string) ($data['command'] ?? 'translation-workbench:export-lang-files'),
            'generated_at' => (string) ($data['generated_at'] ?? ''),
            'dry_run' => (bool) ($summary['dry_run'] ?? true),
            'files' => (int) ($summary['files'] ?? 0),
            'values_exportable' => (int) ($summary['values_exportable'] ?? 0),
            'values_new' => (int) ($summary['values_new'] ?? 0),
            'values_changed' => (int) ($summary['values_changed'] ?? 0),
            'values_unchanged' => (int) ($summary['values_unchanged'] ?? 0),
            'values_pruned' => (int) ($summary['values_pruned'] ?? 0),
            'values_conflicted' => (int) ($summary['values_conflicted'] ?? 0),
            'files_written' => (int) ($summary['files_written'] ?? 0),
            'conflicts' => $conflicts,
            'active_scope' => [
                'source_locale' => (string) ($activeScope['source_locale'] ?? ''),
                'target_main_locale' => (string) ($activeScope['target_main_locale'] ?? ''),
                'target_sub_locales' => is_array($activeScope['target_sub_locales'] ?? null) ? $activeScope['target_sub_locales'] : [],
                'locales' => is_array($activeScope['locales'] ?? null) ? $activeScope['locales'] : [],
                'values_exportable' => (int) ($activeScope['values_exportable'] ?? 0),
                'source_values' => (int) ($activeScope['source_values'] ?? 0),
                'target_main_values' => (int) ($activeScope['target_main_values'] ?? 0),
                'target_main_missing' => (int) ($activeScope['target_main_missing'] ?? 0),
                'target_main_extra' => (int) ($activeScope['target_main_extra'] ?? 0),
                'target_main_balanced' => (bool) ($activeScope['target_main_balanced'] ?? false),
                'target_sub_values' => (int) ($activeScope['target_sub_values'] ?? 0),
                'values_by_locale' => is_array($activeScope['values_by_locale'] ?? null) ? $activeScope['values_by_locale'] : [],
                'target_main_missing_keys' => is_array($activeScope['target_main_missing_keys'] ?? null) ? $activeScope['target_main_missing_keys'] : [],
                'target_main_extra_keys' => is_array($activeScope['target_main_extra_keys'] ?? null) ? $activeScope['target_main_extra_keys'] : [],
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function codeUpdatePlanReport(): ?array
    {
        $path = storage_path('translation-workbench/translation-workbench-plan-code-updates.json');

        if (! File::exists($path)) {
            return null;
        }

        $data = json_decode((string) File::get($path), true);

        if (! is_array($data)) {
            return null;
        }

        $summary = is_array($data['summary'] ?? null) ? $data['summary'] : [];
        $updates = collect(is_array($data['updates'] ?? null) ? $data['updates'] : []);

        $filteredUpdates = $this->filteredCodeUpdatePlanRows($updates);

        return [
            'command' => (string) ($data['command'] ?? 'translation-workbench:plan-code-updates'),
            'generated_at' => (string) ($data['generated_at'] ?? ''),
            'paths' => is_array($data['paths'] ?? null) ? $data['paths'] : [],
            'filters' => [
                'state' => $this->codeUpdatePlanState,
                'search' => $this->codeUpdatePlanSearch,
                'active' => $this->codeUpdatePlanFiltersActive(),
            ],
            'summary' => [
                'reviewed_findings' => (int) ($summary['reviewed_findings'] ?? 0),
                'safe_updates' => (int) ($summary['safe_updates'] ?? 0),
                'already_current' => (int) ($summary['already_current'] ?? 0),
                'manual_review' => (int) ($summary['manual_review'] ?? 0),
                'missing_lang_values' => (int) ($summary['missing_lang_values'] ?? 0),
                'stale_source' => (int) ($summary['stale_source'] ?? 0),
                'missing_source_file' => (int) ($summary['missing_source_file'] ?? 0),
                'unsupported_expression' => (int) ($summary['unsupported_expression'] ?? 0),
            ],
            'filtered_summary' => [
                'rows' => $filteredUpdates->count(),
                'safe_updates' => $filteredUpdates->where('state', 'safe_update')->count(),
                'manual_review' => $filteredUpdates->where('state', 'manual_review')->count(),
                'missing_lang_values' => $filteredUpdates->where('state', 'missing_lang_values')->count(),
                'stale_source' => $filteredUpdates->where('state', 'stale_source')->count(),
                'already_current' => $filteredUpdates->where('state', 'already_current')->count(),
            ],
            'updates_by_state' => [
                'safe_update' => $filteredUpdates->where('state', 'safe_update')->take(50)->values()->all(),
                'manual_review' => $filteredUpdates->where('state', 'manual_review')->take(50)->values()->all(),
                'missing_lang_values' => $filteredUpdates->where('state', 'missing_lang_values')->take(50)->values()->all(),
                'stale_source' => $filteredUpdates->where('state', 'stale_source')->take(50)->values()->all(),
                'already_current' => $filteredUpdates->where('state', 'already_current')->take(50)->values()->all(),
                'unsupported_expression' => $filteredUpdates->where('state', 'unsupported_expression')->take(50)->values()->all(),
                'missing_source_file' => $filteredUpdates->where('state', 'missing_source_file')->take(50)->values()->all(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function codeUpdateApplyReport(): ?array
    {
        $path = storage_path('translation-workbench/translation-workbench-apply-code-updates.json');

        if (! File::exists($path)) {
            return null;
        }

        $data = json_decode((string) File::get($path), true);

        if (! is_array($data)) {
            return null;
        }

        $summary = is_array($data['summary'] ?? null) ? $data['summary'] : [];
        $diff = is_array($data['diff'] ?? null) ? $data['diff'] : [];
        $results = collect(is_array($data['results'] ?? null) ? $data['results'] : []);
        $conflictReviews = $this->latestCodeUpdateConflictReviews(
            $results
                ->map(static fn(array $row): string => ((int) ($row['finding_id'] ?? 0)) . ':' . ((int) ($row['key_id'] ?? 0)))
                ->filter(static fn(string $key): bool => $key !== '0:0')
                ->values()
                ->all(),
        );
        $results = $results->map(function (array $row) use ($conflictReviews): array {
            $reviewKey = ((int) ($row['finding_id'] ?? 0)) . ':' . ((int) ($row['key_id'] ?? 0));
            $row['conflict_review'] = $conflictReviews[$reviewKey] ?? null;

            return $row;
        });
        $diffPath = (string) ($data['diff_path'] ?? storage_path('translation-workbench/translation-workbench-apply-code-updates.patch'));
        $diffContent = File::exists($diffPath)
            ? (string) File::get($diffPath)
            : (string) ($diff['content'] ?? '');
        $diffLines = $diffContent === ''
            ? []
            : preg_split('/\\R/', trim($diffContent));

        return [
            'command' => (string) ($data['command'] ?? 'translation-workbench:apply-code-updates'),
            'generated_at' => (string) ($data['generated_at'] ?? ''),
            'write' => (bool) ($data['write'] ?? false),
            'diff_path' => $diffPath,
            'summary' => [
                'planned_safe_updates' => (int) ($summary['planned_safe_updates'] ?? 0),
                'would_apply' => (int) ($summary['would_apply'] ?? 0),
                'applied' => (int) ($summary['applied'] ?? 0),
                'skipped' => (int) ($summary['skipped'] ?? 0),
                'stale_source' => (int) ($summary['stale_source'] ?? 0),
                'duplicate_expression' => (int) ($summary['duplicate_expression'] ?? 0),
                'duplicate_reviewed' => (int) ($summary['duplicate_reviewed'] ?? 0),
                'diff_files' => (int) ($summary['diff_files'] ?? 0),
            ],
            'diff' => [
                'files' => is_array($diff['files'] ?? null) ? $diff['files'] : [],
                'line_count' => is_array($diffLines) ? count($diffLines) : 0,
                'preview' => is_array($diffLines) ? collect($diffLines)->take(180)->implode("\n") : '',
                'truncated' => is_array($diffLines) && count($diffLines) > 180,
            ],
            'results_by_state' => [
                'would_apply' => $results->where('state', 'would_apply')->take(50)->values()->all(),
                'applied' => $results->where('state', 'applied')->take(50)->values()->all(),
                'duplicate_expression' => $results->where('state', 'duplicate_expression')->take(50)->values()->all(),
                'duplicate_reviewed' => $results->where('state', 'duplicate_reviewed')->take(50)->values()->all(),
                'stale_source' => $results->where('state', 'stale_source')->take(50)->values()->all(),
                'skipped' => $results->where('state', 'skipped')->take(50)->values()->all(),
            ],
        ];
    }

    /**
     * @param  array<int, string>  $reviewKeys
     * @return array<string, array<string, mixed>>
     */
    private function latestCodeUpdateConflictReviews(array $reviewKeys): array
    {
        if ($reviewKeys === [] || ! Schema::hasTable('translation_workbench_reviews')) {
            return [];
        }

        $pairs = collect($reviewKeys)
            ->map(function (string $reviewKey): ?array {
                [$findingId, $keyId] = array_pad(explode(':', $reviewKey, 2), 2, null);

                return ((int) $findingId > 0 && (int) $keyId > 0)
                    ? ['finding_id' => (int) $findingId, 'key_id' => (int) $keyId]
                    : null;
            })
            ->filter()
            ->values();

        if ($pairs->isEmpty()) {
            return [];
        }

        return TranslationWorkbenchReview::query()
            ->where('review_type', 'code_update_conflict')
            ->where(function ($query) use ($pairs): void {
                foreach ($pairs as $pair) {
                    $query->orWhere(function ($query) use ($pair): void {
                        $query
                            ->where('finding_id', $pair['finding_id'])
                            ->where('key_id', $pair['key_id']);
                    });
                }
            })
            ->latest('id')
            ->get()
            ->unique(static fn(TranslationWorkbenchReview $review): string => $review->finding_id . ':' . $review->key_id)
            ->mapWithKeys(static fn(TranslationWorkbenchReview $review): array => [
                $review->finding_id . ':' . $review->key_id => [
                    'id' => $review->id,
                    'decision' => $review->decision,
                    'label' => str($review->decision)->replace('_', ' ')->title()->toString(),
                    'note' => $review->meta['note'] ?? null,
                    'reviewed_at' => optional($review->reviewed_at)->toDateTimeString(),
                ],
            ])
            ->all();
    }

    private function latestCodeUpdateConflictReview(int $findingId, int $keyId): ?TranslationWorkbenchReview
    {
        if (! Schema::hasTable('translation_workbench_reviews')) {
            return null;
        }

        return TranslationWorkbenchReview::query()
            ->where('review_type', 'code_update_conflict')
            ->where('finding_id', $findingId)
            ->where('key_id', $keyId)
            ->latest('id')
            ->first();
    }

    private function normalizedCodeUpdateConflictDecision(string $decision): string
    {
        return in_array($decision, [
            'duplicate_confirmed_same_key',
            'existing_key_should_be_replaced',
            'duplicate_dynamic_manual_workflow',
            'ignore_for_now',
        ], true)
            ? $decision
            : 'ignore_for_now';
    }

    /**
     * @return array<string, mixed>|null
     */
    private function codeUpdateConflictReview(): ?array
    {
        if (! $this->codeUpdateConflictFindingId || ! $this->codeUpdateConflictKeyId) {
            return null;
        }

        $finding = $this->selectedFinding($this->codeUpdateConflictFindingId);

        if (! $finding) {
            return null;
        }

        $applyReport = $this->codeUpdateApplyReport();
        $applyRows = collect($applyReport['results_by_state']['duplicate_expression'] ?? []);
        $applyRow = $applyRows->first(fn(array $row): bool => (int) ($row['finding_id'] ?? 0) === $this->codeUpdateConflictFindingId
            && (int) ($row['key_id'] ?? 0) === $this->codeUpdateConflictKeyId);

        return [
            'finding' => $finding,
            'apply_row' => $applyRow,
            'latest_review' => $this->latestCodeUpdateConflictReview(
                $this->codeUpdateConflictFindingId,
                $this->codeUpdateConflictKeyId,
            ),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function exportConflictResolveContext(): ?array
    {
        if (
            $this->exportConflictLocale === ''
            || $this->exportConflictNamespace === ''
            || $this->exportConflictLangKey === ''
            || $this->exportConflictTranslationKey === ''
        ) {
            return null;
        }

        $report = $this->langFileExportReport();
        $conflict = collect($report['conflicts'] ?? [])
            ->first(fn(array $conflict): bool => (
                $this->exportConflictKey !== ''
                && (string) ($conflict['conflict_key'] ?? '') === $this->exportConflictKey
            ) || (
                (string) ($conflict['locale'] ?? '') === $this->exportConflictLocale
                && (string) ($conflict['namespace'] ?? '') === $this->exportConflictNamespace
                && (string) ($conflict['lang_key'] ?? '') === $this->exportConflictLangKey
                && (string) ($conflict['translation_key'] ?? '') === $this->exportConflictTranslationKey
            ));

        if (! is_array($conflict)) {
            return null;
        }

        $blockedKey = TranslationWorkbenchKey::query()
            ->where('translation_key', (string) ($conflict['translation_key'] ?? ''))
            ->orderByRaw("CASE WHEN status = 'obsolete' THEN 1 ELSE 0 END")
            ->orderBy('id')
            ->first();
        $blockingKey = ($conflict['blocking_key_id'] ?? null)
            ? TranslationWorkbenchKey::query()->find((int) $conflict['blocking_key_id'])
            : null;
        $blockingFindingIds = collect($conflict['blocking_finding_ids'] ?? [])
            ->map(static fn(mixed $id): int => (int) $id)
            ->filter()
            ->values()
            ->all();
        $blockingFindings = $blockingFindingIds !== []
            ? TranslationWorkbenchFinding::query()
                ->with('sourceFile')
                ->whereIn('id', $blockingFindingIds)
                ->orderBy('id')
                ->get()
            : collect();

        return [
            'report' => $report,
            'conflict' => $conflict,
            'blocked_key' => $blockedKey,
            'blocking_key' => $blockingKey,
            'blocking_findings' => $blockingFindings,
        ];
    }

    private function filteredCodeUpdatePlanRows($updates)
    {
        $state = $this->normalizeOptionState($this->codeUpdatePlanState);
        $search = Str::lower(trim($this->codeUpdatePlanSearch));

        return collect($updates)
            ->filter(function (array $row) use ($state): bool {
                return $state === 'all' || ($row['state'] ?? null) === $state;
            })
            ->filter(function (array $row) use ($search): bool {
                if ($search === '') {
                    return true;
                }

                return collect([
                    $row['source_path'] ?? null,
                    $row['raw_expression'] ?? null,
                    $row['new_expression'] ?? null,
                    $row['translation_key'] ?? null,
                    $row['reason'] ?? null,
                ])
                    ->filter(static fn(mixed $value): bool => $value !== null)
                    ->contains(static fn(mixed $value): bool => str_contains(Str::lower((string) $value), $search));
            })
            ->values();
    }

    private function codeUpdatePlanFiltersActive(): bool
    {
        return $this->normalizeOptionState($this->codeUpdatePlanState) !== 'all'
            || trim($this->codeUpdatePlanSearch) !== '';
    }

    private function exportConflictKey(string $locale, string $namespace, string $langKey, string $translationKey): string
    {
        return sha1(implode("\n", [$locale, $namespace, $langKey, $translationKey]));
    }

    private function refreshCodeUpdatePlanReport(): bool
    {
        try {
            return Artisan::call('translation-workbench:plan-code-updates') === 0;
        } catch (\Throwable) {
            return false;
        }
    }

    private function refreshCodeUpdateApplyReportFile(): bool
    {
        try {
            return Artisan::call('translation-workbench:apply-code-updates') === 0;
        } catch (\Throwable) {
            return false;
        }
    }

    private function refreshSharedKeyCandidates(): bool
    {
        try {
            return Artisan::call('translation-workbench:detect-shared-key-candidates') === 0;
        } catch (\Throwable) {
            return false;
        }
    }

    private function refreshLangFileExportReport(): bool
    {
        try {
            $summary = app(TranslationWorkbenchLangFileExporter::class)->export(write: false);
            $path = storage_path('translation-workbench/translation-workbench-export-lang-files.json');
            $directory = dirname($path);

            if (! File::isDirectory($directory)) {
                File::ensureDirectoryExists($directory);
            }

            if (! is_writable($directory) || (File::exists($path) && ! is_writable($path))) {
                return false;
            }

            File::put($path, json_encode([
                'command' => 'translation-workbench:export-lang-files',
                'generated_at' => now()->toISOString(),
                'summary' => collect($summary)->except('plans')->all(),
                'plans' => $summary['plans'],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function obsoleteSourceValueReview(): ?array
    {
        $translationKey = trim((string) $this->obsoleteSourceValueTranslationKey);

        if ($translationKey === '') {
            return null;
        }

        $sourceLocale = $this->sourceMainLocale();
        $targetLocale = (string) ($this->editLocales()['active'] ?? app()->getLocale());
        $langValue = TranslationWorkbenchLangValue::query()
            ->where('locale', $sourceLocale)
            ->where('translation_key', $translationKey)
            ->first();

        return [
            'translation_key' => $translationKey,
            'source_locale' => $sourceLocale,
            'target_locale' => $targetLocale,
            'lang_value' => $langValue,
            'is_obsolete' => $langValue?->status === 'obsolete',
            'possible_matching_entry' => $this->possibleMatchingEntryForObsoleteSourceKey($translationKey),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function possibleMatchingEntryForObsoleteSourceKey(string $translationKey): ?array
    {
        if (! $this->hasTables([
            'translation_workbench_keys',
            'translation_workbench_key_findings',
            'translation_workbench_findings',
            'translation_workbench_source_files',
        ])) {
            return null;
        }

        $segments = collect(explode('.', $translationKey))
            ->map(static fn(string $segment): string => trim($segment))
            ->filter(static fn(string $segment): bool => $segment !== '')
            ->values();

        if ($segments->count() < 2) {
            return null;
        }

        for ($offset = 1; $offset < $segments->count() - 1; $offset++) {
            $candidate = $segments->skip($offset)->implode('.');
            $match = $this->possibleMatchingEntryForKeySuffix($candidate);

            if ($match !== null) {
                $match['searched_suffix'] = $candidate;
                $match['deleted_segments'] = $segments->take($offset)->values()->all();
                $match['deleted_segments_count'] = $offset;

                return $match;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function possibleMatchingEntryForKeySuffix(string $keySuffix): ?array
    {
        $suffixLike = '%.' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $keySuffix);

        $row = DB::table('translation_workbench_keys as keys')
            ->leftJoin('translation_workbench_key_findings as key_findings', function ($join): void {
                $join
                    ->on('key_findings.key_id', '=', 'keys.id')
                    ->where('key_findings.status', '=', 'active');
            })
            ->leftJoin('translation_workbench_findings as findings', 'findings.id', '=', 'key_findings.finding_id')
            ->leftJoin('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->where('keys.status', '!=', 'obsolete')
            ->where(function ($query): void {
                $query
                    ->whereNull('findings.id')
                    ->orWhere('findings.status', '!=', 'obsolete');
            })
            ->where(function ($query) use ($keySuffix, $suffixLike): void {
                $query
                    ->where('keys.translation_key', $keySuffix)
                    ->orWhere('keys.suggested_key', $keySuffix)
                    ->orWhereRaw("keys.translation_key LIKE ? ESCAPE '!'", [$suffixLike])
                    ->orWhereRaw("keys.suggested_key LIKE ? ESCAPE '!'", [$suffixLike])
                    ->orWhere('findings.found_translation_key', $keySuffix)
                    ->orWhere('findings.existing_key', $keySuffix)
                    ->orWhere('findings.suggested_key', $keySuffix)
                    ->orWhereRaw("findings.found_translation_key LIKE ? ESCAPE '!'", [$suffixLike])
                    ->orWhereRaw("findings.existing_key LIKE ? ESCAPE '!'", [$suffixLike])
                    ->orWhereRaw("findings.suggested_key LIKE ? ESCAPE '!'", [$suffixLike]);
            })
            ->orderByRaw(
                "CASE
                    WHEN keys.translation_key = ? THEN 10
                    WHEN keys.suggested_key = ? THEN 20
                    WHEN findings.suggested_key = ? THEN 30
                    WHEN findings.existing_key = ? THEN 40
                    WHEN findings.found_translation_key = ? THEN 50
                    WHEN keys.translation_key LIKE ? ESCAPE '!' THEN 60
                    WHEN keys.suggested_key LIKE ? ESCAPE '!' THEN 70
                    WHEN findings.suggested_key LIKE ? ESCAPE '!' THEN 80
                    WHEN findings.existing_key LIKE ? ESCAPE '!' THEN 90
                    WHEN findings.found_translation_key LIKE ? ESCAPE '!' THEN 100
                    ELSE 999
                END",
                [
                    $keySuffix,
                    $keySuffix,
                    $keySuffix,
                    $keySuffix,
                    $keySuffix,
                    $suffixLike,
                    $suffixLike,
                    $suffixLike,
                    $suffixLike,
                    $suffixLike,
                ],
            )
            ->select([
                'keys.id as key_id',
                'keys.translation_key',
                'keys.suggested_key as key_suggested_key',
                'keys.status as key_status',
                'keys.review_status',
                'findings.id as finding_id',
                'findings.suggested_key as finding_suggested_key',
                'findings.existing_key as finding_existing_key',
                'findings.found_translation_key',
                'findings.status as finding_status',
                'findings.source_line',
                'source_files.path as source_path',
            ])
            ->first();

        if (! $row) {
            return null;
        }

        return [
            'key_id' => $row->key_id ? (int) $row->key_id : null,
            'finding_id' => $row->finding_id ? (int) $row->finding_id : null,
            'translation_key' => $this->nullableString($row->translation_key ?? null),
            'key_suggested_key' => $this->nullableString($row->key_suggested_key ?? null),
            'key_status' => $this->nullableString($row->key_status ?? null),
            'review_status' => $this->nullableString($row->review_status ?? null),
            'finding_suggested_key' => $this->nullableString($row->finding_suggested_key ?? null),
            'finding_existing_key' => $this->nullableString($row->finding_existing_key ?? null),
            'found_translation_key' => $this->nullableString($row->found_translation_key ?? null),
            'finding_status' => $this->nullableString($row->finding_status ?? null),
            'source_line' => $row->source_line ? (int) $row->source_line : null,
            'source_path' => $this->nullableString($row->source_path ?? null),
        ];
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
     * @return array{bytes: int|null, pretty: string}
     */
    private function tableStorageSize(string $table): array
    {
        if (! Schema::hasTable($table)) {
            return [
                'bytes' => null,
                'pretty' => '—',
            ];
        }

        try {
            $row = DB::selectOne(
                'SELECT pg_total_relation_size(to_regclass(?))::bigint as bytes, pg_size_pretty(pg_total_relation_size(to_regclass(?))) as pretty',
                [$table, $table],
            );
        } catch (\Throwable) {
            return [
                'bytes' => null,
                'pretty' => '—',
            ];
        }

        return [
            'bytes' => isset($row->bytes) ? (int) $row->bytes : null,
            'pretty' => (string) ($row->pretty ?? '—'),
        ];
    }

    /**
     * @return array<int, array{label: string, count: int}>
     */
    private function supportedLocaleSummaryRows(): array
    {
        if (! Schema::hasTable('locales')) {
            return [
                [
                    'label' => __('System locales'),
                    'count' => 0,
                ],
            ];
        }

        $appMainLocales = collect((array) app(AppGeneralSettings::class)->availableLocales)
            ->map(static fn(mixed $locale): string => is_string($locale) ? LocaleCode::normalize($locale) : '')
            ->filter(static fn(string $locale): bool => $locale !== '')
            ->unique()
            ->values();

        $supportedLocales = Locale::query()
            ->get(['code', 'normalized_code', 'is_active'])
            ->map(static function (Locale $locale): array {
                $code = LocaleCode::normalize((string) ($locale->normalized_code ?: $locale->code));
                $language = (string) (LocaleCode::parts($code)['language'] ?? '');
                $isSubLocale = $code !== '' && $language !== '' && $code !== $language;

                return [
                    'code' => $code,
                    'language' => $language,
                    'is_active' => (bool) $locale->is_active,
                    'is_sub_locale' => $isSubLocale,
                ];
            })
            ->filter(static fn(array $locale): bool => (string) $locale['code'] !== '');

        $supportedSubLocales = $supportedLocales->where('is_sub_locale', true);
        $appSubLocales = $supportedSubLocales
            ->where('is_active', true)
            ->filter(static fn(array $locale): bool => $appMainLocales->contains((string) $locale['language']));

        return [
            [
                'label' => __('System locales'),
                'count' => $supportedLocales->count(),
            ],
            [
                'label' => __('System main languages'),
                'count' => $supportedLocales->where('is_sub_locale', false)->count(),
            ],
            [
                'label' => __('System sub languages'),
                'count' => $supportedSubLocales->count(),
            ],
            [
                'label' => __('App locales'),
                'count' => $appMainLocales->count() + $appSubLocales->count(),
            ],
            [
                'label' => __('App main languages'),
                'count' => $appMainLocales->count(),
            ],
            [
                'label' => __('App sub languages'),
                'count' => $appSubLocales->count(),
            ],
        ];
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
        return $this->schemaHasTable('translation_workbench_lang_values')
            && collect([
                'locale',
                'locale_role',
                'main_locale',
                'parent_locale',
                'translation_key',
                'status',
            ])->every(fn(string $column): bool => $this->schemaHasColumn('translation_workbench_lang_values', $column));
    }

    /**
     * @param  array<int, string>  $tables
     */
    private function hasTables(array $tables): bool
    {
        return collect($tables)->every(fn(string $table): bool => $this->schemaHasTable($table));
    }

    private function schemaHasTable(string $table): bool
    {
        return $this->schemaTableCache[$table] ??= Schema::hasTable($table);
    }

    private function schemaHasColumn(string $table, string $column): bool
    {
        $key = $table . '.' . $column;

        return $this->schemaColumnCache[$key] ??= Schema::hasColumn($table, $column);
    }
}
