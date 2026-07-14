{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/build/translation.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label @class([
        'text-emerald-600 dark:text-emerald-400' => $builtTranslationKey !== '' && $builtTranslationKeyExists,
        'text-red-600 dark:text-red-400' => $builtTranslationKey !== '' && ! $builtTranslationKeyExists,
    ])>
        {{ __('Filtered translation key result') }}
    </flux:label>

    <flux:textarea
        rows="auto"
        readonly
        copyable
        :invalid="$builtTranslationKey !== '' && ! $builtTranslationKeyExists"
        description="{{ __('This is the translation key based on the selected segments. You can copy it to your clipboard.') }}"
    >
        {{ $builtTranslationKey !== '' ? $builtTranslationKey : __('Select key segments') }}
    </flux:textarea>

</flux:field>
