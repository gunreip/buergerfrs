{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/data-driven/datasets.blade.php --}}

<x-layouts::app :title="__('TW-Graph Data Driven Datasets')">
    <flux:card class="translation-workbench">
        <x-ui.headers.page
            :title="__('TW-Graph Data Driven Datasets')"
            :description="__('Data-driven tw-graph result previews will be collected here.')"
        />

        <flux:callout
            class="mt-6"
            color="zinc"
            icon="database"
        >
            <flux:callout.heading>{{ __('Datasets') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Placeholder for data-driven tw-graph dataset previews.') }}
            </flux:callout.text>
        </flux:callout>
    </flux:card>
</x-layouts::app>
