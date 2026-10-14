<flux:callout id="workbench-control" class="min-w-0" color="cyan">
    <flux:callout.heading icon="route">{{ __('Control flow — translation:workbench') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('Sections 1–8 follow the runner order. Skip options remove the corresponding command from the sequence; they are shown beside each step. Artisan commands run in the same PHP process via call(). UI edits and runtime collection are separate entry points.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Runner') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/RunTranslationWorkbench.php::handle</p>
                <p class="mt-2">{{ __('A failed command or exception stops subsequent steps. Completed writes are not globally rolled back.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-workbench-control"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.workbench.control.start"
    direction="top-bottom"
    color="cyan"
    :start-label="['text' => [__('php artisan translation:workbench')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.control.mode"
    attach-to="workflow.workbench.control.start.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('1. Resolve mode and truncate confirmation')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('CLI: standard / --dry-run / --complete')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('Workbench UI starts --complete'), __('Optional pipeline-run-id tracks progress')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.control.scan"
    attach-to="workflow.workbench.control.mode.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('2. Scan and imports')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Source files and language files')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('Older package tables AND Foundation'), __('Independent scans, not a migration')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.control.discover"
    attach-to="workflow.workbench.control.scan.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('3–4. Discovery and classification')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Imported records and source context')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('Candidates, classifications and reports')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.control.publish"
    attach-to="workflow.workbench.control.discover.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('5. Export and code update plan')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Reviewed state / mappings')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('Preview by default'), __('--complete enables source writes')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-if-else
    id="workflow.workbench.control.refresh"
    attach-to="workflow.workbench.control.publish.anchorNode-end"
    direction="top-bottom"
    side="right"
    color="cyan"
    :condition-label="['text' => [__('--complete AND NOT --dry-run?')], 'width' => 'long', 'align' => 'center']"
    :if-start="['text' => [__('YES'), __('6–8. Refresh and final previews')], 'width' => 'long', 'color' => 'green']"
    :if-end="['text' => [__('NO'), __('Finish after initial pass')], 'width' => 'long', 'color' => 'zinc']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.control.done"
    attach-to="workflow.workbench.control.refresh.anchorNode-end"
    direction="top-bottom"
    color="green"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('SUCCESS: exit code 0')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'right' => ['text' => [__('Pipeline report, log and tracker updated')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/workbench/control.blade.php"
        :segments="4"
    />
</flux:callout>
