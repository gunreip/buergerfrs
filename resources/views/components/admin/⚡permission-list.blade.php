{{-- resources/views/components/admin/⚡permission-list.blade.php --}}

@php
    $highlightSearchMatch = static function (?string $value, ?string $search): string {
        $value = (string) $value;
        $search = trim((string) $search);

        if ($search === '') {
            return e($value);
        }

        $escapedValue = e($value);
        $escapedSearch = e($search);

        return Str::of($escapedValue)
            ->replaceMatches(
                '/' . preg_quote($escapedSearch, '/') . '/iu',
                '<mark class="rounded bg-amber-400/20 px-0.5 text-amber-100">$0</mark>',
            )
            ->toString();
    };
@endphp

<flux:card>
    <x-ui.headers.page
        :title="__('Permission Management')"
        :description="__('Review registered permissions, guards, and role assignments.')"
    >
        <x-ui.button.confirm
            label="Edit Role Managment"
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
