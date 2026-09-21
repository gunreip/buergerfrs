<flux:heading size="lg">{{ __('Flow WHILE') }}</flux:heading>
<flux:text>A WHILE checks its condition before every iteration. TRUE enters the body and returns to the condition; FALSE exits the loop. The body may therefore run zero times.</flux:text>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_while" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-while-test">{{ __('WHILE Test') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="flow-while-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_while'] === 'flow-while-test')
            <div wire:key="documentation-flow-while-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-test')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
