<!DOCTYPE html>
<html
    class="dark"
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
>

{{-- resources/views/layouts/app/sidebar.blade.php --}}

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">

    {{-- Desktop Menu --}}
    <flux:sidebar
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900"
        sticky
        collapsible
    >
        {{-- Brand --}}
        @include('layouts.app.sidebar.⚡brand')

        {{-- Menu --}}
        <flux:sidebar.nav>
            {{-- Platform --}}
            @include('layouts.app.sidebar.⚡platform')

            {{-- Administration --}}
            @include('layouts.app.sidebar.⚡administration')

            {{-- Management --}}
            @include('layouts.app.sidebar.⚡management')
        </flux:sidebar.nav>

        <flux:spacer />

        {{-- User Menu --}}
        @include('layouts.app.sidebar.⚡user-menu')
    </flux:sidebar>

    {{-- Mobile User Menu --}}
    @include('layouts.app.sidebar.⚡mobile-user-menu')

    {{ $slot }}

    {{-- Global Toast --}}
    @persist('toast')
        <flux:toast.group
            position="top end"
            expanded
        >
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
