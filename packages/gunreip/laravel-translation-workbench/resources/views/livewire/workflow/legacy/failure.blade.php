<flux:callout id="legacy-failure" class="min-w-0" color="amber">
    <flux:callout.heading icon="route">{{ __('Failure path — applies to every section') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('The runner stops subsequent steps. There is no transaction covering all subprocesses and files.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Failure handling') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">app/Console/Commands/ProjectTranslations.php::runProcess / handle</p>
                <p class="mt-2">{{ __('A nonzero process exit code throws RuntimeException. handle catches Throwable, records failure and returns self::FAILURE.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-legacy-failure"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.legacy.failure.start"
    direction="top-bottom"
    color="amber"
    :start-label="['text' => [__('Any failed subprocess / exception')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.legacy.failure.catch"
    attach-to="workflow.legacy.failure.start.anchorNode-end"
    direction="top-bottom"
    color="red"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('ProjectTranslations::handle catch(Throwable)'), __('Print error and log failure')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('IN: failure from any section')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: project.translations.failed'), __('FAILURE: exit code 1'), __('Previously completed writes remain')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/legacy/failure.blade.php"
        :segments="4"
    />
</flux:callout>
