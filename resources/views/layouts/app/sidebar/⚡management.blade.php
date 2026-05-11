{{-- resources/views/layouts/app/sidebar/⚡management.blade.php --}}

<flux:sidebar.group
    class="mt-4 grid"
    :heading="__('Management')"
    icon="briefcase-business"
    expandable
>
    {{-- Management -> person --}}
    <flux:sidebar.group
        class="grid"
        :heading="__('Person')"
        icon="id-card"
        expandable
    >
        {{-- Management -> person -> create --}}
        <flux:sidebar.item
            icon="id-card-lanyard"
            :href="route('management.people.create')"
            :current="request()->routeIs('management.people.create')"
            wire:navigate
        >
            {{ __('Create') }}
        </flux:sidebar.item>
    </flux:sidebar.group>

    {{-- Management -> client --}}
    <flux:sidebar.group
        class="grid"
        :heading="__('Client')"
        icon="building-2"
        expandable
    >
        {{-- Management -> client -> create later --}}
    </flux:sidebar.group>
</flux:sidebar.group>
