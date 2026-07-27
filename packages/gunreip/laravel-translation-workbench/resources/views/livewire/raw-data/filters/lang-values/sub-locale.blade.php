{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/lang-values/sub-locale.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Sub locale') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.languages />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="langValuesSubLocale"
            variant="listbox"
            searchable
            :disabled="$langValuesMainLocale === 'all'"
        >
            <x-ui.input.select-option
                value="all"
                icon="languages"
            >
                {{ $langValuesMainLocale === 'all' ? __('Select main locale first') : __('All sub locales') }}
            </x-ui.input.select-option>

            @foreach ($langValueOptions['sub_locales'] ?? [] as $locale)
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
