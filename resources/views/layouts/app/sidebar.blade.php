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
        class="w-72 border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900"
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

    {{-- Global Tooltip --}}
    <template id="global-tooltip-template">
        <div
            class="my-tooltip pointer-events-auto z-50 max-w-[24rem] rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-zinc-100 opacity-0 shadow-xl transition-opacity duration-200"
            role="tooltip"
        >
            <div class="flex gap-3">
                <flux:icon.information-circle class="mt-0.5 size-5 shrink-0 text-zinc-300" />

                <div class="min-w-0 space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="tooltip-title text-sm font-semibold leading-5"></div>

                        <flux:badge
                            class="tooltip-required-badge text-xs"
                            color="red"
                            inset="top bottom"
                            hidden
                        >
                            {{ __('Required') }}
                        </flux:badge>
                    </div>

                    <div class="tooltip-content text-sm leading-relaxed text-zinc-200"></div>
                </div>
            </div>
        </div>
    </template>

    @fluxScripts
</body>

</html>
