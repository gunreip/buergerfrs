{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/tw-graph/data-driven/datasets.blade.php --}}

<flux:card class="translation-workbench">
    <x-ui.headers.page
        :title="__('TW-Graph Data Driven Datasets')"
        :description="__('Random timeline-chain datasets rendered as data-driven tw-graph previews.')"
    >
        @if ($mainRow)
            <flux:button
                icon="refresh-ccw"
                wire:click="reloadDataset"
                wire:loading.attr="disabled"
            >
                {{ __('Reload ID #:id', ['id' => $mainRow['id']]) }}
            </flux:button>
        @endif

        <flux:button
            icon="refresh-cw"
            variant="primary"
            wire:click="randomDataset"
            wire:loading.attr="disabled"
        >
            {{ __('Random dataset') }}
        </flux:button>

        @if (!empty($datasetHistory))
            <flux:select
                class="w-128 max-w-full"
                wire:model.live="selectedHistoryId"
            >
                @foreach ($datasetHistory as $historyEntry)
                    <flux:select.option value="{{ $historyEntry['id'] }}">
                        {{ $historyEntry['label'] }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        @endif

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
                'mainRow' => $mainRow,
                'rootRows' => collect($rootRows ?? []),
                'originRows' => collect($originRows ?? []),
                'dev' => false,
                'coordinates' => false,
            ]
        )
    @else
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
</flux:card>
