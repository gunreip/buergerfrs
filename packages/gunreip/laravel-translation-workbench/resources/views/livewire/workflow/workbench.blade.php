<div class="space-y-4">
    <flux:callout color="cyan">
        <flux:callout.heading icon="route">{{ __('Workbench workflow — current state') }}</flux:callout.heading>
        <flux:callout.text>{{ __('The graphs document the current implementation, including its separate older package and Foundation storage. Read sections 1–8 in order. Each detail shows commands, conditions, inputs and results; information buttons identify the responsible PHP classes. This is an inventory of existing behavior, not a proposed replacement workflow.') }}</flux:callout.text>
    </flux:callout>
    @include('translation-workbench::livewire.workflow.workbench.control')
    @include('translation-workbench::livewire.workflow.workbench.modes')
    @include('translation-workbench::livewire.workflow.workbench.inputs')
    @include('translation-workbench::livewire.workflow.workbench.discovery')
    @include('translation-workbench::livewire.workflow.workbench.classification')
    @include('translation-workbench::livewire.workflow.workbench.publish')
    @include('translation-workbench::livewire.workflow.workbench.refresh-inputs')
    @include('translation-workbench::livewire.workflow.workbench.refresh-analysis')
    @include('translation-workbench::livewire.workflow.workbench.refresh-publish')
    @include('translation-workbench::livewire.workflow.workbench.failure')
</div>
