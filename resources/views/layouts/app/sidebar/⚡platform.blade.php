{{-- resources/views/layouts/app/sidebar/⚡platform.blade.php --}}

<flux:sidebar.group
    class="grid"
    :heading="__('Platform')"
    icon="layout-dashboard"
    expandable
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
