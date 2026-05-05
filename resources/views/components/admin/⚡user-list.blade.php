<?php

// resources/views/components/admin/⚡user-list.blade.php

use Illuminate\Support\Str;

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
?>

<flux:card>

    {{-- Header part --}}
    <x-ui.headers.page
        :title="__('User / Role Management')"
        :description="__('Manage your system\'s users, assign roles, and manage permissions')"
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
