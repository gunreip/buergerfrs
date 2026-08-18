{{-- resources/views/components/admin/partials/country-reference-list/⚡meta.blade.php --}}

{{-- Overview / Meta --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('ui.title.filter')"
        :description="__(
            'admin.country_reference_list.meta.summary_of_imported_country_reference_data_address_formats_and_available_subdivi',
        )"
    >

        {{-- Badges --}}
        <div class="mt-4 flex flex-wrap gap-2">
            {{-- Badge Summary EU --}}
            <flux:badge
                color="blue"
                variant="subtle"
                :label="__('ui.filters.eu').
                ': '.$summary['eu']"
            />

            {{-- Badge Summary EEA --}}
            <flux:badge
                color="blue"
                variant="subtle"
                :label="__('ui.filters.eea').
                ': '.$summary['eea']"
            />

            {{-- Badge Summary Schengenraum --}}
            <flux:badge
                color="blue"
                variant="subtle"
                :label="__('admin.country_reference_list.filter.schengen').
                ': '.$summary['schengen']"
            />
        </div>
    </x-ui.headers.card>

    {{-- Callouts --}}
    <div class="grid grid-cols-2 gap-3 xl:grid-cols-6">
        {{-- Callout Countries --}}
        <flux:callout
            class="hyphens-auto"
            color="fuchsia"
            icon="globe"
            heading="{{ __('layouts.sidebar.administration.countries') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['total'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Callout Active Countries --}}
        <flux:callout
            class="hyphens-auto"
            color="green"
            icon="check-circle"
            heading="{{ __('ui.state.active') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['active'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Callout with Address Format --}}
        <flux:callout
            class="hyphens-auto"
            color="blue"
            icon="map"
            heading="{{ __('admin.country_reference_list.meta.address_formats') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['with_address_format'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Callout with Subdivisions --}}
        <flux:callout
            class="hyphens-auto"
            color="purple"
            icon="waypoints"
            heading="{{ __('admin.country_reference_list.meta.subdivisions') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['with_subdivisions'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Callout missing capital --}}
        <flux:callout
            class="hyphens-auto"
            color="amber"
            icon="circle-question-mark"
            heading="{{ __('ui.filters.missing-capital') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['missing_capital'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Callout missing phone code --}}
        <flux:callout
            class="hyphens-auto"
            color="red"
            icon="phone-off"
            heading="{{ __('admin.country_reference_list.meta.missing_phone') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['missing_phone_code'] }}
            </flux:callout.text>
        </flux:callout>
    </div>

</flux:card>
