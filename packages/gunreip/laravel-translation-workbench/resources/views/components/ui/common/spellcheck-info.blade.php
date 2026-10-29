<span {{ $attributes->class('relative inline-flex shrink-0') }}>
    <x-translation-workbench::ui.common.component-marker name="translation-workbench::ui.common.spellcheck-info" />
    <flux:tooltip toggleable position="bottom" align="end">
        <flux:button
            type="button"
            variant="ghost"
            size="xs"
            icon="information-circle"
            :aria-label="__('About browser spellchecking')"
        />
        <flux:tooltip.content class="max-w-sm whitespace-normal text-left">
            <div class="space-y-2">
                <p class="font-semibold">{{ __('Browser spellchecking') }}</p>
                <p>{{ __('The language of each text field indicates the intended checking language. Your browser controls spellchecking and the dictionaries it uses.') }}</p>
                <p>{{ __('This page cannot detect whether the required dictionary is installed or selected.') }}</p>
                <p>{{ __('In Firefox, right-click inside the text field to enable spellchecking and select or add dictionaries under Languages. Other browsers provide these options in their language or spellchecking settings.') }}</p>
            </div>
        </flux:tooltip.content>
    </flux:tooltip>
</span>
