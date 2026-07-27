{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/header.blade.php --}}

{{-- Table Findings Header Row --}}
<flux:table.columns>
    {{-- Table Findings Header Column ID --}}
    <flux:table.column
        class="w-20 bg-white dark:bg-zinc-700"
        sticky
    >
        <span class="inline-flex items-center gap-1">
            <span>{{ __('ID') }}</span>
            <x-ui.tooltip.simple
                :header="__('Finding ID')"
                :text="__('Primary key of the translation_workbench_findings row.')"
            />
        </span>
    </flux:table.column>
    {{-- Table Findings Header Column Source --}}
    <flux:table.column
        sortable
        :sorted="$findingSortField === 'source'"
        :direction="$findingSortDirection"
        wire:click="sortFindingsBy('source')"
    >
        <span class="inline-flex items-center gap-1">
            <span>{{ __('Source') }}</span>
            <x-ui.tooltip.simple
                :header="__('Source location')"
                :text="__('Source file and line where the scanner found this translation-capable code occurrence.')"
            />
        </span>
    </flux:table.column>
    {{-- Table Findings Header Column Status --}}
    <flux:table.column class="text-center">
        <div class="flex flex-col items-center gap-1">
            <x-ui.tooltip.simple
                :title="$showObsoleteFindings ? __('Obsolete findings visible') : __('Obsolete findings hidden')"
                :text="$showObsoleteFindings
                    ? __('Obsolete findings are included in the current table view.')
                    : __(
                        'Obsolete findings are hidden from the default work list. Use this toggle or the explicit status filter to show them.',
                    )"
            >
                {{-- Button Obsolete (Table Header) --}}
                <flux:button
                    class="h-5 px-1.5 text-[10px]"
                    type="button"
                    size="xs"
                    variant="{{ $showObsoleteFindings ? 'primary' : 'subtle' }}"
                    color="{{ $showObsoleteFindings ? 'amber' : 'zinc' }}"
                    icon="archive"
                    wire:click.stop="toggleObsoleteFindings"
                >
                    {{ __('Obsolete') }}
                </flux:button>
            </x-ui.tooltip.simple>
            <span class="inline-flex items-center justify-center gap-1 text-center">
                <span>{{ __('Status') }}</span>
                <x-ui.tooltip.simple
                    :header="__('Finding status')"
                    :text="__(
                        'Current scanner lifecycle state for this finding. Obsolete entries are hidden by default unless the toggle or status filter includes them.',
                    )"
                />
            </span>
        </div>
    </flux:table.column>
    {{-- Table Findings Header Column Kind --}}
    <flux:table.column>
        <span class="inline-flex items-center gap-1">
            <span>{{ __('Kind') }}</span>
            <x-ui.tooltip.simple
                :header="__('Scanner kind')"
                :text="__('Raw scanner classification, independent of later review decisions.')"
            />
        </span>
    </flux:table.column>
    {{-- Table Findings Header Column Literal --}}
    <flux:table.column
        sortable
        :sorted="$findingSortField === 'literal'"
        :direction="$findingSortDirection"
        wire:click="sortFindingsBy('literal')"
    >
        <span class="inline-flex items-center gap-1">
            <span>{{ __('Literal') }}</span>
            <x-ui.tooltip.simple
                :header="__('Scanned literal')"
                :text="__(
                    'Literal text found in code, or scanner-suggested literal context when the code contains a translation key or dynamic expression.',
                )"
            />
        </span>
    </flux:table.column>
    {{-- Table Findings Header Column Keys --}}
    <flux:table.column
        sortable
        :sorted="$findingSortField === 'keys'"
        :direction="$findingSortDirection"
        wire:click="sortFindingsBy('keys')"
    >
        <span class="inline-flex items-center gap-1">
            <span>{{ __('Keys') }}</span>
            <x-ui.tooltip.simple
                :header="__('Translation keys')"
                :text="__(
                    'Shows the reviewed translation key when set, plus suggested, existing or found key context for review decisions.',
                )"
            />
        </span>
    </flux:table.column>
    {{-- Table Findings Header Column Candidate --}}
    <flux:table.column>
        <span class="inline-flex items-center gap-1">
            <span>{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings_table.candidate') }}</span>
            <x-ui.tooltip.simple
                :header="__('Candidate and reviewed type')"
                :text="__(
                    'Shows scanner candidate badges and reviewed type badges such as Is UI, Is Dynamic or Is Dynamic Multi.',
                )"
            />
        </span>
    </flux:table.column>
    {{-- Table Findings Header Column State --}}
    <flux:table.column>
        <span class="inline-flex items-center gap-1">
            <span>{{ __('State') }}</span>
            <x-ui.tooltip.simple
                :header="__('Workflow state')"
                :text="__('Shows review, source/target translation and saved-to-langfile state for this finding.')"
            />
        </span>
    </flux:table.column>
    {{-- Table Findings Header Column Dynamic Context --}}
    <flux:table.column>
        <span class="inline-flex items-center gap-1">
            <span>{{ __('Dynamic context') }}</span>
            <x-ui.tooltip.simple
                :header="__('Dynamic data context')"
                :text="__(
                    'Shows runtime or scanner dynamic-value context, including structured/unstructured state, discoveries, stored option values and unresolved dynamic sources.',
                )"
            />
        </span>
    </flux:table.column>
    {{-- Table Findings Header Column Actions --}}
    <flux:table.column>
        <div class="flex w-32 flex-col items-center gap-1">
            <span class="inline-flex items-center gap-1">
                <span>{{ __('ui.table.headers.actions') }}</span>
                <x-ui.tooltip.simple
                    :header="__('ui.table.headers.actions')"
                    :text="__(
                        'Open review, edit translation values when prerequisites are complete, inspect the timeline, or bulk-review matching literals.',
                    )"
                />
            </span>

            @if (($bulkEqualizeContext['selected_count'] ?? 0) > 0)
                <div class="flex items-center gap-1">
                    <x-ui.tooltip.simple
                        :title="__('Equalize selected translation keys')"
                        :text="__(
                            'Review the selected findings and set one shared translation key for entries with the same literal.',
                        )"
                    >
                        <flux:button
                            class="h-6"
                            type="button"
                            size="xs"
                            variant="{{ $bulkEqualizeContext['can_confirm'] ?? false ? 'primary' : 'subtle' }}"
                            color="{{ $bulkEqualizeContext['can_confirm'] ?? false ? 'amber' : 'zinc' }}"
                            icon="git-merge"
                            wire:click="openBulkEqualizeTranslationKeyModal"
                        >
                            {{ __('Bulk') }}
                            <flux:badge
                                size="sm"
                                color="{{ $bulkEqualizeContext['can_confirm'] ?? false ? 'amber' : 'zinc' }}"
                            >
                                {{ $bulkEqualizeContext['selected_count'] }}
                            </flux:badge>
                        </flux:button>
                    </x-ui.tooltip.simple>

                    <x-ui.tooltip.simple
                        :title="__('Clear bulk selection')"
                        :text="__('Removes the selected findings from the bulk translation-key review.')"
                    >
                        <flux:button
                            class="h-6 w-6"
                            type="button"
                            size="xs"
                            variant="subtle"
                            color="zinc"
                            icon="x"
                            wire:click="clearBulkEqualizeSelection"
                        />
                    </x-ui.tooltip.simple>
                </div>
            @endif
        </div>
    </flux:table.column>
</flux:table.columns>
