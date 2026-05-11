{{-- resources/views/layouts/app/sidebar/⚡mobile-user-menu.blade.php --}}

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
