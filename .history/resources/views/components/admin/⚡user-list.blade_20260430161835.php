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
            Benutzerverwaltung
        </flux:heading>
        <flux:text>
            Verwalten Sie die Benutzer Ihres Systems, weisen Sie Rollen zu und verwalten Sie Berechtigungen.
        </flux:text>
    </fux:field>

    <flux:card class="my-6 grid w-full grid-cols-12 gap-6 md:grid">
        <flux:input.group class="col-span-4 space-x-3">
            <flux:input.group.prefix>
                <flux:icon.magnifying-glass class="size-5 text-zinc-500" />
                <flux:input
                id="user-list-search"
                name="user-list-search"
                type="text"
                {{-- label="Suche" --}}
                copyable
                clearable
                wire:model="search"
                placeholder="Suche nach Name oder E-Mail"
                />
            </flux:input.group.prefix>
        </flux:input.group>
        <flux:input
            class="w-full"
            type="text"
            copyable
            clearable
            wire:model="search"
            placeholder="Suche nach Name oder E-Mail"
        />
    </flux:card>

    <flux:card class="bg-amber-900/60! mb-6 mt-3">
        <div class="mx-auto max-w-4xl p-6">
            <h1 class="mb-4 text-2xl font-bold">Benutzerverwaltung (native HTML)</h1>
            <input
                class="mb-4 w-full rounded border px-2 py-1"
                type="text"
                wire:model="search"
                placeholder="Suche nach Name oder E-Mail"
            />
            <table class="min-w-full rounded border bg-white shadow">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">E-Mail</th>
                        <th class="px-4 py-2">Rollen</th>
                        <th class="px-4 py-2">Aktionen</th>
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
                                >Bearbeiten</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </flux:card>
</flux:card>
