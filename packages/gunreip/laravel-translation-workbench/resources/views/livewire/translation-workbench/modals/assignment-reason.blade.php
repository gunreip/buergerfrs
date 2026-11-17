<flux:modal
    class="w-full max-w-3xl"
    name="workbench-create-assignment-reason"
    wire:model="assignmentReasonOpen"
>
    <x-translation-workbench::ui.common.modal-loading target="createAssignmentReason" />
    <form
        class="space-y-4"
        wire:submit="createAssignmentReason"
    >
        <div class="-mt-1 flex items-start justify-between gap-3 pe-6">
            <flux:heading
                class="flex items-center gap-2"
                size="xl"
            >
                <flux:icon.pencil-line
                    class="shrink-0"
                    size="sm"
                />
                {{ __('Create a translation key assignment reason') }}
            </flux:heading>
            <x-translation-workbench::ui.common.input-language-selector
                class="shrink-0"
                wire:model.live="newAssignmentReasonLocale"
                :target="$this->reasonTargetLocale()"
                :countries="$translationFlagCountries"
                :focus="'assignment-reason-text-' . $this->getId()"
            />
        </div>
        <flux:text>
            {{ __('The reason is selected here and saved with the confirmed key assignment. Saved reasons can be reused.') }}
        </flux:text>

        <flux:callout
            icon="pencil-line"
            heading="{{ __('New assignment reason') }}"
            color="sky"
        >
            <flux:field>
                <flux:label>
                    <flux:callout.heading>
                        {{ __('Reason for translation key assignment') }}
                    </flux:callout.heading>
                </flux:label>
                <flux:textarea
                    class="min-h-12"
                    :id="'assignment-reason-text-'.$this->getId()"
                    wire:key="assignment-reason-input-{{ $newAssignmentReasonLocale }}"
                    rows="3"
                    autofocus
                    wire:model="newAssignmentReason"
                    :lang="$newAssignmentReasonLocale"
                    spellcheck="true"
                />

                <flux:error name="newAssignmentReason" />

            </flux:field>
        </flux:callout>

        <div class="flex justify-end gap-2">
            <flux:button.group>
                <flux:button
                    type="submit"
                    size="sm"
                    variant="primary"
                    color="green"
                    icon="check"
                >
                    <flux:icon.check class="size-5" />
                </flux:button>
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
