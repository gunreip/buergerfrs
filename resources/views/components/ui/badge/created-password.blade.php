@props([
    'password' => '',
])

@if ($password !== '')
    <x-ui.tooltip.trigger
        title="{{ __('ui.badge.created_password.generated_temporary_password') }}"
        text="{{ __('ui.badge.created_password.tooltip.temporary_password_password_this_password_is_shown_once_here_and_is_also_written_to', [
            'password' => $password,
        ]) }}"
        context="warning"
    >
        <flux:badge
            color="orange"
            size="sm"
            variant="subtle"
        >
            {{ __('ui.badge.created_password.temporary_password') }}
        </flux:badge>
    </x-ui.tooltip.trigger>
@endif
