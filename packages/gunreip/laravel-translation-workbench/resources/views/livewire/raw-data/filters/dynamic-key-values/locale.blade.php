{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-key-values/locale.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Locale') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.languages />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="dynamicKeyValuesLocale"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="language"
            >
                {{ __('ui.filters.all-locales') }}
            </x-ui.input.select-option>
            @foreach ($dynamicKeyValueOptions['locales'] ?? [] as $locale)
                <x-ui.input.select-option
                    value="{{ $locale }}"
                    icon="languages"
                >
                    {{ $locale }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
