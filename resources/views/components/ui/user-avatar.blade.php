{{-- resources/views/components/ui/user-avatar.blade.php --}}

@props([
    'user' => null,
    'size' => 'md',
    'class' => '',
])

@php
    $sizeClass = match ($size) {
        'xs' => 'size-6',
        'sm' => 'size-8',
        'md' => 'size-10',
        'lg' => 'size-14',
        'xl' => 'size-24',
        default => 'size-10',
    };

    $imageClass = trim($sizeClass . ' rounded-2xl object-cover ring-1 ring-zinc-700 ' . $class);
@endphp

@if ($avatarUrl !== null)
    <img
        class="{{ $imageClass }}"
        src="{{ $avatarUrl }}"
        alt="{{ $user?->name ? __('Avatar for :name', ['name' => $user->name]) : __('User avatar') }}"
    >
@else
    <flux:avatar
        class="{{ trim($sizeClass . ' ' . $class) }}"
        :name="$user?->name"
        :initials="$user?->initials()"
    />
@endif
