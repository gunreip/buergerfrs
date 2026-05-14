{{-- resources/views/components/admin/⚡person-list.blade.php --}}

{{-- @php
    $caseSensitive = true; // Set to false if you want case-insensitive search
    TODO: switch per toggle
@endphp --}}

<flux:card>
    <x-ui.headers.page
        :title="__('People Management')"
        :description="__('Review natural persons, linked users, and client assignments.')"
    />

    {{-- Overview --}}
    @include('components.admin.partials.person-list.⚡meta')

    {{-- Filter --}}
    @include('components.admin.partials.person-list.⚡filter')

    {{-- Table --}}
    @include('components.admin.partials.person-list.⚡table')

</flux:card>
