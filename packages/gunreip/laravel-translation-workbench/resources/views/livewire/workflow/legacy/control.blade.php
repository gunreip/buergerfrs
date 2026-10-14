<flux:callout id="legacy-control" class="min-w-0" color="amber">
    <flux:callout.heading icon="route">{{ __('Control flow — project:translations') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('Read sections 1–5 in this order. The detail graphs expand the referenced blocks. Every failed subprocess or thrown exception exits through the separate failure path; completed writes are not globally rolled back.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Runner') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/ProjectTranslations.php::handle / runArtisanStep / runProcess</p>
                <p class="mt-2">{{ __('Each Artisan step runs as a subprocess with --no-interaction and inherited ANSI mode. No global dry-run option. --paths only reaches the literal diff generator; --locales only reaches redundancy audit and export.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-legacy-control"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.legacy.control.start"
    direction="top-bottom"
    color="amber"
    :start-label="['text' => [__('php artisan project:translations')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.control.prepare"
    attach-to="workflow.legacy.control.start.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('1. Preparation and DB sync')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('--paths → literal diff generator')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('--locales → redundancy audit / export')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-if-else
    id="workflow.legacy.control.diff"
    attach-to="workflow.legacy.control.prepare.anchorNode-end"
    direction="top-bottom"
    side="right"
    color="cyan"
    :condition-label="['text' => [__('--apply-diffs?')], 'width' => 'long', 'align' => 'center']"
    :if-start="['text' => [__('YES'), __('2. Check and apply latest.patch')], 'width' => 'long', 'color' => 'green']"
    :if-end="['text' => [__('NO'), __('Continue without source patch')], 'width' => 'long', 'color' => 'zinc']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-if-else
    id="workflow.legacy.control.export"
    attach-to="workflow.legacy.control.diff.anchorNode-end"
    direction="top-bottom"
    side="left"
    color="cyan"
    :condition-label="['text' => [__('Export enabled? NOT --skip-export')], 'width' => 'long', 'align' => 'center']"
    :if-start="['text' => [__('YES'), __('3. Redundancy audit and export')], 'width' => 'long', 'color' => 'green']"
    :if-end="['text' => [__('NO'), __('Skip both commands')], 'width' => 'long', 'color' => 'zinc']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-if-else
    id="workflow.legacy.control.audits"
    attach-to="workflow.legacy.control.export.anchorNode-end"
    direction="top-bottom"
    side="right"
    color="cyan"
    :condition-label="['text' => [__('Final audits enabled? NOT --skip-audits')], 'width' => 'long', 'align' => 'center']"
    :if-start="['text' => [__('YES'), __('4. Final reports; optional section 5')], 'width' => 'long', 'color' => 'green']"
    :if-end="['text' => [__('NO'), __('Skip final reports AND usage apply')], 'width' => 'long', 'color' => 'zinc']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.control.success"
    attach-to="workflow.legacy.control.audits.anchorNode-end"
    direction="top-bottom"
    color="green"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('SUCCESS: exit code 0'), __('Log project.translations.completed')], 'width' => 'long', 'align' => 'center']"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/legacy/control.blade.php"
        :segments="4"
    />
</flux:callout>
