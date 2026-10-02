<flux:heading size="lg">{{ __('Flow BREAK / CONTINUE') }}</flux:heading>
<flux:text>{{ __('BREAK exits the innermost loop. CONTINUE skips the remaining body and starts the next iteration; for FOR, the update runs before the next condition check.') }}</flux:text>
<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_break_continue" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-break-while">{{ __('BREAK in WHILE') }}</flux:tab>
        <flux:tab name="flow-continue-while">{{ __('CONTINUE in WHILE') }}</flux:tab>
        <flux:tab name="flow-continue-for">{{ __('CONTINUE in FOR') }}</flux:tab>
        <flux:tab name="flow-continue-nested">{{ __('CONTINUE in nested WHILE') }}</flux:tab>
        <flux:tab name="flow-break-nested">{{ __('BREAK in nested WHILE') }}</flux:tab>
        <flux:tab name="flow-break-finally">{{ __('BREAK with FINALLY (1)') }}</flux:tab>
        <flux:tab name="flow-break-finally-2">{{ __('BREAK with FINALLY (2)') }}</flux:tab>
        {{-- <flux:tab name="flow-break-test">{{ __('BREAK Test') }}</flux:tab> --}}
        <flux:tab name="flow-continue-finally">{{ __('CONTINUE with FINALLY (1)') }}</flux:tab>
        <flux:tab name="flow-continue-finally-2">{{ __('CONTINUE with FINALLY (2)') }}</flux:tab>
        {{-- <flux:tab name="flow-continue-test">{{ __('CONTINUE Test') }}</flux:tab> --}}
    </flux:tabs>
    <flux:tab.panel name="flow-break-while">
        @if (!isset($documentationTabs) || $documentationTabs['flow_break_continue'] === 'flow-break-while')
            <div wire:key="documentation-flow-break-while">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-while')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-continue-while">
        @if (!isset($documentationTabs) || $documentationTabs['flow_break_continue'] === 'flow-continue-while')
            <div wire:key="documentation-flow-continue-while">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-while')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-continue-for">
        @if (!isset($documentationTabs) || $documentationTabs['flow_break_continue'] === 'flow-continue-for')
            <div wire:key="documentation-flow-continue-for">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-for')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-continue-nested">
        @if (!isset($documentationTabs) || $documentationTabs['flow_break_continue'] === 'flow-continue-nested')
            <div wire:key="documentation-flow-continue-nested">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-nested')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-continue-finally">
        @if (!isset($documentationTabs) || $documentationTabs['flow_break_continue'] === 'flow-continue-finally')
            <div wire:key="documentation-flow-continue-finally">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-finally')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-continue-finally-2">
        @if (!isset($documentationTabs) || $documentationTabs['flow_break_continue'] === 'flow-continue-finally-2')
            <div wire:key="documentation-flow-continue-finally-2">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-finally-2')
            </div>
        @endif
    </flux:tab.panel>
    {{-- Retained for future experiments.
    <flux:tab.panel name="flow-continue-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_break_continue'] === 'flow-continue-test')
            <div wire:key="documentation-flow-continue-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-test')
            </div>
        @endif
    </flux:tab.panel>
    --}}
    <flux:tab.panel name="flow-break-nested">
        @if (!isset($documentationTabs) || $documentationTabs['flow_break_continue'] === 'flow-break-nested')
            <div wire:key="documentation-flow-break-nested">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-nested')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-break-finally">
        @if (!isset($documentationTabs) || $documentationTabs['flow_break_continue'] === 'flow-break-finally')
            <div wire:key="documentation-flow-break-finally">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-finally')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="flow-break-finally-2">
        @if (!isset($documentationTabs) || $documentationTabs['flow_break_continue'] === 'flow-break-finally-2')
            <div wire:key="documentation-flow-break-finally-2">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-finally-2')
            </div>
        @endif
    </flux:tab.panel>
    {{-- Retained for future experiments.
    <flux:tab.panel name="flow-break-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_break_continue'] === 'flow-break-test')
            <div wire:key="documentation-flow-break-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-test')
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
                <flux:table.cell>{{ __('BREAK in WHILE') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Conditional early exit joins the normal loop exit.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('CONTINUE in WHILE') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Skip remaining actions and return to the condition.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('CONTINUE in FOR') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Execute the update before rechecking the condition.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Nested loops') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Show the innermost loop as the target.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('TRY / FINALLY with BREAK or CONTINUE') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Execute FINALLY before transferring control.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="green">{{ __('Complete') }}</flux:badge></flux:table.cell>
            </flux:table.row>
        </flux:table.rows>
    </flux:table>
</flux:callout>
