<div
    class="relative space-y-4"
    x-data="{
        applying: false,
        async confirmBatch() {
            if (this.applying) return;
            this.applying = true;
            const dialog = this.$el.closest('dialog');
            dialog.dataset.awaitingReplacementResult = '';
            dialog.addEventListener('close', () => delete dialog.dataset.awaitingReplacementResult, { once: true });
            try {
                await this.$nextTick();
                await this.$wire.applySource();
            } finally { this.applying = false }
        }
    }"
>
    <x-ui.loading.lazy-indicator
        role="status"
        style="display: none"
        x-show="applying"
        :overlay="true"
        :text="__('Applying selected source replacements…')"
    />
    <flux:heading
        class="flex items-center gap-2"
        size="xl"
    >
        <flux:icon.eye class="size-8 shrink-0" />
        {{ __('Confirm / resume selected source replacements') }}
    </flux:heading>

    <flux:callout
        color="sky"
        icon="key"
        :heading="__('Key #:key · :count findings',
            [
                'key' => $sourceRun['preview']['key_id'],
                'count' => count($sourceRun['preview']['items'])
            ])"
    >
        <flux:callout.text>{{ $sourceRun['reason'] }}</flux:callout.text>
        <flux:callout.text>
            {{ __('Each file is written separately. Completed files remain completed if a later file is blocked. Resume continues the unfinished operations; it does not repeat completed replacements.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="sky"
        heading="{{ __('Confirm / resume selected source replacements ') }}"
        icon="equal-approximately"
    >
        {{-- Table Confirm / Resume Selected Source Replacements  --}}
        <flux:table container:class="mt-3 max-h-96 overflow-auto rounded-t-lg">
            {{-- Table Header Columns --}}
            <flux:table.columns
                class="bg-white dark:bg-zinc-800"
                sticky
            >
                {{-- Table Header Column Source File --}}
                <flux:table.column>
                    <flux:callout.heading class="ml-2">
                        {{ __('translation-workbench.literal.source_file') }}
                    </flux:callout.heading>
                </flux:table.column>
                {{-- Table Header Column Status --}}
                <flux:table.column>
                    <flux:callout.heading>
                        {{ __('Status') }}
                    </flux:callout.heading>
                </flux:table.column>
                {{-- Table Header Column Actions --}}
                <flux:table.column align="center">
                    <flux:callout.heading>
                        {{ __('Actions') }}
                    </flux:callout.heading>
                </flux:table.column>
            </flux:table.columns>
            {{-- Table Body Rows --}}
            <flux:table.rows>
                @foreach ($sourceRun['files'] as $file)
                    {{-- Table Body Row --}}
                    <flux:table.row :key="$file['run']">
                        {{-- Table Body Column Source File --}}
                        <flux:table.cell>
                            <x-translation-workbench::ui.common.path-preview
                                class="ml-2"
                                :path="$file['path']"
                                :length="60"
                                side="center"
                            />
                        </flux:table.cell>
                        {{-- Table Body Column Status --}}
                        <flux:table.cell>
                            <flux:badge
                                size="sm"
                                icon="hourglass"
                                :color="$file['state'] === 'complete' ? 'green' : ($file['file_state'] === 'original' ?
                                    'amber' :
                                    'red')"
                                :label="__($file['state'])"
                            />
                            <flux:badge
                                size="sm"
                                icon="origami"
                                :color="$file['file_state'] === 'original' ? 'blue' : 'gray'"
                                :label="__($file['file_state'])"
                            />
                            @if (!empty($file['error']))
                                <flux:callout.text>
                                    {{ $file['error'] }}
                                </flux:callout.text>
                            @endif
                        </flux:table.cell>
                        {{-- Table Body Column Actions --}}
                        <flux:table.cell>
                            <flux:button.group class="mr-3">
                                <flux:button
                                    size="xs"
                                    variant="filled"
                                    color="sky"
                                    square
                                    wire:click="openSourceRun('{{ $file['run'] }}')"
                                >
                                    <flux:icon.scan-search class="size-5" />
                                </flux:button>
                                <flux:button
                                    size="xs"
                                    variant="filled"
                                    color="sky"
                                    wire:click="openSourceRun('{{ $file['run'] }}')"
                                >
                                    {{ __('Inspect file operation') }}
                                </flux:button>
                            </flux:button.group>
                        </flux:table.cell>
                    </flux:table.row>
                    @foreach ($file['impacts'] as $impact)
                        {{-- Table Body Row --}}
                        <flux:table.row>
                            {{-- Table Body Row Column Details --}}
                            <flux:table.cell colspan="3">
                                {{-- <flux:callout.text class="ml-2 items-center tabular-nums">
                                    #{{ $impact['finding'] }} ·
                                    {{ __($impact['kind']) }}
                                    ·
                                    {{ $impact['before_line'] }}:{{ $impact['before_column'] }}
                                    <flux:icon.arrow-right class="mx-2 inline size-4" />
                                    {{ $impact['after_line'] }}:{{ $impact['after_column'] }} · {{ $impact['key'] }}
                                </flux:callout.text> --}}
                                {{-- IST --}}
                                {{-- <flux:callout.text class="grid grid-cols-8 items-center gap-x-2 whitespace-nowrap">
                                    <span>#{{ $impact['finding'] }}</span>
                                    <span class="col-span-2">{{ __($impact['kind']) }}</span>
                                    <span>{{ $impact['before_line'] }}:{{ $impact['before_column'] }}</span>
                                    <flux:icon.arrow-right class="mx-2 inline size-4" />
                                    <span class="col-span-2">{{ $impact['key'] }}</span>
                                    <span>{{ $impact['after_line'] }}:{{ $impact['after_column'] }}</span>
                                </flux:callout.text> --}}

                                <flux:callout.text
                                    class="ml-2 mr-3 grid grid-cols-[minmax(0,12ch)_minmax(0,1fr)_minmax(0,8ch)_minmax(0,2rem)_minmax(0,1fr)_minmax(0,8ch)] items-center gap-x-2 whitespace-nowrap"
                                >
                                    <span>#{{ $impact['finding'] }}</span>
                                    <span>{{ __($impact['kind']) }}</span>
                                    <span>{{ $impact['before_line'] }}:{{ $impact['before_column'] }}</span>
                                    <flux:icon.arrow-right class="mx-2 inline size-4" />
                                    <span class="font-mono">{{ $impact['key'] }}</span>
                                    <span>{{ $impact['after_line'] }}:{{ $impact['after_column'] }}</span>
                                </flux:callout.text>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:callout>

    <flux:error name="sourceRun" />

    @if ($sourceRun['state'] === 'pending')
        @if ((int) $sourceRun['actor'] === (int) auth()->id())
            <div class="flex items-center justify-end">
                <flux:button.group>
                    <flux:button
                        size="sm"
                        variant="filled"
                        color="green"
                        square
                        x-on:click="confirmBatch()"
                        x-bind:disabled="applying"
                    >
                        <flux:icon.replace class="size-5" />
                    </flux:button>
                    <flux:button
                        size="sm"
                        variant="filled"
                        color="green"
                        x-on:click="confirmBatch()"
                        x-bind:disabled="applying"
                    >
                        <span x-show="!applying">{{ __('Confirm selected replacements / resume') }}</span>
                        <span
                            x-cloak
                            x-show="applying"
                        >{{ __('Applying selected source replacements…') }}</span>
                    </flux:button>
                </flux:button.group>
            </div>
        @endif

        <flux:accordion>
            <flux:callout
                color="green"
                icon="stamp"
            >
                <flux:accordion.item>

                    <flux:accordion.heading>
                        <flux:callout.heading>
                            {{ __('Close unfinished file operations') }}
                        </flux:callout.heading>
                    </flux:accordion.heading>
                    <flux:accordion.content>
                        <flux:callout.text>
                            {{ __('Only unfinished operations whose source files are still unchanged can be closed. Completed files are not reverted.') }}
                        </flux:callout.text>
                        @include(
                            'translation-workbench::livewire.translation-workbench.shared.reason-select',
                            [
                                'reasonField' => 'sourceCloseReason',
                                'reasonLabel' => __('Reason for closure'),
                            ]
                        )
                        <div class="mt-4 flex items-center justify-end">
                            <flux:button.group>
                                <flux:button
                                    size="sm"
                                    square
                                    variant="filled"
                                    color="green"
                                    wire:click="closeSource"
                                    wire:loading.attr="disabled"
                                >
                                    <flux:icon.circle-check-big class="size-5" />
                                </flux:button>
                                <flux:button
                                    size="sm"
                                    variant="filled"
                                    color="green"
                                    wire:click="closeSource"
                                    wire:loading.attr="disabled"
                                >
                                    {{ __('Confirm closure') }}
                                </flux:button>
                            </flux:button.group>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:callout>
        </flux:accordion>
    @elseif ($sourceRun['state'] === 'complete')
        <flux:callout
            color="green"
            icon="check-circle"
            :heading="__('All selected source replacements completed')"
            :text="__('Run a new scan to confirm the updated source positions.')"
            inline
        >
            <x-slot:actions>
                @if ((int) $sourceRun['actor'] === (int) auth()->id())
                    <flux:button size="sm" variant="filled" color="green" icon="check"
                        wire:click="acknowledgeSourceBatch" wire:loading.attr="disabled" wire:target="acknowledgeSourceBatch">
                        {{ __('Confirm') }}
                    </flux:button>
                @endif
            </x-slot:actions>
        </flux:callout>
    @else
        @php
            $fileCounts = array_count_values(array_column($sourceRun['files'], 'state'));
            $completedFiles = $fileCounts['complete'] ?? 0;
        @endphp
        <flux:callout
            color="amber"
            icon="archive-box"
            :heading="$completedFiles > 0
                ? __('Source replacements partially completed')
                : __('Source replacements closed without completion')"
            :text="__('Completed files: :completed · Closed file operations: :closed', [
                'completed' => $completedFiles,
                'closed' => $fileCounts['closed'] ?? 0,
            ])"
        />
    @endif
</div>
