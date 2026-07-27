{{-- resources/views/components/admin/⚡activity-log.blade.php --}}

<flux:card>
    <x-ui.headers.page
        :title="__('ui.activity-log')"
        :description="__(
            'Review application activity_log entries. Detailed properties and change payloads will be opened in a modal in a later step.',
        )"
    />

    {{-- Overview / Metablock --}}
    @include('components.admin.partials.activity-log.⚡meta')

    {{-- Filter Part for Activity Log --}}
    @include('components.admin.partials.activity-log.⚡filter')

    {{-- Table Part for Activity Log --}}
    @include('components.admin.partials.activity-log.⚡table')

    {{-- Modal-Fenster for Activity Log Details --}}
    @include('components.admin.partials.activity-log.⚡modal')

</flux:card>
