<flux:modal
    class="w-full max-w-7xl"
    name="generation-source-run"
    wire:model="sourceRunOpen"
>
    <x-translation-workbench::ui.common.modal-loading target="applySource, closeSource, open, openSourceRun" />
    @if ($sourceRun['batch'] ?? false)
        @include('translation-workbench::livewire.translation-workbench.shared.source-batch-run')
    @elseif ($sourceRun)
        <div class="space-y-4">
            <flux:heading
                class="flex items-center gap-2"
                size="xl"
            >
                <flux:icon.eye class="shrink-0" />
                {{ __('Confirm / resume source replacement') }}
            </flux:heading>

            <flux:callout
                color="sky"
                icon="file-search-corner"
                heading="{{ __('Source') }}"
            >
                <x-translation-workbench::ui.common.details>
                    <x-translation-workbench::ui.common.details.item :label="__('Source file')">
                        <x-translation-workbench::ui.common.path-preview
                            :path="$sourceRun['preview']['path']"
                            :length="60"
                            side="center"
                            :heading="__('Source file')"
                        />
                    </x-translation-workbench::ui.common.details.item>
                    <x-translation-workbench::ui.common.details.item
                        class="tabular-nums"
                        :label="__('Finding ID')"
                        :item="'#' . $sourceRun['preview']['finding']"
                    />
                    <x-translation-workbench::ui.common.details.item
                        class="tabular-nums"
                        :label="__('Assigned key ID')"
                        :item="'#' . $sourceRun['preview']['key_id']"
                    />
                    <x-translation-workbench::ui.common.details.item :label="__('Assigned translation key')">
                        <x-translation-workbench::ui.common.text-preview
                            :text="$sourceRun['preview']['key']"
                            :mono="true"
                            side="center"
                            :heading="__('Assigned translation key')"
                        />
                    </x-translation-workbench::ui.common.details.item>
                    @php
                        [$replacementStatus, $replacementExplanation] = match ($sourceRun['state']) {
                            'pending' => [__('Prepared'), __('Awaiting confirmation or resumption')],
                            'complete' => [__('Completed'), __('Source replacement and database update finished')],
                            'closed' => [__('Closed'), __('This operation can no longer be applied')],
                            default => [__($sourceRun['state']), null],
                        };
                        [$sourceFileStatus, $sourceFileExplanation] = match ($sourceRun['file_state']) {
                            'original' => [
                                __('Unchanged'),
                                __('Matches the source captured when preparing this operation'),
                            ],
                            'written' => [__('Replacement present'), __('Matches the planned source contents')],
                            'changed' => [
                                __('Changed'),
                                __('Matches neither the original nor the planned source contents'),
                            ],
                            default => [__($sourceRun['file_state']), null],
                        };
                    @endphp
                    <x-translation-workbench::ui.common.details.item :label="__('Replacement status')">
                        <div class="flex items-baseline gap-2">
                            <flux:callout.heading class="shrink-0">{{ $replacementStatus }}</flux:callout.heading>
                            @if ($replacementExplanation)
                                <flux:callout.text>— {{ $replacementExplanation }}</flux:callout.text>
                            @endif
                        </div>
                    </x-translation-workbench::ui.common.details.item>
                    <x-translation-workbench::ui.common.details.item :label="__('Source file status')">
                        <div class="flex items-baseline gap-2">
                            <flux:callout.heading class="shrink-0">{{ $sourceFileStatus }}</flux:callout.heading>
                            @if ($sourceFileExplanation)
                                <flux:callout.text>— {{ $sourceFileExplanation }}</flux:callout.text>
                            @endif
                        </div>
                    </x-translation-workbench::ui.common.details.item>
                    <x-translation-workbench::ui.common.details.item
                        :label="__('Reason for replacement')"
                        :item="$sourceRun['reason']"
                    />
                    <x-translation-workbench::ui.common.details.item :label="__('Replacement operation ID')">
                        <x-translation-workbench::ui.common.text-preview
                            :text="$sourceRun['run']"
                            :mono="true"
                            :heading="__('Replacement operation ID')"
                        />
                    </x-translation-workbench::ui.common.details.item>
                </x-translation-workbench::ui.common.details>
            </flux:callout>

            @if ($sourceRun['impacts'] ?? [])
                <flux:callout
                    color="amber"
                    icon="exclamation-triangle"
                    heading="{{ __('Impacts') }}"
                >
                    <flux:callout.heading>
                        {{ __('Affected findings') }}
                    </flux:callout.heading>
                    <flux:callout.text>
                        {{ __('Before and After show the line number followed by the column number, separated by a colon (for example, 24:50). Columns are counted in bytes. Each finding retains its assigned translation key. Findings not listed here remain unchanged.') }}
                    </flux:callout.text>
                    <div class="max-h-64 overflow-auto">
                        {{-- Table --}}
                        <flux:table container:class="rounded-t-lg overflow-auto max-h-90">
                            {{-- Table Header Row --}}
                            <flux:table.columns class="bg-white dark:bg-zinc-800">
                                {{-- Table Header Column Finding --}}
                                <flux:table.column>
                                    <flux:callout.heading class="ml-2">
                                        {{ __('Finding') }}
                                    </flux:callout.heading>
                                </flux:table.column>
                                {{-- Table Header Column Change --}}
                                <flux:table.column>
                                    <flux:callout.heading>
                                        {{ __('Change') }}
                                    </flux:callout.heading>
                                </flux:table.column>
                                {{-- Table Header Column Before --}}
                                <flux:table.column>
                                    <flux:callout.heading>
                                        {{ __('Before') }}
                                    </flux:callout.heading>
                                </flux:table.column>
                                {{-- Table Header Column After --}}
                                <flux:table.column>
                                    <flux:callout.heading>
                                        {{ __('After') }}
                                    </flux:callout.heading>
                                </flux:table.column>
                                {{-- Table Header Column Translation key --}}
                                <flux:table.column>
                                    <flux:callout.heading>
                                        {{ __('Translation key') }}
                                    </flux:callout.heading>
                                </flux:table.column>
                            </flux:table.columns>
                            {{-- Table Rows --}}
                            <flux:table.rows>
                                @foreach ($sourceRun['impacts'] as $impact)
                                    <flux:table.row :key="'source-impact-'.$impact['finding']">
                                        <flux:table.cell>
                                            <flux:callout.text class="ml-2">
                                                #{{ $impact['finding'] }}
                                            </flux:callout.text>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:callout.text>
                                                {{ __($impact['kind']) }}
                                            </flux:callout.text>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:callout.text>
                                                {{ $impact['before_line'] }}:{{ $impact['before_column'] }}
                                            </flux:callout.text>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:callout.text>
                                                {{ $impact['after_line'] }}:{{ $impact['after_column'] }}
                                            </flux:callout.text>
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-normal break-all">
                                            <flux:callout.text class="mr-3">
                                                {{ $impact['key_id'] ? '#' . $impact['key_id'] . ' · ' . $impact['key'] : __('Not assigned') }}
                                            </flux:callout.text>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>
                </flux:callout>
            @endif

            <flux:callout
                class="min-w-0 [&>div]:min-w-0"
                icon="code"
                color="teal"
                :heading="__('Source call comparison')"
            >
                <x-translation-workbench::ui.common.details>
                    <x-translation-workbench::ui.common.details.item :label="__('Before')">
                        <x-translation-workbench::ui.common.source-code :text="$sourceRun['preview']['before']" />
                    </x-translation-workbench::ui.common.details.item>
                    <x-translation-workbench::ui.common.details.item :label="__('After')">
                        <x-translation-workbench::ui.common.source-code :text="$sourceRun['preview']['after']" />
                    </x-translation-workbench::ui.common.details.item>
                </x-translation-workbench::ui.common.details>
            </flux:callout>

            <flux:error name="sourceRun" />

            @if ($sourceRun['state'] === 'pending')
                <flux:text class="justify-stretch hyphens-auto">
                    {{ __('If the file has already been written, resuming only completes the database receipt and successor finding. A changed file or assignment stops the operation.') }}
                </flux:text>
                @if ((int) $sourceRun['actor'] === (int) auth()->id())
                    <div class="mb-4 flex items-end justify-end">
                        <flux:button.group>
                            <flux:button
                                variant="filled"
                                color="green"
                                size="sm"
                                wire:click="applySource"
                            >
                                <flux:icon.check-check class="size-5" />
                            </flux:button>
                            <flux:button
                                variant="filled"
                                color="green"
                                size="sm"
                                wire:click="applySource"
                            >
                                {{ __('Confirm source replacement / resume') }}
                            </flux:button>
                        </flux:button.group>
                    </div>
                @endif

                <flux:callout color="red">
                    <flux:accordion>
                        <flux:accordion.item>
                            <flux:accordion.heading>
                                <flux:icon.shield-alert class="mr-1 inline size-5" />
                                {{ __('Close without changing the source') }}
                            </flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:callout.text>
                                    {{ __('Closing is allowed only while the file still matches its original contents. Already written files must be reconciled instead.') }}
                                </flux:callout.text>
                                @include(
                                    'translation-workbench::livewire.translation-workbench.shared.reason-select',
                                    [
                                        'reasonField' => 'sourceCloseReason',
                                        'reasonLabel' => __('Reason for closure'),
                                    ]
                                )
                                <div class="mb-4 flex items-end justify-end">
                                    <flux:button.group>
                                        <flux:button
                                            variant="outline"
                                            wire:click="closeSource"
                                        >
                                            <flux:icon.x-mark class="size-5" />
                                        </flux:button>
                                        <flux:button
                                            variant="outline"
                                            wire:click="closeSource"
                                        >
                                            {{ __('Confirm closure') }}
                                        </flux:button>
                                    </flux:button.group>
                                </div>
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:callout>
            @elseif ($sourceRun['state'] === 'complete')
                <flux:callout
                    color="green"
                    icon="database-check"
                    heading="{{ __('Source replacement and successor capture completed.') }}"
                >
                    @foreach ($sourceRun['result']['successors'] as $successor)
                        <div class="mb-4 flex items-end justify-end">
                            <flux:button.group>
                                <flux:button
                                    variant="filled"
                                    color="sky"
                                    disabled
                                    size="sm"
                                    {{-- wire:click="open({{ $successor['successor'] }})" --}}
                                >
                                    #{{ $successor['predecessor'] }}
                                </flux:button>
                                <flux:button
                                    variant="filled"
                                    color="fuchsia"
                                    size="sm"
                                    wire:click="open({{ $successor['successor'] }})"
                                >
                                    <flux:icon.arrow-right-from-line class="size-5" />
                                </flux:button>
                                <flux:button
                                    variant="filled"
                                    color="fuchsia"
                                    size="sm"
                                    wire:click="open({{ $successor['successor'] }})"
                                >
                                    {{-- #{{ $successor['predecessor'] }} → #{{ $successor['successor'] }} --}}
                                    #{{ $successor['successor'] }}
                                </flux:button>
                            </flux:button.group>
                        </div>
                    @endforeach
                </flux:callout>
            @else
                <flux:text>
                    {{ __('This operation is closed and cannot write the source.') }}
                </flux:text>
            @endif
        </div>
    @endif
</flux:modal>
