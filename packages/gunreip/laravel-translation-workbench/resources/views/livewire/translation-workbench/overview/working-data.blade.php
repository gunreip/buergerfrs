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
    <x-translation-workbench::ui.common.details class="my-3">
        <x-translation-workbench::ui.common.details.item
            class="tabular-nums"
            :label="__('Active findings')"
            :item="number_format($totals['active'])"
        />
        <x-translation-workbench::ui.common.details.item
            class="tabular-nums"
            :label="__('Archived findings')"
            :item="number_format($totals['archived'])"
        />
        <x-translation-workbench::ui.common.details.item
            class="tabular-nums"
            :label="__('Keys')"
            :item="number_format($totals['keys'])"
        />
        <x-translation-workbench::ui.common.details.item
            class="tabular-nums"
            :label="__('Draft revisions')"
            :item="number_format($totals['revisions'])"
        />
    </x-translation-workbench::ui.common.details>
    <flux:callout.text>
        {{ __('Edit findings, assign translation keys and save drafts. Approved revisions can be published to language files. Source occurrences can be replaced after a separate preview and confirmation. Raw Data and TW-Graph use a separate dataset; their IDs cannot be used interchangeably with Finding or Key IDs here.') }}
    </flux:callout.text>
</flux:callout>
