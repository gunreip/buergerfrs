<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('ui.labels.name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('pages.auth.register.full_name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('pages.auth.register.email_address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('pages.auth.register.password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('pages.auth.register.password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('pages.auth.register.confirm_password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('pages.auth.register.confirm_password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('pages.auth.register.create_account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('pages.auth.register.already_have_an_account') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('pages.auth.register.log_in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
