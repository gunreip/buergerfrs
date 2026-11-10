<flux:callout
    class="space-y-4"
    color="sky"
    icon="pencil-square"
    heading="{{ __('In progress') }} · {{ number_format($workTasks->total()) }}"
    text="{{ __('translation-workbench.tab.in-progress.content.started_work_ordered_by_the_last_manual_edit_status_reflects_saved_revisions_and') }}"
>

    @include('translation-workbench::livewire.translation-workbench.tabs.in-progress.filters')

    <flux:error name="resumeWork" />

    @include('translation-workbench::livewire.translation-workbench.shared.work-table', [
        'workRows' => $workTasks->items(),
    ])

    <flux:pagination :paginator="$workTasks" />

</flux:callout>
