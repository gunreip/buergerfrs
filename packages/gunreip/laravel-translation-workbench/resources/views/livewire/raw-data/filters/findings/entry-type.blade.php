{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/findings/entry-type.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Entry type') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.tag />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="findingsEntryType"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="tag"
            >
                {{ __('All entry types') }}
            </x-ui.input.select-option>

            @foreach ($findingOptions['entry_types'] ?? [] as $entryType)
                @php
                    $entryTypeLabel = match ($entryType) {
                        'dynamic' => __('Dynamic values'),
                        'dynamic_numeric' => __('Numeric dynamic'),
                        'key' => __('ui.translation.translation-key'),
                        'literal' => __('ui.literal.literal'),
                        default => $entryType,
                    };
                    $entryTypeIcon = match ($entryType) {
                        'dynamic' => 'list-tree',
                        'dynamic_numeric' => 'calculator',
                        'key' => 'key-round',
                        default => 'tag',
                    };
                @endphp
                <x-ui.input.select-option
                    value="{{ $entryType }}"
                    icon="{{ $entryTypeIcon }}"
                >
                    {{ $entryTypeLabel }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
