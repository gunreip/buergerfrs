@if ($findingLineage)
    <flux:callout class="my-3 shrink-0" color="sky">
        <flux:callout.heading>{{ __('Finding source-position history') }}</flux:callout.heading>
        <flux:callout.text>{{ __('A moved source occurrence receives a new finding ID. Its predecessor remains archived. Key IDs identify the translation task; they are not finding IDs.') }}</flux:callout.text>
        @if (collect($findingLineage)->firstWhere('role', 'selected')['status'] === 'obsolete')
            <flux:callout.text>{{ __('Archived finding — read-only history. The source location below is historical and may no longer contain this expression.') }}</flux:callout.text>
        @endif
        <flux:table container:class="mt-2 max-h-48 overflow-auto">
            <flux:table.columns>
                <flux:table.column>{{ __('Version / Finding') }}</flux:table.column>
                <flux:table.column>{{ __('Source position') }}</flux:table.column>
                <flux:table.column>{{ __('Key assignments') }}</flux:table.column>
                <flux:table.column>{{ __('Open') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($findingLineage as $version)
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal">
                            <div>{{ match ($version['role']) { 'predecessor' => __('Predecessor'), 'successor' => __('Successor'), default => __('Opened finding') } }} · F#{{ $version['id'] }}</div>
                            <flux:badge size="sm" :color="$version['status'] === 'active' ? 'green' : 'zinc'">{{ __($version['status']) }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-all font-mono">{{ $version['path'] }}:{{ $version['line'] }}{{ $version['column'] !== null ? ':'.$version['column'] : '' }}</flux:table.cell>
                        <flux:table.cell class="whitespace-normal">
                            <div class="flex flex-wrap gap-2">
                                @forelse ($version['keys'] as $link)
                                    <flux:badge size="sm" :color="$link['status'] === 'active' ? 'sky' : 'zinc'">K#{{ $link['key_id'] }} · {{ __($link['status']) }}</flux:badge>
                                @empty
                                    {{ __('No linked key records.') }}
                                @endforelse
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button type="button" size="xs" variant="outline" icon="clock"
                                wire:click="openFindingVersion({{ $version['id'] }})" wire:loading.attr="disabled" wire:target="openFindingVersion"
                                :tooltip="__('Open read-only history for finding #:id', ['id' => $version['id']])">
                                {{ __('History') }}
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:callout>
@endif
