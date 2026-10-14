<div class="space-y-4" data-legacy-workflow-details>
    <flux:callout color="amber" icon="information-circle">
        <flux:callout.heading>{{ __('Legacy workflow — command-level analysis') }}</flux:callout.heading>
        <flux:callout.text>{{ __('Current implementation of ProjectTranslations::handle(). Numbered sections follow the actual execution order. Left-side labels show inputs, right-side labels show outputs and write effects. Information buttons show the responsible PHP classes and methods.') }}</flux:callout.text>
        <flux:callout.text>{{ __('Important dependency: sync-audits consumes existing compare/lang reports. The current run refreshes those reports only in section 4, if final audits are enabled. Manual values are protected by source=manual; values previously saved by Old Workbench use a different source marker.') }}</flux:callout.text>
    </flux:callout>
    @include('translation-workbench::livewire.workflow.legacy.control')
    @include('translation-workbench::livewire.workflow.legacy.prepare')
    @include('translation-workbench::livewire.workflow.legacy.diff-gate')
    @include('translation-workbench::livewire.workflow.legacy.patch')
    @include('translation-workbench::livewire.workflow.legacy.export')
    @include('translation-workbench::livewire.workflow.legacy.audits')
    @include('translation-workbench::livewire.workflow.legacy.usage')
    @include('translation-workbench::livewire.workflow.legacy.failure')
    @include('translation-workbench::livewire.workflow.legacy.manual')
</div>
