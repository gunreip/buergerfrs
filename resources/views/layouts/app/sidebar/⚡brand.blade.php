{{-- resources/views/layouts/app/sidebar/⚡brand.blade.php --}}

<flux:sidebar.header>
    <x-app-logo
        href="{{ route('dashboard') }}"
        :sidebar="true"
        wire:navigate
    />

    <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
</flux:sidebar.header>
