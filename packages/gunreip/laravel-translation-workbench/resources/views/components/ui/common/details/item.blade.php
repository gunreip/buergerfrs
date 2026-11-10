{{-- Use item for plain text, or the slot for custom content (takes precedence).
     Attributes (including class) apply to the value cell. --}}
@props(['label', 'item' => null])

<dt class="font-medium">
    <flux:callout.heading>{{ $label }}:</flux:callout.heading>
</dt>
<dd {{ $attributes->class('min-w-0 wrap-break-word') }}>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @else
        <flux:callout.text>{{ $item }}</flux:callout.text>
    @endif
</dd>
