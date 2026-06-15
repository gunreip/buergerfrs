{{-- resources/views/components/admin/⚡translation-usage-audit.blade.php --}}

{{-- @php
    $caseSensitive = true; // Set to false if you want case-insensitive search
    TODO: switch per toggle
@endphp --}}

<flux:card
    x-data="{}"
    x-on:translation-usage-audit-review.window="$wire.openUsageAuditModal($event.detail)"
>
    <x-ui.headers.page
        :title="__('Translation Usage')"
        :description="__('Review duplicate and frequent source-language literals for possible centralization.')"
    />

    {{-- Overwview Metablock --}}
    @include('components.admin.partials.translation-usage-audit.⚡meta')

    {{-- Filter Part for Translation Usage Audit --}}
    @include('components.admin.partials.translation-usage-audit.⚡filter')

    {{-- Table Part for Translation Usage Audit --}}
    @include('components.admin.partials.translation-usage-audit.⚡table')

    {{-- Review Modal Part for Translation Usage Audit --}}
    @include('components.admin.partials.translation-usage-audit.⚡modal')

    {{-- Edit Modal Part for Translation Usage Audit --}}
    @include('components.admin.partials.translation-usage-audit.⚡modal-edit')

</flux:card>
