{{-- resources/views/components/admin/⚡translation-sub-languages.blade.php --}}

<flux:card>
    <x-ui.headers.page
        :title="__('Translation Sub-Languages')"
        :description="__('Manage locale variants (for example de_AT) as overlays over their main language.')"
    />

    @include('components.admin.partials.translation-sub-languages.⚡meta')

    @include('components.admin.partials.translation-sub-languages.⚡filter')

    @include('components.admin.partials.translation-sub-languages.⚡info')

    @include('components.admin.partials.translation-sub-languages.⚡table')

    @include('components.admin.partials.translation-sub-languages.⚡translations-table')

    @include('components.admin.partials.translation-sub-languages.⚡modal-edit')
</flux:card>
