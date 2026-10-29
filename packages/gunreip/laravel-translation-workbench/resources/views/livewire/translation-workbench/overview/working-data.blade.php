<flux:callout
    class="space-yx-4"
    color="sky"
    icon="database"
>
    <flux:callout.heading>
        {{ __('Working data') }}:
        <flux:callout.text>
            <code>{{ $totals['database'] }} / current_generation_v2</code>
        </flux:callout.text>
    </flux:callout.heading>
    <flux:callout.heading>
        {{ __('Active findings') }}:
        <flux:callout.text>
            {{ number_format($totals['active']) }} ·
        </flux:callout.text>
        {{ __('Archived findings') }}:
        <flux:callout.text>
            {{ number_format($totals['archived']) }} ·
        </flux:callout.text>
        {{ __('Keys') }}:
        <flux:callout.text>
            {{ number_format($totals['keys']) }} ·
        </flux:callout.text>
        {{ __('Draft revisions') }}:
        <flux:callout.text>
            {{ number_format($totals['revisions']) }}
        </flux:callout.text>
    </flux:callout.heading>
    <flux:callout.text>
        {{ __('Edit findings, assign translation keys and save drafts. Approved revisions can be published to language files. Source occurrences can be replaced after a separate preview and confirmation. Raw Data and TW-Graph use a separate dataset; their IDs cannot be used interchangeably with Finding or Key IDs here.') }}
    </flux:callout.text>
</flux:callout>
