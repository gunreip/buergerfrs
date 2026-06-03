{{-- resources/views/components/admin/⚡flag-reference-list.blade.php --}}

<flux:card>
    <x-ui.headers.page
        :title="__('Flag Reference')"
        :description="__('Static-like reference list for locale codes, resolved flags, candidates and editorial comments.')"
    />

    @include('components.admin.partials.flag-reference-list.⚡meta')

    @include('components.admin.partials.flag-reference-list.⚡filter')

    @include('components.admin.partials.flag-reference-list.⚡table')
</flux:card>
