<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\Defaults;

final class LabelWidth
{
    /** Resolve only authored label/continuation props, never source data or diagnostic records. */
    public static function preview(array $preview): array
    {
        foreach (['start_label', 'end_label', 'node_labels', 'extension_node_labels',
            'stem_continuation', 'extension_stem_continuations', 'step', 'branch_extension'] as $key) {
            if (is_array($preview[$key] ?? null)) {
                $preview[$key] = self::labels($preview[$key]);
            }
        }

        return $preview;
    }

    private static function labels(array $value, array $inherited = []): array
    {
        $widthOptions = array_intersect_key($value, array_flip(['width', 'long', 'halfLong', 'half']));
        $inherited = array_replace($inherited, $widthOptions);
        if (array_key_exists('text', $value)) {
            return self::resolve(array_replace($inherited, $value));
        }

        foreach ($value as $key => $entry) {
            if (in_array($key, ['left', 'right', 'top', 'bottom', 'center', 'start', 'end'], true)
                && (is_string($entry) || (is_array($entry) && array_is_list($entry) && $entry !== []))) {
                $value[$key] = self::resolve([...$inherited, 'text' => $entry]);
            } elseif (is_array($entry)) {
                $value[$key] = self::labels($entry, $inherited);
            }
        }

        return $value;
    }

    /** Estimate text width server-side; browser wrapping may still occur at the largest size. */
    public static function resolve(array $label): array
    {
        if (filled($label['width'] ?? null) || ($label['long'] ?? false)
            || ($label['halfLong'] ?? false) || ($label['half'] ?? false)) {
            return $label;
        }

        $longest = 0;
        foreach ((array) ($label['text'] ?? []) as $line) {
            if (is_array($line)) {
                $line = (string) data_get($line, 'ordinal.number', '')
                    .(string) data_get($line, 'ordinal.suffix', '').' '.($line['text'] ?? '');
            }
            foreach (preg_split('/\R/u', (string) $line) ?: [] as $part) {
                $longest = max($longest, mb_strwidth(html_entity_decode(strip_tags($part), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
            }
        }

        // Approximate average glyph width in rem; shared by every line, including secondary text.
        $required = $longest * 0.42;
        foreach (['default' => '12rem', 'halfLong' => '18rem', 'long' => '24rem'] as $width => $fallback) {
            $key = $width === 'halfLong' ? 'half_long' : $width;
            $available = BoundsRegistry::evaluateRemExpression(Defaults::graphString('label_width.'.$key, $fallback));
            if ($required <= $available || $width === 'long') {
                $label['width'] = $width;
                break;
            }
        }

        return $label;
    }
}
