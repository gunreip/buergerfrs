<flux:callout id="workbench-refresh-publish" class="min-w-0" color="cyan">
    <flux:callout.heading icon="route">{{ __('8. Complete refresh — publish and verify') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('Final stage of complete refresh. Export first writes files, then import and node classification re-read the result. The final export and code apply calls are previews, not a second source-code write.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('1. ExportTranslationWorkbenchLangFiles') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ExportTranslationWorkbenchLangFiles.php::handle</p>
                <p class="mt-2">{{ __('Write refreshed language files') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('2. ImportTranslationWorkbenchLangValues') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ImportTranslationWorkbenchLangValues.php::handle</p>
                <p class="mt-2">{{ __('Read exported files again') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('3. ClassifyLangNodeTypes') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ClassifyLangNodeTypes.php::handle</p>
                <p class="mt-2">{{ __('Reclassify exported tree') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('4. InventoryTranslationWorkbenchKeys') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/InventoryTranslationWorkbenchKeys.php::handle</p>
                <p class="mt-2">{{ __('Refresh key inventory') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('5. ExportTranslationWorkbenchLangFiles') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ExportTranslationWorkbenchLangFiles.php::handle</p>
                <p class="mt-2">{{ __('Final export preview') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('6. PlanTranslationWorkbenchCodeUpdates') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/PlanTranslationWorkbenchCodeUpdates.php::handle</p>
                <p class="mt-2">{{ __('Refresh replacement plan') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('7. ApplyTranslationWorkbenchCodeUpdates') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ApplyTranslationWorkbenchCodeUpdates.php::handle</p>
                <p class="mt-2">{{ __('Final source-change preview') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-workbench-refresh-publish"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.workbench.refresh-publish.start"
    direction="top-bottom"
    color="cyan"
    :start-label="['text' => [__('8. Complete refresh — publish and verify')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-publish.step1"
    attach-to="workflow.workbench.refresh-publish.start.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:export-lang-files'), __('Write refreshed language files')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-lang-file-export')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('--write')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-publish.step2"
    attach-to="workflow.workbench.refresh-publish.step1.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:import-lang-values'), __('Read exported files again')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-lang-values'), __('AND unless --skip-lang-file-export')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('--all-locales')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-publish.step3"
    attach-to="workflow.workbench.refresh-publish.step2.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:classify-lang-node-types'), __('Reclassify exported tree')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-lang-node-classification'), __('AND unless --skip-lang-file-export')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: refreshed node types')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-publish.step4"
    attach-to="workflow.workbench.refresh-publish.step3.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:inventory-keys'), __('Refresh key inventory')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-key-inventory')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('--sync; --source-locale')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-publish.step5"
    attach-to="workflow.workbench.refresh-publish.step4.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:export-lang-files'), __('Final export preview')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-lang-file-export')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('Report only; no --write')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-publish.step6"
    attach-to="workflow.workbench.refresh-publish.step5.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:plan-code-updates'), __('Refresh replacement plan')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-code-update-plan')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: remaining proposed changes')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-publish.step7"
    attach-to="workflow.workbench.refresh-publish.step6.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:apply-code-updates'), __('Final source-change preview')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-code-update-plan'), __('AND unless --skip-code-update-apply')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('Report only; no --write')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/workbench/refresh-publish.blade.php"
        :segments="4"
    />
</flux:callout>
