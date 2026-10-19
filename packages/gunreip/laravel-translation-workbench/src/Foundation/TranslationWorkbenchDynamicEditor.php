<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use App\Support\Locale\LocaleCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/** Authorized option editing; locales, option identities and provenance are resolved server-side. */
class TranslationWorkbenchDynamicEditor
{
    public function __construct(
        private readonly TranslationWorkbenchEditorAccess $access,
        private readonly TranslationWorkbenchEditorLocales $locales,
        private readonly TranslationWorkbenchDynamicOptions $options,
        private readonly TranslationWorkbenchTranslationWriter $writer,
    ) {}

    /** @param array<string, string|null> $sourceValues
     * @param  array<string, string|null>  $targetValues
     */
    public function save(int $keyId, int $findingId, array $sourceValues, array $targetValues, ?string $expectedFingerprint = null): array
    {
        $this->access->authorize();
        Validator::make(compact('sourceValues', 'targetValues'), [
            'sourceValues' => ['array'], 'sourceValues.*' => ['nullable', 'string'],
            'targetValues' => ['array'], 'targetValues.*' => ['nullable', 'string'],
        ])->validate();

        return DB::transaction(function () use ($keyId, $findingId, $sourceValues, $targetValues, $expectedFingerprint): array {
            $this->validatePreview($keyId, $findingId, $expectedFingerprint);
            [$key, $finding] = $this->access->assignment($keyId, $findingId);
            $locales = $this->locales->resolve();
            $rows = $this->options->rows($keyId, $findingId, $locales);
            $this->requireUnambiguousSources($rows);
            $allowed = array_column($rows, 'field_key');
            foreach (array_unique([...array_keys($sourceValues), ...array_keys($targetValues)]) as $field) {
                $this->requireOption($field, $allowed);
            }
            $values = [];
            foreach ($rows as $row) {
                $field = $row['field_key'];
                $source = $this->nullableString($sourceValues[$field] ?? $row['source'] ?? $row['native_label'] ?? null);
                $native = $this->nullableString($row['native_label'] ?? $source);
                if ($source !== null) {
                    $values[] = ['value_key' => $row['value_key'], 'locale' => LocaleCode::normalize($locales['source']),
                        'value' => $source, 'native_label' => $native, 'source' => 'runtime_source'];
                }
                $values[] = ['value_key' => $row['value_key'], 'locale' => LocaleCode::normalize($locales['active']),
                    'value' => $targetValues[$field] ?? null, 'native_label' => $native, 'source' => 'translation_workbench_modal'];
            }

            return $this->writer->saveDynamicValues($key, $finding, $values);
        });
    }

    public function saveSource(int $keyId, int $findingId, string $fieldKey, mixed $value, ?string $expectedFingerprint = null): ?array
    {
        $this->access->authorize();
        Validator::make(['value' => $value], ['value' => ['nullable', 'string']])->validate();

        return DB::transaction(function () use ($keyId, $findingId, $fieldKey, $value, $expectedFingerprint): ?array {
            $this->validatePreview($keyId, $findingId, $expectedFingerprint);
            [$key, $finding] = $this->access->assignment($keyId, $findingId);
            $locales = $this->locales->resolve();
            $rows = $this->options->rows($keyId, $findingId, $locales);
            $this->requireOption($fieldKey, array_column($rows, 'field_key'));
            $row = collect($rows)->firstWhere('field_key', $fieldKey);
            $this->requireUnambiguousSources([$row]);
            $source = $this->nullableString($value);

            return $this->writer->saveDynamicSourceValue($key, $finding, $row['value_key'],
                LocaleCode::normalize($locales['source']), $source, $source ?? $this->nullableString($row['native_label']));
        });
    }

    public function fingerprint(int $keyId, int $findingId): string
    {
        $locales = $this->locales->resolve();
        $runtimeIds = $this->options->linkedRuntimeSourceIdsForFinding((object) ['id' => $findingId, 'key_id' => $keyId]);
        sort($runtimeIds);
        $sources = DB::table('translation_workbench_dynamic_sources')
            ->where(fn ($query) => $query->where('key_id', $keyId)->orWhere('finding_id', $findingId)->orWhereIn('id', $runtimeIds))
            ->orderBy('id')->get(['id', 'key_id', 'finding_id', 'source_type', 'source_reference', 'status']);
        $sourceValues = DB::table('translation_workbench_dynamic_source_values')->whereIn('dynamic_source_id', $sources->pluck('id'))
            ->orderBy('id')->get(['id', 'dynamic_source_id', 'value_key', 'native_label', 'status']);
        $stored = DB::table('translation_workbench_dynamic_key_values')->where('key_id', $keyId)->orderBy('id')
            ->get(['id', 'value_key', 'locale', 'value', 'native_label', 'source', 'status', ...(Schema::hasColumn('translation_workbench_dynamic_key_values', 'value_revision') ? ['value_revision'] : [])]);

        return hash('sha256', json_encode([
            app(TranslationWorkbenchScalarEditor::class)->fingerprint($keyId, $findingId),
            $locales, $runtimeIds, $sources, $sourceValues, $stored,
            $this->options->rows($keyId, $findingId, $locales),
        ], JSON_THROW_ON_ERROR));
    }

    private function validatePreview(int $keyId, int $findingId, ?string $expectedFingerprint): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_lang_values, translation_workbench_dynamic_sources, translation_workbench_dynamic_source_values, translation_workbench_dynamic_source_candidates, translation_workbench_dynamic_key_values IN SHARE ROW EXCLUSIVE MODE');
        }
        if ($expectedFingerprint !== null && ! hash_equals($expectedFingerprint, $this->fingerprint($keyId, $findingId))) {
            throw ValidationException::withMessages(['dynamicMultiSourceValues' => __('The options or translations changed after opening the editor. Reopen it and review the current values before saving.')]);
        }
    }

    private function requireUnambiguousSources(array $rows): void
    {
        $conflicts = collect($rows)->filter(fn ($row) => ! empty($row['source_conflicts']))->pluck('value_key');
        if ($conflicts->isNotEmpty()) {
            throw ValidationException::withMessages(['dynamicMultiSourceValues' => __('Conflicting option sources for :keys. Review the sources and refresh the editor before saving.', ['keys' => $conflicts->implode(', ')])]);
        }
    }

    private function requireOption(mixed $field, array $allowed): void
    {
        if (! in_array($field, $allowed, true)) {
            throw ValidationException::withMessages(['dynamicMultiSourceValues' => __('An option is no longer available for this entry. Refresh the editor and try again.')]);
        }
    }

    private function nullableString(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
