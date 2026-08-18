{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/source-type.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('ui.source.source-type') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.type />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="sourceFilesSourceType"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="type"
            >
                {{ __('All source types') }}
            </x-ui.input.select-option>

            @foreach ($sourceFileOptions['source_types'] ?? [] as $sourceType)
                <x-ui.input.select-option
                    value="{{ $sourceType }}"
                    icon="type"
                >
                    {{ $sourceType }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
