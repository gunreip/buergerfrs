{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/text/highlight.blade.php --}}

@props([
    'value' => '',
    'search' => '',
    'exact' => false,
    'caseSensitive' => false,
    'markClass' => 'rounded bg-amber-200 px-0.5 text-amber-950 dark:bg-amber-400/30 dark:text-amber-100',
])

@php
    $highlightValue = (string) $value;
    $highlightSearch = trim((string) $search);
    $escapedValue = e($highlightValue);
    $highlightExact = (bool) $exact;
    $highlightCaseSensitive = (bool) $caseSensitive;
    $highlighted = $escapedValue;

    if (mb_strlen($highlightSearch) >= 2) {
        if ($highlightExact) {
            $matches = $highlightCaseSensitive
                ? $highlightValue === $highlightSearch
                : mb_strtolower($highlightValue) === mb_strtolower($highlightSearch);

            if ($matches) {
                $highlighted = '<mark class="' . e($markClass) . '">' . $escapedValue . '</mark>';
            }
        } else {
            $escapedSearch = e($highlightSearch);
            $pattern = '/' . preg_quote($escapedSearch, '/') . '/' . ($highlightCaseSensitive ? 'u' : 'iu');

            $highlighted = preg_replace(
                $pattern,
                '<mark class="' . e($markClass) . '">$0</mark>',
                $escapedValue,
            ) ?: $escapedValue;
        }
    }
@endphp

{!! $highlighted !!}
