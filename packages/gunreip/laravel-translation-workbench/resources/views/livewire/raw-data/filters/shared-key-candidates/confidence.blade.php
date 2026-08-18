{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/shared-key-candidates/confidence.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Confidence') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.activity />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="sharedKeyCandidatesConfidence"
            variant="listbox"
        >
            <x-ui.input.select-option value="all" icon="asterisk" icon-class="text-sky-400">{{ __('All confidence') }}</x-ui.input.select-option>

            @foreach ($sharedKeyCandidateOptions['confidences'] ?? [] as $confidence)
                <x-ui.input.select-option value="{{ $confidence }}" icon="activity">{{ $confidence }}</x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
