{{-- Table --}}
<flux:table container:class="max-h-[100vh] overflow-x-auto rounded-t-lg">
    {{-- Table Columns --}}
    <flux:table.columns class="bg-white dark:bg-zinc-800">
        {{-- Column Key --}}
        <flux:table.column
            sortable
            :sorted="$translationSort === 'key'"
            :direction="$translationDirection"
            wire:click="sortTranslations('key')"
        >
            <flux:tooltip content="{{ __('Sort by translation key') }}">
                <flux:callout.heading class="ml-2">
                    {{ __('Key') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Namespace --}}
        <flux:table.column
            sortable
            :sorted="$translationSort === 'namespace'"
            :direction="$translationDirection"
            wire:click="sortTranslations('namespace')"
        >
            <flux:tooltip content="{{ __('Sort by namespace') }}">
                <flux:callout.heading>
                    {{ __('Namespace') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Active Findings --}}
        <flux:table.column sortable
            :sorted="$translationSort === 'findings'"
            :direction="$translationDirection"
            wire:click="sortTranslations('findings')">
            <flux:tooltip content="{{ __('Sort by active findings') }}">
                <flux:callout.heading>
                    {{ __('Active findings') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Saved Language Revisions --}}
        <flux:table.column>
            <flux:tooltip content="{{ __('Saved language revisions') }}">
                <flux:callout.heading>
                    {{ __('Saved language revisions') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Actions --}}
        <flux:table.column>
            <flux:tooltip content="{{ __('Actions') }}">
                <flux:callout.heading>
                    {{ __('Actions') }}
                </flux:callout.heading>
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
                    <flux:callout.text class="ml-2">
                        <code>
                            {{ $task['namespace'] !== '' ? $task['namespace'] . '::' : '' }}{{ $task['full_name'] }}
                        </code>
                    </flux:callout.text>
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
                    <flux:button size="sm" variant="outline" icon="queue-list" wire:click="openWorkflowStatus({{ $task['finding_id'] }})" :tooltip="__('Workflow status')" :aria-label="__('Workflow status')" />
                    <flux:tooltip>
                        <flux:button.group>
                            <flux:button
                                size="sm"
                                variant="outline"
                                icon="pencil-square"
                                wire:click="open({{ $task['finding_id'] }})"
                            />
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
                            :text="__('Open the editor through an active finding linked to this key. ID #:key_id', [
                                'key_id' => $task['key_id'],
                            ])"
                        />
                    </flux:tooltip>
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
