<div class="space-y-4">
    <flux:callout color="cyan">
        <flux:callout.heading icon="route">{{ __('Workbench workflow — inventory before consolidation') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('The graphs document the implementation before consolidation, including its separate older package and Foundation storage. Read sections 1–8 in order. Each detail shows commands, conditions, inputs and results; information buttons identify the responsible PHP classes. This is an inventory of existing behavior, not a proposed replacement workflow.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout
        color="amber"
        icon="information-circle"
    >
        <flux:callout.text>
            {{ __('Pipeline update: regular runs now capture source code through sync-foundation. The historical scan, import-existing and detect-duplicates commands shown below are retired and no longer registered in Artisan; their implementations are archived. Dynamic option discovery stores observations and Foundation assignments without creating Legacy entries or values. The detailed graphs retain the original inventory for comparison.') }}
        </flux:callout.text>
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
