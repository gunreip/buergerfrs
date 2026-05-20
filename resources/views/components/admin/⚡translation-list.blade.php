{{-- resources/views/components/admin/⚡translation-list.blade.php --}}

{{-- @php
    $caseSensitive = true; // Set to false if you want case-insensitive search
    TODO: switch per toggle
@endphp --}}

<flux:card>
    {{-- <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between"> --}}
    <x-ui.headers.page
        :title="__('Translation Management')"
        :description="__('Review translation keys, missing values, native strings, dynamic calls, and audit states.')"
    >
    </x-ui.headers.page>

    {{-- Overview Metablock --}}
    @include('components.admin.partials.translation-list.⚡meta')

    {{-- Filter Part for Translation List --}}
    @include('components.admin.partials.translation-list.⚡filter')

    {{-- Table part --}}
    @include('components.admin.partials.translation-list.⚡table')

    {{-- Translation key review modal --}}
    @include('components.admin.partials.translation-list.⚡modal', [
        'selectedTranslationKey' => $selectedTranslationKey,
    ])

</flux:card>
