{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/lang-values/locale.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Main locale') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.languages />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="langValuesMainLocale"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="languages"
            >
                {{ __('All main locales') }}
            </x-ui.input.select-option>

            @foreach ($langValueOptions['main_locales'] ?? [] as $locale)
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
