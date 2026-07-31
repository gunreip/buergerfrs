{{-- resources/views/components/management/people/⚡person-overview.blade.php --}}

<flux:card>
    <x-ui.headers.page
        :title="__('ui.people')"
        :description="__('Review, filter and inspect person records.')"
    />

    @include('components.management.people.person-overview.⚡meta')

    @include('components.management.people.person-overview.⚡filter')

    @include('components.management.people.person-overview.⚡table')
</flux:card>
