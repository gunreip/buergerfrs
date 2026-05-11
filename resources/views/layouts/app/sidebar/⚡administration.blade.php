{{-- resources/views/layouts/app/sidebar/⚡administration.blade.php --}}

<flux:sidebar.group
    class="mt-4 grid"
    :heading="__('Administration')"
    icon="settings-2"
    expandable
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

    {{-- Admin -> persons --}}
    <flux:sidebar.item
        icon="id-card"
        :href="route('admin.people')"
        :current="request()->routeIs('admin.people')"
        wire:navigate
    >
        {{ __('Persons') }}
    </flux:sidebar.item>

    {{-- Admin -> clients --}}
    <flux:sidebar.item
        icon="building-2"
        :href="route('admin.clients')"
        :current="request()->routeIs('admin.clients')"
        wire:navigate
    >
        {{ __('Clients') }}
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

    {{-- Admin -> permissions --}}
    <flux:sidebar.item
        icon="key-round"
        :href="route('admin.permissions')"
        :current="request()->routeIs('admin.permissions')"
        wire:navigate
    >
        {{ __('Permissions') }}
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
