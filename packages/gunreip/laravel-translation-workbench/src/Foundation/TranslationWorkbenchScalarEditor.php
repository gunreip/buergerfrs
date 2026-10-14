<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use App\Support\Locale\LocaleCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/** User-authorized scalar edit boundary. No console bypass, publication or revision protocol. */
class TranslationWorkbenchScalarEditor
{
    public function __construct(
        private readonly TranslationWorkbenchTranslationWriter $writer,
        private readonly TranslationWorkbenchEditorLocales $locales,
    ) {}

    public function authorize(): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
    }

    /** @param array<int, array{locale: string, value: string|null}> $values */
    public function save(int $keyId, int $findingId, array $values): array
    {
        $this->authorize();
        Validator::make(['values' => $values], [
            'values' => ['required', 'array'],
            'values.*' => ['required', 'array:locale,value'],
            'values.*.locale' => ['required', 'string'],
            'values.*.value' => ['present', 'nullable', 'string'],
        ])->validate();

        return DB::transaction(function () use ($keyId, $findingId, $values): array {
            [$key, $finding] = app(TranslationWorkbenchEditorAccess::class)->assignment($keyId, $findingId);
            if (blank($key->translation_key)) {
                throw ValidationException::withMessages(['editFindingId' => __('A translation key is required before editing scalar values.')]);
            }

            $locales = $this->locales->resolve();
            $allowed = array_map(LocaleCode::normalize(...), [$locales['source'], $locales['active'], ...$locales['sub']]);
            foreach ($values as $index => &$value) {
                $value['locale'] = LocaleCode::normalize($value['locale']);
                if (! in_array($value['locale'], $allowed, true)) {
                    throw ValidationException::withMessages(["values.$index.locale" => __('This locale is not available in the translation editor.')]);
                }
            }
            unset($value);

            return $this->writer->save($key, $finding, $key->translation_key, $locales['source'], $values);
        });
    }
}
