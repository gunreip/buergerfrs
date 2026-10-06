<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\Documentation;

/** Common Overview layout by element role. No tab names or per-entry exceptions. */
final class OverviewLayoutDefaults
{
    public static function canvas(): array
    {
        return ['minWidth' => '48rem', 'minHeight' => '64rem', 'horizontalPadding' => '3rem'];
    }

    public static function root(): array
    {
        return ['direction' => 'top-bottom', 'length' => '4rem', 'color' => 'zinc'];
    }

    public static function merge(): array
    {
        return [
            'color' => '', 'startLength' => '0rem', 'bridgeLength' => '4rem',
            'stemLengths' => [1 => '4rem'], 'extensionStartLength' => '0rem',
            'extensionStemLength' => '4rem', 'extensionBridgeLength' => '4rem',
            'extensionStemLengths' => [], 'extensionBridgeContinuations' => [],
            'extensionColors' => [], 'extensionArcRadiuss' => [],
        ];
    }

    public static function tab(): array
    {
        return [...self::markers(), 'direction' => 'top-bottom', 'beforeLength' => '1rem', 'afterLength' => '1rem', 'align' => 'center', 'width' => 'default', 'color' => ''];
    }

    public static function markers(): array
    {
        return ['nodeEnd' => true, 'nodeEndDot' => true];
    }

    public static function levels(): array
    {
        return [
            'subtabs' => [
                ...self::markers(),
                'color' => '', 'direction' => 'top-bottom', 'side' => 'left',
                'stemLength' => '16rem', 'bridgeLength' => '4rem',
                'sideways' => ['extensionLength' => '0rem', 'nodeEnd' => true, 'nodeEndDot' => true, 'extensionEnd' => self::markers()],
                'label' => ['beforeLength' => '2rem', 'afterLength' => '2rem', 'width' => 'default', 'align' => 'center'],
                'endCap' => ['length' => '2rem', 'capLength' => '4rem'],
            ],
            'subsubtabs' => [
                ...self::markers(),
                'stemLength' => '3rem', 'beforeLength' => '0rem', 'labelGap' => '0rem',
                'side' => 'right', 'width' => 'default', 'align' => 'left',
                'endCap' => ['length' => '2rem', 'capLength' => '4rem'],
            ],
        ];
    }
}
