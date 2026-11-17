<div
    class="relative"
    @if ($sourceReplacementTasks->total() === 0 && empty($sourceReplacementTasks->awaitingScan)) data-source-list-empty @endif
    @if ($sourceReplacementTasks->allAssigned ?? false) data-source-all-assigned @endif
    @if ($sourceReplacementTasks->awaitingScan ?? 0) data-source-awaiting-scan @endif
    x-data="{ get selectedIds() { return $wire.$parent.sourceReplacementGroup.finding_ids ?? [] } }"
>
    <div class="flex flex-wrap items-center justify-between gap-2">
        <flux:callout.heading
            class="tabular-nums"
            icon="database-search"
        >
            {{ __('Findings') }}:
            <flux:callout.text>
                {{ number_format($sourceReplacementTasks->total()) }}
            </flux:callout.text>
        </flux:callout.heading>

        @if ($sourceReplacementTasks->awaitingScan ?? 0)
            <div class="ms-auto flex flex-wrap items-center justify-end gap-2">
                <flux:toggle
                    size="sm"
                    wire:model.live="includeAwaitingScan"
                    :label="__('Awaiting scan confirmation') . ' · ' . $sourceReplacementTasks->awaitingScan"
                >
                    <x-slot:icon>
                        <flux:icon.shrink class="group-data-checked:block text-(--color-accent-content) hidden size-5" />
                        <flux:icon.expand
                            class="group-data-checked:hidden size-5 text-zinc-500/85 dark:text-zinc-300/80" />
                    </x-slot:icon>
                </flux:toggle>
                <flux:button.group>
                    <flux:button
                        size="sm"
                        square
                        wire:click="$parent.openNewSourceScan"
                        :disabled="$testToken !== ''"
                    >
                        <flux:icon.arrow-path class="size-5" />
                    </flux:button>
                    <flux:button
                        size="sm"
                        wire:click="$parent.openNewSourceScan"
                        :disabled="$testToken !== ''"
                    >
                        {{ __('translation-workbench.buttons.run_new_scan') }}
                    </flux:button>
                </flux:button.group>
            </div>
        @endif
    </div>

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
                <flux:tooltip>
                    <flux:callout.heading class="ml-2">
                        {{ __('Finding ID') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Finding ID')"
                        :text="__(
                            'Identifies the recorded occurrence in the source code. Each finding has its own ID.',
                        )"
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
                    <flux:callout.heading>
                        {{ __('Key ID') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Key ID')"
                        :text="__(
                            'Database ID of the assigned translation key. Findings that share a key have the same Key ID.',
                        )"
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
                    <flux:callout.heading>
                        {{ __('Translation key') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Translation key')"
                        :text="__('The assigned key that the translation call should use at this occurrence.')"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Table Header Column VS Code Link --}}
            <flux:table.column
                align="center"
                sortable
                :sorted="$sortColumn === 'source'"
                :direction="$sortDirection"
                wire:click="sortBy('source')"
            >
                <flux:tooltip>
                    <flux:callout.heading>
                        {{ __('VSC') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('VSC')"
                        :text="__(
                            'Open the source file at the captured line and column in VS Code. Select this header to sort by source path and position.',
                        )"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Table Header Column Captured source position --}}
            <flux:table.column>
                <flux:tooltip>
                    <flux:callout.heading>
                        {{ __('Captured source position') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Captured source position')"
                        :text="__(
                            'File path, line and column recorded for the finding. Current check shows whether the position still matches.',
                        )"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Table Header Column Captured expression --}}
            <flux:table.column
                sortable
                :sorted="$sortColumn === 'expression'"
                :direction="$sortDirection"
                wire:click="sortBy('expression')"
            >
                <flux:tooltip>
                    <flux:callout.heading>
                        {{ __('Captured expression') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Captured expression')"
                        :text="__('The translation call recorded when this source occurrence was captured.')"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Table Header Column Current check --}}
            <flux:table.column>
                <flux:tooltip>
                    <flux:callout.heading>
                        {{ __('Current check') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Current check')"
                        :text="__(
                            'Compares the captured occurrence with the current file and checks replacement readiness. Run new scan indicates that the key is in the source and still needs scan confirmation.',
                        )"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Table Header Column Select --}}
            <flux:table.column align="center">
                <flux:tooltip>
                    <flux:callout.heading>
                        <span class="sr-only">{{ __('Select') }}</span>
                        <flux:icon.square-mouse-pointer class="size-5" />
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Select')"
                        :text="__(
                            'Select a finding to include the remaining occurrences for its key, even on other pages. Occurrences already using the key are excluded.',
                        )"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Table Header Column Actions --}}
            <flux:table.column align="center">
                <flux:tooltip>
                    <flux:callout.heading>
                        {{ __('Actions') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Actions')"
                        :text="__(
                            'View the workflow status. Select the key group to compare a finding with the current source or preview its replacement.',
                        )"
                    />
                </flux:tooltip>
            </flux:table.column>
        </flux:table.columns>
        {{-- Table Rows --}}
        <flux:table.rows>
            @forelse ($sourceReplacementTasks as $task)
                {{-- Table Row --}}
                <flux:table.row
                    :key="'source-replacement-'.$task['id']"
                    x-data="{ get selected() { return {{ $task['check_state'] === 'already_set' ? 'false' : 'true' }} && selectedIds.includes({{ (int) $task['id'] }}) } }"
                >
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
                                :search="$search"
                                :case-sensitive="$caseSensitive"
                                :whole-word="$wholeWord"
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
                                    :search="$search"
                                    :case-sensitive="$caseSensitive"
                                    :whole-word="$wholeWord"
                                    :vsc="false"
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
                                :search="$search"
                                :case-sensitive="$caseSensitive"
                                :whole-word="$wholeWord"
                                :vsc="false"
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
                        <div class="flex flex-col items-start gap-1">
                            <flux:tooltip>
                                <flux:badge
                                    :color="match($task['check_state']) {
                                        'ready' => 'green', 'already_set' => 'sky',
                                        default => 'amber'
                                    }"
                                    size="sm"
                                >
                                    {{ match ($task['check_state']) {'ready' => __('Ready at check time'),'already_set' => __('Assigned key already in source'),'possible_move' => __('Possible move'),'source_unavailable' => __('Source unavailable'),'source_changed' => __('Changed during check'),'expression_missing' => __('Expression not found'),'ambiguous_matches' => __('Ambiguous matches'),default => __('Needs attention')} }}
                                </flux:badge>
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
                            @if ($task['check_state'] === 'blocked')
                                @php
                                    $nextStep = match ($task['check_detail']) {
                                        \Gunreip\TranslationWorkbench\Generation\SourceReplacementPreview::REVISION_REQUIRED
                                            => __('Save source-language draft'),
                                        \Gunreip\TranslationWorkbench\Generation\SourceReplacementPreview::REVIEW_REQUIRED
                                            => __('Approve source-language revision'),
                                        \Gunreip\TranslationWorkbench\Generation\SourceReplacementPreview::PUBLICATION_REQUIRED
                                            => __('Publish source-language revision'),
                                        default => null,
                                    };
                                @endphp
                                @if ($nextStep)
                                    <flux:badge
                                        size="sm"
                                        color="sky"
                                    >{{ $nextStep }}</flux:badge>
                                @endif
                            @endif
                            @if ($task['needs_scan'] ?? false)
                                <flux:badge
                                    size="sm"
                                    color="amber"
                                >{{ __('translation-workbench.buttons.run_new_scan') }}</flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>
                    {{-- Table Body Row Column Select --}}
                    <flux:table.cell>
                        <div align="center">
                            @if ($task['check_state'] === 'already_set')
                                <flux:checkbox
                                    disabled
                                    :aria-label="__('Assigned key already in source')"
                                />
                            @else
                                <flux:checkbox
                                    x-effect="const checked = selected; $nextTick(() => { $el.checked = checked })"
                                    x-bind:disabled="selectedIds.length > 0"
                                    x-on:change="if (!selectedIds.length) {
                                        try { await $wire.$parent.selectSourceReplacementGroup({{ (int) $task['id'] }}) }
                                        finally { $el.checked = selected }
                                    }"
                                    :aria-label="__('Select remaining findings for key #:key', ['key' => $task['key_id']])"
                                />
                            @endif
                        </div>
                    </flux:table.cell>
                    {{-- Table Row Column Actions --}}
                    <flux:table.cell>
                        <div class="flex items-center gap-1">
                            <flux:tooltip>
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    square
                                    wire:click="$parent.openWorkflowStatus({{ $task['id'] }})"
                                    wire:loading.attr="disabled"
                                    :aria-label="__('Workflow status')"
                                >
                                    <flux:icon.queue-list size="6" />
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
                                        square
                                        wire:click="$parent.openSourceComparison({{ $task['id'] }})"
                                        wire:loading.attr="disabled"
                                        x-bind:disabled="!selected"
                                    >
                                        <flux:icon.eye class="size-6" />
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="$parent.openSourceComparison({{ $task['id'] }})"
                                        wire:loading.attr="disabled"
                                        x-bind:disabled="!selected"
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
                                <flux:button.group class="mr-3">
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        square
                                        wire:click="$parent.previewSelectedSource({{ $task['id'] }})"
                                        wire:loading.attr="disabled"
                                        x-bind:disabled="!selected ||
                                            {{ in_array($task['check_state'], ['ready', 'blocked', 'already_set'], true) ? 'false' : 'true' }}"
                                    >

                                        <flux:icon.code-xml class="size-6" />
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="$parent.previewSelectedSource({{ $task['id'] }})"
                                        wire:loading.attr="disabled"
                                        x-bind:disabled="!selected ||
                                            {{ in_array($task['check_state'], ['ready', 'blocked', 'already_set'], true) ? 'false' : 'true' }}"
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
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                {{-- Table Row Empty State --}}
                <flux:table.row>
                    {{-- Table Row Column Empty State --}}
                    <flux:table.cell colspan="9">
                        <flux:callout.text>
                            {{ $sourceReplacementTasks->awaitingScan ?? 0 ? __('No open source replacements match the current filters.') : __('No matching active findings linked to published keys.') }}
                        </flux:callout.text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:pagination :paginator="$sourceReplacementTasks" />
</div>
