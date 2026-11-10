{{-- label-width accepts a CSS length. Items provide a label and arbitrary value content. --}}
@props(['labelWidth' => '12rem'])

<dl {{ $attributes
    ->class('grid items-baseline gap-x-3 gap-y-2 text-sm')
    ->style(['grid-template-columns: minmax(0, '.$labelWidth.') minmax(0, 1fr)']) }}>
    {{ $slot }}
</dl>
