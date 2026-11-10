<flux:modal name="source-usage-workflow" wire:model="usageWorkflowOpen" class="w-full max-w-2xl">
    <x-translation-workbench::ui.common.modal-loading target="reviewWorkflowCase" />
    <div class="space-y-4">
        <flux:heading size="lg" class="flex items-center gap-2">
            <flux:icon.workflow />
            {{ __('Source usage workflow') }}
        </flux:heading>
        @if ($workflowCase)
            @php
                $usageDecision = $workflowDecision['decision'] ?? '';
                $decided = in_array($usageDecision, ['database_value', 'translate'], true);
                $databaseValue = $usageDecision === 'database_value';
                $steps = [
                    ['id' => 'capture', 'label' => __('Source occurrence captured'), 'done' => true,
                        'status' => 'complete', 'color' => 'green', 'icon' => 'check', 'languages' => []],
                    ['id' => 'decision', 'label' => __('Decide source usage'), 'done' => $decided,
                        'status' => $decided ? 'complete' : 'current', 'color' => $decided ? 'green' : 'amber',
                        'icon' => $decided ? 'check' : 'eye', 'languages' => [],
                        'hint' => __('Decide whether this occurrence needs translation or displays database values unchanged.')],
                    ['id' => 'outcome', 'label' => $databaseValue ? __('DatabaseValue — review complete') : __('Continue translation work'),
                        'done' => $databaseValue, 'status' => $databaseValue ? 'complete' : ($decided ? 'current' : 'incomplete'),
                        'color' => $databaseValue ? 'green' : ($decided ? 'amber' : 'zinc'),
                        'icon' => $databaseValue ? 'check' : 'arrow-right', 'languages' => [],
                        'hint' => $databaseValue
                            ? __('Values are intended to be displayed unchanged. This decision does not rewrite source code or remove existing translations.')
                            : __('Translation required records the need for further work; it does not mean that a translation has been saved, approved or published.')],
                ];
            @endphp
            <flux:callout color="sky">
                <x-translation-workbench::ui.common.details>
                    <x-translation-workbench::ui.common.details.item :label="__('Source')">
                        <x-translation-workbench::ui.common.path-preview :path="$workflowCase['source_path']" />
                    </x-translation-workbench::ui.common.details.item>
                    <x-translation-workbench::ui.common.details.item :label="__('Line')" :item="$workflowCase['source_line']" />
                    <x-translation-workbench::ui.common.details.item :label="__('Captured expression')">
                        <x-translation-workbench::ui.common.text-preview :text="$workflowCase['raw_expression']" :mono="true" />
                    </x-translation-workbench::ui.common.details.item>
                    @if ($workflowDecision)
                        <x-translation-workbench::ui.common.details.item :label="__('Reason')" :item="$workflowDecision['reason'] ?? ''" />
                    @endif
                </x-translation-workbench::ui.common.details>
            </flux:callout>
            <x-translation-workbench::ui.common.workflow-timeline :steps="$steps" />
            @if ($usageDecision === 'translate')
                <flux:callout color="amber">
                    <flux:callout.text>{{ __('This source usage case has no verified finding link in this view. Check the source occurrence and its scan results before continuing with a finding. No translation progress is inferred from the usage decision.') }}</flux:callout.text>
                </flux:callout>
            @endif
            <div class="flex justify-end">
                <flux:button variant="outline" wire:click="reviewWorkflowCase">
                    {{ $decided ? __('Review decision') : __('Decide source usage') }}
                </flux:button>
            </div>
        @else
            <flux:callout color="amber">{{ __('This source usage case is no longer available. Refresh the results.') }}</flux:callout>
        @endif
    </div>
</flux:modal>
