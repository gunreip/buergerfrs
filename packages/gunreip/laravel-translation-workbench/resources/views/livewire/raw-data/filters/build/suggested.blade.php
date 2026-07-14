{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/build/suggested.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label @class([
        'text-emerald-600 dark:text-emerald-400' => $builtSuggestedKey !== '' && $builtSuggestedKeyExists,
        'text-red-600 dark:text-red-400' => $builtSuggestedKey !== '' && ! $builtSuggestedKeyExists,
    ])>
        {{ __('Filtered suggested key result') }}
    </flux:label>

    <flux:textarea
        rows="auto"
        readonly
        copyable
        :invalid="$builtSuggestedKey !== '' && ! $builtSuggestedKeyExists"
        description="{{ __('This is the suggested key based on the selected segments. You can copy it to your clipboard.') }}"
    >
        {{ $builtSuggestedKey !== '' ? $builtSuggestedKey : __('Selected key segments') }}
    </flux:textarea>

</flux:field>
