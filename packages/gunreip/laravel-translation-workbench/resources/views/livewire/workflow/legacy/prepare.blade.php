<flux:callout id="legacy-prepare" class="min-w-0" color="amber">
    <flux:callout.heading icon="route">{{ __('1. Preparation and DB synchronization') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('Always executed. The current code audit does not refresh the compare or language audit reports before sync-audits reads them.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('1. TranslationsEnsureLangDirectories') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsEnsureLangDirectories.php::handle</p>
                <p class="mt-2">{{ __('Ensure enabled locale directories. IN: configured translation locales OUT: lang/{locale}/ directories') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('2. TranslationsAuditCode') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsAuditCode.php::handle</p>
                <p class="mt-2">{{ __('Scan translation calls. IN: application source files OUT: storage/audits/translations/code/*') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('3. SyncTranslationAudits') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/SyncTranslationAudits.php::handle</p>
                <p class="mt-2">{{ __('Import reports into Legacy DB. IN: existing compare/*.json existing lang/keys.json + lang/locales.json Values read from referenced language files OUT: translation_keys / values / usages source=manual values are protected Other sources can be overwritten') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('4. TranslationsGenerateLiteralDiffs') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsGenerateLiteralDiffs.php::handle</p>
                <p class="mt-2">{{ __('Plan literal → key replacements. IN: Legacy mappings + source files --paths forwarded if supplied OUT: diffs/latest.patch and reports Source files unchanged in this step') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-legacy-prepare"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.legacy.prepare.start"
    direction="top-bottom"
    color="amber"
    :start-label="['text' => [__('1. Preparation and DB synchronization')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.prepare.directories"
    attach-to="workflow.legacy.prepare.start.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('1. translations:ensure-lang-directories'), __('Ensure enabled locale directories')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: configured translation locales')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: lang/{locale}/ directories')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.prepare.code"
    attach-to="workflow.legacy.prepare.directories.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('2. translations:audit-code'), __('Scan translation calls')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: application source files')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: storage/audits/translations/code/*')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.prepare.sync"
    attach-to="workflow.legacy.prepare.code.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('3. translations:sync-audits'), __('Import reports into Legacy DB')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: existing compare/*.json'), __('existing lang/keys.json + lang/locales.json'), __('Values read from referenced language files')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: translation_keys / values / usages'), __('source=manual values are protected'), __('Other sources can be overwritten')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.prepare.diff"
    attach-to="workflow.legacy.prepare.sync.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('4. translations:generate-literal-diffs'), __('Plan literal → key replacements')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: Legacy mappings + source files'), __('--paths forwarded if supplied')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: diffs/latest.patch and reports'), __('Source files unchanged in this step')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/legacy/prepare.blade.php"
        :segments="4"
    />
</flux:callout>
