<div class="space-y-3">
    <flux:heading size="lg">{{ __('Paths') }}</flux:heading>
    <flux:text>
        {{ __('Paths assemble segments into connected routes and calculate subsequent anchors from the preceding segment endpoints. Segments compose the underlying primitives; strangs use paths to build complete logical graph strands.') }}
    </flux:text>
    <flux:callout color="indigo" icon="information-circle">
        <flux:callout.heading>{{ __('Paths in the component chain') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('From the drawing level upwards: primitives → segments → paths → strangs. This tab documents the path layer. For regular graph authoring, use the corresponding strang components; direct path examples demonstrate how their internal routes are assembled.') }}
        </flux:callout.text>
    </flux:callout>
</div>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.paths_index" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="idea-to-paper-paths-trunk">{{ __('Trunk') }}</flux:tab>
        <flux:tab name="idea-to-paper-paths-merge">{{ __('Merge') }}</flux:tab>
        <flux:tab name="idea-to-paper-paths-merge-extension">{{ __('Merge extension') }}</flux:tab>
        <flux:tab name="idea-to-paper-paths-branch">{{ __('Branch') }}</flux:tab>
        <flux:tab name="idea-to-paper-paths-branch-extension">{{ __('Branch extension') }}</flux:tab>
        <flux:tab name="idea-to-paper-paths-branch-return">{{ __('Branch return') }}</flux:tab>
        <flux:tab name="idea-to-paper-paths-branch-return-extension">{{ __('Branch return extension') }}</flux:tab>
        <flux:tab name="idea-to-paper-paths-branch-return-bridge">{{ __('Branch return bridge') }}</flux:tab>
        <flux:tab name="idea-to-paper-paths-stem-detour">{{ __('Stem detour') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="idea-to-paper-paths-trunk">
        @if (!isset($documentationTabs) || $documentationTabs['paths_index'] === 'idea-to-paper-paths-trunk')
            <div wire:key="documentation-idea-to-paper-paths-trunk">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-trunk')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-paths-merge">
        @if (!isset($documentationTabs) || $documentationTabs['paths_index'] === 'idea-to-paper-paths-merge')
            <div wire:key="documentation-idea-to-paper-paths-merge">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-merge')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-paths-merge-extension">
        @if (!isset($documentationTabs) || $documentationTabs['paths_index'] === 'idea-to-paper-paths-merge-extension')
            <div wire:key="documentation-idea-to-paper-paths-merge-extension">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-merge-extension')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-paths-branch">
        @if (!isset($documentationTabs) || $documentationTabs['paths_index'] === 'idea-to-paper-paths-branch')
            <div wire:key="documentation-idea-to-paper-paths-branch">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-branch')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-paths-branch-extension">
        @if (!isset($documentationTabs) || $documentationTabs['paths_index'] === 'idea-to-paper-paths-branch-extension')
            <div wire:key="documentation-idea-to-paper-paths-branch-extension">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-branch-extension')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-paths-branch-return">
        @if (!isset($documentationTabs) || $documentationTabs['paths_index'] === 'idea-to-paper-paths-branch-return')
            <div wire:key="documentation-idea-to-paper-paths-branch-return">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-branch-return')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-paths-branch-return-extension">
        @if (!isset($documentationTabs) || $documentationTabs['paths_index'] === 'idea-to-paper-paths-branch-return-extension')
            <div wire:key="documentation-idea-to-paper-paths-branch-return-extension">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-branch-return-extension')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-paths-branch-return-bridge">
        @if (!isset($documentationTabs) || $documentationTabs['paths_index'] === 'idea-to-paper-paths-branch-return-bridge')
            <div wire:key="documentation-idea-to-paper-paths-branch-return-bridge">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-branch-return-bridge')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-paths-stem-detour">
        @if (!isset($documentationTabs) || $documentationTabs['paths_index'] === 'idea-to-paper-paths-stem-detour')
            <div wire:key="documentation-idea-to-paper-paths-stem-detour">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-stem-detour')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
