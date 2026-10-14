<flux:callout id="workbench-modes" class="min-w-0" color="cyan">
    <flux:callout.heading icon="route">{{ __('1. Execution modes and confirmation') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('The following nodes compare modes; they are not four consecutive pipeline operations. --write-lang-files alone enables language-file writes, but does not enable source-code writes or the complete refresh. Explicit skip options still apply.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Mode resolution') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/RunTranslationWorkbench.php::handle</p>
                <p class="mt-2">{{ __('writeLangFiles = (write-lang-files OR complete) AND NOT dry-run. Complete also sets mark-obsolete, including when combined with dry-run.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-workbench-modes"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.workbench.modes.start"
    direction="top-bottom"
    color="cyan"
    :start-label="['text' => [__('1. Mode and destructive options')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.modes.standard"
    attach-to="workflow.workbench.modes.start.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('Standard run')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('No --complete and no --dry-run')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('Database synchronization is active'), __('Language export and code apply: preview')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.modes.dry"
    attach-to="workflow.workbench.modes.standard.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('--dry-run')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Can also be combined with --complete')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('No complete refresh or write export'), __('Reports and logs can still be written')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.modes.complete"
    attach-to="workflow.workbench.modes.dry.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('--complete')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Requires NOT --dry-run for writes')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('mark-obsolete enabled'), __('Language export + source apply + refresh')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.modes.truncate"
    attach-to="workflow.workbench.modes.complete.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('Truncate confirmation before all steps')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('--truncate: package + Foundation scope'), __('--truncate-foundation: Foundation only')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('Declined confirmation → failure'), __('--force-truncate confirms destructive scope')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/workbench/modes.blade.php"
        :segments="4"
    />
</flux:callout>
