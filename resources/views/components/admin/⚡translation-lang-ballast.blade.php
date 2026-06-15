{{-- resources/views/components/admin/⚡translation-lang-ballast.blade.php --}}

<flux:card>
    <x-ui.headers.page
        :title="__('Translation Lang Ballast')"
        :description="__('Review lang/* entries that no longer match the current translation database and export state.')"
    />

    {{-- Overview / Metablock --}}
    @include('components.admin.partials.translation-lang-ballast.⚡meta')

    {{-- Filter Part for Translation Lang Ballast --}}
    @include('components.admin.partials.translation-lang-ballast.⚡filter')

    {{-- Table Part for Translation Lang Ballast --}}
    @include('components.admin.partials.translation-lang-ballast.⚡table')

</flux:card>
