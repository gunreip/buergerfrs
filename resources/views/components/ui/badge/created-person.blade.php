@props([
    'personNumber' => '',
    'personId' => null,
    'userId' => null,
])

@if ($personNumber !== '' || $personId !== null || $userId !== null)
    <x-ui.tooltip.trigger
        title="{{ __('ui.badge.create_person.person_created') }}"
        text="{{ __('ui.badge.created_person.tooltip.person_number_person_number_created_person_id_person_id_created_user_id_user_id', [
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
