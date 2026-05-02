<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<flux:card>
    <fux:field space="md">
        <flux:heading
            class="mb-1"
            size="xl"
        >
            {{ __('User Management') }}
        </flux:heading>
        <flux:text>
            {{ __('Manage your system\'s users, assign roles, and manage permissions') }}.
        </flux:text>
    </fux:field>

    <flux:card class="mt-3">
        <div class="grid grid-cols-6 place-content-stretch gap-4">
            <div class="grid grid-cols-2 gap-1">
                <flux:label for="user-list-search">{{ __('Search') }}</flux:label>
                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.magnifying-glass />
                    </flux:input.group.prefix>
                    <flux:input
                        class="w-full"
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
            <div>Dings</div>
            <div class="col-span-2">
                <flux:radio.group
                    id="user-list-per-page"
                    name="user-list-per-page"
                    label="{{ __('Per Page') }}"
                    variant="segmented"
                >
                    <flux:radio
                        value="10"
                        wire:model="perPage"
                    >10</flux:radio>
                    <flux:radio
                        value="25"
                        wire:model="perPage"
                    >25</flux:radio>
                    <flux:radio
                        value="50"
                        wire:model="perPage"
                        checked
                    >50</flux:radio>
                    <flux:radio
                        value="100"
                        wire:model="perPage"
                    >100</flux:radio>
                </flux:radio.group>
            </div>

    </flux:card>

    <flux:card class="bg-amber-900/60! mb-6 mt-3">
        <div class="mx-auto max-w-4xl p-6">
            <h1 class="mb-4 text-2xl font-bold">{{ __('User Management (native HTML)') }}</h1>
            <input
                class="mb-4 w-full rounded border px-2 py-1"
                type="text"
                wire:model="search"
                placeholder="{{ __('Search by name or email') }}"
            />
            <table class="min-w-full rounded border bg-white shadow">
                <thead>
                    <tr>
                        <th class="px-4 py-2">{{ __('Name') }}</th>
                        <th class="px-4 py-2">{{ __('E-Mail') }}</th>
                        <th class="px-4 py-2">{{ __('Roles') }}</th>
                        <th class="px-4 py-2">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $user->name }}</td>
                            <td class="px-4 py-2">{{ $user->email }}</td>
                            <td class="px-4 py-2">
                                @foreach ($user->roles as $role)
                                    <span
                                        class="mr-1 inline-block rounded bg-gray-200 px-2 py-1 text-xs">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="px-4 py-2">
                                <a
                                    class="text-blue-600 hover:underline"
                                    href="{{ route('admin.users.edit', $user) }}"
                                >{{ __('Edit') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </flux:card>
</flux:card>
