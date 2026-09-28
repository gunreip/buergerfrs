<flux:heading size="lg">{{ __('Flow TRY/CATCH/FINALLY') }}</flux:heading>
<flux:text>{{ __('Separate normal completion, exception handling and shared cleanup. Each example assumes that the handlers complete normally; propagation and early exits are separate topics.') }}</flux:text>
<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_try_catch" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-try-catch-basic">{{ __('TRY / CATCH') }}</flux:tab>
        <flux:tab name="flow-try-catch-finally">{{ __('TRY / CATCH / FINALLY') }}</flux:tab>
        <flux:tab name="flow-try-catch-multiple">{{ __('Multiple CATCH clauses') }}</flux:tab>
        <flux:tab name="flow-try-catch-if-try">{{ __('IF → TRY/CATCH') }}</flux:tab>
        <flux:tab name="flow-try-catch-try-if-finally">{{ __('TRY → IF/ELSE → FINALLY') }}</flux:tab>
        <flux:tab name="flow-try-catch-foreach-try">{{ __('FOREACH → TRY/CATCH') }}</flux:tab>
        <flux:tab name="flow-try-catch-try-foreach">{{ __('TRY → FOREACH → CATCH') }}</flux:tab>
        <flux:tab name="flow-try-catch-while-try-finally">{{ __('WHILE → TRY/CATCH/FINALLY') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="flow-try-catch-basic">
        @if (!isset($documentationTabs) || $documentationTabs['flow_try_catch'] === 'flow-try-catch-basic')
            <div wire:key="documentation-flow-try-catch-basic">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-basic')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-try-catch-finally">
        @if (!isset($documentationTabs) || $documentationTabs['flow_try_catch'] === 'flow-try-catch-finally')
            <div wire:key="documentation-flow-try-catch-finally">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-finally')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-try-catch-multiple">
        @if (!isset($documentationTabs) || $documentationTabs['flow_try_catch'] === 'flow-try-catch-multiple')
            <div wire:key="documentation-flow-try-catch-multiple">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-multiple')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-try-catch-if-try">
        @if (!isset($documentationTabs) || $documentationTabs['flow_try_catch'] === 'flow-try-catch-if-try')
            <div wire:key="documentation-flow-try-catch-if-try">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-if-try')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-try-catch-try-if-finally">
        @if (!isset($documentationTabs) || $documentationTabs['flow_try_catch'] === 'flow-try-catch-try-if-finally')
            <div wire:key="documentation-flow-try-catch-try-if-finally">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-try-if-finally')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-try-catch-foreach-try">
        @if (!isset($documentationTabs) || $documentationTabs['flow_try_catch'] === 'flow-try-catch-foreach-try')
            <div wire:key="documentation-flow-try-catch-foreach-try">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-foreach-try')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-try-catch-try-foreach">
        @if (!isset($documentationTabs) || $documentationTabs['flow_try_catch'] === 'flow-try-catch-try-foreach')
            <div wire:key="documentation-flow-try-catch-try-foreach">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-try-foreach')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-try-catch-while-try-finally">
        @if (!isset($documentationTabs) || $documentationTabs['flow_try_catch'] === 'flow-try-catch-while-try-finally')
            <div wire:key="documentation-flow-try-catch-while-try-finally">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-while-try-finally')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
