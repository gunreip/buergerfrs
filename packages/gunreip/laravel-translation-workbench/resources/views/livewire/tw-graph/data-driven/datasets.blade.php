{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/tw-graph/data-driven/datasets.blade.php --}}

<flux:card class="translation-workbench">
    <div
        data-tw-graph-loading
        wire:ignore
        class="fixed inset-0 z-100 flex items-center justify-center bg-white/60 backdrop-blur-sm dark:bg-zinc-950/60"
        role="status"
        aria-live="polite"
    >
        <div class="flex items-center gap-3 rounded-xl border border-zinc-200 bg-white px-5 py-4 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
            <flux:icon.loading class="size-6 text-sky-600 dark:text-sky-400" />
            <flux:text>{{ __('Loading graph and calculating layout…') }}</flux:text>
        </div>
    </div>
    <x-ui.headers.page
        :title="__('TW-Graph Data Driven Datasets')"
        :description="__('Select timeline-chain datasets by ID, history or random selection.')"
    >
        <x-slot:meta>
            @if ($mainRow)
                <flux:badge
                    class="text-[0.65rem] font-normal leading-none"
                    size="sm"
                    color="cyan"
                >
                    {{ __('Timeline chain ID #:id', ['id' => $mainRow['id']]) }}
                </flux:badge>
            @endif
        </x-slot:meta>
    </x-ui.headers.page>

    @if ($mainRow)
        <flux:callout
            class="mt-6"
            color="zinc"
            icon="database"
        >
            <flux:callout.heading>
                <span class="inline-flex min-w-0 flex-wrap items-center gap-2">
                    <span>{{ __('Selected dataset') }}</span>
                    <flux:badge
                        size="sm"
                        color="zinc"
                    >
                        {{ str((string) $mainRow['chain_type'])->headline() }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="{{ $mainRow['chain_status'] === 'active' ? 'green' : 'amber' }}"
                    >
                        {{ str((string) $mainRow['chain_status'])->headline() }}
                    </flux:badge>
                </span>
            </flux:callout.heading>
            <flux:callout.text>
                <span class="break-anywhere block min-w-0 font-mono text-xs">
                    {{ $mainRow['translation_key'] }}
                </span>
            </flux:callout.text>
        </flux:callout>

        @include(
            'translation-workbench::livewire.raw-data.timeline-chains.graph-preview.tw-graph-data-driven',
            [
                'showDatasetSelector' => true,
                'mainRow' => $mainRow,
                'rootRows' => collect($rootRows ?? []),
                'originRows' => collect($originRows ?? []),
                'dev' => false,
                'coordinates' => false,
            ]
        )
    @else
        @include('translation-workbench::livewire.tw-graph.data-driven.dataset-selector')
        <flux:callout
            class="mt-6"
            color="amber"
            icon="database"
        >
            <flux:callout.heading>{{ __('No timeline-chain datasets found') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Run the timeline-chain collector first, then come back here for random tw-graph previews.') }}
            </flux:callout.text>
        </flux:callout>
    @endif
    @if ($mainRow)
        <livewire:translation-workbench.tw-graph.data-driven.dataset-details :dataset-id="(int) $mainRow['id']" :key="'dataset-details-'.$mainRow['id']" />
    @endif
</flux:card>
