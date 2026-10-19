{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/tw-graph/data-driven/datasets.blade.php --}}

<flux:card class="translation-workbench">
    @include('translation-workbench::livewire.tw-graph.data-driven.loading-overlay')
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
            :color="$graphScope === 'finding' ? 'violet' : 'cyan'"
            icon="database"
        >
            <flux:callout.heading>
                <span class="inline-flex min-w-0 flex-wrap items-center gap-2">
                    <span>{{ $graphScope === 'finding' ? __('Finding history') : __('Timeline chain') }}</span>
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
            <flux:callout.text>
                {{ __('Dataset #:dataset · Root finding #:finding · Root key #:key', ['dataset' => $mainRow['id'], 'finding' => $mainRow['root_finding_id'] ?? '—', 'key' => $mainRow['root_key_id'] ?? '—']) }}
            </flux:callout.text>
            <flux:callout.text>
                @if ($graphScope === 'chain')
                {{ __('Stored graph snapshot updated: :date. The current Workbench history may contain newer events.', ['date' => $mainRow['updated_at'] ?? '—']) }}
                @else
                    {{ __('Current finding history from the database; key assignments are context, not a change of finding ID.') }}
                @endif
                {{ __('Label details: right-click a text label marked with the information icon.') }}
            </flux:callout.text>
        </flux:callout>

        <div class="my-3 flex flex-wrap gap-3">
            <flux:select wire:model.live="graphScope" :label="__('Graph view')" class="max-w-xs">
                <flux:select.option value="chain">{{ __('Timeline chain') }}</flux:select.option>
                <flux:select.option value="finding">{{ __('Finding history') }}</flux:select.option>
            </flux:select>
            @if ($graphScope === 'finding')
                <flux:select wire:model.live="graphFindingId" :label="__('Finding ID')" class="max-w-xs">
                    <flux:select.option value="">{{ __('Root finding') }} #{{ $mainRow['root_finding_id'] }}</flux:select.option>
                    @foreach ($findingIds as $findingOption)
                        <flux:select.option :value="$findingOption">Finding #{{ $findingOption }}</flux:select.option>
                    @endforeach
                </flux:select>
            @endif
        </div>
        @if ($graphScope === 'finding')
            @include('translation-workbench::livewire.tw-graph.data-driven.dataset-selector')
            @include('translation-workbench::livewire.tw-graph.data-driven.finding-graph')
        @else
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
        @endif
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
        <livewire:translation-workbench.tw-graph.data-driven.dataset-details :dataset-id="(int) $mainRow['id']" :finding-id="$finding?->id" :key="'dataset-details-'.$mainRow['id'].'-'.($finding?->id ?? 'chain')" />
    @endif
</flux:card>
