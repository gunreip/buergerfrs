<div class="relative mt-3">
    {{-- <flux:callout.heading>
        {{ __('Findings') }} · {{ $sourceReplacementTasks->total() }}
    </flux:callout.heading> --}}
    {{-- Table Completed --}}
    <flux:table container:class="max-h-120 overflow-auto rounded-t-lg">
        {{-- Table Header Columns --}}
        <flux:table.columns
            class="bg-white dark:bg-zinc-800"
            sticky
        >
            {{-- Table Header Column Finding ID --}}
            <flux:table.column
                sortable
                :sorted="$sortColumn === 'finding'"
                :direction="$sortDirection"
                wire:click="sortBy('finding')"
            >
                <flux:tooltip>
                    <flux:callout.heading class="ml-2">{{ __('Finding ID') }}</flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Finding ID')"
                        :text="__('Identifies the recorded occurrence in the source code. Each finding has its own ID.')"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Table Header Column Key ID --}}
            <flux:table.column
                sortable
                :sorted="$sortColumn === 'key_id'"
                :direction="$sortDirection"
                wire:click="sortBy('key_id')"
            >
                <flux:tooltip>
                    <flux:callout.heading>{{ __('Key ID') }}</flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Key ID')"
                        :text="__('Database ID of the assigned translation key. Findings that share a key have the same Key ID.')"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Table Header Column Translation key --}}
            <flux:table.column
                sortable
                :sorted="$sortColumn === 'key'"
                :direction="$sortDirection"
                wire:click="sortBy('key')"
            >
                <flux:tooltip>
                    <flux:callout.heading>{{ __('Translation key') }}</flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Translation key')"
                        :text="__('The published key confirmed at this source occurrence by a scan.')"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Table Header Column Source --}}
            <flux:table.column
                sortable
                :sorted="$sortColumn === 'source'"
                :direction="$sortDirection"
                wire:click="sortBy('source')"
            >
                <flux:tooltip>
                    <flux:callout.heading>{{ __('Source') }}</flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Source')"
                        :text="__('Path, line and column of the confirmed occurrence in the source file.')"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Table Header Column Actions --}}
            <flux:table.column align="center">
                <flux:tooltip>
                    <flux:callout.heading class="mr-3">{{ __('Actions') }}</flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Actions')"
                        :text="__('Open the workflow status for this completed finding.')"
                    />
                </flux:tooltip>
            </flux:table.column>
        </flux:table.columns>
        {{-- Table Body Rows --}}
        <flux:table.rows>
            @forelse ($sourceReplacementTasks as $task)
                {{-- Table Body Row --}}
                <flux:table.row :key="'completed-'.$task['id']">
                    {{-- Table Body Row Column Finding ID --}}
                    <flux:table.cell>
                        <flux:callout.text class="tabular-nums">
                            #{{ $task['id'] }}
                        </flux:callout.text>
                    </flux:table.cell>
                    {{-- Table Body Row Column Key ID --}}
                    <flux:table.cell>
                        <flux:callout.text class="tabular-nums">
                            #{{ $task['key_id'] }}
                        </flux:callout.text>
                    </flux:table.cell>
                    {{-- Table Body Row Column Translation key --}}
                    <flux:table.cell>
                        <x-translation-workbench::ui.common.text-preview
                            :text="$task['full_name']"
                            :search="$search"
                            :case-sensitive="$caseSensitive"
                            :whole-word="$wholeWord"
                            :mono="true"
                        />
                    </flux:table.cell>
                    {{-- Table Body Row Column Source --}}
                    <flux:table.cell>
                        <x-translation-workbench::ui.common.path-preview
                            :path="$task['path']"
                            :line="$task['source_line']"
                            :column="$task['source_column']"
                            :search="$search"
                            :case-sensitive="$caseSensitive"
                            :whole-word="$wholeWord"
                        />
                    </flux:table.cell>
                    {{-- Table Body Row Column Actions --}}
                    <flux:table.cell align="center">
                        <flux:button
                            size="sm"
                            square
                            :tooltip="__('Workflow status')"
                            :aria-label="__('Workflow status')"
                            wire:click="$parent.openWorkflowStatus({{ $task['id'] }})"
                        >
                            <flux:icon.queue-list class="size-6" />
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                {{-- Table Body Row Empty --}}
                <flux:table.row>
                    {{-- Table Body Row Column Empty --}}
                    <flux:table.cell colspan="5">
                        <flux:callout.text>
                            {{ __('No matching completed findings.') }}
                        </flux:callout.text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:pagination :paginator="$sourceReplacementTasks" />
</div>
