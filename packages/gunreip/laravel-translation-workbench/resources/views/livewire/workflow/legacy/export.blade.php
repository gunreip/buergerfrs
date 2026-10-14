<flux:callout id="legacy-export" class="min-w-0" color="amber">
    <flux:callout.heading icon="route">{{ __('3. Language file export') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('Only when --skip-export is absent. Both commands receive --locales when supplied.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('1. TranslationsAuditSubLanguageRedundancy') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsAuditSubLanguageRedundancy.php::handle</p>
                <p class="mt-2">{{ __('Compare variants with their base language. IN: Legacy language values OUT: is_base_duplicate flags in DB This audit is a writer, not only a report') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('2. TranslationsExportLangFiles') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsExportLangFiles.php::handle</p>
                <p class="mt-2">{{ __('Publish Legacy values. IN: eligible Legacy values OUT: lang/{locale}/*.php and JSON Workbench conflict preflight can stop export This does not merge the two databases') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-legacy-export"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.legacy.export.start"
    direction="top-bottom"
    color="amber"
    :start-label="['text' => [__('3. Language file export')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.export.redundancy"
    attach-to="workflow.legacy.export.start.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('1. translations:audit-sub-language-redundancy'), __('Compare variants with their base language')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: Legacy language values')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: is_base_duplicate flags in DB'), __('This audit is a writer, not only a report')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.export.write"
    attach-to="workflow.legacy.export.redundancy.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('2. translations:export-lang-files'), __('Publish Legacy values')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: eligible Legacy values')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: lang/{locale}/*.php and JSON'), __('Workbench conflict preflight can stop export'), __('This does not merge the two databases')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/legacy/export.blade.php"
        :segments="4"
    />
</flux:callout>
