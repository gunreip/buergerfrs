<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.primitives_primitives_markers_connectors" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="primitives-markers-node">{{ __('Node') }}</flux:tab>
        <flux:tab name="primitives-markers-joint-arrow">{{ __('Joint Arrow') }}</flux:tab>
        <flux:tab name="primitives-markers-connector">{{ __('Connector') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="primitives-markers-node">
        @if (!isset($documentationTabs) || $documentationTabs['primitives_primitives_markers_connectors'] === 'primitives-markers-node')
            <div wire:key="documentation-primitives-markers-node">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-node')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="primitives-markers-joint-arrow">
        @if (!isset($documentationTabs) || $documentationTabs['primitives_primitives_markers_connectors'] === 'primitives-markers-joint-arrow')
            <div wire:key="documentation-primitives-markers-joint-arrow">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-joint-arrow')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="primitives-markers-connector">
        @if (!isset($documentationTabs) || $documentationTabs['primitives_primitives_markers_connectors'] === 'primitives-markers-connector')
            <div wire:key="documentation-primitives-markers-connector">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-connector')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
