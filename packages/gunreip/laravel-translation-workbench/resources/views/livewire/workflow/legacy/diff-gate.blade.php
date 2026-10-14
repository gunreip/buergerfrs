<flux:callout id="legacy-diff-gate" class="min-w-0" color="amber">
    <flux:callout.heading icon="route">{{ __('2. Patch availability') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('Only when --apply-diffs is present. The YES branch continues in the patch execution detail; NO continues with section 3.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Patch helper') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/ProjectTranslations.php::applyLatestDiffPatch</p>
                <p class="mt-2">{{ __('Missing or empty patch returns false. Only a successfully applied patch triggers code audit and sync again.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-legacy-diff-gate"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.legacy.diff.start"
    direction="top-bottom"
    color="amber"
    :start-label="['text' => [__('2. --apply-diffs enabled')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-if-else
    id="workflow.legacy.diff.exists"
    attach-to="workflow.legacy.diff.start.anchorNode-end"
    direction="top-bottom"
    side="right"
    color="cyan"
    :condition-label="['text' => [__('latest.patch exists and is nonempty?')], 'width' => 'long', 'align' => 'center']"
    :if-start="['text' => [__('YES'), __('Execute patch steps shown below')], 'width' => 'long', 'color' => 'green']"
    :if-end="['text' => [__('NO'), __('Return false; skip apply AND re-sync')], 'width' => 'long', 'color' => 'zinc']"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/legacy/diff-gate.blade.php"
        :segments="4"
    />
</flux:callout>
