@php
    $transferBalance = app(\Gunreip\TranslationWorkbench\Support\BaselineTransferBalance::class)->inspect($baselineRehearsal);
    $balanceStatuses = [
        'preserved' => ['sky', __('Evidence preserved')],
        'open' => ['amber', __('Open')],
        'excluded' => ['zinc', __('Excluded from scope')],
        'evidence_only' => ['zinc', __('Evidence only')],
    ];
@endphp
<flux:callout color="sky" icon="table-cells">
    <flux:callout.heading>{{ __('Recorded transfer balance') }}</flux:callout.heading>
    <flux:callout.text>
        {{ __('This balance covers the complete isolated test generation from the saved source snapshot. It is not a live inventory or an approval to migrate all historical database tables. The separate synthetic correction case is not added to these counts.') }}
    </flux:callout.text>
    @if ($transferBalance['state'] === 'verified')
        <flux:callout.text>
            {{ __('Sections describe different units or subsets. Do not add their totals together. Preserved evidence does not mean a completed translation or an approved publication.') }}
        </flux:callout.text>
        <flux:accordion>
            @foreach ($transferBalance['sections'] as $section)
                <flux:accordion.item>
                    <flux:accordion.heading>
                        {{ $section['title'] }} · {{ $section['total'] }} {{ $section['unit'] }}
                    </flux:accordion.heading>
                    <flux:accordion.content>
                        <flux:table container:class="overflow-x-auto">
                            <flux:table.columns>
                                <flux:table.column>{{ __('Category') }}</flux:table.column>
                                <flux:table.column>{{ __('Count') }}</flux:table.column>
                                <flux:table.column>{{ __('Status') }}</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach ($section['rows'] as $row)
                                    <flux:table.row>
                                        <flux:table.cell class="whitespace-normal">{{ $row['label'] }}</flux:table.cell>
                                        <flux:table.cell class="tabular-nums">{{ $row['count'] }}</flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge :color="$balanceStatuses[$row['status']][0]">{{ $balanceStatuses[$row['status']][1] }}</flux:badge>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </flux:accordion.content>
                </flux:accordion.item>
            @endforeach
        </flux:accordion>
        <flux:callout.text>
            {{ __('Additional evidence: :keys keys, :locales locales, :arguments argument records and :producers producer descriptions. These are not additional findings.', [
                'keys' => $transferBalance['keys'], 'locales' => $transferBalance['locales'],
                'arguments' => $transferBalance['argument_records'], 'producers' => $transferBalance['producers'],
            ]) }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Old history and approvals remain outside the active generation. This report does not provide a complete archive count. Unpublished historical values, remaining data categories and the production switch still need their own accounting and decisions.') }}
        </flux:callout.text>
    @else
        <flux:callout color="amber">
            <flux:callout.text>
                {{ $transferBalance['state'] === 'inconsistent'
                    ? __('The recorded counts are incomplete or inconsistent. No transfer totals are presented.')
                    : __('A complete verified rehearsal report is required for this balance. Missing results are not treated as zero.') }}
            </flux:callout.text>
        </flux:callout>
    @endif
</flux:callout>
