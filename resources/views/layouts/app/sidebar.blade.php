<!DOCTYPE html>
<html
    class="dark"
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
>

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900"
        sticky
        collapsible="mobile"
    >
        <flux:sidebar.header>
            <x-app-logo
                href="{{ route('dashboard') }}"
                :sidebar="true"
                wire:navigate
            />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group
                class="grid"
                :heading="__('Platform')"
            >
                {{-- Platform -> dashboard --}}
                <flux:sidebar.item
                    icon="home"
                    :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')"
                    wire:navigate
                >
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group
                class="mt-4 grid"
                :heading="__('Administration')"
            >
                {{-- Admin -> users --}}
                <flux:sidebar.item
                    icon="users"
                    :href="route('admin.users')"
                    :current="request()->routeIs('admin.users')"
                    wire:navigate
                >
                    {{ __('Users') }}
                </flux:sidebar.item>

                {{-- Admin -> roles --}}
                <flux:sidebar.item
                    icon="shield-check"
                    :href="route('admin.roles')"
                    :current="request()->routeIs('admin.roles')"
                    wire:navigate
                >
                    {{ __('Roles') }}
                </flux:sidebar.item>

                {{-- Admin -> app settings --}}
                <flux:sidebar.item
                    icon="settings"
                    :href="route('admin.app-settings')"
                    :current="request()->routeIs('admin.app-settings')"
                    wire:navigate
                >
                    {{ __('App Settings') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <flux:sidebar.nav>
            {{-- Repository --}}
            <flux:sidebar.item
                href="https://github.com/laravel/livewire-starter-kit"
                icon="folder-git-2"
                target="_blank"
            >
                {{ __('Repository') }}
            </flux:sidebar.item>

            {{-- Documentation --}}
            <flux:sidebar.item
                href="https://laravel.com/docs/starter-kits#livewire"
                icon="book-open-text"
                target="_blank"
            >
                {{ __('Documentation') }}
            </flux:sidebar.item>
        </flux:sidebar.nav>

        <x-desktop-user-menu
            class="hidden lg:block"
            :name="auth()->user()->name"
        />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle
            class="lg:hidden"
            icon="bars-2"
            inset="left"
        />

        <flux:spacer />

        <flux:dropdown
            position="top"
            align="end"
        >
            <flux:profile
                :initials="auth()->user()->initials()"
                icon-trailing="chevron-down"
            />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <x-ui.user-avatar :user="auth()->user()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    {{-- Settings --}}
                    <flux:menu.item
                        :href="route('profile.edit')"
                        icon="cog"
                        wire:navigate
                    >
                        {{ __('Settings') }}
                    </flux:menu.item>

                    {{-- Preferences --}}
                    <flux:menu.item
                        icon="sliders-horizontal"
                        :href="route('account.preferences')"
                        :current="request()->routeIs('account.preferences')"
                        wire:navigate
                    >
                        {{ __('Preferences') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form
                    class="w-full"
                    method="POST"
                    action="{{ route('logout') }}"
                >
                    @csrf
                    <flux:menu.item
                        class="w-full cursor-pointer"
                        data-test="logout-button"
                        type="submit"
                        as="button"
                        icon="arrow-right-start-on-rectangle"
                    >
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

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
