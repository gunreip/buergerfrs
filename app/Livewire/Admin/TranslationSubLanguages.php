<?php

// app/Livewire/Admin/TranslationSubLanguages.php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Models\Locale;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Settings\AppGeneralSettings;
use App\Support\Locale\LocaleCode;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

/**
 * First-draft administration page for sub-language (locale variant) coverage.
 */
class TranslationSubLanguages extends Component
{
    use InteractsWithUserSettings;
    use WithPagination;

    private const UI_STATE_SETTING_KEY = 'ui.pages.admin_translation_sub_languages';

    private const MAX_SELECTED_SUB_LANGUAGE_FILTERS = 3;

    private const UI_STATE_ACTIVITY_EVENT = 'admin.translation_sub_languages.ui_state_updated';

    private const SORT_FIELDS = [
        'id',
        'locale',
        'base_locale',
    ];

    private const TRANSLATION_ROW_SORT_FIELDS = [
        'id',
        'key',
    ];

    /**
     * @var array<int, string>
     */
    private const PERSISTED_STATE_PROPERTIES = [
        'search',
        'baseLocaleFilter',
        'onlyWithOverrides',
        'selectedSubLanguageLocales',
        'sortField',
        'sortDirection',
        'translationRowsSortField',
        'translationRowsSortDirection',
        'translationRowsPerPage',
    ];

    public string $search = '';

    public string $baseLocaleFilter = '';

    public bool $onlyWithOverrides = false;

    /**
     * @var array<int, string>
     */
    public array $selectedSubLanguageLocales = [];

    public string $sortField = 'locale';

    public string $sortDirection = 'asc';

    public string $translationRowsSortField = 'key';

    public string $translationRowsSortDirection = 'asc';

    public int $translationRowsPerPage = 25;

    public bool $translationEntryEditModalOpen = false;

    public ?int $editingTranslationKeyId = null;

    public string $editingTranslationKeyName = '';

    /**
     * @var array<int, string>
     */
    public array $editingTranslationLocales = [];

    /**
     * @var array<string, string>
     */
    public array $translationEntryEditValues = [];

    /**
     * @var array<string, string>
     */
    public array $translationEntryOriginalValues = [];

    public function mount(): void
    {
        $state = $this->userSetting(self::UI_STATE_SETTING_KEY, []);

        if (! is_array($state)) {
            return;
        }

        $this->search = trim((string) ($state['search'] ?? $this->search));
        $this->baseLocaleFilter = LocaleCode::normalize((string) ($state['baseLocaleFilter'] ?? $this->baseLocaleFilter));
        $this->onlyWithOverrides = (bool) ($state['onlyWithOverrides'] ?? $this->onlyWithOverrides);
        $this->selectedSubLanguageLocales = $this->normalizeSelectedSubLanguageLocales($state['selectedSubLanguageLocales'] ?? []);

        $storedSortField = trim((string) ($state['sortField'] ?? $this->sortField));
        $this->sortField = in_array($storedSortField, self::SORT_FIELDS, true)
            ? $storedSortField
            : 'locale';

        $storedSortDirection = trim((string) ($state['sortDirection'] ?? $this->sortDirection));
        $this->sortDirection = in_array($storedSortDirection, ['asc', 'desc'], true)
            ? $storedSortDirection
            : 'asc';

        $storedTranslationRowsSortField = trim((string) ($state['translationRowsSortField'] ?? $this->translationRowsSortField));
        $this->translationRowsSortField = $this->normalizeTranslationRowsSortField(
            $storedTranslationRowsSortField,
            $this->translationScopeLocales(),
        );

        $storedTranslationRowsSortDirection = trim((string) ($state['translationRowsSortDirection'] ?? $this->translationRowsSortDirection));
        $this->translationRowsSortDirection = in_array($storedTranslationRowsSortDirection, ['asc', 'desc'], true)
            ? $storedTranslationRowsSortDirection
            : 'asc';

        $this->translationRowsPerPage = $this->normalizeTranslationRowsPerPage(
            $state['translationRowsPerPage'] ?? $this->translationRowsPerPage,
        );
    }

    public function updated(string $property): void
    {
        if (! in_array($property, self::PERSISTED_STATE_PROPERTIES, true)) {
            return;
        }

        $this->persistUiState($property);
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->baseLocaleFilter = '';
        $this->onlyWithOverrides = false;
        $this->selectedSubLanguageLocales = [];
        $this->translationRowsPerPage = 25;
        $this->resetPage();

        $this->persistUiState('clearFilters');
    }

    public function updatedBaseLocaleFilter(): void
    {
        $this->baseLocaleFilter = LocaleCode::normalize($this->baseLocaleFilter);
        $this->selectedSubLanguageLocales = [];
        $this->translationRowsSortField = $this->normalizeTranslationRowsSortField('key', $this->translationScopeLocales());
        $this->resetPage();
    }

    public function updatedSelectedSubLanguageLocales(): void
    {
        $this->selectedSubLanguageLocales = $this->normalizeSelectedSubLanguageLocales($this->selectedSubLanguageLocales);
        $this->translationRowsSortField = $this->normalizeTranslationRowsSortField(
            $this->translationRowsSortField,
            $this->translationScopeLocales(),
        );
        $this->resetPage();
    }

    public function updatedTranslationRowsPerPage(): void
    {
        $this->translationRowsPerPage = $this->normalizeTranslationRowsPerPage($this->translationRowsPerPage);
        $this->resetPage();
        $this->persistUiState('updatedTranslationRowsPerPage');
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, self::SORT_FIELDS, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            $this->persistUiState('sortBy');

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';
        $this->persistUiState('sortBy');
    }

    public function sortTranslationRowsBy(string $field): void
    {
        $normalizedField = $this->normalizeTranslationRowsSortField($field, $this->translationScopeLocales());

        if ($normalizedField === '') {
            return;
        }

        if ($this->translationRowsSortField === $normalizedField) {
            $this->translationRowsSortDirection = $this->translationRowsSortDirection === 'asc' ? 'desc' : 'asc';
            $this->resetPage();
            $this->persistUiState('sortTranslationRowsBy');

            return;
        }

        $this->translationRowsSortField = $normalizedField;
        $this->translationRowsSortDirection = 'asc';
        $this->resetPage();
        $this->persistUiState('sortTranslationRowsBy');
    }

    public function openTranslationEntryEditModal(int $translationKeyId): void
    {
        $scopeLocales = $this->translationScopeLocales();

        if ($scopeLocales === []) {
            Flux::toast(
                heading: __('Selection required'),
                text: __('Select a main language first.'),
                variant: 'warning',
                duration: 3000,
            );

            return;
        }

        $translationKey = TranslationKey::query()
            ->where('id', $translationKeyId)
            ->where('status', 'ok')
            ->whereNotNull('key', 'and')
            ->where('key', '<>', '')
            ->first();

        if (! $translationKey instanceof TranslationKey) {
            Flux::toast(
                heading: __('Translation key not found'),
                text: __('The selected translation entry could not be loaded.'),
                variant: 'danger',
                duration: 3000,
            );

            return;
        }

        $existingValues = TranslationValue::query()
            ->where('translation_key_id', $translationKey->id)
            ->whereIn(DB::raw("LOWER(REPLACE(locale, '_', '-'))"), $scopeLocales, 'and', false)
            ->get(['locale', 'value'])
            ->mapWithKeys(static fn(TranslationValue $value): array => [
                LocaleCode::normalize((string) $value->locale) => (string) ($value->value ?? ''),
            ]);

        $this->resetValidation('translationEntryEditValues.*');

        $this->editingTranslationKeyId = (int) $translationKey->id;
        $this->editingTranslationKeyName = (string) ($translationKey->key ?? '');
        $this->editingTranslationLocales = $scopeLocales;
        $this->translationEntryEditValues = collect($scopeLocales)
            ->mapWithKeys(static fn(string $locale): array => [$locale => (string) ($existingValues[$locale] ?? '')])
            ->all();
        $this->translationEntryOriginalValues = $this->translationEntryEditValues;

        $this->translationEntryEditModalOpen = true;
    }

    public function closeTranslationEntryEditModal(): void
    {
        $this->resetValidation('translationEntryEditValues.*');

        $this->translationEntryEditModalOpen = false;
        $this->editingTranslationKeyId = null;
        $this->editingTranslationKeyName = '';
        $this->editingTranslationLocales = [];
        $this->translationEntryEditValues = [];
        $this->translationEntryOriginalValues = [];
    }

    public function getTranslationEntryHasChangesProperty(): bool
    {
        if ($this->editingTranslationLocales === []) {
            return false;
        }

        foreach ($this->editingTranslationLocales as $locale) {
            $current = $this->normalizeTranslationEntryEditValue($this->translationEntryEditValues[$locale] ?? null);
            $original = $this->normalizeTranslationEntryEditValue($this->translationEntryOriginalValues[$locale] ?? null);

            if ($current !== $original) {
                return true;
            }
        }

        return false;
    }

    public function saveTranslationEntryEdit(): void
    {
        if ($this->editingTranslationKeyId === null) {
            return;
        }

        $rules = collect($this->editingTranslationLocales)
            ->mapWithKeys(static fn(string $locale): array => [
                'translationEntryEditValues.' . $locale => ['nullable', 'string', 'max:10000'],
            ])
            ->all();

        if ($rules === []) {
            return;
        }

        $this->validate($rules);

        $translationKey = TranslationKey::query()->find($this->editingTranslationKeyId);

        if (! $translationKey instanceof TranslationKey) {
            Flux::toast(
                heading: __('Translation key not found'),
                text: __('The selected translation entry no longer exists.'),
                variant: 'danger',
                duration: 3000,
            );

            $this->closeTranslationEntryEditModal();

            return;
        }

        $hasChanges = false;

        foreach ($this->editingTranslationLocales as $locale) {
            $newValue = trim((string) ($this->translationEntryEditValues[$locale] ?? ''));

            $existing = TranslationValue::query()
                ->where('translation_key_id', $translationKey->id)
                ->whereRaw("LOWER(REPLACE(locale, '_', '-')) = ?", [$locale], 'and')
                ->first();

            if (! $existing && $newValue === '') {
                continue;
            }

            $oldValue = trim((string) ($existing?->value ?? ''));
            $newStatus = $newValue === '' ? 'missing' : 'ok';
            $oldStatus = (string) ($existing?->status ?? 'missing');

            if ($oldValue === $newValue && $oldStatus === $newStatus) {
                continue;
            }

            $hasChanges = true;

            TranslationValue::query()->updateOrCreate(
                [
                    'translation_key_id' => $translationKey->id,
                    'locale' => $locale,
                ],
                [
                    'value' => $newValue,
                    'status' => $newStatus,
                    'source' => 'manual',
                    'reviewed_at' => now(),
                    'reviewed_by_user_id' => Auth::id(),
                ]
            );
        }

        if (! $hasChanges) {
            Flux::toast(
                heading: __('No changes detected'),
                text: __('There are no translation updates to save.'),
                variant: 'warning',
                duration: 2500,
            );

            return;
        }

        $savedKey = (string) ($translationKey->key ?? $translationKey->id);

        $this->closeTranslationEntryEditModal();

        Flux::toast(
            heading: __('Translations updated'),
            text: __('Translation values for :key have been saved.', ['key' => $savedKey]),
            variant: 'success',
            duration: 3000,
        );

        $this->dispatch('$refresh');
    }

    public function openNextTranslationEntryEditFromList(): void
    {
        if ($this->editingTranslationKeyId === null) {
            return;
        }

        $scopeLocales = $this->translationScopeLocales();

        if ($scopeLocales === []) {
            return;
        }

        $translationIds = $this->resolveTranslationRows($scopeLocales)
            ->pluck('id')
            ->map(static fn(mixed $id): int => (int) $id)
            ->values();

        $currentIndex = $translationIds->search($this->editingTranslationKeyId);

        if (! is_int($currentIndex)) {
            return;
        }

        $nextId = $translationIds->get($currentIndex + 1);

        if (! is_int($nextId)) {
            Flux::toast(
                heading: __('End of list reached'),
                text: __('There is no next translation entry in the current selection.'),
                variant: 'info',
                duration: 2500,
            );

            return;
        }

        $this->openTranslationEntryEditModal($nextId);
    }

    public function markSubLanguageAsDuplicate(int $translationKeyId, string $locale): void
    {
        $normalizedLocale = LocaleCode::normalize($locale);

        if (! $this->isSelectedSubLanguageLocale($normalizedLocale)) {
            Flux::toast(
                heading: __('Invalid locale selection'),
                text: __('Select the sub-language in the current table scope first.'),
                variant: 'warning',
                duration: 2500,
            );

            return;
        }

        $translationValue = $this->findTranslationValueForLocale($translationKeyId, $normalizedLocale);

        if (! $translationValue instanceof TranslationValue || trim((string) ($translationValue->value ?? '')) === '') {
            Flux::toast(
                heading: __('Value missing'),
                text: __('No translatable value exists for this sub-language entry.'),
                variant: 'warning',
                duration: 2500,
            );

            return;
        }

        $translationValue->fill([
            'is_base_duplicate' => true,
            'source' => 'manual',
            'reviewed_at' => now(),
            'reviewed_by_user_id' => Auth::id(),
        ]);
        $translationValue->save();

        Flux::toast(
            heading: __('Marked as duplicate'),
            text: __('The sub-language value is now marked as base duplicate.'),
            variant: 'success',
            duration: 2500,
        );

        $this->dispatch('$refresh');
    }

    public function keepSubLanguageAsOverride(int $translationKeyId, string $locale): void
    {
        $normalizedLocale = LocaleCode::normalize($locale);

        if (! $this->isSelectedSubLanguageLocale($normalizedLocale)) {
            Flux::toast(
                heading: __('Invalid locale selection'),
                text: __('Select the sub-language in the current table scope first.'),
                variant: 'warning',
                duration: 2500,
            );

            return;
        }

        $translationValue = $this->findTranslationValueForLocale($translationKeyId, $normalizedLocale);

        if (! $translationValue instanceof TranslationValue || trim((string) ($translationValue->value ?? '')) === '') {
            Flux::toast(
                heading: __('Value missing'),
                text: __('No translatable value exists for this sub-language entry.'),
                variant: 'warning',
                duration: 2500,
            );

            return;
        }

        $translationValue->fill([
            'is_base_duplicate' => false,
            'source' => 'manual',
            'reviewed_at' => now(),
            'reviewed_by_user_id' => Auth::id(),
        ]);
        $translationValue->save();

        Flux::toast(
            heading: __('Marked as override'),
            text: __('The sub-language value is now kept as intentional override.'),
            variant: 'success',
            duration: 2500,
        );

        $this->dispatch('$refresh');
    }

    public function render(): View
    {
        $totalKeys = (int) DB::table('translation_keys')->count();

        $allSubLocales = Locale::query()
            ->where('is_active', true)
            ->ordered()
            ->get([
                'id',
                'code',
                'normalized_code',
                'display_name',
                'native_display_name',
                'is_active',
                'is_default',
            ])
            ->map(function (Locale $locale) use ($totalKeys): ?object {
                $normalized = LocaleCode::normalize((string) ($locale->normalized_code ?: $locale->code));

                if ($normalized === '') {
                    return null;
                }

                $parts = LocaleCode::parts($normalized);
                $baseLocale = (string) ($parts['language'] ?? '');

                if ($baseLocale === '' || $baseLocale === $normalized) {
                    return null;
                }

                $variantLocaleCandidates = $this->localeCandidates($normalized);
                $baseLocaleCandidates = $this->localeCandidates($baseLocale);

                $variantTranslatedCount = $this->translatedCountForLocales($variantLocaleCandidates);
                $baseTranslatedCount = $this->translatedCountForLocales($baseLocaleCandidates);
                $effectiveTranslatedCount = $this->effectiveTranslatedCount($baseLocaleCandidates, $variantLocaleCandidates);
                $overrideCount = min($variantTranslatedCount, $effectiveTranslatedCount);

                return (object) [
                    'id' => (int) $locale->id,
                    'locale' => $normalized,
                    'locale_raw' => (string) $locale->code,
                    'base_locale' => $baseLocale,
                    'display_name' => (string) ($locale->display_name ?: $locale->native_display_name ?: strtoupper($normalized)),
                    'is_active' => (bool) $locale->is_active,
                    'is_default' => (bool) $locale->is_default,
                    'base_translated_count' => $baseTranslatedCount,
                    'variant_translated_count' => $variantTranslatedCount,
                    'override_count' => $overrideCount,
                    'effective_translated_count' => $effectiveTranslatedCount,
                    'effective_coverage_pct' => $totalKeys > 0 ? round(($effectiveTranslatedCount / $totalKeys) * 100, 1) : 0.0,
                ];
            })
            ->filter()
            ->sortBy('locale')
            ->values();

        $searchNeedle = mb_strtolower(trim($this->search));

        $availableSubLanguageOptions = $this->baseLocaleFilter === ''
            ? collect()
            : $allSubLocales
            ->filter(fn(object $entry): bool => (string) ($entry->base_locale ?? '') === $this->baseLocaleFilter)
            ->map(static function (object $entry): object {
                return (object) [
                    'locale' => (string) ($entry->locale ?? ''),
                    'display_name' => (string) ($entry->display_name ?? ''),
                ];
            })
            ->unique('locale')
            ->sortBy('locale', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $validSelectedSubLanguageLocales = collect($this->selectedSubLanguageLocales)
            ->filter(fn(mixed $locale): bool => is_string($locale) && trim($locale) !== '')
            ->map(fn(string $locale): string => LocaleCode::normalize($locale))
            ->filter(fn(string $locale): bool => $locale !== '')
            ->intersect($availableSubLanguageOptions->pluck('locale'))
            ->values()
            ->all();

        if ($validSelectedSubLanguageLocales !== $this->selectedSubLanguageLocales) {
            $this->selectedSubLanguageLocales = $validSelectedSubLanguageLocales;
            $this->persistUiState('systemSanitizeSelection');
        }

        $subLocales = $allSubLocales
            ->when($searchNeedle !== '', function (Collection $rows) use ($searchNeedle): Collection {
                return $rows->filter(function (object $entry) use ($searchNeedle): bool {
                    $haystack = mb_strtolower(implode(' ', [
                        (string) ($entry->locale ?? ''),
                        (string) ($entry->display_name ?? ''),
                        (string) ($entry->base_locale ?? ''),
                    ]));

                    return str_contains($haystack, $searchNeedle);
                });
            })
            ->when($this->baseLocaleFilter !== '', function (Collection $rows): Collection {
                return $rows->filter(fn(object $entry): bool => (string) ($entry->base_locale ?? '') === $this->baseLocaleFilter);
            })
            ->when($this->onlyWithOverrides, function (Collection $rows): Collection {
                return $rows->filter(fn(object $entry): bool => (int) ($entry->override_count ?? 0) > 0);
            })
            ->when($this->selectedSubLanguageLocales !== [], function (Collection $rows): Collection {
                return $rows->filter(fn(object $entry): bool => in_array((string) ($entry->locale ?? ''), $this->selectedSubLanguageLocales, true));
            })
            ->sortBy(function (object $entry) {
                $id = (int) ($entry->id ?? 0);
                $locale = (string) ($entry->locale ?? '');
                $baseLocale = (string) ($entry->base_locale ?? '');

                return match ($this->sortField) {
                    'id' => str_pad((string) $id, 12, '0', STR_PAD_LEFT),
                    'base_locale' => $baseLocale . '|' . $locale,
                    default => $locale,
                };
            }, SORT_NATURAL | SORT_FLAG_CASE, $this->sortDirection === 'desc')
            ->values();

        $activeCount = $subLocales->count();
        $withOverridesCount = $subLocales->where('override_count', '>', 0)->count();
        $activeSubLocalesTotal = $allSubLocales->count();
        $baseLocaleOptions = $this->resolveBaseLocaleOptions($allSubLocales);

        if ($this->baseLocaleFilter !== '' && ! $baseLocaleOptions->contains(fn(object $opt): bool => $opt->locale === $this->baseLocaleFilter)) {
            $this->baseLocaleFilter = '';
            $this->selectedSubLanguageLocales = [];

            $this->persistUiState('systemResetInvalidBaseLocale');
        }

        $hasActiveFilters = $searchNeedle !== ''
            || $this->baseLocaleFilter !== ''
            || $this->onlyWithOverrides
            || $this->selectedSubLanguageLocales !== [];

        $translationScopeLocales = $this->translationScopeLocales();

        $translationRows = new LengthAwarePaginator([], 0, $this->normalizeTranslationRowsPerPage(), 1, [
            'path' => request()?->url(),
        ]);
        $nextEditTranslationKeyId = null;

        if ($this->baseLocaleFilter !== '' && $translationScopeLocales !== []) {
            $resolvedTranslationRows = $this->resolveTranslationRows($translationScopeLocales);
            $translationRows = $this->paginateTranslationRows($resolvedTranslationRows);

            if ($this->editingTranslationKeyId !== null) {
                $rowIds = $resolvedTranslationRows
                    ->pluck('id')
                    ->map(static fn(mixed $id): int => (int) $id)
                    ->values();

                $currentIndex = $rowIds->search($this->editingTranslationKeyId);

                if (is_int($currentIndex)) {
                    $candidate = $rowIds->get($currentIndex + 1);

                    if (is_int($candidate)) {
                        $nextEditTranslationKeyId = $candidate;
                    }
                }
            }
        }

        return view('components.admin.⚡translation-sub-languages', [
            'totalKeys' => $totalKeys,
            'subLocales' => $subLocales,
            'activeCount' => $activeCount,
            'activeSubLocalesTotal' => $activeSubLocalesTotal,
            'withOverridesCount' => $withOverridesCount,
            'baseLocaleOptions' => $baseLocaleOptions,
            'availableSubLanguageOptions' => $availableSubLanguageOptions,
            'maxSelectedSubLanguageFilters' => self::MAX_SELECTED_SUB_LANGUAGE_FILTERS,
            'hasActiveFilters' => $hasActiveFilters,
            'translationScopeLocales' => $translationScopeLocales,
            'translationRows' => $translationRows,
            'nextEditTranslationKeyId' => $nextEditTranslationKeyId,
        ]);
    }

    private function translationEntryBaseQuery()
    {
        return TranslationKey::query()
            ->where('status', 'ok')
            ->whereNotNull('key', 'and')
            ->where('key', '<>', '')
            ->whereHas('values', function ($query): void {
                $query
                    ->where('locale', 'en')
                    ->where('status', 'ok');
            })
            ->whereHas('values', function ($query): void {
                $query
                    ->where('locale', $this->baseLocaleFilter)
                    ->where('status', 'ok');
            });
    }

    /**
     * @param array<int, string> $scopeLocales
     */
    private function resolveTranslationRows(array $scopeLocales): Collection
    {
        if ($this->baseLocaleFilter === '' || $scopeLocales === []) {
            return collect();
        }

        $normalizedTranslationRowsSortField = $this->normalizeTranslationRowsSortField(
            $this->translationRowsSortField,
            $scopeLocales,
        );

        if ($normalizedTranslationRowsSortField !== $this->translationRowsSortField) {
            $this->translationRowsSortField = $normalizedTranslationRowsSortField;
            $this->persistUiState('systemNormalizeTranslationRowsSortField');
        }

        return $this->translationEntryBaseQuery()
            ->with([
                'values' => fn($query) => $query
                    ->whereIn(DB::raw("LOWER(REPLACE(locale, '_', '-'))"), $scopeLocales)
                    ->orderBy('locale', 'asc'),
            ])
            ->orderBy('key', 'asc')
            ->get(['id', 'key', 'group', 'namespace', 'native_text'])
            ->map(static function (TranslationKey $translationKey): object {
                $valuesByLocale = $translationKey
                    ->values
                    ->mapWithKeys(static fn(TranslationValue $value): array => [
                        LocaleCode::normalize((string) $value->locale) => (string) ($value->value ?? ''),
                    ]);

                $valueMetaByLocale = $translationKey
                    ->values
                    ->mapWithKeys(static fn(TranslationValue $value): array => [
                        LocaleCode::normalize((string) $value->locale) => [
                            'id' => (int) $value->id,
                            'is_base_duplicate' => $value->is_base_duplicate,
                        ],
                    ]);

                return (object) [
                    'id' => (int) $translationKey->id,
                    'key' => (string) ($translationKey->key ?? ''),
                    'group' => (string) ($translationKey->group ?? ''),
                    'namespace' => (string) ($translationKey->namespace ?? ''),
                    'native_text' => (string) ($translationKey->native_text ?? ''),
                    'values' => $valuesByLocale,
                    'value_meta' => $valueMetaByLocale,
                ];
            })
            ->sortBy(
                fn(object $translationRow): string => $this->translationRowSortKey($translationRow, $normalizedTranslationRowsSortField),
                SORT_NATURAL | SORT_FLAG_CASE,
                $this->translationRowsSortDirection === 'desc'
            )
            ->values();
    }

    /**
     * @param Collection<int, object> $translationRows
     */
    private function paginateTranslationRows(Collection $translationRows): LengthAwarePaginator
    {
        $perPage = $this->normalizeTranslationRowsPerPage();
        $total = $translationRows->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $requestedPage = $this->getPage();
        $page = min(max(1, $requestedPage), $lastPage);

        if ($page !== $requestedPage) {
            $this->setPage($page);
        }

        return new LengthAwarePaginator(
            $translationRows->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            [
                'path' => request()?->url(),
            ]
        );
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

    /**
     * Build selectable main-language options from activated app primary locales.
     * Falls back to detected base locales from active sub-languages when needed.
     */
    private function resolveBaseLocaleOptions(Collection $allSubLocales): Collection
    {
        $appGeneralSettings = app(AppGeneralSettings::class);

        $primaryLocaleCodes = collect($appGeneralSettings->availableLocales ?? [])
            ->map(static fn(mixed $locale): string => is_string($locale) ? LocaleCode::normalize($locale) : '')
            ->filter(static function (string $locale): bool {
                if ($locale === '') {
                    return false;
                }

                $parts = LocaleCode::parts($locale);
                $language = (string) ($parts['language'] ?? '');

                return $language !== '' && $locale === $language;
            })
            ->unique()
            ->sort()
            ->values();

        $languageRows = DB::table('languages')
            ->where('is_active', true)
            ->whereRaw('COALESCE(iso639_1, iso639_3) IS NOT NULL')
            ->get([
                DB::raw('COALESCE(iso639_1, iso639_3) as code'),
                'name',
                'native_name',
            ]);

        $languageByCode = $languageRows->mapWithKeys(static function (object $row): array {
            $code = LocaleCode::normalize((string) ($row->code ?? ''));

            if ($code === '') {
                return [];
            }

            return [$code => $row];
        });

        if ($primaryLocaleCodes->isNotEmpty()) {
            return $primaryLocaleCodes
                ->map(static function (string $locale) use ($languageByCode): object {
                    $languageRow = $languageByCode->get($locale);
                    $fallback = strtoupper($locale);

                    return (object) [
                        'locale' => $locale,
                        'name' => (string) ($languageRow->name ?? $fallback),
                        'native_name' => (string) ($languageRow->native_name ?? $fallback),
                    ];
                })
                ->values();
        }

        return $allSubLocales
            ->pluck('base_locale')
            ->filter(fn(mixed $locale): bool => is_string($locale) && $locale !== '')
            ->unique()
            ->sort()
            ->map(static function (string $locale) use ($languageByCode): object {
                $languageRow = $languageByCode->get($locale);
                $fallback = strtoupper($locale);

                return (object) [
                    'locale' => $locale,
                    'name' => (string) ($languageRow->name ?? $fallback),
                    'native_name' => (string) ($languageRow->native_name ?? $fallback),
                ];
            })
            ->values();
    }

    /**
     * @param array<int, string> $locales
     */
    private function translatedCountForLocales(array $locales): int
    {
        if ($locales === []) {
            return 0;
        }

        return (int) TranslationValue::query()
            ->whereIn('locale', $locales, 'and', false)
            ->whereNotNull('value', 'and')
            ->where('value', '<>', '')
            ->count('*');
    }

    /**
     * @param mixed $locales
     *
     * @return array<int, string>
     */
    private function normalizeSelectedSubLanguageLocales(mixed $locales): array
    {
        if (! is_array($locales)) {
            return [];
        }

        return collect($locales)
            ->filter(fn(mixed $locale): bool => is_string($locale) && trim($locale) !== '')
            ->map(fn(string $locale): string => LocaleCode::normalize($locale))
            ->filter(fn(string $locale): bool => $locale !== '')
            ->unique()
            ->take(self::MAX_SELECTED_SUB_LANGUAGE_FILTERS)
            ->values()
            ->all();
    }

    private function persistUiState(string $trigger = 'unknown'): void
    {
        $before = $this->userSetting(self::UI_STATE_SETTING_KEY, []);

        if (! is_array($before)) {
            $before = [];
        }

        $after = [
            'search' => $this->search,
            'baseLocaleFilter' => $this->baseLocaleFilter,
            'onlyWithOverrides' => $this->onlyWithOverrides,
            'selectedSubLanguageLocales' => $this->selectedSubLanguageLocales,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'translationRowsSortField' => $this->translationRowsSortField,
            'translationRowsSortDirection' => $this->translationRowsSortDirection,
            'translationRowsPerPage' => $this->translationRowsPerPage,
        ];

        if ($before === $after) {
            return;
        }

        $this->setUserSetting(self::UI_STATE_SETTING_KEY, $after);

        $changedKeys = collect(array_keys($after))
            ->filter(fn(string $key): bool => ($before[$key] ?? null) !== ($after[$key] ?? null))
            ->values()
            ->all();

        $this->logUiStateActivity($trigger, $before, $after, $changedKeys);
    }

    /**
     * @param array<string, mixed> $before
     * @param array<string, mixed> $after
     * @param array<int, string> $changedKeys
     */
    private function logUiStateActivity(string $trigger, array $before, array $after, array $changedKeys): void
    {
        try {
            $logger = activity('admin')
                ->event(self::UI_STATE_ACTIVITY_EVENT)
                ->withProperties([
                    'trigger' => $trigger,
                    'changed_keys' => $changedKeys,
                    'before' => $before,
                    'after' => $after,
                    'source' => [
                        'route' => request()?->route()?->getName(),
                        'url' => request()?->fullUrl(),
                        'component' => static::class,
                    ],
                ]);

            if (Auth::check()) {
                $logger->causedBy(Auth::user());
            }

            $logger->log('Translation sub-language UI state updated');
        } catch (Throwable) {
            // Logging darf den UI-State-Workflow nicht blockieren.
        }
    }

    /**
     * @param array<int, string> $baseLocales
     * @param array<int, string> $variantLocales
     */
    private function effectiveTranslatedCount(array $baseLocales, array $variantLocales): int
    {
        $locales = array_values(array_unique(array_merge($baseLocales, $variantLocales)));

        if ($locales === []) {
            return 0;
        }

        return (int) TranslationValue::query()
            ->whereIn('locale', $locales, 'and', false)
            ->whereNotNull('value', 'and')
            ->where('value', '<>', '')
            ->distinct('translation_key_id')
            ->count('translation_key_id');
    }

    /**
     * @return array<int, string>
     */
    private function translationScopeLocales(): array
    {
        $mainLocale = LocaleCode::normalize($this->baseLocaleFilter);

        return collect(array_merge(['en'], $mainLocale !== '' ? [$mainLocale] : [], $this->selectedSubLanguageLocales))
            ->map(static fn(mixed $locale): string => is_string($locale) ? LocaleCode::normalize($locale) : '')
            ->filter(static fn(string $locale): bool => $locale !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeTranslationEntryEditValue(mixed $value): string
    {
        return trim((string) $value);
    }

    private function translationRowSortKey(object $translationRow, string $sortField): string
    {
        $id = (int) ($translationRow->id ?? 0);
        $key = trim((string) ($translationRow->key ?? ''));

        if ($sortField === 'id') {
            return str_pad((string) $id, 12, '0', STR_PAD_LEFT);
        }

        if (str_starts_with($sortField, 'value:')) {
            $locale = substr($sortField, strlen('value:'));
            $value = trim((string) (($translationRow->values[$locale] ?? '') ?: ''));

            return mb_strtolower($value) . '|' . mb_strtolower($key) . '|' . str_pad((string) $id, 12, '0', STR_PAD_LEFT);
        }

        return mb_strtolower($key) . '|' . str_pad((string) $id, 12, '0', STR_PAD_LEFT);
    }

    private function normalizeTranslationRowsPerPage(mixed $value = null): int
    {
        $normalizedValue = (int) ($value ?? $this->translationRowsPerPage);

        return in_array($normalizedValue, [10, 25, 50, 100], true)
            ? $normalizedValue
            : 25;
    }

    /**
     * @param array<int, string> $scopeLocales
     */
    private function normalizeTranslationRowsSortField(string $field, array $scopeLocales): string
    {
        $normalizedField = trim($field);

        if (in_array($normalizedField, self::TRANSLATION_ROW_SORT_FIELDS, true)) {
            return $normalizedField;
        }

        if (str_starts_with($normalizedField, 'value:')) {
            $locale = LocaleCode::normalize(substr($normalizedField, strlen('value:')));

            if ($locale !== '' && in_array($locale, $scopeLocales, true)) {
                return 'value:' . $locale;
            }
        }

        return 'key';
    }

    private function isSelectedSubLanguageLocale(string $locale): bool
    {
        if ($locale === '' || ! str_contains($locale, '-')) {
            return false;
        }

        return in_array($locale, $this->selectedSubLanguageLocales, true);
    }

    private function findTranslationValueForLocale(int $translationKeyId, string $locale): ?TranslationValue
    {
        if ($translationKeyId <= 0 || $locale === '') {
            return null;
        }

        return TranslationValue::query()
            ->where('translation_key_id', $translationKeyId)
            ->whereRaw("LOWER(REPLACE(locale, '_', '-')) = ?", [$locale], 'and')
            ->first();
    }
}
