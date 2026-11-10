<flux:modal
    class="w-full max-w-4xl"
    name="workbench-run-scan"
    wire:model.live.self="scanWorkflowOpen"
>
    <x-translation-workbench::ui.common.modal-loading
        target="refreshScanStorage, showWorkflowScanResult, startScanPreview, confirmScan"
    />
    @if ($scanWorkflowOpen)
        <div class="space-y-4">
            <flux:heading
                class="flex items-center gap-2"
                size="xl"
            >
                <flux:icon.arrow-path />
                {{ __('Run scan') }}
            </flux:heading>
            @if (($scanFreshness['reason'] ?? '') !== '' && !($scanFreshness['reusable'] ?? false))
                <flux:text>{{ $scanFreshness['reason'] }}</flux:text>
            @endif

            <flux:callout
                color="sky"
                icon="arrow-path"
                :heading="__('Scan scope')"
            >
                <flux:accordion transition>
                    <flux:accordion.item>
                        <flux:accordion.heading>
                            {{ __('Scan scope') }}
                        </flux:accordion.heading>
                        <flux:accordion.content>
                            <flux:callout.text>
                                {{ __('The regular scanner checks the configured source paths and compares them with the working database. Source files and language files are not rewritten.') }}
                            </flux:callout.text>
                            @if (isset($scanWorkflow['target']))
                                <x-translation-workbench::ui.common.details class="mt-3">
                                    <x-translation-workbench::ui.common.details.item
                                        :label="__('Database')"
                                        :item="$scanWorkflow['target']['database']"
                                    />
                                    <x-translation-workbench::ui.common.details.item
                                        :label="__('Schema')"
                                        :item="$scanWorkflow['target']['schema']"
                                    />
                                    <x-translation-workbench::ui.common.details.item :label="__('Source paths')">
                                        @foreach ($scanWorkflow['target']['paths'] as $path)
                                            <flux:callout.text class="font-mono">
                                                {{ $path }}/
                                            </flux:callout.text>
                                        @endforeach
                                    </x-translation-workbench::ui.common.details.item>
                                </x-translation-workbench::ui.common.details>
                            @endif
                        </flux:accordion.content>
                    </flux:accordion.item>
                </flux:accordion>
            </flux:callout>

            <flux:callout
                color="purple"
                icon="archive-box"
                heading="{{ __('Scan storage') }}"
            >
                <flux:accordion transition>
                    <flux:accordion.item>
                        <flux:accordion.heading>
                            {{ __('Scan storage') }}
                        </flux:accordion.heading>
                        <flux:accordion.content>
                            <flux:callout.text class="mt-3">
                                {{ __('Scan counts and sizes cover regular saved scans. Sizes describe stored payloads, excluding indexes and database overhead. Expired database scan caches are cleaned hourly by the scheduler. Saved scans and finding history are retained.') }}
                            </flux:callout.text>
                            @if (!empty($scanStorage))
                                <div class="mt-3 grid grid-cols-1 items-start gap-6 md:grid-cols-2">
                                    <x-translation-workbench::ui.common.details
                                        class="min-w-0"
                                        label-width="18rem"
                                    >
                                        <x-translation-workbench::ui.common.details.item :label="__('Saved regular scans')">
                                            <x-ui.number :value="$scanStorage['scans']" />
                                        </x-translation-workbench::ui.common.details.item>
                                        <x-translation-workbench::ui.common.details.item :label="__('Finding confirmations across scans')">
                                            <x-ui.number :value="$scanStorage['confirmations']" />
                                        </x-translation-workbench::ui.common.details.item>
                                        <x-translation-workbench::ui.common.details.item
                                            :label="__('Stored scan snapshots')"
                                            :item="Illuminate\Support\Number::fileSize(
                                                (int) $scanStorage['snapshot_bytes'],
                                            )"
                                        />
                                    </x-translation-workbench::ui.common.details>
                                    <x-translation-workbench::ui.common.details
                                        class="min-w-0"
                                        label-width="18rem"
                                    >
                                        <x-translation-workbench::ui.common.details.item
                                            :label="__('Stored scan results')"
                                            :item="Illuminate\Support\Number::fileSize(
                                                (int) $scanStorage['result_bytes'],
                                            )"
                                        />
                                        @if ($scanStorage['cache']['supported'])
                                            <x-translation-workbench::ui.common.details.item
                                                :label="__('Temporary scan cache')"
                                                :item="Illuminate\Support\Number::fileSize(
                                                    $scanStorage['cache']['bytes'],
                                                )"
                                            />
                                            <x-translation-workbench::ui.common.details.item :label="__('Expired cache entries awaiting cleanup')">
                                                <x-ui.number :value="$scanStorage['cache']['expired']" />
                                            </x-translation-workbench::ui.common.details.item>
                                        @endif
                                    </x-translation-workbench::ui.common.details>
                                </div>
                            @endif

                            <div class="mt-3 flex justify-end gap-2">
                                <flux:button.group>
                                    <flux:button
                                        size="sm"
                                        variant="filled"
                                        color="purple"
                                        wire:click="refreshScanStorage"
                                        wire:loading.attr="disabled"
                                        wire:target="refreshScanStorage"
                                        :disabled="$testToken !== ''"
                                    >
                                        <flux:icon.arrow-path class="size-5" />
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="filled"
                                        color="purple"
                                        wire:click="refreshScanStorage"
                                        wire:loading.attr="disabled"
                                        wire:target="refreshScanStorage"
                                        :disabled="$testToken !== ''"
                                    >
                                        {{ __('Refresh storage summary') }}
                                    </flux:button>
                                </flux:button.group>
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                </flux:accordion>
            </flux:callout>

            @php
                $scanState = $scanWorkflow['state'] ?? 'idle';
                $scanRunning = in_array($scanState, ['queued_preview', 'scanning', 'queued_apply', 'applying'], true);
            @endphp

            @if ($scanRunning)
                <livewire:translation-workbench.scan-progress
                    :scan-workflow-id="$scanWorkflowId"
                    :scan-workflow="['state' => $scanState, 'progress' => ['steps' => $scanWorkflow['progress']['steps'] ?? []]]"
                    :key="'scan-progress-'.$scanWorkflowId"
                    @scan-phase-ended="pollScanWorkflow"
                />
            @elseif (!empty($scanWorkflow['progress']['steps']))
                @include('translation-workbench::livewire.translation-workbench.scan.progress')
            @endif
            @if ($scanState === 'changed')
                <flux:callout
                    color="amber"
                    :heading="__('Preview changed')"
                    :text="__('Source files or stored findings changed after the previous preview. Nothing was saved. Review the refreshed counts and confirm again.')"
                />
            @endif
            @if (isset($scanWorkflow['preview']['counts']))
                <flux:callout
                    :heading="__('Scan result summary')"
                    icon="radar"
                    color="teal"
                >
                    <div class="mt-3 grid grid-cols-1 items-start gap-6 md:grid-cols-2">
                        @foreach (array_chunk(['confirmed' => __('Confirmed findings'), 'created' => __('New findings'), 'moved' => __('Moved findings'), 'removed' => __('Removed findings'), 'review_files' => __('Files requiring review'), 'outside_scope_files' => __('Files outside scan scope'), 'suggested_keys_initialized' => __('Missing SuggestedKeys to initialize'), 'usage_decisions' => __('Usage decisions considered')], 4, true) as $resultBlock)
                            <x-translation-workbench::ui.common.details
                                class="min-w-0"
                                label-width="18rem"
                            >
                                @foreach ($resultBlock as $count => $label)
                                    <x-translation-workbench::ui.common.details.item
                                        class="tabular-nums"
                                        :label="$label"
                                    >
                                        <x-ui.number :value="$scanWorkflow['preview']['counts'][$count]" />
                                    </x-translation-workbench::ui.common.details.item>
                                @endforeach
                            </x-translation-workbench::ui.common.details>
                        @endforeach
                    </div>
                    <flux:callout.text class="mt-3">
                        {{ __('Ambiguous findings are preserved for review. Existing SuggestedKeys remain unchanged; missing keys are initialized once and recorded in the scan history.') }}
                    </flux:callout.text>
                </flux:callout>
            @endif
            @if ($scanState === 'complete')
                <flux:callout
                    class="justify-stretch hyphens-auto"
                    color="green"
                    icon="database-plus"
                    :heading="__('Scan saved')"
                    :text="__('The overview and scan results have been refreshed. Review unresolved cases in Scan results before continuing source replacement.')"
                >
                    <div class="flex flex-col items-end gap-2">
                        <flux:button.group>
                            <flux:button
                                size="sm"
                                variant="filled"
                                color="emerald"
                                wire:click="showWorkflowScanResult"
                            >
                                <flux:icon.eye size="5" />
                            </flux:button>
                            <flux:button
                                size="sm"
                                variant="filled"
                                color="emerald"
                                wire:click="showWorkflowScanResult"
                            >
                                {{ __('Show scan results') }}
                            </flux:button>
                        </flux:button.group>
                    </div>
                </flux:callout>
            @elseif ($scanState === 'failed')
                <flux:callout
                    color="red"
                    icon="siren"
                    :heading="__('Scan could not complete')"
                    :text="$scanWorkflow['error'] ?? __('Start a new preview.')"
                />
            @endif

            <flux:error name="scanWorkflow" />

            @if (!in_array($scanState, ['queued_preview', 'scanning', 'queued_apply', 'applying'], true) && $testToken === '')
                <div class="flex justify-end gap-2">
                    <flux:button.group>
                        <flux:button
                            variant="filled"
                            color="amber"
                            size="sm"
                            wire:click="startScanPreview"
                            wire:loading.attr="disabled"
                        >
                            <flux:icon.arrow-path size="4" />
                        </flux:button>
                        <flux:button
                            variant="filled"
                            color="amber"
                            size="sm"
                            wire:click="startScanPreview"
                            wire:loading.attr="disabled"
                        >
                            {{ __('Preview scan') }}
                        </flux:button>
                    </flux:button.group>
                    @if (in_array($scanState, ['preview_ready', 'changed'], true))
                        <flux:button.group>
                            <flux:button
                                variant="filled"
                                color="green"
                                size="sm"
                                wire:click="confirmScan"
                                wire:loading.attr="disabled"
                            >
                                <flux:icon.database-plus size="5" />
                            </flux:button>
                            <flux:button
                                variant="filled"
                                color="green"
                                size="sm"
                                wire:click="confirmScan"
                                wire:loading.attr="disabled"
                            >
                                {{ __('Scan and save results') }}
                            </flux:button>
                        </flux:button.group>
                    @endif
                </div>
            @endif
        </div>
    @endif
</flux:modal>
