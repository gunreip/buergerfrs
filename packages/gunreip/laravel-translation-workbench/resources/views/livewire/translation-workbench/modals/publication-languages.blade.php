<flux:modal name="workbench-publication-languages" wire:model="publicationSelectionOpen" class="w-full max-w-4xl">
    @if ($publicationSelectionOpen)
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Select languages for publication') }}</flux:heading>
            <flux:text>{{ __('Select the approved English source, main language and language variants to publish together. Drafts need approval first. The next step previews the file changes; it does not write files yet.') }}</flux:text>
            @include('translation-workbench::livewire.translation-workbench.shared.workflow-language-selection', [
                'languageChoices' => $publicationChoices,
                'languageSelection' => 'publicationLocales',
                'languageApprovalRequired' => true,
            ])
            @include('translation-workbench::livewire.translation-workbench.shared.reason-select', ['reasonField' => 'publicationReason', 'reasonLabel' => __('Reason for publication')])
            <flux:error name="publication" />
            <div class="flex justify-end gap-3">
                <flux:modal.close><flux:button>{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button wire:click="preparePublication" :disabled="$publicationLocales === []" wire:loading.attr="disabled">{{ __('Prepare publication preview') }}</flux:button>
            </div>
        </div>
    @endif
</flux:modal>
