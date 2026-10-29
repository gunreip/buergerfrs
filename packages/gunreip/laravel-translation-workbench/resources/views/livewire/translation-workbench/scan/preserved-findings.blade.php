<flux:button
    variant="outline"
    size="sm"
    wire:click="closePreservedFindings"
>{{ __('Back to scan results') }}</flux:button>
<flux:heading>{{ __('Preserved findings') }}</flux:heading>
<flux:text class="break-all">{{ $scanReviewPath }}</flux:text>
<flux:text>
    {{ __('These findings were preserved during this scan. Status and key assignment show their current state; open a finding to inspect its history.') }}
</flux:text>
<flux:table>
    <flux:table.columns>
        <flux:table.column>{{ __('Finding') }}</flux:table.column>
        <flux:table.column>{{ __('Position') }}</flux:table.column>
        <flux:table.column>{{ __('Captured source value') }}</flux:table.column>
        <flux:table.column>{{ __('Current status / key') }}</flux:table.column>
    </flux:table.columns>
    <flux:table.rows>
        @forelse ($preservedRows as $preserved)
            <flux:table.row :key="'preserved-'.$preserved['id']">
                <flux:table.cell>
                    <flux:button
                        variant="outline"
                        size="sm"
                        wire:click="open({{ $preserved['id'] }})"
                    >#{{ $preserved['id'] }}</flux:button>
                </flux:table.cell>
                <flux:table.cell>
                    {{ $preserved['source_line'] }}:{{ $preserved['source_column'] }}
                </flux:table.cell>
                <flux:table.cell class="whitespace-normal break-all">
                    {{ $preserved['raw_source_code_literal'] ?? '—' }}</flux:table.cell>
                <flux:table.cell class="whitespace-normal break-all">
                    {{ $preserved['status'] === 'obsolete' ? __('Archived') : __('Active') }} ·
                    {{ $preserved['full_name'] ?? __('No active key assignment') }}
                </flux:table.cell>
            </flux:table.row>
        @empty
            <flux:table.row>
                <flux:table.cell colspan="4">
                    {{ __('No existing findings were preserved for this file.') }}
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>
<flux:pagination :paginator="$preservedRows" />
