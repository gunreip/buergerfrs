<flux:modal
    class="w-full max-w-6xl"
    name="workbench-publication-languages"
    wire:model="publicationSelectionOpen"
>
    <x-translation-workbench::ui.common.modal-loading
        target="preparePublication, openWorkflowReason"
    />
    @if ($publicationSelectionOpen)
        <div class="space-y-4">
            <flux:heading
                class="flex items-center gap-2"
                size="xl"
            >
                <flux:icon.square-check
                    class="shrink-0"
                    size="xl"
                />
                {{ __('Select languages for publication') }}
            </flux:heading>
            <flux:callout
                size="sm"
                variant="info"
                icon="languages"
                heading="{{ __('Publication languages') }}"
                text="{{ __('Select the approved English source, main language and language variants to publish together. Drafts need approval first. The next step previews the file changes; it does not write files yet.') }}"
            />

            @include(
                'translation-workbench::livewire.translation-workbench.shared.workflow-language-selection',
                [
                    'languageChoices' => $publicationChoices,
                    'languageSelection' => 'publicationLocales',
                    'languageApprovalRequired' => true,
                ]
            )
            @include('translation-workbench::livewire.translation-workbench.shared.reason-select', [
                'reasonField' => 'publicationReason',
                'reasonLabel' => __('Reason for publication'),
                'reasonInputLocale' => $publicationReasonLocale,
                'reasonLocaleField' => 'publicationReasonLocale',
                'reasonSearchId' => 'publication-reason-search-' . $this->getId(),
            ])

            <flux:error name="publication" />

            <div class="flex justify-end gap-3">
                <flux:button.group>
                    <flux:button
                        size="sm"
                        variant="filled"
                        color="sky"
                        wire:click="preparePublication"
                        :disabled="$publicationLocales === []"
                        wire:loading.attr="disabled"
                    >
                        <flux:icon.book-plus
                            class="shrink-0"
                            size="sm"
                        />
                    </flux:button>
                    <flux:button
                        size="sm"
                        variant="filled"
                        color="sky"
                        wire:click="preparePublication"
                        :disabled="$publicationLocales === []"
                        wire:loading.attr="disabled"
                    >
                        {{ __('Prepare publication preview') }}
                    </flux:button>
                </flux:button.group>
            </div>
        </div>
    @endif
</flux:modal>
