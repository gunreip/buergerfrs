{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters-reviews.blade.php --}}

<flux:separator text="{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.filters_event_types.common_filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-6">
    @include('translation-workbench::livewire.raw-data.filters.reviews.search', [
        'fieldClass' => 'md:col-span-3',
    ])
</flux:field>

<flux:separator text="{{ __('ID filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-6">
    @include('translation-workbench::livewire.raw-data.filters.reviews.id', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.reviews.key', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.reviews.finding', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.reviews.reviewed-by-user', [
        'fieldClass' => 'md:col-span-1',
    ])
</flux:field>

<flux:separator text="{{ __('Review filters') }}" />

<flux:field class="mt-2 grid gap-3 md:grid-cols-6">
    @include('translation-workbench::livewire.raw-data.filters.reviews.review-type', [
        'fieldClass' => 'md:col-span-2',
    ])

    @include('translation-workbench::livewire.raw-data.filters.reviews.decision', [
        'fieldClass' => 'md:col-span-2',
    ])

    <flux:field class="md:col-span-2 md:justify-self-end">
        <div class="flex h-full items-end justify-end pt-6">
            <x-ui.button.reset
                label="{{ __('Reset') }}"
                wire:click="resetReviewsFilters"
            />
        </div>
    </flux:field>
</flux:field>
