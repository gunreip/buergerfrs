<flux:callout id="legacy-usage" class="min-w-0" color="amber">
    <flux:callout.heading icon="route">{{ __('5. Apply usage decisions and refresh') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('Only inside the final-audit block AND with --apply-usage-decisions. No language export or lang/compare audit is repeated afterwards.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('1. ApplyTranslationUsageAuditDecisions') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/ApplyTranslationUsageAuditDecisions.php::handle</p>
                <p class="mt-2">{{ __('Apply reviewed usage decisions. IN: ready decisions and source files OUT: modified source files') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('2. TranslationsAuditCode') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsAuditCode.php::handle</p>
                <p class="mt-2">{{ __('Re-scan changed source files. IN: changed source files OUT: updated code audit') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('3. SyncTranslationAudits') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/SyncTranslationAudits.php::handle</p>
                <p class="mt-2">{{ __('Synchronize Legacy DB again. IN: compare/lang reports from section 4 OUT: Legacy DB changes No fresh compare audit after source apply') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('4. TranslationsAuditDuplicateUsageLiterals') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsAuditDuplicateUsageLiterals.php::handle</p>
                <p class="mt-2">{{ __('Refresh duplicate report. IN: Legacy values and usages OUT: duplicate usage reports') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('5. TranslationsAuditFrequentUsageLiterals') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/TranslationsAuditFrequentUsageLiterals.php::handle</p>
                <p class="mt-2">{{ __('Refresh frequent usage report. IN: Legacy values and usages OUT: frequent usage reports') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('6. PreviewTranslationUsageAuditDecisions') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/PreviewTranslationUsageAuditDecisions.php::handle</p>
                <p class="mt-2">{{ __('Refresh remaining usage decisions. IN: ready decisions OUT: updated previews') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-legacy-usage"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.legacy.usage.start"
    direction="top-bottom"
    color="amber"
    :start-label="['text' => [__('--apply-usage-decisions: execution detail')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.usage.apply"
    attach-to="workflow.legacy.usage.start.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('1. translations:usage-decisions:apply --write'), __('Apply reviewed usage decisions')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: ready decisions and source files')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: modified source files')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.usage.code"
    attach-to="workflow.legacy.usage.apply.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('2. translations:audit-code'), __('Re-scan changed source files')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: changed source files')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: updated code audit')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.usage.sync"
    attach-to="workflow.legacy.usage.code.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('3. translations:sync-audits'), __('Synchronize Legacy DB again')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: compare/lang reports from section 4')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: Legacy DB changes'), __('No fresh compare audit after source apply')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.usage.duplicates"
    attach-to="workflow.legacy.usage.sync.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('4. translations:audit-duplicate-usage-literals'), __('Refresh duplicate report')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: Legacy values and usages')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: duplicate usage reports')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.usage.frequent"
    attach-to="workflow.legacy.usage.duplicates.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('5. translations:audit-frequent-usage-literals'), __('Refresh frequent usage report')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: Legacy values and usages')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: frequent usage reports')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.usage.preview"
    attach-to="workflow.legacy.usage.frequent.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('6. translations:usage-decisions:preview'), __('Refresh remaining usage decisions')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: ready decisions')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: updated previews')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/legacy/usage.blade.php"
        :segments="4"
    />
</flux:callout>
