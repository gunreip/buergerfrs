{{-- Table Translations --}}
<flux:table container:class="max-h-[100vh] overflow-auto rounded-t-lg">
    {{-- Table Header --}}
    <flux:table.columns class="bg-white dark:bg-zinc-800">
        {{-- Table Header Column Key --}}
        <flux:table.column
            sortable
            :sorted="$translationSort === 'key'"
            :direction="$translationDirection"
            wire:click="sortTranslations('key')"
        >
            <flux:tooltip>
                <flux:callout.heading class="ml-2">
                    {{ __('Key') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Translation key record ID')"
                    :text="__(
                        'Database ID of the assigned translation key record, not a finding ID. Findings that use this key are grouped into one row.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Table Header Column Namespace --}}
        <flux:table.column
            sortable
            :sorted="$translationSort === 'namespace'"
            :direction="$translationDirection"
            wire:click="sortTranslations('namespace')"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Namespace') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Namespace')"
                    :text="__(
                        'Full assigned translation key, including its namespace followed by :: when present. This is the key used to look up the translation in the language files.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Table Header Column Active Findings --}}
        <flux:table.column
            sortable
            :sorted="$translationSort === 'findings'"
            :direction="$translationDirection"
            wire:click="sortTranslations('findings')"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Active findings') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Active findings')"
                    :text="__(
                        'Number of active source findings currently linked to this translation key. Archived findings and inactive links are not counted.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Table Header Column Saved Language Revisions --}}
        <flux:table.column>
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Saved language revisions') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Saved language revisions')"
                    :text="__(
                        'Languages with a currently saved text revision for this key. Click a flag to see the locale and revision ID. A saved revision is not necessarily reviewed or published.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Table Header Column Actions --}}
        <flux:table.column
            class="w-36"
            align="center"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Actions') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Actions')"
                    :text="__(
                        'View the workflow status or open the translation editor through an active finding linked to this key. Translations saved for a shared key apply to all findings using that key.',
                    )"
                />
            </flux:tooltip>
        </flux:table.column>
    </flux:table.columns>
    {{-- Table Rows --}}
    <flux:table.rows>
        @forelse ($translationTasks as $task)
            {{-- Table Row --}}
            <flux:table.row :key="'translation-task-'.$task['key_id']">
                {{-- Column Key --}}
                <flux:table.cell
                    class="whitespace-normal"
                    align="end"
                >
                    <flux:callout.text class="ml-2">
                        <span class="tabular-nums">
                            #{{ $task['key_id'] }}
                        </span>
                    </flux:callout.text>
                </flux:table.cell>
                {{-- Column Namespace --}}
                <flux:table.cell class="whitespace-normal">
                    <x-translation-workbench::ui.common.text-preview
                        :mono="true"
                        :text="($task['namespace'] !== '' ? $task['namespace'].'::' : '').$task['full_name']"
                        :length="80"
                        side="center"
                        :heading="__('Translation Key')"
                    />
                </flux:table.cell>
                {{-- Column Active Findings --}}
                <flux:table.cell>
                    <flux:callout.text class="tabular-nums">
                        {{ $task['findings_count'] }}
                    </flux:callout.text>
                </flux:table.cell>
                {{-- Column Saved Language Revisions --}}
                <flux:table.cell>
                    <div class="flex flex-wrap gap-2">
                        @forelse ($task['locales'] as $language)
                            <flux:tooltip
                                toggleable
                                wire:key="translation-revision-{{ $task['key_id'] }}-{{ $language['locale'] }}-{{ $language['revision'] }}"
                            >
                                <flux:button.group>
                                    <flux:button
                                        type="button"
                                        variant="ghost"
                                        size="xs"
                                        :aria-label="__('Show saved revision for :locale', ['locale' => $language['locale']])"
                                    />
                                    <flux:button
                                        type="button"
                                        variant="ghost"
                                        size="xs"
                                        :aria-label="__('Show saved revision for :locale', ['locale' => $language['locale']])"
                                    >
                                        <flux:flag
                                            :country="$translationFlagCountries[$language['locale']] ?? null"
                                            size="xs"
                                            circle
                                        />
                                    </flux:button>
                                    <x-translation-workbench::ui.common.tooltip-content
                                        :heading="__('Language Information')"
                                        :text="__('Locale: :locale, Revision: :revision', [
                                            'locale' => $language['locale'],
                                            'revision' => $language['revision'],
                                        ])"
                                    />
                                </flux:button.group>
                            </flux:tooltip>
                        @empty
                            {{-- No Saved Language Revisions --}}
                            <flux:badge
                                size="sm"
                                color="amber"
                            >{{ __('No saved draft') }}</flux:badge>
                        @endforelse
                    </div>
                </flux:table.cell>
                {{-- Column Actions --}}
                <flux:table.cell>
                    <div class="flex justify-start gap-1">
                        {{-- Button Workflow Status --}}
                        <flux:tooltip>
                            <flux:button
                                size="sm"
                                variant="outline"
                                wire:click="openWorkflowStatus({{ $task['finding_id'] }})"
                                :aria-label="__('Workflow status')"
                            >
                                <flux:icon.queue-list size="5" />
                            </flux:button>
                            <x-translation-workbench::ui.common.tooltip-content
                                :heading="__('Workflow status')"
                                :text="__('View the workflow status for this finding.')"
                            />
                        </flux:tooltip>
                        {{-- Button Edit Translations --}}
                        <flux:tooltip>
                            <flux:button.group class="mr-3">
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    wire:click="open({{ $task['finding_id'] }})"
                                >
                                    <flux:icon.package-open size="5" />
                                </flux:button>
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    wire:click="open({{ $task['finding_id'] }})"
                                >
                                    {{ __('Edit translations') }}
                                </flux:button>
                            </flux:button.group>
                            <x-translation-workbench::ui.common.tooltip-content
                                :heading="__('Edit translation')"
                                :text="__(
                                    'Open the editor through an active finding linked to this key. ID #:key_id',
                                    [
                                        'key_id' => $task['key_id'],
                                    ],
                                )"
                            />
                        </flux:tooltip>
                    </div>
                </flux:table.cell>
            </flux:table.row>
        @empty
            {{-- Table Row --}}
            <flux:table.row>
                {{-- Column Empty State  --}}
                <flux:table.cell colspan="5">
                    <flux:callout.text class="ml-2">
                        {{ __('No assigned translation keys found.') }}
                    </flux:callout.text>
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>

<flux:pagination :paginator="$translationTasks" />
