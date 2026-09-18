<flux:heading size="lg">Flow SWITCH/CASE</flux:heading>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_switch_case" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-switch-case-default">{{ __('CASEs single') }}</flux:tab>
        <flux:tab name="flow-switch-case-grouped">{{ __('CASEs grouped') }}</flux:tab>
        <flux:tab name="flow-switch-case-grouped-3">{{ __('CASE grouped (3)') }}</flux:tab>
        <flux:tab name="flow-switch-case-grouped-multi">{{ __('CASE grouped (>4)') }}</flux:tab>
        <flux:tab name="flow-switch-case-nested">{{ __('CASE nested') }}</flux:tab>
        <flux:tab name="flow-switch-case-without-default">{{ __('CASEs without DEFAULT') }}</flux:tab>
        <flux:tab name="flow-switch-case-fallthrough">{{ __('CASE fallthrough') }}</flux:tab>
        <flux:tab name="flow-switch-case-test">{{ __('SWITCH/CASE Test') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="flow-switch-case-default">
        @if (!isset($documentationTabs) || $documentationTabs['flow_switch_case'] === 'flow-switch-case-default')
            <div wire:key="documentation-flow-switch-case-default">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-default')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-switch-case-grouped">
        @if (!isset($documentationTabs) || $documentationTabs['flow_switch_case'] === 'flow-switch-case-grouped')
            <div wire:key="documentation-flow-switch-case-grouped">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-grouped')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-switch-case-grouped-3">
        @if (!isset($documentationTabs) || $documentationTabs['flow_switch_case'] === 'flow-switch-case-grouped-3')
            <div wire:key="documentation-flow-switch-case-grouped-3">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-grouped-3')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-switch-case-grouped-multi">
        @if (!isset($documentationTabs) || $documentationTabs['flow_switch_case'] === 'flow-switch-case-grouped-multi')
            <div wire:key="documentation-flow-switch-case-grouped-multi">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-grouped-multi')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-switch-case-nested">
        @if (!isset($documentationTabs) || $documentationTabs['flow_switch_case'] === 'flow-switch-case-nested')
            <div wire:key="documentation-flow-switch-case-nested">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-nested')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-switch-case-without-default">
        @if (!isset($documentationTabs) || $documentationTabs['flow_switch_case'] === 'flow-switch-case-without-default')
            <div wire:key="documentation-flow-switch-case-without-default">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-without-default')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-switch-case-fallthrough">
        @if (!isset($documentationTabs) || $documentationTabs['flow_switch_case'] === 'flow-switch-case-fallthrough')
            <div wire:key="documentation-flow-switch-case-fallthrough">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-fallthrough')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-switch-case-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_switch_case'] === 'flow-switch-case-test')
            <div wire:key="documentation-flow-switch-case-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-test')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
