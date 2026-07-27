{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/event-types/category.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Category') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.folder />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="eventTypesCategory"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="folder"
            >
                {{ __('All categories') }}
            </x-ui.input.select-option>

            @foreach ($eventTypeOptions['categories'] ?? [] as $category)
                <x-ui.input.select-option
                    value="{{ $category }}"
                    icon="folder"
                    text-class="text-sm"
                >
                    {{ $category }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
