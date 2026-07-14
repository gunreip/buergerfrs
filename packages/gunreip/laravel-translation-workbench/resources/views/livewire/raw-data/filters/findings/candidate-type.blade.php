{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/findings/candidate-type.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Candidate') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.shield-cog />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="findingsCandidateType"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="shield-cog"
            >
                {{ __('All candidates') }}
            </x-ui.input.select-option>

            @foreach ($findingOptions['candidate_types'] ?? [] as $candidateType)
                <x-ui.input.select-option
                    value="{{ $candidateType }}"
                    icon="shield-cog"
                >
                    {{ $candidateType }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
