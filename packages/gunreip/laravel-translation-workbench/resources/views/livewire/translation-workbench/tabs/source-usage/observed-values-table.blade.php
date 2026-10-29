{{-- Table --}}
<flux:table container:class="max-h-[100vh] overflow-x-auto rounded-t-lg">
    {{-- Table Header --}}
    <flux:table.columns
        class="bg-white dark:bg-zinc-800"
        sticky
    >
        {{-- Raw Value --}}
        <flux:table.column>
            <flux:callout.heading class="ml-2">
                {{ __('Raw value') }}
            </flux:callout.heading>
        </flux:table.column>
        {{-- Stored Source Label --}}
        <flux:table.column>
            <flux:callout.heading>
                {{ __('Stored source label') }}
            </flux:callout.heading>
        </flux:table.column>
    </flux:table.columns>
    {{-- Table Rows --}}
    <flux:table.rows>
        @forelse ($group['rows'] as $row)
            <flux:table.row>
                {{-- Raw Values --}}
                <flux:table.cell class="wrap-break-word whitespace-normal">
                    <flux:callout.text class="ml-2">
                        {{ $row['value'] === null ? 'NULL' : ($row['value'] === '' ? __('Empty string') : $row['value']) }}
                    </flux:callout.text>
                </flux:table.cell>
                {{-- Stored Source Label --}}
                <flux:table.cell class="wrap-break-word whitespace-normal">
                    <flux:callout.text>
                        {{ $row['source_label'] ?? __('No label stored in this source') }}
                    </flux:callout.text>
                </flux:table.cell>
            </flux:table.row>
        @empty
            <flux:table.row>
                <flux:table.cell colspan="2">
                    <flux:callout.text>
                        {{ __('Observed successfully; no matching rows.') }}
                    </flux:callout.text>
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>
