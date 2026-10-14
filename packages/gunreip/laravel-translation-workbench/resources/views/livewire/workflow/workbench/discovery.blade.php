<flux:callout id="workbench-discovery" class="min-w-0" color="cyan">
    <flux:callout.heading icon="route">{{ __('3. Discovery and review candidates') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">{{ __('These steps run after imports. Each skip option applies independently. The restore call here is a preview, unlike the optional pre-restore in section 2.') }}</flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('1. DiscoverDynamicOptions') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/DiscoverDynamicOptions.php::handle</p>
                <p class="mt-2">{{ __('Discover options from source providers') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('2. DetectDuplicateCandidates') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/DetectDuplicateCandidates.php::handle</p>
                <p class="mt-2">{{ __('Detect duplicate candidates') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('3. DetectSharedKeyCandidates') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/DetectSharedKeyCandidates.php::handle</p>
                <p class="mt-2">{{ __('Find potential shared-key reuse') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('4. DetectSuspiciousKeyedAdditions') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/DetectSuspiciousKeyedAdditions.php::handle</p>
                <p class="mt-2">{{ __('Detect unexplained keyed additions') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('5. ApplySuspiciousKeyRestores') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p class="font-mono text-xs break-all">packages/gunreip/laravel-translation-workbench/src/Console/ApplySuspiciousKeyRestores.php::handle</p>
                <p class="mt-2">{{ __('Preview reviewed restores') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph graph-id="translation-workflow-workbench-discovery"
                :dev="true" :coordinates="true" :dev-counter-auto="true"
                min-width="48rem" min-height="24rem" horizontal-padding="4rem">
<x-translation-workbench::ui.tw-graph.strang.flow-start
    id="workflow.workbench.discovery.start"
    direction="top-bottom"
    color="cyan"
    :start-label="['text' => [__('3. Discovery and review candidates')], 'width' => 'long', 'align' => 'center']"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.discovery.step1"
    attach-to="workflow.workbench.discovery.start.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:discover-dynamic-options'), __('Discover options from source providers')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-dynamic-options'), __('IN: source files / providers')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('--sync only when NOT dry-run'), __('OUT: dynamic option discoveries')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.discovery.step2"
    attach-to="workflow.workbench.discovery.step1.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:detect-duplicates'), __('Detect duplicate candidates')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-duplicates'), __('IN: scanned entries / values')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: duplicate candidates'), __('--dry-run forwarded')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.discovery.step3"
    attach-to="workflow.workbench.discovery.step2.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:detect-shared-key-candidates'), __('Find potential shared-key reuse')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-shared-key-candidates'), __('IN: findings and reviewed shared keys')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: shared-key candidates'), __('--dry-run forwarded')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.discovery.step4"
    attach-to="workflow.workbench.discovery.step3.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:detect-suspicious-keyed-additions'), __('Detect unexplained keyed additions')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-suspicious-keyed-additions'), __('IN: code and provenance')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: suspicious-key report'), __('No dry-run argument in this call')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
<x-translation-workbench::ui.tw-graph.strang.flow-step
    id="workflow.workbench.discovery.step5"
    attach-to="workflow.workbench.discovery.step4.anchorNode-end"
    direction="top-bottom"
    color="cyan"
    before-length="3rem"
    after-length="5rem"
    :step-label="['text' => [__('translation-workbench:apply-suspicious-key-restores'), __('Preview reviewed restores')], 'width' => 'long', 'align' => 'center']"
    :node-labels="[
        'left' => ['text' => [__('Unless --skip-suspicious-key-restores'), __('IN: restore decisions')], 'width' => 'long', 'align' => 'left'],
        'right' => ['text' => [__('OUT: restore preview'), __('No --write in this call')], 'width' => 'long', 'align' => 'left'],
    ]"
/>
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/workbench/discovery.blade.php"
        :segments="4"
    />
</flux:callout>
