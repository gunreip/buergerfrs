{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/keys/review-status.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Review') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.activity />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keysReviewStatus"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="activity"
            >
                {{ __('All reviews') }}
            </x-ui.input.select-option>

            @foreach ($keyOptions['review_statuses'] ?? [] as $reviewStatus)
                <x-ui.input.select-option
                    value="{{ $reviewStatus }}"
                    icon="activity"
                >
                    {{ $reviewStatus }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
