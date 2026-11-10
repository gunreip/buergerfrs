{{-- Table --}}
<flux:table container:class="max-h-[100vh] overflow-x-auto rounded-t-lg">
    {{-- Table Header --}}
    <flux:table.columns
        class="bg-white dark:bg-zinc-800"
        sticky
    >
        {{-- Header Column Source --}}
        <flux:table.column
            sortable
            :sorted="$sortColumn === 'source'"
            :direction="$sortDirection"
            wire:click="sortBy('source')"
        >
            <flux:tooltip>
                <flux:callout.heading class="ml-2">
                    {{ __('Source') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Source')"
                    :text="__('The source file and line number of the case.')"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Header Column Expression --}}
        <flux:table.column
            sortable
            :sorted="$sortColumn === 'expression'"
            :direction="$sortDirection"
            wire:click="sortBy('expression')"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Expression') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Expression')"
                    :text="__('The raw expression of the case.')"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Header Column Status --}}
        <flux:table.column
            sortable
            :sorted="$sortColumn === 'status'"
            :direction="$sortDirection"
            wire:click="sortBy('status')"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Status') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Status')"
                    :text="__('Status of the case')"
                />
            </flux:tooltip>
        </flux:table.column>
        {{-- Header Column Action --}}
        <flux:table.column
            class="w-48"
            align="center"
        >
            <flux:tooltip>
                <flux:callout.heading>
                    {{ __('Action') }}
                </flux:callout.heading>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Action')"
                    :text="__('Available actions for the case.')"
                />
            </flux:tooltip>
        </flux:table.column>
    </flux:table.columns>
    {{-- Table Rows --}}
    <flux:table.rows>
        @forelse ($rows as $id => $case)
            <flux:table.row wire:key="case-{{ $id }}">
                {{-- Source --}}
                <flux:table.cell class="max-w-md whitespace-normal break-all">
                    <x-translation-workbench::ui.common.path-preview
                        class="ml-2"
                        :path="$case['source_path']"
                        :line="$case['source_line']"
                        :length="100"
                        side="center"
                    />
                </flux:table.cell>
                {{-- Expression --}}
                <flux:table.cell class="wrap-break-word max-w-md whitespace-normal">
                    <flux:callout.text>
                        <code>{{ $case['raw_expression'] }}</code>
                    </flux:callout.text>
                </flux:table.cell>
                {{-- Status --}}
                <flux:table.cell>
                    <flux:badge
                        size="sm"
                        :color="match($decisions[$id]['decision'] ?? '') {
                            'database_value' => 'green',
                            'translate' => 'sky',
                            default => 'amber',
                        }"
                    >
                        {{ match ($decisions[$id]['decision'] ?? '') {'database_value' => __('DatabaseValue — review complete'),'translate' => __('Translation required'),default => __('Decision required')} }}
                    </flux:badge>
                </flux:table.cell>
                {{-- Action --}}
                <flux:table.cell class="flex items-center gap-1">
                    <flux:tooltip>
                        <flux:button
                            size="sm"
                            variant="outline"
                            square
                            wire:click="openUsageWorkflow('{{ $id }}')"
                            :aria-label="__('Workflow status')"
                        >
                            <flux:icon.queue-list size="5" />
                        </flux:button>
                        <x-translation-workbench::ui.common.tooltip-content
                            :heading="__('Workflow status')"
                            :text="__(
                                'Show the source usage decision and the next step for this source occurrence.',
                            )"
                        />
                    </flux:tooltip>
                    <flux:tooltip>
                        <flux:button.group>
                            <flux:button
                                size="sm"
                                variant="outline"
                                wire:click="open('{{ $id }}')"
                            >
                                <flux:icon.eye class="size-5" />
                            </flux:button>
                            <flux:button
                                size="sm"
                                variant="outline"
                                wire:click="open('{{ $id }}')"
                            >
                                {{ __('Review') }}
                            </flux:button>
                        </flux:button.group>
                        <x-translation-workbench::ui.common.tooltip-content
                            :heading="__('Review')"
                            :text="__('Open this case for review.')"
                        />
                    </flux:tooltip>
                </flux:table.cell>
            </flux:table.row>
        @empty
            <flux:table.row>
                <flux:table.cell colspan="4">
                    <flux:callout.text>{{ __('No matching open cases.') }}</flux:callout.text>
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>
<flux:pagination :paginator="$rows" />
