<flux:callout
    color="fuchsia"
    icon="archive"
    heading="{{ __('Preserved findings') }}"
>
    <flux:callout.text>
        {{ __('These findings were preserved during this scan. Status and key assignment show their current state; open a finding to inspect its history.') }}
    </flux:callout.text>

    {{-- Table of preserved findings: --}}
    <flux:table container:class="max-h-120 overflow-auto rounded-t-lg">
        {{-- Table Header --}}
        <flux:table.columns class="bg-white dark:bg-zinc-800">
            {{-- Table Header Column Finding --}}
            <flux:table.column>
                <flux:callout.heading>
                    {{ __('Finding') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Position --}}
            <flux:table.column>
                <flux:callout.heading>
                    {{ __('Position') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Captured source value --}}
            <flux:table.column>
                <flux:callout.heading>
                    {{ __('Captured source value') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Current status / key --}}
            <flux:table.column>
                <flux:callout.heading>
                    {{ __('Current status / key') }}
                </flux:callout.heading>
            </flux:table.column>
        </flux:table.columns>
        {{-- Table Body Rows --}}
        <flux:table.rows>
            @forelse ($preservedRows as $preserved)
                {{-- Table Body Row --}}
                <flux:table.row :key="'preserved-'.$preserved['id']">
                    {{-- Table Body Row Column Finding --}}
                    <flux:table.cell>
                        <flux:button.group>
                            <flux:button
                                variant="outline"
                                size="sm"
                                wire:click="open({{ $preserved['id'] }})"
                            >
                                <flux:icon.square-pen size="5" />
                            </flux:button>
                            <flux:button
                                variant="outline"
                                size="sm"
                                wire:click="open({{ $preserved['id'] }})"
                            >
                                #{{ $preserved['id'] }}
                            </flux:button>
                        </flux:button.group>
                    </flux:table.cell>
                    {{-- Table Body Row Column Position --}}
                    <flux:table.cell>
                        <flux:callout.text>
                            {{ $preserved['source_line'] }}:{{ $preserved['source_column'] }}
                        </flux:callout.text>
                    </flux:table.cell>
                    {{-- Table Body Row Column Captured source value --}}
                    <flux:table.cell class="whitespace-normal break-all">
                        <flux:callout.text>
                            {{ $preserved['raw_source_code_literal'] ?? '—' }}
                        </flux:callout.text>
                    </flux:table.cell>
                    {{-- Table Body Row Column Current status / key --}}
                    <flux:table.cell class="whitespace-normal break-all">
                        <flux:callout.text>
                            {{ $preserved['status'] === 'obsolete' ? __('Archived') : __('Active') }} ·
                            {{ $preserved['full_name'] ?? __('No active key assignment') }}
                        </flux:callout.text>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                {{-- Table Body Row Empty --}}
                <flux:table.row>
                    <flux:table.cell colspan="4">
                        <flux:callout.text>
                            {{ __('No existing findings were preserved for this file.') }}
                        </flux:callout.text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:pagination :paginator="$preservedRows" />

</flux:callout>
