<flux:heading size="lg">{{ __('Flow RETURN') }}</flux:heading>
<flux:text>{{ __('RETURN exits the current function. Early returns skip its remaining actions; a surrounding FINALLY still executes before control returns to the caller.') }}</flux:text>
<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_return" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-return-guard">{{ __('Guard clause') }}</flux:tab>
        <flux:tab name="flow-return-multiple-guards">{{ __('Multiple guards') }}</flux:tab>
        <flux:tab name="flow-return-loop">{{ __('RETURN from a loop') }}</flux:tab>
        <flux:tab name="flow-return-nested">{{ __('RETURN from nested loops') }}</flux:tab>
        <flux:tab name="flow-return-finally">{{ __('RETURN with FINALLY') }}</flux:tab>
        <flux:tab name="flow-return-void">{{ __('RETURN without a value') }}</flux:tab>
        <flux:tab name="flow-return-test">{{ __('RETURN Test') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="flow-return-guard">
        @if (!isset($documentationTabs) || $documentationTabs['flow_return'] === 'flow-return-guard')
            <div wire:key="documentation-flow-return-guard">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-guard')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-return-multiple-guards">
        @if (!isset($documentationTabs) || $documentationTabs['flow_return'] === 'flow-return-multiple-guards')
            <div wire:key="documentation-flow-return-multiple-guards">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-multiple-guards')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-return-loop">
        @if (!isset($documentationTabs) || $documentationTabs['flow_return'] === 'flow-return-loop')
            <div wire:key="documentation-flow-return-loop">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-loop')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-return-nested">
        @if (!isset($documentationTabs) || $documentationTabs['flow_return'] === 'flow-return-nested')
            <div wire:key="documentation-flow-return-nested">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-nested')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-return-finally">
        @if (!isset($documentationTabs) || $documentationTabs['flow_return'] === 'flow-return-finally')
            <div wire:key="documentation-flow-return-finally">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-finally')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-return-void">
        @if (!isset($documentationTabs) || $documentationTabs['flow_return'] === 'flow-return-void')
            <div wire:key="documentation-flow-return-void">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-void')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-return-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_return'] === 'flow-return-test')
            <div wire:key="documentation-flow-return-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-test')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
<flux:callout class="mt-4" color="indigo">
    <flux:callout.heading icon="list-ordered">{{ __('Suggested sequence') }}</flux:callout.heading>
    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Example') }}</flux:table.column>
            <flux:table.column>{{ __('Purpose') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            <flux:table.row>
                <flux:table.cell>{{ __('Guard clause') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Early fallback return and normal final return.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Multiple guards') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Independent early exits before the main action.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('RETURN from a loop') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Exit the function from inside WHILE or FOREACH.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('RETURN from nested control flow') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Exit all enclosing control structures within the current function.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('RETURN with FINALLY') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Evaluate the return value, execute cleanup, then return to the caller.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('RETURN without a value') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('End a procedure or void function; explain language differences.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
        </flux:table.rows>
    </flux:table>
</flux:callout>
