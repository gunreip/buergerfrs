<section class="space-y-4 min-w-0">
    <flux:heading size="lg">Deep Reference</flux:heading>
    <flux:text>Component reference: public props, nested array options and connection anchors. Example tables continue to describe only the props used in their example. Select a public Strang or Parts component below. Each has its own reference, including nested array fields and connection semantics. Archived components are excluded.</flux:text>
    <flux:tab.group class="min-w-0 max-w-full">
        <flux:tabs wire:model.live="tabs.reference_index">
            <flux:tab name="reference-strang">Strang</flux:tab>
            <flux:tab name="reference-parts">Parts</flux:tab>
        </flux:tabs>
        <flux:tab.panel name="reference-strang">
            @if (!isset($documentationTabs) || $documentationTabs['reference_index'] === 'reference-strang')
                <div wire:key="reference-strang">
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.index')
                </div>
            @endif
        </flux:tab.panel>
        <flux:tab.panel name="reference-parts">
            @if (!isset($documentationTabs) || $documentationTabs['reference_index'] === 'reference-parts')
                <div wire:key="reference-parts">
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.parts.index')
                </div>
            @endif
        </flux:tab.panel>
    </flux:tab.group>
</section>
