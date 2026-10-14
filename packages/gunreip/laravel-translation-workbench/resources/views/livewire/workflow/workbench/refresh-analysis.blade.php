<flux:callout id="workbench-refresh-analysis" class="min-w-0" color="cyan">
    <flux:callout.heading icon="route">{{ __('7. Complete refresh — discovery and classification') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('Only after section 6. The same skip options remain active. Discovery, duplicate/shared/suspicious detection, restore preview and dynamic/language-node classification are repeated in this order. Key inventory follows later in section 8.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Refresh sequence') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/RunTranslationWorkbench.php::completeRefreshSteps</p>
                <p class="mt-2">{{ __('No second restore write occurs here. The restore command is called without --write.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-workbench-refresh-analysis"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.workbench.refresh-analysis.start"
    direction="top-bottom"
    color="cyan"
    :start-label="['text' => [__('7. Complete refresh — analysis')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-analysis.step1"
    attach-to="workflow.workbench.refresh-analysis.start.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:discover-dynamic-options'), __('Discover options from source providers')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-dynamic-options'), __('IN: source files / providers')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('--sync'), __('OUT: dynamic option discoveries')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-analysis.step2"
    attach-to="workflow.workbench.refresh-analysis.step1.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:detect-duplicates'), __('Detect duplicate candidates')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-duplicates'), __('IN: scanned entries / values')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: duplicate candidates'), __('Write-enabled refresh')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-analysis.step3"
    attach-to="workflow.workbench.refresh-analysis.step2.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:detect-shared-key-candidates'), __('Find potential shared-key reuse')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-shared-key-candidates'), __('IN: findings and reviewed shared keys')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: shared-key candidates'), __('Write-enabled refresh')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-analysis.step4"
    attach-to="workflow.workbench.refresh-analysis.step3.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:detect-suspicious-keyed-additions'), __('Detect unexplained keyed additions')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-suspicious-keyed-additions'), __('IN: code and provenance')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: suspicious-key report'), __('No dry-run argument in this call')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-analysis.step5"
    attach-to="workflow.workbench.refresh-analysis.step4.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:apply-suspicious-key-restores'), __('Preview reviewed restores')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-suspicious-key-restores'), __('IN: restore decisions')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: restore preview'), __('No --write in this call')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-analysis.step6"
    attach-to="workflow.workbench.refresh-analysis.step5.anchorNode-end"
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
    id="workflow.workbench.refresh-analysis.step7"
    attach-to="workflow.workbench.refresh-analysis.step6.anchorNode-end"
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
    id="workflow.workbench.refresh-analysis.step8"
    attach-to="workflow.workbench.refresh-analysis.step7.anchorNode-end"
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
    id="workflow.workbench.refresh-analysis.step9"
    attach-to="workflow.workbench.refresh-analysis.step8.anchorNode-end"
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
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/workbench/refresh-analysis.blade.php"
        :segments="4"
    />
</flux:callout>
