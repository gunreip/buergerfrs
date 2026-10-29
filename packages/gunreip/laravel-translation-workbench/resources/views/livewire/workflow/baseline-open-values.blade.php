@php
    $openValues = app(\Gunreip\TranslationWorkbench\Support\BaselineOpenValues::class)->inspect($baselinePreview['snapshot'] ?? null, $baselineRehearsal);
@endphp
<flux:accordion>
    <flux:accordion.item>
        <flux:accordion.heading>{{ __('Open published values by key') }}</flux:accordion.heading>
        <flux:accordion.content>
            <flux:callout.text>
                {{ __('This list covers blocked or unassigned published values only. It does not list all untranslated findings or the pending dynamic cases. One row groups the affected locales of one key and status. Selecting a key opens its existing read-only preview above.') }}
            </flux:callout.text>
            @if ($openValues['state'] === 'verified')
                <flux:callout.text>{{ __('Affected locale values') }}: {{ $openValues['values'] }}</flux:callout.text>
                <flux:table container:class="max-h-96 overflow-auto">
                    <flux:table.columns>
                        <flux:table.column>{{ __('Translation key') }}</flux:table.column>
                        <flux:table.column>{{ __('Locales') }}</flux:table.column>
                        <flux:table.column>{{ __('Open question') }}</flux:table.column>
                        <flux:table.column>{{ __('Preview') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($openValues['rows'] as $row)
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal break-all"><code>{{ $row['key'] }}</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal">{{ implode(', ', $row['locales']) }}</flux:table.cell>
                                <flux:table.cell class="whitespace-normal">
                                    {{ $row['state'] === 'blocked_structure' ? __('Blocked key structure')
                                        : ($row['context'] === 'option_source_evidenced' ? __('Option source identified; assignment still open') : __('Source assignment open')) }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:button size="sm" variant="outline"
                                        wire:click="selectOpenBaselineKey({{ \Illuminate\Support\Js::from($row['key']) }})">
                                        {{ __('Select key') }}
                                    </flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:callout.text>{{ __('Matching detailed evidence is unavailable or does not reconcile with the recorded totals. No dataset list is shown.') }}</flux:callout.text>
            @endif
        </flux:accordion.content>
    </flux:accordion.item>
</flux:accordion>
