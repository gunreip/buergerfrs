<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="p-6 max-w-xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Benutzer bearbeiten</h1>
    <div class="mb-4">
        <label class="block font-semibold">Name:</label>
        <span>{{ $user->name }}</span>
    </div>
    <div class="mb-4">
        <label class="block font-semibold">E-Mail:</label>
        <span>{{ $user->email }}</span>
    </div>
    <form wire:submit.prevent="save">
        <div class="mb-4">
            <label class="block font-semibold mb-2">Rollen:</label>
            @foreach($roles as $role)
                <label class="inline-flex items-center mr-4">
                    <input type="checkbox" wire:model="selectedRoles" value="{{ $role->name }}" class="form-checkbox">
                    <span class="ml-2">{{ $role->name }}</span>
                </label>
            @endforeach
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Speichern</button>
        <a href="{{ route('admin.users') }}" class="ml-4 text-gray-600 hover:underline">Zurück</a>
    </form>
    @if (session()->has('success'))
        <div class="mt-4 text-green-600">{{ session('success') }}</div>
    @endif
</div>