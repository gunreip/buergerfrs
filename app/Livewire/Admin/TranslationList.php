<?php

// app/Livewire/Admin/TranslationList.php

namespace App\Livewire\Admin;

use App\Models\TranslationKey;
use App\Models\TranslationLanguage;
use App\Models\TranslationValue;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
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
    use WithoutUrlPagination;
    use WithPagination;

    private const PROBLEM_STATUSES = [
        'missing',
        'dynamic',
    ];

    public string $search = '';

    public string $status = 'all';

    public string $classification = 'all';

    public bool $onlyProblems = false;

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
        if (in_array($property, [
            'search',
            'status',
            'classification',
            'onlyProblems',
            'languageFilter',
            'fileFilter',
            'perPage',
        ], true)) {
            $this->resetPage();
        }
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
    }

    /**
     * Toggle problem-only filter and reset pagination.
     */
    public function toggleOnlyProblems(): void
    {
        $this->onlyProblems = ! $this->onlyProblems;
        $this->resetPage();
    }

    /**
     * Restore default filter and pagination state.
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = 'all';
        $this->classification = 'all';
        $this->onlyProblems = false;
        $this->languageFilter = '';
        $this->fileFilter = '';
        $this->perPage = 25;

        $this->resetPage();
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
            || $this->classification !== 'all'
            || $this->onlyProblems
            || $this->languageFilter !== ''
            || $this->fileFilter !== ''
            || $this->perPage !== 25;
    }

    /**
     * Build the base translation key query with all active filters applied.
     */
    private function translationKeyQuery(): Builder
    {
        return TranslationKey::query()
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
            ->when($this->status !== 'all', fn(Builder $query): Builder => $query->where('status', $this->status))
            ->when($this->classification !== 'all', fn(Builder $query): Builder => $query->where('classification', $this->classification))
            ->when($this->onlyProblems, fn(Builder $query): Builder => $query->whereIn('status', self::PROBLEM_STATUSES))
            ->when($this->languageFilter !== '', function (Builder $query): Builder {
                return $query->whereHas('values', function (Builder $query): void {
                    $query->where('locale', $this->languageFilter);
                });
            })
            ->when($this->fileFilter !== '', fn(Builder $query): Builder => $query->where('group', $this->fileFilter))
            ->when($this->search !== '', function (Builder $query): Builder {
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
            })
            ->latest('updated_at');
    }

    /**
     * Build data for list, counters and modals.
     */
    public function render(): View
    {
        $statusCounts = TranslationKey::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn($value) => (int) $value)
            ->all();

        $classificationCounts = TranslationKey::query()
            ->selectRaw('classification, count(*) as total')
            ->groupBy('classification')
            ->pluck('total', 'classification')
            ->mapWithKeys(fn($value, $key): array => [(string) $key => (int) $value])
            ->all();

        $total = array_sum($statusCounts);

        $problemCount = collect(self::PROBLEM_STATUSES)
            ->sum(fn(string $status): int => (int) ($statusCounts[$status] ?? 0));

        $query = $this->translationKeyQuery();

        $filteredTotal = (clone $query)->count();

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
            'translationKeys' => $query->paginate($this->normalizedPerPage()),
            'statusCounts' => $statusCounts,
            'classificationCounts' => $classificationCounts,
            'total' => $total,
            'filteredTotal' => $filteredTotal,
            'problemStatuses' => self::PROBLEM_STATUSES,
            'problemCount' => $problemCount,
            'hasActiveFilters' => $this->hasActiveFilters(),
            'locales' => $locales,
            'translationLanguages' => $translationLanguages,
            'translationFiles' => $translationFiles,
            'selectedTranslationKey' => $selectedTranslationKey,
            'editingTranslationKey' => $editingTranslationKey,
            'historyTranslationKey' => $historyTranslationKey,
            'historyEvents' => $historyEvents,
        ]);
    }

    /**
     * Normalize page size to allowed values.
     */
    private function normalizedPerPage(): int
    {
        return in_array($this->perPage, [10, 25, 50, 100], true)
            ? $this->perPage
            : 10;
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
}
