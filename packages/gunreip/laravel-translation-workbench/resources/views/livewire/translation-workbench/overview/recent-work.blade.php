<flux:callout color="sky" icon="clock" :heading="__('Recently edited')" class="space-y-3">
    <flux:callout.text>{{ __('Your five most recently edited keys, including completed work. Scanner events do not change this order.') }}</flux:callout.text>
    <flux:error name="resumeWork" />
    @include('translation-workbench::livewire.translation-workbench.shared.work-table', ['workRows' => $recentWork])
</flux:callout>
