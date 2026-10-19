<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use App\Support\Locale\LocaleCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/** User-authorized scalar edits with optional preview validation; no publication; includes database value revisions once migrated. */
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
    public function save(int $keyId, int $findingId, array $values, ?string $expectedFingerprint = null): array
    {
        $this->authorize();
        Validator::make(['values' => $values], [
            'values' => ['required', 'array'],
            'values.*' => ['required', 'array:locale,value'],
            'values.*.locale' => ['required', 'string'],
            'values.*.value' => ['present', 'nullable', 'string'],
        ])->validate();

        return DB::transaction(function () use ($keyId, $findingId, $values, $expectedFingerprint): array {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_lang_values IN SHARE ROW EXCLUSIVE MODE');
            }
            if ($expectedFingerprint !== null && ! hash_equals($expectedFingerprint, $this->fingerprint($keyId, $findingId))) {
                throw ValidationException::withMessages(['scalarEditConflict' => __('The translation changed after opening the editor. Reopen it and review the current values before saving.')]);
            }
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

    public function savePreview(int $keyId, int $findingId, array $values, string $fingerprint): array
    {
        return DB::transaction(function () use ($keyId, $findingId, $values, $fingerprint): array {
            $changes = $this->save($keyId, $findingId, $values, $fingerprint);

            return ['changes' => $changes, 'fingerprint' => $this->fingerprint($keyId, $findingId)];
        }, 3);
    }

    public function fingerprint(int $keyId, int $findingId, ?string $expectedTranslationKey = null): string
    {
        $key = DB::table('translation_workbench_keys')->where('id', $keyId)
            ->first(['id', 'translation_key', 'status', 'review_status']);
        if ($expectedTranslationKey !== null && $expectedTranslationKey !== ($key->translation_key ?? null)) {
            throw ValidationException::withMessages(['scalarEditConflict' => __('The translation key changed while opening the editor. Reopen it before editing.')]);
        }
        $finding = DB::table('translation_workbench_findings')->where('id', $findingId)
            ->first(['id', 'source_file_id', 'status']);
        $parts = $this->writer->keyParts((string) ($key->translation_key ?? ''));
        $values = DB::table('translation_workbench_lang_values')->where('namespace', $parts['namespace'])
            ->where('lang_key', $parts['lang_key'])->orderBy('id')
            ->get(['id', 'locale', 'translation_key', 'value', 'value_type', 'status', ...(Schema::hasColumn('translation_workbench_lang_values', 'value_revision') ? ['value_revision'] : [])]);
        $links = DB::table('translation_workbench_key_findings')->where('finding_id', $findingId)
            ->where('status', 'active')->orderBy('id')->get(['id', 'key_id', 'status']);
        $fileStatus = $finding ? DB::table('translation_workbench_source_files')->where('id', $finding->source_file_id)->value('status') : null;

        return hash('sha256', json_encode([$key, $finding, $fileStatus, $links, $values], JSON_THROW_ON_ERROR));
    }
}
