<flux:modal
    class="w-full max-w-4xl"
    name="workbench-workflow-status"
    wire:model="workflowStatusOpen"
>
    <div class="space-y-4">
        <div class="flex items-center justify-between gap-3 pe-6">
            <flux:heading
                class="flex items-center gap-2"
                size="xl"
            >
                <flux:icon.workflow
                    class="shrink-0"
                    size="sm"
                />
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
                color="teal"
            >
                <dl class="mt-3 grid grid-cols-[auto_minmax(0,1fr)] items-baseline gap-x-4 gap-y-2 text-sm">
                    <dt class="font-medium">
                        <flux:callout.heading>
                            {{ __('Finding ID') }}:
                        </flux:callout.heading>
                    </dt>
                    <dd class="min-w-0 tabular-nums">
                        <flux:callout.text>
                            #{{ $workflowStatus['finding']['id'] }}
                        </flux:callout.text>
                    </dd>

                    <dt class="font-medium">
                        <flux:callout.heading>
                            {{ __('Translation key record ID') }}:
                        </flux:callout.heading>
                    </dt>
                    <dd class="min-w-0 tabular-nums">
                        @if ($workflowStatus['finding']['key_id'])
                            <flux:callout.text>
                                #{{ $workflowStatus['finding']['key_id'] }}
                            </flux:callout.text>
                        @else
                            <flux:callout.text>
                                {{ __('Not assigned') }}
                            </flux:callout.text>
                        @endif
                    </dd>

                    <dt class="font-medium">
                        <flux:callout.heading>
                            {{ __('Source') }}:
                        </flux:callout.heading>
                    </dt>
                    <dd class="min-w-0">
                        <x-translation-workbench::ui.common.path-preview
                            :path="$workflowStatus['finding']['path']"
                            :line="$workflowStatus['finding']['source_line']"
                            :column="$workflowStatus['finding']['source_column']"
                            :length="60"
                            side="center"
                            :heading="__('Source file')"
                        />
                    </dd>

                    <dt class="font-medium">
                        <flux:callout.heading>
                            {{ __('Translation key') }}:
                        </flux:callout.heading>
                    </dt>
                    <dd class="min-w-0">
                        @if ($workflowStatus['finding']['full_name'])
                            <x-translation-workbench::ui.common.text-preview
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
                    </dd>
                </dl>
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
            <flux:callout
                color="blue"
                :heading="__('Workflow timeline')"
                icon="timeline"
            >
                <x-translation-workbench::ui.common.workflow-timeline
                    :navigable="true" :steps="$workflowStatus['steps']" :test-mode="$testToken !== ''"
                    :countries="$translationFlagCountries"
                />
            </flux:callout>
        @endif
    </div>
</flux:modal>
