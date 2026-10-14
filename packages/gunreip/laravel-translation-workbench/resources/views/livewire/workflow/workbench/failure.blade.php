<flux:callout id="workbench-failure" class="min-w-0" color="cyan">
    <flux:callout.heading icon="route">{{ __('Failure handling and separate UI entry points') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('The runner stops on the first failed step; there is no transaction spanning the entire pipeline. The failure node describes only the YES branch. Editing and reviewing in Workbench are independent writes, not additional scheduled pipeline steps.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Failure handling') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/RunTranslationWorkbench.php::handle</p>
                <p class="mt-2">{{ __('Exceptions and failed exit codes are logged and reported. Later steps do not run.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Workbench UI') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchEntries.php</p>
                <p class="mt-2">{{ __('Starts translation:workbench --complete in the background with pipeline-run-id. Manual language-value edits and review decisions are separate operations.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Runtime collection') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Foundation/RuntimeDynamicTranslationCollector.php</p>
                <p class="mt-2">{{ __('Dynamic-value collection from application forms can add records outside an Artisan pipeline run.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-workbench-failure"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.workbench.failure.start"
    direction="top-bottom"
    color="cyan"
    :start-label="['text' => [__('Any pipeline command')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-if-else
    id="workflow.workbench.failure.status"
    attach-to="workflow.workbench.failure.start.anchorNode-end"
    direction="top-bottom"
    side="right"
    color="cyan"
    :condition-label="['text' => [__('Exception or non-zero exit code?')], 'width' => 'long', 'align' => 'center']"
    :if-start="['text' => [__('YES'), __('Stop; report failure')], 'width' => 'long', 'color' => 'green']"
    :if-end="['text' => [__('NO'), __('Run next scheduled command')], 'width' => 'long', 'color' => 'zinc']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.failure.report"
    attach-to="workflow.workbench.failure.status.anchorNode-end"
    direction="top-bottom"
    color="red"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('Failure path: log and pipeline report')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Optional pipeline-run-id')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('Tracker marked failed if supplied'), __('Previously completed writes remain')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/workbench/failure.blade.php"
        :segments="4"
    />
</flux:callout>
