<div class="relative">
    <x-ui.loading.lazy-indicator
        :overlay="true"
        target="nextPage, previousPage, gotoPage, sortBy"
        :text="__('Loading')"
    />
    <flux:callout.heading
        class="tabular-nums"
        icon="database-search"
    >
        {{ __('Findings') }}:
        <flux:callout.text>
            {{ number_format($sourceReplacementTasks->total()) }}
        </flux:callout.text>
    </flux:callout.heading>

    {{-- Table --}}
    <flux:table container:class="max-h-120 mt-3 overflow-auto rounded-t-lg">
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
                <flux:callout.heading class="ml-2">
                    {{ __('Finding ID') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Key ID --}}
            <flux:table.column
                sortable
                :sorted="$sortColumn === 'key_id'"
                :direction="$sortDirection"
                wire:click="sortBy('key_id')"
            >
                <flux:callout.heading>
                    {{ __('Key ID') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Translation key --}}
            <flux:table.column
                sortable
                :sorted="$sortColumn === 'key'"
                :direction="$sortDirection"
                wire:click="sortBy('key')"
            >
                <flux:callout.heading>
                    {{ __('Translation key') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column VS Code Link --}}
            <flux:table.column
                sortable
                :sorted="$sortColumn === 'source'"
                :direction="$sortDirection"
                wire:click="sortBy('source')"
            >
                <flux:callout.heading>
                    {{ __('VSC') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Captured source position --}}
            <flux:table.column>
                <flux:callout.heading>
                    {{ __('Captured source position') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Captured expression --}}
            <flux:table.column
                sortable
                :sorted="$sortColumn === 'expression'"
                :direction="$sortDirection"
                wire:click="sortBy('expression')"
            >
                <flux:callout.heading>
                    {{ __('Captured expression') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Current check --}}
            <flux:table.column>
                <flux:callout.heading>
                    {{ __('Current check') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Select --}}
            <flux:table.column>
                <flux:tooltip>
                    <flux:callout.heading>
                        {{ __('Select') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Select a translation key')"
                        :text="__(
                            'Select one finding to include all active findings assigned to the same key, including findings outside this page or filter. Release the current selection before choosing another key.',
                        )"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Table Header Column Actions --}}
            <flux:table.column>
                <flux:callout.heading>
                    {{ __('Actions') }}
                </flux:callout.heading>
            </flux:table.column>
        </flux:table.columns>
        {{-- Table Rows --}}
        <flux:table.rows>
            @forelse ($sourceReplacementTasks as $task)
                {{-- Table Row --}}
                <flux:table.row :key="'source-replacement-'.$task['id']">
                    {{-- Table Row Column Finding ID --}}
                    <flux:table.cell class="tabular-nums">
                        <flux:callout.text class="ml-2">
                            #{{ $task['id'] }}
                        </flux:callout.text>
                    </flux:table.cell>
                    {{-- Table Row Column Key ID --}}
                    <flux:table.cell class="tabular-nums">
                        <flux:callout.text>
                            #{{ $task['key_id'] }}
                        </flux:callout.text>
                    </flux:table.cell>
                    {{-- Table Row Column Translation Key --}}
                    <flux:table.cell class="max-w-xs whitespace-normal">
                        <flux:callout.text>
                            <x-translation-workbench::ui.common.text-preview
                                :text="$task['full_name']"
                                :mono="true"
                                :length="60"
                                side="center"
                            />
                        </flux:callout.text>
                    </flux:table.cell>
                    {{-- Table Row Column VS Code Link --}}
                    <flux:table.cell>
                        @php
                            $editorPath = str_replace(
                                '%2F',
                                '/',
                                rawurlencode(
                                    str_replace(
                                        '\\',
                                        '/',
                                        app(
                                            \Gunreip\TranslationWorkbench\Support\WorkbenchTestEnvironment::class,
                                        )->root() .
                                            '/' .
                                            $task['path'],
                                    ),
                                ),
                            );
                            $editorUrl =
                                'vscode://vscode-remote/wsl+' .
                                rawurlencode((string) config('translation-workbench.editor.vscode_wsl_distro')) .
                                $editorPath .
                                ':' .
                                max(1, (int) $task['source_line']) .
                                ':' .
                                max(1, (int) $task['source_column']);
                        @endphp
                        <flux:tooltip>
                            <flux:button
                                type="button"
                                size="xs"
                                variant="ghost"
                                icon="external-link"
                                :href="$editorUrl"
                                :aria-label="__('Open source in VS Code')"
                            />
                            <x-translation-workbench::ui.common.tooltip-content
                                :heading="__('Open source in VS Code')"
                                :text="__(
                                    'Open the captured file, line and column. If the occurrence has moved, this scan position may be outdated.',
                                )"
                            >
                                <x-translation-workbench::ui.common.path-preview
                                    :path="$task['path']"
                                    :line="$task['source_line']"
                                    :column="$task['source_column']"
                                    :length="60"
                                    side="center"
                                    :tooltip="false"
                                />
                            </x-translation-workbench::ui.common.tooltip-content>
                        </flux:tooltip>
                    </flux:table.cell>
                    {{-- Table Row Column Captured Source Path --}}
                    <flux:table.cell class="max-w-xs whitespace-normal">
                        <flux:callout.text>
                            <x-translation-workbench::ui.common.path-preview
                                :path="$task['path']"
                                :line="$task['source_line']"
                                :column="$task['source_column']"
                                :length="60"
                                side="center"
                            />
                        </flux:callout.text>
                    </flux:table.cell>
                    {{-- Table Row Column Captured Raw Expression --}}
                    <flux:table.cell class="max-w-xs whitespace-normal">
                        <flux:callout.text>
                            <x-translation-workbench::ui.common.text-preview
                                :text="$task['raw_expression']"
                                :mono="true"
                                :length="80"
                            />
                        </flux:callout.text>
                    </flux:table.cell>
                    {{-- Table Row Column Current Check --}}
                    <flux:table.cell>
                        <flux:tooltip toggleable>
                            <flux:button
                                variant="ghost"
                                size="sm"
                                :aria-label="__('Show check details')"
                            >
                                <flux:badge
                                    :color="match($task['check_state']) {
                                        'ready' => 'green', 'already_set' => 'sky',
                                        default => 'amber'
                                    }"
                                    size="sm"
                                >
                                    {{ match ($task['check_state']) {'ready' => __('Ready at check time'),'already_set' => __('Assigned key already in source'),'possible_move' => __('Possible move'),'source_unavailable' => __('Source unavailable'),'source_changed' => __('Changed during check'),'expression_missing' => __('Expression not found'),'ambiguous_matches' => __('Ambiguous matches'),default => __('Needs attention')} }}
                                </flux:badge>
                            </flux:button>
                            <x-translation-workbench::ui.common.tooltip-content
                                :heading="__('Current source check')"
                                :text="__($task['check_detail'])"
                            >
                                <div class="space-y-2 tabular-nums">
                                    <p>{{ __('Captured position') }}:
                                        {{ $task['source_line'] }}:{{ $task['source_column'] }}</p>
                                    @foreach ($task['position_candidates'] ?? [] as $position)
                                        <p>{{ __('Text match — not yet assigned') }}:
                                            {{ $position['line'] }}:{{ $position['column'] }}</p>
                                    @endforeach
                                </div>
                            </x-translation-workbench::ui.common.tooltip-content>
                        </flux:tooltip>
                    </flux:table.cell>
                    {{-- Table Body Row Column Select --}}
                    <flux:table.cell>
                        {{-- Reinitialize Flux's internal checked state when the fixed selection changes. --}}
                        <div
                            align="center"
                            wire:key="source-group-checkbox-{{ $task['id'] }}-{{ $selectedFindings === [] ? 'available' : 'locked' }}-{{ isset($selectedFindings[$task['id']]) ? 'selected' : 'unselected' }}"
                        >
                            <flux:checkbox
                                :checked="isset($selectedFindings[$task['id']])"
                                :disabled="$selectedFindings !== []"
                                wire:click="$parent.selectSourceReplacementGroup({{ $task['id'] }})"
                                wire:loading.attr="disabled"
                                wire:target="selectSourceReplacementGroup, releaseSourceReplacementGroup"
                                :aria-label="__('Select all active findings for key #:key', ['key' => $task['key_id']])"
                            />
                        </div>
                    </flux:table.cell>
                    {{-- Table Row Column Actions --}}
                    <flux:table.cell class="flex items-center gap-1">
                        <flux:tooltip>
                            <flux:button
                                size="sm"
                                variant="outline"
                                square
                                wire:click="$parent.openWorkflowStatus({{ $task['id'] }})"
                                wire:loading.attr="disabled"
                                :aria-label="__('Workflow status')"
                            >
                                <flux:icon.queue-list size="5" />
                            </flux:button>
                            <x-translation-workbench::ui.common.tooltip-content
                                :heading="__('Workflow status')"
                                :text="__(
                                    'View completed and outstanding steps for finding #:finding and its assigned translation key.',
                                    ['finding' => $task['id']],
                                )"
                            />
                        </flux:tooltip>
                        <flux:tooltip>
                            <flux:button.group>
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    wire:click="$parent.openSourceComparison({{ $task['id'] }})"
                                    wire:loading.attr="disabled"
                                    :disabled="!isset($selectedFindings[$task['id']])"
                                    {{-- :tooltip="__('Compare the captured occurrence with the current source. Select its key group first. No changes are saved.')" --}}
                                >
                                    <flux:icon.eye class="size-5" />
                                </flux:button>
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    wire:click="$parent.openSourceComparison({{ $task['id'] }})"
                                    wire:loading.attr="disabled"
                                    :disabled="!isset($selectedFindings[$task['id']])"
                                    {{-- :tooltip="__('Compare the captured occurrence with the current source. Select its key group first. No changes are saved.')" --}}
                                >
                                    {{ __('Compare source') }}
                                </flux:button>
                            </flux:button.group>
                            <x-translation-workbench::ui.common.tooltip-content
                                :heading="__('Compare source')"
                                :text="__(
                                    'Compare the captured occurrence with the current source. Select its key group first. No changes are saved.',
                                )"
                            />
                        </flux:tooltip>
                        <flux:tooltip>
                            <flux:button.group>
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    wire:click="$parent.previewSelectedSource({{ $task['id'] }})"
                                    wire:loading.attr="disabled"
                                    :disabled="!isset($selectedFindings[$task['id']]) ||
                                        !
                                        in_array($task['check_state'], ['ready', 'blocked', 'already_set'], true)"
                                >

                                    <flux:icon.code-xml class="size-5" />
                                </flux:button>
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    wire:click="$parent.previewSelectedSource({{ $task['id'] }})"
                                    wire:loading.attr="disabled"
                                    :disabled="!isset($selectedFindings[$task['id']]) ||
                                        !
                                        in_array($task['check_state'], ['ready', 'blocked', 'already_set'], true)"
                                >
                                    {{ __('Source replacement preview') }}
                                </flux:button>
                            </flux:button.group>
                            <x-translation-workbench::ui.common.tooltip-content
                                :heading="__('Source replacement preview')"
                                :text="__(
                                    'Select the key group and resolve changed or missing occurrences first. The preview checks this exact finding again and shows the current and proposed call. Opening it does not change any files.',
                                )"
                            />
                        </flux:tooltip>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                {{-- Table Row Empty State --}}
                <flux:table.row>
                    {{-- Table Row Column Empty State --}}
                    <flux:table.cell colspan="9">
                        <flux:callout.text>
                            {{ __('No matching active findings linked to published keys.') }}
                        </flux:callout.text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:pagination :paginator="$sourceReplacementTasks" />
</div>
