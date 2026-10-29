<flux:modal
    class="md:w-lg"
    :name="'workbench-create-workflow-reason-'.$this->getId()"
    wire:model="workflowReasonOpen"
>
    <form
        class="space-y-4"
        wire:submit="createWorkflowReason"
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
                {{ $this->workflowReasonTitle() }}
            </flux:heading>
            @if ($this->workflowReasonHasLanguage())
                <x-translation-workbench::ui.common.input-language-selector
                    class="shrink-0"
                    wire:model.live="newWorkflowReasonLocale"
                    :target="$this->reasonTargetLocale()"
                    :countries="$translationFlagCountries ?? []"
                    :focus="'workflow-reason-text-' . $this->getId()"
                />
            @endif
        </div>
        <flux:text>
            {{ __('The reason is selected in the form. It is saved only when you confirm the corresponding action.') }}
        </flux:text>
        <flux:field>
            <flux:label>{{ __('Reason') }}</flux:label>
            <flux:textarea
                class="min-h-12"
                spellcheck="true"
                rows="3"
                :id="'workflow-reason-text-'.$this->getId()"
                :lang="$this->workflowReasonHasLanguage() ? $newWorkflowReasonLocale : null"
                wire:key="workflow-reason-input-{{ $newWorkflowReasonLocale }}"
                wire:model="newWorkflowReason"
            />

            <flux:error name="newWorkflowReason" />

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
