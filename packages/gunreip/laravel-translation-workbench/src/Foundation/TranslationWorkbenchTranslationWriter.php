<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use App\Support\Locale\LocaleCode;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Shared persistence for scalar and dynamic-option translation editors.
 * Callers still resolve permissions, linked key and editable locales.
 * This extraction does not implement publication, bulk/key review or stale-edit protection.
 */
class TranslationWorkbenchTranslationWriter
{
    public function __construct(private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder) {}

    /**
     * Values are ordered to retain the existing source/target behavior when locales coincide.
     *
     * @param  array<int, array{locale: string, value: string|null}>  $values
     * @return array<int, array{locale: string, old: ?string, new: ?string}>
     */
    public function save(
        TranslationWorkbenchKey $key,
        TranslationWorkbenchFinding $finding,
        string $translationKey,
        string $sourceLocale,
        array $values,
        string $origin = 'translation-workbench:modal-edit',
    ): array {
        return DB::transaction(function () use ($key, $finding, $translationKey, $sourceLocale, $values, $origin): array {
            $changes = [];
            foreach ($values as $value) {
                $change = $this->saveLocale($finding, $key, $sourceLocale, $origin, $translationKey, $value['locale'], $value['value']);
                if ($change !== null) {
                    $changes[] = $change;
                }
            }

            if ($changes !== []) {
                $this->timelineRecorder->recordKeyFindingEvent(
                    key: $key,
                    finding: $finding,
                    eventType: 'translation_values_saved',
                    oldValues: ['values' => collect($changes)->pluck('old', 'locale')->all()],
                    newValues: ['values' => collect($changes)->pluck('new', 'locale')->all()],
                    context: [
                        'source' => $origin,
                        'locales' => collect($changes)->pluck('locale')->values()->all(),
                    ],
                );
            }

            return $changes;
        });
    }

    /**
     * @return array{locale: string, old: ?string, new: ?string}|null
     */
    private function saveLocale(
        TranslationWorkbenchFinding $finding,
        TranslationWorkbenchKey $key,
        string $sourceLocale,
        string $origin,
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
        $keyParts = $this->keyParts($translationKey);
        $langValue = TranslationWorkbenchLangValue::query()
            ->where('locale', $locale)
            ->where('namespace', $keyParts['namespace'])
            ->where('lang_key', $keyParts['lang_key'])
            ->first();
        $oldValue = $this->nullableString($langValue?->value);
        $newStatus = $newValue !== null ? 'active' : 'missing';

        if (! $langValue && $newValue === null) {
            return null;
        }

        $now = now();
        $localeAttributes = $this->localeAttributes($locale, $sourceLocale);
        $attributes = [
            ...$localeAttributes,
            'translation_key' => $translationKey,
            'value' => $newValue,
            'value_type' => 'string',
            'source_path' => 'lang/'.$locale.'/'.$keyParts['namespace'].'.php',
            'source_hash' => $newValue !== null ? sha1($newValue) : null,
            'status' => $newStatus,
            'last_seen_at' => $now,
            'meta' => [
                'source' => $origin,
                'finding_id' => $finding->id,
                'key_id' => $key->id,
            ],
        ];

        if ($langValue) {
            $trackedAttributeKeys = [
                'locale_role',
                'main_locale',
                'parent_locale',
                'is_source_locale',
                'translation_key',
                'value',
                'value_type',
                'source_path',
                'source_hash',
                'status',
            ];
            $oldAttributes = $langValue->only($trackedAttributeKeys);
            $changedAttributes = collect($attributes)
                ->only($trackedAttributeKeys)
                ->filter(static fn (mixed $value, string $key): bool => is_bool($value)
                    ? (bool) $oldAttributes[$key] !== $value
                    : $oldAttributes[$key] !== $value)
                ->all();

            if ($oldValue === $newValue && $changedAttributes === []) {
                return null;
            }

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
     * @param  array<int, array{value_key: string, locale: string, value: ?string, native_label: ?string, source: string}>  $values
     * @return array<int, array{value_key: string, locale: string, old: ?string, new: ?string}>
     */
    public function saveDynamicValues(TranslationWorkbenchKey $key, TranslationWorkbenchFinding $finding, array $values): array
    {
        return $this->writeDynamicValues($key, $finding, $values, false);
    }

    /**
     * @return array{value_key: string, locale: string, old: ?string, new: ?string}|null
     */
    public function saveDynamicSourceValue(
        TranslationWorkbenchKey $key,
        TranslationWorkbenchFinding $finding,
        string $valueKey,
        string $locale,
        ?string $value,
        ?string $nativeLabel,
    ): ?array {
        $changes = $this->writeDynamicValues($key, $finding, [[
            'value_key' => $valueKey, 'locale' => $locale, 'value' => $value,
            'native_label' => $nativeLabel, 'source' => 'translation_workbench_source_correction',
        ]], true);

        return $changes[0] ?? null;
    }

    /**
     * The editor resolves option membership and locales before invoking this persistence boundary.
     * History retains the existing event types, value-key/locale identifiers and context shape.
     */
    private function writeDynamicValues(TranslationWorkbenchKey $key, TranslationWorkbenchFinding $finding, array $values, bool $sourceCorrection): array
    {
        return DB::transaction(function () use ($key, $finding, $values, $sourceCorrection): array {
            $changes = [];
            foreach ($values as $value) {
                $change = $this->saveDynamicLocale(
                    $key, $finding, $value['value_key'], $value['locale'],
                    $value['value'], $value['native_label'], $value['source'],
                );
                if ($change !== null) {
                    $changes[] = $change;
                }
            }
            if ($changes !== []) {
                $context = ['source' => 'translation-workbench:modal-edit-dynamic-multi'];
                $context += $sourceCorrection
                    ? ['locale' => $changes[0]['locale'], 'value_key' => $changes[0]['value_key']]
                    : ['locales' => collect($changes)->pluck('locale')->unique()->values()->all(),
                        'value_keys' => collect($changes)->pluck('value_key')->unique()->values()->all()];
                $this->timelineRecorder->recordKeyFindingEvent(
                    key: $key,
                    finding: $finding,
                    eventType: $sourceCorrection ? 'dynamic_multi_source_value_saved' : 'dynamic_multi_values_saved',
                    oldValues: ['values' => collect($changes)->mapWithKeys(static fn (array $change): array => [
                        $change['value_key'].':'.$change['locale'] => $change['old'],
                    ])->all()],
                    newValues: ['values' => collect($changes)->mapWithKeys(static fn (array $change): array => [
                        $change['value_key'].':'.$change['locale'] => $change['new'],
                    ])->all()],
                    context: $context,
                );
            }

            return $changes;
        });
    }

    /**
     * @return array{value_key: string, locale: string, old: ?string, new: ?string}|null
     */
    private function saveDynamicLocale(
        TranslationWorkbenchKey $key,
        TranslationWorkbenchFinding $finding,
        string $valueKey,
        string $locale,
        mixed $value,
        ?string $nativeLabel,
        string $source,
    ): ?array {
        if (! Schema::hasTable('translation_workbench_dynamic_key_values') || ! $key->id) {
            return null;
        }

        $locale = LocaleCode::normalize($locale);
        $valueKey = trim($valueKey);

        if ($locale === '' || $valueKey === '') {
            return null;
        }

        $newValue = $this->nullableString($value);
        $dynamicValue = DB::table('translation_workbench_dynamic_key_values')
            ->where('key_id', $key->id)
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
                'finding_id' => $finding->id,
                'key_id' => $key->id,
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
                    'key_id' => $key->id,
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
     * @return array{namespace: string, lang_key: string}
     */
    public function keyParts(string $translationKey): array
    {
        $segments = collect(explode('.', $translationKey))
            ->map(static fn (string $segment): string => trim($segment))
            ->filter(static fn (string $segment): bool => $segment !== '')
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
    private function localeAttributes(string $locale, string $sourceLocale): array
    {
        $mainLocale = (string) (LocaleCode::parts($locale)['language'] ?? $locale);
        $isSourceLocale = $locale === $sourceLocale;
        $isSubLocale = str_contains($locale, '-');

        return [
            'is_source_locale' => $isSourceLocale,
            'locale_role' => $isSourceLocale ? 'source_main' : ($isSubLocale ? 'target_sub' : 'target_main'),
            'main_locale' => $isSourceLocale ? $sourceLocale : $mainLocale,
            'parent_locale' => $isSubLocale ? $mainLocale : null,
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
