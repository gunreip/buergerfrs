{{-- resources/views/components/ui/text/marquee.blade.php --}}

@props([
    'title' => null,
])

<span
    title="{{ $title ?? trim(strip_tags((string) $slot)) }}"
    {{ $attributes->class([
        'group/marquee block min-w-0 overflow-hidden whitespace-nowrap [container-type:inline-size]',
    ]) }}
>
    <span
        class="inline-block max-w-full overflow-hidden text-ellipsis align-bottom group-hover/marquee:max-w-none group-hover/marquee:animate-[ui-marquee_7s_linear_infinite]"
    >
        {{ $slot }}
    </span>
</span>
