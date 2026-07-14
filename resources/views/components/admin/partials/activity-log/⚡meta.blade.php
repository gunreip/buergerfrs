{{-- resources/views/components/admin/partials/activity-log/⚡meta.blade.php --}}

@php
    $summary = $summary ?? [];

    $metaCards = [
        [
            'label' => __('Total entries'),
            'value' => (int) data_get($summary, 'total_entries', 0),
            'color' => 'sky',
            'icon' => 'database',
        ],
        [
            'label' => __('Filtered entries'),
            'value' => (int) data_get($summary, 'filtered_entries', 0),
            'color' => 'emerald',
            'icon' => 'list-filter',
        ],
        [
            'label' => __('Latest ID'),
            'value' => (int) data_get($summary, 'latest_id', 0),
            'color' => 'zinc',
            'icon' => 'hash',
        ],
        [
            'label' => __('Log names'),
            'value' => (int) data_get($summary, 'log_names', 0),
            'color' => 'blue',
            'icon' => 'folder-search',
        ],
        [
            'label' => __('admin.translation_list.modal_history.events'),
            'value' => (int) data_get($summary, 'events', 0),
            'color' => 'purple',
            'icon' => 'waypoints',
        ],
        [
            'label' => __('With subject'),
            'value' => (int) data_get($summary, 'with_subject', 0),
            'color' => 'amber',
            'icon' => 'component',
        ],
        [
            'label' => __('With causer'),
            'value' => (int) data_get($summary, 'with_causer', 0),
            'color' => 'green',
            'icon' => 'user',
        ],
        [
            'label' => __('With data'),
            'value' => (int) data_get($summary, 'with_properties', 0) + (int) data_get($summary, 'with_changes', 0),
            'color' => 'violet',
            'icon' => 'braces',
        ],
    ];
@endphp

<flux:card
    class="mt-6"
    x-data="{ showMeta: true }"
>
    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">
            <x-ui.headers.card
                name="meta"
                :title="__('Meta')"
                :description="__('Current activity_log overview and filtered result counters.')"
            />
        </div>

        <div class="ml-auto flex shrink-0 items-center gap-3">
            <x-ui.button.show-hide
                size="xs"
                state="showMeta"
                show-label="{{ __('Show overview') }}"
                hide-label="{{ __('Hide overview') }}"
            />
        </div>
    </div>

    <div
        x-show="showMeta"
        x-collapse
    >
        <div class="grid flex-1 gap-3 md:grid-cols-1 xl:grid-cols-4">
            @foreach ($metaCards as $metaCard)
                <flux:callout
                    class="hyphens-auto"
                    color="{{ $metaCard['color'] }}"
                    icon="{{ $metaCard['icon'] }}"
                    :heading="$metaCard['label']"
                >
                    <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                        {{ number_format((int) $metaCard['value']) }}
                    </flux:callout.text>
                </flux:callout>
            @endforeach
        </div>

    </div>
</flux:card>
