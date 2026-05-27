{{-- resources/views/components/ui/date-time/time.blade.php --}}

@props([
    'value' => null,
    'format' => config('buergerfrs_formats.date_time.formats.time', 'LT'),
    'timezone' => config('buergerfrs_formats.date_time.timezone', config('app.timezone')),
    'locale' => app()->getLocale(),
    'color' => 'muted',
    'size' => 'sm',
    'empty' => '—',
])

<x-ui.date-time.date-time
    :value="$value"
    :format="$format"
    :timezone="$timezone"
    :locale="$locale"
    :color="$color"
    :size="$size"
    :empty="$empty"
    {{ $attributes }}
/>
