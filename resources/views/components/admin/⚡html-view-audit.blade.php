{{-- resources/views/components/admin/⚡html-view-audit.blade.php --}}

{{-- @php
    $caseSensitive = true; // Set to false if you want case-insensitive search
    TODO: switch per toggle
@endphp --}}

<flux:card>
    <x-ui.headers.page
        :title="__('HTML-Tags-Check')"
        :description="__('Static audit for native HTML tags and selected Flux/custom Blade components.')"
    />

    @include('components.admin.partials.html-view-audit.⚡meta', [
        'audit' => $audit,
        'filteredProblemCount' => $filteredProblemCount,
        'hasActiveFilters' => $hasActiveFilters,
    ])

    @include('components.admin.partials.html-view-audit.⚡filter')

    @include('components.admin.partials.html-view-audit.⚡table', [
        'problems' => $problems,
    ])
</flux:card>
