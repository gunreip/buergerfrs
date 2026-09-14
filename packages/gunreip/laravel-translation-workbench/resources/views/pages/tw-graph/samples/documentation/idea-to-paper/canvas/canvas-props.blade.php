<p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('This starts from the coordinated canvas and exposes the common canvas props. The trunk stays untouched, so the visible changes come from the graph wrapper only.') }}
                    </p>
<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.canvas_canvas_props" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="canvas-props-line">{{ __('Line width') }}</flux:tab>
        <flux:tab name="canvas-props-stem-length">{{ __('Stem length') }}</flux:tab>
        <flux:tab name="canvas-props-node-size">{{ __('Node size') }}</flux:tab>
        <flux:tab name="canvas-props-cap-length">{{ __('Cap length') }}</flux:tab>
        <flux:tab name="canvas-props-min-width">{{ __('Min width') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="canvas-props-line">
        @if (!isset($documentationTabs) || $documentationTabs['canvas_canvas_props'] === 'canvas-props-line')
            <div wire:key="documentation-canvas-props-line">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-props-line')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="canvas-props-stem-length">
        @if (!isset($documentationTabs) || $documentationTabs['canvas_canvas_props'] === 'canvas-props-stem-length')
            <div wire:key="documentation-canvas-props-stem-length">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-props-stem-length')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="canvas-props-node-size">
        @if (!isset($documentationTabs) || $documentationTabs['canvas_canvas_props'] === 'canvas-props-node-size')
            <div wire:key="documentation-canvas-props-node-size">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-props-node-size')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="canvas-props-cap-length">
        @if (!isset($documentationTabs) || $documentationTabs['canvas_canvas_props'] === 'canvas-props-cap-length')
            <div wire:key="documentation-canvas-props-cap-length">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-props-cap-length')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="canvas-props-min-width">
        @if (!isset($documentationTabs) || $documentationTabs['canvas_canvas_props'] === 'canvas-props-min-width')
            <div wire:key="documentation-canvas-props-min-width">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-props-min-width')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
