{{-- resources/views/livewire/admin/⚡country-reference-list.blade.php --}}

{{-- <div> --}}
<flux:card>
    <x-ui.headers.page
        :title="__('Reference Countries')"
        :description="__('Audit imported country reference data, address formats and available subdivisions.')"
    />

    {{-- Overview / Meta --}}
    @include('components.admin.partials.country-reference-list.⚡meta')

    {{-- Filter --}}
    @include('components.admin.partials.country-reference-list.⚡filter')

    {{-- Table --}}
    @include('components.admin.partials.country-reference-list.⚡table')

</flux:card>
{{-- </div> --}}
