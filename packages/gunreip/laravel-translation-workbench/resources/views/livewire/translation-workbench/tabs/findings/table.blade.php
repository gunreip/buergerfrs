{{-- Table Findings --}}
<flux:table container:class="max-h-[100vh] overflow-x-auto rounded-t-lg">
    {{-- Table Header --}}
    <flux:table.columns
        class="bg-white dark:bg-zinc-800"
        sticky
    >
        {{-- Column Finding ID --}}
        <flux:table.column
            sortable
            :sorted="$findingsSort === 'id'"
            :direction="$findingsDirection"
            wire:click="sortFindings('id')"
        >
            <flux:tooltip>
                <flux:callout.heading class="ml-2">
                    {{ __('Finding') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Finding ID')"
                    :text="__('Sort by finding ID. This column will show the unique finding ID for each finding.')"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Source --}}
        <flux:table.column
            sortable
            :sorted="$findingsSort === 'source'"
            :direction="$findingsDirection"
            wire:click="sortFindings('source')"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Source') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Source')"
                    :text="__(
                        'Sort by source. This column will show the source file and line number:column for each finding.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Open in VSC --}}
        <flux:table.column align="center">
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('VSC') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('VSC (Visual Studio Code)')"
                    :text="__(
                        'Open source in VSC (Visual Studio Code). This will open the source file in Visual Studio Code using the configured editor.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Captured expression --}}
        <flux:table.column
            sortable
            :sorted="$findingsSort === 'expression'"
            :direction="$findingsDirection"
            wire:click="sortFindings('expression')"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Captured expression') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Captured expression')"
                    :text="__(
                        'Sort by captured expressions. This column will show the captured expressions for the findigs.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Suggested key --}}
        <flux:table.column
            sortable
            :sorted="$findingsSort === 'suggested_key'"
            :direction="$findingsDirection"
            wire:click="sortFindings('suggested_key')"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Suggested key') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Suggested key')"
                    :text="__(
                        'Immutable suggestion created when the finding was first captured. It does not mean a translation key has been assigned.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Translation key --}}
        <flux:table.column
            sortable
            :sorted="$findingsSort === 'key'"
            :direction="$findingsDirection"
            wire:click="sortFindings('key')"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Translation key') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Translation key')"
                    :text="__('This column will show the assigned translation keys for the findings.')"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Status --}}
        <flux:table.column>
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Status') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Status')"
                    :text="__(
                        'This column will show the status of the findings. It can be active, archived, or deleted.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Action --}}
        <flux:table.column
            class="w-36"
            align="center"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Action') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Action')"
                    :text="__(
                        'View the workflow status or open this finding to assign a translation key and edit translations. Archived findings open read-only.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
    </flux:table.columns>
    {{-- Table Rows --}}
    <flux:table.rows>
        @forelse ($rows as $row)
            <flux:table.row :key="'finding-'.$row['id']">
                {{-- Finding ID --}}
                <flux:table.cell align="center">
                    <flux:callout.heading
                        class="text-right tabular-nums"
                        icon="key"
                    >
                        #{{ $row['id'] }}
                    </flux:callout.heading>
                    @if (in_array($status, ['active_compact', 'all_compact'], true) && ($row['compact_count'] ?? 1) > 1)
                        <flux:tooltip
                            :content="__('This row shows one representative finding. Switch to All to see each source separately.')"
                        >
                            <flux:badge
                                size="sm"
                                color="zinc"
                            >{{ __(':count findings', ['count' => $row['compact_count']]) }}</flux:badge>
                        </flux:tooltip>
                    @endif
                </flux:table.cell>
                {{-- Source --}}
                <flux:table.cell class="max-w-md whitespace-normal break-all">
                    <x-translation-workbench::ui.common.text-preview
                        :text="$row['path'] . ':' . $row['source_line'] . ':' . $row['source_column']"
                        :length="50"
                        side="center"
                        :heading="__('Source')"
                    />
                </flux:table.cell>
                {{-- Open in VSC --}}
                <flux:table.cell align="center">
                    @if ($row['status'] === 'active' && $row['path'] !== '')
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
                                            $row['path'],
                                    ),
                                ),
                            );
                            $editorUrl =
                                'vscode://vscode-remote/wsl+' .
                                rawurlencode((string) config('translation-workbench.editor.vscode_wsl_distro')) .
                                $editorPath;
                            if ($row['source_line']) {
                                $editorUrl .= ':' . (int) $row['source_line'];
                                if ($row['source_column']) {
                                    $editorUrl .= ':' . (int) $row['source_column'];
                                }
                            }
                        @endphp
                        <flux:tooltip>
                            <flux:button
                                type="button"
                                size="xs"
                                color="sky"
                                variant="ghost"
                                icon="external-link"
                                :href="$editorUrl"
                                :aria-label="__('Open source in VS Code')"
                            />
                            <x-translation-workbench::ui.common.tooltip-content
                                :heading="__('Open source in VS Code')"
                                :text="[__('Open the source file in VS Code using the configured editor.')]"
                            >
                                <x-translation-workbench::ui.common.path-preview
                                    :path="$editorUrl"
                                    :tooltip="false"
                                    :length="60"
                                    side="center"
                                />
                            </x-translation-workbench::ui.common.tooltip-content>
                        </flux:tooltip>
                    @else
                        <span
                            class="inline-block items-center justify-center"
                            aria-hidden="true"
                        >
                            <flux:tooltip toggleable>
                                <flux:button
                                    variant="subtle"
                                    icon="x"
                                    color="red"
                                    size="xs"
                                />
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Source not available anymore')"
                                    :text="[__('This source code has been deleted and is no longer available.')]"
                                />
                            </flux:tooltip>
                        </span>
                    @endif
                </flux:table.cell>
                {{-- Captured Expression --}}
                <flux:table.cell class="wrap-break-word max-w-md whitespace-normal">
                    {{-- <flux:callout.text>
                        <code>{{ $row['raw_expression'] }}</code>
                    </flux:callout.text> --}}
                    <x-translation-workbench::ui.common.text-preview
                        :heading="__('Captured expression')"
                        :text="$row['raw_expression']"
                        :length="120"
                        side="center"
                    />
                </flux:table.cell>
                {{-- Suggested Key --}}
                <flux:table.cell class="max-w-sm whitespace-normal break-all">
                    @if (($row['suggested_key'] ?? '') !== '')
                        <x-translation-workbench::ui.common.text-preview
                            :heading="__('Suggested key')"
                            :text="$row['suggested_key']"
                            :length="60"
                            side="center"
                        />
                    @else
                        <flux:tooltip :content="__('Capture inconsistency: this finding has no suggested key.')">
                            <flux:badge
                                size="sm"
                                color="red"
                            >{{ __('Missing suggested key') }}</flux:badge>
                        </flux:tooltip>
                    @endif
                </flux:table.cell>
                {{-- Translation Key --}}
                <flux:table.cell class="max-w-sm whitespace-normal break-all">
                    @if ($row['key_id'])
                        <x-translation-workbench::ui.common.text-preview
                            :prefix-key="'#' . $row['key_id']"
                            :heading="__('Translation Key')"
                            :text="$row['full_name']"
                            :length="80"
                            side="center"
                        />
                    @else
                        <flux:tooltip toggleable>
                            <flux:badge
                                as="button"
                                size="sm"
                                color="amber"
                                label="{{ __('Not assigned') }}"
                            />
                            <x-translation-workbench::ui.common.tooltip-content
                                heading="{{ __('Not assigned') }}"
                                :text="[__('This finding has not been assigned a translation key.')]"
                            />
                        </flux:tooltip>
                    @endif
                </flux:table.cell>
                {{-- Shared / UI candidate: informational only --}}
                <flux:table.cell class="whitespace-normal">
                    @if ($row['status'] !== 'active')
                        <div class="flex flex-col items-start gap-2">
                            <flux:tooltip toggleable>
                                <flux:badge
                                    class="hover:cursor-pointer"
                                    as="button"
                                    size="sm"
                                    color="zinc"
                                    label="{{ __('Archived') }}"
                                />
                                <flux:tooltip.content class="max-w-24 space-y-2 hyphens-auto text-justify">
                                    <p>
                                        {{ __('Archived') }}
                                    </p>
                                    <p class="mt-3 tabular-nums dark:text-zinc-300">
                                        {{ __('This finding is no longer active cause it has been deleted from the system. This finding is archived.') }}
                                    </p>
                                </flux:tooltip.content>
                            </flux:tooltip>
                            @if ($row['successor_id'] ?? null)
                                <flux:tooltip toggleable>
                                    <flux:badge
                                        class="hover:cursor-pointer"
                                        as="button"
                                        size="sm"
                                        color="amber"
                                        label="{{ ($row['successor_kind'] ?? '') === 'moved' ? __('Moved') : __('Replaced') }}"
                                    />
                                    <x-translation-workbench::ui.common.tooltip-content
                                        heading="{{ ($row['successor_kind'] ?? '') === 'moved' ? __('Moved') : __('Replaced') }}"
                                        :text="[__('Because the archived finding has been') . ' ' . (($row['successor_kind'] ?? '') === 'moved' ? __('moved') : __('replaced')) . ' ' . __('to a successor, the successor finding replaces this archived finding.'), __('Successor finding: #:id', ['id' => $row['successor_id']])]"
                                    />
                                </flux:tooltip>
                            @elseif (($row['retired_reason'] ?? '') === 'Absent from completed scan inventory')
                                <flux:tooltip toggleable>
                                    <flux:badge
                                        class="hover:cursor-pointer"
                                        as="button"
                                        size="sm"
                                        color="zinc"
                                        label="{{ __('Deleted') }}"
                                    />
                                    <x-translation-workbench::ui.common.tooltip-content
                                        heading="{{ __('Deleted') }}"
                                        :text="[
                                            __(
                                                'The source occurrence was absent from the completed scan. The archived record is retained.',
                                            ),
                                        ]"
                                    />
                                </flux:tooltip>
                            @endif
                        </div>
                    @else
                        <div class="flex flex-col items-start gap-2">
                            @if (!in_array($status, ['active', 'active_compact'], true))
                                <flux:badge
                                    size="sm"
                                    color="zinc"
                                >{{ __('Active') }}</flux:badge>
                            @endif
                            @include(
                                'translation-workbench::livewire.translation-workbench.shared.finding-workflow-status',
                                ['workflowFinding' => $row]
                            )
                            @include(
                                'translation-workbench::livewire.translation-workbench.shared.finding-candidates',
                                [
                                    'candidateFinding' => $row['id'],
                                    'showCandidateCount' => in_array(
                                        $status,
                                        ['active_compact', 'all_compact'],
                                        true),
                                    'candidateDisplayCount' => in_array(
                                        $status,
                                        ['active_compact', 'all_compact'],
                                        true)
                                        ? (int) $row['compact_count']
                                        : null,
                                ]
                            )
                        </div>
                    @endif
                </flux:table.cell>
                {{-- Action --}}
                <flux:table.cell>
                    <flux:button
                        size="sm"
                        variant="outline"
                        icon="queue-list"
                        wire:click="openWorkflowStatus({{ $row['id'] }})"
                        :tooltip="__('Workflow status')"
                        :aria-label="__('Workflow status')"
                    />
                    <flux:tooltip>
                        <flux:button.group class="ml-1 mr-3">
                            <flux:button
                                size="sm"
                                variant="outline"
                                wire:click="open({{ $row['id'] }})"
                            >
                                <flux:icon.package-open class="size-5" />
                            </flux:button>
                            <flux:button
                                size="sm"
                                variant="outline"
                                wire:click="open({{ $row['id'] }})"
                            >
                                {{ __('Open') }}
                            </flux:button>
                        </flux:button.group>
                        <x-translation-workbench::ui.common.tooltip-content
                            heading="{{ __('Open') }}"
                            :text="[
                                __('Open the Edit finding modal for editing finding ID #:id.', ['id' => $row['id']]),
                            ]"
                        />
                    </flux:tooltip>
                </flux:table.cell>
            </flux:table.row>
        @empty
            {{-- Empty --}}
            <flux:table.row>
                <flux:table.cell colspan="8">
                    <flux:callout.text>
                        {{ __('No findings match these filters.') }}
                    </flux:callout.text>
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>

<flux:pagination :paginator="$rows" />
