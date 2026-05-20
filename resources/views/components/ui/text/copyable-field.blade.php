{{-- resources/views/components/ui/text/copyable-field.blade.php --}}

@props([
    'title' => null,
    'value' => null,
    'mono' => false,
    'emptyValue' => '—',
    'copyLabel' => __('Copy to clipboard'),
    'copiedLabel' => __('Copied'),
    'contentClass' => '',
    'badge' => null,
    'badgeColor' => 'zinc',
    'badgeVariant' => 'subtle',
    'badgeContext' => null,
])

@php
    $displayValue = $value === null || $value === '' ? $emptyValue : (string) $value;

    $copyValue = is_string($value) || is_numeric($value) ? trim((string) $value) : '';

    $hasCopyValue = $copyValue !== '' && $copyValue !== '—' && $copyValue !== '-';
@endphp

<div
    x-data="{
        copied: false,
        value: @js($copyValue),
        copy() {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(this.value);
            } else {
                const textarea = document.createElement('textarea');

                textarea.value = this.value;
                textarea.setAttribute('readonly', '');
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';

                document.body.appendChild(textarea);

                textarea.select();
                document.execCommand('copy');

                document.body.removeChild(textarea);
            }

            this.copied = true;

            setTimeout(() => {
                this.copied = false;
            }, 2000);
        },
    }"
    {{ $attributes->class('space-y-2') }}
>
    <div class="flex items-center justify-between gap-3">
        <div class="flex min-w-0 items-center gap-2">

            @php
                $hasLabelSlot = isset($label) && $label->isNotEmpty();
                $hasTitle = $title !== null && $title !== '';
            @endphp

            @if ($hasLabelSlot)
                <div class="min-w-0 truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $label }}
                </div>
            @elseif ($hasTitle)
                <div class="min-w-0 truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $title }}
                </div>
            @endif

            @if ($badge !== null && $badge !== '')
                @if ($badgeContext !== null && $badgeContext !== '')
                    <x-ui.badge.context
                        :context="$badgeContext"
                        :value="$badge"
                        size="sm"
                    />
                @else
                    <flux:badge
                        size="sm"
                        variant="{{ $badgeVariant }}"
                        color="{{ $badgeColor }}"
                    >
                        {{ $badge }}
                    </flux:badge>
                @endif
            @endif
        </div>

        @if ($hasCopyValue)
            <flux:button
                class="h-8 w-8 shrink-0 p-0"
                type="button"
                size="sm"
                variant="ghost"
                x-on:click.prevent.stop="copy()"
                x-bind:title="copied ? @js($copiedLabel) : @js($copyLabel)"
                x-bind:aria-label="copied ? @js($copiedLabel) : @js($copyLabel)"
            >
                <flux:icon.copy-plus
                    class="size-5"
                    stroke-width="1"
                    x-show="! copied"
                />

                <flux:icon.copy-check
                    class="size-5"
                    stroke-width="1"
                    x-cloak
                    x-show="copied"
                />
            </flux:button>
        @endif
    </div>

    <div
        class="shadow-xs bg-zinc-100 px-3 pb-2 text-sm text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
        <code @class([
            'block whitespace-pre-wrap [overflow-wrap:anywhere]',
            'font-mono' => $mono,
            $contentClass,
        ])>{{ $displayValue }}</code>
    </div>
</div>
