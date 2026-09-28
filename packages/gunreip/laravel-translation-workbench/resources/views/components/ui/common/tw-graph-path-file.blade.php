@props(['path', 'segments' => 3])

@php
    $segmentCount = filter_var($segments, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($segmentCount === false) {
        throw new \InvalidArgumentException('tw-graph-path-file segments must be a positive integer.');
    }
    $normalizedPath = str_replace('\\', '/', (string) $path);
    $pathSegments = preg_split('~/+~', $normalizedPath, -1, PREG_SPLIT_NO_EMPTY);
    $displayPath =
        count($pathSegments) > $segmentCount
            ? '.../' . implode('/', array_slice($pathSegments, -$segmentCount))
            : $normalizedPath;
@endphp

<flux:field {{ $attributes->class(['relative m-3 font-mono text-xs text-zinc-400']) }}>
    <x-translation-workbench::ui.common.component-marker name="translation-workbench::ui.common.tw-graph-path-file" />
    <div class="flex items-center justify-end gap-1">
        <flux:icon.code class="size-4 shrink-0" />
        <span>{{ $displayPath }}</span>
    </div>
</flux:field>
