<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="mx-auto max-w-xl p-6">
    <h1 class="mb-4 text-2xl font-bold">Benutzer bearbeiten</h1>
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
            <label class="mb-2 block font-semibold">Rollen:</label>
            @foreach ($roles as $role)
                <label class="mr-4 inline-flex items-center">
                    <input
                        class="form-checkbox"
                        type="checkbox"
                        value="{{ $role->name }}"
                        wire:model="selectedRoles"
                    >
                    <span class="ml-2">{{ $role->name }}</span>
                </label>
            @endforeach
        </div>
        <button
            class="rounded bg-blue-600 px-4 py-2 text-white"
            type="submit"
        >Speichern</button>
        <a
            class="ml-4 text-gray-600 hover:underline"
            href="{{ route('admin.users') }}"
        >Zurück</a>
    </form>
    @if (session()->has('success'))
        <div class="mt-4 text-green-600">{{ session('success') }}</div>
    @endif
</div>
