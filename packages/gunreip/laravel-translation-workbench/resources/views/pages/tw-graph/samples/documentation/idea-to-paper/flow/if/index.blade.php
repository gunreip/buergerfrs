<flux:heading size="lg">{{ __('Flow IF') }}</flux:heading>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_flow_if" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-if-simple">{{ __('IF') }}</flux:tab>
        <flux:tab name="flow-if-else">{{ __('IF ELSE') }}</flux:tab>
        <flux:tab name="flow-if-elseif">{{ __('IF ELSEIF') }}</flux:tab>
        <flux:tab name="flow-if-elseif-multi">{{ __('IF ELSEIF multi') }}</flux:tab>
        <flux:tab name="flow-if-ternary">{{ __('IF ternär') }}</flux:tab>
        <flux:tab name="flow-if-nested-1">{{ __('IF nested 1') }}</flux:tab>
        <flux:tab name="flow-if-nested-2">{{ __('IF nested 2') }}</flux:tab>
        <flux:tab name="flow-if-nested-3">{{ __('IF nested 3') }}</flux:tab>
        <flux:tab name="flow-if-nested-4">{{ __('IF nested 4') }}</flux:tab>
        <flux:tab name="flow-if-nested-5">{{ __('IF nested 5') }}</flux:tab>
        <flux:tab name="flow-if-nested-6">{{ __('IF nested 6') }}</flux:tab>
        <flux:tab name="flow-if-nested-7">{{ __('IF nested 7') }}</flux:tab>
        <flux:tab name="flow-if-nested-8">{{ __('IF nested 8') }}</flux:tab>
        <flux:tab name="flow-if-nested-9">{{ __('IF nested 9') }}</flux:tab>
        {{-- <flux:tab name="flow-if-nested-test">{{ __('Flow IF nested Test') }}</flux:tab> --}}
    </flux:tabs>
    <flux:tab.panel name="flow-if-simple">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-simple')
            <div wire:key="documentation-flow-if-simple">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-simple')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-else">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-else')
            <div wire:key="documentation-flow-if-else">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-else')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-elseif">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-elseif')
            <div wire:key="documentation-flow-if-elseif">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-elseif')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-elseif-multi">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-elseif-multi')
            <div wire:key="documentation-flow-if-elseif-multi">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-elseif-multi')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-ternary">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-ternary')
            <div wire:key="documentation-flow-if-ternary">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-ternary')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-nested-1">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-nested-1')
            <div wire:key="documentation-flow-if-nested-1">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-1')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-nested-2">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-nested-2')
            <div wire:key="documentation-flow-if-nested-2">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-2')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-nested-3">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-nested-3')
            <div wire:key="documentation-flow-if-nested-3">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-3')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-nested-4">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-nested-4')
            <div wire:key="documentation-flow-if-nested-4">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-4')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-nested-5">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-nested-5')
            <div wire:key="documentation-flow-if-nested-5">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-5')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-nested-6">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-nested-6')
            <div wire:key="documentation-flow-if-nested-6">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-6')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-nested-7">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-nested-7')
            <div wire:key="documentation-flow-if-nested-7">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-7')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-nested-8">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-nested-8')
            <div wire:key="documentation-flow-if-nested-8">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-8')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-if-nested-9">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-nested-9')
            <div wire:key="documentation-flow-if-nested-9">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-9')
            </div>
        @endif
    </flux:tab.panel>
    {{-- Experimental page retained for future examples.
    <flux:tab.panel name="flow-if-nested-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_flow_if'] === 'flow-if-nested-test')
            <div wire:key="documentation-flow-if-nested-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-test')
            </div>
        @endif
    </flux:tab.panel>
    --}}
</flux:tab.group>
