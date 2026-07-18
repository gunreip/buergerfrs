<?php

// packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchEntries.php

namespace Gunreip\TranslationWorkbench\Livewire;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Models\Locale;
use App\Settings\AppGeneralSettings;
use App\Support\Locale\LocaleCode;
use Flux\Flux;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTimelineRecorder;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Gunreip\TranslationWorkbench\Scanner\TranslationKeyPartsFactory;
use Gunreip\TranslationWorkbench\Support\TranslationKeySegmentFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
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
        'findingStatus',
        'findingKind',
        'findingCandidateType',
        'findingNamespace',
        'findingGroup',
        'findingKeyRelation',
        'findingSourceValue',
        'perPage',
        'findingSortField',
        'findingSortDirection',
        'showOverviewTabs',
    ];

    public string $findingSearch = '';

    public string $findingStatus = 'all';

    public string $findingKind = 'all';

    public string $findingCandidateType = 'all';

    public string $findingNamespace = 'all';

    public string $findingGroup = 'all';

    public string $findingKeyRelation = 'all';

    public string $findingSourceValue = 'all';

    public int $perPage = 25;

    public string $findingSortField = 'last_seen';

    public string $findingSortDirection = 'desc';

    public bool $showOverviewTabs = true;

    public bool $reviewModalOpen = false;

    public bool $editModalOpen = false;

    public bool $dynamicEditModalOpen = false;

    public bool $dynamicMultiEditModalOpen = false;

    public bool $dynamicReviewModalOpen = false;

    public bool $dynamicSourceLinkConfirmModalOpen = false;

    public bool $timelineModalOpen = false;

    public bool $translationKeyModalOpen = false;

    public ?int $reviewFindingId = null;

    public ?int $editFindingId = null;

    public ?int $dynamicReviewFindingId = null;

    public ?int $dynamicSourceLinkRelatedSourceId = null;

    public ?int $timelineFindingId = null;

    public ?int $translationKeyFindingId = null;

    public ?string $translationKeyValue = null;

    public ?string $translationKeySegmentBaseValue = null;

    public ?string $targetTranslationValue = null;

    public ?string $sourceTranslationValue = null;

    public bool $sourceTranslationEditable = false;

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
     * @var array<int, string>
     */
    public array $translationKeyDeletedSegments = [];

    public function render(): View
    {
        $editFinding = $this->selectedFinding($this->editFindingId);
        $dynamicReviewFinding = $this->selectedFinding($this->dynamicReviewFindingId);
        $editLocales = $this->editLocales();

        return view('translation-workbench::livewire.entries', [
            'findings' => $this->findings(),
            'reviewFinding' => $this->selectedFinding($this->reviewFindingId),
            'editFinding' => $editFinding,
            'dynamicReviewFinding' => $dynamicReviewFinding,
            'dynamicReviewReady' => $dynamicReviewFinding ? $this->isDynamicReviewReady($dynamicReviewFinding) : false,
            'dynamicReviewSources' => $this->dynamicReviewSources($this->dynamicReviewFindingId),
            'dynamicSourceLinkPreview' => $this->dynamicSourceLinkPreview(),
            'dynamicEditFinding' => $this->selectedFinding($this->editFindingId),
            'dynamicMultiEditFinding' => $this->selectedFinding($this->editFindingId),
            'editLocales' => $editLocales,
            'editValues' => $this->editValues($editFinding, $editLocales),
            'dynamicMultiRows' => $this->dynamicMultiRows($this->editFindingId, $editLocales),
            'timelineFinding' => $this->selectedFinding($this->timelineFindingId),
            'translationKeyFinding' => $this->selectedFinding($this->translationKeyFindingId),
            'translationKeySegmentStats' => $this->translationKeySegmentStats($this->translationKeyFindingId),
            'translationKeySegmentControls' => $this->translationKeySegmentControls(),
            'previousReviewFindingId' => $this->reviewAdjacentFindingId('previous'),
            'nextReviewFindingId' => $this->reviewAdjacentFindingId('next'),
            'findingStatusOptions' => $this->distinctOptions('translation_workbench_findings', 'status'),
            'findingKindOptions' => $this->distinctOptions('translation_workbench_findings', 'kind'),
            'findingCandidateTypeOptions' => $this->findingCandidateTypeOptions(),
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
            'targetMainLocale' => (string) ($editLocales['active'] ?? app()->getLocale()),
            'findingKindCounts' => $this->distribution('translation_workbench_findings', 'kind'),
            'keyTypeCounts' => $this->distribution('translation_workbench_keys', 'key_type'),
            'localeRoleCounts' => $this->distribution('translation_workbench_lang_values', 'locale_role'),
            'localeCounts' => $this->distribution('translation_workbench_lang_values', 'locale'),
            'timelineEventCounts' => $this->distribution('translation_workbench_timeline_events', 'event_type'),
            'findingFiltersActive' => $this->findingFiltersActive(),
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
        $this->findingStatus = $this->normalizeOptionState($state['findingStatus'] ?? $defaults['findingStatus'] ?? $this->findingStatus);
        $this->findingKind = $this->normalizeOptionState($state['findingKind'] ?? $defaults['findingKind'] ?? $this->findingKind);
        $this->findingCandidateType = $this->normalizeOptionState($state['findingCandidateType'] ?? $defaults['findingCandidateType'] ?? $this->findingCandidateType);
        $this->findingNamespace = $this->normalizeOptionState($state['findingNamespace'] ?? $defaults['findingNamespace'] ?? $this->findingNamespace);
        $this->findingGroup = $this->normalizeOptionState($state['findingGroup'] ?? $defaults['findingGroup'] ?? $this->findingGroup);
        $this->findingKeyRelation = $this->normalizeOptionState($state['findingKeyRelation'] ?? $defaults['findingKeyRelation'] ?? $this->findingKeyRelation);
        $this->findingSourceValue = $this->normalizeOptionState($state['findingSourceValue'] ?? $defaults['findingSourceValue'] ?? $this->findingSourceValue);
        $this->perPage = $this->normalizedPerPage($state['perPage'] ?? $defaults['perPage'] ?? $this->perPage);
        $this->findingSortField = $this->normalizeFindingSortField($state['findingSortField'] ?? $defaults['findingSortField'] ?? $this->findingSortField);
        $this->findingSortDirection = $this->normalizeSortDirection($state['findingSortDirection'] ?? $defaults['findingSortDirection'] ?? $this->findingSortDirection);
        $this->showOverviewTabs = (bool) ($state['showOverviewTabs'] ?? $defaults['showOverviewTabs'] ?? $this->showOverviewTabs);

        $this->setPage(1);
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

    public function resetFindingFilters(): void
    {
        $this->findingSearch = '';
        $this->findingStatus = 'all';
        $this->findingKind = 'all';
        $this->findingCandidateType = 'all';
        $this->findingNamespace = 'all';
        $this->findingGroup = 'all';
        $this->findingKeyRelation = 'all';
        $this->findingSourceValue = 'all';

        $this->resetPage();
        $this->persistUiState();
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

    public function updatedShowOverviewTabs(): void
    {
        $this->persistUiState();
    }

    public function openReviewModal(int $findingId): void
    {
        $this->reviewFindingId = $findingId;
        $this->reviewModalOpen = true;
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

        if ($selectedFinding && $this->isDynamicFinding($selectedFinding)) {
            $this->openDynamicReviewModal($findingId);

            return;
        }

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

        $this->bootstrapEditState($selectedFinding);
        $this->reviewModalOpen = false;
        $this->editModalOpen = false;
        $this->dynamicEditModalOpen = false;
        $this->dynamicMultiEditModalOpen = false;
        $this->dynamicReviewModalOpen = false;

        if ($this->isDynamicMultiFinding($selectedFinding)) {
            $this->dynamicMultiEditModalOpen = true;

            return;
        }

        if ($this->isDynamicFinding($selectedFinding)) {
            $this->dynamicEditModalOpen = true;

            return;
        }

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

        if ($this->isDynamicMultiFinding($selectedFinding)) {
            $this->dynamicMultiEditModalOpen = true;

            return;
        }

        $this->dynamicEditModalOpen = true;
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
                $sourceValue = $this->nullableString($row['source'] ?? $row['native_label'] ?? null);

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

        $this->dispatch('buergerfrs:focus-field-and-select', inputId: 'translation-workbench-target-translation-value');
    }

    public function copyDynamicMultiSourceToTarget(string $fieldKey): void
    {
        $row = collect($this->dynamicMultiRows($this->editFindingId, $this->editLocales()))
            ->firstWhere('field_key', $fieldKey);

        if (! is_array($row)) {
            return;
        }

        $sourceValue = $this->nullableString($row['source'] ?? $row['native_label'] ?? null);

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
            $sourceValue = $this->nullableString($row['source'] ?? $row['native_label'] ?? null);

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
            'translationKeyValue' => ['nullable', 'string'],
        ]);

        $translationKey = $this->nullableString($this->translationKeyValue);

        $saved = $this->saveKeyReviewDecision(
            key: $key,
            finding: $finding,
            selectedFinding: $selectedFinding,
            attributes: $this->keyStructureFromTranslationKey($translationKey),
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

        DB::transaction(function () use ($key, $finding, $suggestedKey): void {
            $oldValues = $key->only([
                'translation_key',
                'review_status',
                'reviewed_at',
                'reviewed_by_user_id',
            ]);

            $key->forceFill([
                'translation_key' => $suggestedKey,
                'review_status' => 'reviewed',
                'reviewed_at' => now(),
                'reviewed_by_user_id' => Auth::id(),
            ])->save();

            $newValues = $key->only([
                'translation_key',
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
        $this->editModalOpen = false;
        $this->editFindingId = null;
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
            ])
            ->addSelect([
                Schema::hasColumn('translation_workbench_findings', 'dynamic_data_state')
                    ? 'findings.dynamic_data_state'
                    : DB::raw('null as dynamic_data_state'),
                Schema::hasColumn('translation_workbench_keys', 'dynamic_data_state')
                    ? 'keys.dynamic_data_state as key_dynamic_data_state'
                    : DB::raw('null as key_dynamic_data_state'),
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
                        AND NULLIF(BTRIM(COALESCE(findings.literal_text, findings.literal_text_suggested, ?)), ?) IS NOT NULL
                        AND BTRIM(source_values.value) <> BTRIM(COALESCE(findings.literal_text, findings.literal_text_suggested, ?))
                ) THEN 1 ELSE 0 END as source_value_differs',
                [$sourceLocale, 'active', '', '', '', ''],
            );
        $this->addKeyCandidateReviewSelects($query);
        $this->addFindingHistorySelect($query);
        $this->addFindingDynamicContextSelect($query, $targetLocale);
        $this->addFindingDynamicSourceSelects($query);

        $this->applyFindingFilters($query, $sourceLocale);

        $this->applyFindingSort($query);

        return $query->paginate($this->normalizedPerPage());
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
            ->select('findings.id');

        $this->applyFindingFilters($query, $sourceLocale);
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
            Schema::hasColumn('translation_workbench_findings', 'dynamic_data_state')
                ? 'findings.dynamic_data_state'
                : DB::raw('null as dynamic_data_state'),
            Schema::hasColumn('translation_workbench_keys', 'dynamic_data_state')
                ? 'keys.dynamic_data_state as key_dynamic_data_state'
                : DB::raw('null as key_dynamic_data_state'),
        ]);
        $this->addKeyCandidateReviewSelects($query);
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

        if (Schema::hasTable('translation_workbench_dynamic_key_values')) {
            $values = DB::table('translation_workbench_dynamic_key_values')
                ->where('key_id', $selectedFinding->key_id)
                ->whereIn('locale', [$sourceLocale, $targetLocale])
                ->orderBy('value_key')
                ->get(['value_key', 'locale', 'value', 'native_label', 'status'])
                ->groupBy('value_key');

            if ($values->isNotEmpty()) {
                return $values
                    ->map(function ($localeRows, string $valueKey) use ($sourceLocale, $targetLocale): array {
                        $sourceRow = $localeRows->firstWhere('locale', $sourceLocale);
                        $targetRow = $localeRows->firstWhere('locale', $targetLocale);

                        return [
                            'field_key' => $this->dynamicMultiFieldKey($valueKey),
                            'value_key' => $valueKey,
                            'source' => $this->nullableString($sourceRow?->value),
                            'target' => $this->nullableString($targetRow?->value),
                            'native_label' => $this->nullableString($sourceRow?->native_label ?? $targetRow?->native_label),
                            'status' => $this->nullableString($targetRow?->status ?? $sourceRow?->status),
                        ];
                    })
                    ->values()
                    ->all();
            }
        }

        if (! $this->hasTables([
            'translation_workbench_dynamic_sources',
            'translation_workbench_dynamic_source_values',
        ])) {
            return [];
        }

        return DB::table('translation_workbench_dynamic_source_values as source_values')
            ->join('translation_workbench_dynamic_sources as sources', 'sources.id', '=', 'source_values.dynamic_source_id')
            ->where('sources.status', '<>', 'obsolete')
            ->where('source_values.status', 'active')
            ->where(function ($query) use ($selectedFinding): void {
                $query->where('sources.finding_id', $selectedFinding->id)
                    ->orWhere('sources.key_id', $selectedFinding->key_id);
            })
            ->orderBy('source_values.value_key')
            ->get(['source_values.value_key', 'source_values.native_label', 'source_values.status'])
            ->map(fn(object $value): array => [
                'field_key' => $this->dynamicMultiFieldKey((string) $value->value_key),
                'value_key' => (string) $value->value_key,
                'source' => $this->nullableString($value->native_label),
                'target' => null,
                'native_label' => $this->nullableString($value->native_label),
                'status' => $this->nullableString($value->status),
            ])
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

        if ($oldValue === $newValue || (! $langValue && $newValue === null)) {
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
            'status' => $newValue !== null ? 'active' : 'missing',
            'last_seen_at' => $now,
            'meta' => [
                'source' => 'translation-workbench:modal-edit',
                'finding_id' => $selectedFinding->id,
                'key_id' => $selectedFinding->key_id,
            ],
        ];

        if ($langValue) {
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
            Schema::hasColumn('translation_workbench_keys', 'is_ui_candidate_rejected')
                ? 'keys.is_ui_candidate_rejected'
                : DB::raw('false as is_ui_candidate_rejected'),
            Schema::hasColumn('translation_workbench_keys', 'is_dynamic_candidate_rejected')
                ? 'keys.is_dynamic_candidate_rejected'
                : DB::raw('false as is_dynamic_candidate_rejected'),
            Schema::hasColumn('translation_workbench_keys', 'reviewed_is_ui_candidate')
                ? 'keys.reviewed_is_ui_candidate'
                : DB::raw('null as reviewed_is_ui_candidate'),
            Schema::hasColumn('translation_workbench_keys', 'reviewed_is_dynamic_candidate')
                ? 'keys.reviewed_is_dynamic_candidate'
                : DB::raw('null as reviewed_is_dynamic_candidate'),
            Schema::hasColumn('translation_workbench_keys', 'reviewed_is_dynamic_multi')
                ? 'keys.reviewed_is_dynamic_multi'
                : DB::raw('null as reviewed_is_dynamic_multi'),
        ]);
    }

    private function addFindingHistorySelect($query): void
    {
        $historyChecks = [];

        if (Schema::hasTable('translation_workbench_timeline_events')) {
            $historyChecks[] = 'EXISTS (
                SELECT 1
                FROM translation_workbench_timeline_events as timeline_events
                WHERE timeline_events.finding_id = findings.id
                    OR (
                        keys.id IS NOT NULL
                        AND timeline_events.key_id = keys.id
                    )
            )';
        }

        if (Schema::hasTable('translation_workbench_reviews')) {
            $historyChecks[] = 'EXISTS (
                SELECT 1
                FROM translation_workbench_reviews as reviews
                WHERE reviews.finding_id = findings.id
                    OR (
                        keys.id IS NOT NULL
                        AND reviews.key_id = keys.id
                    )
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

        if (Schema::hasTable('translation_workbench_option_discoveries')) {
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
        if (! Schema::hasTable('translation_workbench_dynamic_sources')) {
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

        $runtimeSourceIds = $sources
            ->filter(static fn(object $source): bool => in_array($source->source_type, ['runtime_options', 'runtime_db_options'], true))
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
                'is_runtime_options' => in_array($source->source_type, ['runtime_options', 'runtime_db_options'], true),
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

        if ($this->hasConfirmedDynamicSourceLink($selectedFinding)) {
            return true;
        }

        return (int) ($selectedFinding->dynamic_source_count ?? 0) > 0
            && (int) ($selectedFinding->dynamic_unresolved_source_count ?? 0) === 0;
    }

    private function hasConfirmedDynamicSourceLink(object $selectedFinding): bool
    {
        if (! $this->hasTables([
            'translation_workbench_dynamic_sources',
            'translation_workbench_dynamic_source_candidates',
        ])) {
            return false;
        }

        return DB::table('translation_workbench_dynamic_source_candidates as candidates')
            ->join('translation_workbench_dynamic_sources as runtime_sources', 'runtime_sources.id', '=', 'candidates.dynamic_source_id')
            ->where('candidates.candidate_source_type', 'related_dynamic_source')
            ->where('candidates.review_status', 'confirmed')
            ->where('candidates.status', 'active')
            ->where('runtime_sources.status', '<>', 'obsolete')
            ->whereIn('runtime_sources.source_type', ['runtime_options', 'runtime_db_options'])
            ->where(function ($query) use ($selectedFinding): void {
                $query->where('runtime_sources.finding_id', $selectedFinding->id);

                if ($selectedFinding->key_id) {
                    $query->orWhere('runtime_sources.key_id', $selectedFinding->key_id);
                }
            })
            ->exists();
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
            || (int) ($selectedFinding->dynamic_value_count ?? 0) > 1;
    }

    private function hasKeyCandidateReviewColumns(): bool
    {
        return Schema::hasColumn('translation_workbench_keys', 'is_ui_candidate_rejected')
            && Schema::hasColumn('translation_workbench_keys', 'is_dynamic_candidate_rejected')
            && Schema::hasColumn('translation_workbench_keys', 'reviewed_is_ui_candidate')
            && Schema::hasColumn('translation_workbench_keys', 'reviewed_is_dynamic_candidate')
            && Schema::hasColumn('translation_workbench_keys', 'reviewed_is_dynamic_multi');
    }

    private function hasDynamicDataStateColumns(): bool
    {
        return Schema::hasColumn('translation_workbench_findings', 'dynamic_data_state')
            && Schema::hasColumn('translation_workbench_keys', 'dynamic_data_state');
    }

    /**
     * @return array<string, string|null>
     */
    private function dynamicDataStateAttributes(bool $isDynamic): array
    {
        if (! Schema::hasColumn('translation_workbench_keys', 'dynamic_data_state')) {
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
        });

        Flux::toast(
            heading: $toastHeading,
            text: $toastText,
            variant: 'success',
        );

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function keyStructureFromTranslationKey(?string $translationKey): array
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
            ];
        }

        return [
            'translation_key' => $translationKey,
            ...app(TranslationKeyPartsFactory::class)->fromKey($translationKey),
            ...app(TranslationKeySegmentFactory::class)->fromKey($translationKey),
        ];
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

    private function applyFindingFilters($query, string $sourceLocale): void
    {
        if ($this->findingStatus !== 'all') {
            $query->where('findings.status', $this->findingStatus);
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
                    'dynamic' => __('Dynamic candidate'),
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

    private function findingFiltersActive(): bool
    {
        return $this->findingSearch !== ''
            || $this->findingStatus !== 'all'
            || $this->findingKind !== 'all'
            || $this->findingCandidateType !== 'all'
            || $this->findingNamespace !== 'all'
            || $this->findingGroup !== 'all'
            || $this->findingKeyRelation !== 'all'
            || $this->findingSourceValue !== 'all';
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
            'findingStatus' => $this->findingStatus,
            'findingKind' => $this->findingKind,
            'findingCandidateType' => $this->findingCandidateType,
            'findingNamespace' => $this->findingNamespace,
            'findingGroup' => $this->findingGroup,
            'findingKeyRelation' => $this->findingKeyRelation,
            'findingSourceValue' => $this->findingSourceValue,
            'perPage' => $this->perPage,
            'findingSortField' => $this->findingSortField,
            'findingSortDirection' => $this->findingSortDirection,
            'showOverviewTabs' => $this->showOverviewTabs,
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
