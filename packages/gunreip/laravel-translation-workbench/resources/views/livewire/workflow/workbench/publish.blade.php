<flux:callout id="workbench-publish" class="min-w-0" color="cyan">
    <flux:callout.heading icon="route">{{ __('5. Export and code updates') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('Language export and source apply have different write gates. Deferred import is selected when language writes are enabled, even if --skip-lang-file-export suppresses export. In complete mode that import takes place in section 6 instead.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('1. ExportTranslationWorkbenchLangFiles') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ExportTranslationWorkbenchLangFiles.php::handle</p>
                <p class="mt-2">{{ __('Preview or write language files') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('2. ImportTranslationWorkbenchLangValues') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ImportTranslationWorkbenchLangValues.php::handle</p>
                <p class="mt-2">{{ __('Deferred import after initial export') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('3. PlanTranslationWorkbenchCodeUpdates') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/PlanTranslationWorkbenchCodeUpdates.php::handle</p>
                <p class="mt-2">{{ __('Plan source replacements') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('4. ApplyTranslationWorkbenchCodeUpdates') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ApplyTranslationWorkbenchCodeUpdates.php::handle</p>
                <p class="mt-2">{{ __('Preview or apply planned replacements') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-workbench-publish"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.workbench.publish.start"
    direction="top-bottom"
    color="cyan"
    :start-label="['text' => [__('5. Export and code updates')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.publish.step1"
    attach-to="workflow.workbench.publish.start.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:export-lang-files'), __('Preview or write language files')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-lang-file-export'), __('IN: eligible Foundation values')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('--write if complete OR write-lang-files'), __('Never --write with --dry-run')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.publish.step2"
    attach-to="workflow.workbench.publish.step1.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:import-lang-values'), __('Deferred import after initial export')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('If import deferred AND NOT complete'), __('Unless --skip-lang-values')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: language values from files'), __('--all-locales')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.publish.step3"
    attach-to="workflow.workbench.publish.step2.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:plan-code-updates'), __('Plan source replacements')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-code-update-plan'), __('IN: mappings and source files; --paths')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: code update plan')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.publish.step4"
    attach-to="workflow.workbench.publish.step3.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:apply-code-updates'), __('Preview or apply planned replacements')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-code-update-plan'), __('AND unless --skip-code-update-apply')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('--write only if complete AND NOT dry-run'), __('Otherwise preview / patch report')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/workbench/publish.blade.php"
        :segments="4"
    />
</flux:callout>
