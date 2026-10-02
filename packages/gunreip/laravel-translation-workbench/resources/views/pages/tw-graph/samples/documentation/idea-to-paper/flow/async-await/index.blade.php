<flux:heading size="lg">{{ __('Flow ASYNC/AWAIT') }}</flux:heading>
<flux:text>{{ __('Asynchronous operations can complete later. These examples distinguish starting an operation, suspending at AWAIT and resuming with a value or an error.') }}</flux:text>
<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_async_await" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-async-await-basic">{{ __('Await one operation') }}</flux:tab>
        <flux:tab name="flow-async-await-sequential">{{ __('Sequential awaits') }}</flux:tab>
        <flux:tab name="flow-async-await-concurrent">{{ __('Concurrent operations') }}</flux:tab>
        <flux:tab name="flow-async-await-finally">{{ __('Await with TRY/CATCH/FINALLY') }}</flux:tab>
        <flux:tab name="flow-async-await-loop">{{ __('Await inside a loop') }}</flux:tab>
        <flux:tab name="flow-async-await-cancellation">{{ __('Cancellation') }}</flux:tab>
        <flux:tab name="flow-async-await-timeout">{{ __('Timeout') }}</flux:tab>
        <flux:tab name="flow-async-await-partial">{{ __('Partial success') }}</flux:tab>
        <flux:tab name="flow-async-await-first">{{ __('First completion / First success') }}</flux:tab>
        <flux:tab name="flow-async-await-limited">{{ __('Limited concurrency') }}</flux:tab>
        <flux:tab name="flow-async-await-retry">{{ __('Retry with delay') }}</flux:tab>
        <flux:tab name="flow-async-await-group-cleanup">{{ __('Failure → Cancel remaining → Cleanup') }}</flux:tab>
        <flux:tab name="flow-async-await-stream">{{ __('Async iteration / Stream') }}</flux:tab>
        <flux:tab name="flow-async-await-retry-deadline">{{ __('Retry with total deadline') }}</flux:tab>
        {{-- <flux:tab name="flow-async-await-test">{{ __('ASYNC/AWAIT Test') }}</flux:tab> --}}
    </flux:tabs>
    <flux:tab.panel name="flow-async-await-basic">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-basic')
            <div wire:key="documentation-flow-async-await-basic">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-basic')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-async-await-sequential">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-sequential')
            <div wire:key="documentation-flow-async-await-sequential">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-sequential')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-async-await-concurrent">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-concurrent')
            <div wire:key="documentation-flow-async-await-concurrent">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-concurrent')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-async-await-finally">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-finally')
            <div wire:key="documentation-flow-async-await-finally">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-finally')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-async-await-loop">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-loop')
            <div wire:key="documentation-flow-async-await-loop">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-loop')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-async-await-cancellation">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-cancellation')
            <div wire:key="documentation-flow-async-await-cancellation">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-cancellation')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-async-await-timeout">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-timeout')
            <div wire:key="documentation-flow-async-await-timeout">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-timeout')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-async-await-partial">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-partial')
            <div wire:key="documentation-flow-async-await-partial">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-partial')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-async-await-first">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-first')
            <div wire:key="documentation-flow-async-await-first">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-first')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-async-await-limited">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-limited')
            <div wire:key="documentation-flow-async-await-limited">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-limited')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-async-await-retry">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-retry')
            <div wire:key="documentation-flow-async-await-retry">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-retry')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-async-await-group-cleanup">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-group-cleanup')
            <div wire:key="documentation-flow-async-await-group-cleanup">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-group-cleanup')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-async-await-stream">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-stream')
            <div wire:key="documentation-flow-async-await-stream">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-stream')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-async-await-retry-deadline">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-retry-deadline')
            <div wire:key="documentation-flow-async-await-retry-deadline">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-retry-deadline')
            </div>
        @endif
    </flux:tab.panel>
    {{--
    <flux:tab.panel name="flow-async-await-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_async_await'] === 'flow-async-await-test')
            <div wire:key="documentation-flow-async-await-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-test')
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
                <flux:table.cell>{{ __('Await one operation') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Suspend for one pending operation and continue with its result.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Sequential awaits') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Await dependent operations in their required order.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Concurrent operations') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Start independent operations and await their combined results.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Await with TRY/CATCH/FINALLY') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Handle a rejected operation and execute cleanup.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Await inside a loop') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Compare sequential iteration with deliberately concurrent work.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Cancellation') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Observe cancellation and return through the appropriate cleanup path.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Timeout') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Request cancellation when a time limit expires and clean up both timers.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Partial success') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Collect successful results and errors from independent operations.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('First completion / First success') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Distinguish the first settled operation from the first successful one.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Limited concurrency') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Limit the number of active operations and start more as slots become available.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Retry with delay') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Retry after a delay with an attempt limit and cancellation support.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Failure → Cancel remaining → Cleanup') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Cancel sibling tasks after a failure, await cleanup, then propagate the original error.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Async iteration / Stream') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Await incoming items and close the asynchronous source on early exit.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Retry with total deadline') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Share one deadline across every attempt and retry delay.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
        </flux:table.rows>
    </flux:table>
</flux:callout>
