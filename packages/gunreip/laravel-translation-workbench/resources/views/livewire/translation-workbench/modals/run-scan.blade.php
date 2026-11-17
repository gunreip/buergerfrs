<flux:modal
    class="w-full max-w-5xl"
    name="workbench-run-scan"
    wire:model.live.self="scanWorkflowOpen"
>
    <x-translation-workbench::ui.common.modal-loading
        target="refreshScanStorage, showWorkflowScanResult, startScanPreview, startScanAndSave, confirmScan"
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
            @if (($scanAction['reason'] ?? '') !== '' && !($scanAction['reusable'] ?? false))
                <flux:text>{{ $scanAction['reason'] }}</flux:text>
            @endif

            <flux:callout
                color="sky"
                icon="arrow-path"
            >
                <flux:accordion transition>
                    <flux:accordion.item>
                        <flux:accordion.heading>
                            <flux:callout.heading>
                                {{ __('Scan scope') }}
                            </flux:callout.heading>
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
            >
                <flux:accordion transition>
                    <flux:accordion.item>
                        <flux:accordion.heading>
                            <flux:callout.heading>
                                {{ __('Scan storage') }}
                            </flux:callout.heading>
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
                                        square
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
                $counts = $scanWorkflow['counts'] ?? $scanWorkflow['preview']['counts'] ?? null;
                $saveAction = in_array($scanState, ['preview_ready', 'changed'], true) ? 'confirmScan' : 'startScanAndSave';
                $scanRunning = in_array($scanState, ['queued_preview', 'scanning', 'queued_apply', 'applying'], true);
            @endphp

            @if ($scanRunning)
                <livewire:translation-workbench.scan-progress
                    :scan-workflow-id="$scanWorkflowId"
                    :scan-workflow="[
                        'state' => $scanState,
                        'progress' => ['steps' => $scanWorkflow['progress']['steps'] ?? []],
                    ]"
                    :key="'scan-progress-' . $scanWorkflowId"
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
            @if ($counts !== null)
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
                                        <x-ui.number :value="$counts[$count]" />
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
            @if (
                $scanPositionFinding &&
                    in_array($scanState, ['preview_ready', 'changed'], true) &&
                    !in_array($scanPositionPath, $scanWorkflow['preview']['position_files'] ?? [], true))
                <flux:callout
                    color="amber"
                    :text="__('This preview lists no positions requiring review for :path (finding #:id).', ['path' =>
                        $scanPositionPath, 'id' => $scanPositionFinding
                    ])"
                />
            @endif
            @if (in_array($scanState, ['preview_ready', 'changed'], true) && !empty($scanWorkflow['preview']['position_files']))
                <flux:callout
                    class="[&>.flex-1]:min-w-0"
                    icon="file-search-corner"
                    color="amber"
                    :heading="__('Position preview for files requiring review')"
                >
                    <flux:callout.text>
                        {{ __('Saving the scan applies unique moves when their destinations are available. Ambiguous findings and moves that depend on them remain open for review.') }}
                    </flux:callout.text>
                    <flux:field class="mx-3 w-full min-w-0">
                        <flux:label>
                            <flux:callout.heading>
                                {{ __('translation-workbench.literal.source_file') }}
                            </flux:callout.heading>
                        </flux:label>
                        <flux:input.group>
                            <flux:input.group.prefix>
                                <flux:icon.file-search-corner class="size-5" />
                            </flux:input.group.prefix>
                            <flux:select
                                class="truncate"
                                variant="listbox"
                                searchable
                                wire:model.live="scanPositionPath"
                                :placeholder="__('Choose a file')"
                            >
                                @foreach ($scanWorkflow['preview']['position_files'] as $path)
                                    <flux:select.option
                                        :value="$path"
                                        icon="file"
                                    >
                                        {{ $path }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:input.group>
                    </flux:field>

                    @if ($scanPositionRows ?? null)
                        <flux:table container:class="max-h-60 overflow-auto rounded-t-lg">
                            <flux:table.columns
                                class="bg-white dark:bg-zinc-800"
                                sticky
                            >
                                <flux:table.column>
                                    <flux:callout.heading class="ml-2">
                                        {{ __('Finding ID') }}
                                    </flux:callout.heading>
                                </flux:table.column>
                                <flux:table.column>
                                    <flux:callout.heading>
                                        {{ __('Captured position') }}
                                    </flux:callout.heading>
                                </flux:table.column>
                                <flux:table.column>
                                    <flux:callout.heading>
                                        {{ __('Scan position preview') }}
                                    </flux:callout.heading>
                                </flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach ($scanPositionRows as $id => $observation)
                                    <flux:table.row :key="'scan-position-'.$id">
                                        <flux:table.cell>
                                            @if ((int) $id === $scanPositionFinding)
                                                <flux:badge
                                                    class="ml-2"
                                                    size="sm"
                                                    color="sky"
                                                >
                                                    #{{ $id }}
                                                </flux:badge>
                                            @else
                                                <flux:callout.text class="ml-2 tabular-nums">
                                                    #{{ $id }}
                                                </flux:callout.text>
                                            @endif
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:callout.text>
                                                {{ $observation['from'] }}
                                            </flux:callout.text>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:callout.text>
                                                @include(
                                                    'translation-workbench::livewire.translation-workbench.scan.position-status',
                                                    [
                                                        'state' => $observation['state'],
                                                        'destination' => $observation['to'] ?? null,
                                                        'reason' => $observation['reason'] ?? null,
                                                    ]
                                                )
                                            </flux:callout.text>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>

                        <flux:pagination :paginator="$scanPositionRows" />

                    @endif
                </flux:callout>
            @endif

            @if ($scanState === 'complete')
                <flux:callout
                    inline
                    class="justify-stretch hyphens-auto"
                    color="green"
                    icon="database-plus"
                    :heading="__('Scan saved')"
                    :text="__('The overview and scan results have been refreshed. Review unresolved cases in Scan results before continuing source replacement.')"
                >
                    <x-slot:actions class="@md:self-center">
                        <flux:button.group>
                            <flux:button
                                size="sm"
                                variant="filled"
                                square
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
                    </x-slot:actions>
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

            @if (!in_array($scanState, ['queued_preview', 'scanning', 'queued_apply', 'applying', 'complete'], true) && $testToken === '')
                <div class="flex justify-end gap-2">
                    <flux:button.group>
                        <flux:button
                            variant="filled"
                            square
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
                    <flux:button.group>
                        <flux:button
                            variant="filled"
                            square
                            color="green"
                            size="sm"
                            wire:click="{{ $saveAction }}"
                            wire:loading.attr="disabled"
                        >
                            <flux:icon.database-plus size="5" />
                        </flux:button>
                        <flux:button
                            variant="filled"
                            color="green"
                            size="sm"
                            wire:click="{{ $saveAction }}"
                            wire:loading.attr="disabled"
                        >
                            {{ __('Save scan results to database') }}
                        </flux:button>
                    </flux:button.group>
                </div>
            @endif
        </div>
    @endif
</flux:modal>
