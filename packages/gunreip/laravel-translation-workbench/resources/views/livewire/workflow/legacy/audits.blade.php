<flux:callout id="legacy-audits" class="min-w-0" color="amber">
    <flux:callout.heading icon="route">{{ __('4. Final audits and previews') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('Only when --skip-audits is absent. These commands run after export, or after the export block was skipped. They do not run sync-audits again in this block.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('1. TranslationsAuditLang') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsAuditLang.php::handle</p>
                <p class="mt-2">{{ __('Inventory language files. IN: language files OUT: audits/translations/lang/*') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('2. TranslationsAuditCompare') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsAuditCompare.php::handle</p>
                <p class="mt-2">{{ __('Compare source and language reports. IN: code/lang audit data Legacy DB context OUT: audits/translations/compare/* These reports can feed a later sync') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('3. TranslationsAuditLangBallast') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsAuditLangBallast.php::handle</p>
                <p class="mt-2">{{ __('Find language file cleanup candidates. IN: language files + Legacy state OUT: cleanup candidate reports') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('4. TranslationsApplyLangBallastDecisions') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsApplyLangBallastDecisions.php::handle</p>
                <p class="mt-2">{{ __('Preview approved cleanup decisions. IN: ballast decisions OUT: preview files; no --write supplied No language file cleanup is applied here') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('5. TranslationsAuditDuplicateUsageLiterals') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsAuditDuplicateUsageLiterals.php::handle</p>
                <p class="mt-2">{{ __('Find repeated source-language values. IN: Legacy values and usages OUT: duplicate usage reports') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('6. TranslationsAuditFrequentUsageLiterals') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsAuditFrequentUsageLiterals.php::handle</p>
                <p class="mt-2">{{ __('Find frequently used literals. IN: Legacy values and usages OUT: frequent usage reports') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('7. PreviewTranslationUsageAuditDecisions') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/PreviewTranslationUsageAuditDecisions.php::handle</p>
                <p class="mt-2">{{ __('Plan reviewed usage changes. IN: ready usage decisions OUT: source-change previews') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-legacy-audits"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.legacy.audits.start"
    direction="top-bottom"
    color="amber"
    :start-label="['text' => [__('4. Final audits and previews')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.audits.lang"
    attach-to="workflow.legacy.audits.start.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('1. translations:audit-lang'), __('Inventory language files')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: language files')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: audits/translations/lang/*')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.audits.compare"
    attach-to="workflow.legacy.audits.lang.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('2. translations:audit-compare'), __('Compare source and language reports')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: code/lang audit data'), __('Legacy DB context')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: audits/translations/compare/*'), __('These reports can feed a later sync')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.audits.ballast"
    attach-to="workflow.legacy.audits.compare.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('3. translations:audit-lang-ballast'), __('Find language file cleanup candidates')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: language files + Legacy state')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: cleanup candidate reports')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.audits.ballastpreview"
    attach-to="workflow.legacy.audits.ballast.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('4. translations:lang-ballast:apply'), __('Preview approved cleanup decisions')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: ballast decisions')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: preview files; no --write supplied'), __('No language file cleanup is applied here')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.audits.duplicates"
    attach-to="workflow.legacy.audits.ballastpreview.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('5. translations:audit-duplicate-usage-literals'), __('Find repeated source-language values')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: Legacy values and usages')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: duplicate usage reports')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.audits.frequent"
    attach-to="workflow.legacy.audits.duplicates.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('6. translations:audit-frequent-usage-literals'), __('Find frequently used literals')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: Legacy values and usages')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: frequent usage reports')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.audits.preview"
    attach-to="workflow.legacy.audits.frequent.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('7. translations:usage-decisions:preview'), __('Plan reviewed usage changes')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: ready usage decisions')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: source-change previews')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-if-else
    id="workflow.legacy.audits.usage"
    attach-to="workflow.legacy.audits.preview.anchorNode-end"
    direction="top-bottom"
    side="right"
    color="cyan"
    :condition-label="['text' => [__('--apply-usage-decisions?')], 'width' => 'long', 'align' => 'center']"
    :if-start="['text' => [__('YES'), __('5. Apply and refresh')], 'width' => 'long', 'color' => 'green']"
    :if-end="['text' => [__('NO'), __('Finish successfully')], 'width' => 'long', 'color' => 'zinc']"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/legacy/audits.blade.php"
        :segments="4"
    />
</flux:callout>
