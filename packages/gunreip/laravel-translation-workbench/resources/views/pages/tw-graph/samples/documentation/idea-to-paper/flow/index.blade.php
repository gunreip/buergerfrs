<flux:heading size="lg">Flow</flux:heading>
<flux:text>Flow components assemble connected process steps, decisions and conditional branches. Each example remains individually authored.</flux:text>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_index" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-start">{{ __('Flow start') }}</flux:tab>
        <flux:tab name="flow-step">{{ __('Flow step') }}</flux:tab>
        <flux:tab name="flow-branch-steps">{{ __('Flow branch steps') }}</flux:tab>
        <flux:tab name="flow-if">{{ __('Flow IF') }}</flux:tab>
        <flux:tab name="flow-switch-case">{{ __('Flow SWITCH/CASE') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="flow-start">
        @if (!isset($documentationTabs) || $documentationTabs['flow_index'] === 'flow-start')
            <div wire:key="documentation-flow-start">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.flow-start')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-step">
        @if (!isset($documentationTabs) || $documentationTabs['flow_index'] === 'flow-step')
            <div wire:key="documentation-flow-step">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.flow-step')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-branch-steps">
        @if (!isset($documentationTabs) || $documentationTabs['flow_index'] === 'flow-branch-steps')
            <div wire:key="documentation-flow-branch-steps">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.flow-branch-steps')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if">
        @if (!isset($documentationTabs) || $documentationTabs['flow_index'] === 'flow-if')
            <div wire:key="documentation-flow-if">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.index')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-switch-case">
        @if (!isset($documentationTabs) || $documentationTabs['flow_index'] === 'flow-switch-case')
            <div wire:key="documentation-flow-switch-case">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.index')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
