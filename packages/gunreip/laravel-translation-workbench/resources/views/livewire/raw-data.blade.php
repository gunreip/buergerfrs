{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data.blade.php --}}

{{--
TODO: Finalize filters for currently empty raw-data tables once real rows exist.
TODO: Scope or lazy-load large select option lists further if raw-data tables grow significantly.
TODO: Consider a shared FK preview pattern instead of adding table-specific FK context blocks one by one.
TODO: Consider extracting repeated result-row presentations once more raw-data tables need them.
TODO: Add a Raw Data Diagnostics callout with active filters, query count, table count, and visible row count.
--}}

<flux:card class="translation-workbench">
    {{-- Page Header --}}
    <x-ui.headers.page
        :title="$pageTitle"
        :description="$pageDescription"
    >
        <x-slot:meta>
            <flux:badge
                class="text-[0.65rem] font-normal leading-none"
                size="sm"
                color="zinc"
            >
                {{ $workbenchVersion['label'] ?? 'v0.7.0-dev' }}
            </flux:badge>
        </x-slot:meta>
    </x-ui.headers.page>

    <flux:tab.group class="mt-6 min-w-0 max-w-full">
        @include('translation-workbench::livewire.raw-data.table-tabs')
        @include('translation-workbench::livewire.raw-data.table-panel')
    </flux:tab.group>
</flux:card>
