{{-- label-width and row-gap accept CSS lengths. Items provide a label and arbitrary value content. --}}
@props(['labelWidth' => '12rem', 'rowGap' => null])

<dl {{ $attributes
    ->class(['grid items-baseline gap-x-3 text-sm', 'gap-y-2' => $rowGap === null])
    ->style([
        'grid-template-columns: minmax(0, '.$labelWidth.') minmax(0, 1fr)',
        'row-gap: '.$rowGap => $rowGap !== null,
    ]) }}>
    {{ $slot }}
</dl>
