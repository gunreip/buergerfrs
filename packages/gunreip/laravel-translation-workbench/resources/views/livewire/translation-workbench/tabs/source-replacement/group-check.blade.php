@if ($sourceGroupCheck !== [])
    <flux:callout
        class="mb-3"
        icon="mouse-pointer-click"
        :color="$sourceGroupCheck['attention'] === [] ? 'green' : 'amber'"
        :heading="__('Selected findings — source position check')"
    >
        <flux:callout.text>
            {{ __(':total findings checked · :confirmed positions confirmed · :attention need attention.', [
                'total' => $sourceGroupCheck['total'],
                'confirmed' => $sourceGroupCheck['confirmed'],
                'attention' => count($sourceGroupCheck['attention']),
            ]) }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Checked at:') }}
            <x-ui.date-time.date-time :value="$sourceGroupCheck['checked_at']" />
        </flux:callout.text>
        <flux:callout.text>
            {{ __('This result covers the entire selected key group, regardless of pagination and filters. It confirms source positions only, not replacement readiness. Files are not locked; a later change requires a new check. No changes were saved.') }}
        </flux:callout.text>

        @if ($sourceGroupCheck['attention'] !== [])
            {{-- Table for findings that need attention --}}
            <flux:table container:class="max-h-64 mt-3 overflow-auto rounded-t-lg">
                {{-- Table Header Columns --}}
                <flux:table.columns
                    class="bg-white dark:bg-zinc-800"
                    sticky
                >
                    {{-- Table Header Column Finding --}}
                    <flux:table.column>
                        <flux:callout.heading class="ml-2">
                            {{ __('Finding') }}
                        </flux:callout.heading>
                    </flux:table.column>
                    {{-- Table Header Column Observation --}}
                    <flux:table.column>
                        <flux:callout.heading>
                            {{ __('Observation') }}
                        </flux:callout.heading>
                    </flux:table.column>
                    {{-- Table Header Column Actions --}}
                    <flux:table.column>
                        <flux:callout.heading>
                            {{ __('Actions') }}
                        </flux:callout.heading>
                    </flux:table.column>
                </flux:table.columns>
                {{-- Table Body Rows --}}
                <flux:table.rows>
                    @foreach ($sourceGroupCheck['attention'] as $result)
                        {{-- Table Body Row --}}
                        <flux:table.row :key="'source-group-result-'.$result['id']">
                            {{-- Table Body Row Column Finding --}}
                            <flux:table.cell>
                                <flux:callout.text>
                                    #{{ $result['id'] }}
                                </flux:callout.text>
                            </flux:table.cell>
                            {{-- Table Body Row Column Observation --}}
                            <flux:table.cell class="whitespace-normal">
                                <flux:callout.text>
                                    {{ __($result['detail']) }}
                                </flux:callout.text>
                            </flux:table.cell>
                            {{-- Table Body Row Column Actions --}}
                            <flux:table.cell class="flex items-center gap-1">
                                <flux:button.group>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="openSourceComparison({{ $result['id'] }})"
                                        wire:loading.attr="disabled"
                                    >
                                        <flux:icon.git-compare class="size-5" />
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="openSourceComparison({{ $result['id'] }})"
                                        wire:loading.attr="disabled"
                                    >
                                        {{ __('Compare source') }}
                                    </flux:button>
                                </flux:button.group>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </flux:callout>
@endif
