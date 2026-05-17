{{-- resources/views/layouts/app/sidebar/⚡administration.blade.php --}}

<flux:sidebar.group
    class="mt-4 grid"
    :heading="__('Administration')"
    icon="settings-2"
    expandable
    :expanded="request()->routeIs('admin.*')"
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

    {{-- Admin -> translations --}}
    <flux:sidebar.item
        icon="languages"
        :href="route('admin.translations')"
        :current="request()->routeIs('admin.translations')"
        wire:navigate
    >
        {{ __('Translations') }}
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

    {{-- Admin -> reference --}}
    <flux:sidebar.group
        class="grid"
        :heading="__('Reference')"
        icon="globe"
        expandable
        :expanded="request()->routeIs('admin.country-references') || request()->routeIs('admin.html-view-audit')"
    >

        {{-- Admin -> reference -> countries --}}
        <flux:sidebar.item
            icon="globe"
            :href="route('admin.country-references')"
            :current="request()->routeIs('admin.country-references')"
            wire:navigate
        >
            {{ __('Countries') }}
        </flux:sidebar.item>

        {{-- Admin -> reference -> HTML tags check --}}
        <flux:sidebar.item
            icon="code-xml"
            :href="route('admin.html-view-audit')"
            :current="request()->routeIs('admin.html-view-audit')"
            wire:navigate
        >
            {{ __('HTML-Tags-Check') }}
        </flux:sidebar.item>

    </flux:sidebar.group>

    {{-- Admin -> fallback reports --}}
    <flux:sidebar.item
        icon="triangle-alert"
        :href="route('admin.fallback-reports')"
        :current="request()->routeIs('admin.fallback-reports')"
        wire:navigate
    >
        {{ __('Fallback Reports') }}
    </flux:sidebar.item>
</flux:sidebar.group>
