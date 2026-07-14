{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/keys/suggested-key.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Suggested key') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.sparkles />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keysSuggestedKey"
            variant="listbox"
            searchable
            :disabled="$keysNamespace === 'all' || $keysGroup === 'all'"
        >
            <x-ui.input.select-option
                value="all"
                icon="sparkles"
            >
                {{ $keysNamespace === 'all' || $keysGroup === 'all' ? __('Select namespace and group first') : __('All suggested keys') }}
            </x-ui.input.select-option>

            @foreach ($keyOptions['suggested_keys'] ?? [] as $suggestedKey)
                <x-ui.input.select-option
                    value="{{ $suggestedKey }}"
                    icon="sparkles"
                >
                    {{ $suggestedKey }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
