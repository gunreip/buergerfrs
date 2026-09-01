<?php

// packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchRawData.php

namespace Gunreip\TranslationWorkbench\Livewire;

use App\Livewire\Concerns\InteractsWithUserSettings;
use Gunreip\TranslationWorkbench\Support\RawDataColumnPresentation;
use Gunreip\TranslationWorkbench\Support\TranslationWorkbenchVersion;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

class TranslationWorkbenchRawData extends Component
{
    use InteractsWithUserSettings;
    use WithPagination;

    /**
     * User-facing Raw-Data table state that is safe to persist.
     *
     * This list intentionally contains only table selections, filters, sort and pagination.
     *
     * @var array<int, string>
     */
    private const PERSISTED_STATE_PROPERTIES = [
        'activeTable',
        'perPage',
        'sortField',
        'sortDirection',
        'sourceFilesSearch',
        'sourceFilesId',
        'sourceFilesPath',
        'sourceFilesRoot',
        'sourceFilesArea',
        'sourceFilesPackageVendor',
        'sourceFilesPackageName',
        'sourceFilesDomain',
        'sourceFilesSection',
        'sourceFilesContext',
        'sourceFilesScope',
        'sourceFilesExtra',
        'sourceFilesFilename',
        'sourceFilesSourceType',
        'sourceFilesExtension',
        'sourceFilesStatus',
        'eventTypesSearch',
        'eventTypesId',
        'eventTypesKey',
        'eventTypesLabel',
        'eventTypesCategory',
        'eventTypesSeverity',
        'findingsSearch',
        'findingsId',
        'findingsSourceFileId',
        'findingsSourceLine',
        'findingsKind',
        'findingsFunctionName',
        'findingsEntryType',
        'findingsCandidateType',
        'findingsStatus',
        'findingsNamespace',
        'findingsGroup',
        'findingsScope',
        'findingsDynamicScope',
        'keysSearch',
        'keysId',
        'keysTranslationKey',
        'keysSuggestedKey',
        'keysNamespace',
        'keysGroup',
        'keysScope',
        'keysSegmentDomain',
        'keysSegmentSection',
        'keysSegmentContext',
        'keysSegmentExtra',
        'keysSegmentName',
        'keysKeyType',
        'keysIsUiKey',
        'keysIsDynamicKey',
        'keysIsDynamicMulti',
        'keysStatus',
        'keysReviewStatus',
        'keyFindingsSearch',
        'keyFindingsId',
        'keyFindingsKeyId',
        'keyFindingsFindingId',
        'keyFindingsRelationType',
        'keyFindingsStatus',
        'keyValuesSearch',
        'keyValuesId',
        'keyValuesKeyId',
        'keyValuesLocale',
        'keyValuesStatus',
        'keyValuesSource',
        'dynamicKeyValuesSearch',
        'dynamicKeyValuesId',
        'dynamicKeyValuesKeyId',
        'dynamicKeyValuesValueKey',
        'dynamicKeyValuesLocale',
        'dynamicKeyValuesStatus',
        'dynamicKeyValuesSource',
        'dynamicSourcesSearch',
        'dynamicSourcesId',
        'dynamicSourcesKeyId',
        'dynamicSourcesFindingId',
        'dynamicSourcesOptionDiscoveryId',
        'dynamicSourcesClassification',
        'dynamicSourcesCardinality',
        'dynamicSourcesOrigin',
        'dynamicSourcesSourceType',
        'dynamicSourcesConfidence',
        'dynamicSourcesStatus',
        'dynamicSourceCandidatesSearch',
        'dynamicSourceCandidatesId',
        'dynamicSourceCandidatesDynamicSourceId',
        'dynamicSourceCandidatesKeyId',
        'dynamicSourceCandidatesFindingId',
        'dynamicSourceCandidatesCandidateSourceType',
        'dynamicSourceCandidatesConfidence',
        'dynamicSourceCandidatesReviewStatus',
        'dynamicSourceCandidatesStatus',
        'dynamicSourceValuesSearch',
        'dynamicSourceValuesId',
        'dynamicSourceValuesDynamicSourceId',
        'dynamicSourceValuesValueKey',
        'dynamicSourceValuesTranslationKey',
        'dynamicSourceValuesOrigin',
        'dynamicSourceValuesStatus',
        'langValuesSearch',
        'langValuesId',
        'langValuesMainLocale',
        'langValuesSubLocale',
        'langValuesNamespace',
        'langValuesLangKey',
        'langValuesTranslationKey',
        'langValuesSourcePath',
        'langValuesValueType',
        'langValuesStatus',
        'reviewsSearch',
        'reviewsId',
        'reviewsKeyId',
        'reviewsFindingId',
        'reviewsReviewType',
        'reviewsDecision',
        'reviewsReviewedByUserId',
        'keyInventorySearch',
        'keyInventoryId',
        'keyInventoryTranslationKey',
        'keyInventoryNamespace',
        'keyInventoryGroup',
        'keyInventoryKeyType',
        'keyInventoryStatus',
        'keyInventoryIsShared',
        'keyInventoryIsUi',
        'keyInventoryIsDynamic',
        'keyInventoryIsDynamicMulti',
        'keyInventoryHasActiveCodeUsage',
        'keyInventoryHasLangValues',
        'keyInventoryIsOrphanedLangValue',
        'keyInventoryCandidateForLangDelete',
        'sharedKeyCandidatesSearch',
        'sharedKeyCandidatesId',
        'sharedKeyCandidatesFindingId',
        'sharedKeyCandidatesKeyId',
        'sharedKeyCandidatesMatchedKeyId',
        'sharedKeyCandidatesNormalizedLiteral',
        'sharedKeyCandidatesCurrentTranslationKey',
        'sharedKeyCandidatesSuggestedSharedTranslationKey',
        'sharedKeyCandidatesConfidence',
        'sharedKeyCandidatesStatus',
        'sharedKeyCandidatesMinReviewCount',
        'sharedKeyCandidatesMinFindingCount',
        'timelineEventsSearch',
        'timelineEventsId',
        'timelineEventsEventType',
        'timelineEventsEventTypeId',
        'timelineEventsKeyId',
        'timelineEventsFindingId',
        'timelineEventsReviewId',
        'timelineEventsCreatedByUserId',
        'timelineEventsCreatedRange',
        'timelineEventsTimeFrom',
        'timelineEventsChangedRange',
        'timelineEventsChangedTime',
        'timelineEventsTimeSpan',
    ];

    public string $pageTitle = 'Translation Workbench Raw-Data';

    public string $pageDescription = 'Raw database table output for the Translation Workbench package tables.';

    /**
     * @var array<int, string>
     */
    public array $tables = [
        'translation_workbench_entries',
        'translation_workbench_occurrences',
        'translation_workbench_events',
        'translation_workbench_values',
        'translation_workbench_dynamic_values',
        'translation_workbench_option_discoveries',
        'translation_workbench_duplicate_candidates',
    ];

    public string $activeTable = 'translation_workbench_entries';

    public int $perPage = 50;

    public string $sortField = 'id';

    public string $sortDirection = 'desc';

    public string $sourceFilesSearch = '';

    public string $sourceFilesId = '';

    public string $sourceFilesPath = 'all';

    public string $sourceFilesRoot = 'all';

    public string $sourceFilesArea = 'all';

    public string $sourceFilesPackageVendor = 'all';

    public string $sourceFilesPackageName = 'all';

    public string $sourceFilesDomain = 'all';

    public string $sourceFilesSection = 'all';

    public string $sourceFilesContext = 'all';

    public string $sourceFilesScope = 'all';

    public string $sourceFilesExtra = 'all';

    public string $sourceFilesFilename = 'all';

    public string $sourceFilesSourceType = 'all';

    public string $sourceFilesExtension = 'all';

    public string $sourceFilesStatus = 'all';

    public string $eventTypesSearch = '';

    public string $eventTypesId = '';

    public string $eventTypesKey = 'all';

    public string $eventTypesLabel = 'all';

    public string $eventTypesCategory = 'all';

    public string $eventTypesSeverity = 'all';

    public string $findingsSearch = '';

    public string $findingsId = '';

    public string $findingsSourceFileId = '';

    public string $findingsSourceLine = '';

    public string $findingsKind = 'all';

    public string $findingsFunctionName = 'all';

    public string $findingsEntryType = 'all';

    public string $findingsCandidateType = 'all';

    public string $findingsStatus = 'all';

    public string $findingsNamespace = 'all';

    public string $findingsGroup = 'all';

    public string $findingsScope = 'all';

    public string $findingsDynamicScope = 'all';

    public string $keysSearch = '';

    public string $keysId = '';

    public string $keysTranslationKey = 'all';

    public string $keysSuggestedKey = 'all';

    public string $keysNamespace = 'all';

    public string $keysGroup = 'all';

    public string $keysScope = 'all';

    public string $keysSegmentDomain = 'all';

    public string $keysSegmentSection = 'all';

    public string $keysSegmentContext = 'all';

    public string $keysSegmentExtra = 'all';

    public string $keysSegmentName = 'all';

    public string $keysKeyType = 'all';

    public string $keysIsUiKey = 'all';

    public string $keysIsDynamicKey = 'all';

    public string $keysIsDynamicMulti = 'all';

    public string $keysStatus = 'all';

    public string $keysReviewStatus = 'all';

    public string $keyFindingsSearch = '';

    public string $keyFindingsId = '';

    public string $keyFindingsKeyId = '';

    public string $keyFindingsFindingId = '';

    public string $keyFindingsRelationType = 'all';

    public string $keyFindingsStatus = 'all';

    public string $keyValuesSearch = '';

    public string $keyValuesId = '';

    public string $keyValuesKeyId = '';

    public string $keyValuesLocale = 'all';

    public string $keyValuesStatus = 'all';

    public string $keyValuesSource = 'all';

    public string $dynamicKeyValuesSearch = '';

    public string $dynamicKeyValuesId = '';

    public string $dynamicKeyValuesKeyId = '';

    public string $dynamicKeyValuesValueKey = '';

    public string $dynamicKeyValuesLocale = 'all';

    public string $dynamicKeyValuesStatus = 'all';

    public string $dynamicKeyValuesSource = 'all';

    public string $dynamicSourcesSearch = '';

    public string $dynamicSourcesId = '';

    public string $dynamicSourcesKeyId = '';

    public string $dynamicSourcesFindingId = '';

    public string $dynamicSourcesOptionDiscoveryId = '';

    public string $dynamicSourcesClassification = 'all';

    public string $dynamicSourcesCardinality = 'all';

    public string $dynamicSourcesOrigin = 'all';

    public string $dynamicSourcesSourceType = 'all';

    public string $dynamicSourcesConfidence = 'all';

    public string $dynamicSourcesStatus = 'all';

    public string $dynamicSourceCandidatesSearch = '';

    public string $dynamicSourceCandidatesId = '';

    public string $dynamicSourceCandidatesDynamicSourceId = '';

    public string $dynamicSourceCandidatesKeyId = '';

    public string $dynamicSourceCandidatesFindingId = '';

    public string $dynamicSourceCandidatesCandidateSourceType = 'all';

    public string $dynamicSourceCandidatesConfidence = 'all';

    public string $dynamicSourceCandidatesReviewStatus = 'all';

    public string $dynamicSourceCandidatesStatus = 'all';

    public string $dynamicSourceValuesSearch = '';

    public string $dynamicSourceValuesId = '';

    public string $dynamicSourceValuesDynamicSourceId = '';

    public string $dynamicSourceValuesValueKey = '';

    public string $dynamicSourceValuesTranslationKey = '';

    public string $dynamicSourceValuesOrigin = 'all';

    public string $dynamicSourceValuesStatus = 'all';

    public string $langValuesSearch = '';

    public string $langValuesId = '';

    public string $langValuesMainLocale = 'all';

    public string $langValuesSubLocale = 'all';

    public string $langValuesNamespace = 'all';

    public string $langValuesLangKey = '';

    public string $langValuesTranslationKey = '';

    public string $langValuesSourcePath = '';

    public string $langValuesValueType = 'all';

    public string $langValuesStatus = 'all';

    public string $reviewsSearch = '';

    public string $reviewsId = '';

    public string $reviewsKeyId = '';

    public string $reviewsFindingId = '';

    public string $reviewsReviewType = 'all';

    public string $reviewsDecision = 'all';

    public string $reviewsReviewedByUserId = '';

    public string $keyInventorySearch = '';

    public string $keyInventoryId = '';

    public string $keyInventoryTranslationKey = '';

    public string $keyInventoryNamespace = 'all';

    public string $keyInventoryGroup = 'all';

    public string $keyInventoryKeyType = 'all';

    public string $keyInventoryStatus = 'all';

    public string $keyInventoryIsShared = 'all';

    public string $keyInventoryIsUi = 'all';

    public string $keyInventoryIsDynamic = 'all';

    public string $keyInventoryIsDynamicMulti = 'all';

    public string $keyInventoryHasActiveCodeUsage = 'all';

    public string $keyInventoryHasLangValues = 'all';

    public string $keyInventoryIsOrphanedLangValue = 'all';

    public string $keyInventoryCandidateForLangDelete = 'all';

    public string $sharedKeyCandidatesSearch = '';

    public string $sharedKeyCandidatesId = '';

    public string $sharedKeyCandidatesFindingId = '';

    public string $sharedKeyCandidatesKeyId = '';

    public string $sharedKeyCandidatesMatchedKeyId = '';

    public string $sharedKeyCandidatesNormalizedLiteral = '';

    public string $sharedKeyCandidatesCurrentTranslationKey = '';

    public string $sharedKeyCandidatesSuggestedSharedTranslationKey = '';

    public string $sharedKeyCandidatesConfidence = 'all';

    public string $sharedKeyCandidatesStatus = 'all';

    public string $sharedKeyCandidatesMinReviewCount = '';

    public string $sharedKeyCandidatesMinFindingCount = '';

    public string $timelineEventsSearch = '';

    public string $timelineEventsId = '';

    public string $timelineEventsEventType = 'all';

    public string $timelineEventsEventTypeId = '';

    public string $timelineEventsKeyId = '';

    public string $timelineEventsFindingId = '';

    public string $timelineEventsReviewId = '';

    public string $timelineEventsCreatedByUserId = '';

    /**
     * @var array{start: string|null, end: string|null, preset?: string|null}
     */
    public array $timelineEventsCreatedRange = [
        'start' => null,
        'end' => null,
        'preset' => null,
    ];

    public ?string $timelineEventsTimeFrom = null;

    /**
     * @var array{start: string|null, end: string|null, preset?: string|null}
     */
    public array $timelineEventsChangedRange = [
        'start' => null,
        'end' => null,
        'preset' => null,
    ];

    public ?string $timelineEventsChangedTime = null;

    public ?string $timelineEventsTimeSpan = '02:00';

    public string $timelineChainPreviewId = 'auto';

    public function mount(): void
    {
        $state = $this->userSetting($this->uiStateSettingKey(), $this->uiStateDefaults());

        if (! is_array($state)) {
            $state = [];
        }

        foreach (self::PERSISTED_STATE_PROPERTIES as $property) {
            if (! array_key_exists($property, $state) || ! property_exists($this, $property)) {
                continue;
            }

            $this->{$property} = $this->normalizedPersistedPropertyValue($property, $state[$property]);
        }

        $this->activeTable = $this->normalizedActiveTable();
        $this->perPage = $this->normalizedPerPage();
        $this->sortDirection = $this->sortDirection === 'asc' ? 'asc' : 'desc';
        $this->setPage(1);
    }

    public function updated(string $property): void
    {
        if (in_array($property, self::PERSISTED_STATE_PROPERTIES, true)) {
            $this->persistUiState();
        }

        if (str_starts_with($property, 'keyInventory')) {
            $this->resetPage();
        }

        if (str_starts_with($property, 'sharedKeyCandidates')) {
            $this->resetPage();
        }
    }

    public function setActiveTable(string $table, string $scroll = 'nearest'): void
    {
        $this->activeTable = in_array($table, $this->tables, true)
            ? $table
            : $this->tables[0];

        $this->resetSortForTable();
        $this->resetPage();
        $this->persistUiState();
        $this->dispatchActiveTableTabChanged($scroll);
    }

    public function updatedActiveTable(): void
    {
        $this->activeTable = $this->normalizedActiveTable();
        $this->resetSortForTable();
        $this->resetPage();
        $this->persistUiState();
        $this->dispatchActiveTableTabChanged();
    }

    public function openFirstTableTab(): void
    {
        $this->setActiveTable($this->tables[0] ?? $this->normalizedActiveTable(), 'first');
    }

    public function openPreviousTableTab(): void
    {
        $index = $this->activeTableIndex();

        if ($index <= 0) {
            return;
        }

        $this->setActiveTable($this->tables[$index - 1]);
    }

    public function openNextTableTab(): void
    {
        $index = $this->activeTableIndex();
        $lastIndex = count($this->tables) - 1;

        if ($index >= $lastIndex) {
            return;
        }

        $this->setActiveTable($this->tables[$index + 1]);
    }

    public function openLastTableTab(): void
    {
        $lastTable = $this->tables[array_key_last($this->tables)] ?? $this->normalizedActiveTable();

        $this->setActiveTable($lastTable, 'last');
    }

    public function sortBy(string $column): void
    {
        $table = $this->normalizedActiveTable();
        $columns = Schema::hasTable($table) ? Schema::getColumnListing($table) : [];

        if (! $this->isSortableColumn($column, $columns)) {
            return;
        }

        if ($this->sortField === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $column;
            $this->sortDirection = $this->defaultSortDirection($column);
        }

        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = $this->normalizedPerPage();
        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedSourceFilesSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSourceFilesId(): void
    {
        $this->resetPage();
    }

    public function updatedSourceFilesPath(): void
    {
        $this->resetPage();
    }

    public function updatedSourceFilesSourceType(): void
    {
        $this->sourceFilesExtension = 'all';
        $this->sourceFilesPath = 'all';

        $this->resetPage();
    }

    public function updatedSourceFilesExtension(): void
    {
        $this->sourceFilesPath = 'all';

        $this->resetPage();
    }

    public function updatedSourceFilesStatus(): void
    {
        $this->sourceFilesPath = 'all';

        $this->resetPage();
    }

    public function updatedSourceFilesRoot(): void
    {
        $this->sourceFilesArea = 'all';
        $this->sourceFilesPackageVendor = 'all';
        $this->sourceFilesPackageName = 'all';
        $this->resetSourceFilePathSegmentsAfterArea();

        $this->resetPage();
    }

    public function updatedSourceFilesArea(): void
    {
        $this->sourceFilesPackageVendor = 'all';
        $this->sourceFilesPackageName = 'all';
        $this->resetSourceFilePathSegmentsAfterArea();

        $this->resetPage();
    }

    public function updatedSourceFilesPackageVendor(): void
    {
        $this->sourceFilesPackageName = 'all';
        $this->resetSourceFilePathSegmentsAfterArea();

        $this->resetPage();
    }

    public function updatedSourceFilesPackageName(): void
    {
        $this->resetSourceFilePathSegmentsAfterArea();

        $this->resetPage();
    }

    public function updatedSourceFilesDomain(): void
    {
        $this->sourceFilesSection = 'all';
        $this->sourceFilesContext = 'all';
        $this->sourceFilesScope = 'all';
        $this->sourceFilesExtra = 'all';
        $this->sourceFilesFilename = 'all';
        $this->sourceFilesPath = 'all';

        $this->resetPage();
    }

    public function updatedSourceFilesSection(): void
    {
        $this->sourceFilesContext = 'all';
        $this->sourceFilesScope = 'all';
        $this->sourceFilesExtra = 'all';
        $this->sourceFilesFilename = 'all';
        $this->sourceFilesPath = 'all';

        $this->resetPage();
    }

    public function updatedSourceFilesContext(): void
    {
        $this->sourceFilesScope = 'all';
        $this->sourceFilesExtra = 'all';
        $this->sourceFilesFilename = 'all';
        $this->sourceFilesPath = 'all';

        $this->resetPage();
    }

    public function updatedSourceFilesScope(): void
    {
        $this->sourceFilesExtra = 'all';
        $this->sourceFilesFilename = 'all';
        $this->sourceFilesPath = 'all';

        $this->resetPage();
    }

    public function updatedSourceFilesExtra(): void
    {
        $this->sourceFilesFilename = 'all';
        $this->sourceFilesPath = 'all';

        $this->resetPage();
    }

    public function updatedSourceFilesFilename(): void
    {
        $this->sourceFilesPath = 'all';

        $this->resetPage();
    }

    public function resetSourceFilesFilters(): void
    {
        $this->sourceFilesSearch = '';
        $this->sourceFilesId = '';
        $this->sourceFilesPath = 'all';
        $this->sourceFilesRoot = 'all';
        $this->sourceFilesArea = 'all';
        $this->sourceFilesPackageVendor = 'all';
        $this->sourceFilesPackageName = 'all';
        $this->sourceFilesDomain = 'all';
        $this->sourceFilesSection = 'all';
        $this->sourceFilesContext = 'all';
        $this->sourceFilesScope = 'all';
        $this->sourceFilesExtra = 'all';
        $this->sourceFilesFilename = 'all';
        $this->sourceFilesSourceType = 'all';
        $this->sourceFilesExtension = 'all';
        $this->sourceFilesStatus = 'all';

        $this->resetPage();
        $this->persistUiState();
    }

    private function resetSourceFilePathSegmentsAfterArea(): void
    {
        $this->sourceFilesDomain = 'all';
        $this->sourceFilesSection = 'all';
        $this->sourceFilesContext = 'all';
        $this->sourceFilesScope = 'all';
        $this->sourceFilesExtra = 'all';
        $this->sourceFilesFilename = 'all';
        $this->sourceFilesPath = 'all';
    }

    public function updatedEventTypesSearch(): void
    {
        $this->resetPage();
    }

    public function updatedEventTypesId(): void
    {
        $this->resetPage();
    }

    public function updatedEventTypesKey(): void
    {
        $this->resetPage();
    }

    public function updatedEventTypesLabel(): void
    {
        $this->resetPage();
    }

    public function updatedEventTypesCategory(): void
    {
        $this->resetPage();
    }

    public function updatedEventTypesSeverity(): void
    {
        $this->resetPage();
    }

    public function resetEventTypesFilters(): void
    {
        $this->eventTypesSearch = '';
        $this->eventTypesId = '';
        $this->eventTypesKey = 'all';
        $this->eventTypesLabel = 'all';
        $this->eventTypesCategory = 'all';
        $this->eventTypesSeverity = 'all';

        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedFindingsSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFindingsId(): void
    {
        $this->resetPage();
    }

    public function updatedFindingsSourceFileId(): void
    {
        $this->resetPage();
    }

    public function updatedFindingsSourceLine(): void
    {
        $this->resetPage();
    }

    public function updatedFindingsKind(): void
    {
        $this->resetPage();
    }

    public function updatedFindingsFunctionName(): void
    {
        $this->resetPage();
    }

    public function updatedFindingsEntryType(): void
    {
        $this->resetPage();
    }

    public function updatedFindingsCandidateType(): void
    {
        $this->resetPage();
    }

    public function updatedFindingsStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFindingsNamespace(): void
    {
        $this->resetPage();
    }

    public function updatedFindingsGroup(): void
    {
        $this->normalizeFindingsScopeForSelectedGroup();

        $this->resetPage();
    }

    public function updatedFindingsScope(): void
    {
        $this->normalizeFindingsGroupForSelectedScope();

        $this->resetPage();
    }

    public function updatedFindingsDynamicScope(): void
    {
        $this->resetPage();
    }

    public function resetFindingsFilters(): void
    {
        $this->findingsSearch = '';
        $this->findingsId = '';
        $this->findingsSourceFileId = '';
        $this->findingsSourceLine = '';
        $this->findingsKind = 'all';
        $this->findingsFunctionName = 'all';
        $this->findingsEntryType = 'all';
        $this->findingsCandidateType = 'all';
        $this->findingsStatus = 'all';
        $this->findingsNamespace = 'all';
        $this->findingsGroup = 'all';
        $this->findingsScope = 'all';
        $this->findingsDynamicScope = 'all';

        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedKeysSearch(): void
    {
        $this->resetPage();
    }

    public function updatedKeysId(): void
    {
        $this->resetPage();
    }

    public function updatedKeysTranslationKey(): void
    {
        $this->resetPage();
    }

    public function updatedKeysSuggestedKey(): void
    {
        $this->resetPage();
    }

    public function updatedKeysNamespace(): void
    {
        $this->keysGroup = 'all';
        $this->resetKeysSegmentFilters();
        $this->keysTranslationKey = 'all';
        $this->keysSuggestedKey = 'all';

        $this->resetPage();
    }

    public function updatedKeysGroup(): void
    {
        $this->resetKeysSegmentFilters();
        $this->keysTranslationKey = 'all';
        $this->keysSuggestedKey = 'all';

        $this->resetPage();
    }

    public function updatedKeysScope(): void
    {
        $this->resetPage();
    }

    public function updatedKeysSegmentDomain(): void
    {
        $this->keysSegmentSection = 'all';
        $this->keysSegmentContext = 'all';
        $this->keysSegmentExtra = 'all';
        $this->keysSegmentName = 'all';
        $this->keysTranslationKey = 'all';
        $this->keysSuggestedKey = 'all';

        $this->resetPage();
    }

    public function updatedKeysSegmentSection(): void
    {
        $this->keysSegmentContext = 'all';
        $this->keysSegmentExtra = 'all';
        $this->keysSegmentName = 'all';
        $this->keysTranslationKey = 'all';
        $this->keysSuggestedKey = 'all';

        $this->resetPage();
    }

    public function updatedKeysSegmentContext(): void
    {
        $this->keysSegmentExtra = 'all';
        $this->keysSegmentName = 'all';
        $this->keysTranslationKey = 'all';
        $this->keysSuggestedKey = 'all';

        $this->resetPage();
    }

    public function updatedKeysSegmentExtra(): void
    {
        $this->keysSegmentName = 'all';
        $this->keysTranslationKey = 'all';
        $this->keysSuggestedKey = 'all';

        $this->resetPage();
    }

    public function updatedKeysSegmentName(): void
    {
        $this->keysTranslationKey = 'all';
        $this->keysSuggestedKey = 'all';

        $this->resetPage();
    }

    public function updatedKeysKeyType(): void
    {
        $this->resetPage();
    }

    public function updatedKeysIsUiKey(): void
    {
        $this->resetPage();
    }

    public function updatedKeysIsDynamicKey(): void
    {
        $this->resetPage();
    }

    public function updatedKeysIsDynamicMulti(): void
    {
        $this->resetPage();
    }

    public function updatedKeysStatus(): void
    {
        $this->resetPage();
    }

    public function updatedKeysReviewStatus(): void
    {
        $this->resetPage();
    }

    public function resetKeysFilters(): void
    {
        $this->keysSearch = '';
        $this->keysId = '';
        $this->keysTranslationKey = 'all';
        $this->keysSuggestedKey = 'all';
        $this->keysNamespace = 'all';
        $this->keysGroup = 'all';
        $this->keysScope = 'all';
        $this->resetKeysSegmentFilters();
        $this->keysKeyType = 'all';
        $this->keysIsUiKey = 'all';
        $this->keysIsDynamicKey = 'all';
        $this->keysIsDynamicMulti = 'all';
        $this->keysStatus = 'all';
        $this->keysReviewStatus = 'all';

        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedKeyFindingsSearch(): void
    {
        $this->resetPage();
    }

    public function updatedKeyFindingsId(): void
    {
        $this->resetPage();
    }

    public function updatedKeyFindingsKeyId(): void
    {
        $this->resetPage();
    }

    public function updatedKeyFindingsFindingId(): void
    {
        $this->resetPage();
    }

    public function updatedKeyFindingsRelationType(): void
    {
        $this->resetPage();
    }

    public function updatedKeyFindingsStatus(): void
    {
        $this->resetPage();
    }

    public function resetKeyFindingsFilters(): void
    {
        $this->keyFindingsSearch = '';
        $this->keyFindingsId = '';
        $this->keyFindingsKeyId = '';
        $this->keyFindingsFindingId = '';
        $this->keyFindingsRelationType = 'all';
        $this->keyFindingsStatus = 'all';

        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedKeyValuesSearch(): void
    {
        $this->resetPage();
    }

    public function updatedKeyValuesId(): void
    {
        $this->resetPage();
    }

    public function updatedKeyValuesKeyId(): void
    {
        $this->resetPage();
    }

    public function updatedKeyValuesLocale(): void
    {
        $this->resetPage();
    }

    public function updatedKeyValuesStatus(): void
    {
        $this->resetPage();
    }

    public function updatedKeyValuesSource(): void
    {
        $this->resetPage();
    }

    public function resetKeyValuesFilters(): void
    {
        $this->keyValuesSearch = '';
        $this->keyValuesId = '';
        $this->keyValuesKeyId = '';
        $this->keyValuesLocale = 'all';
        $this->keyValuesStatus = 'all';
        $this->keyValuesSource = 'all';

        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedDynamicKeyValuesSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicKeyValuesId(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicKeyValuesKeyId(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicKeyValuesValueKey(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicKeyValuesLocale(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicKeyValuesStatus(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicKeyValuesSource(): void
    {
        $this->resetPage();
    }

    public function resetDynamicKeyValuesFilters(): void
    {
        $this->dynamicKeyValuesSearch = '';
        $this->dynamicKeyValuesId = '';
        $this->dynamicKeyValuesKeyId = '';
        $this->dynamicKeyValuesValueKey = '';
        $this->dynamicKeyValuesLocale = 'all';
        $this->dynamicKeyValuesStatus = 'all';
        $this->dynamicKeyValuesSource = 'all';

        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedDynamicSourcesSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourcesId(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourcesKeyId(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourcesFindingId(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourcesOptionDiscoveryId(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourcesClassification(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourcesCardinality(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourcesOrigin(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourcesSourceType(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourcesConfidence(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourcesStatus(): void
    {
        $this->resetPage();
    }

    public function resetDynamicSourcesFilters(): void
    {
        $this->dynamicSourcesSearch = '';
        $this->dynamicSourcesId = '';
        $this->dynamicSourcesKeyId = '';
        $this->dynamicSourcesFindingId = '';
        $this->dynamicSourcesOptionDiscoveryId = '';
        $this->dynamicSourcesClassification = 'all';
        $this->dynamicSourcesCardinality = 'all';
        $this->dynamicSourcesOrigin = 'all';
        $this->dynamicSourcesSourceType = 'all';
        $this->dynamicSourcesConfidence = 'all';
        $this->dynamicSourcesStatus = 'all';

        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedDynamicSourceCandidatesSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceCandidatesId(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceCandidatesDynamicSourceId(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceCandidatesKeyId(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceCandidatesFindingId(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceCandidatesCandidateSourceType(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceCandidatesConfidence(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceCandidatesReviewStatus(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceCandidatesStatus(): void
    {
        $this->resetPage();
    }

    public function resetDynamicSourceCandidatesFilters(): void
    {
        $this->dynamicSourceCandidatesSearch = '';
        $this->dynamicSourceCandidatesId = '';
        $this->dynamicSourceCandidatesDynamicSourceId = '';
        $this->dynamicSourceCandidatesKeyId = '';
        $this->dynamicSourceCandidatesFindingId = '';
        $this->dynamicSourceCandidatesCandidateSourceType = 'all';
        $this->dynamicSourceCandidatesConfidence = 'all';
        $this->dynamicSourceCandidatesReviewStatus = 'all';
        $this->dynamicSourceCandidatesStatus = 'all';

        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedDynamicSourceValuesSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceValuesId(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceValuesDynamicSourceId(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceValuesValueKey(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceValuesTranslationKey(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceValuesOrigin(): void
    {
        $this->resetPage();
    }

    public function updatedDynamicSourceValuesStatus(): void
    {
        $this->resetPage();
    }

    public function resetDynamicSourceValuesFilters(): void
    {
        $this->dynamicSourceValuesSearch = '';
        $this->dynamicSourceValuesId = '';
        $this->dynamicSourceValuesDynamicSourceId = '';
        $this->dynamicSourceValuesValueKey = '';
        $this->dynamicSourceValuesTranslationKey = '';
        $this->dynamicSourceValuesOrigin = 'all';
        $this->dynamicSourceValuesStatus = 'all';

        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedLangValuesSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLangValuesId(): void
    {
        $this->resetPage();
    }

    public function updatedLangValuesMainLocale(): void
    {
        $this->langValuesSubLocale = 'all';

        $this->resetPage();
    }

    public function updatedLangValuesSubLocale(): void
    {
        $this->resetPage();
    }

    public function updatedLangValuesNamespace(): void
    {
        $this->resetPage();
    }

    public function updatedLangValuesLangKey(): void
    {
        $this->resetPage();
    }

    public function updatedLangValuesTranslationKey(): void
    {
        $this->resetPage();
    }

    public function updatedLangValuesSourcePath(): void
    {
        $this->resetPage();
    }

    public function updatedLangValuesValueType(): void
    {
        $this->resetPage();
    }

    public function updatedLangValuesStatus(): void
    {
        $this->resetPage();
    }

    public function resetLangValuesFilters(): void
    {
        $this->langValuesSearch = '';
        $this->langValuesId = '';
        $this->langValuesMainLocale = 'all';
        $this->langValuesSubLocale = 'all';
        $this->langValuesNamespace = 'all';
        $this->langValuesLangKey = '';
        $this->langValuesTranslationKey = '';
        $this->langValuesSourcePath = '';
        $this->langValuesValueType = 'all';
        $this->langValuesStatus = 'all';

        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedKeyInventoryNamespace(): void
    {
        $this->keyInventoryGroup = 'all';
        $this->resetPage();
    }

    public function resetKeyInventoryFilters(): void
    {
        $this->keyInventorySearch = '';
        $this->keyInventoryId = '';
        $this->keyInventoryTranslationKey = '';
        $this->keyInventoryNamespace = 'all';
        $this->keyInventoryGroup = 'all';
        $this->keyInventoryKeyType = 'all';
        $this->keyInventoryStatus = 'all';
        $this->keyInventoryIsShared = 'all';
        $this->keyInventoryIsUi = 'all';
        $this->keyInventoryIsDynamic = 'all';
        $this->keyInventoryIsDynamicMulti = 'all';
        $this->keyInventoryHasActiveCodeUsage = 'all';
        $this->keyInventoryHasLangValues = 'all';
        $this->keyInventoryIsOrphanedLangValue = 'all';
        $this->keyInventoryCandidateForLangDelete = 'all';

        $this->resetPage();
        $this->persistUiState();
    }

    public function resetSharedKeyCandidatesFilters(): void
    {
        $this->sharedKeyCandidatesSearch = '';
        $this->sharedKeyCandidatesId = '';
        $this->sharedKeyCandidatesFindingId = '';
        $this->sharedKeyCandidatesKeyId = '';
        $this->sharedKeyCandidatesMatchedKeyId = '';
        $this->sharedKeyCandidatesNormalizedLiteral = '';
        $this->sharedKeyCandidatesCurrentTranslationKey = '';
        $this->sharedKeyCandidatesSuggestedSharedTranslationKey = '';
        $this->sharedKeyCandidatesConfidence = 'all';
        $this->sharedKeyCandidatesStatus = 'all';
        $this->sharedKeyCandidatesMinReviewCount = '';
        $this->sharedKeyCandidatesMinFindingCount = '';

        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedReviewsSearch(): void
    {
        $this->resetPage();
    }

    public function updatedReviewsId(): void
    {
        $this->resetPage();
    }

    public function updatedReviewsKeyId(): void
    {
        $this->resetPage();
    }

    public function updatedReviewsFindingId(): void
    {
        $this->resetPage();
    }

    public function updatedReviewsReviewType(): void
    {
        $this->resetPage();
    }

    public function updatedReviewsDecision(): void
    {
        $this->resetPage();
    }

    public function updatedReviewsReviewedByUserId(): void
    {
        $this->resetPage();
    }

    public function resetReviewsFilters(): void
    {
        $this->reviewsSearch = '';
        $this->reviewsId = '';
        $this->reviewsKeyId = '';
        $this->reviewsFindingId = '';
        $this->reviewsReviewType = 'all';
        $this->reviewsDecision = 'all';
        $this->reviewsReviewedByUserId = '';

        $this->resetPage();
        $this->persistUiState();
    }

    public function updatedTimelineEventsSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTimelineEventsId(): void
    {
        $this->resetPage();
    }

    public function updatedTimelineEventsEventType(): void
    {
        $this->resetPage();
    }

    public function updatedTimelineEventsEventTypeId(): void
    {
        $this->resetPage();
    }

    public function updatedTimelineEventsKeyId(): void
    {
        $this->resetPage();
    }

    public function updatedTimelineEventsFindingId(): void
    {
        $this->resetPage();
    }

    public function updatedTimelineEventsReviewId(): void
    {
        $this->resetPage();
    }

    public function updatedTimelineEventsCreatedByUserId(): void
    {
        $this->resetPage();
    }

    public function updatedTimelineEventsCreatedRange(): void
    {
        $this->resetPage();
    }

    public function updatedTimelineEventsTimeFrom(): void
    {
        $timeFrom = $this->normalizedTimelineEventTime($this->timelineEventsTimeFrom);

        $this->timelineEventsTimeFrom = $timeFrom;

        if ($timeFrom === null) {
            $this->resetPage();

            return;
        }

        $this->resetPage();
    }

    public function updatedTimelineEventsChangedRange(): void
    {
        $this->resetPage();
    }

    public function updatedTimelineEventsChangedTime(): void
    {
        $this->timelineEventsChangedTime = $this->normalizedTimelineEventTime($this->timelineEventsChangedTime);

        $this->resetPage();
    }

    public function updatedTimelineEventsTimeSpan(): void
    {
        $this->timelineEventsTimeSpan = $this->normalizedTimelineEventTimeSpan($this->timelineEventsTimeSpan);

        $this->resetPage();
    }

    public function resetTimelineEventsFilters(): void
    {
        $this->timelineEventsSearch = '';
        $this->timelineEventsId = '';
        $this->timelineEventsEventType = 'all';
        $this->timelineEventsEventTypeId = '';
        $this->timelineEventsKeyId = '';
        $this->timelineEventsFindingId = '';
        $this->timelineEventsReviewId = '';
        $this->timelineEventsCreatedByUserId = '';
        $this->timelineEventsCreatedRange = [
            'start' => null,
            'end' => null,
            'preset' => null,
        ];
        $this->timelineEventsTimeFrom = null;
        $this->timelineEventsChangedRange = [
            'start' => null,
            'end' => null,
            'preset' => null,
        ];
        $this->timelineEventsChangedTime = null;
        $this->timelineEventsTimeSpan = '02:00';

        $this->resetPage();
        $this->persistUiState();
    }

    private function resetKeysSegmentFilters(): void
    {
        $this->keysSegmentDomain = 'all';
        $this->keysSegmentSection = 'all';
        $this->keysSegmentContext = 'all';
        $this->keysSegmentExtra = 'all';
        $this->keysSegmentName = 'all';
    }

    private function uiStateSettingKey(): string
    {
        return (string) config('translation-workbench.raw_data_ui_state.setting_key', 'ui.pages.translation_workbench.raw_data');
    }

    /**
     * @return array<string, mixed>
     */
    private function uiStateDefaults(): array
    {
        $defaults = config('translation-workbench.raw_data_ui_state.defaults', []);
        $fileDefaults = $this->uiStateFileDefaults();

        return [
            ...(is_array($defaults) ? $defaults : []),
            ...$fileDefaults,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function uiStateFileDefaults(): array
    {
        $path = base_path((string) config(
            'translation-workbench.raw_data_ui_state.defaults_file',
            'packages/gunreip/laravel-translation-workbench/resources/ui-state/raw-data-defaults.json',
        ));

        if (! File::exists($path)) {
            return [];
        }

        $decoded = json_decode((string) File::get($path), true);

        return is_array($decoded) ? $decoded : [];
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
        return collect(self::PERSISTED_STATE_PROPERTIES)
            ->filter(fn(string $property): bool => property_exists($this, $property))
            ->mapWithKeys(fn(string $property): array => [$property => $this->{$property}])
            ->all();
    }

    /**
     * Store the most recent non-user-specific Raw-Data UI state as an inspectable file.
     *
     * This mirrors the entries page: user-specific state is stored in the users.settings JSON,
     * while this export file can be reviewed or used as a future package default.
     *
     * @param  array<string, mixed>  $state
     */
    private function persistUiStateFile(array $state): void
    {
        $path = storage_path((string) config(
            'translation-workbench.raw_data_ui_state.export_file',
            'translation-workbench/ui-state/raw-data.json',
        ));

        try {
            File::ensureDirectoryExists(dirname($path));
            File::put($path, json_encode([
                'page' => 'translation-workbench.raw-data',
                'updated_at' => now()->toISOString(),
                'state' => $state,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL);
        } catch (Throwable) {
            // File export is diagnostic only; user-specific DB settings remain authoritative.
        }
    }

    private function normalizedPersistedPropertyValue(string $property, mixed $value): mixed
    {
        if ($property === 'activeTable') {
            return in_array($value, $this->tables, true)
                ? (string) $value
                : $this->activeTable;
        }

        if ($property === 'perPage') {
            return $this->normalizedPerPage($value);
        }

        if ($property === 'sortDirection') {
            return $value === 'asc' ? 'asc' : 'desc';
        }

        if (in_array($property, ['timelineEventsCreatedRange', 'timelineEventsChangedRange'], true)) {
            return is_array($value)
                ? [
                    'start' => $value['start'] ?? null,
                    'end' => $value['end'] ?? null,
                    'preset' => $value['preset'] ?? null,
                ]
                : [
                    'start' => null,
                    'end' => null,
                    'preset' => null,
                ];
        }

        if ($property === 'timelineEventsTimeSpan') {
            return $this->normalizedTimelineEventTimeSpan(is_string($value) ? $value : null);
        }

        if (in_array($property, ['timelineEventsTimeFrom', 'timelineEventsChangedTime'], true)) {
            return $this->normalizedTimelineEventTime(is_string($value) ? $value : null);
        }

        return is_scalar($value) || $value === null
            ? (string) ($value ?? '')
            : $this->{$property};
    }

    public function render(): View
    {
        $table = $this->normalizedActiveTable();
        $columns = $this->displayColumns(
            $table,
            Schema::hasTable($table) ? Schema::getColumnListing($table) : [],
        );

        return view('translation-workbench::livewire.raw-data', [
            'workbenchVersion' => app(TranslationWorkbenchVersion::class)->toArray(),
            'table' => $table,
            'tables' => $this->tables,
            'activeTableIndex' => $this->activeTableIndex(),
            'lastTableIndex' => count($this->tables) - 1,
            'columns' => $columns,
            'columnMetadata' => $this->columnMetadata($table, $columns),
            'columnPresentation' => RawDataColumnPresentation::forTable($table, $columns),
            'foreignKeyMetadata' => $this->foreignKeyMetadata($table),
            'sortableColumns' => $this->sortableColumns($columns),
            'rows' => $this->rows($table, $columns),
            'summary' => $this->summary($table, $columns),
            'tableCounts' => $this->tableCounts(),
            'tableDescriptions' => $this->tableDescriptions(),
            'tableStorageSize' => $this->tableStorageSize($table),
            'sourceFileOptions' => $this->sourceFileOptions($table),
            'sourceFilePathOptions' => $this->sourceFilePathOptions($table),
            'builtSourceFilePath' => $this->builtSourceFilePath(),
            'builtSourceFilePathExists' => $this->sourceFilePathExists($this->builtSourceFilePath()),
            'builtSourcePackagePath' => $this->builtSourcePackagePath(),
            'builtSourcePackagePathExists' => $this->sourcePackagePathExists($this->builtSourcePackagePath()),
            'eventTypeOptions' => $this->eventTypeOptions($table),
            'findingOptions' => $this->findingOptions($table),
            'keyOptions' => $this->keyOptions($table),
            'keyFindingOptions' => $this->keyFindingOptions($table),
            'keyValueOptions' => $this->keyValueOptions($table),
            'dynamicKeyValueOptions' => $this->dynamicKeyValueOptions($table),
            'dynamicSourceOptions' => $this->dynamicSourceOptions($table),
            'dynamicSourceCandidateOptions' => $this->dynamicSourceCandidateOptions($table),
            'dynamicSourceValueOptions' => $this->dynamicSourceValueOptions($table),
            'langValueOptions' => $this->langValueOptions($table),
            'reviewOptions' => $this->reviewOptions($table),
            'sharedKeyCandidateOptions' => $this->sharedKeyCandidateOptions($table),
            'keyInventoryOptions' => $this->keyInventoryOptions($table),
            'timelineEventOptions' => $this->timelineEventOptions($table),
            'timelineEventsTimePickersDisabled' => ! $this->hasTimelineEventsDateRange(),
            'timelineEventsChangedTimePickerDisabled' => ! $this->hasTimelineEventsChangedRange(),
            'timelineEventsCreatedMinDate' => $this->timelineEventsDateBoundary('created_at', 'min'),
            'timelineEventsCreatedMaxDate' => $this->timelineEventsDateBoundary('created_at', 'max'),
            'timelineEventsCreatedMinTime' => $this->timelineEventsTimeBoundary('created_at', $this->timelineEventsCreatedRange, 'min'),
            'timelineEventsCreatedMaxTime' => $this->timelineEventsTimeBoundary('created_at', $this->timelineEventsCreatedRange, 'max'),
            'timelineEventsChangedMinDate' => $this->timelineEventsDateBoundary('updated_at', 'min'),
            'timelineEventsChangedMaxDate' => $this->timelineEventsDateBoundary('updated_at', 'max'),
            'timelineEventsChangedMinTime' => $this->timelineEventsTimeBoundary('updated_at', $this->timelineEventsChangedRange, 'min'),
            'timelineEventsChangedMaxTime' => $this->timelineEventsTimeBoundary('updated_at', $this->timelineEventsChangedRange, 'max'),
            'timelineEventsCreatedResult' => $this->timelineEventsDateTimeResult($this->timelineEventsCreatedRange, $this->timelineEventsTimeFrom),
            'timelineEventsChangedResult' => $this->timelineEventsDateTimeResult($this->timelineEventsChangedRange, $this->timelineEventsChangedTime),
            'timelineEventsTimeSpanResult' => $this->normalizedTimelineEventTimeSpan($this->timelineEventsTimeSpan),
            'builtSuggestedKey' => $this->builtSuggestedKey(),
            'builtTranslationKey' => $this->builtTranslationKey(),
            'builtSuggestedKeyExists' => $this->builtKeyExists('suggested_key', $this->builtSuggestedKey()),
            'builtTranslationKeyExists' => $this->builtKeyExists('translation_key', $this->builtTranslationKey()),
            'builtKeySegments' => $this->builtKeySegments(),
            'rawDataSourceFileLookup' => $this->rawDataSourceFileLookup($table),
            'rawDataKeyLookup' => $this->rawDataKeyLookup($table),
            'rawDataFindingLookup' => $this->rawDataFindingLookup($table),
            'timelineChainMainRow' => $this->timelineChainMainRow($table),
            'timelineChainRootRows' => $this->timelineChainRootRows($table),
            'timelineChainOriginRows' => $this->timelineChainOriginRows($table),
            'timelineChainSampleRows' => $this->timelineChainSampleRows($table),
            'timelineChainPreviewOptions' => $this->timelineChainPreviewOptions($table),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function tableDescriptions(): array
    {
        return [
            'translation_workbench_source_files' => __('Source files scanned by the workbench, including normalized path segments and file-level metadata.'),
            'translation_workbench_event_types' => __('Timeline event definitions used to classify and present workbench history entries consistently.'),
            'translation_workbench_findings' => __('Raw translation-capable code findings detected by scanner runs, including literals, keys, source locations and lifecycle state.'),
            'translation_workbench_keys' => __('Reviewed or candidate translation keys with namespace, group, segments, classification flags and review state.'),
            'translation_workbench_key_findings' => __('Relation table linking translation keys to the code findings where they were detected or reviewed.'),
            'translation_workbench_key_values' => __('Workbench-managed translation values for normal translation keys before they are exported to lang files.'),
            'translation_workbench_dynamic_key_values' => __('Workbench-managed translation values for dynamic or dynamic-multi keys and their option values.'),
            'translation_workbench_dynamic_sources' => __('Detected or runtime-captured dynamic value sources, including source context and classification details.'),
            'translation_workbench_dynamic_source_candidates' => __('Candidate links between dynamic sources, findings and keys that still need or document review decisions.'),
            'translation_workbench_dynamic_source_values' => __('Runtime option values captured for dynamic sources, including origin and status.'),
            'translation_workbench_lang_values' => __('Current imported or exported lang-file values across source, target and sub locales.'),
            'translation_workbench_reviews' => __('Review decisions made in the UI, including key edits, classification choices and bulk/shared-key decisions.'),
            'translation_workbench_shared_key_candidates' => __('Potential follow-up candidates for already shared literal keys, kept separate from normal findings workflow decisions.'),
            'translation_workbench_key_inventory' => __('Aggregated inventory of established translation keys, code usage, lang values, dynamic values and possible lang-file cleanup candidates.'),
            'translation_workbench_timeline_chains' => __('Aggregated translation-chain snapshots that collect related keys, findings, reviews, lang values and timeline events for future extended timeline views.'),
            'translation_workbench_pipeline_runs' => __('Pipeline run headers for UI-started Translation Workbench orchestrator runs, including status, current step, options and summary state.'),
            'translation_workbench_pipeline_run_steps' => __('Per-step progress rows for Translation Workbench pipeline runs, including command, arguments, status, duration and error details.'),
            'translation_workbench_timeline_events' => __('Detailed audit trail of scanner, command and UI changes used to build the workbench timeline.'),
        ];
    }

    private function builtSuggestedKey(): string
    {
        if (! in_array($this->keysSuggestedKey, ['', 'all'], true)) {
            return $this->keysSuggestedKey;
        }

        return $this->buildKeyFromSelectedSegments();
    }

    private function builtTranslationKey(): string
    {
        if (! in_array($this->keysTranslationKey, ['', 'all'], true)) {
            return $this->keysTranslationKey;
        }

        return $this->buildKeyFromSelectedSegments();
    }

    private function buildKeyFromSelectedSegments(): string
    {
        return collect($this->builtKeySegments())
            ->pluck('value')
            ->filter(static fn(?string $value): bool => filled($value))
            ->implode('.');
    }

    private function builtKeyExists(string $column, string $key): bool
    {
        if ($key === '' || ! Schema::hasTable('translation_workbench_keys') || ! Schema::hasColumn('translation_workbench_keys', $column)) {
            return false;
        }

        return DB::table('translation_workbench_keys')
            ->where($column, $key)
            ->exists();
    }

    /**
     * @return array<int, array{label: string, value: string|null, selected: bool}>
     */
    private function builtKeySegments(): array
    {
        return collect([
            __('Namespace') => $this->keysNamespace,
            __('Group') => $this->keysGroup,
            __('Domain') => $this->keysSegmentDomain,
            __('Section') => $this->keysSegmentSection,
            __('Context') => $this->keysSegmentContext,
            __('Extra') => $this->keysSegmentExtra,
            __('Name') => $this->keysSegmentName,
        ])
            ->map(static function (string $value, string $label): array {
                $selected = ! in_array($value, ['', 'all'], true);

                return [
                    'label' => $label,
                    'value' => $selected ? $value : null,
                    'selected' => $selected,
                ];
            })
            ->values()
            ->all();
    }

    private function builtSourceFilePath(): string
    {
        if (! in_array($this->sourceFilesPath, ['', 'all'], true)) {
            return $this->sourceFilesPath;
        }

        $segments = $this->selectedSourcePathSegments(includeFilename: true);

        if ($segments === []) {
            return '';
        }

        $path = implode('/', $segments);
        $extension = $this->sourceFileExtensionSuffix();

        return $extension !== '' ? $path . $extension : $path;
    }

    private function builtSourcePackagePath(): string
    {
        if ($this->sourceFilesRoot !== 'packages') {
            return '';
        }

        return collect([
            $this->sourceFilesRoot,
            $this->sourceFilesPackageVendor,
            $this->sourceFilesPackageName,
        ])
            ->reject(static fn(string $value): bool => in_array($value, ['', 'all'], true))
            ->implode('/');
    }

    /**
     * @return array<int, string>
     */
    private function selectedSourcePathSegments(bool $includeFilename): array
    {
        $segments = collect([$this->sourceFilesRoot])
            ->reject(static fn(string $value): bool => in_array($value, ['', 'all'], true))
            ->values();

        if ($this->sourceFilesRoot === 'packages') {
            $segments = $segments
                ->merge([$this->sourceFilesPackageVendor, $this->sourceFilesPackageName])
                ->reject(static fn(string $value): bool => in_array($value, ['', 'all'], true))
                ->values();
        }

        foreach ([
            $this->sourceFilesArea,
            $this->sourceFilesDomain,
            $this->sourceFilesSection,
            $this->sourceFilesContext,
            $this->sourceFilesScope,
            $this->sourceFilesExtra,
        ] as $value) {
            if (! in_array($value, ['', 'all'], true)) {
                $segments = $segments->merge(explode('.', $value));
            }
        }

        if ($includeFilename && ! in_array($this->sourceFilesFilename, ['', 'all'], true)) {
            $segments->push($this->sourceFilesFilename);
        }

        return $segments
            ->filter(static fn(string $value): bool => $value !== '')
            ->values()
            ->all();
    }

    private function sourceFileExtensionSuffix(): string
    {
        if (in_array($this->sourceFilesExtension, ['', 'all'], true) || $this->sourceFilesFilename === 'all') {
            return '';
        }

        if ($this->sourceFilesSourceType === 'blade' && $this->sourceFilesExtension === 'php') {
            return '.blade.php';
        }

        return '.' . ltrim($this->sourceFilesExtension, '.');
    }

    private function sourceFilePathExists(string $path): bool
    {
        if ($path === '' || ! Schema::hasTable('translation_workbench_source_files')) {
            return false;
        }

        return DB::table('translation_workbench_source_files')
            ->where('path', $path)
            ->exists();
    }

    private function sourcePackagePathExists(string $path): bool
    {
        if ($path === '' || ! Schema::hasTable('translation_workbench_source_files')) {
            return false;
        }

        return DB::table('translation_workbench_source_files')
            ->where('path', 'like', $this->likeFilterValue(rtrim($path, '/') . '/'))
            ->exists();
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<int, string>
     */
    private function displayColumns(string $table, array $columns): array
    {
        if ($table === 'translation_workbench_duplicate_candidates') {
            $columns = $this->moveColumnsAfter($columns, 'id', ['entry_id']);
            $columns = $this->moveColumnsAfter($columns, 'entry_id', ['duplicate_type', 'confidence', 'group_size']);

            return $this->moveColumnsAfter($columns, 'group_size', ['group_fingerprint', 'matched_entry_ids']);
        }

        if ($table === 'translation_workbench_timeline_chains') {
            return array_values(array_intersect([
                'id',
                'translation_key',
                'namespace',
                'group',
                'chain_type',
                'chain_status',
                'root_key_id',
                'root_finding_id',
                'key_count',
                'finding_count',
                'active_finding_count',
                'obsolete_finding_count',
                'commented_out_finding_count',
                'review_count',
                'timeline_event_count',
                'lang_value_count',
                'shared_candidate_count',
                'bulk_review_count',
                'first_seen_at',
                'last_seen_at',
                'scan_count',
                'created_at',
                'updated_at',
            ], $columns));
        }

        if ($table !== 'translation_workbench_entries') {
            return $columns;
        }

        $columns = $this->moveColumnsAfter($columns, 'id', ['previous_entry_id', 'replaced_by_entry_id']);
        $columns = $this->moveColumnsAfter($columns, 'source_signature', ['source_fingerprint', 'expression_fingerprint', 'semantic_fingerprint']);
        $columns = $this->moveColumnsAfter($columns, 'kind', ['entry_type', 'candidate_type', 'is_ui_key', 'is_dynamic_key', 'is_dynamic_multi', 'candidate_reason']);
        $columns = $this->moveColumnsAfter($columns, 'source_type', ['target_type']);
        $columns = $this->moveColumnsAfter($columns, 'literal_text', ['literal_text_suggested']);

        return $this->moveColumnsAfter($columns, 'translation_key', ['translation_key_source', 'deleted_segments', 'existing_key', 'suggested_key']);
    }

    /**
     * @param  array<int, string>  $columns
     * @param  array<int, string>  $movingColumns
     * @return array<int, string>
     */
    private function moveColumnsAfter(array $columns, string $anchorColumn, array $movingColumns): array
    {
        if (! in_array($anchorColumn, $columns, true)) {
            return $columns;
        }

        $movingColumns = array_values(array_filter(
            $movingColumns,
            static fn(string $column): bool => in_array($column, $columns, true),
        ));

        if ($movingColumns === []) {
            return $columns;
        }

        $orderedColumns = [];

        foreach ($columns as $column) {
            if (in_array($column, $movingColumns, true)) {
                continue;
            }

            $orderedColumns[] = $column;

            if ($column === $anchorColumn) {
                array_push($orderedColumns, ...$movingColumns);
            }
        }

        return $orderedColumns;
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function rows(string $table, array $columns): LengthAwarePaginator
    {
        if ($columns === []) {
            return new LengthAwarePaginator([], 0, $this->normalizedPerPage());
        }

        $query = DB::table($table)->select($columns);

        $this->applyTableFilters($query, $table, $columns);

        if ($this->isSortableColumn($this->sortField, $columns)) {
            $query->orderBy($this->sortField, $this->sortDirection === 'asc' ? 'asc' : 'desc');
        } elseif (in_array('id', $columns, true)) {
            $query->orderByDesc('id');
        }

        return $query->paginate($this->normalizedPerPage());
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyTableFilters($query, string $table, array $columns): void
    {
        if ($table === 'translation_workbench_source_files') {
            $this->applySourceFilesFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_event_types') {
            $this->applyEventTypesFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_findings') {
            $this->applyFindingsFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_keys') {
            $this->applyKeysFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_key_findings') {
            $this->applyKeyFindingsFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_key_values') {
            $this->applyKeyValuesFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_dynamic_key_values') {
            $this->applyDynamicKeyValuesFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_dynamic_sources') {
            $this->applyDynamicSourcesFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_dynamic_source_candidates') {
            $this->applyDynamicSourceCandidatesFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_dynamic_source_values') {
            $this->applyDynamicSourceValuesFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_lang_values') {
            $this->applyLangValuesFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_reviews') {
            $this->applyReviewsFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_shared_key_candidates') {
            $this->applySharedKeyCandidatesFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_key_inventory') {
            $this->applyKeyInventoryFilters($query, $columns);

            return;
        }

        if ($table === 'translation_workbench_timeline_events') {
            $this->applyTimelineEventsFilters($query, $columns);
        }
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applySourceFilesFilters($query, array $columns): void
    {
        if (in_array('id', $columns, true) && trim($this->sourceFilesId) !== '') {
            $query->where('id', (int) $this->sourceFilesId);
        }

        if (in_array('path', $columns, true) && ! in_array($this->sourceFilesPath, ['', 'all'], true)) {
            $query->where('path', $this->sourceFilesPath);
        }

        foreach ([
            'source_root' => $this->sourceFilesRoot,
            'source_area' => $this->sourceFilesArea,
            'package_vendor' => $this->sourceFilesPackageVendor,
            'package_name' => $this->sourceFilesPackageName,
            'path_domain' => $this->sourceFilesDomain,
            'path_section' => $this->sourceFilesSection,
            'path_context' => $this->sourceFilesContext,
            'path_scope' => $this->sourceFilesScope,
            'path_extra' => $this->sourceFilesExtra,
            'filename' => $this->sourceFilesFilename,
            'source_type' => $this->sourceFilesSourceType,
            'extension' => $this->sourceFilesExtension,
            'status' => $this->sourceFilesStatus,
        ] as $column => $value) {
            if (in_array($column, $columns, true) && ! in_array($value, ['', 'all'], true)) {
                $query->where($column, $value);
            }
        }

        $search = trim($this->sourceFilesSearch);

        if ($search !== '') {
            $query->where(function ($query) use ($columns, $search): void {
                if (in_array('path', $columns, true)) {
                    $query->orWhere('path', 'like', $this->likeFilterValue($search));
                }

                foreach (['source_type', 'extension', 'status'] as $column) {
                    if (in_array($column, $columns, true)) {
                        $query->orWhere($column, 'like', $this->likeFilterValue($search));
                    }
                }

                foreach ([
                    'source_root',
                    'source_area',
                    'package_vendor',
                    'package_name',
                    'path_domain',
                    'path_section',
                    'path_context',
                    'path_scope',
                    'path_extra',
                    'filename',
                ] as $column) {
                    if (in_array($column, $columns, true)) {
                        $query->orWhere($column, 'like', $this->likeFilterValue($search));
                    }
                }
            });
        }
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyEventTypesFilters($query, array $columns): void
    {
        if (in_array('id', $columns, true) && trim($this->eventTypesId) !== '') {
            $query->where('id', (int) $this->eventTypesId);
        }

        foreach ([
            'key' => $this->eventTypesKey,
            'label' => $this->eventTypesLabel,
            'category' => $this->eventTypesCategory,
            'severity' => $this->eventTypesSeverity,
        ] as $column => $value) {
            if (in_array($column, $columns, true) && ! in_array($value, ['', 'all'], true)) {
                $query->where($column, $value);
            }
        }

        $search = trim($this->eventTypesSearch);

        if ($search !== '') {
            $query->where(function ($query) use ($columns, $search): void {
                foreach (['key', 'label', 'description', 'category', 'severity'] as $column) {
                    if (in_array($column, $columns, true)) {
                        $query->orWhere($column, 'like', $this->likeFilterValue($search));
                    }
                }
            });
        }
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyFindingsFilters($query, array $columns): void
    {
        if (in_array('id', $columns, true) && trim($this->findingsId) !== '') {
            $query->where('id', (int) $this->findingsId);
        }

        if (in_array('source_file_id', $columns, true) && trim($this->findingsSourceFileId) !== '' && $this->findingsSourceFileId !== 'all') {
            $query->where('source_file_id', (int) $this->findingsSourceFileId);
        }

        if (in_array('source_line', $columns, true) && trim($this->findingsSourceLine) !== '') {
            $query->where('source_line', (int) $this->findingsSourceLine);
        }

        foreach ([
            'kind' => $this->findingsKind,
            'function_name' => $this->findingsFunctionName,
            'entry_type' => $this->findingsEntryType,
            'candidate_type' => $this->findingsCandidateType,
            'status' => $this->findingsStatus,
            'namespace' => $this->findingsNamespace,
            'group' => $this->findingsGroup,
            'scope' => $this->findingsScope,
            'dynamic_scope' => $this->findingsDynamicScope,
        ] as $column => $value) {
            if (in_array($column, $columns, true) && ! in_array($value, ['', 'all'], true)) {
                $query->where($column, $value);
            }
        }

        $search = trim($this->findingsSearch);

        if ($search !== '') {
            $query->where(function ($query) use ($columns, $search): void {
                if (in_array('source_line', $columns, true) && ctype_digit($search)) {
                    $query->orWhere('source_line', (int) $search);
                }

                foreach ([
                    'fingerprint',
                    'source_signature',
                    'source_fingerprint',
                    'expression_fingerprint',
                    'semantic_fingerprint',
                    'kind',
                    'function_name',
                    'raw_expression',
                    'literal_text',
                    'literal_text_suggested',
                    'found_translation_key',
                    'existing_key',
                    'suggested_key',
                    'namespace',
                    'group',
                    'path_key',
                    'scope',
                    'dynamic_scope',
                    'entry_type',
                    'candidate_type',
                    'candidate_reason',
                    'status',
                ] as $column) {
                    if (in_array($column, $columns, true)) {
                        $query->orWhere($column, 'like', $this->likeFilterValue($search));
                    }
                }
            });
        }
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyKeysFilters($query, array $columns): void
    {
        if (in_array('id', $columns, true) && trim($this->keysId) !== '') {
            $query->where('id', (int) $this->keysId);
        }

        foreach ([
            'translation_key' => $this->keysTranslationKey,
            'suggested_key' => $this->keysSuggestedKey,
            'namespace' => $this->keysNamespace,
            'group' => $this->keysGroup,
            'scope' => $this->keysScope,
            'key_segment_domain' => $this->keysSegmentDomain,
            'key_segment_section' => $this->keysSegmentSection,
            'key_segment_context' => $this->keysSegmentContext,
            'key_segment_extra' => $this->keysSegmentExtra,
            'key_segment_name' => $this->keysSegmentName,
            'key_type' => $this->keysKeyType,
            'status' => $this->keysStatus,
            'review_status' => $this->keysReviewStatus,
        ] as $column => $value) {
            if (in_array($column, $columns, true) && ! in_array($value, ['', 'all'], true)) {
                $query->where($column, $value);
            }
        }

        foreach ([
            'is_ui_key' => $this->keysIsUiKey,
            'is_dynamic_key' => $this->keysIsDynamicKey,
            'is_dynamic_multi' => $this->keysIsDynamicMulti,
        ] as $column => $value) {
            if (! in_array($column, $columns, true) || ! in_array($value, ['yes', 'no'], true)) {
                continue;
            }

            $query->where($column, $value === 'yes');
        }

        $search = trim($this->keysSearch);

        if ($search !== '') {
            $query->where(function ($query) use ($columns, $search): void {
                foreach ([
                    'fingerprint',
                    'translation_key',
                    'suggested_key',
                    'namespace',
                    'group',
                    'path_key',
                    'scope',
                    'key_segment_domain',
                    'key_segment_section',
                    'key_segment_context',
                    'key_segment_extra',
                    'key_segment_name',
                    'key_type',
                    'status',
                    'review_status',
                ] as $column) {
                    if (in_array($column, $columns, true)) {
                        $query->orWhere($column, 'like', $this->likeFilterValue($search));
                    }
                }
            });
        }
    }

    private function likeFilterValue(string $value): string
    {
        return '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($value)) . '%';
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyKeyFindingsFilters($query, array $columns): void
    {
        if (in_array('id', $columns, true) && trim($this->keyFindingsId) !== '') {
            $query->where('id', (int) $this->keyFindingsId);
        }

        if (in_array('key_id', $columns, true) && trim($this->keyFindingsKeyId) !== '') {
            $query->where('key_id', (int) $this->keyFindingsKeyId);
        }

        if (in_array('finding_id', $columns, true) && trim($this->keyFindingsFindingId) !== '') {
            $query->where('finding_id', (int) $this->keyFindingsFindingId);
        }

        foreach ([
            'relation_type' => $this->keyFindingsRelationType,
            'status' => $this->keyFindingsStatus,
        ] as $column => $value) {
            if (in_array($column, $columns, true) && ! in_array($value, ['', 'all'], true)) {
                $query->where($column, $value);
            }
        }

        $search = trim($this->keyFindingsSearch);

        if ($search !== '') {
            $query->where(function ($query) use ($columns, $search): void {
                foreach (['relation_type', 'status'] as $column) {
                    if (in_array($column, $columns, true)) {
                        $query->orWhere($column, 'like', $this->likeFilterValue($search));
                    }
                }
            });
        }
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyKeyValuesFilters($query, array $columns): void
    {
        $this->applyExactNumericFilters($query, $columns, [
            'id' => $this->keyValuesId,
            'key_id' => $this->keyValuesKeyId,
        ]);

        $this->applyExactSelectFilters($query, $columns, [
            'locale' => $this->keyValuesLocale,
            'status' => $this->keyValuesStatus,
            'source' => $this->keyValuesSource,
        ]);

        $this->applyTextSearchFilter($query, $columns, $this->keyValuesSearch, [], [
            'locale',
            'value',
            'status',
            'source',
            'meta',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyDynamicKeyValuesFilters($query, array $columns): void
    {
        $this->applyExactNumericFilters($query, $columns, [
            'id' => $this->dynamicKeyValuesId,
            'key_id' => $this->dynamicKeyValuesKeyId,
        ]);

        $this->applyExactSelectFilters($query, $columns, [
            'locale' => $this->dynamicKeyValuesLocale,
            'status' => $this->dynamicKeyValuesStatus,
            'source' => $this->dynamicKeyValuesSource,
        ]);

        $this->applyLikeFilters($query, $columns, [
            'value_key' => $this->dynamicKeyValuesValueKey,
        ]);

        $this->applyTextSearchFilter($query, $columns, $this->dynamicKeyValuesSearch, [], [
            'value_key',
            'locale',
            'value',
            'native_label',
            'status',
            'source',
            'meta',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyDynamicSourcesFilters($query, array $columns): void
    {
        $this->applyExactNumericFilters($query, $columns, [
            'id' => $this->dynamicSourcesId,
            'key_id' => $this->dynamicSourcesKeyId,
            'finding_id' => $this->dynamicSourcesFindingId,
            'option_discovery_id' => $this->dynamicSourcesOptionDiscoveryId,
        ]);

        $this->applyExactSelectFilters($query, $columns, [
            'classification' => $this->dynamicSourcesClassification,
            'cardinality' => $this->dynamicSourcesCardinality,
            'origin' => $this->dynamicSourcesOrigin,
            'source_type' => $this->dynamicSourcesSourceType,
            'confidence' => $this->dynamicSourcesConfidence,
            'status' => $this->dynamicSourcesStatus,
        ]);

        $this->applyTextSearchFilter($query, $columns, $this->dynamicSourcesSearch, [
            'source_line',
            'values_count',
        ], [
            'fingerprint',
            'classification',
            'cardinality',
            'origin',
            'source_type',
            'source_reference',
            'source_path',
            'source_expression',
            'dynamic_scope',
            'confidence',
            'status',
            'meta',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyDynamicSourceCandidatesFilters($query, array $columns): void
    {
        $this->applyExactNumericFilters($query, $columns, [
            'id' => $this->dynamicSourceCandidatesId,
            'dynamic_source_id' => $this->dynamicSourceCandidatesDynamicSourceId,
            'key_id' => $this->dynamicSourceCandidatesKeyId,
            'finding_id' => $this->dynamicSourceCandidatesFindingId,
        ]);

        $this->applyExactSelectFilters($query, $columns, [
            'candidate_source_type' => $this->dynamicSourceCandidatesCandidateSourceType,
            'confidence' => $this->dynamicSourceCandidatesConfidence,
            'review_status' => $this->dynamicSourceCandidatesReviewStatus,
            'status' => $this->dynamicSourceCandidatesStatus,
        ]);

        $this->applyTextSearchFilter($query, $columns, $this->dynamicSourceCandidatesSearch, [
            'candidate_values_count',
        ], [
            'suggested_key',
            'dynamic_scope',
            'source_expression',
            'candidate_source_type',
            'candidate_reference',
            'candidate_values',
            'confidence',
            'review_status',
            'status',
            'meta',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyDynamicSourceValuesFilters($query, array $columns): void
    {
        $this->applyExactNumericFilters($query, $columns, [
            'id' => $this->dynamicSourceValuesId,
            'dynamic_source_id' => $this->dynamicSourceValuesDynamicSourceId,
        ]);

        $this->applyExactSelectFilters($query, $columns, [
            'origin' => $this->dynamicSourceValuesOrigin,
            'status' => $this->dynamicSourceValuesStatus,
        ]);

        $this->applyLikeFilters($query, $columns, [
            'value_key' => $this->dynamicSourceValuesValueKey,
            'translation_key' => $this->dynamicSourceValuesTranslationKey,
        ]);

        $this->applyTextSearchFilter($query, $columns, $this->dynamicSourceValuesSearch, [], [
            'value_key',
            'native_label',
            'origin',
            'translation_key',
            'status',
            'meta',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * @param  array<int, string>  $columns
     * @param  array<string, string>  $filters
     */
    private function applyExactNumericFilters($query, array $columns, array $filters): void
    {
        foreach ($filters as $column => $value) {
            if (in_array($column, $columns, true) && trim($value) !== '') {
                $query->where($column, (int) $value);
            }
        }
    }

    /**
     * @param  array<int, string>  $columns
     * @param  array<string, string>  $filters
     */
    private function applyExactSelectFilters($query, array $columns, array $filters): void
    {
        foreach ($filters as $column => $value) {
            if (in_array($column, $columns, true) && ! in_array($value, ['', 'all'], true)) {
                $query->where($column, $value);
            }
        }
    }

    /**
     * @param  array<int, string>  $columns
     * @param  array<string, string>  $filters
     */
    private function applyLikeFilters($query, array $columns, array $filters): void
    {
        foreach ($filters as $column => $value) {
            if (in_array($column, $columns, true) && trim($value) !== '') {
                $query->where($column, 'like', $this->likeFilterValue($value));
            }
        }
    }

    /**
     * @param  array<int, string>  $columns
     * @param  array<int, string>  $numericColumns
     * @param  array<int, string>  $textColumns
     */
    private function applyTextSearchFilter($query, array $columns, string $search, array $numericColumns, array $textColumns): void
    {
        $search = trim($search);

        if ($search === '') {
            return;
        }

        $query->where(function ($query) use ($columns, $search, $numericColumns, $textColumns): void {
            if (ctype_digit($search)) {
                foreach ($numericColumns as $column) {
                    if (in_array($column, $columns, true)) {
                        $query->orWhere($column, (int) $search);
                    }
                }
            }

            foreach ($textColumns as $column) {
                if (in_array($column, $columns, true)) {
                    $query->orWhere($column, 'like', $this->likeFilterValue($search));
                }
            }
        });
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyLangValuesFilters($query, array $columns): void
    {
        if (in_array('id', $columns, true) && trim($this->langValuesId) !== '') {
            $query->where('id', (int) $this->langValuesId);
        }

        if (in_array('locale', $columns, true)) {
            if (! in_array($this->langValuesSubLocale, ['', 'all'], true)) {
                $query->where('locale', $this->langValuesSubLocale);
            } elseif (! in_array($this->langValuesMainLocale, ['', 'all'], true)) {
                $mainLocale = $this->langValuesMainLocale;

                $query->where(function ($query) use ($mainLocale): void {
                    $query
                        ->where('locale', $mainLocale)
                        ->orWhere('locale', 'like', $this->likeFilterValue($mainLocale . '-'))
                        ->orWhere('locale', 'like', $this->likeFilterValue($mainLocale . '_'));
                });
            }
        }

        foreach ([
            'namespace' => $this->langValuesNamespace,
            'value_type' => $this->langValuesValueType,
            'status' => $this->langValuesStatus,
        ] as $column => $value) {
            if (in_array($column, $columns, true) && ! in_array($value, ['', 'all'], true)) {
                $query->where($column, $value);
            }
        }

        foreach ([
            'lang_key' => $this->langValuesLangKey,
            'translation_key' => $this->langValuesTranslationKey,
            'source_path' => $this->langValuesSourcePath,
        ] as $column => $value) {
            if (in_array($column, $columns, true) && trim($value) !== '') {
                $query->where($column, 'like', $this->likeFilterValue($value));
            }
        }

        $search = trim($this->langValuesSearch);

        if ($search !== '') {
            $query->where(function ($query) use ($columns, $search): void {
                foreach ([
                    'locale',
                    'namespace',
                    'lang_key',
                    'translation_key',
                    'value',
                    'value_type',
                    'source_path',
                    'source_hash',
                    'status',
                ] as $column) {
                    if (in_array($column, $columns, true)) {
                        $query->orWhere($column, 'like', $this->likeFilterValue($search));
                    }
                }
            });
        }
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyReviewsFilters($query, array $columns): void
    {
        $this->applyExactNumericFilters($query, $columns, [
            'id' => $this->reviewsId,
            'key_id' => $this->reviewsKeyId,
            'finding_id' => $this->reviewsFindingId,
            'reviewed_by_user_id' => $this->reviewsReviewedByUserId,
        ]);

        $this->applyExactSelectFilters($query, $columns, [
            'review_type' => $this->reviewsReviewType,
            'decision' => $this->reviewsDecision,
        ]);

        $this->applyTextSearchFilter($query, $columns, $this->reviewsSearch, [], [
            'review_type',
            'decision',
            'old_values',
            'new_values',
            'meta',
            'reviewed_at',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyKeyInventoryFilters($query, array $columns): void
    {
        $this->applyExactNumericFilters($query, $columns, [
            'id' => $this->keyInventoryId,
        ]);

        $this->applyExactSelectFilters($query, $columns, [
            'namespace' => $this->keyInventoryNamespace,
            'group' => $this->keyInventoryGroup,
            'key_type' => $this->keyInventoryKeyType,
            'inventory_status' => $this->keyInventoryStatus,
        ]);

        $translationKey = trim($this->keyInventoryTranslationKey);

        if ($translationKey !== '') {
            $query->where(function ($query) use ($columns, $translationKey): void {
                foreach (['translation_key', 'normalized_translation_key'] as $column) {
                    if (in_array($column, $columns, true)) {
                        $query->orWhere($column, 'like', $this->likeFilterValue($translationKey));
                    }
                }
            });
        }

        foreach ([
            'is_shared' => $this->keyInventoryIsShared,
            'is_ui' => $this->keyInventoryIsUi,
            'is_dynamic' => $this->keyInventoryIsDynamic,
            'is_dynamic_multi' => $this->keyInventoryIsDynamicMulti,
            'has_active_code_usage' => $this->keyInventoryHasActiveCodeUsage,
            'has_lang_values' => $this->keyInventoryHasLangValues,
            'is_orphaned_lang_value' => $this->keyInventoryIsOrphanedLangValue,
            'candidate_for_lang_delete' => $this->keyInventoryCandidateForLangDelete,
        ] as $column => $value) {
            if (! in_array($column, $columns, true) || ! in_array($value, ['yes', 'no'], true)) {
                continue;
            }

            $query->where($column, $value === 'yes');
        }

        $this->applyTextSearchFilter($query, $columns, $this->keyInventorySearch, ['id'], [
            'translation_key',
            'normalized_translation_key',
            'namespace',
            'group',
            'key_type',
            'inventory_status',
            'meta',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applySharedKeyCandidatesFilters($query, array $columns): void
    {
        $this->applyExactNumericFilters($query, $columns, [
            'id' => $this->sharedKeyCandidatesId,
            'finding_id' => $this->sharedKeyCandidatesFindingId,
            'key_id' => $this->sharedKeyCandidatesKeyId,
            'matched_key_id' => $this->sharedKeyCandidatesMatchedKeyId,
        ]);

        $this->applyExactSelectFilters($query, $columns, [
            'confidence' => $this->sharedKeyCandidatesConfidence,
            'status' => $this->sharedKeyCandidatesStatus,
        ]);

        $literal = trim($this->sharedKeyCandidatesNormalizedLiteral);

        if ($literal !== '') {
            $query->where(function ($query) use ($columns, $literal): void {
                foreach (['normalized_literal', 'literal_text'] as $column) {
                    if (in_array($column, $columns, true)) {
                        $query->orWhere($column, 'like', $this->likeFilterValue($literal));
                    }
                }
            });
        }

        $this->applyLikeFilters($query, $columns, [
            'current_translation_key' => $this->sharedKeyCandidatesCurrentTranslationKey,
            'suggested_shared_translation_key' => $this->sharedKeyCandidatesSuggestedSharedTranslationKey,
        ]);

        foreach ([
            'matched_review_count' => $this->sharedKeyCandidatesMinReviewCount,
            'matched_finding_count' => $this->sharedKeyCandidatesMinFindingCount,
        ] as $column => $value) {
            if (in_array($column, $columns, true) && trim($value) !== '') {
                $query->where($column, '>=', max(0, (int) $value));
            }
        }

        $this->applyTextSearchFilter($query, $columns, $this->sharedKeyCandidatesSearch, [
            'id',
            'finding_id',
            'key_id',
            'matched_key_id',
        ], [
            'normalized_literal',
            'literal_text',
            'current_translation_key',
            'suggested_shared_translation_key',
            'confidence',
            'status',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function applyTimelineEventsFilters($query, array $columns): void
    {
        foreach ([
            'id' => $this->timelineEventsId,
            'event_type_id' => $this->timelineEventsEventTypeId,
            'key_id' => $this->timelineEventsKeyId,
            'finding_id' => $this->timelineEventsFindingId,
            'review_id' => $this->timelineEventsReviewId,
            'created_by_user_id' => $this->timelineEventsCreatedByUserId,
        ] as $column => $value) {
            if (in_array($column, $columns, true) && trim($value) !== '') {
                $query->where($column, (int) $value);
            }
        }

        if (in_array('event_type', $columns, true) && ! in_array($this->timelineEventsEventType, ['', 'all'], true)) {
            $query->where('event_type', $this->timelineEventsEventType);
        }

        if (in_array('created_at', $columns, true) && ($createdFrom = $this->timelineEventsDateTimeBoundary('from')) !== null) {
            $query->where('created_at', '>=', $createdFrom);
        }

        if (in_array('created_at', $columns, true) && ($createdTo = $this->timelineEventsDateTimeBoundary('to')) !== null) {
            $query->where('created_at', '<=', $createdTo);
        }

        if (in_array('updated_at', $columns, true) && ($changedFrom = $this->timelineEventsChangedDateTimeBoundary('from')) !== null) {
            $query->where('updated_at', '>=', $changedFrom);
        }

        if (in_array('updated_at', $columns, true) && ($changedTo = $this->timelineEventsChangedDateTimeBoundary('to')) !== null) {
            $query->where('updated_at', '<=', $changedTo);
        }

        $search = trim($this->timelineEventsSearch);

        if ($search !== '') {
            $query->where(function ($query) use ($columns, $search): void {
                foreach ([
                    'event_type',
                    'old_values',
                    'new_values',
                    'context',
                    'created_at',
                    'updated_at',
                ] as $column) {
                    if (in_array($column, $columns, true)) {
                        $query->orWhere($column, 'like', $this->likeFilterValue($search));
                    }
                }
            });
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function sourceFileOptions(string $table): array
    {
        if ($table !== 'translation_workbench_source_files' || ! Schema::hasTable('translation_workbench_source_files')) {
            return [
                'roots' => [],
                'areas' => [],
                'package_vendors' => [],
                'package_names' => [],
                'domains' => [],
                'sections' => [],
                'contexts' => [],
                'scopes' => [],
                'extras' => [],
                'filenames' => [],
                'source_types' => [],
                'extensions' => [],
                'statuses' => [],
            ];
        }

        return [
            'roots' => $this->sourceFileScopedDistinctOptions('source_root'),
            'areas' => $this->sourceFileScopedDistinctOptions('source_area'),
            'package_vendors' => $this->sourceFileScopedDistinctOptions('package_vendor'),
            'package_names' => $this->sourceFileScopedDistinctOptions('package_name'),
            'domains' => $this->sourceFileScopedDistinctOptions('path_domain'),
            'sections' => $this->sourceFileScopedDistinctOptions('path_section'),
            'contexts' => $this->sourceFileScopedDistinctOptions('path_context'),
            'scopes' => $this->sourceFileScopedDistinctOptions('path_scope'),
            'extras' => $this->sourceFileScopedDistinctOptions('path_extra'),
            'filenames' => $this->sourceFileScopedDistinctOptions('filename'),
            'source_types' => $this->distinctColumnOptions('translation_workbench_source_files', 'source_type'),
            'extensions' => $this->sourceFileScopedDistinctOptions('extension'),
            'statuses' => $this->sourceFileScopedDistinctOptions('status'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function sourceFileScopedDistinctOptions(
        string $column
    ): array
    {
        if (! Schema::hasTable('translation_workbench_source_files') || ! Schema::hasColumn('translation_workbench_source_files', $column)) {
            return [];
        }

        $query = DB::table('translation_workbench_source_files')
            ->whereNotNull($column)
            ->where($column, '!=', '');

        foreach ([
            'source_root' => $this->sourceFilesRoot,
            'source_area' => $this->sourceFilesArea,
            'package_vendor' => $this->sourceFilesPackageVendor,
            'package_name' => $this->sourceFilesPackageName,
            'path_domain' => $this->sourceFilesDomain,
            'path_section' => $this->sourceFilesSection,
            'path_context' => $this->sourceFilesContext,
            'path_scope' => $this->sourceFilesScope,
            'path_extra' => $this->sourceFilesExtra,
            'filename' => $this->sourceFilesFilename,
            'source_type' => $this->sourceFilesSourceType,
            'extension' => $this->sourceFilesExtension,
            'status' => $this->sourceFilesStatus,
        ] as $filterColumn => $filterValue) {
            if ($filterColumn === $column || in_array($filterValue, ['', 'all'], true)) {
                continue;
            }

            $query->where($filterColumn, $filterValue);
        }

        return collect($query
            ->distinct()
            ->orderBy($column)
            ->pluck($column))
            ->map(static fn($value): string => (string) $value)
            ->reject(static fn(string $value): bool => $value === 'all')
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function sourceFilePathOptions(string $table): array
    {
        if ($table !== 'translation_workbench_source_files') {
            return [];
        }

        if (! Schema::hasTable('translation_workbench_source_files') || ! Schema::hasColumn('translation_workbench_source_files', 'path')) {
            return [];
        }

        $hasScopedFilter = ! in_array($this->sourceFilesSourceType, ['', 'all'], true)
            || ! in_array($this->sourceFilesExtension, ['', 'all'], true)
            || ! in_array($this->sourceFilesStatus, ['', 'all'], true)
            || ! in_array($this->sourceFilesRoot, ['', 'all'], true)
            || ! in_array($this->sourceFilesArea, ['', 'all'], true)
            || ! in_array($this->sourceFilesPackageVendor, ['', 'all'], true)
            || ! in_array($this->sourceFilesPackageName, ['', 'all'], true)
            || ! in_array($this->sourceFilesDomain, ['', 'all'], true)
            || ! in_array($this->sourceFilesSection, ['', 'all'], true)
            || ! in_array($this->sourceFilesContext, ['', 'all'], true)
            || ! in_array($this->sourceFilesScope, ['', 'all'], true)
            || ! in_array($this->sourceFilesExtra, ['', 'all'], true)
            || ! in_array($this->sourceFilesFilename, ['', 'all'], true)
            || trim($this->sourceFilesSearch) !== '';

        if (! $hasScopedFilter) {
            return ! in_array($this->sourceFilesPath, ['', 'all'], true)
                ? [$this->sourceFilesPath]
                : [];
        }

        $query = DB::table('translation_workbench_source_files')
            ->whereNotNull('path')
            ->where('path', '!=', '');

        foreach ([
            'source_root' => $this->sourceFilesRoot,
            'source_area' => $this->sourceFilesArea,
            'package_vendor' => $this->sourceFilesPackageVendor,
            'package_name' => $this->sourceFilesPackageName,
            'path_domain' => $this->sourceFilesDomain,
            'path_section' => $this->sourceFilesSection,
            'path_context' => $this->sourceFilesContext,
            'path_scope' => $this->sourceFilesScope,
            'path_extra' => $this->sourceFilesExtra,
            'filename' => $this->sourceFilesFilename,
            'source_type' => $this->sourceFilesSourceType,
            'extension' => $this->sourceFilesExtension,
            'status' => $this->sourceFilesStatus,
        ] as $column => $value) {
            if (! in_array($value, ['', 'all'], true)) {
                $query->where($column, $value);
            }
        }

        $search = trim($this->sourceFilesSearch);

        if ($search !== '') {
            $query->where('path', 'like', $this->likeFilterValue($search));
        }

        $paths = $query
            ->distinct()
            ->orderBy('path')
            ->limit(250)
            ->pluck('path')
            ->map(static fn($path): string => (string) $path)
            ->all();

        if (! in_array($this->sourceFilesPath, ['', 'all'], true) && ! in_array($this->sourceFilesPath, $paths, true)) {
            array_unshift($paths, $this->sourceFilesPath);
        }

        return $paths;
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function eventTypeOptions(string $table): array
    {
        if ($table !== 'translation_workbench_event_types' || ! Schema::hasTable('translation_workbench_event_types')) {
            return [
                'keys' => [],
                'labels' => [],
                'categories' => [],
                'severities' => [],
            ];
        }

        return [
            'keys' => $this->distinctColumnOptions('translation_workbench_event_types', 'key'),
            'labels' => $this->distinctColumnOptions('translation_workbench_event_types', 'label'),
            'categories' => $this->distinctColumnOptions('translation_workbench_event_types', 'category'),
            'severities' => $this->distinctColumnOptions('translation_workbench_event_types', 'severity'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function findingOptions(string $table): array
    {
        if ($table !== 'translation_workbench_findings' || ! Schema::hasTable('translation_workbench_findings')) {
            return [
                'kinds' => [],
                'function_names' => [],
                'entry_types' => [],
                'candidate_types' => [],
                'statuses' => [],
                'namespaces' => [],
                'groups' => [],
                'scopes' => [],
                'dynamic_scopes' => [],
            ];
        }

        return [
            'kinds' => $this->findingDistinctOptions('kind'),
            'function_names' => $this->findingDistinctOptions('function_name'),
            'entry_types' => $this->findingDistinctOptions('entry_type'),
            'candidate_types' => $this->findingDistinctOptions('candidate_type'),
            'statuses' => $this->findingDistinctOptions('status'),
            'namespaces' => $this->findingDistinctOptions('namespace'),
            'groups' => $this->findingScopedDistinctOptions('group'),
            'scopes' => $this->findingScopedDistinctOptions('scope'),
            'dynamic_scopes' => $this->findingDistinctOptions('dynamic_scope'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function findingDistinctOptions(string $column): array
    {
        return collect($this->distinctColumnOptions('translation_workbench_findings', $column))
            ->reject(static fn(string $value): bool => $value === 'all')
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function findingScopedDistinctOptions(string $column): array
    {
        if (! Schema::hasTable('translation_workbench_findings') || ! Schema::hasColumn('translation_workbench_findings', $column)) {
            return [];
        }

        $query = DB::table('translation_workbench_findings')
            ->whereNotNull($column)
            ->where($column, '!=', '');

        foreach ([
            'group' => $this->findingsGroup,
            'scope' => $this->findingsScope,
        ] as $filterColumn => $filterValue) {
            if ($filterColumn === $column || in_array($filterValue, ['', 'all'], true)) {
                continue;
            }

            $query->where($filterColumn, $filterValue);
        }

        return collect($query
            ->distinct()
            ->orderBy($column)
            ->pluck($column))
            ->map(static fn($value): string => (string) $value)
            ->reject(static fn(string $value): bool => $value === 'all')
            ->values()
            ->all();
    }

    private function normalizeFindingsScopeForSelectedGroup(): void
    {
        if (in_array($this->findingsScope, ['', 'all'], true)) {
            return;
        }

        if (! in_array($this->findingsScope, $this->findingScopedDistinctOptions('scope'), true)) {
            $this->findingsScope = 'all';
        }
    }

    private function normalizeFindingsGroupForSelectedScope(): void
    {
        if (in_array($this->findingsGroup, ['', 'all'], true)) {
            return;
        }

        if (! in_array($this->findingsGroup, $this->findingScopedDistinctOptions('group'), true)) {
            $this->findingsGroup = 'all';
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function keyOptions(string $table): array
    {
        if ($table !== 'translation_workbench_keys' || ! Schema::hasTable('translation_workbench_keys')) {
            return [
                'translation_keys' => [],
                'suggested_keys' => [],
                'namespaces' => [],
                'groups' => [],
                'scopes' => [],
                'segment_domains' => [],
                'segment_sections' => [],
                'segment_contexts' => [],
                'segment_extras' => [],
                'segment_names' => [],
                'key_types' => [],
                'statuses' => [],
                'review_statuses' => [],
            ];
        }

        return [
            'translation_keys' => $this->keyScopedDistinctOptions('translation_key', requireNamespace: true, requireGroup: true),
            'suggested_keys' => $this->keyScopedDistinctOptions('suggested_key', requireNamespace: true, requireGroup: true),
            'namespaces' => $this->keyDistinctOptions('namespace'),
            'groups' => $this->keyScopedDistinctOptions('group', requireNamespace: true),
            'scopes' => $this->keyDistinctOptions('scope'),
            'segment_domains' => $this->keyScopedDistinctOptions('key_segment_domain', requireNamespace: true, requireGroup: true),
            'segment_sections' => $this->keyScopedDistinctOptions('key_segment_section', requireNamespace: true, requireGroup: true, requireDomain: true),
            'segment_contexts' => $this->keyScopedDistinctOptions('key_segment_context', requireNamespace: true, requireGroup: true, requireDomain: true, requireSection: true),
            'segment_extras' => $this->keyScopedDistinctOptions('key_segment_extra', requireNamespace: true, requireGroup: true, requireDomain: true, requireSection: true, requireContext: true),
            'segment_names' => $this->keyScopedDistinctOptions('key_segment_name', requireNamespace: true, requireGroup: true),
            'key_types' => $this->keyDistinctOptions('key_type'),
            'statuses' => $this->keyDistinctOptions('status'),
            'review_statuses' => $this->keyDistinctOptions('review_status'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function keyDistinctOptions(string $column): array
    {
        return collect($this->distinctColumnOptions('translation_workbench_keys', $column))
            ->reject(static fn(string $value): bool => $value === 'all')
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function keyScopedDistinctOptions(
        string $column,
        bool $requireNamespace = false,
        bool $requireGroup = false,
        bool $requireDomain = false,
        bool $requireSection = false,
        bool $requireContext = false,
    ): array
    {
        if (! Schema::hasTable('translation_workbench_keys') || ! Schema::hasColumn('translation_workbench_keys', $column)) {
            return [];
        }

        if ($requireNamespace && in_array($this->keysNamespace, ['', 'all'], true)) {
            return [];
        }

        if ($requireGroup && in_array($this->keysGroup, ['', 'all'], true)) {
            return [];
        }

        if ($requireDomain && in_array($this->keysSegmentDomain, ['', 'all'], true)) {
            return [];
        }

        if ($requireSection && in_array($this->keysSegmentSection, ['', 'all'], true)) {
            return [];
        }

        if ($requireContext && in_array($this->keysSegmentContext, ['', 'all'], true)) {
            return [];
        }

        $query = DB::table('translation_workbench_keys')
            ->whereNotNull($column)
            ->where($column, '!=', '');

        if (! in_array($this->keysNamespace, ['', 'all'], true)) {
            $query->where('namespace', $this->keysNamespace);
        }

        if (! in_array($this->keysGroup, ['', 'all'], true)) {
            $query->where('group', $this->keysGroup);
        }

        foreach ([
            'key_segment_domain' => $this->keysSegmentDomain,
            'key_segment_section' => $this->keysSegmentSection,
            'key_segment_context' => $this->keysSegmentContext,
            'key_segment_extra' => $this->keysSegmentExtra,
            'key_segment_name' => $this->keysSegmentName,
        ] as $segmentColumn => $segmentValue) {
            if ($segmentColumn === $column || in_array($segmentValue, ['', 'all'], true)) {
                continue;
            }

            $query->where($segmentColumn, $segmentValue);
        }

        return collect($query
            ->distinct()
            ->orderBy($column)
            ->pluck($column))
            ->map(static fn($value): string => (string) $value)
            ->reject(static fn(string $value): bool => $value === 'all')
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function keyFindingOptions(string $table): array
    {
        if ($table !== 'translation_workbench_key_findings' || ! Schema::hasTable('translation_workbench_key_findings')) {
            return [
                'relation_types' => [],
                'statuses' => [],
            ];
        }

        return [
            'relation_types' => $this->distinctColumnOptions('translation_workbench_key_findings', 'relation_type'),
            'statuses' => $this->distinctColumnOptions('translation_workbench_key_findings', 'status'),
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function keyValueOptions(string $table): array
    {
        if ($table !== 'translation_workbench_key_values' || ! Schema::hasTable('translation_workbench_key_values')) {
            return [
                'locales' => [],
                'statuses' => [],
                'sources' => [],
            ];
        }

        return [
            'locales' => $this->distinctColumnOptions('translation_workbench_key_values', 'locale'),
            'statuses' => $this->distinctColumnOptions('translation_workbench_key_values', 'status'),
            'sources' => $this->distinctColumnOptions('translation_workbench_key_values', 'source'),
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function dynamicKeyValueOptions(string $table): array
    {
        if ($table !== 'translation_workbench_dynamic_key_values' || ! Schema::hasTable('translation_workbench_dynamic_key_values')) {
            return [
                'locales' => [],
                'statuses' => [],
                'sources' => [],
            ];
        }

        return [
            'locales' => $this->distinctColumnOptions('translation_workbench_dynamic_key_values', 'locale'),
            'statuses' => $this->distinctColumnOptions('translation_workbench_dynamic_key_values', 'status'),
            'sources' => $this->distinctColumnOptions('translation_workbench_dynamic_key_values', 'source'),
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function dynamicSourceOptions(string $table): array
    {
        if ($table !== 'translation_workbench_dynamic_sources' || ! Schema::hasTable('translation_workbench_dynamic_sources')) {
            return [
                'classifications' => [],
                'cardinalities' => [],
                'origins' => [],
                'source_types' => [],
                'confidences' => [],
                'statuses' => [],
            ];
        }

        return [
            'classifications' => $this->distinctColumnOptions('translation_workbench_dynamic_sources', 'classification'),
            'cardinalities' => $this->distinctColumnOptions('translation_workbench_dynamic_sources', 'cardinality'),
            'origins' => $this->distinctColumnOptions('translation_workbench_dynamic_sources', 'origin'),
            'source_types' => $this->distinctColumnOptions('translation_workbench_dynamic_sources', 'source_type'),
            'confidences' => $this->distinctColumnOptions('translation_workbench_dynamic_sources', 'confidence'),
            'statuses' => $this->distinctColumnOptions('translation_workbench_dynamic_sources', 'status'),
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function dynamicSourceCandidateOptions(string $table): array
    {
        if ($table !== 'translation_workbench_dynamic_source_candidates' || ! Schema::hasTable('translation_workbench_dynamic_source_candidates')) {
            return [
                'candidate_source_types' => [],
                'confidences' => [],
                'review_statuses' => [],
                'statuses' => [],
            ];
        }

        return [
            'candidate_source_types' => $this->distinctColumnOptions('translation_workbench_dynamic_source_candidates', 'candidate_source_type'),
            'confidences' => $this->distinctColumnOptions('translation_workbench_dynamic_source_candidates', 'confidence'),
            'review_statuses' => $this->distinctColumnOptions('translation_workbench_dynamic_source_candidates', 'review_status'),
            'statuses' => $this->distinctColumnOptions('translation_workbench_dynamic_source_candidates', 'status'),
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function dynamicSourceValueOptions(string $table): array
    {
        if ($table !== 'translation_workbench_dynamic_source_values' || ! Schema::hasTable('translation_workbench_dynamic_source_values')) {
            return [
                'origins' => [],
                'statuses' => [],
            ];
        }

        return [
            'origins' => $this->distinctColumnOptions('translation_workbench_dynamic_source_values', 'origin'),
            'statuses' => $this->distinctColumnOptions('translation_workbench_dynamic_source_values', 'status'),
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function langValueOptions(string $table): array
    {
        if ($table !== 'translation_workbench_lang_values' || ! Schema::hasTable('translation_workbench_lang_values')) {
            return [
                'main_locales' => [],
                'sub_locales' => [],
                'namespaces' => [],
                'value_types' => [],
                'statuses' => [],
            ];
        }

        return [
            'main_locales' => $this->langValueMainLocaleOptions(),
            'sub_locales' => $this->langValueSubLocaleOptions(),
            'namespaces' => $this->langValueDistinctOptions('namespace'),
            'value_types' => $this->langValueDistinctOptions('value_type'),
            'statuses' => $this->langValueDistinctOptions('status'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function langValueMainLocaleOptions(): array
    {
        return collect($this->langValueDistinctOptions('locale'))
            ->map(static fn(string $locale): string => preg_split('/[-_]/', $locale, 2)[0] ?? $locale)
            ->filter(static fn(string $locale): bool => $locale !== '')
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function langValueSubLocaleOptions(): array
    {
        if (in_array($this->langValuesMainLocale, ['', 'all'], true)) {
            return [];
        }

        $mainLocale = $this->langValuesMainLocale;

        return collect($this->langValueDistinctOptions('locale'))
            ->filter(static function (string $locale) use ($mainLocale): bool {
                return $locale !== $mainLocale
                    && (str_starts_with($locale, $mainLocale . '-') || str_starts_with($locale, $mainLocale . '_'));
            })
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function langValueDistinctOptions(string $column): array
    {
        return collect($this->distinctColumnOptions('translation_workbench_lang_values', $column))
            ->reject(static fn(string $value): bool => $value === 'all')
            ->values()
            ->all();
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function reviewOptions(string $table): array
    {
        if ($table !== 'translation_workbench_reviews' || ! Schema::hasTable('translation_workbench_reviews')) {
            return [
                'review_types' => [],
                'decisions' => [],
            ];
        }

        return [
            'review_types' => $this->reviewDistinctOptions('review_type'),
            'decisions' => $this->reviewDistinctOptions('decision'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function reviewDistinctOptions(string $column): array
    {
        return collect($this->distinctColumnOptions('translation_workbench_reviews', $column))
            ->reject(static fn(string $value): bool => $value === 'all')
            ->values()
            ->all();
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function keyInventoryOptions(string $table): array
    {
        if ($table !== 'translation_workbench_key_inventory' || ! Schema::hasTable('translation_workbench_key_inventory')) {
            return [
                'namespaces' => [],
                'groups' => [],
                'key_types' => [],
                'statuses' => [],
            ];
        }

        return [
            'namespaces' => $this->keyInventoryDistinctOptions('namespace'),
            'groups' => $this->keyInventoryGroupOptions(),
            'key_types' => $this->keyInventoryDistinctOptions('key_type'),
            'statuses' => $this->keyInventoryDistinctOptions('inventory_status'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function keyInventoryGroupOptions(): array
    {
        if (in_array($this->keyInventoryNamespace, ['', 'all'], true)) {
            return $this->keyInventoryDistinctOptions('group');
        }

        if (! Schema::hasTable('translation_workbench_key_inventory') || ! Schema::hasColumn('translation_workbench_key_inventory', 'group')) {
            return [];
        }

        return DB::table('translation_workbench_key_inventory')
            ->where('namespace', $this->keyInventoryNamespace)
            ->whereNotNull('group')
            ->where('group', '!=', '')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->map(static fn($value): string => (string) $value)
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function keyInventoryDistinctOptions(string $column): array
    {
        return collect($this->distinctColumnOptions('translation_workbench_key_inventory', $column))
            ->reject(static fn(string $value): bool => $value === 'all')
            ->values()
            ->all();
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function sharedKeyCandidateOptions(string $table): array
    {
        if ($table !== 'translation_workbench_shared_key_candidates' || ! Schema::hasTable('translation_workbench_shared_key_candidates')) {
            return [
                'confidences' => [],
                'statuses' => [],
            ];
        }

        return [
            'confidences' => $this->sharedKeyCandidateDistinctOptions('confidence'),
            'statuses' => $this->sharedKeyCandidateDistinctOptions('status'),
            'min_review_counts' => $this->sharedKeyCandidateMinCountOptions('matched_review_count'),
            'min_finding_counts' => $this->sharedKeyCandidateMinCountOptions('matched_finding_count'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function sharedKeyCandidateDistinctOptions(string $column): array
    {
        return collect($this->distinctColumnOptions('translation_workbench_shared_key_candidates', $column))
            ->reject(static fn(string $value): bool => $value === 'all')
            ->values()
            ->all();
    }

    /**
     * @return array<int, int>
     */
    private function sharedKeyCandidateMinCountOptions(string $column): array
    {
        if (! Schema::hasTable('translation_workbench_shared_key_candidates') || ! Schema::hasColumn('translation_workbench_shared_key_candidates', $column)) {
            return [0];
        }

        $max = (int) DB::table('translation_workbench_shared_key_candidates')->max($column);
        $max = min(max($max, 0), 20);

        return range(0, $max);
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function timelineEventOptions(string $table): array
    {
        if ($table !== 'translation_workbench_timeline_events' || ! Schema::hasTable('translation_workbench_timeline_events')) {
            return [
                'event_types' => [],
            ];
        }

        return [
            'event_types' => $this->timelineEventDistinctOptions('event_type'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function timelineEventDistinctOptions(string $column): array
    {
        return collect($this->distinctColumnOptions('translation_workbench_timeline_events', $column))
            ->reject(static fn(string $value): bool => $value === 'all')
            ->values()
            ->all();
    }

    private function timelineEventsDateBoundary(string $column, string $aggregate): ?string
    {
        $date = $this->timelineEventsTimestampBoundary($column, $aggregate);

        if ($date === null) {
            return null;
        }

        return $aggregate === 'max'
            ? $date->copy()->addDay()->toDateString()
            : $date->toDateString();
    }

    /**
     * @param  array{start?: string|null, end?: string|null}  $range
     */
    private function timelineEventsDateTimeResult(array $range, ?string $time): string
    {
        $from = $this->formattedTimelineEventDate((string) ($range['start'] ?? ''));
        $to = $this->formattedTimelineEventDate((string) ($range['end'] ?? ''));
        $time = $this->normalizedTimelineEventTime($time);

        if ($time !== null) {
            $timeSpanHours = $this->timelineEventsTimeSpanHours();
            $from .= ' ' . $this->timeWindowBoundary($time, -$timeSpanHours);
            $to .= ' ' . $this->timeWindowBoundary($time, $timeSpanHours);
        }

        return $from . ' - ' . $to;
    }

    private function formattedTimelineEventDate(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return __('Any date');
        }

        try {
            return Carbon::parse($value)->translatedFormat('D, d.M.Y');
        } catch (Throwable) {
            return $value;
        }
    }

    private function timeWindowBoundary(string $time, int $hours): string
    {
        try {
            return Carbon::createFromFormat('H:i', $time)
                ->addHours($hours)
                ->format('H:i');
        } catch (Throwable) {
            return $time;
        }
    }

    /**
     * @param  array{start?: string|null, end?: string|null}  $range
     */
    private function timelineEventsTimeBoundary(string $column, array $range, string $aggregate): string
    {
        $default = $aggregate === 'min' ? '00:00' : '23:00';
        $timestamp = $this->timelineEventsTimestampBoundary($column, $aggregate);

        if ($timestamp === null) {
            return $default;
        }

        $dateKey = $aggregate === 'min' ? 'start' : 'end';
        $selectedDate = trim((string) ($range[$dateKey] ?? ''));

        try {
            $selectedDate = $selectedDate !== '' ? Carbon::parse($selectedDate)->toDateString() : '';
        } catch (Throwable) {
            $selectedDate = '';
        }

        if ($selectedDate === '' || $selectedDate !== $timestamp->toDateString()) {
            return $default;
        }

        $time = $timestamp->copy()->second(0)->microsecond(0);

        if ($aggregate === 'max' && ((int) $time->minute !== 0)) {
            $time->addHour()->minute(0);
        } else {
            $time->minute(0);
        }

        if ($time->toDateString() !== $timestamp->toDateString()) {
            return '23:00';
        }

        return $time->format('H:i');
    }

    private function timelineEventsTimestampBoundary(string $column, string $aggregate): ?Carbon
    {
        if (! Schema::hasTable('translation_workbench_timeline_events')
            || ! Schema::hasColumn('translation_workbench_timeline_events', $column)
            || ! in_array($aggregate, ['min', 'max'], true)
        ) {
            return null;
        }

        $value = DB::table('translation_workbench_timeline_events')
            ->whereNotNull($column)
            ->{$aggregate}($column);

        if ($value === null) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    private function hasTimelineEventsDateRange(): bool
    {
        return trim((string) ($this->timelineEventsCreatedRange['start'] ?? '')) !== ''
            || trim((string) ($this->timelineEventsCreatedRange['end'] ?? '')) !== '';
    }

    private function hasTimelineEventsChangedRange(): bool
    {
        return trim((string) ($this->timelineEventsChangedRange['start'] ?? '')) !== ''
            || trim((string) ($this->timelineEventsChangedRange['end'] ?? '')) !== '';
    }

    private function timelineEventsDateTimeBoundary(string $boundary): ?Carbon
    {
        return $this->timelineEventsDateTimeRangeBoundary(
            range: $this->timelineEventsCreatedRange,
            time: $this->timelineEventsTimeFrom,
            boundary: $boundary,
        );
    }

    private function timelineEventsChangedDateTimeBoundary(string $boundary): ?Carbon
    {
        return $this->timelineEventsDateTimeRangeBoundary(
            range: $this->timelineEventsChangedRange,
            time: $this->timelineEventsChangedTime,
            boundary: $boundary,
        );
    }

    /**
     * @param  array{start?: string|null, end?: string|null}  $range
     */
    private function timelineEventsDateTimeRangeBoundary(array $range, ?string $time, string $boundary): ?Carbon
    {
        $dateKey = $boundary === 'from' ? 'start' : 'end';
        $dateValue = trim((string) ($range[$dateKey] ?? ''));

        if ($dateValue === '') {
            return null;
        }

        try {
            $date = Carbon::parse($dateValue);
        } catch (Throwable) {
            return null;
        }

        $timeValue = $this->normalizedTimelineEventTime($time);

        if ($timeValue === null) {
            return $boundary === 'from'
                ? $date->startOfDay()
                : $date->endOfDay();
        }

        [$hour, $minute] = explode(':', $timeValue);

        $date = $date->setTime((int) $hour, (int) $minute);
        $timeSpanHours = $this->timelineEventsTimeSpanHours();

        return $boundary === 'from'
            ? $date->subHours($timeSpanHours)
            : $date->addHours($timeSpanHours);
    }

    private function timelineEventsTimeSpanHours(): int
    {
        $timeSpan = $this->normalizedTimelineEventTimeSpan($this->timelineEventsTimeSpan);
        [$hours] = array_map('intval', explode(':', $timeSpan));

        return max(0, min(12, $hours));
    }

    private function normalizedTimelineEventTime(?string $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '') {
            return null;
        }

        if (! preg_match('/^\d{1,2}:\d{2}$/', $value)) {
            return null;
        }

        [$hour, $minute] = array_map('intval', explode(':', $value));

        if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
            return null;
        }

        return sprintf('%02d:%02d', $hour, $minute);
    }

    private function normalizedTimelineEventTimeSpan(?string $value): string
    {
        $value = $this->normalizedTimelineEventTime($value);

        if ($value === null) {
            return '02:00';
        }

        [$hour] = array_map('intval', explode(':', $value));

        return sprintf('%02d:00', max(0, min(12, $hour)));
    }

    /**
     * @return array<int, string>
     */
    private function rawDataSourceFileLookup(string $table): array
    {
        if ($table !== 'translation_workbench_findings' || ! Schema::hasTable('translation_workbench_source_files')) {
            return [];
        }

        return DB::table('translation_workbench_source_files')
            ->pluck('path', 'id')
            ->mapWithKeys(static fn($path, $id): array => [(int) $id => (string) $path])
            ->all();
    }

    /**
     * @return array<int, array{translation_key: string|null, suggested_key: string|null, namespace: string|null, group: string|null}>
     */
    private function rawDataKeyLookup(string $table): array
    {
        if ($table !== 'translation_workbench_key_findings' || ! Schema::hasTable('translation_workbench_keys')) {
            return [];
        }

        return DB::table('translation_workbench_keys')
            ->select(['id', 'translation_key', 'suggested_key', 'namespace', 'group'])
            ->whereIn('id', $this->visibleRawDataForeignIds('key_id'))
            ->get()
            ->mapWithKeys(static fn($row): array => [
                (int) $row->id => [
                    'translation_key' => $row->translation_key,
                    'suggested_key' => $row->suggested_key,
                    'namespace' => $row->namespace,
                    'group' => $row->group,
                ],
            ])
            ->all();
    }

    /**
     * @return array<int, array{literal_text: string|null, suggested_key: string|null, raw_expression: string|null, source_line: int|null}>
     */
    private function rawDataFindingLookup(string $table): array
    {
        if ($table !== 'translation_workbench_key_findings' || ! Schema::hasTable('translation_workbench_findings')) {
            return [];
        }

        return DB::table('translation_workbench_findings')
            ->select(['id', 'literal_text', 'suggested_key', 'raw_expression', 'source_line'])
            ->whereIn('id', $this->visibleRawDataForeignIds('finding_id'))
            ->get()
            ->mapWithKeys(static fn($row): array => [
                (int) $row->id => [
                    'literal_text' => $row->literal_text,
                    'suggested_key' => $row->suggested_key,
                    'raw_expression' => $row->raw_expression,
                    'source_line' => $row->source_line !== null ? (int) $row->source_line : null,
                ],
            ])
            ->all();
    }

    /**
     * @return array<int, int>
     */
    private function visibleRawDataForeignIds(string $column): array
    {
        $table = $this->normalizedActiveTable();
        $columns = Schema::hasTable($table) ? Schema::getColumnListing($table) : [];

        if (! in_array($column, $columns, true)) {
            return [];
        }

        $query = DB::table($table);
        $this->applyTableFilters($query, $table, $columns);

        if ($this->isSortableColumn($this->sortField, $columns)) {
            $query->orderBy($this->sortField, $this->sortDirection === 'asc' ? 'asc' : 'desc');
        } elseif (in_array('id', $columns, true)) {
            $query->orderByDesc('id');
        }

        return $query
            ->limit($this->normalizedPerPage())
            ->pluck($column)
            ->filter(static fn($id): bool => $id !== null)
            ->map(static fn($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function distinctColumnOptions(string $table, string $column): array
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return [];
        }

        return DB::table($table)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->map(static fn($value): string => (string) $value)
            ->all();
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<string, array<string, mixed>>
     */
    private function columnMetadata(string $table, array $columns): array
    {
        $schemaColumns = [];

        try {
            $schemaColumns = collect(Schema::getColumns($table))
                ->mapWithKeys(function (array $column): array {
                    $name = (string) ($column['name'] ?? '');

                    return $name !== '' ? [$name => $column] : [];
                })
                ->all();
        } catch (Throwable) {
            $schemaColumns = [];
        }

        return collect($columns)
            ->mapWithKeys(function (string $column) use ($schemaColumns): array {
                $metadata = $schemaColumns[$column] ?? [];

                return [
                    $column => [
                        'name' => $column,
                        'type' => (string) ($metadata['type_name'] ?? $metadata['type'] ?? 'unknown'),
                        'nullable' => (bool) ($metadata['nullable'] ?? false),
                        'default' => $metadata['default'] ?? null,
                        'auto_increment' => (bool) ($metadata['auto_increment'] ?? false),
                        'comment' => $metadata['comment'] ?? null,
                    ],
                ];
            })
            ->all();
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function foreignKeyMetadata(string $table): array
    {
        if (! Schema::hasTable($table)) {
            return [];
        }

        $metadata = [];

        try {
            foreach (Schema::getForeignKeys($table) as $foreignKey) {
                foreach ((array) ($foreignKey['columns'] ?? []) as $column) {
                    $column = (string) $column;
                    $foreignColumns = (array) ($foreignKey['foreign_columns']
                        ?? $foreignKey['foreignColumns']
                        ?? $foreignKey['foreign_columns_names']
                        ?? $foreignKey['foreign_column']
                        ?? []);

                    $metadata[$column] = [
                        'is_foreign_key' => true,
                        'is_schema_foreign_key' => true,
                        'is_name_fallback' => false,
                        'name' => $foreignKey['name'] ?? null,
                        'foreign_table' => $foreignKey['foreign_table']
                            ?? $foreignKey['foreignTable']
                            ?? $foreignKey['foreign_table_name']
                            ?? null,
                        'foreign_columns' => $foreignColumns,
                    ];
                }
            }
        } catch (Throwable) {
            //
        }

        foreach ($this->knownForeignKeys($table) as $column => $foreignKey) {
            $metadata[$column] = [
                'is_foreign_key' => true,
                'is_schema_foreign_key' => $metadata[$column]['is_schema_foreign_key'] ?? false,
                'is_name_fallback' => false,
                'name' => $metadata[$column]['name'] ?? $foreignKey['name'],
                'foreign_table' => $metadata[$column]['foreign_table'] ?? $foreignKey['foreign_table'],
                'foreign_columns' => ($metadata[$column]['foreign_columns'] ?? []) ?: $foreignKey['foreign_columns'],
            ];
        }

        foreach (Schema::getColumnListing($table) as $column) {
            if (! str_ends_with($column, '_id') || $column === 'id' || isset($metadata[$column])) {
                continue;
            }

            $metadata[$column] = [
                'is_foreign_key' => true,
                'is_schema_foreign_key' => false,
                'is_name_fallback' => true,
                'name' => null,
                'foreign_table' => null,
                'foreign_columns' => [],
            ];
        }

        return $metadata;
    }

    /**
     * @return array<string, array{name: string, foreign_table: string, foreign_columns: array<int, string>}>
     */
    private function knownForeignKeys(string $table): array
    {
        return match ($table) {
            'translation_workbench_entries' => [
                'previous_entry_id' => [
                    'name' => 'translation_workbench_entries_previous_entry_id_foreign',
                    'foreign_table' => 'translation_workbench_entries',
                    'foreign_columns' => ['id'],
                ],
                'replaced_by_entry_id' => [
                    'name' => 'translation_workbench_entries_replaced_by_entry_id_foreign',
                    'foreign_table' => 'translation_workbench_entries',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_events' => [
                'entry_id' => [
                    'name' => 'translation_workbench_events_entry_id_foreign',
                    'foreign_table' => 'translation_workbench_entries',
                    'foreign_columns' => ['id'],
                ],
                'created_by' => [
                    'name' => 'translation_workbench_events_created_by_foreign',
                    'foreign_table' => 'users',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_values' => [
                'entry_id' => [
                    'name' => 'translation_workbench_values_entry_id_foreign',
                    'foreign_table' => 'translation_workbench_entries',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_occurrences' => [
                'entry_id' => [
                    'name' => 'translation_workbench_occurrences_entry_id_foreign',
                    'foreign_table' => 'translation_workbench_entries',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_dynamic_values' => [
                'entry_id' => [
                    'name' => 'translation_workbench_dynamic_values_entry_id_foreign',
                    'foreign_table' => 'translation_workbench_entries',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_option_discoveries' => [
                'matched_entry_id' => [
                    'name' => 'translation_workbench_option_discoveries_matched_entry_id_foreign',
                    'foreign_table' => 'translation_workbench_entries',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_duplicate_candidates' => [
                'entry_id' => [
                    'name' => 'translation_workbench_duplicate_candidates_entry_id_foreign',
                    'foreign_table' => 'translation_workbench_entries',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_findings' => [
                'source_file_id' => [
                    'name' => 'translation_workbench_findings_source_file_id_foreign',
                    'foreign_table' => 'translation_workbench_source_files',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_key_findings' => [
                'key_id' => [
                    'name' => 'translation_workbench_key_findings_key_id_foreign',
                    'foreign_table' => 'translation_workbench_keys',
                    'foreign_columns' => ['id'],
                ],
                'finding_id' => [
                    'name' => 'translation_workbench_key_findings_finding_id_foreign',
                    'foreign_table' => 'translation_workbench_findings',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_key_values' => [
                'key_id' => [
                    'name' => 'translation_workbench_key_values_key_id_foreign',
                    'foreign_table' => 'translation_workbench_keys',
                    'foreign_columns' => ['id'],
                ],
                'reviewed_by_user_id' => [
                    'name' => 'translation_workbench_key_values_reviewed_by_user_id_foreign',
                    'foreign_table' => 'users',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_dynamic_key_values' => [
                'key_id' => [
                    'name' => 'translation_workbench_dynamic_key_values_key_id_foreign',
                    'foreign_table' => 'translation_workbench_keys',
                    'foreign_columns' => ['id'],
                ],
                'reviewed_by_user_id' => [
                    'name' => 'translation_workbench_dynamic_key_values_reviewed_by_user_id_foreign',
                    'foreign_table' => 'users',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_dynamic_sources' => [
                'key_id' => [
                    'name' => 'translation_workbench_dynamic_sources_key_id_foreign',
                    'foreign_table' => 'translation_workbench_keys',
                    'foreign_columns' => ['id'],
                ],
                'finding_id' => [
                    'name' => 'translation_workbench_dynamic_sources_finding_id_foreign',
                    'foreign_table' => 'translation_workbench_findings',
                    'foreign_columns' => ['id'],
                ],
                'option_discovery_id' => [
                    'name' => 'translation_workbench_dynamic_sources_option_discovery_id_foreign',
                    'foreign_table' => 'translation_workbench_option_discoveries',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_dynamic_source_candidates' => [
                'dynamic_source_id' => [
                    'name' => 'translation_workbench_dynamic_source_candidates_dynamic_source_id_foreign',
                    'foreign_table' => 'translation_workbench_dynamic_sources',
                    'foreign_columns' => ['id'],
                ],
                'key_id' => [
                    'name' => 'translation_workbench_dynamic_source_candidates_key_id_foreign',
                    'foreign_table' => 'translation_workbench_keys',
                    'foreign_columns' => ['id'],
                ],
                'finding_id' => [
                    'name' => 'translation_workbench_dynamic_source_candidates_finding_id_foreign',
                    'foreign_table' => 'translation_workbench_findings',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_dynamic_source_values' => [
                'dynamic_source_id' => [
                    'name' => 'translation_workbench_dynamic_source_values_dynamic_source_id_foreign',
                    'foreign_table' => 'translation_workbench_dynamic_sources',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_reviews' => [
                'key_id' => [
                    'name' => 'translation_workbench_reviews_key_id_foreign',
                    'foreign_table' => 'translation_workbench_keys',
                    'foreign_columns' => ['id'],
                ],
                'finding_id' => [
                    'name' => 'translation_workbench_reviews_finding_id_foreign',
                    'foreign_table' => 'translation_workbench_findings',
                    'foreign_columns' => ['id'],
                ],
                'reviewed_by_user_id' => [
                    'name' => 'translation_workbench_reviews_reviewed_by_user_id_foreign',
                    'foreign_table' => 'users',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_shared_key_candidates' => [
                'finding_id' => [
                    'name' => 'translation_workbench_shared_key_candidates_finding_id_foreign',
                    'foreign_table' => 'translation_workbench_findings',
                    'foreign_columns' => ['id'],
                ],
                'key_id' => [
                    'name' => 'translation_workbench_shared_key_candidates_key_id_foreign',
                    'foreign_table' => 'translation_workbench_keys',
                    'foreign_columns' => ['id'],
                ],
                'matched_key_id' => [
                    'name' => 'translation_workbench_shared_key_candidates_matched_key_id_foreign',
                    'foreign_table' => 'translation_workbench_keys',
                    'foreign_columns' => ['id'],
                ],
            ],
            'translation_workbench_timeline_events' => [
                'event_type_id' => [
                    'name' => 'translation_workbench_timeline_events_event_type_id_foreign',
                    'foreign_table' => 'translation_workbench_event_types',
                    'foreign_columns' => ['id'],
                ],
                'key_id' => [
                    'name' => 'translation_workbench_timeline_events_key_id_foreign',
                    'foreign_table' => 'translation_workbench_keys',
                    'foreign_columns' => ['id'],
                ],
                'finding_id' => [
                    'name' => 'translation_workbench_timeline_events_finding_id_foreign',
                    'foreign_table' => 'translation_workbench_findings',
                    'foreign_columns' => ['id'],
                ],
                'review_id' => [
                    'name' => 'translation_workbench_timeline_events_review_id_foreign',
                    'foreign_table' => 'translation_workbench_reviews',
                    'foreign_columns' => ['id'],
                ],
                'created_by_user_id' => [
                    'name' => 'translation_workbench_timeline_events_created_by_user_id_foreign',
                    'foreign_table' => 'users',
                    'foreign_columns' => ['id'],
                ],
            ],
            default => [],
        };
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<int, string>
     */
    private function sortableColumns(array $columns): array
    {
        return collect($columns)
            ->filter(fn(string $column): bool => $this->isSortableColumn($column, $columns))
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function isSortableColumn(string $column, array $columns): bool
    {
        if (! in_array($column, $columns, true)) {
            return false;
        }

        return $column === 'id'
            || str_ends_with($column, '_id')
            || in_array($column, [
                'fingerprint',
                'source_signature',
                'source_fingerprint',
                'expression_fingerprint',
                'semantic_fingerprint',
                'kind',
                'entry_type',
                'candidate_type',
                'is_ui_key',
                'is_dynamic_key',
                'is_dynamic_multi',
                'candidate_reason',
                'source_type',
                'source_root',
                'source_area',
                'package_vendor',
                'package_name',
                'path_domain',
                'path_section',
                'path_context',
                'path_scope',
                'path_extra',
                'filename',
                'target_type',
                'source_path',
                'source_line',
                'function_name',
                'literal_text',
                'literal_text_suggested',
                'found_translation_key',
                'translation_key',
                'normalized_translation_key',
                'current_translation_key',
                'suggested_shared_translation_key',
                'normalized_literal',
                'key_type',
                'namespace',
                'group',
                'inventory_status',
                'key_record_count',
                'reviewed_key_count',
                'finding_active_count',
                'finding_commented_out_count',
                'finding_obsolete_count',
                'relation_active_count',
                'relation_commented_out_count',
                'relation_obsolete_count',
                'source_value_active_count',
                'source_value_obsolete_count',
                'source_value_deleted_count',
                'target_value_active_count',
                'target_value_obsolete_count',
                'target_value_deleted_count',
                'lang_file_locale_count',
                'workbench_value_count',
                'dynamic_value_count',
                'dynamic_source_count',
                'shared_finding_count',
                'matched_review_count',
                'matched_finding_count',
                'is_shared',
                'is_ui',
                'is_dynamic',
                'has_active_code_usage',
                'has_only_obsolete_code_usage',
                'has_lang_values',
                'is_orphaned_lang_value',
                'candidate_for_lang_delete',
                'path_key',
                'scope',
                'key_segment_domain',
                'key_segment_section',
                'key_segment_context',
                'key_segment_extra',
                'key_segment_name',
                'dynamic_scope',
                'translation_key_source',
                'deleted_segments',
                'existing_key',
                'suggested_key',
                'relation_type',
                'locale',
                'is_source_locale',
                'locale_role',
                'main_locale',
                'parent_locale',
                'value_key',
                'source',
                'review_type',
                'decision',
                'value_type',
                'source_hash',
                'status',
                'review_status',
                'event_type',
                'event_type_id',
                'key',
                'category',
                'severity',
                'icon',
                'color',
                'is_active',
                'created_by',
                'created_by_user_id',
                'reviewed_by_user_id',
                'value_key',
                'source_reference',
                'duplicate_type',
                'group_fingerprint',
                'confidence',
                'group_size',
                'scan_count',
                'first_seen_at',
                'last_seen_at',
                'created_at',
                'updated_at',
            ], true);
    }

    private function defaultSortDirection(string $column): string
    {
        return in_array($column, ['id', 'source_line', 'scan_count', 'first_seen_at', 'last_seen_at', 'created_at', 'updated_at'], true)
            ? 'desc'
            : 'asc';
    }

    private function resetSortForTable(): void
    {
        $table = $this->normalizedActiveTable();
        $columns = Schema::hasTable($table) ? Schema::getColumnListing($table) : [];

        $this->sortField = in_array('id', $columns, true)
            ? 'id'
            : (string) ($columns[0] ?? '');
        $this->sortDirection = $this->defaultSortDirection($this->sortField);
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function summary(string $table, array $columns): array
    {
        if ($columns === [] || ! Schema::hasTable($table)) {
            return [
                'ranges' => [],
                'distincts' => [],
                'distributions' => [],
                'nulls' => [],
                'key_namespaces' => [],
                'duplicate_diagnostics' => [],
            ];
        }

        return [
            'ranges' => $this->rangeSummary($table, $columns),
            'distincts' => $this->distinctSummary($table, $columns),
            'distributions' => $this->distributionSummary($table, $columns),
            'nulls' => $this->nullSummary($table, $columns),
            'key_namespaces' => $this->keyNamespaceSummary($table, $columns),
            'duplicate_diagnostics' => $this->duplicateDiagnosticsSummary($table),
        ];
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<int, array{column: string, min: mixed, max: mixed, count: int}>
     */
    private function rangeSummary(string $table, array $columns): array
    {
        $rangeColumns = collect(['id', 'source_line', 'scan_count'])
            ->merge(collect($columns)->filter(static fn(string $column): bool => str_ends_with($column, '_id')))
            ->filter(static fn(string $column): bool => in_array($column, $columns, true))
            ->unique()
            ->values();

        return $rangeColumns
            ->map(function (string $column) use ($table): array {
                $row = DB::table($table)
                    ->selectRaw(
                        sprintf(
                            'MIN(%1$s) as min_value, MAX(%1$s) as max_value, COUNT(%1$s) as filled_count',
                            DB::getQueryGrammar()->wrap($column),
                        ),
                    )
                    ->first();

                return [
                    'column' => $column,
                    'min' => $row?->min_value,
                    'max' => $row?->max_value,
                    'count' => (int) ($row?->filled_count ?? 0),
                ];
            })
            ->all();
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<int, array{column: string, count: int}>
     */
    private function distinctSummary(string $table, array $columns): array
    {
        $distinctColumns = collect([
            'fingerprint',
            'source_signature',
            'source_fingerprint',
            'expression_fingerprint',
            'semantic_fingerprint',
            'entry_type',
            'candidate_type',
            'is_ui_key',
            'is_dynamic_key',
            'is_dynamic_multi',
            'candidate_reason',
            'target_type',
            'translation_key',
            'found_translation_key',
            'translation_key_source',
            'existing_key',
            'suggested_key',
            'namespace',
            'group',
            'path_key',
            'scope',
            'dynamic_scope',
            'source_path',
            'raw_expression',
            'literal_text',
            'literal_text_suggested',
            'value_key',
            'locale',
            'is_source_locale',
            'locale_role',
            'main_locale',
            'parent_locale',
            'source',
            'value_type',
            'source_hash',
            'review_type',
            'decision',
            'event_type',
            'event_type_id',
            'key',
            'category',
            'severity',
            'icon',
            'color',
            'is_active',
            'native_label',
            'duplicate_type',
            'group_fingerprint',
            'confidence',
        ])
            ->filter(static fn(string $column): bool => in_array($column, $columns, true))
            ->values();

        return $distinctColumns
            ->map(function (string $column) use ($table): array {
                return [
                    'column' => $column,
                    'count' => (int) DB::table($table)->distinct()->count($column),
                ];
            })
            ->all();
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<int, array{column: string, values: array<int, array{value: string, count: int}>}>
     */
    private function distributionSummary(string $table, array $columns): array
    {
        $distributionColumns = collect([
            'kind',
            'entry_type',
            'candidate_type',
            'is_ui_key',
            'is_dynamic_key',
            'is_dynamic_multi',
            'candidate_reason',
            'source_type',
            'target_type',
            'translation_key_source',
            'deleted_segments',
            'function_name',
            'namespace',
            'group',
            'path_key',
            'scope',
            'dynamic_scope',
            'key_type',
            'status',
            'review_status',
            'relation_type',
            'locale',
            'is_source_locale',
            'locale_role',
            'main_locale',
            'parent_locale',
            'source',
            'value_type',
            'review_type',
            'decision',
            'event_type',
            'event_type_id',
            'key',
            'category',
            'severity',
            'icon',
            'color',
            'is_active',
            'created_by',
            'created_by_user_id',
            'reviewed_by_user_id',
            'duplicate_type',
            'confidence',
            'group_size',
        ])
            ->filter(static fn(string $column): bool => in_array($column, $columns, true))
            ->values();

        return $distributionColumns
            ->map(function (string $column) use ($table): array {
                $wrappedColumn = DB::getQueryGrammar()->wrap($column);
                $values = DB::table($table)
                    ->selectRaw("COALESCE(CAST({$wrappedColumn} AS TEXT), 'NULL') as value, COUNT(*) as total")
                    ->groupByRaw("COALESCE(CAST({$wrappedColumn} AS TEXT), 'NULL')")
                    ->orderByDesc('total')
                    ->orderBy('value')
                    ->limit(12)
                    ->get()
                    ->map(static fn($row): array => [
                        'value' => (string) $row->value,
                        'count' => (int) $row->total,
                    ])
                    ->all();

                return [
                    'column' => $column,
                    'values' => $values,
                ];
            })
            ->all();
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<int, array{column: string, filled: int, null: int, empty: int}>
     */
    private function nullSummary(string $table, array $columns): array
    {
        $summaryColumns = collect($columns)
            ->reject(static fn(string $column): bool => in_array($column, ['id', 'created_at', 'updated_at'], true))
            ->values();

        return $summaryColumns
            ->map(function (string $column) use ($table): array {
                $wrappedColumn = DB::getQueryGrammar()->wrap($column);
                $row = DB::table($table)
                    ->selectRaw("COUNT({$wrappedColumn}) as filled_count")
                    ->selectRaw("SUM(CASE WHEN {$wrappedColumn} IS NULL THEN 1 ELSE 0 END) as null_count")
                    ->selectRaw("SUM(CASE WHEN {$wrappedColumn} IS NOT NULL AND CAST({$wrappedColumn} AS TEXT) = '' THEN 1 ELSE 0 END) as empty_count")
                    ->first();

                return [
                    'column' => $column,
                    'filled' => (int) ($row?->filled_count ?? 0),
                    'null' => (int) ($row?->null_count ?? 0),
                    'empty' => (int) ($row?->empty_count ?? 0),
                ];
            })
            ->all();
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<int, array{namespace: string, translation_key: int, existing_key: int, suggested_key: int, total: int}>
     */
    private function keyNamespaceSummary(string $table, array $columns): array
    {
        $keyColumns = ['translation_key', 'existing_key', 'suggested_key'];

        if ($table !== 'translation_workbench_entries' || array_diff($keyColumns, $columns) !== []) {
            return [];
        }

        $namespaces = [];

        foreach ($keyColumns as $column) {
            DB::table($table)
                ->whereNotNull($column)
                ->select($column)
                ->orderBy($column)
                ->chunk(500, function ($rows) use (&$namespaces, $column): void {
                    foreach ($rows as $row) {
                        $key = trim((string) ($row->{$column} ?? ''), '.');

                        if ($key === '') {
                            continue;
                        }

                        $namespace = explode('.', $key, 2)[0] ?: 'unknown';
                        $namespaces[$namespace] ??= [
                            'namespace' => $namespace,
                            'translation_key' => 0,
                            'existing_key' => 0,
                            'suggested_key' => 0,
                            'total' => 0,
                        ];
                        $namespaces[$namespace][$column]++;
                        $namespaces[$namespace]['total']++;
                    }
                });
        }

        return collect($namespaces)
            ->sortByDesc('total')
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function duplicateDiagnosticsSummary(string $table): array
    {
        if (
            ! in_array($table, ['translation_workbench_entries', 'translation_workbench_duplicate_candidates'], true)
            || ! Schema::hasTable('translation_workbench_duplicate_candidates')
        ) {
            return [];
        }

        $baseQuery = DB::table('translation_workbench_duplicate_candidates');
        $total = (int) (clone $baseQuery)->count();

        if ($total === 0) {
            return [];
        }

        $byType = (clone $baseQuery)
            ->selectRaw('duplicate_type, COUNT(*) as total')
            ->groupBy('duplicate_type')
            ->orderByDesc('total')
            ->orderBy('duplicate_type')
            ->get()
            ->map(static fn($row): array => [
                'label' => (string) $row->duplicate_type,
                'count' => (int) $row->total,
            ])
            ->all();

        $byConfidence = (clone $baseQuery)
            ->selectRaw('confidence, COUNT(*) as total')
            ->groupBy('confidence')
            ->orderByDesc('total')
            ->orderBy('confidence')
            ->get()
            ->map(static fn($row): array => [
                'label' => (string) $row->confidence,
                'count' => (int) $row->total,
            ])
            ->all();

        $groups = (clone $baseQuery)
            ->selectRaw('duplicate_type, confidence, group_fingerprint, MAX(group_size) as group_size, COUNT(*) as candidate_rows')
            ->groupBy('duplicate_type', 'confidence', 'group_fingerprint')
            ->orderByDesc('group_size')
            ->orderByDesc('candidate_rows')
            ->limit(8)
            ->get()
            ->map(static fn($row): array => [
                'duplicate_type' => (string) $row->duplicate_type,
                'confidence' => (string) $row->confidence,
                'group_fingerprint' => (string) $row->group_fingerprint,
                'group_size' => (int) $row->group_size,
                'candidate_rows' => (int) $row->candidate_rows,
            ])
            ->all();

        return [
            'total' => $total,
            'by_type' => $byType,
            'by_confidence' => $byConfidence,
            'groups' => $groups,
        ];
    }

    /**
     * @return array<string, int>
     */
    private function tableCounts(): array
    {
        return collect($this->tables)
            ->mapWithKeys(function (string $table): array {
                return [
                    $table => Schema::hasTable($table)
                        ? (int) DB::table($table)->count()
                        : 0,
                ];
            })
            ->all();
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

    private function selectedTimelineChainPreviewId(): ?int
    {
        $selected = trim($this->timelineChainPreviewId);

        if ($selected === '' || $selected === 'auto') {
            return null;
        }

        $id = (int) $selected;

        return $id > 0 ? $id : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function timelineChainSampleRows(string $table): array
    {
        if ($table !== 'translation_workbench_timeline_chains' || ! Schema::hasTable($table)) {
            return [];
        }

        $baseSelect = [
            'id',
            'translation_key',
            'chain_type',
            'chain_status',
            'key_count',
            'finding_count',
            'active_finding_count',
            'obsolete_finding_count',
            'commented_out_finding_count',
            'review_count',
            'timeline_event_count',
            'lang_value_count',
            'shared_candidate_count',
            'bulk_review_count',
            'related_translation_keys',
            'relation_summary',
            'timeline_event_summary',
        ];
        $rows = collect(['bulk', 'shared', 'moved'])
            ->flatMap(fn(string $type): array => DB::table($table)
                ->where('chain_type', $type)
                ->orderByDesc('timeline_event_count')
                ->orderByDesc('finding_count')
                ->limit($type === 'bulk' ? 2 : 1)
                ->get($baseSelect)
                ->all())
            ->unique('id')
            ->values();

        if ($rows->count() < 5) {
            $rows = $rows
                ->merge(DB::table($table)
                    ->whereNotIn('id', $rows->pluck('id')->all())
                    ->orderByRaw("CASE WHEN chain_status = 'active' THEN 0 ELSE 1 END")
                    ->orderByDesc('timeline_event_count')
                    ->orderByDesc('finding_count')
                    ->limit(5 - $rows->count())
                    ->get($baseSelect))
                ->unique('id')
                ->values();
        }

        return $rows
            ->take(5)
            ->map(fn(object $row): array => [
                'id' => (int) $row->id,
                'translation_key' => (string) $row->translation_key,
                'chain_type' => (string) $row->chain_type,
                'chain_status' => (string) $row->chain_status,
                'key_count' => (int) $row->key_count,
                'finding_count' => (int) $row->finding_count,
                'active_finding_count' => (int) $row->active_finding_count,
                'obsolete_finding_count' => (int) $row->obsolete_finding_count,
                'commented_out_finding_count' => (int) $row->commented_out_finding_count,
                'review_count' => (int) $row->review_count,
                'timeline_event_count' => (int) $row->timeline_event_count,
                'lang_value_count' => (int) $row->lang_value_count,
                'shared_candidate_count' => (int) $row->shared_candidate_count,
                'bulk_review_count' => (int) $row->bulk_review_count,
                'related_translation_keys' => $this->decodeJsonArray($row->related_translation_keys ?? null),
                'relation_summary' => $this->decodeJsonArray($row->relation_summary ?? null),
                'timeline_event_summary' => $this->decodeJsonArray($row->timeline_event_summary ?? null),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function timelineChainPreviewOptions(string $table): array
    {
        if ($table !== 'translation_workbench_timeline_chains' || ! Schema::hasTable($table)) {
            return [];
        }

        $select = [
            'id',
            'translation_key',
            'chain_type',
            'chain_status',
            'finding_count',
            'timeline_event_count',
            'shared_candidate_count',
            'bulk_review_count',
        ];

        $optionRows = collect();
        $appendRows = function (string $group, $query) use ($select, $optionRows): void {
            $query
                ->get($select)
                ->each(static function (object $row) use ($group, $optionRows): void {
                    $row->preview_group = $group;
                    $optionRows->push($row);
                });
        };

        if (Schema::hasTable('translation_workbench_shared_key_candidates')) {
            $pendingChainIds = DB::table('translation_workbench_shared_key_candidates as candidates')
                ->join($table . ' as chains', 'chains.translation_key', '=', 'candidates.suggested_shared_translation_key')
                ->where('candidates.status', 'pending')
                ->selectRaw('chains.id, count(*) as pending_count')
                ->groupBy('chains.id')
                ->orderByDesc('pending_count')
                ->limit(8)
                ->pluck('chains.id')
                ->map(static fn(mixed $id): int => (int) $id)
                ->all();

            if ($pendingChainIds !== []) {
                $pendingOrderSql = collect($pendingChainIds)
                    ->values()
                    ->map(static fn(int $id, int $index): string => 'WHEN ' . $id . ' THEN ' . $index)
                    ->implode(' ');

                $appendRows(
                    'Shared review pending',
                    DB::table($table)
                        ->whereIn('id', $pendingChainIds)
                        ->orderByRaw('CASE id ' . $pendingOrderSql . ' ELSE 999 END')
                );
            }
        }

        $appendRows(
            'Graph stress',
            DB::table($table)
                ->whereIn('chain_type', ['bulk', 'shared', 'moved'])
                ->orderByRaw('((finding_count * 3) + (shared_candidate_count * 3) + (timeline_event_count * 2) + key_count + review_count + lang_value_count + bulk_review_count) desc')
                ->orderByDesc('finding_count')
                ->limit(8)
        );
        $appendRows(
            'Event stress',
            DB::table($table)
                ->orderByDesc('timeline_event_count')
                ->orderByDesc('finding_count')
                ->limit(5)
        );
        $appendRows(
            'Single active',
            DB::table($table)
                ->where('chain_type', 'single')
                ->where('chain_status', 'active')
                ->orderByDesc('timeline_event_count')
                ->orderByDesc('finding_count')
                ->limit(5)
        );
        $appendRows(
            'Single inactive',
            DB::table($table)
                ->where('chain_type', 'single')
                ->where('chain_status', 'inactive')
                ->orderByDesc('timeline_event_count')
                ->orderByDesc('finding_count')
                ->limit(5)
        );
        $appendRows(
            'Moved',
            DB::table($table)
                ->where('chain_type', 'moved')
                ->orderByRaw("CASE WHEN chain_status = 'active' THEN 0 ELSE 1 END")
                ->orderByDesc('timeline_event_count')
                ->orderByDesc('finding_count')
                ->limit(5)
        );
        $appendRows(
            'Bulk/shared samples',
            DB::table($table)
                ->whereIn('chain_type', ['bulk', 'shared'])
                ->orderByRaw("CASE chain_type WHEN 'bulk' THEN 0 WHEN 'shared' THEN 1 ELSE 2 END")
                ->orderByRaw("CASE WHEN chain_status = 'active' THEN 0 ELSE 1 END")
                ->orderByDesc('timeline_event_count')
                ->orderByDesc('finding_count')
                ->limit(10)
        );

        return $optionRows
            ->unique('id')
            ->values()
            ->map(static fn(object $row): array => [
                'id' => (int) $row->id,
                'preview_group' => (string) ($row->preview_group ?? 'Sample'),
                'translation_key' => (string) $row->translation_key,
                'chain_type' => (string) $row->chain_type,
                'chain_status' => (string) $row->chain_status,
                'finding_count' => (int) $row->finding_count,
                'timeline_event_count' => (int) $row->timeline_event_count,
                'shared_candidate_count' => (int) $row->shared_candidate_count,
                'bulk_review_count' => (int) $row->bulk_review_count,
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function timelineChainMainRow(string $table): ?array
    {
        if ($table !== 'translation_workbench_timeline_chains' || ! Schema::hasTable($table)) {
            return null;
        }

        $select = [
            'id',
            'translation_key',
            'chain_type',
            'chain_status',
            'root_key_id',
            'root_finding_id',
            'key_count',
            'finding_count',
            'active_finding_count',
            'obsolete_finding_count',
            'commented_out_finding_count',
            'review_count',
            'timeline_event_count',
            'lang_value_count',
            'shared_candidate_count',
            'bulk_review_count',
            'key_ids',
            'finding_ids',
            'review_ids',
            'lang_value_ids',
            'related_translation_keys',
            'relation_summary',
            'lang_value_summary',
            'timeline_event_summary',
            'timeline_event_ids',
            'meta',
            'first_seen_at',
            'last_seen_at',
            'created_at',
            'updated_at',
        ];
        $selectedId = $this->selectedTimelineChainPreviewId();
        $row = $selectedId
            ? DB::table($table)->where('id', $selectedId)->first($select)
            : null;

        if (! $row) {
            if ($selectedId !== null) {
                $this->timelineChainPreviewId = 'auto';
            }

            $row = DB::table($table)
                ->where('chain_status', 'active')
                ->whereIn('chain_type', ['bulk', 'shared', 'moved', 'single'])
                ->orderByRaw("CASE chain_type WHEN 'bulk' THEN 0 WHEN 'shared' THEN 1 WHEN 'moved' THEN 2 ELSE 3 END")
                ->orderByDesc('timeline_event_count')
                ->orderByDesc('finding_count')
                ->first($select);
        }

        if (! $row) {
            return null;
        }

        return [
            'id' => (int) $row->id,
            'translation_key' => (string) $row->translation_key,
            'chain_type' => (string) $row->chain_type,
            'chain_status' => (string) $row->chain_status,
            'root_key_id' => $row->root_key_id !== null ? (int) $row->root_key_id : null,
            'root_finding_id' => $row->root_finding_id !== null ? (int) $row->root_finding_id : null,
            'key_count' => (int) $row->key_count,
            'finding_count' => (int) $row->finding_count,
            'active_finding_count' => (int) $row->active_finding_count,
            'obsolete_finding_count' => (int) $row->obsolete_finding_count,
            'commented_out_finding_count' => (int) $row->commented_out_finding_count,
            'review_count' => (int) $row->review_count,
            'timeline_event_count' => (int) $row->timeline_event_count,
            'lang_value_count' => (int) $row->lang_value_count,
            'shared_candidate_count' => (int) $row->shared_candidate_count,
            'bulk_review_count' => (int) $row->bulk_review_count,
            'key_ids' => $this->decodeJsonArray($row->key_ids ?? null),
            'finding_ids' => $this->decodeJsonArray($row->finding_ids ?? null),
            'review_ids' => $this->decodeJsonArray($row->review_ids ?? null),
            'lang_value_ids' => $this->decodeJsonArray($row->lang_value_ids ?? null),
            'related_translation_keys' => $this->decodeJsonArray($row->related_translation_keys ?? null),
            'relation_summary' => $this->decodeJsonArray($row->relation_summary ?? null),
            'lang_value_summary' => $this->decodeJsonArray($row->lang_value_summary ?? null),
            'timeline_event_summary' => $this->decodeJsonArray($row->timeline_event_summary ?? null),
            'timeline_event_ids' => $this->decodeJsonArray($row->timeline_event_ids ?? null),
            'meta' => $this->decodeJsonArray($row->meta ?? null),
            'first_seen_at' => $row->first_seen_at,
            'last_seen_at' => $row->last_seen_at,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function timelineChainOriginRows(string $table): array
    {
        $mainRow = $this->timelineChainMainRow($table);

        if (
            $mainRow === null
            || empty($mainRow['root_key_id'])
            || ! Schema::hasTable('translation_workbench_findings')
        ) {
            return [];
        }

        $translationKey = (string) $mainRow['translation_key'];
        $trunk = 'key #' . $mainRow['root_key_id'];
        $bulkReviews = Schema::hasTable('translation_workbench_reviews')
            ? DB::table('translation_workbench_reviews')
                ->where('key_id', (int) $mainRow['root_key_id'])
                ->where('decision', 'translation_key_bulk_equalized')
                ->orderBy('reviewed_at')
                ->get(['id', 'finding_id', 'meta', 'reviewed_at', 'created_at'])
            : collect();

        $selectedFindingIds = $bulkReviews
            ->flatMap(function (object $review): array {
                $meta = $this->decodeJsonArray($review->meta ?? null);
                $ids = collect($meta['selected_finding_ids'] ?? [])
                    ->map(static fn(mixed $id): int => (int) $id)
                    ->filter(static fn(int $id): bool => $id > 0)
                    ->values()
                    ->all();

                if (! empty($review->finding_id)) {
                    $ids[] = (int) $review->finding_id;
                }

                return $ids;
            })
            ->unique()
            ->values();

        $findings = $selectedFindingIds->isNotEmpty()
            ? $this->timelineChainFindings($selectedFindingIds->all())
            : collect();

        $edgeFindings = collect([
            $findings->sortBy(fn(object $finding): string => (string) ($finding->first_seen_at ?: $finding->created_at))->first(),
            $findings->sortByDesc(fn(object $finding): string => (string) ($finding->first_seen_at ?: $finding->created_at))->first(),
        ])
            ->filter()
            ->unique('id')
            ->values();

        $bulkReviewByFindingId = collect();

        $bulkReviews->each(function (object $review) use ($bulkReviewByFindingId): void {
            $meta = $this->decodeJsonArray($review->meta ?? null);
            $ids = collect($meta['selected_finding_ids'] ?? [])
                ->map(static fn(mixed $id): int => (int) $id)
                ->filter(static fn(int $id): bool => $id > 0);

            if (! empty($review->finding_id)) {
                $ids->push((int) $review->finding_id);
            }

            $ids
                ->unique()
                ->each(static function (int $id) use ($bulkReviewByFindingId, $review): void {
                    if (! $bulkReviewByFindingId->has($id)) {
                        $bulkReviewByFindingId->put($id, $review);
                    }
                });
        });

        $bulkOriginRows = $edgeFindings
            ->map(function (object $finding) use ($bulkReviewByFindingId, $translationKey, $trunk): array {
                $root = 'finding #' . $finding->id;
                $literal = trim((string) ($finding->literal_text ?? $finding->literal_text_suggested ?? ''));
                $source = trim((string) ($finding->source_path ?? ''));
                $source .= ! empty($finding->source_line) ? ':' . (string) $finding->source_line : '';
                $bulkReview = $bulkReviewByFindingId->get((int) $finding->id);

                return [
                    'trunk' => $trunk,
                    'context' => $literal !== '' ? $literal : $source,
                    'source_path' => $source,
                    'translation_key' => $translationKey,
                    'first_timestamp' => $finding->first_seen_at ?: $finding->created_at,
                    'first_root' => $root,
                    'first_origin_key' => (string) ($finding->suggested_key ?? ''),
                    'first_event' => __('First seen as single finding'),
                    'first_state' => (string) $finding->status,
                    'first_color' => $this->timelineGraphColor('finding_event', 'sky'),
                    'last_timestamp' => $bulkReview?->reviewed_at ?: $bulkReview?->created_at,
                    'last_root' => $root,
                    'last_origin_key' => (string) ($finding->suggested_key ?? ''),
                    'last_event' => __('Last single state before shared key'),
                    'last_state' => $bulkReview ? ('review #' . $bulkReview->id) : __('No bulk review found'),
                    'last_color' => $bulkReview
                        ? $this->timelineGraphColor('review_event', 'amber')
                        : $this->timelineGraphColor('fallback', 'zinc'),
                ];
            })
            ->filter(static fn(array $row): bool => filled($row['first_timestamp'] ?? null) || filled($row['last_timestamp'] ?? null));

        return $bulkOriginRows
            ->merge($this->timelineChainSharedOriginRows($mainRow, $trunk))
            ->unique(static fn(array $row): string => implode('|', [
                (string) ($row['first_root'] ?? ''),
                (string) ($row['first_origin_key'] ?? ''),
                (string) ($row['last_state'] ?? ''),
            ]))
            ->sortBy('first_timestamp')
            ->values()
            ->all();
    }

    private function timelineChainFindings(array $findingIds)
    {
        $ids = collect($findingIds)
            ->map(static fn(mixed $id): int => (int) $id)
            ->filter(static fn(int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return DB::table('translation_workbench_findings as findings')
            ->leftJoin('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->whereIn('findings.id', $ids->all())
            ->get([
                'findings.id',
                'findings.status',
                'findings.suggested_key',
                'findings.literal_text',
                'findings.literal_text_suggested',
                'findings.first_seen_at',
                'findings.last_seen_at',
                'findings.created_at',
                'findings.updated_at',
                'source_files.path as source_path',
                'findings.source_line',
            ]);
    }

    private function timelineChainSharedOriginRows(array $mainRow, string $trunk)
    {
        $translationKey = (string) ($mainRow['translation_key'] ?? '');
        $sharedCandidates = collect(data_get($mainRow, 'meta.shared_candidates', []))
            ->filter(static fn(mixed $candidate): bool => is_array($candidate))
            ->values();

        if ($sharedCandidates->isEmpty()) {
            return collect();
        }

        $findingsById = $this->timelineChainFindings(
            $sharedCandidates
                ->pluck('finding_id')
                ->filter()
                ->all()
        )->keyBy('id');

        return $sharedCandidates
            ->map(function (array $candidate) use ($findingsById, $translationKey, $trunk): ?array {
                $findingId = (int) ($candidate['finding_id'] ?? 0);
                $finding = $findingId > 0 ? $findingsById->get($findingId) : null;
                $originKey = collect([
                    $candidate['current_translation_key'] ?? null,
                    $candidate['matched_translation_key'] ?? null,
                    $finding?->suggested_key ?? null,
                ])
                    ->map(static fn(mixed $key): string => trim((string) $key))
                    ->first(static fn(string $key): bool => $key !== '' && $key !== $translationKey);

                if (! filled($originKey)) {
                    return null;
                }

                $literal = trim((string) ($finding?->literal_text ?? $finding?->literal_text_suggested ?? ''));
                $source = trim((string) ($finding?->source_path ?? ''));
                $source .= ! empty($finding?->source_line) ? ':' . (string) $finding->source_line : '';

                return [
                    'trunk' => $trunk,
                    'context' => $literal !== '' ? $literal : $source,
                    'source_path' => $source,
                    'translation_key' => $translationKey,
                    'first_timestamp' => $finding?->first_seen_at ?: $finding?->created_at ?: ($mainRow['first_seen_at'] ?? null),
                    'first_root' => $findingId > 0 ? ('finding #' . $findingId) : ('candidate #' . (string) ($candidate['id'] ?? '?')),
                    'first_origin_key' => $originKey,
                    'first_event' => __('First seen as shared candidate origin'),
                    'first_state' => (string) ($finding?->status ?? $candidate['status'] ?? ''),
                    'first_color' => $this->timelineGraphColor('finding_event', 'sky'),
                    'last_timestamp' => $finding?->last_seen_at ?: $finding?->updated_at ?: ($mainRow['updated_at'] ?? null),
                    'last_root' => 'key #' . (string) ($candidate['matched_key_id'] ?? $mainRow['root_key_id'] ?? '?'),
                    'last_origin_key' => $originKey,
                    'last_event' => __('Mapped to shared key'),
                    'last_state' => 'candidate #' . (string) ($candidate['id'] ?? '?') . ' / ' . (string) ($candidate['status'] ?? ''),
                    'last_color' => $this->timelineGraphColor('merge', 'amber'),
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function timelineChainRootRows(string $table): array
    {
        $mainRow = $this->timelineChainMainRow($table);

        if ($mainRow === null) {
            return [];
        }

        $translationKey = (string) $mainRow['translation_key'];
        $trunk = ! empty($mainRow['root_key_id'])
            ? 'key #' . $mainRow['root_key_id']
            : __('No root key');
        $rows = collect();

        if (! empty($mainRow['root_key_id'])) {
            $rows->push([
                'timestamp' => $mainRow['updated_at'],
                'trunk' => $trunk,
                'branch' => __('Root'),
                'translation_key' => $translationKey,
                'event' => __('Current canonical root'),
                'state' => str((string) $mainRow['chain_type'])->headline() . ' / ' . str((string) $mainRow['chain_status'])->headline(),
                'color' => $this->timelineGraphColor('root_event', 'green'),
                'branch_color' => $this->timelineGraphColor('root_event', 'green'),
            ]);
        }

        if (! empty($mainRow['root_key_id']) && Schema::hasTable('translation_workbench_keys')) {
            $key = DB::table('translation_workbench_keys')->find((int) $mainRow['root_key_id']);

            if ($key) {
                $rows->push([
                    'timestamp' => $key->created_at,
                    'trunk' => $trunk,
                    'branch' => __('Root key'),
                    'translation_key' => $translationKey,
                    'event' => __('Key created'),
                    'state' => (string) $key->status,
                    'color' => $this->timelineGraphColor('key_event', 'violet'),
                    'branch_color' => $this->timelineGraphColor('key_event', 'violet'),
                ]);

                if ($key->reviewed_at) {
                    $rows->push([
                        'timestamp' => $key->reviewed_at,
                        'trunk' => $trunk,
                        'branch' => __('Root key'),
                        'translation_key' => $translationKey,
                        'event' => __('Key reviewed'),
                        'state' => (string) $key->review_status,
                        'color' => $this->timelineGraphColor('key_reviewed_event', 'green'),
                        'branch_color' => $this->timelineGraphColor('key_event', 'violet'),
                    ]);
                }

                if ($key->updated_at && (string) $key->updated_at !== (string) $key->created_at) {
                    $rows->push([
                        'timestamp' => $key->updated_at,
                        'trunk' => $trunk,
                        'branch' => __('Root key'),
                        'translation_key' => $translationKey,
                        'event' => __('Key updated'),
                        'state' => (string) $key->review_status,
                        'color' => $this->timelineGraphColor('key_updated_event', 'cyan'),
                        'branch_color' => $this->timelineGraphColor('key_event', 'violet'),
                    ]);
                }
            }
        }

        if (! empty($mainRow['root_finding_id']) && Schema::hasTable('translation_workbench_findings')) {
            $finding = DB::table('translation_workbench_findings')->find((int) $mainRow['root_finding_id']);

            if ($finding) {
                $rows->push([
                    'timestamp' => $finding->created_at,
                    'trunk' => $trunk,
                    'branch' => 'finding #' . $finding->id,
                    'translation_key' => $translationKey,
                    'event' => __('Finding created'),
                    'state' => (string) $finding->status,
                    'color' => $this->timelineGraphColor('finding_event', 'sky'),
                    'branch_color' => $this->timelineGraphColor('finding_event', 'sky'),
                ]);
            }
        }

        if (Schema::hasTable('translation_workbench_lang_values')) {
            DB::table('translation_workbench_lang_values')
                ->where('translation_key', $translationKey)
                ->orderBy('locale')
                ->get(['id', 'locale', 'status', 'created_at', 'updated_at'])
                ->each(function (object $langValue) use ($rows, $translationKey, $trunk): void {
                    $rows->push([
                        'timestamp' => $langValue->updated_at ?: $langValue->created_at,
                        'trunk' => $trunk,
                        'branch' => 'lang value #' . $langValue->id,
                        'translation_key' => $translationKey,
                        'event' => __('Lang value'),
                        'state' => trim((string) $langValue->locale . ' / ' . (string) $langValue->status),
                        'color' => $langValue->status === 'active'
                            ? $this->timelineGraphColor('lang_value_active_event', 'emerald')
                            : $this->timelineGraphColor('lang_value_inactive_event', 'zinc'),
                        'branch_color' => $langValue->status === 'active'
                            ? $this->timelineGraphColor('lang_value_active_event', 'emerald')
                            : $this->timelineGraphColor('lang_value_inactive_event', 'zinc'),
                    ]);
                });
        }

        if (! empty($mainRow['root_key_id']) && Schema::hasTable('translation_workbench_reviews')) {
            DB::table('translation_workbench_reviews')
                ->where('key_id', (int) $mainRow['root_key_id'])
                ->when(
                    ! empty($mainRow['root_finding_id']),
                    fn($query) => $query->where('finding_id', (int) $mainRow['root_finding_id']),
                )
                ->whereIn('decision', [
                    'translation_key_updated',
                    'translation_key_bulk_equalized',
                    'translation_values_saved',
                ])
                ->orderByDesc('reviewed_at')
                ->limit(12)
                ->get(['id', 'review_type', 'decision', 'reviewed_at', 'created_at'])
                ->each(function (object $review) use ($rows, $translationKey, $trunk): void {
                    $rows->push([
                        'timestamp' => $review->reviewed_at ?: $review->created_at,
                        'trunk' => $trunk,
                        'branch' => 'review #' . $review->id,
                        'translation_key' => $translationKey,
                        'event' => str((string) $review->decision)->replace('_', ' ')->headline()->toString(),
                        'state' => (string) $review->review_type,
                        'color' => $this->timelineGraphColor('review_event', 'amber'),
                        'branch_color' => $this->timelineGraphColor('review_event', 'amber'),
                    ]);
                });
        }

        return $rows
            ->filter(static fn(array $row): bool => filled($row['timestamp'] ?? null))
            ->unique(static fn(array $row): string => implode('|', [
                (string) $row['timestamp'],
                (string) $row['trunk'],
                (string) $row['branch'],
                (string) $row['event'],
                (string) $row['state'],
            ]))
            ->sortByDesc('timestamp')
            ->values()
            ->all();
    }

    /**
     * @return array<mixed>
     */
    private function decodeJsonArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function timelineGraphColor(string $key, string $fallback): string
    {
        return \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::dataDrivenString(
            'colors.' . $key,
            \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('colors.' . $key, $fallback),
        );
    }

    private function normalizedActiveTable(): string
    {
        return in_array($this->activeTable, $this->tables, true)
            ? $this->activeTable
            : $this->tables[0];
    }

    private function activeTableIndex(): int
    {
        $index = array_search($this->normalizedActiveTable(), $this->tables, true);

        return $index === false ? 0 : (int) $index;
    }

    private function dispatchActiveTableTabChanged(string $scroll = 'nearest'): void
    {
        $this->dispatch(
            'translation-workbench:raw-data-table-tab-changed',
            table: $this->normalizedActiveTable(),
            scroll: in_array($scroll, ['first', 'last', 'nearest'], true) ? $scroll : 'nearest',
        );
    }

    private function normalizedPerPage(): int
    {
        return in_array($this->perPage, [10, 25, 50, 100], true)
            ? $this->perPage
            : 25;
    }
}
