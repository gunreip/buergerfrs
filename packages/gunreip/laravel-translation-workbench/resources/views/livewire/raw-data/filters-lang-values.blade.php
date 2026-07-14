{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters-lang-values.blade.php --}}

<flux:separator text="{{ __('Lang Value Filters') }}" />

<flux:field class="mt-2 grid gap-3 md:grid-cols-8">
    {{-- Lang Values Search Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.lang-values.search', [
        'fieldClass' => 'md:col-span-3',
    ])

    {{-- Lang Values ID Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.lang-values.id', [
        'fieldClass' => 'md:col-span-1',
    ])

    {{-- Lang Values Main Locale Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.lang-values.locale', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Lang Values Sub Locale Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.lang-values.sub-locale', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Lang Values Namespace Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.lang-values.namespace', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Lang Values Lang Key Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.lang-values.lang-key', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Lang Values Translation Key Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.lang-values.translation-key', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Lang Values Value Type Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.lang-values.value-type', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Lang Values Source Path Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.lang-values.source-path', [
        'fieldClass' => 'md:col-span-3',
    ])

    {{-- Lang Values Status Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.lang-values.status', [
        'fieldClass' => 'md:col-span-1',
    ])

    {{-- Lang Values Reset Filter --}}
    <flux:field class="md:col-span-4 md:justify-self-end">
        <div class="flex h-full items-end justify-end pt-6">
            <x-ui.button.reset
                label="{{ __('Reset') }}"
                wire:click="resetLangValuesFilters"
            />
        </div>
    </flux:field>
</flux:field>
