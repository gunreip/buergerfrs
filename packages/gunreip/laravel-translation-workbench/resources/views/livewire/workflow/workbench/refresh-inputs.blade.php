<flux:callout id="workbench-refresh-inputs" class="min-w-0" color="cyan">
    <flux:callout.heading icon="route">{{ __('6. Complete refresh — re-scan and import') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('Only with --complete AND NOT --dry-run. This refresh still runs when source-code application was skipped. It does not repeat truncate or the pre-scan restore write.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('1. ScanTranslationWorkbench') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">ScanTranslationWorkbench::handle · {{ __('Historical implementation removed') }}</p>
                <p class="mt-2">{{ __('Re-scan current source files') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('2. SyncTranslationWorkbenchFoundation') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/SyncTranslationWorkbenchFoundation.php::handle</p>
                <p class="mt-2">{{ __('Re-scan Foundation') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('3. ImportTranslationWorkbenchLangValues') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ImportTranslationWorkbenchLangValues.php::handle</p>
                <p class="mt-2">{{ __('Import current language files') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('4. ImportExistingTranslations') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">ImportExistingTranslations::handle · {{ __('Historical implementation removed') }}</p>
                <p class="mt-2">{{ __('Refresh source-language import') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-workbench-refresh-inputs"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.workbench.refresh-inputs.start"
    direction="top-bottom"
    color="cyan"
    :start-label="['text' => [__('6. Complete refresh — re-scan and import')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-inputs.step1"
    attach-to="workflow.workbench.refresh-inputs.start.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:scan'), __('Re-scan current source files')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Always within complete refresh'), __('--paths; --mark-obsolete')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: refreshed older package records')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-inputs.step2"
    attach-to="workflow.workbench.refresh-inputs.step1.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:sync-foundation'), __('Re-scan Foundation')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-foundation'), __('--paths; --mark-obsolete')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: refreshed Foundation records')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-inputs.step3"
    attach-to="workflow.workbench.refresh-inputs.step2.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:import-lang-values'), __('Import current language files')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-lang-values'), __('--all-locales')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: refreshed language values')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.refresh-inputs.step4"
    attach-to="workflow.workbench.refresh-inputs.step3.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:import-existing'), __('Refresh source-language import')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-import'), __('--source-locale')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: older package values')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/workbench/refresh-inputs.blade.php"
        :segments="4"
    />
</flux:callout>
