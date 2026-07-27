{{-- resources/views/components/management/people/edit-person/⚡form-person.blade.php --}}

@php
    $formTabs = [
        [
            'name' => 'person',
            'label' => __('Person'),
            'icon' => 'circle-user-round',
        ],
        [
            'name' => 'address',
            'label' => __('ui.address'),
            'icon' => 'map-pin-house',
        ],
        [
            'name' => 'contact',
            'label' => __('Contact'),
            'icon' => 'contact-round',
        ],
        [
            'name' => 'international',
            'label' => __('International'),
            'icon' => 'globe-alt',
        ],
        [
            'name' => 'identification',
            'label' => __('Identification'),
            'icon' => 'id-card',
        ],
        [
            'name' => 'health-insurance',
            'label' => __('Health insurance'),
            'icon' => 'heart-pulse',
        ],
        [
            'name' => 'documents',
            'label' => __('Documents'),
            'icon' => 'file-text',
        ],
        [
            'name' => 'emergency-contact',
            'label' => __('Emergency contact'),
            'icon' => 'siren',
        ],
    ];
@endphp

<flux:tab.group>
    <flux:tabs
        class="min-h-0"
        wire:model.live="activeFormTab"
    >
        @foreach ($formTabs as $formTab)
            <flux:tab
                class="px-4 hover:cursor-pointer"
                name="{{ $formTab['name'] }}"
                icon="{{ $formTab['icon'] }}"
            >
                {{ $formTab['label'] }}
            </flux:tab>
        @endforeach
    </flux:tabs>

    <flux:tab.panel
        class="space-y-6"
        name="person"
    >
        @include('components.management.people.edit-person.sections.⚡person-core')
    </flux:tab.panel>

    <flux:tab.panel
        class="space-y-6"
        name="address"
    >
        @include('components.management.people.edit-person.sections.⚡address')
    </flux:tab.panel>

    <flux:tab.panel
        class="space-y-6"
        name="international"
    >
        @include('components.management.people.edit-person.sections.⚡international')
    </flux:tab.panel>

    <flux:tab.panel
        class="space-y-6"
        name="identification"
    >
        @include('components.management.people.edit-person.sections.⚡identification')
    </flux:tab.panel>

    <flux:tab.panel
        class="space-y-6"
        name="health-insurance"
    >
        @include('components.management.people.edit-person.sections.⚡health-insurance')
    </flux:tab.panel>

    <flux:tab.panel
        class="space-y-6"
        name="documents"
    >
        @include('components.management.people.edit-person.sections.⚡documents')
    </flux:tab.panel>

    <flux:tab.panel
        class="space-y-6"
        name="emergency-contact"
    >
        @include('components.management.people.edit-person.sections.⚡emergency-contact')
    </flux:tab.panel>

    <flux:tab.panel
        class="space-y-6"
        name="contact"
    >
        @include('components.management.people.edit-person.sections.⚡contact')
    </flux:tab.panel>
</flux:tab.group>
