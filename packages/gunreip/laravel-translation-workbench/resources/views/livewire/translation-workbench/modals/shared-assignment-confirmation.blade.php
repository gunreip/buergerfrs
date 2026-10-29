<flux:modal
    class="w-full max-w-6xl"
    name="workbench-shared-assignment-confirmation"
    wire:model="sharedConfirmOpen"
>
    @if ($sharedAssignmentPlan !== [])
        <div class="space-y-4">
            <flux:heading size="lg">
                {{ __('Confirm shared key assignment') }}
            </flux:heading>
            <flux:text>
                {{ __('Assign :count selected findings to translation key #:id.', ['count' => count($sharedAssignmentPlan['findings']), 'id' => $sharedAssignmentPlan['reference']['key_id']]) }}
            </flux:text>
            <flux:callout
                color="sky"
                icon="key-square"
                heading="Actually assigned key"
            >
                <flux:callout.text class="break-all">
                    {{ !empty($sharedAssignmentPlan['reference']['namespace']) ? $sharedAssignmentPlan['reference']['namespace'] . '::' : '' }}{{ $sharedAssignmentPlan['reference']['full_name'] }}
                </flux:callout.text>
            </flux:callout>
            <flux:callout
                color="amber"
                icon="key"
                :text="__('Existing different key assignments will be replaced. Translation values are not copied or merged. Captures and suggested keys remain unchanged. Publication and source files are not changed.')"
            />
            <flux:callout
                color="red"
                heading="{{ __('Selected findings') }}"
                icon="key"
            >
                {{-- Table --}}
                <flux:table container:class="max-h-[80vh] overflow-x-auto rounded-t-lg mt-3">
                    {{-- Table Header --}}
                    <flux:table.columns class="bg-white dark:bg-zinc-800">
                        {{-- Column Finding --}}
                        <flux:table.column align="center">
                            <flux:tooltip
                                content="{{ __('Finding ID') }}"
                                toggleable
                            >
                                <flux:button
                                    size="xs"
                                    color="red"
                                    variant="ghost"
                                    icon="search-code"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Translation key to assign --}}
                        <flux:table.column align="center">
                            <flux:tooltip
                                content="{{ __('Assigned translation key') }}"
                                toggleable
                            >
                                <flux:button
                                    size="xs"
                                    color="red"
                                    variant="ghost"
                                    icon="globe-code"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Key to assign --}}
                        <flux:table.column>
                            <flux:callout.heading>
                                {{ __('Key to assign') }}
                            </flux:callout.heading>
                        </flux:table.column>
                        {{-- Column Source --}}
                        <flux:table.column>
                            <flux:callout.heading>
                                {{ __('Source') }}
                            </flux:callout.heading>
                        </flux:table.column>
                    </flux:table.columns>
                    {{-- Table Rows --}}
                    <flux:table.rows>
                        @foreach ($sharedAssignmentPlan['findings'] as $item)
                            {{-- Table Row --}}
                            <flux:table.row :key="'shared-assignment-'.$item['id']">
                                {{-- Column Finding ID --}}
                                <flux:table.cell align="end">
                                    <flux:callout.text class="tabular-nums">
                                        #{{ $item['id'] }}
                                    </flux:callout.text>
                                </flux:table.cell>
                                {{-- Column Key To Assign --}}
                                <flux:table.cell align="end">
                                    <flux:callout.text>
                                        #{{ $sharedAssignmentPlan['reference']['key_id'] }}
                                    </flux:callout.text>
                                </flux:table.cell>
                                {{-- Column Key To Assign --}}
                                <flux:table.cell class="max-w-sm whitespace-normal break-all">
                                    <flux:callout.text class="items-center">
                                        <div class="flex items-start gap-2">
                                            <span class="min-w-0">
                                                {{ !empty($sharedAssignmentPlan['reference']['namespace']) ? $sharedAssignmentPlan['reference']['namespace'] . '::' : '' }}{{ $sharedAssignmentPlan['reference']['full_name'] }}
                                                @if ((int) $item['key_id'] === (int) $sharedAssignmentPlan['reference']['key_id'])
                                                    <flux:badge size="sm" color="green">{{ __('Already assigned — unchanged') }}</flux:badge>
                                                @endif
                                            </span>
                                            <flux:tooltip
                                                class="justify-end"
                                                toggleable
                                            >
                                                <flux:button
                                                    class="shrink-0"
                                                    size="xs"
                                                    variant="ghost"
                                                    icon="info"
                                                    :aria-label="__('Previous translation key')"
                                                />
                                                <x-translation-workbench::ui.common.tooltip-content :heading="__('Previous key')">
                                                    <span class="break-all">
                                                        @if ($item['key_id'])
                                                            #{{ $item['key_id'] }} ·
                                                            {{ $item['namespace'] ? $item['namespace'] . '::' : '' }}{{ $item['full_name'] }}
                                                        @else
                                                            {{ __('No assigned translation key before.') }}
                                                        @endif
                                                    </span>
                                                </x-translation-workbench::ui.common.tooltip-content>
                                            </flux:tooltip>
                                        </div>
                                    </flux:callout.text>
                                </flux:table.cell>
                                <flux:table.cell class="max-w-sm whitespace-normal break-all">
                                    <flux:callout.text>
                                        {{ $item['path'] }}:{{ $item['source_line'] }}:{{ $item['source_column'] }}
                                    </flux:callout.text>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:callout>

            <flux:callout
                color="green"
                icon="leaf"
            >
                <flux:callout.heading>
                    Reason:
                    <flux:callout.text>
                        {{ $sharedConfirmedReason }}
                    </flux:callout.text>
                </flux:callout.heading>
            </flux:callout>
            <flux:error name="sharedAssignment" />
            <div class="flex justify-end gap-2">
                <flux:button.group>
                    <flux:button
                        size="sm"
                        icon="check"
                        variant="filled"
                        color="green"
                        wire:click="assignSharedFindings"
                        wire:loading.attr="disabled"
                        wire:target="assignSharedFindings"
                    />
                    <flux:button
                        size="sm"
                        variant="filled"
                        color="green"
                        wire:click="assignSharedFindings"
                        wire:loading.attr="disabled"
                        wire:target="assignSharedFindings"
                    >
                        {{ __('Confirm assignment') }}
                    </flux:button>
                </flux:button.group>
            </div>
        </div>
    @endif
</flux:modal>
