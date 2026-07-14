{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/findings/kind.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Kind') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.tag />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="findingsKind"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="tag"
            >
                {{ __('All kinds') }}
            </x-ui.input.select-option>

            @foreach ($findingOptions['kinds'] ?? [] as $kind)
                <x-ui.input.select-option
                    value="{{ $kind }}"
                    icon="tag"
                >
                    {{ $kind }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
