<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<flux:card>
    <fux:field>
        <flux:heading>
            Benutzerverwaltung
        </flux:heading>
        <flux:text>
            Verwalten Sie die Benutzer Ihres Systems, weisen Sie Rollen zu und verwalten Sie Berechtigungen.
        </flux:text>
    </fux:field>

    <flux:field>
        <p>Hier können Sie die Benutzer Ihres Systems verwalten. Sie können Benutzer bearbeiten, Rollen zuweisen und
            Berechtigungen verwalten.</p>
    </flux:field>
</flux:card>

<div class="mx-auto max-w-4xl p-6">
    <h1 class="mb-4 text-2xl font-bold">Benutzerverwaltung</h1>
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
