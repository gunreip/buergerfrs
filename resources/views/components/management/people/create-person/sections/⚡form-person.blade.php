{{-- resources/views/components/management/people/create-person/⚡form-person.blade.php --}}

<flux:card>
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Person Data') }}
    </flux:heading>

    <div class="space-y-4">
        @include('components.management.people.create-person.sections.⚡person-core')

        @include('components.management.people.create-person.sections.⚡contact')

        @include('components.management.people.create-person.sections.⚡address')

        @include('components.management.people.create-person.sections.⚡international')

        @include('components.management.people.create-person.sections.⚡identification')

        @include('components.management.people.create-person.sections.⚡health-insurance')

        @include('components.management.people.create-person.sections.⚡documents')

        @include('components.management.people.create-person.sections.⚡emergency-contact')
    </div>
</flux:card>
