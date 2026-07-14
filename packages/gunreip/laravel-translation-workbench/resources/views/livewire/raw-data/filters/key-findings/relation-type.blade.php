{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/key-findings/relation-type.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Relation type') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.link />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keyFindingsRelationType"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="link"
            >
                {{ __('All relation types') }}
            </x-ui.input.select-option>

            @foreach ($keyFindingOptions['relation_types'] ?? [] as $relationType)
                <x-ui.input.select-option
                    value="{{ $relationType }}"
                    icon="link"
                >
                    {{ $relationType }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
