{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/keys/scope.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Scope') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.folder />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keysScope"
            variant="listbox"
            searchable
        >
            <x-ui.input.select-option
                value="all"
                icon="folder"
            >
                {{ __('ui.filters.all-scopes') }}
            </x-ui.input.select-option>

            @foreach ($keyOptions['scopes'] ?? [] as $scope)
                <x-ui.input.select-option
                    value="{{ $scope }}"
                    icon="folder"
                >
                    {{ $scope }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
