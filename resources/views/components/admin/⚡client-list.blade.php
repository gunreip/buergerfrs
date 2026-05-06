{{-- resources/views/components/admin/⚡client-list.blade.php --}}

@php
    $highlightSearchMatch = static function (?string $value, ?string $search): string {
        $value = (string) $value;
        $search = trim((string) $search);

        if ($search === '') {
            return e($value);
        }

        $escapedValue = e($value);
        $escapedSearch = e($search);

        return Str::of($escapedValue)
            ->replaceMatches(
                '/' . preg_quote($escapedSearch, '/') . '/iu',
                '<mark class="rounded bg-amber-400/20 px-0.5 text-amber-100">$0</mark>',
            )
            ->toString();
    };
@endphp

<flux:card>
    <x-ui.headers.page
        :title="__('Client Management')"
        :description="__('Review organizations, client status, and assigned people.')"
    />

    {{-- Partial: Overview --}}
    @include('components.admin.partials.client-list.⚡meta')

    {{-- Partial: Filter --}}
    @include('components.admin.partials.client-list.⚡filter')

    {{-- Partial: Table --}}
    @include('components.admin.partials.client-list.⚡table')
</flux:card>
