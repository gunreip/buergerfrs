<?php

use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component {
    //
};

$highlightSearchMatch = static function (?string $value, ?string $search): string {
    $value = (string) $value;
    $search = trim((string) $search);

    if ($search === '') {
        return e($value);
    }

    $escapedValue = e($value);
    $escapedSearch = e($search);

    return Str::of($escapedValue)
        ->replaceMatches('/' . preg_quote($escapedSearch, '/') . '/iu', '<mark class="rounded bg-yellow-300/30 px-0.5 text-zinc-950">$0</mark>')
        ->toString();
};
?>

<flux:card>
    <flux:field space="md">
        <flux:heading
            class="mb-1"
            size="xl"
        >
            {{ __('User Management') }}
        </flux:heading>

        <flux:text>
            {{ __('Manage your system\'s users, assign roles, and manage permissions') }}.
        </flux:text>
    </flux:field>

    @include('components.admin.partials.user-list.meta')

    @include('components.admin.partials.user-list.filter')

    @include('components.admin.partials.user-list.table')

    @include('components.admin.partials.user-list.modal')

</flux:card>
