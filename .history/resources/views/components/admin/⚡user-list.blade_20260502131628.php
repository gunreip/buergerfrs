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

    <flux:card class="mt-6">
        <flux:heading
            class="mb-4"
            size="lg"
        >
            {{ __('Filtering') }}
        </flux:heading>

        <div class="flex w-full items-end gap-3">

            <div class="min-w-0 flex-none basis-1/4">
                <flux:label for="user-list-search">
                    {{ __('Search') }}
                </flux:label>

                <flux:input.group class="w-full min-w-0">
                    <flux:input.group.prefix>
                        <flux:icon.magnifying-glass />
                    </flux:input.group.prefix>

                    <flux:input
                        class="w-full min-w-0"
                        id="user-list-search"
                        name="user-list-search"
                        type="text"
                        copyable
                        clearable
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Search by name or email') }}"
                    />
                </flux:input.group>
            </div>

            <div class="min-w-0 flex-none basis-1/4">
                <flux:label for="user-list-role-filter">
                    {{ __('Role') }}
                </flux:label>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.tag />
                    </flux:input.group.prefix>

                    <flux:select
                        id="user-list-role-filter"
                        name="user-list-role-filter"
                        wire:model.live="roleFilter"
                    >
                        <flux:select.option value="">
                            {{ __('All roles') }}
                        </flux:select.option>

                        <flux:select.option value="__none__">
                            {{ __('Without role') }}
                        </flux:select.option>

                        @foreach ($roles as $role)
                            <flux:select.option value="{{ $role }}">
                                {{ $role }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:input.group>
            </div>

            <div class="ml-auto flex-none">
                <flux:radio.group
                    id="user-list-per-page"
                    name="user-list-per-page"
                    label="{{ __('Per Page') }}"
                    variant="segmented"
                    wire:model.live="perPage"
                >
                    <flux:radio value="10">
                        10
                    </flux:radio>

                    <flux:radio value="25">
                        25
                    </flux:radio>

                    <flux:radio value="50">
                        50
                    </flux:radio>

                    <flux:radio value="100">
                        100
                    </flux:radio>
                </flux:radio.group>
            </div>

        </div>
    </flux:card>

    <flux:card class="mt-6">
        <flux:heading
            class="mb-4"
            size="lg"
        >
            {{ __('User List') }}
        </flux:heading>

        <div class="mx-auto max-w-full">
            <div class="overflow-hidden rounded-t-lg">
                <flux:table>
                    <flux:table.columns class="bg-zinc-800 text-zinc-400">
                        <flux:table.column
                            sortable
                            wire:click="sortBy('id')"
                            align="center"
                        >
                            {{ __('ID') }}
                        </flux:table.column>

                        <flux:table.column
                            sortable
                            wire:click="sortBy('name')"
                        >
                            {{ __('Name') }}
                        </flux:table.column>

                        <flux:table.column
                            sortable
                            wire:click="sortBy('email')"
                        >
                            {{ __('E-Mail') }}
                        </flux:table.column>

                        <flux:table.column
                            sortable
                            wire:click="sortBy('roles.name')"
                        >
                            {{ __('Roles') }}
                        </flux:table.column>

                        <flux:table.column align="center">
                            {{ __('Actions') }}
                        </flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($users as $user)
                            <flux:table.row>
                                <flux:table.cell align="end">
                                    {{ $user->id }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    {!! $highlightSearchMatch($user->name, $search) !!}
                                </flux:table.cell>

                                <flux:table.cell>
                                    {!! $highlightSearchMatch($user->email, $search) !!}
                                </flux:table.cell>

                                <flux:table.cell>
                                    @forelse ($user->roles as $role)
                                        <x-ui.role-badge
                                            :label="$role->name"
                                            :badge="$roleBadges[$role->name] ?? null"
                                        />
                                    @empty
                                        <x-ui.role-badge
                                            :label="__('Without role')"
                                            :badge="$withoutRoleBadge"
                                        />
                                    @endforelse
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:button.group>
                                        <flux:button
                                            href="{{ route('admin.users.edit', $user) }}"
                                            variant="primary"
                                            color="sky"
                                            size="xs"
                                        >
                                            {{ __('Edit') }}

                                            <flux:separator
                                                class="mx-1"
                                                vertical
                                                variant="subtle"
                                            />

                                            <flux:icon.pencil-square variant="micro" />
                                        </flux:button>
                                    </flux:button.group>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>

            <flux:separator text="{{ __('Pagination') }}" />

            @if ($users->hasPages())
                @php
                    $currentPage = $users->currentPage();
                    $lastPage = $users->lastPage();

                    $windowStart = max(1, $currentPage - 4);
                    $windowEnd = min($lastPage, $currentPage + 4);

                    if ($currentPage <= 5) {
                        $windowStart = 1;
                        $windowEnd = min($lastPage, 9);
                    }

                    if ($currentPage >= $lastPage - 4) {
                        $windowStart = max(1, $lastPage - 8);
                        $windowEnd = $lastPage;
                    }
                @endphp

                <div class="my-4 py-4">
                    <div class="flex items-center justify-between gap-4">
                        <flux:text class="text-sm text-zinc-400">
                            {{ __('Showing') }}
                            {{ $users->firstItem() }}
                            {{ __('to') }}
                            {{ $users->lastItem() }}
                            {{ __('of') }}
                            {{ $users->total() }}
                            {{ __('results') }}
                        </flux:text>

                        <div class="flex items-center gap-3">
                            <flux:button.group>
                                <flux:button
                                    size="sm"
                                    icon="chevrons-left"
                                    wire:click="goToFirstPage"
                                    :disabled="$users->onFirstPage()"
                                />

                                <flux:button
                                    size="sm"
                                    icon="chevron-left"
                                    wire:click="goToPreviousPage"
                                    :disabled="$users->onFirstPage()"
                                />

                                @if ($windowStart > 1)
                                    <flux:button
                                        size="sm"
                                        wire:click="goToPage(1)"
                                    >
                                        1
                                    </flux:button>

                                    @if ($windowStart > 2)
                                        <flux:button
                                            size="sm"
                                            disabled
                                        >
                                            ...
                                        </flux:button>
                                    @endif
                                @endif

                                @for ($page = $windowStart; $page <= $windowEnd; $page++)
                                    <flux:button
                                        size="sm"
                                        wire:click="goToPage({{ $page }})"
                                        :variant="$page === $currentPage ? 'primary' : null"
                                    >
                                        {{ $page }}
                                    </flux:button>
                                @endfor

                                @if ($windowEnd < $lastPage)
                                    @if ($windowEnd < $lastPage - 1)
                                        <flux:button
                                            size="sm"
                                            disabled
                                        >
                                            ...
                                        </flux:button>
                                    @endif

                                    <flux:button
                                        size="sm"
                                        wire:click="goToPage({{ $lastPage }})"
                                    >
                                        {{ $lastPage }}
                                    </flux:button>
                                @endif

                                <flux:button
                                    size="sm"
                                    icon="chevron-right"
                                    wire:click="goToNextPage"
                                    :disabled="! $users->hasMorePages()"
                                />

                                <flux:button
                                    size="sm"
                                    icon="chevrons-right"
                                    wire:click="goToLastPage"
                                    :disabled="! $users->hasMorePages()"
                                />
                            </flux:button.group>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </flux:card>

    <!-- zukunft: nur zu testzwecken stehenlassen! -->
    {{-- <select>
        <button>
            <selectedcontent></selectedcontent>
        </button>

        <option>
            <span class="badge admin">Admin</span>
        </option>

        <option>
            <span class="badge user">User</span>
        </option>
    </select> --}}

</flux:card>
