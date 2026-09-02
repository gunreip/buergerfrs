{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/resume-a-einstein.blade.php --}}

<x-layouts::app :title="__('TW-Graph Sample: Resume A. Einstein')">
    <flux:card class="translation-workbench">
        <x-ui.headers.page
            :title="__('Resume A. Einstein')"
            :description="__('Hand-authored tw-graph samples will be collected here.')"
        />

        <flux:callout
            class="mt-6"
            color="zinc"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Sample') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Placeholder for a hand-authored tw-graph resume sample.') }}
            </flux:callout.text>
        </flux:callout>
    </flux:card>
</x-layouts::app>
