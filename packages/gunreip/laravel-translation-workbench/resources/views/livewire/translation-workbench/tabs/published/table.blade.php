{{-- Table Published --}}
<flux:table container:class="max-h-[100vh] overflow-x-auto rounded-t-lg">
    {{-- Table Header --}}
    <flux:table.columns
        class="bg-white dark:bg-zinc-800"
        sticky
    >
        {{-- Header Column Key --}}
        <flux:table.column
            align="center"
            sortable
            :sorted="$publishedSort === 'key'"
            :direction="$publishedDirection"
            wire:click="sortPublished('key')"
        >
            <flux:tooltip>
                <flux:callout.heading
                    class="ml-2"
                    align="center"
                >
                    {{ __('Key') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Key')"
                    :text="__(
                        'Database ID of the published translation key record. Select this header to sort by ID.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Header Column TranslationKey --}}
        <flux:table.column>
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Translation Key') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Translation Key')"
                    :text="__(
                        'Translation key used for this publication, including its namespace when present. Select a shortened key below to view it in full.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Header Column Language --}}
        <flux:table.column
            align="center"
            sortable
            :sorted="$publishedSort === 'language'"
            :direction="$publishedDirection"
            wire:click="sortPublished('language')"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Language') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Language')"
                    :text="__(
                        'Language or regional variant of the published translation. Select this header to sort by locale.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Header Column Working revision --}}
        <flux:table.column
            sortable
            :sorted="$publishedSort === 'working'"
            :direction="$publishedDirection"
            wire:click="sortPublished('working')"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Working revision') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Working revision')"
                    :text="__(
                        'Shows whether the current working revision and key still match the published version. Select this header to sort by this status.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Header Column Published at --}}
        <flux:table.column
            sortable
            :sorted="$publishedSort === 'published'"
            :direction="$publishedDirection"
            wire:click="sortPublished('published')"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Published at') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Published at')"
                    :text="__(
                        'Date and time when this publication was completed. Select this header to sort chronologically.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Header Column Actions --}}
        <flux:table.column align="center">
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Actions') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Actions')"
                    :text="__(
                        'View workflow progress, inspect the publication result, or edit translations through an active finding.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
    </flux:table.columns>
    {{-- Table Rows --}}
    <flux:table.rows>
        @forelse ($publishedTasks as $task)
            {{-- Table Row --}}
            <flux:table.row :key="'published-'.$task['run'].
            '-'.$task['revision']">
                {{-- Column Key --}}
                <flux:table.cell
                    class="ml-2 tabular-nums"
                    align="end"
                >
                    <flux:callout.text class="w-24">
                        <code>#{{ $task['key_id'] }}</code>
                    </flux:callout.text>
                </flux:table.cell>
                {{-- Column TranslationKey --}}
                <flux:table.cell class="whitespace-normal break-all">
                    <x-translation-workbench::ui.common.text-preview
                        :text="($task['namespace'] !== '' ? $task['namespace'].'::' : '').$task['full_name']"
                        :length="80"
                        side="center"
                        :heading="__('Translation Key')"
                    />
                </flux:table.cell>
                {{-- Column Language --}}
                <flux:table.cell align="center">
                    <flux:tooltip toggleable>
                        <flux:button
                            variant="ghost"
                            size="xs"
                            :aria-label="__('Show published revision for :locale', ['locale' => $task['locale']])"
                        >
                            <flux:flag
                                circle
                                :country="$translationFlagCountries[$task['locale']] ?? null"
                            />
                        </flux:button>
                        <x-translation-workbench::ui.common.tooltip-content-tabular :rows="[
                            ['label' => __('Language'), 'value' => $task['locale']],
                            ['label' => __('Published revision'), 'value' => '#' . $task['revision']],
                            ['label' => __('Published text'), 'value' => $task['value']],
                        ]" />
                    </flux:tooltip>
                </flux:table.cell>
                {{-- Column Working revision --}}
                <flux:table.cell>
                    @if ($task['current_name'] !== $task['full_name'] || $task['current_namespace'] !== $task['namespace'])
                        <flux:badge
                            size="sm"
                            color="amber"
                        >
                            {{ __('Key changed') }}
                        </flux:badge>
                    @elseif ((int) $task['current_revision_id'] !== (int) $task['revision'])
                        <flux:badge
                            size="sm"
                            color="amber"
                        >
                            {{ __('Different working revision') }}
                        </flux:badge>
                    @else
                        <flux:badge
                            size="sm"
                            color="green"
                        >
                            {{ __('Matches published revision') }}
                        </flux:badge>
                    @endif
                </flux:table.cell>
                {{-- Column Published at --}}
                <flux:table.cell>
                    <x-ui.date-time.date-time :value="$task['occurred_at']" />
                </flux:table.cell>
                {{-- Column Actions --}}
                <flux:table.cell>
                    <div class="flex flex-wrap gap-2">
                        <flux:button
                            size="sm"
                            variant="outline"
                            icon="queue-list"
                            wire:click="openKeyWorkflowStatus({{ $task['key_id'] }})"
                            :tooltip="__('Workflow status')"
                            :aria-label="__('Workflow status')"
                        />
                        <flux:button
                            size="sm"
                            variant="outline"
                            icon="eye"
                            wire:click="openPublication('{{ $task['run'] }}')"
                        >
                            {{ __('Publication result') }}
                        </flux:button>
                        @if ($task['finding_id'])
                            <flux:button
                                size="sm"
                                variant="outline"
                                icon="pencil-square"
                                wire:click="open({{ $task['finding_id'] }})"
                                :tooltip="__('Open the current key through an active finding. Editing uses the active language in Settings.')"
                            >
                                {{ __('Edit translations') }}
                            </flux:button>
                        @else
                            <flux:badge
                                size="sm"
                                color="zinc"
                            >
                                {{ __('No active finding') }}
                            </flux:badge>
                        @endif
                    </div>
                </flux:table.cell>
            </flux:table.row>
        @empty
            {{-- Table Row Empty --}}
            <flux:table.row>
                {{-- Column Empty --}}
                <flux:table.cell colspan="6">
                    <flux:callout.text>
                        {{ __('No completed publications found.') }}
                    </flux:callout.text>
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>

<flux:pagination :paginator="$publishedTasks" />
