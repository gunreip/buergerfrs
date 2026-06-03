{{-- resources/views/components/ui/text/key-segment-diff.blade.php --}}

@props([
    'currentKey' => '',
    'referenceKey' => '',
    'highlightDifferences' => false,
    'baseClass' => '',
    'diffClass' => 'underline decoration-wavy underline-offset-2 decoration-amber-500 dark:decoration-amber-400',
    'emptyValue' => '—',
])

@php
    $currentKey = trim((string) $currentKey);
    $referenceKey = trim((string) $referenceKey);

    if ($currentKey === '') {
        $segments = [];
    } else {
        $segments = explode('.', $currentKey);
    }

    if ($referenceKey === '') {
        $referenceSegments = [];
    } else {
        $referenceSegments = explode('.', $referenceKey);
    }

    $showDiff = (bool) $highlightDifferences && $referenceKey !== '';

    $currentCount = count($segments);
    $referenceCount = count($referenceSegments);

    $prefixLength = 0;

    if ($showDiff) {
        $maxPrefix = min($currentCount, $referenceCount);

        while (
            $prefixLength < $maxPrefix &&
            $segments[$prefixLength] === $referenceSegments[$prefixLength]
        ) {
            $prefixLength++;
        }
    }

    $suffixLength = 0;

    if ($showDiff) {
        while (
            $suffixLength < ($currentCount - $prefixLength) &&
            $suffixLength < ($referenceCount - $prefixLength) &&
            $segments[$currentCount - 1 - $suffixLength] === $referenceSegments[$referenceCount - 1 - $suffixLength]
        ) {
            $suffixLength++;
        }
    }

    $currentDiffStart = $prefixLength;
    $currentDiffEnd = $currentCount - $suffixLength - 1;

    $hasCurrentDiff = $showDiff && $currentDiffStart <= $currentDiffEnd;

    $renderedSegments = [];

    foreach ($segments as $index => $segment) {
        $isDifferent = $hasCurrentDiff && $index >= $currentDiffStart && $index <= $currentDiffEnd;
        $segmentValue = e($segment);

        if ($isDifferent) {
            $renderedSegments[] = '<span class="' . e($diffClass) . '">' . $segmentValue . '</span>';
        } else {
            $renderedSegments[] = '<span>' . $segmentValue . '</span>';
        }

        if ($index < $currentCount - 1) {
            $renderedSegments[] = '<span class="text-zinc-400 dark:text-zinc-500">.</span>';
        }
    }

    $renderedKeyHtml = implode('', $renderedSegments);
@endphp

@if ($currentKey === '')
    <span class="text-zinc-400">{{ $emptyValue }}</span>
@else
    <span {{ $attributes->class($baseClass) }}>{!! $renderedKeyHtml !!}</span>
@endif
