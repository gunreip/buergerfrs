{{-- resources/views/components/admin/⚡permission-list.blade.php --}}

{{-- @php
    $caseSensitive = true; // Set to false if you want case-insensitive search
    TODO: switch per toggle
@endphp --}}

<flux:card>
    <x-ui.headers.page
        :title="__('admin.permissions.title')"
        :description="__('admin.permissions.description')"
    >
        <x-ui.button.confirm
            label="{{ __('admin.permission_list.edit_role_permissions') }}"
            wire:click="openRolePermissionsModal"
        />
    </x-ui.headers.page>

    {{-- Partial: Overview part --}}
    @include('components.admin.partials.permission-list.⚡meta-overview')

    {{-- Partial: Filter part --}}
    @include('components.admin.partials.permission-list.⚡filter')

    {{-- Partial: Table permissions part --}}
    @include('components.admin.partials.permission-list.⚡table-permissions')

    {{-- Partial: Edit permission modal --}}
    @include('components.admin.partials.permission-list.⚡modal-edit-permission')

    {{-- Partial: Role permissions modal --}}
    @include('components.admin.partials.permission-list.⚡modal-role-permissions')

</flux:card>
