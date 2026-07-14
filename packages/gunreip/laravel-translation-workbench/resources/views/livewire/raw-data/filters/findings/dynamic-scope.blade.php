{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/findings/dynamic-scope.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Dynamic scope') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.code />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="findingsDynamicScope"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="code"
            >
                {{ __('All dynamic scopes') }}
            </x-ui.input.select-option>

            @foreach ($findingOptions['dynamic_scopes'] ?? [] as $dynamicScope)
                <x-ui.input.select-option
                    value="{{ $dynamicScope }}"
                    icon="code"
                >
                    {{ $dynamicScope }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
