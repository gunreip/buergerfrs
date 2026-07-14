@props([
    'show' => true,
])

@if ($show)
    <flux:badge
        color="amber"
        size="sm"
        variant="subtle"
    >
        {{ __('Test data') }}
    </flux:badge>
@endif
