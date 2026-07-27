@props([
    'personNumber' => '',
    'personId' => null,
    'userId' => null,
])

@if ($personNumber !== '' || $personId !== null || $userId !== null)
    <x-ui.tooltip.trigger
        title="{{ __('ui.badge.create_person.person_created') }}"
        text="{{ __('Person number: :personNumber | Created person ID: :personId | Created user ID: :userId', [
            'personNumber' => $personNumber !== '' ? $personNumber : '—',
            'personId' => $personId ?? '—',
            'userId' => $userId ?? '—',
        ]) }}"
        context="success"
    >
        <flux:badge
            color="green"
            size="sm"
            variant="subtle"
        >
            {{ __('ui.badge.create_person.person_created') }}
        </flux:badge>
    </x-ui.tooltip.trigger>
@endif
