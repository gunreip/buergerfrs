{{-- resources/views/components/ui/date-time/date-time.blade.php --}}

{{--
    Date/time format tokens use Carbon isoFormat()/Moment-style tokens.

    Date:
    - ddd      Short weekday, e.g. Mon
    - dddd     Full weekday, e.g. Monday
    - DD       Day with leading zero, e.g. 02
    - D        Day without leading zero, e.g. 2
    - MMM      Short month name, e.g. Jul
    - MMMM     Full month name, e.g. July
    - MM       Month number with leading zero, e.g. 07
    - M        Month number without leading zero, e.g. 7
    - YYYY     Four-digit year, e.g. 2026
    - YY       Two-digit year, e.g. 26
    - LL       Localized date, e.g. July 2, 2026

    Time:
    - HH       24-hour with leading zero, e.g. 09
    - H        24-hour without leading zero, e.g. 9
    - hh       12-hour with leading zero, e.g. 09
    - h        12-hour without leading zero, e.g. 9
    - mm       Minutes with leading zero, e.g. 05
    - ss       Seconds with leading zero, e.g. 09
    - SSS      Milliseconds, e.g. 123
    - A        AM/PM
    - LT       Localized time
    - LTS      Localized time with seconds

    Timezone:
    - Z        Offset, e.g. +02:00
    - ZZ       Offset, e.g. +0200

    Example:
    - ddd, DD.MMM.YYYY, HH:mm:ss.SSS
--}}

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
        'callout-text-blue' => 'text-blue-600/70 dark:text-blue-300/70',
        'callout-text-sky' => 'text-sky-600/70 dark:text-sky-300/70',
        'callout-text-red' => 'text-red-600/70 dark:text-red-300/70',
        'callout-text-orange' => 'text-orange-600/70 dark:text-orange-300/70',
        'callout-text-amber' => 'text-amber-600/70 dark:text-amber-300/70',
        'callout-text-yellow' => 'text-yellow-600/70 dark:text-yellow-300/70',
        'callout-text-lime' => 'text-lime-600/70 dark:text-lime-300/70',
        'callout-text-green' => 'text-green-600/70 dark:text-green-300/70',
        'callout-text-emerald' => 'text-emerald-600/70 dark:text-emerald-300/70',
        'callout-text-teal' => 'text-teal-600/70 dark:text-teal-300/70',
        'callout-text-cyan' => 'text-cyan-600/70 dark:text-cyan-300/70',
        'callout-text-indigo' => 'text-indigo-600/70 dark:text-indigo-300/70',
        'callout-text-violet' => 'text-violet-600/70 dark:text-violet-300/70',
        'callout-text-purple' => 'text-purple-600/70 dark:text-purple-300/70',
        'callout-text-fuchsia' => 'text-fuchsia-600/70 dark:text-fuchsia-300/70',
        'callout-text-pink' => 'text-pink-600/70 dark:text-pink-300/70',
        'callout-text-rose' => 'text-rose-600/70 dark:text-rose-300/70',
        'callout-text-zinc' => 'text-zinc-500/70 dark:text-zinc-300/70',
        'callout-text-default' => 'text-zinc-500/70 dark:text-zinc-300/70',
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
