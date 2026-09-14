<flux:heading size="lg">Flow IF</flux:heading>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_flow_if" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-if-if">{{ __('IF') }}</flux:tab>
        <flux:tab name="flow-if-endif">{{ __('IF ENDIF') }}</flux:tab>
        <flux:tab name="flow-if-else-endif">{{ __('IF ELSE ENDIF') }}</flux:tab>
        <flux:tab name="flow-if-elseif-endif">{{ __('IF ELSEIF ENDIF') }}</flux:tab>
        <flux:tab name="flow-if-test">{{ __('Flow IF Test') }}</flux:tab>
        <flux:tab name="flow-if-nested-test">{{ __('Flow IF nested Test') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="flow-if-if">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-if')
            <div wire:key="documentation-flow-if-if">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.flow-if-if')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-endif">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-endif')
            <div wire:key="documentation-flow-if-endif">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.flow-if-endif')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-else-endif">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-else-endif')
            <div wire:key="documentation-flow-if-else-endif">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.flow-if-else-endif')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-elseif-endif">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-elseif-endif')
            <div wire:key="documentation-flow-if-elseif-endif">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.flow-if-elseif-endif')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-test')
            <div wire:key="documentation-flow-if-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.flow-if-test')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-nested-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-nested-test')
            <div wire:key="documentation-flow-if-nested-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.flow-if-nested-test')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
