<flux:checkbox.group>
    {{-- Table Publications --}}
    <flux:table container:class="max-h-[100vh] overflow-x-auto rounded-t-lg">
        {{-- Table Header --}}
        <flux:table.columns class="bg-white dark:bg-zinc-800">
            {{-- Column Key --}}
            <flux:table.column
                class="tabular-nums"
                sortable
                :sorted="$publicationSort === 'key'"
                :direction="$publicationDirection"
                wire:click="sortPublication('key')"
            >
                <flux:tooltip>
                    <flux:callout.heading class="ml-2">
                        {{ __('Key') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Sort by Key')"
                        :text="__(
                            'Database ID of the translation key, followed by its full name and namespace if present. This is not a finding ID. Click the header to sort by key ID.',
                        )"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Column Language --}}
            <flux:table.column
                sortable
                :sorted="$publicationSort === 'language'"
                :direction="$publicationDirection"
                wire:click="sortPublication('language')"
            >
                <flux:tooltip>
                    <flux:callout.heading>
                        {{ __('Language') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Sort by Language')"
                        :text="__(
                            'Language code and ID of the approved saved revision waiting for publication. The revision ID identifies this saved text version. Click the header to sort by language.',
                        )"
                    />
                </flux:tooltip>
            </flux:table.column>
            <flux:table.column>
                <flux:checkbox.all
                    :aria-label="__('Select all visible publication revisions')"
                    :tooltip="__('Select or clear the revisions on this page.')"
                />
            </flux:table.column>
            {{-- Column Actions --}}
            <flux:table.column class="max-w-0">
                <flux:tooltip>
                    <flux:callout.heading>
                        {{ __('Actions') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Actions')"
                        :text="__(
                            'View the workflow status for this key or open an approved revision to prepare its publication. Opening the task does not write language files; publication requires a separate confirmation.',
                        )"
                    />
                </flux:tooltip>
                <flux:button.group class="ml-2">
                    <flux:button
                        variant="outline"
                        size="xs"
                        wire:click="openPublicationTask"
                        :disabled="count($selectedPublicationRevisions) === 0"
                        wire:loading.attr="disabled"
                    >
                        <flux:icon.eye class="size-5" />
                    </flux:button>
                    <flux:button
                        variant="outline"
                        size="xs"
                        wire:click="openPublicationTask"
                        :disabled="count($selectedPublicationRevisions) === 0"
                        wire:loading.attr="disabled"
                    >
                        {{ __('Prepare selected revisions (:count)', ['count' => count($selectedPublicationRevisions)]) }}
                    </flux:button>
                </flux:button.group>
            </flux:table.column>
        </flux:table.columns>
        {{-- Table Rows --}}
        <flux:table.rows>
            @forelse ($publicationTasks as $task)
                {{-- Table Row --}}
                <flux:table.row :key="'publication-task-'.$task['revision']">
                    {{-- Column Key --}}
                    <flux:table.cell class="whitespace-normal tabular-nums">
                        <flux:callout.text>
                            <code>#{{ $task['key_id'] }} ·
                                {{ $task['namespace'] !== '' ? $task['namespace'] . '::' : '' }}{{ $task['full_name'] }}</code>
                        </flux:callout.text>
                    </flux:table.cell>
                    {{-- Column Language --}}
                    <flux:table.cell>
                        <flux:callout.text class="flex justify-start">
                            <flux:flag
                                class="mr-2"
                                circle
                                size="xs"
                                :country="$translationFlagCountries[$task['locale']] ?? null"
                            />
                            {{ $task['locale'] }} · #{{ $task['revision'] }}
                        </flux:callout.text>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:checkbox
                            wire:model.live="selectedPublicationRevisions"
                            :value="(string) $task['revision']"
                            :aria-label="__('Select revision #:revision', ['revision' => $task['revision']])"
                        />
                    </flux:table.cell>
                    {{-- Column Actions --}}
                    <flux:table.cell>
                        <div class="flex justify-start gap-1">
                            {{-- Button Workflow Status --}}
                            <flux:tooltip>
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    wire:click="openKeyWorkflowStatus({{ $task['key_id'] }})"
                                    :aria-label="__('Workflow status')"
                                >
                                    <flux:icon.queue-list size="5" />
                                </flux:button>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Workflow status')"
                                    :text="__('View the workflow status for this translation key.')"
                                />
                            </flux:tooltip>
                            {{-- Button Prepare Publication --}}
                            <flux:tooltip>
                                <flux:button.group>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="openPublicationTask({{ $task['revision'] }})"
                                        :disabled="count($selectedPublicationRevisions) > 1 && in_array((string) $task['revision'],
                                            array_map('strval', $selectedPublicationRevisions), true)"
                                    >
                                        <flux:icon.eye size="5" />
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="openPublicationTask({{ $task['revision'] }})"
                                        :disabled="count($selectedPublicationRevisions) > 1 && in_array((string) $task['revision'],
                                            array_map('strval', $selectedPublicationRevisions), true)"
                                    >
                                        {{ __('Prepare publication') }}
                                    </flux:button>
                                    <x-translation-workbench::ui.common.tooltip-content
                                        :heading="__('Prepare publication')"
                                        :text="count($selectedPublicationRevisions) > 1 &&
                                        in_array(
                                            (string) $task['revision'],
                                            array_map('strval', $selectedPublicationRevisions),
                                            true,
                                        )
                                            ? __(
                                                'Multiple revisions are selected. Use Prepare selected revisions to continue with the selection together.',
                                            )
                                            : __(
                                                'Prepare this saved revision for publication. Files are written only after confirmation.',
                                            )"
                                    />
                                </flux:button.group>
                            </flux:tooltip>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                {{-- Table Row Empty --}}
                <flux:table.row>
                    {{-- Column Empty --}}
                    <flux:table.cell colspan="4">
                        <flux:callout.text>
                            {{ __('No approved revisions waiting for publication.') }}
                        </flux:callout.text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</flux:checkbox.group>

<flux:pagination :paginator="$publicationTasks" />
