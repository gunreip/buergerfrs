{{-- resources/views/components/admin/⚡user-list.blade.php --}}

{{-- @php
    $caseSensitive = true; // Set to false if you want case-insensitive search
    TODO: switch per toggle
@endphp --}}

<flux:card>

    {{-- Header part --}}
    <x-ui.headers.page
        :title="__('admin.user_list.user_role_management')"
        :description="__('admin.user_list.manage_your_system_s_users_assign_roles_and_manage_permissions')"
    />

    {{-- Metablock: Overview --}}
    @include('components.admin.partials.user-list.⚡meta')

    {{-- Filter part --}}
    @include('components.admin.partials.user-list.⚡filter')

    {{-- Table part --}}
    @include('components.admin.partials.user-list.⚡table')

    {{-- Modal part --}}
    @include('components.admin.partials.user-list.⚡modal')

</flux:card>
