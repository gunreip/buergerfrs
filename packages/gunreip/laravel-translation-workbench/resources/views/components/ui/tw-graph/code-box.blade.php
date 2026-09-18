{{-- Inline-size containment keeps long code from widening ancestor flex/grid items. Escaping remains at the call site. --}}
@props([
    'maxHeight' => '48rem',
])

<div
    role="region"
    aria-label="{{ __('Code example') }}"
    tabindex="0"
    {{ $attributes->class(['w-full min-w-0 max-w-full overflow-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700'])->style(['max-height: ' . $maxHeight, 'contain: inline-size']) }}
>
    <pre><code>{!! \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\CodeHighlight::render((string) $slot) !!}</code></pre>
</div>
