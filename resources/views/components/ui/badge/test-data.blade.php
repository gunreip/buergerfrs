@props([
    'show' => true,
])

@if ($show)
    <flux:badge
        color="amber"
        size="sm"
        variant="subtle"
    >
        {{ __('ui.badge.test-data') }}
    </flux:badge>
@endif
