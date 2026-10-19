<div wire:key="translation-workbench-key-assignment-confirm-host">
    <flux:modal
        class="w-full max-w-xl"
        name="translation-workbench-key-assignment-confirm"
        wire:key="translation-workbench-key-assignment-confirm-modal"
        wire:model="keyAssignmentConfirmOpen"
    >
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Confirm key assignment') }}</flux:heading>
            @if ($pendingKeyAssignment)
                <flux:callout
                    color="red"
                    icon="key"
                >
                    <flux:callout.heading>
                        {{ __('Finding #:finding → Key #:key', ['finding' => $pendingKeyAssignment['finding_id'], 'key' => $pendingKeyAssignment['key_id']]) }}
                    </flux:callout.heading>
                    <flux:callout.text class="break-all font-mono">{{ $pendingKeyAssignment['suggested_key'] }}
                    </flux:callout.text>
                    <flux:callout.text>
                        {{ __('The selected link is retained and the other active links of this finding are archived. Key records, other findings and translation values are retained.') }}
                    </flux:callout.text>
                </flux:callout>
                <flux:error name="translationKeyValue" />
                <div class="flex justify-end gap-3">
                    <flux:modal.close>
                        <flux:button
                            type="button"
                            variant="outline"
                        >{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button
                        type="button"
                        variant="danger"
                        wire:click="confirmFindingKeyAssignment"
                        wire:loading.attr="disabled"
                        wire:target="confirmFindingKeyAssignment"
                    >
                        {{ __('Confirm key assignment') }}
                    </flux:button>
                </div>
            @endif
        </div>
    </flux:modal>

</div>
