@props([
    'password' => '',
])

@if ($password !== '')
    <x-ui.tooltip.trigger
        title="{{ __('Generated temporary password') }}"
        text="{{ __('Temporary password: :password | This password is shown once here and is also written to the local development JSONL password log.', [
            'password' => $password,
        ]) }}"
        context="warning"
    >
        <flux:badge
            color="orange"
            size="sm"
            variant="subtle"
        >
            {{ __('Temporary password') }}
        </flux:badge>
    </x-ui.tooltip.trigger>
@endif
