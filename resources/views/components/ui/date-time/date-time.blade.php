{{-- resources/views/components/ui/date-time/date-time.blade.php --}}

@props([
    'value' => null,
    'format' => config('buergerfrs_formats.date_time.formats.date_time', 'ddd, LL, LT'),
    'timezone' => config('buergerfrs_formats.date_time.timezone', config('app.timezone')),
    'locale' => app()->getLocale(),
    'color' => 'muted',
    'size' => 'sm',
    'empty' => '—',
])

@php
    $dateTimeValue = $value;

    if ($dateTimeValue instanceof \DateTimeInterface) {
        $dateTimeValue = \Carbon\CarbonImmutable::instance($dateTimeValue)
            ->timezone($timezone)
            ->locale($locale)
            ->isoFormat($format);
    } elseif (filled($dateTimeValue)) {
        $dateTimeValue = \Carbon\CarbonImmutable::parse($dateTimeValue)
            ->timezone($timezone)
            ->locale($locale)
            ->isoFormat($format);
    } else {
        $dateTimeValue = $empty;
    }

    $colorClass = match ($color) {
        'default' => 'text-zinc-700 dark:text-zinc-300',
        'muted' => 'text-zinc-500 dark:text-zinc-400',
        'subtle' => 'text-zinc-400 dark:text-zinc-500',
        'danger' => 'text-red-600 dark:text-red-400',
        'success' => 'text-green-600 dark:text-green-400',
        'warning' => 'text-amber-600 dark:text-amber-400',
        default => $color,
    };

    $sizeClass = match ($size) {
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'base' => 'text-base',
        'lg' => 'text-lg',
        default => $size,
    };
@endphp

<time {{ $attributes->class([$colorClass, $sizeClass]) }}>
    {{ $dateTimeValue }}
</time>
