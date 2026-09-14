<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.canvas_index" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="canvas-default">{{ __('Default') }}</flux:tab>
        <flux:tab name="canvas-borders">{{ __('Borders') }}</flux:tab>
        <flux:tab name="canvas-default-trunk">{{ __('Default + trunk') }}</flux:tab>
        <flux:tab name="canvas-coordinates">{{ __('Canvas + coord') }}</flux:tab>
        <flux:tab name="canvas-height">{{ __('Canvas height') }}</flux:tab>
        <flux:tab name="canvas-props">{{ __('Canvas + props') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="canvas-default">
        @if (!isset($documentationTabs) || $documentationTabs['canvas_index'] === 'canvas-default')
            <div wire:key="documentation-canvas-default">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-default')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="canvas-borders">
        @if (!isset($documentationTabs) || $documentationTabs['canvas_index'] === 'canvas-borders')
            <div wire:key="documentation-canvas-borders">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-borders')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="canvas-default-trunk">
        @if (!isset($documentationTabs) || $documentationTabs['canvas_index'] === 'canvas-default-trunk')
            <div wire:key="documentation-canvas-default-trunk">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-default-trunk')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="canvas-coordinates">
        @if (!isset($documentationTabs) || $documentationTabs['canvas_index'] === 'canvas-coordinates')
            <div wire:key="documentation-canvas-coordinates">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-coordinates')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="canvas-height">
        @if (!isset($documentationTabs) || $documentationTabs['canvas_index'] === 'canvas-height')
            <div wire:key="documentation-canvas-height">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-height')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="canvas-props">
        @if (!isset($documentationTabs) || $documentationTabs['canvas_index'] === 'canvas-props')
            <div wire:key="documentation-canvas-props">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-props')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
