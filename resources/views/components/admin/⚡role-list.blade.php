{{-- resources/views/components/admin/⚡role-list.blade.php --}}

@php
    // highlight search term in table results
    $highlightSearchMatch = static function (?string $value, ?string $search): string {
        $value = (string) $value;
        $search = trim((string) $search);

        if ($search === '') {
            return e($value);
        }

        $escapedValue = e($value);
        $escapedSearch = e($search);

        return Str::of($escapedValue)
            ->replaceMatches('/' . preg_quote($escapedSearch, '/') . '/iu', '<mark class="highlight">$0</mark>')
            ->toString();
    };
@endphp

<flux:card>

    {{-- Header part --}}
    <x-ui.headers.page
        title="{{ __('Role Management') }}"
        description="{{ __('Manage role metadata, assignment visibility, and role badge display settings.') }}"
    >
        @role('Super-Admin')
            <flux:button
                type="button"
                variant="primary"
                color="green"
                icon="plus"
                wire:click="openCreateRoleModal"
            >
                {{ __('Create Role') }}
            </flux:button>
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
