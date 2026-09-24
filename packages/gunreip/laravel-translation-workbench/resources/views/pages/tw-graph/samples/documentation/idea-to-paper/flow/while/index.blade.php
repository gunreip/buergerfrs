<flux:heading size="lg">{{ __('Flow WHILE') }}</flux:heading>
<flux:text>A WHILE checks its condition before every iteration. TRUE enters the body and returns to the condition; FALSE
    exits the loop. The body may therefore run zero times.</flux:text>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs
        wire:model.live="tabs.flow_while"
        scrollable
        scrollable:fade
        scrollable:scrollbar="hide"
    >
        <flux:tab name="flow-while-basic">{{ __('WHILE basic') }}</flux:tab>
        <flux:tab name="flow-while-multiple-actions">{{ __('WHILE multiple actions') }}</flux:tab>
        <flux:tab name="flow-while-if">{{ __('WHILE with IF') }}</flux:tab>
        <flux:tab name="flow-while-nested">{{ __('Nested WHILE') }}</flux:tab>
        <flux:tab name="flow-while-independent">{{ __('Two independent inner loops') }}</flux:tab>
        <flux:tab name="flow-while-mixed">{{ __('Mixed sides and crossings') }}</flux:tab>
        <flux:tab name="flow-while-action-sequence">{{ __('Action → Nested WHILE → Action') }}</flux:tab>
        <flux:tab name="flow-while-test">{{ __('WHILE Test') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="flow-while-basic">
        @if (!isset($documentationTabs) || $documentationTabs['flow_while'] === 'flow-while-basic')
            <div wire:key="documentation-flow-while-basic">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-basic')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-while-multiple-actions">
        @if (!isset($documentationTabs) || $documentationTabs['flow_while'] === 'flow-while-multiple-actions')
            <div wire:key="documentation-flow-while-multiple-actions">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-multiple-actions')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-while-if">
        @if (!isset($documentationTabs) || $documentationTabs['flow_while'] === 'flow-while-if')
            <div wire:key="documentation-flow-while-if">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-if')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-while-nested">
        @if (!isset($documentationTabs) || $documentationTabs['flow_while'] === 'flow-while-nested')
            <div wire:key="documentation-flow-while-nested">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-nested')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-while-independent">
        @if (!isset($documentationTabs) || $documentationTabs['flow_while'] === 'flow-while-independent')
            <div wire:key="documentation-flow-while-independent">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-independent')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-while-mixed">
        @if (!isset($documentationTabs) || $documentationTabs['flow_while'] === 'flow-while-mixed')
            <div wire:key="documentation-flow-while-mixed">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-mixed')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-while-action-sequence">
        @if (!isset($documentationTabs) || $documentationTabs['flow_while'] === 'flow-while-action-sequence')
            <div wire:key="documentation-flow-while-action-sequence">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-action-sequence')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-while-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_while'] === 'flow-while-test')
            <div wire:key="documentation-flow-while-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-test')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
