<flux:modal
    class="w-full max-w-6xl"
    name="workbench-shared-findings"
    wire:model="sharedOpen"
>
    @if ($sharedOpen && $sharedRows)
        <div class="relative space-y-4">
            <x-ui.loading.lazy-indicator
                class="absolute inset-0 z-40"
                target="gotoPage, nextPage, previousPage, setPage, reviewSharedAssignment, assignSharedFindings, toggleSharedPageSelection"
                :text="__('Loading')"
            />
            <flux:heading size="xl">
                {{ __('Shared / UI candidate') }} · #{{ $sharedFinding }}
            </flux:heading>
            <flux:text>
                {{ __('Active findings with exactly the same captured literal or the same assigned key, including the current finding. Select which findings should use the target key. Unselected findings keep their existing assignments.') }}
            </flux:text>
            <flux:callout
                color="sky"
                icon="scroll-text"
            >
                <flux:callout.heading>
                    {{ __('Captured literal') }}
                </flux:callout.heading>
                <flux:callout>
                    <flux:callout.text class="wrap-break-word whitespace-normal">
                        {!! nl2br(e(trim($sharedReference['literal'] ?? __('No captured literal available.')))) !!}
                    </flux:callout.text>
                </flux:callout>
            </flux:callout>

            @if (($sharedReference['status'] ?? null) === 'active' && !empty($sharedReference['key_id']))
                <flux:callout
                    color="green"
                    icon="key"
                >
                    <flux:callout.heading>
                        {{ __('Shared target key') }} · #{{ $sharedReference['key_id'] }}
                    </flux:callout.heading>
                    <flux:callout>
                        <flux:callout.text class="break-all">
                            {{ !empty($sharedReference['namespace']) ? $sharedReference['namespace'] . '::' : '' }}{{ $sharedReference['full_name'] ?? '' }}
                        </flux:callout.text>
                    </flux:callout>
                    <flux:callout.text>
                        {{ __('This is the saved key assignment of the current finding. Save a different key assignment in the editor first if needed.') }}
                    </flux:callout.text>
                </flux:callout>
            @else
                <flux:callout
                    color="amber"
                    :text="__('Assign a key to the active reference finding first.')"
                />
            @endif

            @if ($sharedNotice !== '')
                <flux:callout
                    color="green"
                    :text="$sharedNotice"
                />
            @endif

            <flux:error name="sharedAssignment" />
            <flux:error name="sharedSelection" />

            <flux:callout color="amber">
                {{-- Table of shared findings: --}}
                <flux:table container:class="max-h-[60vh] overflow-x-auto rounded-t-lg">
                    {{-- Table Header --}}
                    <flux:table.columns class="bg-white dark:bg-zinc-800">
                        {{-- Column Select --}}
                        <flux:table.column>
                            <span class="ml-2">{{ __('Select') }}</span>
                        </flux:table.column>
                        <flux:table.column>
                            {{ __('Finding') }}
                        </flux:table.column>
                        <flux:table.column>
                            {{ __('Literal') }}
                        </flux:table.column>
                        <flux:table.column>
                            {{ __('Source') }}
                        </flux:table.column>
                        <flux:table.column>
                            {{ __('Suggested key') }}
                        </flux:table.column>
                        <flux:table.column>
                            {{ __('Translation key / Assignment') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($sharedRows as $match)
                            <flux:table.row :key="'shared-finding-'.$match['id']">
                                <flux:table.cell>
                                    <div class="ml-2 flex items-center justify-center gap-2">
                                        <flux:checkbox
                                            wire:model.live="sharedSelection"
                                            :value="(string) $match['id']"
                                            :disabled="!in_array((string) $match['id'], $selectableSharedIds, true)"
                                            :aria-label="__('Select finding #:id', ['id' => $match['id']])"
                                        />
                                        @if ($loop->first)
                                            <flux:button
                                                size="xs"
                                                variant="ghost"
                                                icon="list-checks"
                                                :disabled="$selectableSharedIds === []"
                                                :tooltip="__('Select all eligible findings on this page; click again to clear this page.')"
                                                :aria-label="__('Toggle selection of all eligible findings on this page')"
                                                wire:click="toggleSharedPageSelection"
                                                wire:loading.attr="disabled"
                                                wire:target="toggleSharedPageSelection"
                                            />
                                        @endif
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    #{{ $match['id'] }}
                                    @if ((int) $match['id'] === $sharedFinding)
                                        <div class="mt-1"><flux:badge size="sm" color="sky">{{ __('Current finding') }}</flux:badge></div>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word max-w-sm whitespace-normal">
                                    {{ $match['literal'] ?? '—' }}</flux:table.cell>
                                <flux:table.cell class="max-w-md whitespace-normal break-all">
                                    {{ $match['path'] }}:{{ $match['source_line'] }}:{{ $match['source_column'] }}
                                </flux:table.cell>
                                <flux:table.cell class="max-w-sm whitespace-normal break-all">
                                    {{ $match['suggested_key'] ?? '—' }}</flux:table.cell>
                                <flux:table.cell class="max-w-sm whitespace-normal break-all">
                                    @if ($match['key_id'])
                                        #{{ $match['key_id'] }} · {{ $match['full_name'] }}
                                        <div class="mt-1">
                                            @if ($match['key_id'] == ($sharedReference['key_id'] ?? null))
                                                <flux:badge
                                                    color="green"
                                                    size="sm"
                                                >{{ __('Same assigned key') }}</flux:badge>
                                            @else
                                                <flux:badge size="sm">{{ __('Different assigned key') }}
                                                </flux:badge>
                                            @endif
                                        </div>
                                    @else
                                        <flux:badge
                                            color="amber"
                                            size="sm"
                                        >{{ __('Not assigned') }}</flux:badge>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6">{{ __('No matching active findings.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:callout>

            <flux:pagination :paginator="$sharedRows" />

            @if (($sharedReference['status'] ?? null) === 'active' && !empty($sharedReference['key_id']))
                <flux:field>
                    <div class="flex h-6 items-center gap-3">
                        <flux:label class="shrink-0">
                            {{ __('Reason for assignment') }}
                        </flux:label>
                        <flux:error
                            class="mt-0! min-w-0 truncate"
                            name="sharedReason"
                            :title="$errors->first('sharedReason')"
                        />
                    </div>
                    <flux:select
                        variant="listbox"
                        searchable
                        clearable
                        wire:model.live="sharedReason"
                        x-data="{ reasonSearch: '' }"
                    >
                        <x-slot name="search">
                            <flux:select.search x-model="reasonSearch" />
                        </x-slot>
                        <flux:select.option.create x-on:click="$wire.openAssignmentReason(reasonSearch, true)">
                            {{ __('Create a translation key reason') }}
                        </flux:select.option.create>
                        @foreach ($assignmentReasonOptions as $reasonOption)
                            <flux:select.option :value="$reasonOption">{{ $reasonOption }}</flux:select.option>
                        @endforeach
                    </flux:select>

                </flux:field>
                <div class="flex items-center justify-between gap-3">
                    <flux:text>
                        {{ __('Selected findings: :count', ['count' => count($sharedSelection)]) }}
                    </flux:text>
                    <flux:button.group>
                        <flux:button
                            icon="check"
                            size="sm"
                            variant="filled"
                            color="green"
                            wire:click="reviewSharedAssignment"
                            :disabled="$sharedSelection === [] || count($sharedSelection) > 100 || mb_strlen(trim($sharedReason)) <
                                3 || mb_strlen(trim($sharedReason)) > 2000"
                            wire:loading.attr="disabled"
                            wire:target="sharedReason, sharedSelection, toggleSharedPageSelection, reviewSharedAssignment"
                        />
                        <flux:button
                            size="sm"
                            variant="filled"
                            color="green"
                            wire:click="reviewSharedAssignment"
                            :disabled="$sharedSelection === [] || count($sharedSelection) > 100 || mb_strlen(trim($sharedReason)) <
                                3 || mb_strlen(trim($sharedReason)) > 2000"
                            wire:loading.attr="disabled"
                            wire:target="sharedReason, sharedSelection, toggleSharedPageSelection, reviewSharedAssignment"
                        >
                            {{ __('Assign selected findings to this translation key') }}
                        </flux:button>
                    </flux:button.group>
                </div>
            @endif
        </div>
    @endif
</flux:modal>
