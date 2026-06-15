{{-- resources/views/components/admin/partials/country-reference-list/⚡meta.blade.php --}}

{{-- Overview / Meta --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('admin.permissions.overview.title')"
        :description="__('admin.country_reference_list.meta.summary_of_imported_country_reference_data_address_formats_and_available_subdivi')"
    >

        <div class="mt-4 flex flex-wrap gap-2">
            <flux:badge
                color="blue"
                variant="subtle"
            >
                {{ __('admin.country_reference_list.filter.eu') }}: {{ $summary['eu'] }}
            </flux:badge>

            <flux:badge
                color="blue"
                variant="subtle"
            >
                {{ __('admin.country_reference_list.filter.eea') }}: {{ $summary['eea'] }}
            </flux:badge>

            <flux:badge
                color="blue"
                variant="subtle"
            >
                {{ __('admin.country_reference_list.filter.schengen') }}: {{ $summary['schengen'] }}
            </flux:badge>
        </div>
    </x-ui.headers.card>

    <div class="grid grid-cols-2 gap-3 xl:grid-cols-6">
        <flux:callout
            color="fuchsia"
            icon="globe"
        >
            <flux:callout.heading>
                {{ __('layouts.sidebar.administration.countries') }}
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
                {{ __('admin.country_reference_list.filter.active') }}
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
                {{ __('admin.country_reference_list.meta.address_formats') }}
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
                {{ __('admin.country_reference_list.meta.subdivisions') }}
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
                {{ __('admin.country_reference_list.filter.missing_capital') }}
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
                {{ __('admin.country_reference_list.meta.missing_phone') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['missing_phone_code'] }}
            </flux:callout.text>
        </flux:callout>
    </div>

</flux:card>
