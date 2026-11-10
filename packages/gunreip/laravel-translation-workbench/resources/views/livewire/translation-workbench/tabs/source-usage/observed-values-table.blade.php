    {{-- Table --}}
    <flux:table container:class="max-h-80 overflow-auto rounded-t-lg mt-2">
        {{-- Table Header --}}
        <flux:table.columns
            class="bg-white dark:bg-zinc-800"
            sticky
        >
            {{-- Raw Value --}}
            <flux:table.column>
                <flux:tooltip>
                    <flux:callout.heading class="ml-2">
                        {{ __('Raw value') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Raw value')"
                        :text="__('Value observed in the application table at the displayed observation time. NULL and an empty string are shown separately. This preview does not determine whether the value requires translation.')"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Stored Source Label --}}
            <flux:table.column>
                <flux:tooltip>
                    <flux:callout.heading>
                        {{ __('Stored source label') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Stored source label')"
                        :text="__('Label stored alongside the observed value in its source table, when available. This is not a translation retrieved from the Workbench or a language file. The badge indicates that no source label was available in this observation.')"
                    />
                </flux:tooltip>
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
                        @if (($row['source_label'] ?? null) === null)
                            <flux:badge size="sm" color="zinc">
                                {{ __('No label stored in this source') }}
                            </flux:badge>
                        @else
                            <flux:callout.text>{{ $row['source_label'] }}</flux:callout.text>
                        @endif
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
