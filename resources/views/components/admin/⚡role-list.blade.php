{{-- resources/views/components/admin/⚡role-list.blade.php --}}

{{-- @php
    $caseSensitive = true; // Set to false if you want case-insensitive search
    TODO: switch per toggle
@endphp --}}

<flux:card>

    {{-- Header part --}}
    <x-ui.headers.page
        :title="__('Role Management')"
        :description="__('Manage role metadata, assignment visibility, and role badge display settings.')"
    >
        @role('Super-Admin')
            <x-ui.button.save
                icon="plus"
                label="{{ __('Create Role') }}"
                wire:click="openCreateRoleModal"
            />
        @endrole
    </x-ui.headers.page>

    {{-- Metablock: Overview --}}
    @include('components.admin.partials.role-list.⚡meta')

    {{-- Filter part --}}
    @include('components.admin.partials.role-list.⚡filter')

    {{-- Table part --}}
    @include('components.admin.partials.role-list.⚡table')

    {{-- Modal Create roles --}}
    @include('components.admin.partials.role-list.⚡modal-create')

    {{-- Modal Edit roles --}}
    @include('components.admin.partials.role-list.⚡modal-edit')

</flux:card>
