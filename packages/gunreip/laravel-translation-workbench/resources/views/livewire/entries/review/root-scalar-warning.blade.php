@if (\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangNodeClassifier::isRootScalarKey($structureKey))
    @if ($compactWarning ?? false)
        <flux:callout variant="danger" class="p-2!">
            <flux:callout.heading>
                <flux:tooltip toggleable>
                    <flux:button type="button" size="xs" variant="ghost" icon="exclamation-triangle" class="max-w-full whitespace-normal text-left">
                        {{ __('Invalid key structure') }}
                    </flux:button>
                    <flux:tooltip.content class="max-w-sm whitespace-normal">
                        <code>{{ $structureKey }}</code>:
            {{ __('A scalar translation directly below the language file is not allowed. Choose a nested key using Edit translation key, for example ui.type.type or ui.button.type. Publication is blocked; nothing is renamed automatically.') }}
                    </flux:tooltip.content>
                </flux:tooltip>
            </flux:callout.heading>
        </flux:callout>
    @else
    <flux:callout variant="danger" icon="exclamation-triangle">
        <flux:callout.heading>{{ __('Translation key structure error') }}</flux:callout.heading>
        <flux:callout.text>
            <code>{{ $structureKey }}</code>:
            {{ __('A scalar translation directly below the language file is not allowed. Choose a nested key using Edit translation key, for example ui.type.type or ui.button.type. Publication is blocked; nothing is renamed automatically.') }}
        </flux:callout.text>
    </flux:callout>
    @endif
@endif
