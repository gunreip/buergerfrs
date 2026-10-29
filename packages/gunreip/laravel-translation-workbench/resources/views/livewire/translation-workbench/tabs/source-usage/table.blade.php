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
            <flux:tooltip content="{{ __('Source of the case') }}">
                <flux:callout.heading class="ml-2">
                    {{ __('Source') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
        {{-- Header Column Expression --}}
        <flux:table.column
            sortable
            :sorted="$sortColumn === 'expression'"
            :direction="$sortDirection"
            wire:click="sortBy('expression')"
        >
            <flux:tooltip content="{{ __('Expression of the case') }}">
                <flux:callout.heading>
                    {{ __('Expression') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
        {{-- Header Column Status --}}
        <flux:table.column
            sortable
            :sorted="$sortColumn === 'status'"
            :direction="$sortDirection"
            wire:click="sortBy('status')"
        >
            <flux:tooltip content="{{ __('Status of the case') }}">
                <flux:callout.heading>
                    {{ __('Status') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
        {{-- Header Column Action --}}
        <flux:table.column
            class="w-36"
            align="center"
        >
            <flux:tooltip content="Action">
                <flux:callout.heading>
                    {{ __('Action') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
    </flux:table.columns>
    {{-- Table Rows --}}
    <flux:table.rows>
        @forelse ($rows as $id => $case)
            <flux:table.row wire:key="case-{{ $id }}">
                {{-- Source --}}
                <flux:table.cell class="max-w-md whitespace-normal break-all">
                    <flux:callout.text class="ml-2">
                        <code>{{ $case['source_path'] }}:{{ $case['source_line'] }}</code>
                    </flux:callout.text>
                </flux:table.cell>
                {{-- Expression --}}
                <flux:table.cell class="wrap-break-word max-w-md whitespace-normal">
                    <flux:callout.text>
                        <code>{{ $case['raw_expression'] }}</code>
                    </flux:callout.text>
                </flux:table.cell>
                {{-- Status --}}
                <flux:table.cell>
                    <flux:callout.text>
                        {{ match ($decisions[$id]['decision'] ?? '') {'database_value' => __('DatabaseValue — review complete'),'translate' => __('Translation required'),default => __('Decision required')} }}
                    </flux:callout.text>
                </flux:table.cell>
                {{-- Action --}}
                <flux:table.cell>
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
