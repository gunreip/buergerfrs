{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/findings/function-name.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Function') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.code />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="findingsFunctionName"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="code"
            >
                {{ __('All functions') }}
            </x-ui.input.select-option>

            @foreach ($findingOptions['function_names'] ?? [] as $functionName)
                <x-ui.input.select-option
                    value="{{ $functionName }}"
                    icon="code"
                >
                    {{ $functionName }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
