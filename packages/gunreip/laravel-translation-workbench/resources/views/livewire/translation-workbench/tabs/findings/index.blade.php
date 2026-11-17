<flux:callout
    class="space-y-4"
    color="green"
    icon="radar"
    heading="{{ __('Findings') }} · {{ number_format($rows->total()) }}"
    text="{{ __('translation-workbench.literal.open_shows_active_findings_that_still_need_work_for_the_active_target_language_enable') }}"
>
    @include('translation-workbench::livewire.translation-workbench.tabs.findings.filters')

    @if (in_array($status, ['active_compact', 'all_compact'], true))
        <flux:callout.text class="my-3">
            {{ __('Active findings with identical literal text are grouped for this overview. IDs, sources and key assignments in each row belong to the representative finding. Archived findings remain separate. Switch to Active or All for individual findings within the selected filters.') }}
        </flux:callout.text>
    @endif

    @include('translation-workbench::livewire.translation-workbench.tabs.findings.table')

</flux:callout>
