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
            class="my-tooltip z-9999 pointer-events-auto max-w-[24rem] px-4 py-3 opacity-0 transition-opacity duration-200"
            role="tooltip"
        >
            <div class="flex gap-3">
                <flux:icon.information-circle class="mt-0.5 size-5 shrink-0" />

                <div class="min-w-0 space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="tooltip-title text-sm font-semibold leading-5"></div>

                        <flux:badge
                            class="tooltip-required-badge text-xs"
                            color="red"
                            inset="top bottom"
                            hidden
                        >
                            {{ __('ui.form.tab_status_dot.required') }}
                        </flux:badge>
                    </div>

                    <div class="tooltip-content text-sm leading-relaxed"></div>

                    <div
                        class="tooltip-action mt-2 flex items-center gap-3 border-t pt-2"
                        {{-- class="tooltip-action mt-2 flex flex-col items-start gap-2 border-t border-zinc-700/70 pt-2" --}}
                        hidden
                    >
                        <span class="tooltip-action-text min-w-0 flex-1 text-xs leading-relaxed"></span>

                        <flux:button
                            class="tooltip-action-button h-8 px-3 text-xs font-semibold"
                            type="button"
                            size="sm"
                            variant="primary"
                        ></flux:button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    @fluxScripts
</body>

</html>
