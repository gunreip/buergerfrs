<flux:modal
    class="w-full max-w-6xl"
    name="workbench-shared-findings"
    wire:model="sharedOpen"
>
    <x-translation-workbench::ui.common.modal-loading
        target="gotoPage, nextPage, previousPage, setPage, reviewSharedAssignment, sharedSelection, openAssignmentReason"
    />
    @if ($sharedOpen && $sharedRows)
        <div class="relative space-y-4">
            <flux:heading
                size="xl"
                icon="group"
            >
                {{ __('Shared / UI candidate') }} · #{{ $sharedFinding }}
            </flux:heading>
            @php
                $candidateCount = (int) ($sharedReference['candidates'] ?? 0);
                $sharedCount = (int) ($sharedReference['shared'] ?? 0);
                $countsMatch = $candidateCount === $sharedCount;
                $canAddSharedFindings = !empty($sharedReference['key_id']) && $sharedRows->total() > $sharedCount;
            @endphp
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
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <flux:callout.heading>
                            {{ __('Shared target key') }} · #{{ $sharedReference['key_id'] }}
                        </flux:callout.heading>
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:badge :color="$countsMatch ? 'green' : 'amber'">
                                {{ __('Shared candidates') }} · {{ $candidateCount }}
                            </flux:badge>
                            <flux:tooltip>
                                <flux:badge
                                    tabindex="0"
                                    :color="$countsMatch ? 'green' : 'red'"
                                >
                                    {{ __('Shared translations') }} · {{ $sharedCount }}
                                </flux:badge>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Shared translations')"
                                    :text="__(
                                        'Number of active findings already using this target key, across all pages. This counts key assignments, not translated languages. Checked, disabled rows are already assigned; other candidates are listed first.',
                                    )"
                                />
                            </flux:tooltip>
                        </div>
                    </div>
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


            <flux:error name="sharedAssignment" />
            <flux:error name="sharedSelection" />

            <flux:callout color="amber">
                {{-- Table of shared findings: --}}
                <flux:checkbox.group>
                    <flux:table container:class="max-h-120 overflow-x-auto rounded-t-lg">
                        {{-- Table Header --}}
                        <flux:table.columns class="bg-white dark:bg-zinc-800">
                            {{-- Table Header Column Select --}}
                            <flux:table.column>
                                <div class="ml-2 flex items-center gap-2">
                                    <flux:checkbox.all
                                        :disabled="$selectableSharedIds === []"
                                        :aria-label="__('Select or clear all eligible findings on this page')"
                                        :tooltip="__('Select or clear all eligible findings on this page. Selections on other pages are kept.')"
                                    />
                                    <span>{{ __('Select') }}</span>
                                </div>
                            </flux:table.column>
                            {{-- Table HeaderColumn Finding --}}
                            <flux:table.column>
                                {{ __('Finding') }}
                            </flux:table.column>
                            {{-- Table Header Column Literal --}}
                            <flux:table.column>
                                {{ __('Literal') }}
                            </flux:table.column>
                            {{-- Table Header Column Source --}}
                            <flux:table.column>
                                {{ __('Source') }}
                            </flux:table.column>
                            {{-- Table Header Column Suggested Key --}}
                            <flux:table.column>
                                {{ __('Suggested key') }}
                            </flux:table.column>
                            {{-- Table Header Column Translation Key / Assignment --}}
                            <flux:table.column>
                                {{ __('Translation key / Assignment') }}
                            </flux:table.column>
                        </flux:table.columns>
                        {{-- Table Body Rows --}}
                        <flux:table.rows>
                            @forelse ($sharedRows as $match)
                                {{-- Table Body Row --}}
                                <flux:table.row :key="'shared-finding-'.$match['id']">
                                    {{-- Table Body Column Select --}}
                                    <flux:table.cell>
                                        <div class="ml-2 flex items-center justify-center gap-2">
                                            @if (!empty($sharedReference['key_id']) && $match['key_id'] == $sharedReference['key_id'])
                                                <flux:checkbox
                                                    checked
                                                    disabled
                                                    :value="(string) $match['id']"
                                                    :aria-label="__('Finding #:id already uses this key', ['id' => $match['id']])"
                                                />
                                            @else
                                                <flux:checkbox
                                                    wire:model.live="sharedSelection"
                                                    :value="(string) $match['id']"
                                                    :disabled="!in_array((string) $match['id'], $selectableSharedIds, true)"
                                                    :aria-label="__('Select finding #:id', ['id' => $match['id']])"
                                                />
                                            @endif
                                        </div>
                                    </flux:table.cell>
                                    {{-- Table Body Column Finding --}}
                                    <flux:table.cell>
                                        #{{ $match['id'] }}
                                        @if ((int) $match['id'] === $sharedFinding)
                                            <div class="mt-1">
                                                <flux:badge
                                                    size="sm"
                                                    color="sky"
                                                >{{ __('Current finding') }}</flux:badge>
                                            </div>
                                        @endif
                                    </flux:table.cell>
                                    {{-- Table Body Column Literal --}}
                                    <flux:table.cell class="wrap-break-word max-w-sm whitespace-normal">
                                        {{ $match['literal'] ?? '—' }}
                                    </flux:table.cell>
                                    {{-- Table Body Column Source --}}
                                    <flux:table.cell class="max-w-md whitespace-normal break-all">
                                        <x-translation-workbench::ui.common.path-preview
                                            :heading="__('Source')"
                                            :path="$match['path']"
                                            :line="$match['source_line']"
                                            :column="$match['source_column']"
                                            :length="60"
                                            side="center"
                                        />
                                    </flux:table.cell>
                                    {{-- Table Body Column Suggested Key --}}
                                    <flux:table.cell class="max-w-sm whitespace-normal break-all">
                                        <x-translation-workbench::ui.common.text-preview :mono="true"
                                            :heading="__('Suggested key')"
                                            :text="$match['suggested_key'] ?? '—'"
                                            :length="60"
                                            side="center"
                                        />
                                    </flux:table.cell>
                                    {{-- Table Body ColumnTranslation Key / Assignment --}}
                                    <flux:table.cell class="max-w-sm whitespace-normal break-all">
                                        @if ($match['key_id'])
                                            #{{ $match['key_id'] }} · {{ $match['full_name'] }}
                                            <div class="mt-1">
                                                @if ($match['key_id'] == ($sharedReference['key_id'] ?? null))
                                                    <flux:badge
                                                        class="mb-1"
                                                        color="green"
                                                        size="sm"
                                                    >
                                                        {{ __('Same assigned key') }}
                                                    </flux:badge>
                                                    <flux:tooltip>
                                                        <flux:badge
                                                            tabindex="0"
                                                            color="green"
                                                            size="sm"
                                                        >
                                                            {{ __('Assignment reason') }}
                                                        </flux:badge>
                                                        <x-translation-workbench::ui.common.tooltip-content
                                                            :heading="__('Saved assignment reason')"
                                                            :text="$match['assignment_reason'] ??
                                                                __(
                                                                    'No explicit reason is recorded for this assignment.',
                                                                )"
                                                        />
                                                    </flux:tooltip>
                                                @else
                                                    <flux:badge size="sm">
                                                        {{ __('Different assigned key') }}
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
                                {{-- Table Body Empty State --}}
                                <flux:table.row>
                                    {{-- Table Body Column Empty State --}}
                                    <flux:table.cell colspan="6">
                                        {{ __('No matching active findings.') }}
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </flux:checkbox.group>
            </flux:callout>

            <flux:pagination :paginator="$sharedRows" />

            @if (($sharedReference['status'] ?? null) === 'active' && !empty($sharedReference['key_id']))
                <flux:field>
                    <div class="flex h-6 items-center gap-3">
                        <flux:label class="shrink-0">
                            {{ __('Reason for assignment') }}
                        </flux:label>
                        @unless ($canAddSharedFindings)
                            <flux:tooltip>
                                <flux:badge
                                    tabindex="0"
                                    color="green"
                                    size="sm"
                                >
                                    {{ __('Already assigned') }}
                                </flux:badge>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Already assigned')"
                                    :text="__(
                                        'All listed findings already use this key. No new assignment reason is needed. Saved reasons can be inspected beside each assigned finding.',
                                    )"
                                />
                            </flux:tooltip>
                        @endunless
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
                        :disabled="!$canAddSharedFindings"
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
                            wire:target="sharedReason, sharedSelection, reviewSharedAssignment"
                        />
                        <flux:button
                            size="sm"
                            variant="filled"
                            color="green"
                            wire:click="reviewSharedAssignment"
                            :disabled="$sharedSelection === [] || count($sharedSelection) > 100 || mb_strlen(trim($sharedReason)) <
                                3 || mb_strlen(trim($sharedReason)) > 2000"
                            wire:loading.attr="disabled"
                            wire:target="sharedReason, sharedSelection, reviewSharedAssignment"
                        >
                            {{ __('Assign selected findings to this translation key') }}
                        </flux:button>
                    </flux:button.group>
                </div>
            @endif
        </div>
    @endif
</flux:modal>
