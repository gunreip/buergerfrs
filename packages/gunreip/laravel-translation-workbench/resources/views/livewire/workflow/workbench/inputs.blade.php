<flux:callout id="workbench-inputs" class="min-w-0" color="cyan">
    <flux:callout.heading icon="route">{{ __('2. Scan and imports') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('Optional pre-restore runs before scanning only with --complete, without --dry-run, without --truncate and without --skip-suspicious-key-restores. The remaining steps follow in the displayed order.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('1. ApplySuspiciousKeyRestores') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ApplySuspiciousKeyRestores.php::handle</p>
                <p class="mt-2">{{ __('Apply reviewed restores before scanning') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('2. ScanTranslationWorkbench') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/_old/Console/ScanTranslationWorkbench.php::handle</p>
                <p class="mt-2">{{ __('Scan into older package tables') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('3. SyncTranslationWorkbenchFoundation') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/SyncTranslationWorkbenchFoundation.php::handle</p>
                <p class="mt-2">{{ __('Independently scan into Foundation') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('4. ImportTranslationWorkbenchLangValues') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ImportTranslationWorkbenchLangValues.php::handle</p>
                <p class="mt-2">{{ __('Import all locale values') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('5. ImportExistingTranslations') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/_old/Console/ImportExistingTranslations.php::handle</p>
                <p class="mt-2">{{ __('Import source-language values') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-workbench-inputs"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.workbench.inputs.start"
    direction="top-bottom"
    color="cyan"
    :start-label="['text' => [__('2. Scan and imports')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.inputs.step1"
    attach-to="workflow.workbench.inputs.start.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:apply-suspicious-key-restores'), __('Apply reviewed restores before scanning')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Gate: complete write run; see above'), __('IN: reviewed restore decisions')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('--write --only-apply'), __('OUT: modified source files')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.inputs.step2"
    attach-to="workflow.workbench.inputs.step1.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:scan'), __('Scan into older package tables')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Always executed; --paths forwarded'), __('IN: source files')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: entries, occurrences and events'), __('--dry-run / --truncate / --mark-obsolete')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.inputs.step3"
    attach-to="workflow.workbench.inputs.step2.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:sync-foundation'), __('Independently scan into Foundation')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-foundation'), __('IN: source files; --paths')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: findings, keys, key findings'), __('Source files; obsolete state')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.inputs.step4"
    attach-to="workflow.workbench.inputs.step3.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:import-lang-values'), __('Import all locale values')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-lang-values'), __('Deferred when language writes enabled')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: translation_workbench_lang_values'), __('--all-locales; --dry-run forwarded')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.inputs.step5"
    attach-to="workflow.workbench.inputs.step4.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:import-existing'), __('Import source-language values')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-import'), __('IN: language files; --source-locale')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: older package translation values'), __('--dry-run forwarded')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/workbench/inputs.blade.php"
        :segments="4"
    />
</flux:callout>
