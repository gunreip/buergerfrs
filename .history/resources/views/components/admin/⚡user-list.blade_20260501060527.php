<?php

use Livewire\Component;

new class extends Component {
    //
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

    <flux:card class="mt-3">
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
                        wire:model="search"
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
                >
                    <flux:radio
                        value="10"
                        wire:model="perPage"
                    >
                        10
                    </flux:radio>

                    <flux:radio
                        value="25"
                        wire:model="perPage"
                    >
                        25
                    </flux:radio>

                    <flux:radio
                        value="50"
                        wire:model="perPage"
                        checked
                    >
                        50
                    </flux:radio>

                    <flux:radio
                        value="100"
                        wire:model="perPage"
                    >
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
            <flux:table>
                <caption class="mb-2 caption-top text-zinc-400 md:caption-bottom">
                    {{ __('List of all registered users. Click on a column header to sort by that column.') }}
                </caption>
                <flux:table.columns>
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
                                {{ $user->name }}
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $user->email }}
                            </flux:table.cell>

                            <flux:table.cell>
                                @foreach ($user->roles as $role)
                                    <span
                                        class="mr-1 inline-block rounded px-2 py-1 text-xs">{{ $role->name }}</span>
                                @endforeach
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                <flux:button.group>
                                    <flux:button
                                        href="{{ route('admin.users.edit', $user) }}"
                                        variant="primary"
                                        color="blue"
                                        size="sm"
                                    >
                                        {{ __('Edit') }}
                                    </flux:button>
                                </flux:button.group>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>

</flux:card>
