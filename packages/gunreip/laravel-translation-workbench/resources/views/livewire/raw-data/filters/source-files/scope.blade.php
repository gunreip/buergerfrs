{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/scope.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Scope') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.rows-3 />
        </flux:input.group.prefix>
        <flux:select
            wire:key="source-files-scope-{{ $sourceFilesContext }}-{{ count($sourceFileOptions['scopes'] ?? []) }}"
            wire:model.live="sourceFilesScope"
            variant="listbox"
            searchable
            :disabled="count($sourceFileOptions['scopes'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="rows-3"
            >
                {{ count($sourceFileOptions['scopes'] ?? []) === 0 ? __('No scopes') : __('All scopes') }}
            </x-ui.input.select-option>
            @foreach ($sourceFileOptions['scopes'] ?? [] as $scope)
                <x-ui.input.select-option
                    value="{{ $scope }}"
                    icon="rows-3"
                >
                    {{ $scope }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
