{{-- resources/views/components/admin/partials/country-reference-list/⚡meta.blade.php --}}

{{-- Overview / Meta --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Overview')"
        :description="__('Summary of imported country reference data, address formats and available subdivisions.')"
    >
        <div class="mt-4 flex flex-wrap gap-2">
            <flux:badge
                color="blue"
                variant="subtle"
            >
                {{ __('EU') }}: {{ $summary['eu'] }}
            </flux:badge>

            <flux:badge
                color="blue"
                variant="subtle"
            >
                {{ __('EEA') }}: {{ $summary['eea'] }}
            </flux:badge>

            <flux:badge
                color="blue"
                variant="subtle"
            >
                {{ __('Schengen') }}: {{ $summary['schengen'] }}
            </flux:badge>
        </div>
    </x-ui.headers.card>

    <div class="grid grid-cols-2 gap-3 xl:grid-cols-6">
        <flux:callout
            color="fuchsia"
            icon="globe"
        >
            <flux:callout.heading>
                {{ __('Countries') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['total'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="green"
            icon="check-circle"
        >
            <flux:callout.heading>
                {{ __('Active') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['active'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="blue"
            icon="map"
        >
            <flux:callout.heading>
                {{ __('Address formats') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['with_address_format'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="purple"
            icon="waypoints"
        >
            <flux:callout.heading>
                {{ __('Subdivisions') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['with_subdivisions'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="amber"
            icon="circle-question-mark"
        >
            <flux:callout.heading>
                {{ __('Missing capital') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['missing_capital'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="red"
            icon="phone-off"
        >
            <flux:callout.heading>
                {{ __('Missing phone') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['missing_phone_code'] }}
            </flux:callout.text>
        </flux:callout>
    </div>

</flux:card>
