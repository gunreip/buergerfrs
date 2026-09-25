<flux:heading size="lg">{{ __('Trunk end') }}</flux:heading>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.strang_trunk_trunk_end" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="trunk-end-overview">{{ __('Overview') }}</flux:tab>
        <flux:tab name="trunk-end-default">{{ __('End default') }}</flux:tab>
        <flux:tab name="trunk-end-long-end">{{ __('End long end') }}</flux:tab>
        <flux:tab name="trunk-end-wide-label">{{ __('End wide label') }}</flux:tab>
        <flux:tab name="trunk-end-cap">{{ __('End cap') }}</flux:tab>
        <flux:tab name="trunk-end-color">{{ __('End color') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="trunk-end-overview">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_trunk_end'] === 'trunk-end-overview')
            <div wire:key="documentation-trunk-end-overview">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-end-overview')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-end-default">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_trunk_end'] === 'trunk-end-default')
            <div wire:key="documentation-trunk-end-default">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-end-default')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-end-long-end">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_trunk_end'] === 'trunk-end-long-end')
            <div wire:key="documentation-trunk-end-long-end">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-end-long-end')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-end-wide-label">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_trunk_end'] === 'trunk-end-wide-label')
            <div wire:key="documentation-trunk-end-wide-label">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-end-wide-label')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-end-cap">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_trunk_end'] === 'trunk-end-cap')
            <div wire:key="documentation-trunk-end-cap">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-end-cap')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-end-color">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_trunk_end'] === 'trunk-end-color')
            <div wire:key="documentation-trunk-end-color">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-end-color')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
