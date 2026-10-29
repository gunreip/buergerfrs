<flux:modal
    class="md:w-lg"
    name="workbench-create-change-reason"
    wire:model="changeReasonOpen"
>
    <form
        class="space-y-4"
        wire:submit="createChangeReason"
    >
        <div class="-mt-1 flex items-start justify-between gap-3 pe-6">
            <flux:heading class="flex items-center gap-2" size="xl">
                <flux:icon.pencil-line class="shrink-0" size="sm" />
                {{ __('Create a translation reason') }}
            </flux:heading>
            <x-translation-workbench::ui.common.input-language-selector
                class="shrink-0"
                wire:model.live="newChangeReasonLocale"
                :target="$this->reasonTargetLocale()"
                :countries="$translationFlagCountries"
                :focus="'change-reason-text-'.$this->getId()"
            />
        </div>
        <flux:text>
            {{ __('The reason is selected here and saved with the translation draft. Saved reasons can be reused.') }}
        </flux:text>
        <flux:field>
            <flux:label>
                {{ __('Reason for a translation change') }}
            </flux:label>
            <flux:textarea
                :id="'change-reason-text-'.$this->getId()"
                class="min-h-12"
                wire:key="change-reason-input-{{ $newChangeReasonLocale }}"
                wire:model="newChangeReason"
                :lang="$newChangeReasonLocale"
                spellcheck="true"
                rows="3"
            />

            <flux:error name="newChangeReason" />

        </flux:field>

        <div class="flex justify-end gap-2">
            <flux:button.group>
                <flux:button
                    type="submit"
                    size="sm"
                    variant="primary"
                    color="green"
                    icon="check"
                />
                <flux:button
                    type="submit"
                    size="sm"
                    variant="primary"
                    color="green"
                >
                    {{ __('Use reason') }}
                </flux:button>
            </flux:button.group>
        </div>
    </form>
</flux:modal>
