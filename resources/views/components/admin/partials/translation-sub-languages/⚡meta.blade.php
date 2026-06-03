{{-- resources/views/components/admin/partials/translation-sub-languages/⚡meta.blade.php --}}

<flux:card class="mt-6">

    <x-ui.headers.card
        :title="__('Sub-Language Overview')"
        :description="__('Summary of active sub-languages, their coverage, and relation to main language keys.')"
    />

    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <flux:callout
            color="sky"
            icon="languages"
        >
            <flux:callout.heading>{{ __('Matching sub-languages') }}</flux:callout.heading>
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format($subLocales->count()) }}
            </flux:callout.text>
            <flux:callout.text class="font-extralight">
                {{ __('Active locale variants matching the current filters.') }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="{{ $activeSubLocalesTotal > 0 ? 'green' : 'zinc' }}"
            icon="badge-check"
        >
            <flux:callout.heading>{{ __('Active total') }}</flux:callout.heading>
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format($activeSubLocalesTotal) }}
            </flux:callout.text>
            <flux:callout.text class="font-extralight">
                {{ __('All active sub-languages available in locale management.') }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="{{ $withOverridesCount > 0 ? 'amber' : 'zinc' }}"
            icon="replace"
        >
            <flux:callout.heading>{{ __('With overrides') }}</flux:callout.heading>
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format($withOverridesCount) }}
            </flux:callout.text>
            <flux:callout.text class="font-extralight">
                {{ __('Variants containing locale-specific translated values.') }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="orange"
            icon="database"
        >
            <flux:callout.heading>{{ __('Total keys') }}</flux:callout.heading>
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format($totalKeys) }}
            </flux:callout.text>
            <flux:callout.text class="font-extralight">
                {{ __('Reference total from translation keys table.') }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
