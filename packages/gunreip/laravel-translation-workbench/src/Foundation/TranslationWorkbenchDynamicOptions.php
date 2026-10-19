<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use App\Support\Locale\LocaleCode;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Existing option resolution shared by preview and the dynamic editor boundary. */
class TranslationWorkbenchDynamicOptions
{
    public function rows(int $keyId, int $findingId, array $editLocales): array
    {
        $selectedFinding = (object) ['id' => $findingId, 'key_id' => $keyId];
        $sourceLocale = LocaleCode::normalize((string) ($editLocales['source'] ?? config('translation-workbench.source_locale', 'en')));
        $targetLocale = LocaleCode::normalize((string) ($editLocales['active'] ?? app()->getLocale()));
        $keyExists = TranslationWorkbenchKey::query()
            ->whereKey($selectedFinding->key_id)
            ->exists();

        if (! $keyExists) {
            return [];
        }

        $storedValues = collect();

        if (Schema::hasTable('translation_workbench_dynamic_key_values')) {
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
            'translation_workbench_findings',
            'translation_workbench_source_files',
            'translation_workbench_key_findings',
        ])) {
            $sourceRows = collect();
        } else {
            $linkedRuntimeSourceIds = $this->linkedRuntimeSourceIdsForFinding($selectedFinding);

            $sourceRows = DB::table('translation_workbench_dynamic_source_values as source_values')
                ->join('translation_workbench_dynamic_sources as sources', 'sources.id', '=', 'source_values.dynamic_source_id')
                ->where('sources.status', '<>', 'obsolete')
                ->where('source_values.status', 'active')
                ->where(function ($query) use ($selectedFinding, $linkedRuntimeSourceIds): void {
                    $query->where(function ($runtime) use ($selectedFinding): void {
                        $runtime->whereNull('sources.finding_id')
                            ->whereIn('sources.source_type', $this->dynamicRuntimeSourceTypes())
                            ->where('sources.key_id', $selectedFinding->key_id)
                            ->whereExists(fn ($key) => $key->selectRaw('1')->from('translation_workbench_keys as runtime_key')
                                ->whereColumn('runtime_key.id', 'sources.key_id')->where('runtime_key.status', '<>', 'obsolete'));
                    })->orWhere(function ($owned) use ($selectedFinding): void {
                        $owned->where(function ($identity) use ($selectedFinding): void {
                            $identity->where('sources.finding_id', $selectedFinding->id)->orWhere('sources.key_id', $selectedFinding->key_id);
                        })->whereExists(function ($owner) use ($selectedFinding): void {
                            $owner->selectRaw('1')->from('translation_workbench_findings as source_owner')
                                ->join('translation_workbench_source_files as source_file', 'source_file.id', '=', 'source_owner.source_file_id')
                                ->join('translation_workbench_key_findings as source_link', 'source_link.finding_id', '=', 'source_owner.id')
                                ->join('translation_workbench_keys as source_key', 'source_key.id', '=', 'source_link.key_id')
                                ->whereColumn('source_owner.id', 'sources.finding_id')
                                ->where('source_link.key_id', $selectedFinding->key_id)
                                ->where(fn ($key) => $key->whereNull('sources.key_id')->orWhereColumn('sources.key_id', 'source_link.key_id'))
                                ->where('source_owner.status', 'active')->where('source_file.status', 'active')
                                ->where('source_link.status', 'active')->where('source_key.status', '<>', 'obsolete');
                        });
                    });

                    if ($linkedRuntimeSourceIds !== []) {
                        $query->orWhereIn('sources.id', $linkedRuntimeSourceIds);
                    }
                })
                ->orderBy('source_values.value_key')
                ->orderBy('sources.id')
                ->get(['source_values.value_key', 'source_values.native_label', 'source_values.status', 'sources.id as source_id', 'sources.source_reference'])
                ->groupBy('value_key')
                ->map(static function ($values) {
                    if ($values->pluck('native_label')->uniqueStrict()->count() <= 1) {
                        return $values->first();
                    }

                    return (object) ['native_label' => null, 'status' => 'needs_review',
                        'source_conflicts' => $values->map(static fn ($value) => [
                            'source_id' => $value->source_id, 'reference' => $value->source_reference,
                            'value' => $value->native_label,
                        ])->all()];
                });
        }

        $valueKeys = $sourceRows
            ->keys()
            ->merge($storedValues->keys())
            ->map(static fn (mixed $valueKey): string => (string) $valueKey)
            ->filter(static fn (string $valueKey): bool => $valueKey !== '')
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
                    ...(isset($sourceValueRow->source_conflicts) ? ['source_conflicts' => $sourceValueRow->source_conflicts] : []),
                ];
            })
            ->values()
            ->all();
    }

    public function linkedRuntimeSourceIdsForFinding(object $selectedFinding): array
    {
        if (! $this->hasTables([
            'translation_workbench_dynamic_sources',
            'translation_workbench_dynamic_source_candidates',
            'translation_workbench_findings',
            'translation_workbench_source_files',
            'translation_workbench_key_findings',
            'translation_workbench_keys',
        ])) {
            return [];
        }

        return DB::table('translation_workbench_dynamic_source_candidates as candidates')
            ->join('translation_workbench_dynamic_sources as runtime_sources', 'runtime_sources.id', '=', 'candidates.dynamic_source_id')
            ->where('candidates.candidate_source_type', 'related_dynamic_source')
            ->where('candidates.review_status', 'confirmed')
            ->where('candidates.status', 'active')
            // A historical confirmation remains stored, but only an active owner can supply current links.
            ->whereExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('translation_workbench_findings as owner')
                    ->join('translation_workbench_source_files as file', 'file.id', '=', 'owner.source_file_id')
                    ->join('translation_workbench_key_findings as link', 'link.finding_id', '=', 'owner.id')
                    ->join('translation_workbench_keys as key', 'key.id', '=', 'link.key_id')
                    ->whereColumn('owner.id', 'candidates.finding_id')
                    ->whereColumn('link.key_id', 'candidates.key_id')
                    ->where('owner.status', 'active')->where('file.status', 'active')
                    ->where('link.status', 'active')->where('key.status', '<>', 'obsolete');
            })
            ->where('runtime_sources.status', '<>', 'obsolete')
            ->whereIn('runtime_sources.source_type', $this->dynamicRuntimeSourceTypes())
            ->where(function ($query) use ($selectedFinding): void {
                $query->where('candidates.finding_id', $selectedFinding->id);

                if ($selectedFinding->key_id) {
                    $query->orWhere('candidates.key_id', $selectedFinding->key_id);
                }
            })
            ->pluck('runtime_sources.id')
            ->map(static fn (mixed $sourceId): int => (int) $sourceId)
            ->unique()
            ->values()
            ->all();
    }

    private function dynamicRuntimeSourceTypes(): array
    {
        return ['runtime_options', 'runtime_db_options'];
    }

    private function dynamicMultiFieldKey(string $valueKey): string
    {
        return 'v_'.sha1($valueKey);
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function hasTables(array $tables): bool
    {
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }
}
