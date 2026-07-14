{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/context.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Context') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.component />
        </flux:input.group.prefix>
        <flux:select
            wire:key="source-files-context-{{ $sourceFilesSection }}-{{ count($sourceFileOptions['contexts'] ?? []) }}"
            wire:model.live="sourceFilesContext"
            variant="listbox"
            searchable
            :disabled="count($sourceFileOptions['contexts'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="component"
            >
                {{ count($sourceFileOptions['contexts'] ?? []) === 0 ? __('No contexts') : __('All contexts') }}
            </x-ui.input.select-option>
            @foreach ($sourceFileOptions['contexts'] ?? [] as $context)
                <x-ui.input.select-option
                    value="{{ $context }}"
                    icon="component"
                >
                    {{ $context }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
