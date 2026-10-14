<flux:callout id="legacy-patch" class="min-w-0" color="amber">
    <flux:callout.heading icon="route">{{ __('2. Patch execution detail') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('This is the YES branch of patch availability. After these steps, continue with section 3.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Patch processes') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/ProjectTranslations.php::applyLatestDiffPatch</p>
                <p class="mt-2">{{ __('Runs git apply --check followed by git apply with the full latest.patch path. An unsuccessful process throws; no re-sync follows a failed patch.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Refresh') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/ProjectTranslations.php::handle</p>
                <p class="mt-2">{{ __('Calls TranslationsAuditCode::handle and SyncTranslationAudits::handle after a successful apply.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-legacy-patch"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.legacy.patch.start"
    direction="top-bottom"
    color="amber"
    :start-label="['text' => [__('2. YES: nonempty latest.patch')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.patch.check"
    attach-to="workflow.legacy.patch.start.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('git apply --check latest.patch'), __('Validate patch without source writes')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: storage/audits/translations/diffs/latest.patch')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('Failure → abort entire pipeline')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.patch.apply"
    attach-to="workflow.legacy.patch.check.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('git apply latest.patch'), __('Apply the checked patch')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: checked patch')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: modified source files')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.patch.code"
    attach-to="workflow.legacy.patch.apply.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translations:audit-code'), __('Refresh code audit')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: changed source files')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: code audit files')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.patch.sync"
    attach-to="workflow.legacy.patch.code.anchorNode-end"
    direction="top-bottom"
    color="amber"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translations:sync-audits'), __('Synchronize Legacy DB again')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: existing compare/lang reports')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: Legacy DB changes'), __('No fresh lang/compare audit before this sync')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/legacy/patch.blade.php"
        :segments="4"
    />
</flux:callout>
