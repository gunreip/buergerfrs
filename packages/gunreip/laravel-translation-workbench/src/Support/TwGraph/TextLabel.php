<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

use Illuminate\Support\Collection;

class TextLabel
{
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
        $text = data_get($resolvedLabel, 'text');

        foreach (['left', 'right', 'top', 'bottom', 'center'] as $candidateSide) {
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

        $resolvedBadgeColor = data_get(
            $resolvedLabel,
            'badgeColor',
            data_get($resolvedLabel, 'color', $badgeColor),
        );
        $defaults = array_filter([
            'text' => $lines,
            'side' => $side,
            'badgeColor' => $resolvedBadgeColor,
        ], static fn (mixed $value): bool => $value !== null);

        return array_replace($defaults, collect($resolvedLabel)->except(['left', 'right', 'top', 'bottom'])->all(), [
            'text' => $lines,
            'side' => $side,
        ]);
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
