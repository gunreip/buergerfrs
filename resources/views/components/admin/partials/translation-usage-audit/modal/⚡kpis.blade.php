{{-- resources/views/components/admin/partials/translation-usage-audit/modal/⚡kpis.blade.php --}}

<div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
    {{-- Callout Keys --}}
    <flux:callout
        color="rose"
        icon="key-round"
    >
        <flux:callout.heading>{{ __('Keys') }}</flux:callout.heading>
        <flux:callout.text class="text-2xl! font-semibold tabular-nums">
            {{ (int) ($selectedItem['translation_key_count'] ?? 0) }}
        </flux:callout.text>
    </flux:callout>

    {{-- Callout Usages total --}}
    <flux:callout
        color="violet"
        icon="hash"
    >
        <flux:callout.heading>{{ __('Usages total') }}</flux:callout.heading>
        <flux:callout.text class="text-2xl! font-semibold tabular-nums">
            {{ (int) ($selectedItem['usage_count_total'] ?? ($selectedItem['usage_count'] ?? 0)) }}
        </flux:callout.text>
    </flux:callout>

    {{-- Callout Current usage --}}
    <flux:callout
        color="lime"
        icon="lamp-desk"
    >
        <flux:callout.heading>{{ __('Current usages') }}</flux:callout.heading>
        <flux:callout.text class="text-2xl! font-semibold tabular-nums">
            {{ (int) ($selectedItem['usage_count_current'] ?? 0) }}
        </flux:callout.text>
    </flux:callout>

    {{-- Callout Stale usages --}}
    <flux:callout
        color="amber"
        icon="shrink"
        stroke-width="1"
    >
        <flux:callout.heading>{{ __('ui.stale.stale-usages') }}</flux:callout.heading>
        <flux:callout.text class="text-2xl! font-semibold tabular-nums">
            {{ (int) ($selectedItem['usage_count_stale'] ?? 0) }}
        </flux:callout.text>
    </flux:callout>

    {{-- Callout UI candidate --}}
    <flux:callout
        icon="blocks"
        color="{{ (bool) ($selectedItem['already_has_ui_candidate'] ?? false) ? 'emerald' : 'zinc' }}"
    >
        <flux:callout.heading>{{ __('UI candidate') }}</flux:callout.heading>
        <flux:callout.text class="mt-1 text-lg font-semibold">
            {{ (bool) ($selectedItem['already_has_ui_candidate'] ?? false) ? __('ui.filters.yes') : __('no') }}
        </flux:callout.text>
    </flux:callout>
</div>
