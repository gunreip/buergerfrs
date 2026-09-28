<flux:heading size="lg">{{ __('Flow FOREACH') }}</flux:heading>
<flux:text>{{ __('FOREACH visits the elements of an iterable without an application-managed counter. Empty iterables skip the body. The examples distinguish values, key/value pairs and independent nested iterations.') }}</flux:text>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_foreach" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-foreach-collection">{{ __('Collection') }}</flux:tab>
        <flux:tab name="flow-foreach-key-value">{{ __('Key / Value') }}</flux:tab>
        <flux:tab name="flow-foreach-nested">{{ __('Nested FOREACH') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="flow-foreach-collection">
        @if (!isset($documentationTabs) || $documentationTabs['flow_foreach'] === 'flow-foreach-collection')
            <div wire:key="documentation-flow-foreach-collection">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.foreach.flow-foreach-collection')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-foreach-key-value">
        @if (!isset($documentationTabs) || $documentationTabs['flow_foreach'] === 'flow-foreach-key-value')
            <div wire:key="documentation-flow-foreach-key-value">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.foreach.flow-foreach-key-value')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-foreach-nested">
        @if (!isset($documentationTabs) || $documentationTabs['flow_foreach'] === 'flow-foreach-nested')
            <div wire:key="documentation-flow-foreach-nested">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.foreach.flow-foreach-nested')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
