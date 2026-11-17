<flux:callout color="sky" icon="clock" :heading="__('translation-workbench.overview.recent-work.recently-edited')" class="space-y-3">
    <flux:callout.text>{{ __('translation-workbench.literal.your_five_most_recently_edited_keys_including_completed_work_scanner_events_do_not_ch') }}</flux:callout.text>
    <flux:error name="resumeWork" />
    @include('translation-workbench::livewire.translation-workbench.shared.work-table', ['workRows' => $recentWork])
</flux:callout>
