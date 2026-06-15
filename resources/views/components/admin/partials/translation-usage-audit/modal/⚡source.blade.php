{{-- resources/views/components/admin/partials/translation-usage-audit/modal/⚡source.blade.php --}}

{{-- Callout Source value --}}
<flux:callout
    icon="languages"
    color="sky"
    stroke-width="1"
>
    <div class="flex items-center justify-between gap-3">
        <flux:callout.heading>
            {{ __('Source value') }}
        </flux:callout.heading>

        <x-ui.button.show-hide
            size="xs"
            state="showSourceValue"
        />
    </div>

    <div
        x-show="showSourceValue"
        x-collapse
    >
        <flux:callout.text class="wrap-anywhere mt-2">
            {{ $selectedItem['value'] ?? '—' }}
        </flux:callout.text>

        <div class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
            <span class="font-semibold">{{ __('Normalized') }}:</span>
            <code class="wrap-anywhere ml-1">
                {{ $selectedItem['normalized_value'] ?? '—' }}
            </code>
        </div>
    </div>
</flux:callout>
