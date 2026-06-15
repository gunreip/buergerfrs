{{-- resources/views/components/ui/text/copyable-field.blade.php --}}

@props([
    'title' => null,
    'value' => null,
    'rows' => 1,
    'mono' => false,
    'emptyValue' => '—',
    'copyLabel' => __('ui.text.copyable_field.copy_to_clipboard'),
    'copiedLabel' => __('admin.translation_list.copied'),
    'contentClass' => '',
    'showHiddenButton' => false,
    'badge' => null,
    'badgeColor' => 'zinc',
    'badgeVariant' => 'subtle',
    'badgeContext' => null,
    'syncResizeGroup' => null,
])

@php
    $displayValue = $value === null || $value === '' ? $emptyValue : (string) $value;

    $copyValue = is_string($value) || is_numeric($value) ? trim((string) $value) : '';

    $hasCopyValue = $copyValue !== '' && $copyValue !== '—' && $copyValue !== '-';
    $hasActionSlot = isset($action) && $action->isNotEmpty();
    $hasVisibleControls = $hasActionSlot || $hasCopyValue;
    $syncResizeGroupValue = is_string($syncResizeGroup) ? trim($syncResizeGroup) : '';
@endphp

<div
    data-copyable-field-sync-group="{{ $syncResizeGroupValue }}"
    x-data="{
        copied: false,
        async copy() {
            if (!@js($hasCopyValue)) {
                return;
            }

            try {
                await navigator.clipboard.writeText(@js($copyValue));
                this.copied = true;
                setTimeout(() => {
                    this.copied = false;
                }, 2000);
            } catch (error) {
                console.error('Failed to copy value.', error);
            }
        },
    }"
    @if ($syncResizeGroupValue !== '')
    @endif {{ $attributes->class('space-y-2') }}>
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

        @if ($hasVisibleControls)
            <div class="flex items-center gap-2">
                @if ($hasActionSlot)
                    {{ $action }}
                @endif

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
        @elseif ($showHiddenButton)
            <flux:button
                class="pointer-events-none invisible h-8 w-8 shrink-0 p-0"
                type="button"
                aria-hidden="true"
                tabindex="-1"
                size="sm"
                variant="ghost"
            >
                <flux:icon.copy-plus
                    class="size-5"
                    stroke-width="1"
                />
            </flux:button>
        @endif
    </div>

    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.pen-line stroke-width="1" />
        </flux:input.group.prefix>
        <flux:textarea
            readonly
            rows="{{ $rows }}"
            @class([
                'whitespace-pre-wrap [overflow-wrap:anywhere]',
                'rounded-l-none',
                'resize-y' => $syncResizeGroupValue !== '',
                'resize-none' => $syncResizeGroupValue === '',
                'font-mono' => $mono,
                $contentClass,
            ])
        >{{ $displayValue }}
        </flux:textarea>
    </flux:input.group>
</div>
