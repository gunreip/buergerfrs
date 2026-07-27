{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/reviews/decision.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Decision') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.badge-check />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="reviewsDecision"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="badge-check"
            >
                {{ __('ui.all-decisions') }}
            </x-ui.input.select-option>
            @foreach ($reviewOptions['decisions'] ?? [] as $decision)
                <x-ui.input.select-option
                    value="{{ $decision }}"
                    icon="badge-check"
                >
                    {{ $decision }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
