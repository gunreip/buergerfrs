{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/findings/kind.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Kind') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.tag />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="findingsKind"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="tag"
            >
                {{ __('All kinds') }}
            </x-ui.input.select-option>

            @foreach ($findingOptions['kinds'] ?? [] as $kind)
                @php
                    $kindLabel = match ($kind) {
                        'dynamic_multi' => __('Dynamic values'),
                        'dynamic_numeric' => __('Numeric dynamic'),
                        default => $kind,
                    };
                    $kindIcon = match ($kind) {
                        'dynamic_multi' => 'list-tree',
                        'dynamic_numeric' => 'calculator',
                        default => 'tag',
                    };
                @endphp
                <x-ui.input.select-option
                    value="{{ $kind }}"
                    icon="{{ $kindIcon }}"
                >
                    {{ $kindLabel }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
