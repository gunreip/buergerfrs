<flux:heading size="lg">{{ __('Flow CALLBACK') }}</flux:heading>
<flux:text>{{ __('A callback is passed to another function, which decides when to invoke it. These examples begin with synchronous calls and show the callback result returning through its direct caller.') }}</flux:text>
<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_callback" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-callback-basic">{{ __('Pass and invoke a callback') }}</flux:tab>
        <flux:tab name="flow-callback-interchangeable">{{ __('Interchangeable callbacks') }}</flux:tab>
        <flux:tab name="flow-callback-loop">{{ __('Callback inside a loop') }}</flux:tab>
        {{-- <flux:tab name="flow-callback-test">{{ __('CALLBACK Test') }}</flux:tab> --}}
    </flux:tabs>
    <flux:tab.panel name="flow-callback-basic">
        @if (!isset($documentationTabs) || $documentationTabs['flow_callback'] === 'flow-callback-basic')
            <div wire:key="documentation-flow-callback-basic">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-basic')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-callback-interchangeable">
        @if (!isset($documentationTabs) || $documentationTabs['flow_callback'] === 'flow-callback-interchangeable')
            <div wire:key="documentation-flow-callback-interchangeable">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-interchangeable')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-callback-loop">
        @if (!isset($documentationTabs) || $documentationTabs['flow_callback'] === 'flow-callback-loop')
            <div wire:key="documentation-flow-callback-loop">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-loop')
            </div>
        @endif
    </flux:tab.panel>
    {{--
    <flux:tab.panel name="flow-callback-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_callback'] === 'flow-callback-test')
            <div wire:key="documentation-flow-callback-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-test')
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
                <flux:table.cell>{{ __('Pass and invoke a callback') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Pass a named function, invoke it with an argument and return its result to the caller.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Interchangeable callbacks') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Pass different callbacks to the same receiving function and compare the results.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Callback inside a loop') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Invoke one callback for each item and continue the loop after each return.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
        </flux:table.rows>
    </flux:table>
</flux:callout>
