<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

/** Record authored merge lengths before resolved values are forwarded to child paths. */
final class MergeLengthProvenance
{
    public static function record(?CalculatedLengths $registry, string $component, string $owner, string $path, array $props, bool $extension = false, array $names = []): void
    {
        if ($registry === null) {
            return;
        }
        $record = static function (string $element, string $property, mixed $value) use ($registry, $component, $owner, $path, $names): void {
            $registry->recordProp($path.'.'.$element, $component, $owner, $names[$property] ?? $property, $value !== null);
        };
        foreach (['start' => 'startLength', 'end' => 'startLength', 'start-shift' => 'startShiftLength', 'bridge' => 'bridgeLength'] as $element => $property) {
            $record($element, $property, $props[$property] ?? null);
        }
        $length = static fn (mixed $entry): mixed => is_array($entry) ? ($entry['length'] ?? $entry[0] ?? null) : $entry;
        $stemCount = 0;
        if ($extension) {
            $record('stem1', 'stemLength', $props['stemLength'] ?? null);
            $stemCount = 1;
        } else {
            $entries = (array) ($props['stemLengths'] ?? []);
            foreach ($entries as $index => $entry) {
                $number = max(1, (int) $index + (array_is_list($entries) ? 1 : 0));
                $value = $length($entry);
                $resolved = $value === null ? null : BoundsRegistry::evaluateRemExpression((string) $value);
                if ($value === null || ($resolved !== null && $resolved <= 0)) {
                    continue;
                }
                $record('stem'.$number, 'stemLengths.'.$index, $value);
                $stemCount++;
            }
        }
        $entries = (array) ($props['stemContinuation'] ?? []);
        foreach ($entries as $index => $entry) {
            $number = max(1, (int) $index + (array_is_list($entries) ? 1 : 0));
            $record('stem'.($stemCount + $number), 'stemContinuation.'.$index, $length($entry));
        }
    }
}
