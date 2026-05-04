{{-- resources/views/components/desktop-user-menu.blade.php --}}

<flux:dropdown
    {{ $attributes }}
    position="bottom"
    align="start"
>
    {{-- Desktop user menu trigger --}}
    <button
        class="flex w-full items-center gap-2 rounded-lg px-2 py-2 text-start transition hover:bg-zinc-800/70 focus:outline-none focus:ring-2 focus:ring-sky-500/70"
        data-test="sidebar-menu-button"
        type="button"
    >
        <x-ui.user-avatar
            :user="auth()->user()"
            size="sm"
        />

        <div class="grid min-w-0 flex-1 text-start text-sm leading-tight">
            <x-ui.text.marquee class="font-medium text-zinc-100">
                {{ auth()->user()->name }}
            </x-ui.text.marquee>

            <x-ui.text.marquee class="text-zinc-400">
                {{ auth()->user()->email }}
            </x-ui.text.marquee>
        </div>

        <flux:icon.chevrons-up-down class="size-4 shrink-0 text-zinc-400" />
    </button>

    <flux:menu>
        {{-- Current user --}}
        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
            <x-ui.user-avatar
                :user="auth()->user()"
                size="md"
            />

            <div class="grid flex-1 text-start text-sm leading-tight">
                <flux:heading class="truncate">
                    {{ auth()->user()->name }}
                </flux:heading>

                <flux:text class="truncate">
                    {{ auth()->user()->email }}
                </flux:text>
            </div>
        </div>

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
                :href="route('account.preferences')"
                :current="request()->routeIs('account.preferences')"
                icon="sliders-horizontal"
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
