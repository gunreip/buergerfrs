<flux:tab.group class="min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.reference_parts" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="reference-parts-start">start</flux:tab>
        <flux:tab name="reference-parts-end">end</flux:tab>
        <flux:tab name="reference-parts-sideways">sideways</flux:tab>
        <flux:tab name="reference-parts-chain">chain</flux:tab>
        <flux:tab name="reference-parts-fusion">fusion</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="reference-parts-start">
        @if (!isset($documentationTabs) || $documentationTabs['reference_parts'] === 'reference-parts-start')
            <div wire:key="reference-parts-start">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.parts.start')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-parts-end">
        @if (!isset($documentationTabs) || $documentationTabs['reference_parts'] === 'reference-parts-end')
            <div wire:key="reference-parts-end">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.parts.end')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-parts-sideways">
        @if (!isset($documentationTabs) || $documentationTabs['reference_parts'] === 'reference-parts-sideways')
            <div wire:key="reference-parts-sideways">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.parts.sideways')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-parts-chain">
        @if (!isset($documentationTabs) || $documentationTabs['reference_parts'] === 'reference-parts-chain')
            <div wire:key="reference-parts-chain">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.parts.chain')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-parts-fusion">
        @if (!isset($documentationTabs) || $documentationTabs['reference_parts'] === 'reference-parts-fusion')
            <div wire:key="reference-parts-fusion">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.parts.fusion')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
