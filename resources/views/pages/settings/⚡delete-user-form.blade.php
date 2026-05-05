<?php

use Livewire\Component;

new class extends Component {}; ?>

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>{{ __('Delete account') }}</flux:heading>
        <flux:subheading>{{ __('Delete your account and all of its resources') }}</flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <x-ui.button.delete
            data-test="delete-user-button"
            label="{{ __('Delete account') }}"
            icon="trash"
        />
        {{-- <flux:button
            data-test="delete-user-button"
            variant="danger"
            icon="trash-2"
            color="red"
        >
            {{ __('Delete account') }}
        </flux:button> --}}
    </flux:modal.trigger>

    <livewire:pages::settings.delete-user-modal />
</section>
