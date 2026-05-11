{{-- resources/views/layouts/app/sidebar/⚡user-menu.blade.php --}}

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
