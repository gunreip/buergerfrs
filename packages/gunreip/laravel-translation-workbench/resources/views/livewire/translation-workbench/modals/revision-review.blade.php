<flux:modal
    class="w-full max-w-4xl"
    name="generation-revision-confirmation"
    wire:model="reviewOpen"
>
    @if ($reviewDecision)
        <div class="space-y-4">
            <flux:heading size="lg">
                {{ $reviewDecision['approve'] ? __('Confirm revision approval') : __('Confirm approval revocation') }}
            </flux:heading>
            <flux:text class="break-all">{{ __('Key') }} #{{ $context['finding']['key_id'] }} ·
                {{ $context['finding']['full_name'] }}</flux:text>
            <flux:text>{{ __('Active linked findings') }}: {{ count($context['review']['context']['usages']) }}</flux:text>
            <flux:text>{{ __('Review the saved text for each selected language. English and saved language variants can be approved together. Unsaved changes must be saved or discarded first.') }}</flux:text>
            @include('translation-workbench::livewire.translation-workbench.shared.workflow-language-selection', [
                'languageChoices' => $reviewChoices,
                'languageSelection' => 'reviewLocales',
                'languageApprovalRequired' => false,
            ])
            <flux:text>
                {{ __('This decision is recorded with your user and reason. It does not write language files or source code.') }}
            </flux:text>
            @include('translation-workbench::livewire.translation-workbench.shared.reason-select', ['reasonField' => 'reviewReason', 'reasonLabel' => __('Reason for review decision')])
            <flux:error name="review" />
            <div class="flex justify-end gap-3">
                <flux:button
                    variant="ghost"
                    wire:click="$set('reviewOpen', false)"
                >{{ __('Cancel') }}</flux:button>
                <flux:button
                    variant="primary"
                    wire:click="review"
                    :disabled="$reviewLocales === []"
                    wire:loading.attr="disabled"
                >{{ __('Confirm decision') }}</flux:button>
            </div>
        </div>
    @endif
</flux:modal>
