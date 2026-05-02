<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Benutzerverwaltung</h1>
    <input type="text" wire:model="search" placeholder="Suche nach Name oder E-Mail" class="border rounded px-2 py-1 mb-4 w-full" />
    <table class="min-w-full bg-white border rounded shadow">
        <thead>
            <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">E-Mail</th>
                <th class="px-4 py-2">Rollen</th>
                <th class="px-4 py-2">Aktionen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2">
                        @foreach($user->roles as $role)
                            <span class="inline-block bg-gray-200 rounded px-2 py-1 text-xs mr-1">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:underline">Bearbeiten</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>