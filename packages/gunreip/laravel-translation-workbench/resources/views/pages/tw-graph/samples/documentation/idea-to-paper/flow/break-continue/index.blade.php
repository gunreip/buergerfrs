<flux:heading size="lg">{{ __('Flow BREAK / CONTINUE') }}</flux:heading>
<flux:text>{{ __('BREAK exits the innermost loop. CONTINUE skips the remaining body and starts the next iteration; for FOR, the update runs before the next condition check.') }}</flux:text>
<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.flow_break_continue" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="flow-break-test">{{ __('BREAK Test') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="flow-break-test">
        @if (!isset($documentationTabs) || $documentationTabs['flow_break_continue'] === 'flow-break-test')
            <div wire:key="documentation-flow-break-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-break-test')
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
                <flux:table.cell>{{ __('BREAK in WHILE') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Conditional early exit joins the normal loop exit.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="amber">{{ __('In test') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('CONTINUE in WHILE') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Skip remaining actions and return to the condition.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="zinc">{{ __('Planned') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('CONTINUE in FOR') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Execute the update before rechecking the condition.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="zinc">{{ __('Planned') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('Nested loops') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Show the innermost loop as the target.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="zinc">{{ __('Planned') }}</flux:badge></flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell>{{ __('TRY / FINALLY with BREAK or CONTINUE') }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Execute FINALLY before transferring control.') }}</flux:table.cell>
                <flux:table.cell><flux:badge color="zinc">{{ __('Planned') }}</flux:badge></flux:table.cell>
            </flux:table.row>
        </flux:table.rows>
    </flux:table>
</flux:callout>
