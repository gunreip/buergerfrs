<flux:modal
    class="w-full max-w-5xl"
    name="workbench-create-translation-key"
    wire:model="translationKeyOpen"
>
    @php
        $proposedKey = trim($newTranslationKey);
        $proposedKeyValid = mb_strlen($proposedKey) <= 255
            && \Gunreip\TranslationWorkbench\Generation\TranslationKeySegments::configured()->allows($proposedKey);
    @endphp
    <form
        class="space-y-4"
        wire:submit="createTranslationKey"
    >
        <flux:heading size="xl">
            {{ __('Create a translation key') }}
        </flux:heading>
        <flux:text>
            {{ __('The key is selected here and saved with the confirmed key assignment. Existing keys are reused.') }}
        </flux:text>

        <flux:callout
            icon="key"
            heading="{{ __('Current assigned translation key') }}"
            color="sky"
        >
            <flux:field>
                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.key class="text-zinc-400" />
                    </flux:input.group.prefix>
                    <flux:input
                        readonly
                        copyable
                        :value="!empty($context['finding']['key_id']) ?
                            (($context['finding']['namespace'] ?? '') !== '' ? $context['finding']['namespace'].
                                '::' : '').$context['finding']['full_name'] :
                            __('Not assigned')"
                    />
                </flux:input.group>
            </flux:field>
        </flux:callout>

        <flux:callout
            icon="key"
            heading="{{ __('Suggested translation key') }}"
            color="rose"
        >
            <flux:input
                readonly
                copyable
                :value="$context['finding']['suggested_key'] ?? ''"
            />
        </flux:callout>

        <flux:callout
            icon="language"
            heading="{{ __('Assign a translation key') }}"
            color="green"
        >
            <flux:field>
                <div class="flex items-center justify-between gap-3">
                    <flux:label>
                        <flux:callout.text>
                            {{ __('At least three dot-separated segments, for example ui.button.save.') }}
                        </flux:callout.text>
                    </flux:label>
                    <flux:badge size="sm" :color="$proposedKeyValid ? 'green' : 'amber'" class="shrink-0">
                        {{ $proposedKeyValid ? __('Valid translation key') : __('Invalid translation key') }}
                    </flux:badge>
                </div>
                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.key class="text-green-600" />
                    </flux:input.group.prefix>
                    <flux:input
                        data-translation-key-input
                        autofocus
                        clearable
                        wire:model.live.debounce.200ms="newTranslationKey"
                        autocapitalize="off"
                        :spellcheck="false"
                        placeholder="ui.button.save"
                    />

                </flux:input.group>

                <flux:error name="newTranslationKey" />

            </flux:field>
        </flux:callout>

        <div class="flex flex-wrap justify-end gap-2">
            <flux:button.group>
                <flux:button
                    type="submit"
                    size="sm"
                    color="green"
                    variant="primary"
                    :aria-label="__('Use translation key')"
                    :disabled="!$proposedKeyValid"
                    wire:loading.attr="disabled"
                    wire:target="newTranslationKey,createTranslationKey"
                >
                    <flux:icon.check />
                </flux:button>
                <flux:button
                    type="submit"
                    size="sm"
                    color="green"
                    variant="primary"
                    :disabled="!$proposedKeyValid"
                    wire:loading.attr="disabled"
                    wire:target="newTranslationKey,createTranslationKey"
                >
                    {{ __('Use translation key') }}
                </flux:button>
            </flux:button.group>
        </div>
    </form>
</flux:modal>
