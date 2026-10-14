<flux:tab.group>
    <flux:tabs wire:model.live="timelineChainTab">
        <flux:tab name="raw-data" icon="table">{{ __('Raw data') }}</flux:tab>
        <flux:tab name="tw-graph" icon="git-branch">{{ __('TW-Graph') }}</flux:tab>
    </flux:tabs>

    <flux:tab.panel name="raw-data">
        @if ($timelineChainTab === 'raw-data')
            @include('translation-workbench::livewire.raw-data.table-card')
            @include('translation-workbench::livewire.raw-data.table-summary')
        @endif
    </flux:tab.panel>

    <flux:tab.panel name="tw-graph">
        @if ($timelineChainTab === 'tw-graph')
            @include('translation-workbench::livewire.raw-data.results-timeline-chains')
        @endif
    </flux:tab.panel>
</flux:tab.group>
