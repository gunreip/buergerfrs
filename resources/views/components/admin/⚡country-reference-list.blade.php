{{-- resources/views/livewire/admin/⚡country-reference-list.blade.php --}}

{{-- <div> --}}
<flux:card>
    <x-ui.headers.page
        :title="__('admin.country_reference_list.reference_countries')"
        :description="__('admin.country_reference_list.audit_imported_country_reference_data_address_formats_and_available_subdivisions')"
    />

    {{-- Overview / Meta --}}
    @include('components.admin.partials.country-reference-list.⚡meta')

    {{-- Filter --}}
    @include('components.admin.partials.country-reference-list.⚡filter')

    {{-- Table --}}
    @include('components.admin.partials.country-reference-list.⚡table')

</flux:card>
{{-- </div> --}}
