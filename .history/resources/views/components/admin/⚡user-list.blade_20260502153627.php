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
        {{-- Heading and controls for filtering the user list, including search, role filter, and per page selector. --}}
        <flux:heading
            class="mb-4"
            size="lg"
        >
            {{ __('Filtering') }}
        </flux:heading>

        <div class="flex w-full items-end gap-3">

            {{-- Filtering and searching controls --}}
            {{-- Filtering search --}}
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

            {{-- Filtering by role --}}
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

            {{-- Filtering by per Page --}}
            <div class="ml-auto flex-none">
                <x-ui.table.per-page-selector
                    id="user-list-per-page"
                    name="user-list-per-page"
                    model="perPage"
                    :options="[10, 25, 50, 100]"
                />
            </div>

        </div>
    </flux:card>

    <flux:card class="mt-6">
        {{-- Heading and table for displaying the list of users, including pagination controls. The table supports sorting by ID, name, email, and roles, and includes an "Actions" column with an edit button for each user. --}}
        <flux:heading
            class="mb-4"
            size="lg"
        >
            {{ __('User List') }}
        </flux:heading>

        {{-- Table of users with sortable columns for ID, name, email, and roles, and an "Actions" column with an edit button for each user. Below the table, pagination controls are displayed if there are multiple pages of users. --}}
        <div class="mx-auto max-w-full">
            <div class="overflow-hidden rounded-t-lg">
                {{-- Table of users with sortable columns for ID, name, email, and roles, and an "Actions" column with an edit button for each user. --}}
                <flux:table>
                    {{-- Table Row Header --}}
                    <flux:table.columns class="bg-zinc-800 text-zinc-400">
                        {{-- Columns for ID, name, email, roles, and actions, with sorting enabled on the first four columns. The "Actions" column is aligned to the center. --}}
                        {{-- Column ID --}}
                        <flux:table.column
                            sortable
                            wire:click="sortBy('id')"
                            align="center"
                        >
                            {{ __('ID') }}
                        </flux:table.column>

                        {{-- Column Name --}}
                        <flux:table.column
                            sortable
                            wire:click="sortBy('name')"
                        >
                            {{ __('Name') }}
                        </flux:table.column>

                        {{-- Column Email --}}
                        <flux:table.column
                            sortable
                            wire:click="sortBy('email')"
                        >
                            {{ __('E-Mail') }}
                        </flux:table.column>

                        {{-- Column Roles --}}
                        <flux:table.column
                            sortable
                            wire:click="sortBy('roles.name')"
                        >
                            {{ __('Roles') }}
                        </flux:table.column>

                        {{-- Column Actions --}}
                        <flux:table.column align="center">
                            {{ __('Actions') }}
                        </flux:table.column>
                    </flux:table.columns>

                    {{-- Table Body with rows for each user, displaying their ID, name, email, roles, and an edit button in the "Actions" column. The name and email fields highlight any search terms that match the current search query. If a user has no roles, a badge indicating "Without role" is displayed. The edit button links to the user edit page. --}}
                    <flux:table.rows>
                        @foreach ($users as $user)
                            {{-- Table Row for each user, displaying their ID, name, email, roles, and an edit button in the "Actions" column. The name and email fields highlight any search terms that match the current search query. If a user has no roles, a badge indicating "Without role" is displayed. The edit button links to the user edit page. --}}
                            <flux:table.row>
                                {{-- Column for user ID, aligned to the end. --}}
                                <flux:table.cell
                                    class="w-32 tabular-nums"
                                    align="end"
                                >
                                    {{ $user->id }}
                                </flux:table.cell>

                                {{-- Column for user name. --}}
                                <flux:table.cell>
                                    {!! $highlightSearchMatch($user->name, $search) !!}
                                </flux:table.cell>

                                {{-- Column for user email. --}}
                                <flux:table.cell>
                                    {!! $highlightSearchMatch($user->email, $search) !!}
                                </flux:table.cell>

                                {{-- Column for user roles. --}}
                                <flux:table.cell class="w-64">
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

                                {{-- Column for actions, aligned to the center. --}}
                                <flux:table.cell class="w-36">
                                    <flux:button.group class="justify-center">
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

            {{-- Parameters for pagination controls, including the current page, total pages, and methods for navigating to the first, previous, next, and last pages. The pagination controls display a range of page numbers around the current page, with ellipses indicating skipped page ranges when there are many pages. The controls also include buttons for navigating to the first and last pages, as well as the previous and next pages, with appropriate disabling when on the first or last page. Additionally, a summary of the currently displayed results is shown above the pagination controls. --}}
            <x-ui.table.pagination :paginator="$users" />
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
