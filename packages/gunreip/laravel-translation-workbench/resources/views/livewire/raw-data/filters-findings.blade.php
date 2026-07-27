{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters-findings.blade.php --}}

<flux:separator text="{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.filters_event_types.common_filters') }}" />

<flux:field class="mt-2 grid gap-3 md:grid-cols-6">
    {{-- Findings Search Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.findings.search', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Findings ID Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.findings.id')

    {{-- Findings Source File Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.findings.source-file', [
        'fieldClass' => 'md:col-span-1',
    ])

    {{-- Findings Namespace Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.findings.namespace', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Findings Source Line Filter --}}
    {{-- @include('translation-workbench::livewire.raw-data.filters.findings.source-line') --}}

    {{-- Findings Status Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.findings.status')

    {{-- Findings Kind Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.findings.kind', [
        'fieldClass' => 'col-span-1',
    ])

    {{-- Findings Function Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.findings.function-name', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Findings Entry Type Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.findings.entry-type')

    {{-- Findings Candidate Type Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.findings.candidate-type')

    {{-- Findings Group Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.findings.group', [
        'fieldClass' => 'col-span-2',
    ])

    {{-- Findings Scope Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.findings.scope', [
        'fieldClass' => 'col-span-2',
    ])

    {{-- Findings Dynamic Scope Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.findings.dynamic-scope', [
        'fieldClass' => 'col-span-2',
    ])

    {{-- Findings Reset Filter --}}
    <flux:field class="col-span-6 md:justify-self-end">
        <div class="flex h-full items-end justify-end pt-6">
            <x-ui.button.reset
                label="{{ __('Reset') }}"
                wire:click="resetFindingsFilters"
            />
        </div>
    </flux:field>
</flux:field>
