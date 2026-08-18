{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/shared-key-candidates/min-review-count.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Min. reviews') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.badge-check />
        </flux:input.group.prefix>
        <flux:select
            clearable
            variant="combobox"
            wire:model.live="sharedKeyCandidatesMinReviewCount"
            placeholder="{{ __('ui.count.min') }}"
        >
            @foreach ($sharedKeyCandidateOptions['min_review_counts'] ?? [0] as $count)
                <flux:select.option value="{{ $count }}">{{ $count }}</flux:select.option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
