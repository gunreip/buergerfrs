{{-- resources/views/components/admin/⚡client-list.blade.php --}}

{{-- @php
    $caseSensitive = true; // Set to false if you want case-insensitive search
    TODO: switch per toggle
@endphp --}}

<flux:card>
    <x-ui.headers.page
        :title="__('Client Management')"
        :description="__('Review organizations, client status, and assigned people.')"
    />

    {{-- Partial: Overview --}}
    @include('components.admin.partials.client-list.⚡meta')

    {{-- Partial: Filter --}}
    @include('components.admin.partials.client-list.⚡filter')

    {{-- Partial: Table --}}
    @include('components.admin.partials.client-list.⚡table')
</flux:card>
