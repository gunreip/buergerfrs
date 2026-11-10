<flux:tooltip>
    <div class="inline-flex">
        <flux:button.group
            x-data="{
                hasSavedBase: {{ \Illuminate\Support\Js::from(!empty($context['sourceRevision']) && !empty($context['revision']['id'])) }},
                savedSource: {{ \Illuminate\Support\Js::from($context['sourceText'] ?? null) }},
                savedTarget: {{ \Illuminate\Support\Js::from($context['revision']['value'] ?? null) }},
                canSubmit() {
                    return this.hasSavedBase
                        &amp;&amp; $wire.sourceLanguageValue.trim() !== ''
                        &amp;&amp; $wire.value.trim() !== ''
                        &amp;&amp; $wire.locale === $wire.loadedLocale
                        &amp;&amp; $wire.sourceLanguageValue === this.savedSource
                        &amp;&amp; $wire.value === this.savedTarget
                        &amp;&amp; $wire.selectedSubLanguages.every(code =>
                            $wire.subLanguageRevisions[code]
                            &amp;&amp; ($wire.subLanguageValues[code] ?? '').trim() !== ''
                            &amp;&amp; $wire.subLanguageValues[code] === $wire.subLanguageSavedValues[code]);
                }
            }"
            wire:key="submit-drafts-{{ $selected }}-{{ $context['sourceRevision'] ?? 0 }}-{{ $context['revision']['id'] ?? 0 }}"
        >
            <flux:button
                variant="filled"
                color="sky"
                size="sm"
                icon="paper-airplane"
                wire:click="submitForReview"
                disabled
                x-bind:disabled="!canSubmit()"
                wire:loading.attr="disabled"
            />
            <flux:button
                variant="filled"
                color="sky"
                size="sm"
                wire:click="submitForReview"
                disabled
                x-bind:disabled="!canSubmit()"
                wire:loading.attr="disabled"
            >
                {{ __('Submit saved drafts for review') }}
            </flux:button>
        </flux:button.group>
    </div>
    <x-translation-workbench::ui.common.tooltip-content
        :heading="__('Submit saved drafts for review')"
        :text="__('Submit saved source, main-language and selected variant drafts for review. Save all changes first and complete the selected languages. This does not approve or publish them.')"
    />
</flux:tooltip>
