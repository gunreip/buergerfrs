<flux:tab.group class="min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.reference_strang" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="reference-flow-switch-case">flow-switch-case</flux:tab>
        <flux:tab name="reference-flow-start">flow-start</flux:tab>
        <flux:tab name="reference-flow-step">flow-step</flux:tab>
        <flux:tab name="reference-flow-if">flow-if</flux:tab>
        <flux:tab name="reference-flow-if-else">flow-if-else</flux:tab>
        <flux:tab name="reference-flow-if-elseif">flow-if-elseif</flux:tab>
        <flux:tab name="reference-flow-if-elseif-multi">flow-if-elseif-multi</flux:tab>
        <flux:tab name="reference-flow-if-ternary">flow-if-ternary</flux:tab>
        <flux:tab name="reference-trunk">trunk</flux:tab>
        <flux:tab name="reference-merge-left">merge-left</flux:tab>
        <flux:tab name="reference-merge-right">merge-right</flux:tab>
        <flux:tab name="reference-branch-left">branch-left</flux:tab>
        <flux:tab name="reference-branch-right">branch-right</flux:tab>
        <flux:tab name="reference-branch-end">branch-end</flux:tab>
        <flux:tab name="reference-rekey-source-left">rekey-source-left</flux:tab>
        <flux:tab name="reference-rekey-source-right">rekey-source-right</flux:tab>
        <flux:tab name="reference-rekey-target-left">rekey-target-left</flux:tab>
        <flux:tab name="reference-rekey-target-right">rekey-target-right</flux:tab>
        <flux:tab name="reference-flow-while">flow-while</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="reference-flow-switch-case">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-flow-switch-case')
            <div wire:key="reference-flow-switch-case">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.flow-switch-case')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-flow-start">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-flow-start')
            <div wire:key="reference-flow-start">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.flow-start')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-flow-step">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-flow-step')
            <div wire:key="reference-flow-step">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.flow-step')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-flow-if">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-flow-if')
            <div wire:key="reference-flow-if">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.flow-if')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-flow-if-else">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-flow-if-else')
            <div wire:key="reference-flow-if-else">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.flow-if-else')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-flow-if-elseif">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-flow-if-elseif')
            <div wire:key="reference-flow-if-elseif">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.flow-if-elseif')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-flow-if-elseif-multi">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-flow-if-elseif-multi')
            <div wire:key="reference-flow-if-elseif-multi">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.flow-if-elseif-multi')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-flow-if-ternary">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-flow-if-ternary')
            <div wire:key="reference-flow-if-ternary">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.flow-if-ternary')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-trunk">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-trunk')
            <div wire:key="reference-trunk">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.trunk')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-merge-left">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-merge-left')
            <div wire:key="reference-merge-left">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.merge-left')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-merge-right">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-merge-right')
            <div wire:key="reference-merge-right">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.merge-right')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-branch-left">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-branch-left')
            <div wire:key="reference-branch-left">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.branch-left')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-branch-right">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-branch-right')
            <div wire:key="reference-branch-right">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.branch-right')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-branch-end">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-branch-end')
            <div wire:key="reference-branch-end">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.branch-end')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-rekey-source-left">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-rekey-source-left')
            <div wire:key="reference-rekey-source-left">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.rekey-source-left')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-rekey-source-right">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-rekey-source-right')
            <div wire:key="reference-rekey-source-right">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.rekey-source-right')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-rekey-target-left">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-rekey-target-left')
            <div wire:key="reference-rekey-target-left">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.rekey-target-left')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-rekey-target-right">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-rekey-target-right')
            <div wire:key="reference-rekey-target-right">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.rekey-target-right')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="reference-flow-while">
        @if (!isset($documentationTabs) || $documentationTabs['reference_strang'] === 'reference-flow-while')
            <div wire:key="reference-flow-while">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.strang.flow-while')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
