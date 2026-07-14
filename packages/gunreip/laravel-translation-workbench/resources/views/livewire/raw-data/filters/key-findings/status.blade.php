{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/key-findings/status.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Status') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.activity />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keyFindingsStatus"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="activity"
            >
                {{ __('All statuses') }}
            </x-ui.input.select-option>

            @foreach ($keyFindingOptions['statuses'] ?? [] as $status)
                <x-ui.input.select-option
                    value="{{ $status }}"
                    icon="activity"
                >
                    {{ $status }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
