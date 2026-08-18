{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters-key-inventory.blade.php --}}

<flux:separator text="{{ __('Key Inventory Filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-6">
    @include('translation-workbench::livewire.raw-data.filters.key-inventory.search', [
        'fieldClass' => 'md:col-span-3',
    ])

    @include('translation-workbench::livewire.raw-data.filters.key-inventory.id', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.key-inventory.translation-key', [
        'fieldClass' => 'md:col-span-2',
    ])

    @include('translation-workbench::livewire.raw-data.filters.key-inventory.namespace', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.key-inventory.group', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.key-inventory.key-type', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.key-inventory.status', [
        'fieldClass' => 'md:col-span-1',
    ])
</flux:field>

<flux:separator text="{{ __('Inventory State Filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-8">
    @include('translation-workbench::livewire.raw-data.filters.key-inventory.is-shared', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.key-inventory.is-ui', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.key-inventory.is-dynamic', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.key-inventory.is-dynamic-multi', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.key-inventory.has-active-code-usage', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.key-inventory.has-lang-values', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.key-inventory.is-orphaned-lang-value', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.key-inventory.candidate-for-lang-delete', [
        'fieldClass' => 'md:col-span-1',
    ])

    <flux:field class="md:col-span-8 md:justify-self-end">
        <div class="flex h-full items-end justify-end pt-6">
            <x-ui.button.reset
                label="{{ __('Reset') }}"
                wire:click="resetKeyInventoryFilters"
            />
        </div>
    </flux:field>
</flux:field>
