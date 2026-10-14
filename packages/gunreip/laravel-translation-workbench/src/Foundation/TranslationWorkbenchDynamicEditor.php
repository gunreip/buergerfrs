<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use App\Support\Locale\LocaleCode;
use Illuminate\Support\Facades\DB;
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
    public function save(int $keyId, int $findingId, array $sourceValues, array $targetValues): array
    {
        $this->access->authorize();
        Validator::make(compact('sourceValues', 'targetValues'), [
            'sourceValues' => ['array'], 'sourceValues.*' => ['nullable', 'string'],
            'targetValues' => ['array'], 'targetValues.*' => ['nullable', 'string'],
        ])->validate();

        return DB::transaction(function () use ($keyId, $findingId, $sourceValues, $targetValues): array {
            [$key, $finding] = $this->access->assignment($keyId, $findingId);
            $locales = $this->locales->resolve();
            $rows = $this->options->rows($keyId, $findingId, $locales);
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

    public function saveSource(int $keyId, int $findingId, string $fieldKey, mixed $value): ?array
    {
        $this->access->authorize();
        Validator::make(['value' => $value], ['value' => ['nullable', 'string']])->validate();

        return DB::transaction(function () use ($keyId, $findingId, $fieldKey, $value): ?array {
            [$key, $finding] = $this->access->assignment($keyId, $findingId);
            $locales = $this->locales->resolve();
            $rows = $this->options->rows($keyId, $findingId, $locales);
            $this->requireOption($fieldKey, array_column($rows, 'field_key'));
            $row = collect($rows)->firstWhere('field_key', $fieldKey);
            $source = $this->nullableString($value);

            return $this->writer->saveDynamicSourceValue($key, $finding, $row['value_key'],
                LocaleCode::normalize($locales['source']), $source, $source ?? $this->nullableString($row['native_label']));
        });
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
