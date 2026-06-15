{{-- resources/views/layouts/app/sidebar/⚡management.blade.php --}}

<flux:sidebar.group
    class="mt-4 grid"
    :heading="__('Management')"
    icon="briefcase-business"
    expandable
    :expanded="request()->routeIs('management.*')"
>
    {{-- Management -> person --}}
    <flux:sidebar.group
        class="grid"
        :heading="__('Person')"
        icon="id-card"
        expandable
        :expanded="request()->routeIs('management.people.*')"
    >
        {{-- Management -> person -> create --}}
        <flux:sidebar.item
            icon="id-card-lanyard"
            :href="route('management.people.create')"
            :current="request()->routeIs('management.people.create')"
            wire:navigate
        >
            {{ __('admin.roles.actions.create') }}
        </flux:sidebar.item>
    </flux:sidebar.group>

    {{-- Management -> client --}}
    <flux:sidebar.group
        class="grid"
        :heading="__('layouts.sidebar.management.client')"
        icon="building-2"
        expandable
        :expanded="false"
    >
        {{-- Management -> client -> create later --}}
    </flux:sidebar.group>
</flux:sidebar.group>
