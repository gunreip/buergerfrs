<flux:modal
    class="w-full max-w-6xl"
    name="generation-revision-confirmation"
    wire:model="reviewOpen"
>
    <x-translation-workbench::ui.common.modal-loading target="review, openWorkflowReason" />
    @if ($reviewDecision)
        <div class="space-y-4">
            <div class="-mt-1 flex items-start justify-between gap-3 pe-6">
                <flux:heading
                    class="flex items-center gap-2"
                    size="xl"
                >
                    <flux:icon.square-check
                        class="shrink-0"
                        class="soize-8"
                    />
                    {{ $reviewDecision['approve'] ? __('Confirm revision approval') : __('Confirm approval revocation') }}
                </flux:heading>
            </div>

            <flux:callout
                color="sky"
                icon="square-check"
                heading="{{ __('Revision approval') }}"
            >
                <x-translation-workbench::ui.common.details>
                    <x-translation-workbench::ui.common.details.item
                        class="tabular-nums"
                        :label="__('Key ID')"
                        :item="'#' . $context['finding']['key_id']"
                    />
                    <x-translation-workbench::ui.common.details.item :label="__('Translation key')">
                        <x-translation-workbench::ui.common.text-preview
                            :mono="true"
                            :text="$context['finding']['full_name']"
                            :length="60"
                            side="center"
                            :heading="__('Translation key')"
                        />
                    </x-translation-workbench::ui.common.details.item>
                    <x-translation-workbench::ui.common.details.item
                        class="tabular-nums"
                        :label="__('Active linked findings')"
                        :item="count($context['review']['context']['usages'])"
                    />
                </x-translation-workbench::ui.common.details>

                <flux:callout.text>
                    {{ __('Review the saved text for each selected language. English and saved language variants can be approved together. Unsaved changes must be saved or discarded first.') }}
                </flux:callout.text>
            </flux:callout>

            @include(
                'translation-workbench::livewire.translation-workbench.shared.workflow-language-selection',
                [
                    'languageChoices' => $reviewChoices,
                    'languageSelection' => 'reviewLocales',
                    'languageApprovalRequired' => false,
                ]
            )

            @include('translation-workbench::livewire.translation-workbench.shared.reason-select', [
                'reasonField' => 'reviewReason',
                'reasonLabel' => __('Reason for review decision'),
                'reasonInputLocale' => $reviewReasonLocale,
                'reasonLocaleField' => 'reviewReasonLocale',
                'reasonSearchId' => 'revision-review-reason-search-' . $this->getId(),
            ])

            <flux:error name="review" />

            <div class="flex justify-end gap-3">
                <flux:button.group>
                    <flux:button
                        variant="filled"
                        color="green"
                        size="sm"
                        :disabled="$reviewLocales === []"
                        wire:click="review"
                        wire:loading.attr="disabled"
                    >
                        <flux:icon.check-check size="5" />
                    </flux:button>
                    <flux:button
                        variant="filled"
                        color="green"
                        size="sm"
                        :disabled="$reviewLocales === []"
                        wire:click="review"
                        wire:loading.attr="disabled"
                    >
                        {{ __('Confirm decision') }}
                    </flux:button>
                </flux:button.group>
            </div>
        </div>
    @endif
</flux:modal>
