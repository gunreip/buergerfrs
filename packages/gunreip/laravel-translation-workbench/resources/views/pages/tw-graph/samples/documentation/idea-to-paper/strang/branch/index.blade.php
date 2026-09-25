<flux:heading size="lg">{{ __('Strang Branch') }}</flux:heading>
<flux:text>{{ __('Each example contains its own handmade components, source code, props table and preview controls.') }}</flux:text>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.strang_branch_index" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="branch-default">{{ __('Default') }}</flux:tab>
        <flux:tab name="branch-offset">{{ __('Offset') }}</flux:tab>
        <flux:tab name="branch-step">{{ __('Step') }}</flux:tab>
        <flux:tab name="branch-continuation">{{ __('Continuation') }}</flux:tab>
        <flux:tab name="branch-return">{{ __('Return') }}</flux:tab>
        <flux:tab name="branch-mismatch">{{ __('Mismatch') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="branch-default">
        @if (!isset($documentationTabs) || $documentationTabs['strang_branch_index'] === 'branch-default')
            <div wire:key="documentation-branch-default">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.branch-default')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="branch-offset">
        @if (!isset($documentationTabs) || $documentationTabs['strang_branch_index'] === 'branch-offset')
            <div wire:key="documentation-branch-offset">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.branch-offset')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="branch-step">
        @if (!isset($documentationTabs) || $documentationTabs['strang_branch_index'] === 'branch-step')
            <div wire:key="documentation-branch-step">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.branch-step')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="branch-continuation">
        @if (!isset($documentationTabs) || $documentationTabs['strang_branch_index'] === 'branch-continuation')
            <div wire:key="documentation-branch-continuation">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.branch-continuation')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="branch-return">
        @if (!isset($documentationTabs) || $documentationTabs['strang_branch_index'] === 'branch-return')
            <div wire:key="documentation-branch-return">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.branch-return')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="branch-mismatch">
        @if (!isset($documentationTabs) || $documentationTabs['strang_branch_index'] === 'branch-mismatch')
            <div wire:key="documentation-branch-mismatch">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.branch-mismatch')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
