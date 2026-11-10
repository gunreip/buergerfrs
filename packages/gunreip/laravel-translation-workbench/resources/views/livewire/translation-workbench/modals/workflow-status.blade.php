<flux:modal
    class="w-full max-w-4xl"
    name="workbench-workflow-status"
    wire:model="workflowStatusOpen"
>
    <x-translation-workbench::ui.common.modal-loading target="openWorkflowStatus, continueWorkflow" />
    <div class="space-y-4">
        <div class="mr-2 flex items-center justify-between gap-3 pe-6">
            <flux:heading
                class="flex items-center gap-2"
                size="xl"
            >
                <flux:icon.workflow class="size-8 shrink-0" />
                {{ __('Workflow status') }}
            </flux:heading>
            @if (($workflowStatus['shared']['shared'] ?? 0) > 1)
                <flux:badge
                    class="shrink-0"
                    color="sky"
                >{{ __('Shared key') }} · {{ $workflowStatus['shared']['shared'] }}</flux:badge>
            @elseif (($workflowStatus['shared']['candidates'] ?? 0) > 1)
                <flux:badge
                    class="shrink-0"
                    color="sky"
                >{{ __('Shared candidate') }}</flux:badge>
            @endif
        </div>

        <flux:error name="workflowStatus" />

        @if ($workflowStatus)
            <flux:callout
                :heading="__('Finding and translation key')"
                icon="lighthouse"
                color="green"
            >
                <x-translation-workbench::ui.common.details class="mt-3">
                    <x-translation-workbench::ui.common.details.item
                        class="tabular-nums"
                        :label="__('Finding ID')"
                        :item="'#' . $workflowStatus['finding']['id']"
                    />

                    <x-translation-workbench::ui.common.details.item
                        class="tabular-nums"
                        :label="__('Translation key record ID')"
                        :item="$workflowStatus['finding']['key_id']
                            ? '#' . $workflowStatus['finding']['key_id']
                            : __('Not assigned')"
                    />

                    <x-translation-workbench::ui.common.details.item :label="__('Source')">
                        <x-translation-workbench::ui.common.path-preview
                            :path="$workflowStatus['finding']['path']"
                            :line="$workflowStatus['finding']['source_line']"
                            :column="$workflowStatus['finding']['source_column']"
                            :length="60"
                            side="center"
                            :heading="__('Source file')"
                        />
                    </x-translation-workbench::ui.common.details.item>

                    <x-translation-workbench::ui.common.details.item :label="__('Translation key')">
                        @if ($workflowStatus['finding']['full_name'])
                            <x-translation-workbench::ui.common.text-preview
                                :mono="true"
                                :text="(($workflowStatus['finding']['namespace'] ?? '') !== '' ? $workflowStatus['finding']['namespace'].'::' : '').$workflowStatus['finding']['full_name']"
                                :length="60"
                                side="center"
                                :heading="__('Assigned translation key')"
                            />
                        @else
                            <flux:callout.text>
                                {{ __('No translation key assigned') }}
                            </flux:callout.text>
                        @endif
                    </x-translation-workbench::ui.common.details.item>
                </x-translation-workbench::ui.common.details>
            </flux:callout>

            @if ($workflowStatus['finding']['status'] !== 'active')
                <flux:callout
                    color="amber"
                    :heading="__('Archived finding')"
                    icon="archive"
                >
                    <flux:callout.text>
                        {{ __('This source occurrence is no longer active.') }}
                    </flux:callout.text>
                    @if ($workflowStatus['successor'])
                        <flux:button
                            size="sm"
                            wire:click="openWorkflowStatus({{ $workflowStatus['successor'] }})"
                        >
                            {{ __('Successor') }} #{{ $workflowStatus['successor'] }}
                        </flux:button>
                    @endif
                </flux:callout>
            @endif

            @php
                $timelineSharedKey = null;
                if (($workflowStatus['shared']['shared'] ?? 0) > 1 && $workflowStatus['finding']['key_id']) {
                    $timelineNamespace = $workflowStatus['finding']['namespace'] ?? '';
                    $timelineSharedKey = [
                        'id' => $workflowStatus['finding']['key_id'],
                        'name' =>
                            ($timelineNamespace !== '' ? $timelineNamespace . '::' : '') .
                            $workflowStatus['finding']['full_name'],
                    ];
                }
            @endphp

            <flux:callout
                color="blue"
                :heading="__('Workflow timeline')"
                icon="timeline"
            >
                <x-translation-workbench::ui.common.workflow-timeline
                    :navigable="true"
                    :steps="$workflowStatus['steps']"
                    :test-mode="$testToken !== ''"
                    :countries="$translationFlagCountries"
                    :shared-key="$timelineSharedKey"
                />
            </flux:callout>
        @endif
    </div>
</flux:modal>
