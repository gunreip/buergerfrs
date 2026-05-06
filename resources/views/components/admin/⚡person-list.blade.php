{{-- resources/views/components/admin/⚡person-list.blade.php --}}

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
        :title="__('People Management')"
        :description="__('Review natural persons, linked users, and client assignments.')"
    />

    {{-- Overview --}}
    @include('components.admin.partials.person-list.⚡meta')

    {{-- Filter --}}
    @include('components.admin.partials.person-list.⚡filter')

    {{-- Table --}}
    @include('components.admin.partials.person-list.⚡table')

</flux:card>
