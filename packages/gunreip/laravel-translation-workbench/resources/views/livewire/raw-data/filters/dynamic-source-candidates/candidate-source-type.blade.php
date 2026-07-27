{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-source-candidates/candidate-source-type.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Candidate source') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.database />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="dynamicSourceCandidatesCandidateSourceType"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="database"
            >
                {{ __('ui.states.all') }}
            </x-ui.input.select-option>
            @foreach ($dynamicSourceCandidateOptions['candidate_source_types'] ?? [] as $option)
                <x-ui.input.select-option
                    value="{{ $option }}"
                    icon="database"
                >
                    {{ $option }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
