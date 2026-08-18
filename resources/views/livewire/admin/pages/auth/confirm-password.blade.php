<x-layouts::auth :title="__('pages.auth.register.confirm_password')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('pages.auth.register.confirm_password')"
            :description="__('This is a secure area of the application. Please confirm your password before continuing.')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="password"
                :label="__('pages.auth.register.password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('pages.auth.register.password')"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="confirm-password-button">
                {{ __('ui.button.confirm.confirm') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
