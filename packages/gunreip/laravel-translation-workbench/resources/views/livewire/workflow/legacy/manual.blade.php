<flux:callout id="legacy-manual" class="min-w-0" color="amber">
    <flux:callout.heading icon="route">{{ __('Independent manual entry points') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('These actions are triggered by the user, not by project:translations. They write into the same Legacy domain; they are not sequential pipeline steps.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Management') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Livewire/Admin/TranslationList.php::saveTranslationEdit</p>
                <p class="mt-2">{{ __('Writes manual Legacy values; no immediate language file export.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Sub-Languages') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Livewire/Admin/TranslationSubLanguages.php::saveTranslationEntryEdit</p>
                <p class="mt-2">{{ __('Writes manual Legacy variant values.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Usage') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Livewire/Admin/TranslationUsageAudit.php::createTranslationKeyFromUsageAudit / saveUsageAuditDecision</p>
                <p class="mt-2">{{ __('Creates keys and stores decisions used by preview/apply commands.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Lang Ballast') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Livewire/Admin/TranslationLangBallast.php::setDecisionStatus</p>
                <p class="mt-2">{{ __('Stores cleanup decisions; the pipeline calls only a preview, without --write.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-legacy-manual"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.manual.management" direction="top-bottom" color="violet"
    :anchor-start="['x' => '0rem', 'y' => '2rem']"
    :step-label="['text' => [__('Management (Legacy)'), __('Save translation edit')], 'width' => 'long']"
    :node-labels="['right' => ['text' => [__('translation_values: source=manual')], 'width' => 'long']]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.manual.variants" direction="top-bottom" color="violet"
    :anchor-start="['x' => '60rem', 'y' => '2rem']"
    :step-label="['text' => [__('Sub-Languages'), __('Save variant translation')], 'width' => 'long']"
    :node-labels="['right' => ['text' => [__('translation_values: source=manual')], 'width' => 'long']]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.manual.usage" direction="top-bottom" color="violet"
    :anchor-start="['x' => '0rem', 'y' => '22rem']"
    :step-label="['text' => [__('Usage'), __('Create key / save usage decision')], 'width' => 'long']"
    :node-labels="['right' => ['text' => [__('translation_keys / usage audit decisions')], 'width' => 'long']]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.manual.ballast" direction="top-bottom" color="violet"
    :anchor-start="['x' => '60rem', 'y' => '22rem']"
    :step-label="['text' => [__('Lang Ballast'), __('Save cleanup decision')], 'width' => 'long']"
    :node-labels="['right' => ['text' => [__('translation_lang_ballast_decisions')], 'width' => 'long']]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/legacy/manual.blade.php"
        :segments="4"
    />
</flux:callout>
