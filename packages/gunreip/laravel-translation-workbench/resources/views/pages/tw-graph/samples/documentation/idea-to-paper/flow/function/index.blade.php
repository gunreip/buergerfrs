<flux:heading size="lg">{{ __('Flow FUNCTION') }}</flux:heading>
<flux:text>{{ __('Functions separate a reusable operation from its callers. These examples show arguments, local execution, return values and the continuation at the original call site.') }}</flux:text>
<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_function" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-function-basic">{{ __('Function call and return value') }}</flux:tab>
        <flux:tab name="flow-function-arguments">{{ __('Arguments and local variables') }}</flux:tab>
        <flux:tab name="flow-function-void">{{ __('Function without return value') }}</flux:tab>
        <flux:tab name="flow-function-nested">{{ __('Nested function calls') }}</flux:tab>
        <flux:tab name="flow-function-multiple">{{ __('Multiple call sites') }}</flux:tab>
        <flux:tab name="flow-function-recursion">{{ __('Recursion with a base case') }}</flux:tab>
        {{-- <flux:tab name="flow-function-test">{{ __('FUNCTION Test') }}</flux:tab> --}}
    </flux:tabs>
    <flux:tab.panel name="flow-function-basic">
        @if (!isset($documentationTabs) || $documentationTabs['flow_function'] === 'flow-function-basic')
            <div wire:key="documentation-flow-function-basic">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-basic')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-function-arguments">
        @if (!isset($documentationTabs) || $documentationTabs['flow_function'] === 'flow-function-arguments')
            <div wire:key="documentation-flow-function-arguments">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-arguments')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-function-void">
        @if (!isset($documentationTabs) || $documentationTabs['flow_function'] === 'flow-function-void')
            <div wire:key="documentation-flow-function-void">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-void')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-function-nested">
        @if (!isset($documentationTabs) || $documentationTabs['flow_function'] === 'flow-function-nested')
            <div wire:key="documentation-flow-function-nested">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-nested')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-function-multiple">
        @if (!isset($documentationTabs) || $documentationTabs['flow_function'] === 'flow-function-multiple')
            <div wire:key="documentation-flow-function-multiple">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-multiple')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-function-recursion">
        @if (!isset($documentationTabs) || $documentationTabs['flow_function'] === 'flow-function-recursion')
            <div wire:key="documentation-flow-function-recursion">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-recursion')
            </div>
        @endif
    </flux:tab.panel>
    {{--
    <flux:tab.panel name="flow-function-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_function'] === 'flow-function-test')
            <div wire:key="documentation-flow-function-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-test')
            </div>
        @endif
    </flux:tab.panel>
    --}}
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
                <flux:table.cell>{{ __('Function call and return value') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Call a function, calculate a result and resume the caller.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Arguments and local variables') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Separate caller values, parameters and local intermediate results.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Function without return value') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Perform an action and return control without a result value.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Nested function calls') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Return from the inner function to the outer function, then to the original caller.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Multiple call sites') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Call the same function from different locations and return to the correct continuation.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Recursion with a base case') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Show separate call frames, the stopping condition and returns in reverse call order.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
        </flux:table.rows>
    </flux:table>
</flux:callout>
