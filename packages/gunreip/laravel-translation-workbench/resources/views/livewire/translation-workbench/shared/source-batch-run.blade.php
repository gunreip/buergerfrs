<div class="space-y-4">
    <flux:callout.heading size="xl">
        <flux:icon.eye />
        {{ __('Confirm / resume selected source replacements') }}
    </flux:callout.heading>
    <flux:callout
        color="sky"
        :heading="__('Key #:key · :count findings', ['key' => $sourceRun['preview']['key_id'], 'count' => count($sourceRun[
            'preview']['items'])])"
    >
        <flux:callout.text>{{ $sourceRun['reason'] }}</flux:callout.text>
        <flux:callout.text>
            {{ __('Each file is written separately. Completed files remain completed if a later file is blocked. Resume continues the unfinished operations; it does not repeat completed replacements.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:table container:class="max-h-96 overflow-auto">
        <flux:table.columns>
            <flux:table.column>{{ __('Source file') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($sourceRun['files'] as $file)
                <flux:table.row :key="$file['run']">
                    <flux:table.cell><x-translation-workbench::ui.common.path-preview
                            :path="$file['path']"
                            :length="60"
                            side="center"
                        /></flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            :color="$file['state'] === 'complete' ? 'green' : ($file['file_state'] === 'original' ? 'amber' :
                                'red')"
                        >{{ __($file['state']) }}</flux:badge>
                        <flux:text>{{ __($file['file_state']) }}</flux:text>
                        @if (!empty($file['error']))
                            <flux:text>{{ $file['error'] }}</flux:text>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="openSourceRun('{{ $file['run'] }}')"
                        >{{ __('Inspect file operation') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                @foreach ($file['impacts'] as $impact)
                    <flux:table.row>
                        <flux:table.cell colspan="3">
                            <flux:text class="tabular-nums">#{{ $impact['finding'] }} · {{ __($impact['kind']) }} ·
                                {{ $impact['before_line'] }}:{{ $impact['before_column'] }} →
                                {{ $impact['after_line'] }}:{{ $impact['after_column'] }} · {{ $impact['key'] }}
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            @endforeach
        </flux:table.rows>
    </flux:table>
    <flux:error name="sourceRun" />
    @if ($sourceRun['state'] === 'pending')
        @if ((int) $sourceRun['actor'] === (int) auth()->id())
            <flux:button
                variant="outline"
                wire:click="applySource"
                wire:loading.attr="disabled"
            >{{ __('Confirm selected replacements / resume') }}</flux:button>
        @endif
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>{{ __('Close unfinished file operations') }}</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>
                        {{ __('Only unfinished operations whose source files are still unchanged can be closed. Completed files are not reverted.') }}
                    </flux:text>
                    @include('translation-workbench::livewire.translation-workbench.shared.reason-select', [
                        'reasonField' => 'sourceCloseReason',
                        'reasonLabel' => __('Reason for closure'),
                    ])
                    <flux:button
                        variant="outline"
                        wire:click="closeSource"
                        wire:loading.attr="disabled"
                    >{{ __('Confirm closure') }}</flux:button>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    @else
        <flux:callout :color="$sourceRun['state'] === 'complete' ? 'green' : 'amber'">
            {{ $sourceRun['state'] === 'complete' ? __('All selected source replacements completed.') : __('This group contains closed file operations. See each file status above.') }}
        </flux:callout>
    @endif
</div>
