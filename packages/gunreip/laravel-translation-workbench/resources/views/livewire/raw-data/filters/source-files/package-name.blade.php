{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/package-name.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Package') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.component />
        </flux:input.group.prefix>
        <flux:select
            wire:key="source-files-package-name-{{ $sourceFilesPackageVendor }}-{{ count($sourceFileOptions['package_names'] ?? []) }}"
            wire:model.live="sourceFilesPackageName"
            variant="listbox"
            searchable
            :disabled="count($sourceFileOptions['package_names'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="component"
            >
                {{ count($sourceFileOptions['package_names'] ?? []) === 0 ? __('No packages') : __('All packages') }}
            </x-ui.input.select-option>
            @foreach ($sourceFileOptions['package_names'] ?? [] as $packageName)
                <x-ui.input.select-option
                    value="{{ $packageName }}"
                    icon="component"
                >{{ $packageName }}</x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
