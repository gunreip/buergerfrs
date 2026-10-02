<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

/** Server-side step spacing uses authored lines, never browser wrapping. */
final class StepLabelLayout
{
    public const MAX_LINES = 5;

    public static function resolve(mixed $label, mixed $gap = null, mixed $inheritedOffset = null): array
    {
        $config = is_array($label) ? $label : ['text' => $label];
        $lines = TextLabel::lines(data_get($config, 'text'));
        $offset = Defaults::string(data_get($config, 'offset'), $inheritedOffset, Defaults::graphString('label_offset', '0.75rem'));

        return [
            'lines' => $lines,
            'count' => count($lines),
            'offset' => $offset,
            'gap' => filled($gap) ? (string) $gap : 'calc('.Defaults::stepLabelContentGap(count($lines)).' + ('.$offset.' * 2))',
        ];
    }
}
