<flux:modal
    class="w-full max-w-2xl"
    name="source-usage-decision"
    wire:model="modalOpen"
>
    <div class="space-y-4">
        <flux:heading size="lg">{{ __('Decide source usage') }}</flux:heading>
        @if ($selectedCase)
            @if ($sourceChanged)
                <flux:callout color="amber">
                    {{ __('This source file has changed since capture. Refresh the scan evidence before saving a decision.') }}
                </flux:callout>
            @endif
            <flux:text class="break-all">
                <code>{{ $selectedCase['source_path'] }}:{{ $selectedCase['source_line'] }}</code>
            </flux:text>
            <flux:textarea
                readonly
                :label="__('Source expression')"
            >{{ $selectedCase['raw_expression'] }}</flux:textarea>
            <flux:text>
                {{ __('This decision applies only to this source occurrence. DatabaseValue records that its values should be displayed unchanged; it does not rewrite existing calls or remove translations. Translate marks further translation work as required.') }}
            </flux:text>
            @if (isset($decisions[$selected]))
                <flux:text>{{ __('Last decision') }}: {{ $decisions[$selected]['occurred_at'] }} ·
                    {{ __('User ID') }} {{ $decisions[$selected]['actor_id'] }}</flux:text>
            @endif
            <flux:select
                wire:model="decision"
                :label="__('Decision')"
                :placeholder="__('Choose a decision')"
            >
                <flux:select.option value="translate">{{ __('Translate — translation work required') }}
                </flux:select.option>
                <flux:select.option value="database_value">
                    {{ __('DatabaseValue — display unchanged, review complete') }}</flux:select.option>
            </flux:select>
            @include('translation-workbench::livewire.translation-workbench.shared.reason-select', ['reasonField' => 'reason', 'reasonLabel' => __('Reason')])
            <flux:error name="decision" />
            <flux:button
                wire:click="save"
                variant="primary"
                :disabled="$sourceChanged"
            >{{ __('Save decision') }}</flux:button>
        @endif
    </div>
</flux:modal>
