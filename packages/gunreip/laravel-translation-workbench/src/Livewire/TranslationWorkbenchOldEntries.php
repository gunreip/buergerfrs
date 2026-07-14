<?php

// packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchOldEntries.php

namespace Gunreip\TranslationWorkbench\Livewire;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Models\Locale;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Settings\AppGeneralSettings;
use App\Support\Locale\LocaleCode;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchDynamicValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchEntry;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchEvent;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery;
use Gunreip\TranslationWorkbench\Scanner\TranslationFingerprintFactory;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class TranslationWorkbenchOldEntries extends Component
{
    use InteractsWithUserSettings;
    use WithPagination;

    /**
     * @var array<int, string>
     */
    private const PERSISTED_STATE_PROPERTIES = [
        'search',
        'kind',
        'status',
        'dynamicFilter',
        'dynamicOptionFilter',
        'optionDiscoveryFilter',
        'workflowFilter',
        'perPage',
        'sortField',
        'sortDirection',
        'showDynamicTable',
        'showEntriesTable',
    ];

    public string $search = '';

    public string $kind = '';

    public string $status = '';

    public string $dynamicFilter = '';

    public string $dynamicOptionFilter = '';

    public string $optionDiscoveryFilter = '';

    public string $workflowFilter = '';

    public int $perPage = 50;

    public string $sortField = 'last_seen_at';

    public string $sortDirection = 'desc';

    public bool $showDynamicTable = true;

    public bool $showEntriesTable = true;

    public bool $reviewModalOpen = false;

    public ?int $reviewEntryId = null;

    public ?int $nextReviewEntryIdSnapshot = null;

    public bool $editModalOpen = false;

    public ?int $editEntryId = null;

    public ?int $nextTranslationEntryIdSnapshot = null;

    public ?string $targetTranslationValue = null;

    public bool $dynamicEditModalOpen = false;

    public ?int $dynamicEditEntryId = null;

    public ?int $nextDynamicTranslationEntryIdSnapshot = null;

    /**
     * @var array<int, string>
     */
    public array $dynamicValueKeys = [];

    /**
     * @var array<string, ?string>
     */
    public array $dynamicSourceValues = [];

    /**
     * @var array<string, ?string>
     */
    public array $dynamicTargetValues = [];

    /**
     * @var array<int, string>
     */
    public array $selectedTargetSubLocales = [];

    /**
     * @var array<string, ?string>
     */
    public array $targetSubTranslationValues = [];

    public bool $translationKeyModalOpen = false;

    public ?int $translationKeyEntryId = null;

    public ?string $translationKeyValue = null;

    public function mount(): void
    {
        $defaults = $this->uiStateDefaults();
        $state = $this->userSetting($this->uiStateSettingKey(), []);

        if (! is_array($state)) {
            $state = [];
        }

        $this->search = trim((string) ($state['search'] ?? $defaults['search'] ?? $this->search));
        $this->kind = trim((string) ($state['kind'] ?? $defaults['kind'] ?? $this->kind));
        $this->status = trim((string) ($state['status'] ?? $defaults['status'] ?? $this->status));
        $this->dynamicFilter = $this->normalizeDynamicFilter($state['dynamicFilter'] ?? $defaults['dynamicFilter'] ?? $this->dynamicFilter);
        $this->dynamicOptionFilter = $this->normalizeDynamicOptionFilter($state['dynamicOptionFilter'] ?? $defaults['dynamicOptionFilter'] ?? $this->dynamicOptionFilter);
        $this->optionDiscoveryFilter = $this->normalizeOptionDiscoveryFilter($state['optionDiscoveryFilter'] ?? $defaults['optionDiscoveryFilter'] ?? $this->optionDiscoveryFilter);
        $this->workflowFilter = $this->normalizeWorkflowFilter($state['workflowFilter'] ?? $defaults['workflowFilter'] ?? $this->workflowFilter);
        $this->perPage = $this->normalizePerPage($state['perPage'] ?? $defaults['perPage'] ?? $this->perPage);
        $this->sortField = $this->normalizeSortField($state['sortField'] ?? $defaults['sortField'] ?? $this->sortField);
        $this->sortDirection = $this->normalizeSortDirection($state['sortDirection'] ?? $defaults['sortDirection'] ?? $this->sortDirection);
        $this->showDynamicTable = (bool) ($state['showDynamicTable'] ?? $defaults['showDynamicTable'] ?? $this->showDynamicTable);
        $this->showEntriesTable = (bool) ($state['showEntriesTable'] ?? $defaults['showEntriesTable'] ?? $this->showEntriesTable);

        $this->setPage(1);
    }

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'kind', 'status', 'dynamicFilter', 'dynamicOptionFilter', 'optionDiscoveryFilter', 'workflowFilter', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, self::PERSISTED_STATE_PROPERTIES, true)) {
            $this->persistUiState();
        }
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->kind = '';
        $this->status = '';
        $this->dynamicFilter = '';
        $this->dynamicOptionFilter = '';
        $this->optionDiscoveryFilter = '';
        $this->workflowFilter = '';
        $this->perPage = 25;
        $this->showDynamicTable = true;
        $this->showEntriesTable = true;
        $this->resetPage();
        $this->persistUiState();
    }

    public function toggleDynamicTable(): void
    {
        $this->showDynamicTable = ! $this->showDynamicTable;
        $this->persistUiState();
    }

    public function toggleEntriesTable(): void
    {
        $this->showEntriesTable = ! $this->showEntriesTable;
        $this->persistUiState();
    }

    public function sortBy(string $field): void
    {
        if (! array_key_exists($field, $this->sortableFields())) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = $this->defaultSortDirection($field);
        }

        $this->resetPage();
        $this->persistUiState();
    }

    public function openReviewModal(int $entryId): void
    {
        $this->reviewEntryId = $entryId;
        $this->nextReviewEntryIdSnapshot = $this->nextReviewEntryIdFor($entryId, $this->reviewSequenceIds());
        $this->reviewModalOpen = true;
    }

    public function openReviewModalFromOptionDiscovery(int $discoveryId): void
    {
        $discovery = TranslationWorkbenchOptionDiscovery::query()->find($discoveryId);

        if (! $discovery) {
            Flux::toast(
                heading: __('Discovery missing'),
                text: __('The selected dynamic option discovery no longer exists.'),
                variant: 'warning',
            );

            return;
        }

        $entryId = $this->matchedEntryIdForOptionDiscovery($discovery);

        if ($entryId === null) {
            Flux::toast(
                heading: __('No review entry'),
                text: __('This dynamic option discovery is not matched to a workbench entry yet.'),
                variant: 'warning',
            );

            return;
        }

        $this->openReviewModal($entryId);
    }

    public function closeReviewModal(): void
    {
        $this->reviewModalOpen = false;
        $this->reviewEntryId = null;
        $this->nextReviewEntryIdSnapshot = null;
    }

    public function openEditModal(int $entryId): void
    {
        $entry = TranslationWorkbenchEntry::query()->findOrFail($entryId);

        if (blank($entry->translation_key)) {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Review this entry and set a translation key before editing translation values.'),
                variant: 'warning',
            );

            return;
        }

        if ((bool) $entry->is_dynamic_multi) {
            $this->openDynamicEditModal($entry->id);

            return;
        }

        $editLocales = $this->editLocales();
        $this->editEntryId = $entry->id;
        $this->targetTranslationValue = $this->editValues($entry, $editLocales)['target'];
        $this->targetSubTranslationValues = $this->targetSubTranslationValues($entry, $editLocales);
        $this->selectedTargetSubLocales = collect($this->targetSubTranslationValues)
            ->filter(fn(mixed $value): bool => $this->nullableString($value) !== null)
            ->keys()
            ->values()
            ->all();
        $this->nextTranslationEntryIdSnapshot = $this->nextTranslationEntryIdFor($entry->id, $this->translationSequenceIds());
        $this->reviewModalOpen = false;
        $this->editModalOpen = true;
    }

    public function openDynamicEditModal(int $entryId): void
    {
        $entry = TranslationWorkbenchEntry::query()->findOrFail($entryId);

        if (blank($entry->translation_key)) {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Review this entry and set a translation key before editing dynamic values.'),
                variant: 'warning',
            );

            return;
        }

        if (! (bool) $entry->is_dynamic_multi) {
            Flux::toast(
                heading: __('Dynamic multi missing'),
                text: __('This entry must be marked as dynamic multi before editing multiple dynamic values.'),
                variant: 'warning',
            );

            return;
        }

        $editLocales = $this->editLocales();
        $rows = $this->dynamicEditRows($entry, $editLocales);
        $this->dynamicEditEntryId = $entry->id;
        $this->dynamicValueKeys = $rows->pluck('value_key')->values()->all();
        $this->dynamicSourceValues = $rows->pluck('source')->values()->all();
        $this->dynamicTargetValues = $rows->pluck('target')->values()->all();
        $this->nextDynamicTranslationEntryIdSnapshot = $this->nextDynamicTranslationEntryIdFor($entry->id, $this->dynamicTranslationSequenceIds());
        $this->reviewModalOpen = false;
        $this->editModalOpen = false;
        $this->dynamicEditModalOpen = true;
    }

    public function openDynamicEditModalFromOptionDiscovery(int $discoveryId): void
    {
        $discovery = TranslationWorkbenchOptionDiscovery::query()->find($discoveryId);

        if (! $discovery) {
            Flux::toast(
                heading: __('Discovery missing'),
                text: __('The selected dynamic option discovery no longer exists.'),
                variant: 'warning',
            );

            return;
        }

        $entryId = $this->matchedEntryIdForOptionDiscovery($discovery);

        if ($entryId === null) {
            Flux::toast(
                heading: __('No dynamic entry'),
                text: __('This dynamic option discovery is not matched to a dynamic multi workbench entry yet.'),
                variant: 'warning',
            );

            return;
        }

        $this->openDynamicEditModal($entryId);
    }

    public function closeEditModal(): void
    {
        $this->editModalOpen = false;
        $this->editEntryId = null;
        $this->nextTranslationEntryIdSnapshot = null;
        $this->targetTranslationValue = null;
        $this->selectedTargetSubLocales = [];
        $this->targetSubTranslationValues = [];
    }

    public function closeDynamicEditModal(): void
    {
        $this->dynamicEditModalOpen = false;
        $this->dynamicEditEntryId = null;
        $this->nextDynamicTranslationEntryIdSnapshot = null;
        $this->dynamicValueKeys = [];
        $this->dynamicSourceValues = [];
        $this->dynamicTargetValues = [];
    }

    public function saveTranslationValue(): void
    {
        $entry = $this->editEntry();

        if (! $entry) {
            return;
        }

        $translationKey = $this->nullableString($entry->translation_key);

        if ($translationKey === null) {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Review this entry and set a translation key before editing translation values.'),
                variant: 'warning',
            );

            return;
        }

        $editLocales = $this->editLocales();
        $sourceLocale = (string) ($editLocales['source'] ?? 'en');
        $targetLocale = (string) ($editLocales['active'] ?? app()->getLocale());
        $editValues = $this->editValues($entry, $editLocales);
        $translationKeyRow = $this->translationKeyRowForEntry($entry, $translationKey);
        $savedLocales = [];

        if ($this->saveTranslationValueForLocale($entry, $translationKeyRow, $translationKey, $sourceLocale, $editValues['source'] ?? null)) {
            $savedLocales[] = $sourceLocale;
        }

        if ($this->saveTranslationValueForLocale($entry, $translationKeyRow, $translationKey, $targetLocale, $this->targetTranslationValue)) {
            $savedLocales[] = $targetLocale;
        }

        $availableSubLocales = collect((array) ($editLocales['sub'] ?? []))
            ->filter(static fn(mixed $locale): bool => is_string($locale) && trim($locale) !== '')
            ->map(static fn(string $locale): string => LocaleCode::normalize($locale))
            ->values();

        foreach ($this->selectedTargetSubLocales as $subLocale) {
            $subLocale = LocaleCode::normalize((string) $subLocale);

            if (! $availableSubLocales->contains($subLocale)) {
                continue;
            }

            if ($this->saveTranslationValueForLocale($entry, $translationKeyRow, $translationKey, $subLocale, $this->targetSubTranslationValues[$subLocale] ?? null)) {
                $savedLocales[] = $subLocale;
            }
        }

        if ($savedLocales === []) {
            Flux::toast(
                heading: __('No changes'),
                text: __('The translation values have not changed.'),
                variant: 'info',
            );

            return;
        }

        Flux::toast(
            heading: __('Translations saved'),
            text: __('The translation values have been saved.'),
            variant: 'success',
        );
    }

    public function saveDynamicTranslationValues(): void
    {
        $entry = $this->dynamicEditEntry();

        if (! $entry) {
            return;
        }

        if (blank($entry->translation_key) || ! (bool) $entry->is_dynamic_multi) {
            Flux::toast(
                heading: __('Dynamic multi missing'),
                text: __('Review this entry before editing dynamic values.'),
                variant: 'warning',
            );

            return;
        }

        if (! Schema::hasTable('translation_workbench_dynamic_values')) {
            Flux::toast(
                heading: __('Dynamic value storage missing'),
                text: __('Run the workbench migrations before saving dynamic translation values.'),
                variant: 'warning',
            );

            return;
        }

        $editLocales = $this->editLocales();
        $sourceLocale = (string) ($editLocales['source'] ?? 'en');
        $targetLocale = (string) ($editLocales['active'] ?? app()->getLocale());
        $savedValues = 0;

        foreach ($this->normalizedDynamicValueKeysByIndex() as $index => $valueKey) {
            if ($this->saveDynamicValueForLocale($entry, $valueKey, $sourceLocale, $this->dynamicSourceValues[$index] ?? null)) {
                $savedValues++;
            }

            if ($this->saveDynamicValueForLocale($entry, $valueKey, $targetLocale, $this->dynamicTargetValues[$index] ?? null)) {
                $savedValues++;
            }
        }

        if ($savedValues === 0) {
            Flux::toast(
                heading: __('No changes'),
                text: __('The dynamic translation values have not changed.'),
                variant: 'info',
            );

            return;
        }

        Flux::toast(
            heading: __('Dynamic values saved'),
            text: __('The dynamic translation values have been saved.'),
            variant: 'success',
        );
    }

    public function addDynamicValueRow(): void
    {
        $index = count($this->dynamicValueKeys) + 1;

        do {
            $valueKey = 'value_' . $index;
            $index++;
        } while (in_array($valueKey, $this->dynamicValueKeys, true));

        $this->dynamicValueKeys[] = $valueKey;
        $this->dynamicSourceValues[] = null;
        $this->dynamicTargetValues[] = null;
    }

    public function removeDynamicValueRow(int $index): void
    {
        if (! array_key_exists($index, $this->dynamicValueKeys)) {
            return;
        }

        unset($this->dynamicValueKeys[$index], $this->dynamicSourceValues[$index], $this->dynamicTargetValues[$index]);

        $this->dynamicValueKeys = array_values($this->dynamicValueKeys);
        $this->dynamicSourceValues = array_values($this->dynamicSourceValues);
        $this->dynamicTargetValues = array_values($this->dynamicTargetValues);
    }

    public function copyDynamicSourceToTargetValue(int $index): void
    {
        if (! array_key_exists($index, $this->dynamicValueKeys)) {
            return;
        }

        $sourceValue = $this->nullableString($this->dynamicSourceValues[$index] ?? null);

        if ($sourceValue === null) {
            Flux::toast(
                heading: __('No source value'),
                text: __('There is no source value to copy for this dynamic option.'),
                variant: 'warning',
            );

            return;
        }

        $this->dynamicTargetValues[$index] = $sourceValue;

        $this->dispatch('buergerfrs:focus-field-and-select', inputId: 'translation-workbench-dynamic-target-' . $index);
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
        $this->targetSubTranslationValues[$locale] ??= $this->targetSubTranslationValues($this->editEntry(), $this->editLocales())[$locale] ?? null;
    }

    public function copySourceToTargetValue(): void
    {
        $entry = $this->editEntry();

        if (! $entry) {
            return;
        }

        $sourceValue = $this->nullableString($this->editValues($entry, $this->editLocales())['source'] ?? null);

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

    public function openNextTranslationEntry(): void
    {
        $nextEntryId = $this->nextTranslationEntryId();

        if ($nextEntryId === null) {
            Flux::toast(
                heading: __('No next entry'),
                text: __('There is no next translation entry in the current list.'),
                variant: 'info',
            );

            return;
        }

        $this->openEditModal($nextEntryId);
    }

    public function openNextDynamicTranslationEntry(): void
    {
        $nextEntryId = $this->nextDynamicTranslationEntryId();

        if ($nextEntryId === null) {
            Flux::toast(
                heading: __('No next entry'),
                text: __('There is no next dynamic multi entry in the current list.'),
                variant: 'info',
            );

            return;
        }

        $this->openDynamicEditModal($nextEntryId);
    }

    public function openTranslationKeyModal(int $entryId): void
    {
        $entry = TranslationWorkbenchEntry::query()->findOrFail($entryId);

        $this->translationKeyEntryId = $entry->id;
        $this->translationKeyValue = $entry->translation_key;
        $this->translationKeyModalOpen = true;
    }

    public function closeTranslationKeyModal(): void
    {
        $this->translationKeyModalOpen = false;
        $this->translationKeyEntryId = null;
        $this->translationKeyValue = null;
    }

    public function copySuggestedKeyToTranslationKeyModal(): void
    {
        $entry = $this->translationKeyEntry();

        if (! $entry || ! $entry->suggested_key) {
            return;
        }

        $this->translationKeyValue = $entry->suggested_key;
    }

    public function acceptSuggestedKey(int $entryId): void
    {
        $entry = TranslationWorkbenchEntry::query()->find($entryId);

        if (! $entry || ! $entry->suggested_key) {
            Flux::toast(
                heading: __('Suggested key missing'),
                text: __('This workbench entry does not have a suggested key to accept.'),
                variant: 'warning',
            );

            return;
        }

        $newValues = [
            'translation_key' => $entry->suggested_key,
            'translation_key_source' => 'suggested',
            'review_status' => 'reviewed',
        ];
        $oldValues = $entry->only(array_keys($newValues));
        $changedValues = collect($newValues)
            ->filter(static fn(mixed $value, string $key): bool => ($oldValues[$key] ?? null) !== $value)
            ->all();

        if ($changedValues === []) {
            Flux::toast(
                heading: __('No changes'),
                text: __('The suggested key is already used as translation key.'),
                variant: 'warning',
            );

            return;
        }

        $entry->forceFill($changedValues)->save();

        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => 'suggested_key_accepted',
            'old_values' => collect($oldValues)->only(array_keys($changedValues))->all(),
            'new_values' => $changedValues,
            'context' => [
                'source' => 'translation-workbench:entries-review-modal',
                'suggested_key' => $entry->suggested_key,
                'changed_fields' => array_keys($changedValues),
            ],
            'created_by' => auth()->id(),
        ]);

        $this->reviewEntryId = $entry->id;
        $this->reviewModalOpen = true;

        Flux::toast(
            heading: __('Suggested key accepted'),
            text: __('The suggested key has been copied to the translation key.'),
            variant: 'success',
        );
    }

    public function setUiCandidate(int $entryId, bool $checked): void
    {
        $entry = TranslationWorkbenchEntry::query()->find($entryId);

        if (! $entry) {
            return;
        }

        if ($checked && blank($entry->translation_key)) {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Set a translation key before confirming this entry as UI candidate.'),
                variant: 'warning',
            );

            return;
        }

        $oldValues = $entry->only(['is_ui_key', 'is_ui_candidate_rejected', 'is_dynamic_key', 'is_dynamic_candidate_rejected', 'is_dynamic_multi', 'translation_key', 'translation_key_source', 'namespace', 'group']);
        $translationKey = $checked
            ? $this->uiTranslationKeyFor($entry)
            : $this->withoutTranslationKeyNamespace($entry->translation_key, 'ui');
        $newValues = [
            'is_ui_key' => $checked,
            'is_ui_candidate_rejected' => false,
            'is_dynamic_key' => false,
            'is_dynamic_candidate_rejected' => false,
            'is_dynamic_multi' => false,
            'translation_key' => $translationKey,
            'translation_key_source' => $checked
                ? 'manual'
                : $this->nullableString($entry->translation_key_source),
            'namespace' => $checked
                ? 'ui'
                : $this->nullableString($entry->namespace),
            'group' => $checked
                ? $this->groupFromTranslationKey($translationKey)
                : $this->nullableString($entry->group),
        ];
        $newValues = $checked ? $newValues : [
            'is_ui_key' => false,
            'is_ui_candidate_rejected' => false,
            'is_dynamic_candidate_rejected' => false,
            'translation_key' => $translationKey,
            'translation_key_source' => 'manual',
            'namespace' => null,
            'group' => null,
        ];

        $changedValues = collect($newValues)
            ->filter(static fn(mixed $value, string $key): bool => ($oldValues[$key] ?? null) !== $value)
            ->all();

        if ($changedValues === []) {
            return;
        }

        $entry->forceFill($changedValues)->save();

        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => 'ui_key_confirmation_updated',
            'old_values' => collect($oldValues)->only(array_keys($changedValues))->all(),
            'new_values' => $changedValues,
            'context' => [
                'source' => 'translation-workbench:entries-review-modal',
                'scan_candidate_type' => $entry->candidate_type,
                'confirmed' => $checked,
            ],
            'created_by' => auth()->id(),
        ]);

        $this->reviewEntryId = $entry->id;
        $this->reviewModalOpen = true;

        Flux::toast(
            heading: __('Candidate updated'),
            text: $checked
                ? __('The UI candidate has been confirmed and the translation key has been moved to the UI namespace.')
                : __('The explicit UI marker has been removed.'),
            variant: 'success',
        );
    }

    public function setUiCandidateRejected(int $entryId, bool $checked): void
    {
        $entry = TranslationWorkbenchEntry::query()->find($entryId);

        if (! $entry) {
            return;
        }

        $oldValues = $entry->only(['is_ui_key', 'is_ui_candidate_rejected', 'translation_key', 'translation_key_source', 'namespace', 'group']);
        $translationKey = $checked
            ? $this->withoutTranslationKeyNamespace($entry->translation_key, 'ui')
            : $this->nullableString($entry->translation_key);
        $shouldClearUiNamespace = $checked
            && ($translationKey !== $entry->translation_key || $this->nullableString($entry->namespace) === 'ui');
        $newValues = [
            'is_ui_key' => false,
            'is_ui_candidate_rejected' => $checked,
            'translation_key' => $translationKey,
            'translation_key_source' => $translationKey !== $entry->translation_key
                ? 'manual'
                : $this->nullableString($entry->translation_key_source),
            'namespace' => $shouldClearUiNamespace
                ? null
                : $this->nullableString($entry->namespace),
            'group' => $shouldClearUiNamespace
                ? null
                : $this->nullableString($entry->group),
        ];

        $changedValues = collect($newValues)
            ->filter(static fn(mixed $value, string $key): bool => ($oldValues[$key] ?? null) !== $value)
            ->all();

        if ($changedValues === []) {
            return;
        }

        $entry->forceFill($changedValues)->save();

        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => 'ui_candidate_rejection_updated',
            'old_values' => collect($oldValues)->only(array_keys($changedValues))->all(),
            'new_values' => $changedValues,
            'context' => [
                'source' => 'translation-workbench:entries-review-modal',
                'scan_candidate_type' => $entry->candidate_type,
                'rejected' => $checked,
            ],
            'created_by' => auth()->id(),
        ]);

        $this->reviewEntryId = $entry->id;
        $this->reviewModalOpen = true;

        Flux::toast(
            heading: __('UI candidate updated'),
            text: $checked
                ? __('The UI candidate has been explicitly rejected.')
                : __('The explicit UI rejection has been removed.'),
            variant: 'success',
        );
    }

    public function setDynamicCandidate(int $entryId, bool $checked): void
    {
        $entry = TranslationWorkbenchEntry::query()->find($entryId);

        if (! $entry) {
            return;
        }

        if ($checked && blank($entry->translation_key)) {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('Set a translation key before confirming this entry as dynamic.'),
                variant: 'warning',
            );

            return;
        }

        $oldValues = $entry->only(['is_ui_key', 'is_ui_candidate_rejected', 'is_dynamic_key', 'is_dynamic_candidate_rejected', 'is_dynamic_multi', 'translation_key', 'translation_key_source', 'namespace', 'group']);
        $translationKey = $checked
            ? $this->dynamicTranslationKeyFor($entry)
            : $this->withoutTranslationKeyNamespace($entry->translation_key, 'dynamic');
        $newValues = $checked
            ? [
                'is_ui_key' => false,
                'is_ui_candidate_rejected' => false,
                'is_dynamic_key' => true,
                'is_dynamic_candidate_rejected' => false,
                'translation_key' => $translationKey,
                'translation_key_source' => 'manual',
                'namespace' => 'dynamic',
                'group' => $this->groupFromTranslationKey($translationKey),
            ]
            : [
                'is_dynamic_key' => false,
                'is_dynamic_candidate_rejected' => false,
                'is_dynamic_multi' => false,
                'translation_key' => $translationKey,
                'translation_key_source' => 'manual',
                'namespace' => null,
                'group' => null,
            ];
        $changedValues = collect($newValues)
            ->filter(static fn(mixed $value, string $key): bool => ($oldValues[$key] ?? null) !== $value)
            ->all();

        if ($changedValues === []) {
            return;
        }

        $entry->forceFill($changedValues)->save();

        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => 'dynamic_key_confirmation_updated',
            'old_values' => collect($oldValues)->only(array_keys($changedValues))->all(),
            'new_values' => $changedValues,
            'context' => [
                'source' => 'translation-workbench:entries-review-modal',
                'scan_kind' => $entry->kind,
                'scan_entry_type' => $entry->entry_type,
                'scan_candidate_type' => $entry->candidate_type,
                'confirmed' => $checked,
            ],
            'created_by' => auth()->id(),
        ]);

        $this->reviewEntryId = $entry->id;
        $this->reviewModalOpen = true;

        Flux::toast(
            heading: __('Dynamic updated'),
            text: $checked
                ? __('The entry has been confirmed as dynamic.')
                : __('The explicit dynamic marker has been removed.'),
            variant: 'success',
        );
    }

    public function setDynamicCandidateRejected(int $entryId, bool $checked): void
    {
        $entry = TranslationWorkbenchEntry::query()->find($entryId);

        if (! $entry) {
            return;
        }

        $oldValues = $entry->only(['is_dynamic_key', 'is_dynamic_candidate_rejected', 'is_dynamic_multi', 'translation_key', 'translation_key_source', 'namespace', 'group']);
        $translationKey = $checked
            ? $this->withoutTranslationKeyNamespace($entry->translation_key, 'dynamic')
            : $this->nullableString($entry->translation_key);
        $shouldClearDynamicNamespace = $checked
            && ($translationKey !== $entry->translation_key || $this->nullableString($entry->namespace) === 'dynamic');
        $newValues = [
            'is_dynamic_key' => false,
            'is_dynamic_candidate_rejected' => $checked,
            'is_dynamic_multi' => false,
            'translation_key' => $translationKey,
            'translation_key_source' => $translationKey !== $entry->translation_key
                ? 'manual'
                : $this->nullableString($entry->translation_key_source),
            'namespace' => $shouldClearDynamicNamespace
                ? null
                : $this->nullableString($entry->namespace),
            'group' => $shouldClearDynamicNamespace
                ? null
                : $this->nullableString($entry->group),
        ];

        $changedValues = collect($newValues)
            ->filter(static fn(mixed $value, string $key): bool => ($oldValues[$key] ?? null) !== $value)
            ->all();

        if ($changedValues === []) {
            return;
        }

        $entry->forceFill($changedValues)->save();

        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => 'dynamic_candidate_rejection_updated',
            'old_values' => collect($oldValues)->only(array_keys($changedValues))->all(),
            'new_values' => $changedValues,
            'context' => [
                'source' => 'translation-workbench:entries-review-modal',
                'scan_kind' => $entry->kind,
                'scan_entry_type' => $entry->entry_type,
                'scan_candidate_type' => $entry->candidate_type,
                'rejected' => $checked,
            ],
            'created_by' => auth()->id(),
        ]);

        $this->reviewEntryId = $entry->id;
        $this->reviewModalOpen = true;

        Flux::toast(
            heading: __('Dynamic candidate updated'),
            text: $checked
                ? __('The dynamic candidate has been explicitly rejected.')
                : __('The explicit dynamic rejection has been removed.'),
            variant: 'success',
        );
    }

    public function setDynamicMultiCandidate(int $entryId, bool $checked): void
    {
        $entry = TranslationWorkbenchEntry::query()->find($entryId);

        if (! $entry) {
            return;
        }

        if ($checked && ! (bool) $entry->is_dynamic_key) {
            Flux::toast(
                heading: __('Dynamic missing'),
                text: __('Confirm this entry as dynamic before marking it as dynamic multi.'),
                variant: 'warning',
            );

            return;
        }

        $oldValues = $entry->only(['is_dynamic_multi']);
        $newValues = [
            'is_dynamic_multi' => $checked,
        ];
        $changedValues = collect($newValues)
            ->filter(static fn(mixed $value, string $key): bool => ($oldValues[$key] ?? null) !== $value)
            ->all();

        if ($changedValues === []) {
            return;
        }

        $entry->forceFill($changedValues)->save();

        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => 'dynamic_multi_confirmation_updated',
            'old_values' => collect($oldValues)->only(array_keys($changedValues))->all(),
            'new_values' => $changedValues,
            'context' => [
                'source' => 'translation-workbench:entries-review-modal',
                'scan_kind' => $entry->kind,
                'scan_entry_type' => $entry->entry_type,
                'scan_candidate_type' => $entry->candidate_type,
                'confirmed' => $checked,
            ],
            'created_by' => auth()->id(),
        ]);

        $this->reviewEntryId = $entry->id;
        $this->reviewModalOpen = true;

        Flux::toast(
            heading: __('Dynamic multi updated'),
            text: $checked
                ? __('The entry has been marked as dynamic multi.')
                : __('The explicit dynamic multi marker has been removed.'),
            variant: 'success',
        );
    }

    public function saveTranslationKeyModal(): void
    {
        $entry = $this->translationKeyEntry();

        if (! $entry) {
            return;
        }

        $this->translationKeyValue = $this->nullableString($this->translationKeyValue);

        $this->validate([
            'translationKeyValue' => ['nullable', 'string'],
        ]);

        $oldValue = $entry->translation_key;
        $oldSource = $entry->translation_key_source;
        $oldFlags = $entry->only(['is_ui_key', 'is_ui_candidate_rejected', 'is_dynamic_key', 'is_dynamic_candidate_rejected', 'is_dynamic_multi', 'namespace', 'group']);
        $newValue = $this->nullableString($this->translationKeyValue);
        $newSource = $this->translationKeySourceFor($entry, $newValue, 'manual');
        $flagUpdates = $newValue === null
            ? [
                'is_ui_key' => false,
                'is_ui_candidate_rejected' => false,
                'is_dynamic_key' => false,
                'is_dynamic_candidate_rejected' => false,
                'is_dynamic_multi' => false,
                'namespace' => null,
                'group' => null,
            ]
            : [];
        $changedFlagUpdates = collect($flagUpdates)
            ->filter(static fn(mixed $value, string $key): bool => ($oldFlags[$key] ?? null) !== $value)
            ->all();

        if ($oldValue === $newValue && $oldSource === $newSource && $changedFlagUpdates === []) {
            Flux::toast(
                heading: __('No changes'),
                text: __('The translation key has not changed.'),
                variant: 'warning',
            );

            return;
        }

        $entry->forceFill([
            'translation_key' => $newValue,
            'translation_key_source' => $newSource,
            ...$changedFlagUpdates,
        ])->save();

        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => 'translation_key_updated',
            'old_values' => [
                'translation_key' => $oldValue,
                'translation_key_source' => $oldSource,
                ...collect($oldFlags)->only(array_keys($changedFlagUpdates))->all(),
            ],
            'new_values' => [
                'translation_key' => $newValue,
                'translation_key_source' => $newSource,
                ...$changedFlagUpdates,
            ],
            'context' => [
                'source' => 'translation-workbench:translation-key-modal',
                'suggested_key' => $entry->suggested_key,
            ],
            'created_by' => auth()->id(),
        ]);

        $this->reviewEntryId = $entry->id;
        $this->reviewModalOpen = true;
        $this->closeTranslationKeyModal();

        Flux::toast(
            heading: __('Translation key saved'),
            text: __('The translation key has been updated.'),
            variant: 'success',
        );
    }

    public function deleteFirstTranslationKeySegment(): void
    {
        $entry = $this->translationKeyEntry();

        if (! $entry) {
            return;
        }

        $oldValue = $this->nullableString($this->translationKeyValue)
            ?? $this->nullableString($entry->translation_key);

        if ($oldValue === null) {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('There is no translation key segment to delete.'),
                variant: 'warning',
            );

            return;
        }

        $segments = array_values(array_filter(explode('.', $oldValue), static fn(string $segment): bool => trim($segment) !== ''));

        if (count($segments) < 2) {
            Flux::toast(
                heading: __('Segment cannot be deleted'),
                text: __('The translation key needs at least two segments.'),
                variant: 'warning',
            );

            return;
        }

        $deletedSegment = array_shift($segments);
        $newValue = implode('.', $segments);
        $oldSource = $entry->translation_key_source;
        $oldNamespace = $entry->namespace;
        $oldGroup = $entry->group;
        $oldDeletedSegments = is_array($entry->deleted_segments) ? $entry->deleted_segments : [];
        $deletedSegments = [
            ...$oldDeletedSegments,
            [
                'segment' => $deletedSegment,
                'old_key' => $oldValue,
                'new_key' => $newValue,
                'deleted_at' => now()->toISOString(),
                'deleted_by_user_id' => auth()->id(),
            ],
        ];
        $newSource = $this->translationKeySourceFor($entry, $newValue, 'manual');
        $newNamespace = $this->namespaceFromTranslationKey($newValue);
        $newGroup = $this->groupFromTranslationKey($newValue);

        $entry->forceFill([
            'translation_key' => $newValue,
            'translation_key_source' => $newSource,
            'namespace' => $newNamespace,
            'group' => $newGroup,
            'deleted_segments' => $deletedSegments,
        ])->save();

        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => 'translation_key_segment_deleted',
            'old_values' => [
                'translation_key' => $oldValue,
                'translation_key_source' => $oldSource,
                'namespace' => $oldNamespace,
                'group' => $oldGroup,
                'deleted_segments' => $oldDeletedSegments,
            ],
            'new_values' => [
                'translation_key' => $newValue,
                'translation_key_source' => $newSource,
                'namespace' => $newNamespace,
                'group' => $newGroup,
                'deleted_segments' => $deletedSegments,
            ],
            'context' => [
                'source' => 'translation-workbench:translation-key-modal',
                'deleted_segment' => $deletedSegment,
            ],
            'created_by' => auth()->id(),
        ]);

        $this->translationKeyValue = $newValue;
        $this->reviewEntryId = $entry->id;
        $this->reviewModalOpen = true;

        Flux::toast(
            heading: __('Translation key segment deleted'),
            text: __('The first translation key segment has been removed.'),
            variant: 'success',
        );
    }

    public function restoreLastDeletedTranslationKeySegment(): void
    {
        $entry = $this->translationKeyEntry();

        if (! $entry) {
            return;
        }

        $oldDeletedSegments = is_array($entry->deleted_segments) ? $entry->deleted_segments : [];
        $originalDeletedSegments = $oldDeletedSegments;
        $restoredEntry = array_pop($oldDeletedSegments);
        $restoredSegment = is_array($restoredEntry)
            ? $this->nullableString($restoredEntry['segment'] ?? null)
            : $this->nullableString($restoredEntry);

        if ($restoredSegment === null) {
            Flux::toast(
                heading: __('No segment to restore'),
                text: __('There is no deleted translation key segment available.'),
                variant: 'warning',
            );

            return;
        }

        $oldValue = $this->nullableString($this->translationKeyValue)
            ?? $this->nullableString($entry->translation_key);

        if ($oldValue === null) {
            Flux::toast(
                heading: __('Translation key missing'),
                text: __('There is no translation key to restore the segment into.'),
                variant: 'warning',
            );

            return;
        }

        $newValue = $restoredSegment . '.' . trim($oldValue, '.');
        $oldSource = $entry->translation_key_source;
        $oldNamespace = $entry->namespace;
        $oldGroup = $entry->group;
        $newSource = $this->translationKeySourceFor($entry, $newValue, 'manual');
        $newNamespace = $this->namespaceFromTranslationKey($newValue);
        $newGroup = $this->groupFromTranslationKey($newValue);

        $entry->forceFill([
            'translation_key' => $newValue,
            'translation_key_source' => $newSource,
            'namespace' => $newNamespace,
            'group' => $newGroup,
            'deleted_segments' => $oldDeletedSegments !== [] ? $oldDeletedSegments : null,
        ])->save();

        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => 'translation_key_segment_restored',
            'old_values' => [
                'translation_key' => $oldValue,
                'translation_key_source' => $oldSource,
                'namespace' => $oldNamespace,
                'group' => $oldGroup,
                'deleted_segments' => $originalDeletedSegments,
            ],
            'new_values' => [
                'translation_key' => $newValue,
                'translation_key_source' => $newSource,
                'namespace' => $newNamespace,
                'group' => $newGroup,
                'deleted_segments' => $oldDeletedSegments,
            ],
            'context' => [
                'source' => 'translation-workbench:translation-key-modal',
                'restored_segment' => $restoredSegment,
            ],
            'created_by' => auth()->id(),
        ]);

        $this->translationKeyValue = $newValue;
        $this->reviewEntryId = $entry->id;
        $this->reviewModalOpen = true;

        Flux::toast(
            heading: __('Translation key segment restored'),
            text: __('The deleted translation key segment has been restored.'),
            variant: 'success',
        );
    }

    public function render(): View
    {
        $baseQuery = TranslationWorkbenchEntry::query();
        $query = $this->filteredQuery();
        $optionDiscoveryTableExists = Schema::hasTable('translation_workbench_option_discoveries');
        $optionDiscoveryQuery = $this->optionDiscoveryQuery();
        $canCountOccurrences = Schema::hasTable('translation_workbench_occurrences');
        $reviewEntry = $this->reviewEntry($canCountOccurrences);
        $editEntry = $this->editEntry();
        $dynamicEditEntry = $this->dynamicEditEntry();
        $editLocales = $this->editLocales();
        $editValues = $this->editValues($editEntry, $editLocales);
        $dynamicEditRows = $this->dynamicEditRows($dynamicEditEntry, $editLocales);
        $translationKeyEntry = $this->translationKeyEntry();
        $entries = $this->withOccurrenceCounts($this->applySorting($query), $canCountOccurrences)
            ->paginate($this->normalizedPerPage());
        $optionDiscoveries = $optionDiscoveryTableExists
            ? $optionDiscoveryQuery
                ->with('matchedEntry:id,suggested_key,translation_key,is_dynamic_multi')
                ->orderByDesc('last_seen_at')
                ->orderByDesc('id')
                ->limit(50)
                ->get()
            : collect();
        $entryWorkflowStates = $this->entryWorkflowStates($entries->getCollection(), $editLocales);

        return view('translation-workbench::livewire.old.entries', [
            'entries' => $entries,
            'total' => (clone $baseQuery)->count(),
            'filteredTotal' => (clone $query)->count(),
            'occurrenceCounts' => $canCountOccurrences ? [
                'total' => TranslationWorkbenchEntry::query()->join('translation_workbench_occurrences', 'translation_workbench_occurrences.entry_id', '=', 'translation_workbench_entries.id')->count(),
                'active' => TranslationWorkbenchEntry::query()->join('translation_workbench_occurrences', 'translation_workbench_occurrences.entry_id', '=', 'translation_workbench_entries.id')->where('translation_workbench_occurrences.status', 'active')->count(),
                'stale' => TranslationWorkbenchEntry::query()->join('translation_workbench_occurrences', 'translation_workbench_occurrences.entry_id', '=', 'translation_workbench_entries.id')->where('translation_workbench_occurrences.status', 'stale')->count(),
            ] : [
                'total' => 0,
                'active' => 0,
                'stale' => 0,
            ],
            'kindCounts' => TranslationWorkbenchEntry::query()
                ->selectRaw('kind, count(*) as total')
                ->groupBy('kind')
                ->pluck('total', 'kind')
                ->map(fn($value) => (int) $value)
                ->all(),
            'statusCounts' => TranslationWorkbenchEntry::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->map(fn($value) => (int) $value)
                ->all(),
            'dynamicCounts' => [
                'dynamic' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query): void {
                        $this->applyDynamicFilter($query, 'dynamic');
                    })
                    ->count(),
                'dynamic_multi' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query): void {
                        $this->applyDynamicFilter($query, 'dynamic_multi');
                    })
                    ->count(),
                'not_dynamic' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query): void {
                        $this->applyDynamicFilter($query, 'not_dynamic');
                    })
                    ->count(),
            ],
            'dynamicOptionCounts' => [
                'discovered' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query): void {
                        $this->applyDynamicOptionFilter($query, 'discovered');
                    })
                    ->count(),
                'plain_label' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query): void {
                        $this->applyDynamicOptionFilter($query, 'plain_label');
                    })
                    ->count(),
                'translated_label' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query): void {
                        $this->applyDynamicOptionFilter($query, 'translated_label');
                    })
                    ->count(),
                'unresolved_source' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query): void {
                        $this->applyDynamicOptionFilter($query, 'unresolved_source');
                    })
                    ->count(),
                'hardcoded_source' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query): void {
                        $this->applyDynamicOptionFilter($query, 'hardcoded_source');
                    })
                    ->count(),
            ],
            'optionDiscoveryTableExists' => $optionDiscoveryTableExists,
            'optionDiscoveries' => $optionDiscoveries,
            'optionDiscoveryCounts' => $optionDiscoveryTableExists ? [
                'total' => TranslationWorkbenchOptionDiscovery::query()->count(),
                'matched' => TranslationWorkbenchOptionDiscovery::query()->whereNotNull('matched_entry_id')->count(),
                'unmatched' => TranslationWorkbenchOptionDiscovery::query()->whereNull('matched_entry_id')->count(),
                'plain_label' => TranslationWorkbenchOptionDiscovery::query()->where('label_usage', 'plain_label')->count(),
                'translated_label' => TranslationWorkbenchOptionDiscovery::query()->where('label_usage', 'translated_label')->count(),
                'unresolved_source' => TranslationWorkbenchOptionDiscovery::query()->where('source_type', 'unresolved')->count(),
                'hardcoded_source' => TranslationWorkbenchOptionDiscovery::query()->where('source_type', 'hardcoded_public_array')->count(),
            ] : [
                'total' => 0,
                'matched' => 0,
                'unmatched' => 0,
                'plain_label' => 0,
                'translated_label' => 0,
                'unresolved_source' => 0,
                'hardcoded_source' => 0,
            ],
            'workflowCounts' => [
                'ready_for_edit' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query) use ($editLocales): void {
                        $this->applyWorkflowFilter($query, 'ready_for_edit', $editLocales);
                    })
                    ->count(),
                'has_key' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query): void {
                        $this->applyWorkflowFilter($query, 'has_key');
                    })
                    ->count(),
                'missing_key' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query): void {
                        $this->applyWorkflowFilter($query, 'missing_key');
                    })
                    ->count(),
                'editable' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query): void {
                        $this->applyWorkflowFilter($query, 'editable');
                    })
                    ->count(),
                'source_exists' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query) use ($editLocales): void {
                        $this->applyWorkflowFilter($query, 'source_exists', $editLocales);
                    })
                    ->count(),
                'source_saved' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query) use ($editLocales): void {
                        $this->applyWorkflowFilter($query, 'source_saved', $editLocales);
                    })
                    ->count(),
                'target_exists' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query) use ($editLocales): void {
                        $this->applyWorkflowFilter($query, 'target_exists', $editLocales);
                    })
                    ->count(),
                'target_missing' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query) use ($editLocales): void {
                        $this->applyWorkflowFilter($query, 'target_missing', $editLocales);
                    })
                    ->count(),
                'has_deleted_segments' => TranslationWorkbenchEntry::query()
                    ->where(function (Builder $query): void {
                        $this->applyWorkflowFilter($query, 'has_deleted_segments');
                    })
                    ->count(),
            ],
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'canCountOccurrences' => $canCountOccurrences,
            'reviewEntry' => $reviewEntry,
            'editEntry' => $editEntry,
            'dynamicEditEntry' => $dynamicEditEntry,
            'editLocales' => $editLocales,
            'editValues' => $editValues,
            'dynamicEditRows' => $dynamicEditRows,
            'entryWorkflowStates' => $entryWorkflowStates,
            'translationKeyEntry' => $translationKeyEntry,
            'nextReviewEntryId' => $this->nextReviewEntryId(),
            'nextTranslationEntryId' => $this->nextTranslationEntryId(),
            'nextDynamicTranslationEntryId' => $this->nextDynamicTranslationEntryId(),
        ]);
    }

    private function withOccurrenceCounts(Builder $query, bool $canCountOccurrences): Builder
    {
        if (! $canCountOccurrences) {
            return $query;
        }

        return $query->withCount([
            'occurrences',
            'occurrences as active_occurrences_count' => fn(Builder $query) => $query->where('status', 'active'),
            'occurrences as stale_occurrences_count' => fn(Builder $query) => $query->where('status', 'stale'),
        ]);
    }

    private function reviewEntry(bool $canCountOccurrences): ?TranslationWorkbenchEntry
    {
        if (! $this->reviewModalOpen || $this->reviewEntryId === null) {
            return null;
        }

        $query = TranslationWorkbenchEntry::query();

        if ($canCountOccurrences) {
            $query->with([
                'occurrences' => fn($query) => $query
                    ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                    ->orderBy('source_path')
                    ->orderBy('source_line'),
            ])->withCount([
                'occurrences',
                'occurrences as active_occurrences_count' => fn(Builder $query) => $query->where('status', 'active'),
                'occurrences as stale_occurrences_count' => fn(Builder $query) => $query->where('status', 'stale'),
            ]);
        }

        return $query->find($this->reviewEntryId);
    }

    private function editEntry(): ?TranslationWorkbenchEntry
    {
        if (! $this->editModalOpen || $this->editEntryId === null) {
            return null;
        }

        return TranslationWorkbenchEntry::query()->find($this->editEntryId);
    }

    private function dynamicEditEntry(): ?TranslationWorkbenchEntry
    {
        if (! $this->dynamicEditModalOpen || $this->dynamicEditEntryId === null) {
            return null;
        }

        return TranslationWorkbenchEntry::query()->find($this->dynamicEditEntryId);
    }

    private function translationKeyEntry(): ?TranslationWorkbenchEntry
    {
        if (! $this->translationKeyModalOpen || $this->translationKeyEntryId === null) {
            return null;
        }

        return TranslationWorkbenchEntry::query()->find($this->translationKeyEntryId);
    }

    /**
     * @return array{source: string, active: string, sub: array<int, string>}
     */
    private function editLocales(): array
    {
        $activeLocale = LocaleCode::normalize((string) (app(AppGeneralSettings::class)->locale ?? app()->getLocale()));
        $activeLocale = $activeLocale !== '' ? $activeLocale : 'de';
        $activeLanguage = (string) (LocaleCode::parts($activeLocale)['language'] ?? $activeLocale);
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
            'source' => 'en',
            'active' => $activeLocale,
            'sub' => $activeSubLocales,
        ];
    }

    /**
     * @param  array{source: string, active: string, sub: array<int, string>}  $editLocales
     * @return array{source: ?string, target: ?string, source_exists: bool, target_exists: bool, source_origin: string}
     */
    private function editValues(?TranslationWorkbenchEntry $entry, array $editLocales): array
    {
        if (! $entry) {
            return [
                'source' => null,
                'target' => null,
                'source_exists' => false,
                'target_exists' => false,
                'source_origin' => 'missing',
            ];
        }

        $sourceLocale = (string) ($editLocales['source'] ?? 'en');
        $activeLocale = (string) ($editLocales['active'] ?? app()->getLocale());
        $translationKey = $this->nullableString($entry->translation_key);
        $sourceWorkbenchValue = $entry->values()
            ->where('value_key', "source:{$sourceLocale}")
            ->value('native_label');
        $sourceTranslationValue = null;
        $targetTranslationValue = null;

        if ($translationKey !== null) {
            $sourceTranslationValue = $this->translationValueForKeyAndLocale($translationKey, $sourceLocale);
            $targetTranslationValue = $this->translationValueForKeyAndLocale($translationKey, $activeLocale);
        }

        $literalText = $this->nullableString($entry->literal_text);
        $literalTextSuggested = $this->nullableString($entry->literal_text_suggested);
        $sourceOrigin = match (true) {
            $sourceTranslationValue !== null => 'translation_value',
            $sourceWorkbenchValue !== null => 'workbench_value',
            $literalText !== null => 'literal_text',
            $literalTextSuggested !== null => 'literal_text_suggested',
            default => 'missing',
        };
        $sourceValue = $sourceTranslationValue
            ?? $sourceWorkbenchValue
            ?? $literalText
            ?? $literalTextSuggested;

        return [
            'source' => $sourceValue,
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
    private function targetSubTranslationValues(?TranslationWorkbenchEntry $entry, array $editLocales): array
    {
        $subLocales = collect((array) ($editLocales['sub'] ?? []))
            ->filter(static fn(mixed $locale): bool => is_string($locale) && trim($locale) !== '')
            ->map(static fn(string $locale): string => LocaleCode::normalize($locale))
            ->filter(static fn(string $locale): bool => $locale !== '')
            ->unique()
            ->values();

        if (! $entry) {
            return $subLocales
                ->mapWithKeys(static fn(string $locale): array => [$locale => null])
                ->all();
        }

        $translationKey = $this->nullableString($entry->translation_key);

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

    private function saveTranslationValueForLocale(
        TranslationWorkbenchEntry $entry,
        TranslationKey $translationKeyRow,
        string $translationKey,
        string $locale,
        mixed $value,
    ): bool {
        $locale = LocaleCode::normalize($locale);

        if ($locale === '') {
            return false;
        }

        $newValue = $this->nullableString($value);
        $translationValue = TranslationValue::query()
            ->where('translation_key_id', $translationKeyRow->id)
            ->where('locale', $locale)
            ->first();
        $oldValues = $translationValue?->only(['value', 'status', 'source', 'reviewed_at', 'reviewed_by_user_id']) ?? [];
        $oldValue = $this->nullableString($oldValues['value'] ?? null);

        if ($oldValue === $newValue) {
            return false;
        }

        $attributes = [
            'value' => $newValue,
            'status' => $newValue !== null ? 'ok' : 'missing',
            'source' => 'translation_workbench',
            'reviewed_at' => $newValue !== null ? now() : null,
            'reviewed_by_user_id' => $newValue !== null ? auth()->id() : null,
        ];

        $translationValue = TranslationValue::query()->updateOrCreate(
            [
                'translation_key_id' => $translationKeyRow->id,
                'locale' => $locale,
            ],
            $attributes,
        );

        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => 'translation_value_saved',
            'old_values' => $oldValues,
            'new_values' => [
                'translation_key_id' => $translationKeyRow->id,
                'translation_value_id' => $translationValue->id,
                'translation_key' => $translationKey,
                'locale' => $locale,
                ...$attributes,
            ],
            'context' => [
                'source' => 'translation-workbench:entries-edit-modal',
            ],
        ]);

        return true;
    }

    /**
     * @param  array{source: string, active: string, sub: array<int, string>}  $editLocales
     * @return Collection<int, array{value_key: string, source: ?string, target: ?string, source_exists: bool, target_exists: bool}>
     */
    private function dynamicEditRows(?TranslationWorkbenchEntry $entry, array $editLocales): Collection
    {
        if (! $entry) {
            return collect();
        }

        if ($this->dynamicEditEntryId === $entry->id && $this->dynamicValueKeys !== []) {
            return collect($this->dynamicValueKeys)
                ->map(function (mixed $valueKey, int $index): ?array {
                    $valueKey = $this->normalizedDynamicValueKey($valueKey);

                    if ($valueKey === null) {
                        return null;
                    }

                    return [
                        'value_key' => $valueKey,
                        'source' => $this->nullableString($this->dynamicSourceValues[$index] ?? null),
                        'target' => $this->nullableString($this->dynamicTargetValues[$index] ?? null),
                        'source_exists' => filled($this->dynamicSourceValues[$index] ?? null),
                        'target_exists' => filled($this->dynamicTargetValues[$index] ?? null),
                    ];
                })
                ->filter()
                ->values();
        }

        $sourceLocale = (string) ($editLocales['source'] ?? 'en');
        $targetLocale = (string) ($editLocales['active'] ?? app()->getLocale());
        $sourceLangValues = $this->dynamicLangValues($entry, $sourceLocale);
        $targetLangValues = $this->dynamicLangValues($entry, $targetLocale);
        $storedValues = Schema::hasTable('translation_workbench_dynamic_values')
            ? TranslationWorkbenchDynamicValue::query()
                ->where('entry_id', $entry->id)
                ->whereIn('locale', [$sourceLocale, $targetLocale])
                ->get()
                ->groupBy('value_key')
            : collect();
        $workbenchOptionValues = $entry->values()
            ->whereNot('value_key', 'like', 'source:%')
            ->pluck('native_label', 'value_key');

        $valueKeys = collect()
            ->merge(array_keys($sourceLangValues))
            ->merge(array_keys($targetLangValues))
            ->merge($storedValues->keys())
            ->merge($workbenchOptionValues->keys())
            ->merge($this->dynamicValueKeys)
            ->map(fn(mixed $valueKey): ?string => $this->normalizedDynamicValueKey($valueKey))
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return $valueKeys
            ->map(function (string $valueKey) use ($sourceLocale, $targetLocale, $sourceLangValues, $targetLangValues, $storedValues, $workbenchOptionValues): array {
                $storedGroup = $storedValues->get($valueKey, collect());
                $storedSource = $storedGroup->firstWhere('locale', $sourceLocale);
                $storedTarget = $storedGroup->firstWhere('locale', $targetLocale);
                $sourceValue = $storedSource?->value
                    ?? $sourceLangValues[$valueKey]
                    ?? $workbenchOptionValues->get($valueKey);
                $targetValue = $storedTarget?->value
                    ?? $targetLangValues[$valueKey]
                    ?? null;

                return [
                    'value_key' => $valueKey,
                    'source' => $this->nullableString($sourceValue),
                    'target' => $this->nullableString($targetValue),
                    'source_exists' => $storedSource !== null || array_key_exists($valueKey, $sourceLangValues),
                    'target_exists' => $storedTarget !== null || array_key_exists($valueKey, $targetLangValues),
                ];
            })
            ->values();
    }

    private function saveDynamicValueForLocale(
        TranslationWorkbenchEntry $entry,
        string $valueKey,
        string $locale,
        mixed $value,
    ): bool {
        $valueKey = $this->normalizedDynamicValueKey($valueKey);
        $locale = LocaleCode::normalize($locale);

        if ($valueKey === null || $locale === '') {
            return false;
        }

        $newValue = $this->nullableString($value);
        $dynamicValue = TranslationWorkbenchDynamicValue::query()
            ->where('entry_id', $entry->id)
            ->where('value_key', $valueKey)
            ->where('locale', $locale)
            ->first();
        $oldValues = $dynamicValue?->only(['value', 'status', 'source', 'reviewed_at', 'reviewed_by_user_id']) ?? [];
        $oldValue = $this->nullableString($oldValues['value'] ?? null);

        if ($oldValue === $newValue) {
            return false;
        }

        $attributes = [
            'value' => $newValue,
            'status' => $newValue !== null ? 'ok' : 'missing',
            'source' => 'translation_workbench',
            'reviewed_at' => $newValue !== null ? now() : null,
            'reviewed_by_user_id' => $newValue !== null ? auth()->id() : null,
            'meta' => [
                'translation_key' => $entry->translation_key,
                'namespace' => $entry->namespace,
                'group' => $entry->group,
            ],
        ];

        $dynamicValue = TranslationWorkbenchDynamicValue::query()->updateOrCreate(
            [
                'entry_id' => $entry->id,
                'value_key' => $valueKey,
                'locale' => $locale,
            ],
            $attributes,
        );

        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => 'dynamic_translation_value_saved',
            'old_values' => $oldValues,
            'new_values' => [
                'dynamic_value_id' => $dynamicValue->id,
                'translation_key' => $entry->translation_key,
                'value_key' => $valueKey,
                'locale' => $locale,
                ...$attributes,
            ],
            'context' => [
                'source' => 'translation-workbench:entries-dynamic-edit-modal',
            ],
            'created_by' => auth()->id(),
        ]);

        return true;
    }

    /**
     * @return array<string, string>
     */
    private function dynamicLangValues(TranslationWorkbenchEntry $entry, string $locale): array
    {
        $translationKey = $this->nullableString($entry->translation_key);
        $locale = LocaleCode::normalize($locale);

        if ($translationKey === null || $locale === '') {
            return [];
        }

        [$namespace, $langKey] = $this->splitTranslationKey($translationKey);

        if ($namespace === '' || $langKey === '') {
            return [];
        }

        $path = lang_path("{$locale}/{$namespace}.php");

        if (! is_file($path)) {
            return [];
        }

        $lines = require $path;

        if (! is_array($lines)) {
            return [];
        }

        $value = data_get($lines, $langKey);

        if (! is_array($value)) {
            return [];
        }

        return collect(Arr::dot($value))
            ->mapWithKeys(function (mixed $label, string $valueKey): array {
                return is_scalar($label)
                    ? [$valueKey => (string) $label]
                    : [];
            })
            ->all();
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitTranslationKey(string $translationKey): array
    {
        $segments = explode('.', trim($translationKey, '.'));
        $namespace = (string) array_shift($segments);

        return [$namespace, implode('.', $segments)];
    }

    /**
     * @param  Collection<int, TranslationWorkbenchEntry>  $entries
     * @param  array{source: string, active: string, sub: array<int, string>}  $editLocales
     * @return array<int, array{has_key: bool, source_exists: bool, target_exists: bool, has_deleted_segments: bool}>
     */
    private function entryWorkflowStates(Collection $entries, array $editLocales): array
    {
        $translationKeys = $entries
            ->pluck('translation_key')
            ->filter(static fn(mixed $key): bool => is_string($key) && trim($key) !== '')
            ->map(static fn(string $key): string => trim($key))
            ->unique()
            ->values();

        $translationKeyIds = $translationKeys->isEmpty()
            ? collect()
            : TranslationKey::query()
                ->whereIn('key', $translationKeys->all())
                ->pluck('id', 'key');

        $sourceLocale = (string) ($editLocales['source'] ?? 'en');
        $targetLocale = (string) ($editLocales['active'] ?? app()->getLocale());
        $sourceTranslationKeyIds = $translationKeyIds->isEmpty()
            ? collect()
            : TranslationValue::query()
                ->whereIn('translation_key_id', $translationKeyIds->values()->all())
                ->whereIn('locale', $this->localeCandidates($sourceLocale))
                ->pluck('translation_key_id')
                ->flip();
        $targetTranslationKeyIds = $translationKeyIds->isEmpty()
            ? collect()
            : TranslationValue::query()
                ->whereIn('translation_key_id', $translationKeyIds->values()->all())
                ->whereIn('locale', $this->localeCandidates($targetLocale))
                ->pluck('translation_key_id')
                ->flip();
        return $entries
            ->mapWithKeys(function (TranslationWorkbenchEntry $entry) use ($translationKeyIds, $sourceTranslationKeyIds, $targetTranslationKeyIds): array {
                $translationKey = $this->nullableString($entry->translation_key);
                $translationKeyId = $translationKey !== null ? $translationKeyIds->get($translationKey) : null;

                return [
                    $entry->id => [
                        'has_key' => $translationKey !== null,
                        'source_exists' => $translationKeyId !== null && $sourceTranslationKeyIds->has($translationKeyId),
                        'target_exists' => $translationKeyId !== null && $targetTranslationKeyIds->has($translationKeyId),
                        'has_deleted_segments' => filled($entry->deleted_segments),
                    ],
                ];
            })
            ->all();
    }

    private function translationValueForKeyAndLocale(string $translationKey, string $locale): ?string
    {
        $translationKeyRow = TranslationKey::query()
            ->where('key', $translationKey)
            ->first(['id']);

        if (! $translationKeyRow) {
            return null;
        }

        return TranslationValue::query()
            ->where('translation_key_id', $translationKeyRow->id)
            ->whereIn('locale', $this->localeCandidates($locale))
            ->orderByRaw("CASE WHEN locale = ? THEN 0 ELSE 1 END", [$locale])
            ->value('value');
    }

    /**
     * @return array<int, string>
     */
    private function localeCandidates(string $locale): array
    {
        $normalized = LocaleCode::normalize($locale);

        if ($normalized === '') {
            return [];
        }

        $icu = LocaleCode::toIcu($normalized);

        return array_values(array_unique([
            $normalized,
            strtolower($normalized),
            $icu,
            strtolower($icu),
        ]));
    }

    private function translationKeyRowForEntry(TranslationWorkbenchEntry $entry, string $translationKey): TranslationKey
    {
        $existing = TranslationKey::query()
            ->where('key', $translationKey)
            ->first();

        if ($existing) {
            return $existing;
        }

        return TranslationKey::query()->create([
            'fingerprint' => app(TranslationFingerprintFactory::class)->signature(["translation-key:{$translationKey}"]),
            'key' => $translationKey,
            'namespace' => $this->nullableString($entry->namespace)
                ?? $this->nullableString(str($translationKey)->before('.')->toString()),
            'group' => $this->nullableString($entry->group)
                ?? $this->groupFromTranslationKey($translationKey),
            'status' => 'open',
            'workflow_status' => 'open',
            'classification' => $entry->is_dynamic_key ? 'dynamic' : 'static',
            'is_dynamic_multi' => (bool) $entry->is_dynamic_multi,
            'source' => 'translation_workbench',
            'suggested_key' => $entry->suggested_key,
            'native_text' => $entry->literal_text ?? $entry->literal_text_suggested,
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function reviewSequenceIds(): array
    {
        return $this->applySorting($this->filteredQuery())
            ->pluck('id')
            ->map(static fn(int|string $id): int => (int) $id)
            ->all();
    }

    private function nextReviewEntryId(): ?int
    {
        if (! $this->reviewModalOpen || $this->reviewEntryId === null) {
            return null;
        }

        $reviewSequenceIds = $this->reviewSequenceIds();
        $currentIndex = array_search($this->reviewEntryId, $reviewSequenceIds, true);

        if ($currentIndex === false) {
            return $this->nextReviewEntryIdSnapshot;
        }

        $nextId = $reviewSequenceIds[$currentIndex + 1] ?? null;
        $this->nextReviewEntryIdSnapshot = $nextId;

        return $nextId;
    }

    /**
     * @param  array<int, int>  $reviewSequenceIds
     */
    private function nextReviewEntryIdFor(int $entryId, array $reviewSequenceIds): ?int
    {
        $currentIndex = array_search($entryId, $reviewSequenceIds, true);

        return $currentIndex === false
            ? null
            : ($reviewSequenceIds[$currentIndex + 1] ?? null);
    }

    /**
     * @return array<int, int>
     */
    private function translationSequenceIds(): array
    {
        return $this->applySorting($this->filteredQuery())
            ->whereNotNull('translation_key')
            ->where('translation_key', '!=', '')
            ->pluck('id')
            ->map(static fn(int|string $id): int => (int) $id)
            ->all();
    }

    private function nextTranslationEntryId(): ?int
    {
        if (! $this->editModalOpen || $this->editEntryId === null) {
            return null;
        }

        $translationSequenceIds = $this->translationSequenceIds();
        $currentIndex = array_search($this->editEntryId, $translationSequenceIds, true);

        if ($currentIndex === false) {
            return $this->nextTranslationEntryIdSnapshot;
        }

        $nextId = $translationSequenceIds[$currentIndex + 1] ?? null;
        $this->nextTranslationEntryIdSnapshot = $nextId;

        return $nextId;
    }

    /**
     * @param  array<int, int>  $translationSequenceIds
     */
    private function nextTranslationEntryIdFor(int $entryId, array $translationSequenceIds): ?int
    {
        $currentIndex = array_search($entryId, $translationSequenceIds, true);

        return $currentIndex === false
            ? null
            : ($translationSequenceIds[$currentIndex + 1] ?? null);
    }

    /**
     * @return array<int, int>
     */
    private function dynamicTranslationSequenceIds(): array
    {
        return $this->applySorting($this->filteredQuery())
            ->whereNotNull('translation_key')
            ->where('translation_key', '!=', '')
            ->where('is_dynamic_multi', true)
            ->pluck('id')
            ->map(static fn(int|string $id): int => (int) $id)
            ->all();
    }

    private function nextDynamicTranslationEntryId(): ?int
    {
        if (! $this->dynamicEditModalOpen || $this->dynamicEditEntryId === null) {
            return null;
        }

        $dynamicTranslationSequenceIds = $this->dynamicTranslationSequenceIds();
        $currentIndex = array_search($this->dynamicEditEntryId, $dynamicTranslationSequenceIds, true);

        if ($currentIndex === false) {
            return $this->nextDynamicTranslationEntryIdSnapshot;
        }

        $nextId = $dynamicTranslationSequenceIds[$currentIndex + 1] ?? null;
        $this->nextDynamicTranslationEntryIdSnapshot = $nextId;

        return $nextId;
    }

    /**
     * @param  array<int, int>  $dynamicTranslationSequenceIds
     */
    private function nextDynamicTranslationEntryIdFor(int $entryId, array $dynamicTranslationSequenceIds): ?int
    {
        $currentIndex = array_search($entryId, $dynamicTranslationSequenceIds, true);

        return $currentIndex === false
            ? null
            : ($dynamicTranslationSequenceIds[$currentIndex + 1] ?? null);
    }

    private function filteredQuery(): Builder
    {
        return TranslationWorkbenchEntry::query()
            ->when($this->kind !== '', fn(Builder $query): Builder => $query->where('kind', $this->kind))
            ->when($this->status !== '', fn(Builder $query): Builder => $query->where('status', $this->status))
            ->when($this->dynamicFilter !== '', function (Builder $query): Builder {
                return $query->where(function (Builder $query): void {
                    $this->applyDynamicFilter($query, $this->dynamicFilter);
                });
            })
            ->when($this->dynamicOptionFilter !== '', function (Builder $query): Builder {
                return $query->where(function (Builder $query): void {
                    $this->applyDynamicOptionFilter($query, $this->dynamicOptionFilter);
                });
            })
            ->when($this->workflowFilter !== '', function (Builder $query): Builder {
                return $query->where(function (Builder $query): void {
                    $this->applyWorkflowFilter($query, $this->workflowFilter, $this->editLocales());
                });
            })
            ->when(trim($this->search) !== '', function (Builder $query): Builder {
                $search = '%' . str_replace(['%', '_'], ['\\%', '\\_'], trim($this->search)) . '%';

                return $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('literal_text', 'ilike', $search)
                        ->orWhere('translation_key', 'ilike', $search)
                        ->orWhere('suggested_key', 'ilike', $search)
                        ->orWhere('source_path', 'ilike', $search)
                        ->orWhere('raw_expression', 'ilike', $search);
                });
            });
    }

    private function optionDiscoveryQuery(): Builder
    {
        $query = TranslationWorkbenchOptionDiscovery::query();

        if (! Schema::hasTable('translation_workbench_option_discoveries')) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->when($this->optionDiscoveryFilter !== '', function (Builder $query): Builder {
                return $query->where(function (Builder $query): void {
                    $this->applyOptionDiscoveryFilter($query, $this->optionDiscoveryFilter);
                });
            })
            ->when(trim($this->search) !== '', function (Builder $query): Builder {
                $search = '%' . str_replace(['%', '_'], ['\\%', '\\_'], trim($this->search)) . '%';
                $hasSuggestedKeyColumn = Schema::hasColumn('translation_workbench_option_discoveries', 'suggested_key');

                return $query->where(function (Builder $query) use ($search, $hasSuggestedKeyColumn): void {
                    $query
                        ->where('scope', 'ilike', $search)
                        ->orWhere('suggested_dynamic_key', 'ilike', $search)
                        ->orWhere('workbench_suggested_key', 'ilike', $search)
                        ->orWhere('source_path', 'ilike', $search)
                        ->orWhere('options_variable', 'ilike', $search);

                    if ($hasSuggestedKeyColumn) {
                        $query->orWhere('suggested_key', 'ilike', $search);
                    }
                });
            });
    }

    private function matchedEntryIdForOptionDiscovery(TranslationWorkbenchOptionDiscovery $discovery): ?int
    {
        if ($discovery->matched_entry_id !== null) {
            $entryExists = TranslationWorkbenchEntry::query()
                ->whereKey($discovery->matched_entry_id)
                ->exists();

            if ($entryExists) {
                return (int) $discovery->matched_entry_id;
            }
        }

        $scope = trim((string) $discovery->scope);
        $suggestedKey = trim((string) ($discovery->suggested_key ?: $discovery->workbench_suggested_key ?: $discovery->suggested_dynamic_key));
        $suggestedDynamicKey = trim((string) $discovery->suggested_dynamic_key);

        if ($scope === '' && $suggestedKey === '' && $suggestedDynamicKey === '') {
            return null;
        }

        return TranslationWorkbenchEntry::query()
            ->where(function (Builder $query) use ($scope, $suggestedKey, $suggestedDynamicKey): void {
                if ($suggestedKey !== '') {
                    $query
                        ->where('suggested_key', $suggestedKey)
                        ->orWhere('translation_key', $suggestedKey);
                }

                if ($suggestedDynamicKey !== '') {
                    $query
                        ->orWhere('suggested_key', $suggestedDynamicKey)
                        ->orWhere('translation_key', $suggestedDynamicKey);
                }

                if ($scope !== '') {
                    $query
                        ->orWhere('meta->dynamic_scope', $scope);
                }
            })
            ->orderByRaw(
                'CASE WHEN suggested_key = ? THEN 0 WHEN translation_key = ? THEN 1 ELSE 2 END',
                [$suggestedKey, $suggestedKey],
            )
            ->orderBy('id')
            ->value('id');
    }

    private function applyDynamicFilter(Builder $query, string $filter): void
    {
        match ($filter) {
            'dynamic' => $query->where(function (Builder $query): void {
                $query
                    ->orWhere('is_dynamic_key', true)
                    ->orWhere('is_dynamic_multi', true)
                    ->orWhere(function (Builder $query): void {
                        $query
                            ->where('is_dynamic_candidate_rejected', false)
                            ->where(function (Builder $query): void {
                                $query
                                    ->where('entry_type', 'dynamic')
                                    ->orWhere('candidate_type', 'dynamic')
                                    ->orWhere('kind', 'dynamic_multi');
                            });
                    });
            }),
            'dynamic_multi' => $query->where(function (Builder $query): void {
                $query
                    ->where('is_dynamic_multi', true)
                    ->orWhere('kind', 'dynamic_multi');
            }),
            'not_dynamic' => $query->where(function (Builder $query): void {
                $query
                    ->where('is_dynamic_candidate_rejected', true)
                    ->orWhere(function (Builder $query): void {
                        $query
                            ->where('is_dynamic_key', false)
                            ->where('is_dynamic_multi', false)
                            ->where(function (Builder $query): void {
                                $query->whereNull('entry_type')->orWhere('entry_type', '!=', 'dynamic');
                            })
                            ->where(function (Builder $query): void {
                                $query->whereNull('candidate_type')->orWhere('candidate_type', '!=', 'dynamic');
                            })
                            ->where('kind', '!=', 'dynamic_multi');
                    });
            }),
            default => null,
        };
    }

    private function applyDynamicOptionFilter(Builder $query, string $filter): void
    {
        match ($filter) {
            'discovered' => $query->whereNotNull('meta->dynamic_option_discovery'),
            'plain_label' => $query->where('meta->dynamic_option_discovery->label_usage', 'plain_label'),
            'translated_label' => $query->where('meta->dynamic_option_discovery->label_usage', 'translated_label'),
            'unresolved_source' => $query->where('meta->dynamic_option_discovery->source_type', 'unresolved'),
            'hardcoded_source' => $query->where('meta->dynamic_option_discovery->source_type', 'hardcoded_public_array'),
            default => null,
        };
    }

    private function applyOptionDiscoveryFilter(Builder $query, string $filter): void
    {
        match ($filter) {
            'matched' => $query->whereNotNull('matched_entry_id'),
            'unmatched' => $query->whereNull('matched_entry_id'),
            'plain_label' => $query->where('label_usage', 'plain_label'),
            'translated_label' => $query->where('label_usage', 'translated_label'),
            'unresolved_source' => $query->where('source_type', 'unresolved'),
            'hardcoded_source' => $query->where('source_type', 'hardcoded_public_array'),
            default => null,
        };
    }

    /**
     * @param  array{source: string, active: string, sub: array<int, string>}|null  $editLocales
     */
    private function applyWorkflowFilter(Builder $query, string $filter, ?array $editLocales = null): void
    {
        $editLocales ??= $this->editLocales();

        match ($filter) {
            'has_key', 'editable' => $query->whereNotNull('translation_key')->where('translation_key', '!=', ''),
            'missing_key', 'review_required' => $query->where(function (Builder $query): void {
                $query->whereNull('translation_key')->orWhere('translation_key', '');
            }),
            'ready_for_edit' => $query
                ->whereNotNull('translation_key')
                ->where('translation_key', '!=', '')
                ->where(function (Builder $query) use ($editLocales): void {
                    $this->whereTranslationValueExists($query, (string) ($editLocales['source'] ?? 'en'));
                })
                ->where(function (Builder $query) use ($editLocales): void {
                    $this->whereTargetTranslationMissing($query, (string) ($editLocales['active'] ?? app()->getLocale()));
                }),
            'source_exists', 'source_saved' => $query->where(function (Builder $query) use ($editLocales): void {
                $this->whereTranslationValueExists($query, (string) ($editLocales['source'] ?? 'en'));
            }),
            'source_missing' => $query->where(function (Builder $query) use ($editLocales): void {
                $this->whereTranslationValueMissing($query, (string) ($editLocales['source'] ?? 'en'));
            }),
            'target_exists' => $query->where(function (Builder $query) use ($editLocales): void {
                $this->whereTargetTranslationExists($query, (string) ($editLocales['active'] ?? app()->getLocale()));
            }),
            'target_missing' => $query
                ->whereNotNull('translation_key')
                ->where('translation_key', '!=', '')
                ->where(function (Builder $query) use ($editLocales): void {
                    $this->whereTargetTranslationMissing($query, (string) ($editLocales['active'] ?? app()->getLocale()));
                }),
            'has_deleted_segments' => $query
                ->when(
                    Schema::hasColumn('translation_workbench_entries', 'deleted_segments'),
                    fn(Builder $query): Builder => $query
                        ->whereNotNull('deleted_segments')
                        ->whereRaw("CAST(deleted_segments AS TEXT) <> '[]'"),
                    fn(Builder $query): Builder => $query->whereRaw('1 = 0'),
                ),
            default => null,
        };
    }

    private function whereSourceTranslationExists(Builder $query, string $sourceLocale): void
    {
        $query
            ->whereHas('values', fn(Builder $query): Builder => $query->where('value_key', "source:{$sourceLocale}"))
            ->orWhere(function (Builder $query) use ($sourceLocale): void {
                $this->whereTranslationValueExists($query, $sourceLocale);
            });
    }

    private function whereSourceTranslationMissing(Builder $query, string $sourceLocale): void
    {
        $query
            ->whereDoesntHave('values', fn(Builder $query): Builder => $query->where('value_key', "source:{$sourceLocale}"))
            ->where(function (Builder $query) use ($sourceLocale): void {
                $this->whereTranslationValueMissing($query, $sourceLocale);
            });
    }

    private function whereTargetTranslationExists(Builder $query, string $locale): void
    {
        $this->whereTranslationValueExists($query, $locale);
    }

    private function whereTargetTranslationMissing(Builder $query, string $locale): void
    {
        $this->whereTranslationValueMissing($query, $locale);
    }

    private function whereTranslationValueExists(Builder $query, string $locale): void
    {
        $query->whereExists(function ($query) use ($locale): void {
            $query
                ->selectRaw('1')
                ->from('translation_values')
                ->join('translation_keys', 'translation_keys.id', '=', 'translation_values.translation_key_id')
                ->whereColumn('translation_keys.key', 'translation_workbench_entries.translation_key')
                ->whereIn('translation_values.locale', $this->localeCandidates($locale));
        });
    }

    private function whereTranslationValueMissing(Builder $query, string $locale): void
    {
        $query->whereNotExists(function ($query) use ($locale): void {
            $query
                ->selectRaw('1')
                ->from('translation_values')
                ->join('translation_keys', 'translation_keys.id', '=', 'translation_values.translation_key_id')
                ->whereColumn('translation_keys.key', 'translation_workbench_entries.translation_key')
                ->whereIn('translation_values.locale', $this->localeCandidates($locale));
        });
    }

    private function normalizedPerPage(): int
    {
        return $this->normalizePerPage($this->perPage);
    }

    private function normalizePerPage(mixed $value): int
    {
        $value = (int) $value;

        return in_array($value, [10, 25, 50, 100], true) ? $value : 25;
    }

    private function applySorting(Builder $query): Builder
    {
        $field = array_key_exists($this->sortField, $this->sortableFields())
            ? $this->sortField
            : 'last_seen_at';
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return match ($field) {
            'current_value' => $query
                ->orderByRaw("COALESCE(NULLIF(literal_text, ''), NULLIF(translation_key, ''), NULLIF(existing_key, ''), NULLIF(suggested_key, ''), '') {$direction}")
                ->orderBy('id', $direction),
            'source' => $query
                ->orderBy('source_path', $direction)
                ->orderBy('source_line', $direction)
                ->orderBy('id', $direction),
            'last_seen_at' => $query
                ->orderBy('last_seen_at', $direction)
                ->orderBy('id', $direction),
            default => $query
                ->orderBy($this->sortableFields()[$field], $direction)
                ->orderBy('id', $direction),
        };
    }

    /**
     * @return array<string, string>
     */
    private function sortableFields(): array
    {
        return [
            'id' => 'id',
            'kind' => 'kind',
            'current_value' => 'literal_text',
            'suggested_key' => 'suggested_key',
            'source' => 'source_path',
            'last_seen_at' => 'last_seen_at',
            'status' => 'status',
        ];
    }

    private function defaultSortDirection(string $field): string
    {
        return in_array($field, ['id', 'last_seen_at'], true) ? 'desc' : 'asc';
    }

    private function normalizeSortField(mixed $field): string
    {
        $field = trim((string) $field);

        return array_key_exists($field, $this->sortableFields()) ? $field : 'last_seen_at';
    }

    private function normalizeSortDirection(mixed $direction): string
    {
        return trim((string) $direction) === 'asc' ? 'asc' : 'desc';
    }

    private function normalizeDynamicFilter(mixed $value): string
    {
        $value = trim((string) $value);

        return in_array($value, ['', 'dynamic', 'dynamic_multi', 'not_dynamic'], true) ? $value : '';
    }

    private function normalizeDynamicOptionFilter(mixed $value): string
    {
        $value = trim((string) $value);

        return in_array($value, ['', 'discovered', 'plain_label', 'translated_label', 'unresolved_source', 'hardcoded_source'], true) ? $value : '';
    }

    private function normalizeOptionDiscoveryFilter(mixed $value): string
    {
        $value = trim((string) $value);

        return in_array($value, ['', 'matched', 'unmatched', 'plain_label', 'translated_label', 'unresolved_source', 'hardcoded_source'], true) ? $value : '';
    }

    private function normalizeWorkflowFilter(mixed $value): string
    {
        $value = trim((string) $value);

        return in_array($value, ['', 'ready_for_edit', 'missing_key', 'has_key', 'editable', 'source_exists', 'source_saved', 'target_exists', 'target_missing', 'has_deleted_segments'], true) ? $value : '';
    }

    /**
     * @return array<string, mixed>
     */
    private function uiStateDefaults(): array
    {
        return (array) config('translation-workbench.ui_state.defaults', []);
    }

    private function uiStateSettingKey(): string
    {
        return (string) config('translation-workbench.ui_state.setting_key', 'ui.pages.translation_workbench.entries');
    }

    private function persistUiState(): void
    {
        $this->setUserSetting($this->uiStateSettingKey(), [
            'search' => $this->search,
            'kind' => $this->kind,
            'status' => $this->status,
            'dynamicFilter' => $this->dynamicFilter,
            'dynamicOptionFilter' => $this->dynamicOptionFilter,
            'optionDiscoveryFilter' => $this->optionDiscoveryFilter,
            'workflowFilter' => $this->workflowFilter,
            'perPage' => $this->perPage,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'showDynamicTable' => $this->showDynamicTable,
            'showEntriesTable' => $this->showEntriesTable,
        ]);
    }

    private function nullableString(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : '';

        return $value !== '' ? $value : null;
    }

    /**
     * @return array<int, string>
     */
    private function normalizedDynamicValueKeysByIndex(): array
    {
        return collect($this->dynamicValueKeys)
            ->mapWithKeys(fn(mixed $valueKey, int $index): array => [$index => $this->normalizedDynamicValueKey($valueKey)])
            ->filter()
            ->uniqueStrict()
            ->all();
    }

    private function normalizedDynamicValueKey(mixed $valueKey): ?string
    {
        $valueKey = is_string($valueKey) ? trim($valueKey) : '';
        $valueKey = trim($valueKey, '.');

        return $valueKey !== '' ? $valueKey : null;
    }

    private function translationKeySourceFor(
        TranslationWorkbenchEntry $entry,
        ?string $translationKey,
        string $changedSource,
    ): ?string {
        $translationKey = $this->nullableString($translationKey);

        if ($translationKey === null) {
            return null;
        }

        if ($translationKey === $this->nullableString($entry->translation_key)) {
            return $this->nullableString($entry->translation_key_source);
        }

        return $changedSource;
    }

    private function uiTranslationKeyFor(TranslationWorkbenchEntry $entry): ?string
    {
        $translationKey = $this->nullableString($entry->translation_key)
            ?? $this->nullableString($entry->suggested_key)
            ?? $this->nullableString($entry->existing_key);

        if ($translationKey === null) {
            return null;
        }

        $translationKey = trim($translationKey, '.');
        $translationKey = $this->withoutTranslationKeyNamespace($translationKey, 'dynamic') ?? $translationKey;
        $translationKey = $this->withoutTranslationKeyNamespace($translationKey, 'ui') ?? $translationKey;

        return 'ui.' . trim($translationKey, '.');
    }

    private function dynamicTranslationKeyFor(TranslationWorkbenchEntry $entry): ?string
    {
        $translationKey = $this->nullableString($entry->translation_key)
            ?? $this->nullableString($entry->suggested_key)
            ?? $this->nullableString($entry->existing_key);

        if ($translationKey === null) {
            return null;
        }

        $translationKey = $this->withoutTranslationKeyNamespace($translationKey, 'ui');
        $translationKey = $this->withoutTranslationKeyNamespace($translationKey, 'dynamic');

        return $translationKey !== null ? 'dynamic.' . trim($translationKey, '.') : null;
    }

    private function withoutTranslationKeyNamespace(?string $translationKey, string $namespace): ?string
    {
        $translationKey = $this->nullableString($translationKey);

        if ($translationKey === null) {
            return null;
        }

        $prefix = trim($namespace, '.') . '.';

        if (! str_starts_with($translationKey, $prefix)) {
            return $translationKey;
        }

        return $this->nullableString(substr($translationKey, strlen($prefix)));
    }

    private function groupFromTranslationKey(?string $translationKey): ?string
    {
        $translationKey = $this->nullableString($translationKey);

        if ($translationKey === null) {
            return null;
        }

        $segments = array_values(array_filter(explode('.', $translationKey)));

        return $segments[1] ?? null;
    }

    private function namespaceFromTranslationKey(?string $translationKey): ?string
    {
        $translationKey = $this->nullableString($translationKey);

        if ($translationKey === null) {
            return null;
        }

        $segments = array_values(array_filter(explode('.', $translationKey)));

        return $segments[0] ?? null;
    }
}
