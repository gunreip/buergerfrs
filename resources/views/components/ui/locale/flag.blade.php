{{-- resources/views/components/ui/locale/flag.blade.php --}}

@props(['locale', 'size' => 'sm', 'title' => null])

@php
    $normalizedLocale = str_replace('_', '-', strtolower((string) $locale));
    $localeParts = array_values(array_filter(explode('-', $normalizedLocale)));

    $language = $localeParts[0] ?? '';
    $region = $localeParts[1] ?? null;

    $sizeClass = match ($size) {
        'xs' => 'h-3 w-3',
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
        'xl' => 'h-8 w-8',
        default => (string) $size,
    };

    $countrySizeClass = match ($size) {
        'xs' => 'h-3 w-4',
        'sm' => 'h-4 w-6',
        'md' => 'h-5 w-7',
        'lg' => 'h-6 w-9',
        'xl' => 'h-8 w-12',
        default => (string) $size,
    };

    $languageComponent = $language !== '' ? 'flag-circle-language-' . $language : null;

    $countryComponent = $region !== null && $region !== '' ? 'flag-country-' . strtolower($region) : null;

    $component = $countryComponent ?: $languageComponent;
    $class = $countryComponent ? $countrySizeClass : $sizeClass;

    $label = $title ?: strtoupper((string) $locale);
@endphp

@if ($component)
    <span
        aria-label="{{ $label }}"
        {{-- title="{{ $label }}" --}}
        {{ $attributes->class('inline-flex shrink-0 items-center align-middle') }}
    >
        <x-dynamic-component
            class="{{ $class }}"
            :component="$component"
        />
    </span>
@else
    <span
        aria-label="{{ $label }}"
        {{-- title="{{ $label }}" --}}
        {{ $attributes->class('inline-flex shrink-0 items-center rounded bg-zinc-100 px-1.5 py-0.5 text-xs font-semibold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400') }}
    >
        {{ strtoupper((string) $locale) }}
    </span>
@endif
