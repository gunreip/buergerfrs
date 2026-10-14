<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Scanner\TranslationKeyPartsFactory;
use Gunreip\TranslationWorkbench\Support\TranslationKeySegmentFactory;
use Illuminate\Support\Facades\Schema;

/** The existing key structure and classification rules, shared by review writers. */
class TranslationWorkbenchKeyAttributes
{
    public function forKey(?string $translationKey, ?TranslationWorkbenchKey $currentKey = null): array
    {
        $translationKey = $this->nullableString($translationKey);

        if ($translationKey === null) {
            return [
                'translation_key' => null,
                'namespace' => null,
                'group' => null,
                'path_key' => null,
                'scope' => null,
                'key_segment_domain' => null,
                'key_segment_section' => null,
                'key_segment_context' => null,
                'key_segment_extra' => null,
                'key_segment_name' => null,
                'is_ui_key' => false,
            ];
        }

        $isUiKey = $this->isUiTranslationKey($translationKey);

        return [
            'translation_key' => $translationKey,
            ...app(TranslationKeyPartsFactory::class)->fromKey($translationKey),
            ...app(TranslationKeySegmentFactory::class)->fromKey($translationKey),
            'is_ui_key' => $isUiKey,
            ...($isUiKey ? [
                'is_dynamic_key' => false,
                'is_dynamic_multi' => false,
                ...$this->dynamicDataStateAttributes(false),
                'key_type' => 'ui',
            ] : $this->nonUiClassificationAttributes($currentKey)),
        ];
    }

    private function nonUiClassificationAttributes(?TranslationWorkbenchKey $currentKey): array
    {
        if (! $currentKey || $currentKey->key_type !== 'ui') {
            return [];
        }

        $isDynamic = (bool) $currentKey->is_dynamic_key || (bool) $currentKey->is_dynamic_multi;

        return [
            'key_type' => $isDynamic ? 'dynamic' : 'static',
        ];
    }

    public function isUiTranslationKey(?string $translationKey): bool
    {
        $translationKey = $this->nullableString($translationKey);

        return $translationKey !== null && str_starts_with($translationKey, 'ui.');
    }

    public function dynamicDataStateAttributes(bool $isDynamic): array
    {
        if (! Schema::hasColumn('translation_workbench_keys', 'dynamic_data_state')) {
            return [];
        }

        return [
            'dynamic_data_state' => $isDynamic ? 'unstructured' : null,
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
