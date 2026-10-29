<flux:error name="publication" />

@if ($publications)
    <flux:callout
        color="sky"
        icon="package-open"
        heading="{{ __('Publication runs') }}"
        text="{{ __('All open runs and the latest 20 completed or closed runs. Reopen a prepared or interrupted run to continue or close it explicitly.') }}"
    >
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>
                    <flux:callout.heading icon="spotlight">
                        {{ __('Show publication runs') }}
                    </flux:callout.heading>
                </flux:accordion.heading>
                <flux:accordion.content>
                    <div class="max-h-64 overflow-auto">
                        {{-- Table --}}
                        <flux:table container:class="max-h-80 overflow-x-auto rounded-t-lg">
                            {{-- Table Header Row --}}
                            <flux:table.columns class="bg-white dark:bg-zinc-800">
                                {{-- Table Header Column Run / User --}}
                                <flux:table.column>
                                    <flux:callout.heading class="ml-2">
                                        {{ __('Run / user') }}
                                    </flux:callout.heading>
                                </flux:table.column>
                                {{-- Table Header Column Status --}}
                                <flux:table.column>
                                    <flux:callout.heading>
                                        {{ __('Status') }}
                                    </flux:callout.heading>
                                </flux:table.column>
                                {{-- Table Header Column Reason --}}
                                <flux:table.column>
                                    <flux:callout.heading>
                                        {{ __('Reason') }}
                                    </flux:callout.heading>
                                </flux:table.column>
                                {{-- Table Header Column Open --}}
                                <flux:table.column>
                                    <flux:callout.heading>
                                        {{ __('Open') }}
                                    </flux:callout.heading>
                                </flux:table.column>
                            </flux:table.columns>
                            {{-- Table Rows --}}
                            <flux:table.rows>
                                @foreach ($publications as $run)
                                    {{-- Table Row --}}
                                    <flux:table.row :key="'publication-'.$run['id']">
                                        {{-- Table Row Column Run / User --}}
                                        <flux:table.cell class="whitespace-normal break-all">
                                            <flux:callout.text class="ml-2">
                                                {{ $run['id'] }} · #{{ $run['actor'] }}
                                            </flux:callout.text>
                                        </flux:table.cell>
                                        {{-- Table Row Column Status --}}
                                        <flux:table.cell>
                                            <flux:callout.text>
                                                {{ __($run['state']) }}
                                            </flux:callout.text>
                                        </flux:table.cell>
                                        {{-- Table Row Column Reason --}}
                                        <flux:table.cell class="whitespace-normal">
                                            <flux:callout.text>
                                                {{ $run['reason'] }}
                                            </flux:callout.text>
                                            @if ($run['error'])
                                                <flux:text class="text-red-600">
                                                    {{ $run['error'] }}
                                                </flux:text>
                                            @endif
                                        </flux:table.cell>
                                        {{-- Table Row Column Open --}}
                                        <flux:table.cell>
                                            <flux:button.group>
                                                <flux:button
                                                    size="sm"
                                                    icon="package-open"
                                                    variant="outline"
                                                    wire:click="openPublication('{{ $run['id'] }}')"
                                                />
                                                <flux:button
                                                    size="sm"
                                                    variant="outline"
                                                    wire:click="openPublication('{{ $run['id'] }}')"
                                                >
                                                    {{ __('Open') }}
                                                </flux:button>
                                            </flux:button.group>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </flux:callout>
@endif
