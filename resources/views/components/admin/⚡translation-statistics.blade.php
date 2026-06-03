{{-- resources/views/components/admin/⚡translation-statistics.blade.php --}}

<flux:card>

    <x-ui.headers.page
        :title="__('Translation Statistics')"
        :description="__('Coverage overview per target language and key health across the translation system.')"
    />

    {{-- Key Health: summary callouts --}}
    @include('components.admin.partials.translation-statistics.⚡meta')

    {{-- Distribution charts: by status + by classification --}}
    @include('components.admin.partials.translation-statistics.⚡charts')

    {{-- Language Coverage table --}}
    @include('components.admin.partials.translation-statistics.⚡table')

</flux:card>
