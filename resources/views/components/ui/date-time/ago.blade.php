{{-- resources/views/components/ui/date-time/ago.blade.php --}}

@props([
    'value' => null,
    'timezone' => config('buergerfrs_formats.date_time.timezone', config('app.timezone')),
    'locale' => app()->getLocale(),
    'color' => 'muted',
    'size' => 'sm',
    'empty' => '—',
])

@php
    $dateTime = null;
    $dateTimeValue = $empty;
    $dateTimeIso = null;
    $dateTimeTitle = null;

    if ($value instanceof \DateTimeInterface) {
        $dateTime = \Carbon\CarbonImmutable::instance($value)->timezone($timezone)->locale($locale);
    } elseif (filled($value)) {
        $dateTime = \Carbon\CarbonImmutable::parse($value)->timezone($timezone)->locale($locale);
    }

    if ($dateTime !== null) {
        $dateTimeValue = $dateTime->diffForHumans();
        $dateTimeIso = $dateTime->toIso8601String();
        $dateTimeTitle = $dateTime->isoFormat(config('buergerfrs_formats.date_time.formats.date_time', 'ddd, LL, LT'));
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

<time
    @if ($dateTimeIso !== null) datetime="{{ $dateTimeIso }}" @endif
    @if ($dateTimeTitle !== null) title="{{ $dateTimeTitle }}" @endif
    {{ $attributes->class([$colorClass, $sizeClass]) }}
>
    {{ $dateTimeValue }}
</time>
