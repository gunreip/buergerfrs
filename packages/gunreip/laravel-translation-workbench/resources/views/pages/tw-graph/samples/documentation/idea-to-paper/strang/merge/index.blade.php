<flux:heading size="lg">{{ __('Strang Merge') }}</flux:heading>
<flux:text>{{ __('Merge strands collect side sources and lead them back to the central chain. Each example contains its own component calls, source code, props table and preview controls.') }}</flux:text>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.strang_merge_index" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="idea-to-paper-merge-base">{{ __('Default') }}</flux:tab>
        <flux:tab name="idea-to-paper-merge-start">{{ __('Merge start') }}</flux:tab>
        <flux:tab name="idea-to-paper-merge-mismatch">{{ __('Merge mismatch') }}</flux:tab>
        <flux:tab name="idea-to-paper-merge-extension">{{ __('Extension') }}</flux:tab>
        <flux:tab name="idea-to-paper-merge-aggregated">{{ __('Aggregated') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="idea-to-paper-merge-base">
        @if (!isset($documentationTabs) || $documentationTabs['strang_merge_index'] === 'idea-to-paper-merge-base')
            <div wire:key="documentation-idea-to-paper-merge-base">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-default')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-merge-start">
        @if (!isset($documentationTabs) || $documentationTabs['strang_merge_index'] === 'idea-to-paper-merge-start')
            <div wire:key="documentation-idea-to-paper-merge-start">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-start')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-merge-mismatch">
        @if (!isset($documentationTabs) || $documentationTabs['strang_merge_index'] === 'idea-to-paper-merge-mismatch')
            <div wire:key="documentation-idea-to-paper-merge-mismatch">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-mismatch')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-merge-extension">
        @if (!isset($documentationTabs) || $documentationTabs['strang_merge_index'] === 'idea-to-paper-merge-extension')
            <div wire:key="documentation-idea-to-paper-merge-extension">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-extension')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-merge-aggregated">
        @if (!isset($documentationTabs) || $documentationTabs['strang_merge_index'] === 'idea-to-paper-merge-aggregated')
            <div wire:key="documentation-idea-to-paper-merge-aggregated">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-aggregated')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
