{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/domain.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>
        {{ __('Domain') }}
    </flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.route />
        </flux:input.group.prefix>
        <flux:select
            wire:key="source-files-domain-{{ $sourceFilesRoot }}-{{ $sourceFilesArea }}-{{ $sourceFilesPackageVendor }}-{{ $sourceFilesPackageName }}-{{ count($sourceFileOptions['domains'] ?? []) }}"
            wire:model.live="sourceFilesDomain"
            variant="listbox"
            searchable
            :disabled="count($sourceFileOptions['domains'] ?? []) === 0"
        >
            <x-ui.input.select-option
                value="all"
                icon="route"
            >
                {{ count($sourceFileOptions['domains'] ?? []) === 0 ? __('No domains') : __('ui.filters.all-domains') }}
            </x-ui.input.select-option>
            @foreach ($sourceFileOptions['domains'] ?? [] as $domain)
                <x-ui.input.select-option
                    value="{{ $domain }}"
                    icon="route"
                >
                    {{ $domain }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
