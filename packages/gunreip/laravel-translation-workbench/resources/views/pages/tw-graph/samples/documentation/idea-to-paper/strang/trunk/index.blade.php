<flux:heading size="lg">{{ __('Strang Trunk') }}</flux:heading>
<flux:text>{{ __('The trunk combines paths and segments into a complete graph strand. Each example is authored separately with its own source code, props table, and preview controls.') }}</flux:text>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.strang_trunk_index" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="trunk-default">{{ __('Default') }}</flux:tab>
        <flux:tab name="trunk-stem-count">{{ __('Stem count') }}</flux:tab>
        <flux:tab name="trunk-stem-lengths">{{ __('Stem lengths') }}</flux:tab>
        <flux:tab name="trunk-direction">{{ __('Direction') }}</flux:tab>
        <flux:tab name="trunk-start">{{ __('Start') }}</flux:tab>
        <flux:tab name="trunk-start-shift">{{ __('Start shift') }}</flux:tab>
        <flux:tab name="trunk-end">{{ __('End') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="trunk-default">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_index'] === 'trunk-default')
            <div wire:key="documentation-trunk-default">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-default')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-stem-count">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_index'] === 'trunk-stem-count')
            <div wire:key="documentation-trunk-stem-count">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-stem-count')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-stem-lengths">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_index'] === 'trunk-stem-lengths')
            <div wire:key="documentation-trunk-stem-lengths">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-stem-lengths')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-direction">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_index'] === 'trunk-direction')
            <div wire:key="documentation-trunk-direction">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-direction')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-start">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_index'] === 'trunk-start')
            <div wire:key="documentation-trunk-start">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-start')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-start-shift">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_index'] === 'trunk-start-shift')
            <div wire:key="documentation-trunk-start-shift">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-start-shift')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="trunk-end">
        @if (!isset($documentationTabs) || $documentationTabs['strang_trunk_index'] === 'trunk-end')
            <div wire:key="documentation-trunk-end">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-end')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
