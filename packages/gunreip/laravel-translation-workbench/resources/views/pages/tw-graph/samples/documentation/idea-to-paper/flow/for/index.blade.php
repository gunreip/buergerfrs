<flux:heading size="lg">{{ __('Flow FOR') }}</flux:heading>
<flux:text>{{ __('FOR executes initialization once, checks its condition before each iteration and runs the increment after the body. A false initial condition skips the body entirely.') }}</flux:text>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_for" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-for-basic">{{ __('FOR basic') }}</flux:tab>
        <flux:tab name="flow-for-descending">{{ __('FOR descending') }}</flux:tab>
        <flux:tab name="flow-for-multiple-actions">{{ __('FOR multiple actions') }}</flux:tab>
        <flux:tab name="flow-for-if">{{ __('FOR with IF') }}</flux:tab>
        <flux:tab name="flow-for-switch">{{ __('FOR with SWITCH') }}</flux:tab>
        <flux:tab name="flow-for-nested">{{ __('Nested FOR') }}</flux:tab>
        <flux:tab name="flow-for-independent">{{ __('Two independent inner loops') }}</flux:tab>
        <flux:tab name="flow-for-mixed">{{ __('Mixed sides and crossings') }}</flux:tab>
        <flux:tab name="flow-for-action-sequence">{{ __('Action → Nested FOR → Action') }}</flux:tab>
        {{-- FOR Test is retained for later experiments.
        <flux:tab name="flow-for-test">{{ __('FOR Test') }}</flux:tab>
        --}}
    </flux:tabs>
    <flux:tab.panel name="flow-for-basic">
        @if (!isset($documentationTabs) || $documentationTabs['flow_for'] === 'flow-for-basic')
            <div wire:key="documentation-flow-for-basic">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-basic')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-for-descending">
        @if (!isset($documentationTabs) || $documentationTabs['flow_for'] === 'flow-for-descending')
            <div wire:key="documentation-flow-for-descending">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-descending')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-for-multiple-actions">
        @if (!isset($documentationTabs) || $documentationTabs['flow_for'] === 'flow-for-multiple-actions')
            <div wire:key="documentation-flow-for-multiple-actions">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-multiple-actions')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-for-if">
        @if (!isset($documentationTabs) || $documentationTabs['flow_for'] === 'flow-for-if')
            <div wire:key="documentation-flow-for-if">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-if')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-for-switch">
        @if (!isset($documentationTabs) || $documentationTabs['flow_for'] === 'flow-for-switch')
            <div wire:key="documentation-flow-for-switch">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-switch')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-for-nested">
        @if (!isset($documentationTabs) || $documentationTabs['flow_for'] === 'flow-for-nested')
            <div wire:key="documentation-flow-for-nested">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-nested')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-for-independent">
        @if (!isset($documentationTabs) || $documentationTabs['flow_for'] === 'flow-for-independent')
            <div wire:key="documentation-flow-for-independent">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-independent')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-for-mixed">
        @if (!isset($documentationTabs) || $documentationTabs['flow_for'] === 'flow-for-mixed')
            <div wire:key="documentation-flow-for-mixed">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-mixed')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-for-action-sequence">
        @if (!isset($documentationTabs) || $documentationTabs['flow_for'] === 'flow-for-action-sequence')
            <div wire:key="documentation-flow-for-action-sequence">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-action-sequence')
            </div>
        @endif
    </flux:tab.panel>
    {{-- FOR Test is retained for later experiments.
    <flux:tab.panel name="flow-for-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_for'] === 'flow-for-test')
            <div wire:key="documentation-flow-for-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-test')
            </div>
        @endif
    </flux:tab.panel>
    --}}
</flux:tab.group>
