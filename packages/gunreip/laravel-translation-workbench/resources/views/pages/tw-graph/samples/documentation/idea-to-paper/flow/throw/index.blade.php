<flux:heading size="lg">{{ __('Flow THROW / RETHROW') }}</flux:heading>
<flux:text>{{ __('THROW interrupts the current execution path and searches for a matching handler. RETHROW propagates an exception from a handler; FINALLY participates in cleanup along the way.') }}</flux:text>
<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_throw" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-throw-catch">{{ __('THROW to matching CATCH') }}</flux:tab>
        <flux:tab name="flow-throw-propagation">{{ __('Propagation to outer CATCH') }}</flux:tab>
        <flux:tab name="flow-throw-rethrow">{{ __('RETHROW after logging') }}</flux:tab>
        <flux:tab name="flow-throw-finally">{{ __('FINALLY during propagation') }}</flux:tab>
        <flux:tab name="flow-throw-finally-return">{{ __('THROW in FINALLY replaces RETURN') }}</flux:tab>
        <flux:tab name="flow-throw-finally-exception">{{ __('THROW in FINALLY replaces an exception') }}</flux:tab>
        <flux:tab name="flow-throw-return-finally">{{ __('RETURN inside FINALLY') }}</flux:tab>
        <flux:tab name="flow-throw-unhandled">{{ __('Unhandled exception') }}</flux:tab>
        <flux:tab name="flow-throw-foreach">{{ __('THROW inside FOREACH') }}</flux:tab>
        <flux:tab name="flow-throw-foreach-catch">{{ __('CATCH inside FOREACH') }}</flux:tab>
        {{-- <flux:tab name="flow-throw-test">{{ __('THROW Test') }}</flux:tab> --}}
    </flux:tabs>
    <flux:tab.panel name="flow-throw-catch">
        @if (!isset($documentationTabs) || $documentationTabs['flow_throw'] === 'flow-throw-catch')
            <div wire:key="documentation-flow-throw-catch">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-catch')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-throw-propagation">
        @if (!isset($documentationTabs) || $documentationTabs['flow_throw'] === 'flow-throw-propagation')
            <div wire:key="documentation-flow-throw-propagation">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-propagation')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-throw-rethrow">
        @if (!isset($documentationTabs) || $documentationTabs['flow_throw'] === 'flow-throw-rethrow')
            <div wire:key="documentation-flow-throw-rethrow">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-rethrow')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-throw-finally">
        @if (!isset($documentationTabs) || $documentationTabs['flow_throw'] === 'flow-throw-finally')
            <div wire:key="documentation-flow-throw-finally">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-throw-finally-return">
        @if (!isset($documentationTabs) || $documentationTabs['flow_throw'] === 'flow-throw-finally-return')
            <div wire:key="documentation-flow-throw-finally-return">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally-return')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-throw-finally-exception">
        @if (!isset($documentationTabs) || $documentationTabs['flow_throw'] === 'flow-throw-finally-exception')
            <div wire:key="documentation-flow-throw-finally-exception">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally-exception')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-throw-return-finally">
        @if (!isset($documentationTabs) || $documentationTabs['flow_throw'] === 'flow-throw-return-finally')
            <div wire:key="documentation-flow-throw-return-finally">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-return-finally')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-throw-unhandled">
        @if (!isset($documentationTabs) || $documentationTabs['flow_throw'] === 'flow-throw-unhandled')
            <div wire:key="documentation-flow-throw-unhandled">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-unhandled')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-throw-foreach">
        @if (!isset($documentationTabs) || $documentationTabs['flow_throw'] === 'flow-throw-foreach')
            <div wire:key="documentation-flow-throw-foreach">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-foreach')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-throw-foreach-catch">
        @if (!isset($documentationTabs) || $documentationTabs['flow_throw'] === 'flow-throw-foreach-catch')
            <div wire:key="documentation-flow-throw-foreach-catch">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-foreach-catch')
            </div>
        @endif
    </flux:tab.panel>
    {{-- THROW Test is retained for later experiments.
    <flux:tab.panel name="flow-throw-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_throw'] === 'flow-throw-test')
            <div wire:key="documentation-flow-throw-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-test')
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
                <flux:table.cell>{{ __('THROW to matching CATCH') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Skip the remaining TRY body, handle the exception, then continue.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Propagation to outer CATCH') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('An inner scope has no matching handler; an outer CATCH handles the exception.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('RETHROW after logging') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('The handler logs and rethrows the exception to its caller.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('FINALLY during propagation') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Cleanup runs before the exception reaches the outer handler.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('THROW in FINALLY replaces RETURN') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('A cleanup exception cancels the pending return before it reaches the caller.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('THROW in FINALLY replaces an exception') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Compare the original error with the cleanup error and preserve its cause where supported.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('RETURN inside FINALLY') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Language-specific replacement of a pending return or exception; not supported in every language.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Unhandled exception') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('FINALLY runs, then the exception leaves the shown call without a matching CATCH or normal continuation.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('THROW inside FOREACH') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('An invalid item leaves the whole iteration and reaches an outer CATCH; remaining items are skipped.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('CATCH inside FOREACH') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Handle an invalid item inside its iteration and continue processing the remaining items.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
        </flux:table.rows>
    </flux:table>
</flux:callout>
