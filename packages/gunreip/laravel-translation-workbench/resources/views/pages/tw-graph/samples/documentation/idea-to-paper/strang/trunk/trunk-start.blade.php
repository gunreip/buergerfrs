<flux:heading size="lg">Trunk start</flux:heading>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.strang_trunk_trunk_start" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="trunk-start-overview">{{ __('Overview') }}</flux:tab>
        <flux:tab name="trunk-start-compare">{{ __('Start compare') }}</flux:tab>
        <flux:tab name="trunk-start-default">{{ __('Start default') }}</flux:tab>
        <flux:tab name="trunk-start-long-start">{{ __('Start long start') }}</flux:tab>
        <flux:tab name="trunk-start-wide-labels">{{ __('Start wide labels') }}</flux:tab>
        <flux:tab name="trunk-start-spacing">{{ __('Start spacing') }}</flux:tab>
        <flux:tab name="trunk-start-colors">{{ __('Start colors') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="trunk-start-overview">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_trunk_start'] === 'trunk-start-overview')
            <div wire:key="documentation-trunk-start-overview">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-start-overview')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-start-compare">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_trunk_start'] === 'trunk-start-compare')
            <div wire:key="documentation-trunk-start-compare">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-start-compare')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-start-default">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_trunk_start'] === 'trunk-start-default')
            <div wire:key="documentation-trunk-start-default">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-start-default')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-start-long-start">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_trunk_start'] === 'trunk-start-long-start')
            <div wire:key="documentation-trunk-start-long-start">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-start-long-start')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-start-wide-labels">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_trunk_start'] === 'trunk-start-wide-labels')
            <div wire:key="documentation-trunk-start-wide-labels">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-start-wide-labels')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-start-spacing">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_trunk_start'] === 'trunk-start-spacing')
            <div wire:key="documentation-trunk-start-spacing">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-start-spacing')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-start-colors">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_trunk_start'] === 'trunk-start-colors')
            <div wire:key="documentation-trunk-start-colors">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-start-colors')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
