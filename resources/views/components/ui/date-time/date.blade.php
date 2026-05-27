{{-- resources/views/components/ui/date-time/date.blade.php --}}

@props([
    'value' => null,
    'format' => config('buergerfrs_formats.date_time.formats.date', 'LL'),
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
