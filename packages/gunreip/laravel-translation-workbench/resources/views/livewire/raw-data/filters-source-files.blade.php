{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters-source-files.blade.php --}}

<flux:separator text="{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.filters_event_types.common_filters') }}" />

<flux:field class="mb-4 grid gap-3 md:grid-cols-10">
    {{-- Source Files Search Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.search', [
        'fieldClass' => 'md:col-span-4',
    ])

    {{-- Source Files ID Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.id', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Source Files Source Type Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.source-type', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Source Files Extension Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.extension', [
        'fieldClass' => 'md:col-span-2',
    ])
</flux:field>

<flux:separator text="{{ __('Path / Package filters') }}" />

<flux:field class="mb-4 grid gap-3 md:grid-cols-10">
    {{-- Source Files Root Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.root', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Source Files Area Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.area', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Source Files Package Vendor Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.package-vendor', [
        'fieldClass' => 'md:col-span-3',
    ])

    {{-- Source Files Package Name Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.package-name', [
        'fieldClass' => 'md:col-span-3',
    ])
</flux:field>

<flux:separator text="{{ __('Path filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-10">
    {{-- Source Files Domain Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.domain', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Source Files Section Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.section', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Source Files Context Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.context', [
        'fieldClass' => 'md:col-span-3',
    ])

    {{-- Source Files Scope Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.scope', [
        'fieldClass' => 'md:col-span-3',
    ])
</flux:field>

<flux:field class="mb-4 grid gap-3 md:grid-cols-6">
    {{-- Source Files Filename Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.filename', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Source Files Path Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.path', [
        'fieldClass' => 'md:col-span-4',
    ])
</flux:field>

<flux:separator text="Other filters" />

<flux:field class="mb-4 grid gap-3 md:grid-cols-6">

    {{-- Source Files Extra Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.extra', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Source Files Status Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.status', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Source Files Reset Filter --}}
    <flux:field class="md:col-span-2 md:justify-self-end">
        <div class="flex h-full items-end justify-end pt-6">
            <x-ui.button.reset
                label="{{ __('Reset') }}"
                wire:click="resetSourceFilesFilters"
            />
        </div>
    </flux:field>

</flux:field>

<flux:separator text="{{ __('Build filtered paths') }}" />

<flux:field class="mb-6 mt-4 grid gap-3 md:grid-cols-4">
    {{-- Source Files Path Build --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.build.file-path', [
        'fieldClass' => 'col-span-2',
    ])

    {{-- Source Files Package Build --}}
    @include('translation-workbench::livewire.raw-data.filters.source-files.build.package-path', [
        'fieldClass' => 'col-span-2',
    ])
</flux:field>
