{{-- resources/views/components/ui/text/copyable-textarea.blade.php --}}

@props([
    'title' => null,
    'value' => null,
    'rows' => 3,
    'mono' => false,
    'resize' => 'none',
    'emptyValue' => '—',
    'copyLabel' => __('ui.text.copyable_field.copy_to_clipboard'),
    'copiedLabel' => __('admin.translation_list.copied'),
])

@php
    $displayValue = $value === null || $value === '' ? $emptyValue : (string) $value;

    $resizeClass = match ($resize) {
        'vertical' => 'resize-y',
        'horizontal' => 'resize-x',
        'both' => 'resize',
        default => 'resize-none',
    };
@endphp

<div
    x-data="{
        copied: false,
        copy() {
            const value = this.$refs.copySource.value ?? '';

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(value);
            } else {
                this.$refs.copySource.focus();
                this.$refs.copySource.select();
                document.execCommand('copy');
                this.$refs.copySource.setSelectionRange(0, 0);
            }

            this.copied = true;

            setTimeout(() => {
                this.copied = false;
            }, 2000);
        },
    }"
    {{ $attributes->class('space-y-2') }}
>
    @if ($title !== null)
        <div class="flex items-center justify-between gap-3">
            <div class="py-3 text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $title }}
            </div>

            <flux:button
                class="h-10 w-10 p-0"
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
        </div>
    @else
        <div class="flex justify-end">
            <flux:button
                class="h-10 w-10 p-0"
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
        </div>
    @endif

    <textarea
        x-ref="copySource"
        readonly
        rows="{{ $rows }}"
        @class([
            'block w-full border rounded-lg bg-zinc-100 px-3 py-2 text-sm text-zinc-800 shadow-xs outline-none',
            'selection:bg-sky-200 selection:text-zinc-950',
            'focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20',
            'dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:selection:bg-sky-700/60 dark:selection:text-white',
            'whitespace-pre-wrap [overflow-wrap:anywhere]',
            $resizeClass,
            'font-mono' => $mono,
        ])
    >{{ $displayValue }}</textarea>
</div>
