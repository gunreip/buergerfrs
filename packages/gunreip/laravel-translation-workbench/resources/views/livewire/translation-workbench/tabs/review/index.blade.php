<flux:callout
    class="space-y-4"
    color="amber"
    icon="scan-eye"
    heading="{{ __('Review') }} · {{ number_format($reviewTasks->total()) }} {{ __('Language revisions') }}"
    text="{{ __('Explicitly submitted saved revisions. Approval does not publish files. Return a revision to Translations when corrections are needed.') }}"
>
    {{-- <flux:callout.heading>
        {{ __('Review') }} · {{ number_format($reviewTasks->total()) }}
        {{ __('Language revisions') }}
    </flux:callout.heading>
    <flux:callout.text class="mb-4">
        {{ __('Explicitly submitted saved revisions. Approval does not publish files. Return a revision to Translations when corrections are needed.') }}
    </flux:callout.text> --}}

    @include('translation-workbench::livewire.translation-workbench.tabs.review.filters')

    <flux:error name="reviewTask" />

    @include('translation-workbench::livewire.translation-workbench.tabs.review.table')
</flux:callout>
