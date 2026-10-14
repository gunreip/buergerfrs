<flux:callout id="workbench-classification" class="min-w-0" color="cyan">
    <flux:callout.heading icon="route">{{ __('4. Classification and key inventory') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('Classification commands receive --dry-run. Inventory synchronizes only when not in dry-run mode.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('1. ClassifyDynamicValues') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ClassifyDynamicValues.php::handle</p>
                <p class="mt-2">{{ __('Classify dynamic value sources') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('2. ResolveUnknownDynamicSources') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ResolveUnknownDynamicSources.php::handle</p>
                <p class="mt-2">{{ __('Resolve unknown source references') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('3. DiscoverDynamicSourceCandidates') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/DiscoverDynamicSourceCandidates.php::handle</p>
                <p class="mt-2">{{ __('Find reviewable source candidates') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('4. ClassifyLangNodeTypes') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ClassifyLangNodeTypes.php::handle</p>
                <p class="mt-2">{{ __('Classify language tree nodes') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('5. InventoryTranslationWorkbenchKeys') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/InventoryTranslationWorkbenchKeys.php::handle</p>
                <p class="mt-2">{{ __('Aggregate established keys') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-workbench-classification"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.workbench.classification.start"
    direction="top-bottom"
    color="cyan"
    :start-label="['text' => [__('4. Classification and key inventory')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.classification.step1"
    attach-to="workflow.workbench.classification.start.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:classify-dynamic-values'), __('Classify dynamic value sources')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-dynamic-classification')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: source classifications')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.classification.step2"
    attach-to="workflow.workbench.classification.step1.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:resolve-unknown-dynamic-sources'), __('Resolve unknown source references')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-dynamic-resolution'), __('IN: option discoveries')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: resolved dynamic sources')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.classification.step3"
    attach-to="workflow.workbench.classification.step2.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:discover-dynamic-source-candidates'), __('Find reviewable source candidates')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-dynamic-source-candidates')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: dynamic source candidates')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.classification.step4"
    attach-to="workflow.workbench.classification.step3.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:classify-lang-node-types'), __('Classify language tree nodes')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-lang-node-classification')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: leaf / container / conflict types')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.classification.step5"
    attach-to="workflow.workbench.classification.step4.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:inventory-keys'), __('Aggregate established keys')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-key-inventory'), __('IN: key and source-locale state')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: inventory; --sync unless dry-run')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/workbench/classification.blade.php"
        :segments="4"
    />
</flux:callout>
