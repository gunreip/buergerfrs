{{-- resources/views/components/admin/⚡fallback-report-list.blade.php --}}

<flux:card>
    <x-ui.headers.page
        :title="__('Fallback Reports')"
        :description="__(
            'Review technical fallback events such as missing icons, missing config values or other recoverable UI fallbacks.',
        )"
    />

    {{-- Overview --}}
    @include('components.admin.partials.fallback-report-list.⚡meta')

    {{-- Filter --}}
    @include('components.admin.partials.fallback-report-list.⚡filter')

    {{-- Table --}}
    @include('components.admin.partials.fallback-report-list.⚡table')

</flux:card>
