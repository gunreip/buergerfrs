<flux:heading size="lg">{{ __('Flow DO WHILE') }}</flux:heading>
<flux:text>{{ __('DO WHILE runs its body at least once and checks the condition afterwards. TRUE repeats the entire body; FALSE continues after the loop.') }}</flux:text>
<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_do_while" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-do-while-basic">{{ __('DO WHILE basic') }}</flux:tab>
        <flux:tab name="flow-do-while-multiple-actions">{{ __('Multiple actions') }}</flux:tab>
        <flux:tab name="flow-do-while-bounded-retry">{{ __('Bounded retry') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="flow-do-while-basic">
        @if (!isset($documentationTabs) || $documentationTabs['flow_do_while'] === 'flow-do-while-basic')
            <div wire:key="documentation-flow-do-while-basic">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.do-while.flow-do-while-basic')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-do-while-multiple-actions">
        @if (!isset($documentationTabs) || $documentationTabs['flow_do_while'] === 'flow-do-while-multiple-actions')
            <div wire:key="documentation-flow-do-while-multiple-actions">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.do-while.flow-do-while-multiple-actions')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-do-while-bounded-retry">
        @if (!isset($documentationTabs) || $documentationTabs['flow_do_while'] === 'flow-do-while-bounded-retry')
            <div wire:key="documentation-flow-do-while-bounded-retry">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.do-while.flow-do-while-bounded-retry')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
