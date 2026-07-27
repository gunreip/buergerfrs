{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-sources/cardinality.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_dynamic_review.cardinality') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.badge-check />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="dynamicSourcesCardinality"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="badge-check"
            >
                {{ __('ui.states.all') }}
            </x-ui.input.select-option>
            @foreach ($dynamicSourceOptions['cardinalities'] ?? [] as $option)
                <x-ui.input.select-option
                    value="{{ $option }}"
                    icon="badge-check"
                >
                    {{ $option }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
