{{-- resources/views/components/admin/partials/translation-statistics/⚡meta.blade.php --}}

{{-- Key Health: summary callouts --}}
<flux:card class="mt-6">

    <x-ui.headers.card
        :title="__('Key Health')"
        :description="__('Overview of translation key states across the audit table.')"
    >
        @if ($recentlySyncedAt)
            <span class="text-xs text-zinc-400 dark:text-zinc-500">
                {{ __('Last update') }}: {{ \Carbon\Carbon::parse($recentlySyncedAt)->diffForHumans() }}
            </span>
        @endif
    </x-ui.headers.card>

    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">

        <flux:callout
            color="orange"
            icon="database"
        >
            <flux:callout.heading>
                {{ __('Total Keys') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format($totalKeys) }}
            </flux:callout.text>

            <flux:callout.text class="font-extralight">
                {{ __('Translation keys in the audit table.') }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="{{ ($keysByStatus['ok'] ?? 0) > 0 ? 'green' : 'zinc' }}"
            icon="check-circle"
        >
            <flux:callout.heading>
                {{ __('OK') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format($keysByStatus['ok'] ?? 0) }}
            </flux:callout.text>

            <flux:callout.text class="font-extralight">
                {{ __('Keys marked as OK.') }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="{{ ($keysByStatus['missing'] ?? 0) > 0 ? 'amber' : 'green' }}"
            icon="shield-alert"
        >
            <flux:callout.heading>
                {{ __('Missing') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format($keysByStatus['missing'] ?? 0) }}
            </flux:callout.text>

            <flux:callout.text class="font-extralight">
                {{ __('Keys with missing values.') }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="{{ ($keysByStatus['obsolete'] ?? 0) > 0 ? 'amber' : 'green' }}"
            icon="archive"
        >
            <flux:callout.heading>
                {{ __('Obsolete') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format($keysByStatus['obsolete'] ?? 0) }}
            </flux:callout.text>

            <flux:callout.text class="font-extralight">
                {{ __('Keys marked as obsolete.') }}
            </flux:callout.text>
        </flux:callout>

    </div>
</flux:card>
