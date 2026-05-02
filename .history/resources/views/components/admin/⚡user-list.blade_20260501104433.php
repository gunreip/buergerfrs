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
        ->replaceMatches('/' . preg_quote($escapedSearch, '/') . '/iu', '<mark >$0</mark>')
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
        <div class="flex w-full items-end gap-3">

            <div class="min-w-0 flex-none basis-1/3">
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

        <div class="mx-auto max-w-4xl p-3">
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
                                    @foreach ($user->roles as $role)
                                        <span class="mr-1 inline-block rounded px-2 py-1 text-xs">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:button.group>
                                        <flux:button
                                            href="{{ route('admin.users.edit', $user) }}"
                                            variant="primary"
                                            color="sky"
                                            size="sm"
                                        >
                                            {{ __('Edit') }}

                                            <flux:separator
                                                class="mx-1"
                                                vertical
                                                variant="subtle"
                                            />

                                            <flux:icon.pencil-square variant="mini" />
                                        </flux:button>
                                    </flux:button.group>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>

            @if ($users->hasPages())
                <div class="mt-4 flex items-center justify-center gap-3">
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

                    <flux:text class="text-sm text-zinc-400">
                        {{ __('Page') }} {{ $users->currentPage() }} / {{ $users->lastPage() }}
                    </flux:text>
                </div>
            @endif
        </div>
    </flux:card>
</flux:card>
