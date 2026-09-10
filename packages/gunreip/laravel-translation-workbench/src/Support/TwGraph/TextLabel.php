<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

use Illuminate\Support\Collection;

class TextLabel
{
    private const SIDE_KEYS = ['left', 'right', 'top', 'bottom', 'center'];

    /**
     * Canonical text label normalization for all tw-graph layers.
     * Components decide placement and options; this class decides how text is
     * converted into renderable lines so Blade views do not drift apart again.
     */
    public static function normalize(mixed $label, ?string $side = null, ?string $badgeColor = null): ?array
    {
        if ($label === false || blank($label) || $label === 'null') {
            return null;
        }

        if (! is_array($label)) {
            return array_filter([
                'text' => self::lines($label),
                'side' => $side,
                'badgeColor' => $badgeColor,
            ], static fn (mixed $value): bool => $value !== null);
        }

        $resolvedLabel = $label;
        if (array_key_exists('connector-length', $resolvedLabel) && ! array_key_exists('connectorLength', $resolvedLabel)) {
            $resolvedLabel['connectorLength'] = $resolvedLabel['connector-length'];
        }

        if (array_key_exists('connector-gap', $resolvedLabel) && ! array_key_exists('connectorGap', $resolvedLabel)) {
            $resolvedLabel['connectorGap'] = $resolvedLabel['connector-gap'];
        }

        $side = $side ?? data_get($resolvedLabel, 'side');
        $text = data_get($resolvedLabel, 'text');

        foreach (self::SIDE_KEYS as $candidateSide) {
            $sideText = data_get($resolvedLabel, $candidateSide);

            if (blank($sideText)) {
                continue;
            }

            $side = $candidateSide;
            $text = $sideText;

            if (is_array($sideText) && array_key_exists('text', $sideText)) {
                $resolvedLabel = array_replace($resolvedLabel, $sideText);
                $text = data_get($sideText, 'text');
            }

            break;
        }

        if (is_array($text) && array_key_exists('text', $text)) {
            $resolvedLabel = array_replace($resolvedLabel, $text);
            $text = data_get($text, 'text');
        }

        $lines = self::lines($text);

        if ($lines === []) {
            return null;
        }

        $resolvedBadgeColor = data_get($resolvedLabel, 'badgeColor')
            ?? data_get($resolvedLabel, 'color')
            ?? $badgeColor;
        $defaults = array_filter([
            'text' => $lines,
            'side' => $side,
            'badgeColor' => $resolvedBadgeColor,
        ], static fn (mixed $value): bool => $value !== null);
        $labelOptions = collect($resolvedLabel)
            ->except(['left', 'right', 'top', 'bottom', 'center', 'connector-length', 'connector-gap'])
            ->filter(static fn (mixed $value): bool => $value !== null)
            ->all();

        return array_replace($defaults, $labelOptions, [
            'text' => $lines,
            'side' => $side,
        ]);
    }

    /**
     * Normalize one node's label declaration into the two render slots used by
     * vertical/horizontal graph segments. Prefer `labels => ['left' => ...]`
     * when the same entry also contains geometry like `length`; direct
     * `left/right/top/bottom` keys are kept as a legacy authoring fallback.
     *
     * @param  list<string>  $geometryKeys
     * @return array<int, array<string, mixed>|null>|true
     */
    public static function nodeLabels(
        mixed $entry,
        string $defaultSide,
        ?string $badgeColor = null,
        array $geometryKeys = [],
    ): array|true {
        if (! is_array($entry)) {
            $label = self::normalize($entry, $defaultSide, $badgeColor);

            return $label ? [$label, null] : true;
        }

        $labelSource = array_key_exists('labels', $entry) ? data_get($entry, 'labels') : $entry;
        $sharedOptions = collect($entry)
            ->except(array_values(array_unique([
                'labels',
                ...self::SIDE_KEYS,
                ...$geometryKeys,
            ])))
            ->all();

        if (is_array($labelSource) && array_is_list($labelSource)) {
            $labels = collect($labelSource)
                ->map(static fn (mixed $label): ?array => self::normalize($label, $defaultSide, $badgeColor))
                ->filter()
                ->values()
                ->all();

            return $labels === [] ? true : array_pad(array_slice($labels, 0, 2), 2, null);
        }

        if (is_array($labelSource)) {
            $directedLabels = collect(self::SIDE_KEYS)
                ->map(static function (string $side) use ($labelSource, $sharedOptions, $badgeColor): ?array {
                    if (! array_key_exists($side, $labelSource) || blank($labelSource[$side])) {
                        return null;
                    }

                    return self::normalize([
                        $side => $labelSource[$side],
                        ...$sharedOptions,
                    ], $side, $badgeColor);
                })
                ->filter()
                ->values()
                ->all();

            if ($directedLabels !== []) {
                return array_pad(array_slice($directedLabels, 0, 2), 2, null);
            }

            $label = self::normalize(array_replace($sharedOptions, $labelSource), $defaultSide, $badgeColor);

            return $label ? [$label, null] : true;
        }

        $label = self::normalize(array_replace($sharedOptions, ['text' => $labelSource]), $defaultSide, $badgeColor);

        return $label ? [$label, null] : true;
    }

    public static function lines(mixed $text): array
    {
        if ($text === false || blank($text) || $text === 'null') {
            return [];
        }

        if (is_array($text) && array_key_exists('text', $text)) {
            return self::lines(data_get($text, 'text'));
        }

        return collect(is_iterable($text) && ! is_string($text) ? $text : explode('|', (string) $text))
            ->flatMap(static function (mixed $line): Collection {
                if (is_array($line)) {
                    if (data_get($line, 'ordinal')) {
                        return collect([$line]);
                    }

                    if (array_key_exists('text', $line)) {
                        return collect(self::lines(data_get($line, 'text')));
                    }

                    return collect($line)->flatMap(fn (mixed $nestedLine): Collection => collect(self::lines($nestedLine)));
                }

                return collect(explode('|', (string) $line))
                    ->map(static fn (string $part): string => trim($part))
                    ->filter(static fn (string $part): bool => $part !== '');
            })
            ->values()
            ->all();
    }
}
