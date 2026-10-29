<flux:modal
    class="w-full max-w-3xl"
    name="generation-source-run"
    wire:model="sourceRunOpen"
>
    @if ($sourceRun)
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Confirm / resume source replacement') }}</flux:heading>
            <flux:text class="break-all">{{ $sourceRun['run'] }} · {{ __($sourceRun['state']) }} ·
                {{ __($sourceRun['file_state']) }}</flux:text>
            <flux:text>{{ $sourceRun['preview']['path'] }} · {{ __('Finding') }}
                #{{ $sourceRun['preview']['finding'] }} → {{ $sourceRun['preview']['key'] }}</flux:text>
            <flux:text>{{ $sourceRun['reason'] }}</flux:text>
            @if ($sourceRun['impacts'] ?? [])
                <flux:heading>{{ __('Affected findings') }}</flux:heading>
                <flux:text>
                    {{ __('Positions are line:column; columns count bytes. Each finding keeps its own key assignment. Findings not listed here remain unchanged.') }}
                </flux:text>
                <div class="max-h-64 overflow-auto">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('Finding') }}</flux:table.column>
                            <flux:table.column>{{ __('Change') }}</flux:table.column>
                            <flux:table.column>{{ __('Before') }}</flux:table.column>
                            <flux:table.column>{{ __('After') }}</flux:table.column>
                            <flux:table.column>{{ __('Translation key') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($sourceRun['impacts'] as $impact)
                                <flux:table.row :key="'source-impact-'.$impact['finding']">
                                    <flux:table.cell>#{{ $impact['finding'] }}</flux:table.cell>
                                    <flux:table.cell>{{ __($impact['kind']) }}</flux:table.cell>
                                    <flux:table.cell>{{ $impact['before_line'] }}:{{ $impact['before_column'] }}
                                    </flux:table.cell>
                                    <flux:table.cell>{{ $impact['after_line'] }}:{{ $impact['after_column'] }}
                                    </flux:table.cell>
                                    <flux:table.cell class="whitespace-normal break-all">
                                        {{ $impact['key_id'] ? '#' . $impact['key_id'] . ' · ' . $impact['key'] : __('Not assigned') }}
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            @endif
            <flux:textarea
                readonly
                rows="3"
                :label="__('Before')"
            >{{ $sourceRun['preview']['before'] }}</flux:textarea>
            <flux:textarea
                readonly
                rows="3"
                :label="__('After')"
            >{{ $sourceRun['preview']['after'] }}</flux:textarea>
            <flux:error name="sourceRun" />
            @if ($sourceRun['state'] === 'pending')
                <flux:text>
                    {{ __('If the file has already been written, resuming only completes the database receipt and successor finding. A changed file or assignment stops the operation.') }}
                </flux:text>
                @if ((int) $sourceRun['actor'] === (int) auth()->id())
                    <flux:button
                        variant="primary"
                        wire:click="applySource"
                    >{{ __('Confirm source replacement / resume') }}</flux:button>
                @endif
                <flux:accordion>
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('Close without changing the source') }}
                        </flux:accordion.heading>
                        <flux:accordion.content>
                            <flux:text>
                                {{ __('Closing is allowed only while the file still matches its original contents. Already written files must be reconciled instead.') }}
                            </flux:text>
                            @include('translation-workbench::livewire.translation-workbench.shared.reason-select', ['reasonField' => 'sourceCloseReason', 'reasonLabel' => __('Reason for closure')])
                            <flux:button
                                variant="outline"
                                wire:click="closeSource"
                            >{{ __('Confirm closure') }}</flux:button>
                        </flux:accordion.content>
                    </flux:accordion.item>
                </flux:accordion>
            @elseif ($sourceRun['state'] === 'complete')
                <flux:callout color="green">{{ __('Source replacement and successor capture completed.') }}
                    @foreach ($sourceRun['result']['successors'] as $successor)
                        <flux:button
                            variant="ghost"
                            size="sm"
                            wire:click="open({{ $successor['successor'] }})"
                        >#{{ $successor['predecessor'] }} → #{{ $successor['successor'] }}</flux:button>
                    @endforeach
                </flux:callout>
            @else
                <flux:text>{{ __('This operation is closed and cannot write the source.') }}</flux:text>
            @endif
        </div>
    @endif
</flux:modal>
