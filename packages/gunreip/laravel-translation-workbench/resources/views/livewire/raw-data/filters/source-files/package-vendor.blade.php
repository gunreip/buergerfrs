{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/package-vendor.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Package vendor') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.folder />
        </flux:input.group.prefix>
        <flux:select
            wire:key="source-files-package-vendor-{{ $sourceFilesRoot }}-{{ count($sourceFileOptions['package_vendors'] ?? []) }}"
            wire:model.live="sourceFilesPackageVendor"
            variant="listbox"
            searchable
            :disabled="count($sourceFileOptions['package_vendors'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="folder"
            >
                {{ count($sourceFileOptions['package_vendors'] ?? []) === 0 ? __('No vendors') : __('All vendors') }}
            </x-ui.input.select-option>
            @foreach ($sourceFileOptions['package_vendors'] ?? [] as $vendor)
                <x-ui.input.select-option
                    value="{{ $vendor }}"
                    icon="folder"
                >
                    {{ $vendor }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
