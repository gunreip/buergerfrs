{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/shared-key-candidates/min-finding-count.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Min. findings') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.search-check />
        </flux:input.group.prefix>
        <flux:select
            clearable
            variant="combobox"
            wire:model.live="sharedKeyCandidatesMinFindingCount"
            placeholder="{{ __('ui.count.min') }}"
        >
            @foreach ($sharedKeyCandidateOptions['min_finding_counts'] ?? [0] as $count)
                <flux:select.option value="{{ $count }}">{{ $count }}</flux:select.option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
