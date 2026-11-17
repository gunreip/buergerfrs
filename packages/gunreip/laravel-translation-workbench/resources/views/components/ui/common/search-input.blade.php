@props(['caseSensitive' => false, 'wholeWord' => false, 'caseAction', 'wordAction', 'wholeLiteral' => false, 'literalAction' => null])

<flux:input
    :attributes="$attributes"
    :input:class="$literalAction ? 'pe-44!' : 'pe-36!'"
>
    <x-slot name="iconTrailing">
        <flux:tooltip position="top" gap="12">
            <flux:button
                type="button"
                size="xs"
                square
                :variant="$caseSensitive ? 'primary' : 'subtle'"
                :aria-pressed="$caseSensitive ? 'true' : 'false'"
                :aria-label="__('translation-workbench.filter.toggle.match_case')"
                wire:click="{{ $caseAction }}"
            >
                <flux:icon.a-large-small class="size-6" />
                {{-- Aa --}}
            </flux:button>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('translation-workbench.filter.toggle.match_case')"
                :text="__(
                    'When enabled, uppercase and lowercase letters must match: Finding does not match finding.',
                )"
            />
        </flux:tooltip>
        <flux:tooltip position="top" gap="12">
            <flux:button
                type="button"
                size="xs"
                square
                :variant="$wholeWord ? 'primary' : 'subtle'"
                :aria-pressed="$wholeWord ? 'true' : 'false'"
                :aria-label="__('translation-workbench.filter.toggle.whole_word_or_segment')"
                wire:click="{{ $wordAction }}"
            >
                <flux:icon.whole-word class="size-6" />
                {{-- [ab] --}}
            </flux:button>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('translation-workbench.filter.toggle.whole_word_or_segment')"
                :text="__(
                    'Match complete words or segments: Finding matches Edit Finding, but not Findings. Slashes, dots, underscores and hyphens separate segments.',
                )"
            />
        </flux:tooltip>
        @if ($literalAction)
            <flux:tooltip position="top" gap="12">
                <flux:button
                    type="button"
                    size="xs"
                    square
                    :variant="$wholeLiteral ? 'primary' : 'subtle'"
                    :aria-pressed="$wholeLiteral ? 'true' : 'false'"
                    :aria-label="__('Match entire literal')"
                    wire:click="{{ $literalAction }}"
                >
                    <flux:icon.parentheses class="size-6" />
                </flux:button>
                <x-translation-workbench::ui.common.tooltip-content
                    :heading="__('Match entire literal')"
                    :text="__('Enter the complete text inside the quotes of the translation call. Enable Match case to require the same capitalization.')"
                />
            </flux:tooltip>
        @endif
        <flux:input.copyable size="sm" />
        <flux:input.clearable size="sm" />
    </x-slot>
</flux:input>
