<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

final class DevIdentifier
{
    public static function label(mixed $id): string
    {
        $id = trim((string) $id);

        if ($id === '') {
            return 'tw-graph';
        }

        $label = $id;

        if (preg_match('/(?:^|\.)strang\.([^.]+)(?:\.(.*))?$/', $id, $matches) === 1) {
            $strang = $matches[1];
            $rawTail = (string) ($matches[2] ?? '');
            $strangCounter = self::strangCounter($rawTail);
            $tail = self::withoutStrangCounter($rawTail);
            $section = self::section($tail);
            $segment = self::segment($strang, $tail);
            $prefix = 'strang.' . $strang . ($strangCounter !== null ? '.' . $strangCounter : '') . '.' . $section;

            if ($segment !== null) {
                $labelElement = self::labelElement($tail);
                $path = self::path($strang, $tail);

                if ($labelElement !== null) {
                    $label = $prefix . '.' . $path . '.' . $segment . '.' . $labelElement;

                    return self::canonical($label);
                }

                $label = $prefix . '.' . $path . '.' . $segment . self::anchor($tail);

                return self::canonical($label);
            }

            if (str_starts_with($tail, 'extension.') || str_starts_with($tail, 'branch-return.')) {
                $label = $prefix . '.' . self::path($strang, $tail);

                return self::canonical($label);
            }

            $label = $prefix . '.' . self::element($tail);
        }

        return self::canonical($label);
    }

    private static function canonical(string $label): string
    {
        return ElementIdentifier::normalize($label);
    }

    private static function strangCounter(string $tail): ?int
    {
        $tail = trim($tail, '.');

        if ($tail === '') {
            return null;
        }

        $firstSegment = explode('.', $tail)[0] ?? '';

        return ctype_digit($firstSegment) ? max(1, (int) $firstSegment) : null;
    }

    private static function withoutStrangCounter(string $tail): string
    {
        $tail = trim($tail, '.');

        if ($tail === '') {
            return '';
        }

        $segments = explode('.', $tail);

        if (isset($segments[0]) && ctype_digit($segments[0])) {
            array_shift($segments);
        }

        return implode('.', $segments);
    }

    private static function section(string $tail): string
    {
        if (str_starts_with($tail, 'extension.')) {
            return 'extension' . self::extensionNumber(substr($tail, strlen('extension.')));
        }

        if (preg_match('/^extension(\d+)(?:\.|$)/', $tail, $matches) === 1) {
            return 'extension' . max(1, (int) $matches[1]);
        }

        if (str_starts_with($tail, 'branch-return.')) {
            return 'return' . self::extensionNumber(substr($tail, strlen('branch-return.')));
        }

        return 'main';
    }

    private static function extensionNumber(string $tail): int
    {
        $markers = [
            'arc',
            'bridge',
            'connector',
            'dev-box',
            'end',
            'label',
            'path',
            'paths',
            'start',
            'stem',
            'text',
        ];
        $number = 1;

        foreach (explode('.', trim($tail, '.')) as $segment) {
            if (in_array($segment, $markers, true)) {
                break;
            }

            if (ctype_digit($segment)) {
                $number = (int) $segment;
            }
        }

        return max(1, $number);
    }

    private static function element(string $tail): string
    {
        $segments = explode('.', trim($tail, '.'));

        if (in_array('connector', $segments, true)) {
            return 'connector';
        }

        if (in_array('dev-box', $segments, true)) {
            return 'devBox';
        }

        if (in_array('text', $segments, true) || in_array('label', $segments, true)) {
            return 'label';
        }

        $segmentIndex = self::lastIndexOfAny($segments, ['arc', 'bridge', 'path', 'paths', 'stem']);
        $nodeIndex = self::lastIndexOfAny($segments, ['dev-node-counter', 'node']);

        if ($nodeIndex !== null && ($segmentIndex === null || $nodeIndex > $segmentIndex)) {
            return 'node';
        }

        if ($segmentIndex !== null) {
            return 'segment';
        }

        return 'element';
    }

    private static function segment(string $strang, string $tail): ?string
    {
        if (preg_match('/(?:^|\.)arc\.in(?:\.|$)/', $tail) === 1) {
            return 'arc1-' . self::arcDirection($strang, $tail, 'in');
        }

        if (preg_match('/(?:^|\.)arc\.out(?:\.|$)/', $tail) === 1) {
            return 'arc2-' . self::arcDirection($strang, $tail, 'out');
        }

        if (preg_match('/(?:^|\.)arc(?:\.|$)/', $tail) === 1) {
            return 'arc1-' . self::arcDirection($strang, $tail, 'single');
        }

        if (preg_match('/(?:^|\.)bridge(\d*)(?:\.|$)/', $tail, $matches) === 1) {
            return 'bridge' . max(1, (int) ($matches[1] !== '' ? $matches[1] : 1));
        }

        if (preg_match('/(?:^|\.)stem(?:\.(\d+)|(\d*))?(?:\.|$)/', $tail, $matches) === 1) {
            $stemNumber = (string) ($matches[1] ?? '') !== ''
                ? (int) $matches[1]
                : (int) (($matches[2] ?? '') !== '' ? $matches[2] : 1);

            return 'stem' . max(1, $stemNumber);
        }

        if (preg_match('/(?:^|\.)continuation\.(\d+)(?:\.|$)/', $tail, $matches) === 1) {
            return 'stem' . max(1, (int) $matches[1]);
        }

        if (preg_match('/(?:^|\.)start-stem(?:\.|$)/', $tail) === 1) {
            return 'start.stem';
        }

        if (preg_match('/(?:^|\.)path\.(\d+)(?:\.|$)/', $tail, $matches) === 1) {
            return 'path' . max(1, (int) $matches[1]);
        }

        if (preg_match('/(?:^|\.)path(\d+)(?:\.|$)/', $tail, $matches) === 1) {
            return 'path' . max(1, (int) $matches[1]);
        }

        if (preg_match('/(?:^|\.)start(?:\.|$)/', $tail) === 1) {
            return 'start';
        }

        if (preg_match('/(?:^|\.)end(?:\.|$)/', $tail) === 1) {
            return 'end';
        }

        return null;
    }

    private static function path(string $strang, string $tail): string
    {
        if (str_contains($tail, 'merge-extension')) {
            return 'path.merge-extension';
        }

        if (str_contains($tail, 'branch-return-extension')) {
            return 'path.branch-return-extension';
        }

        if (str_contains($tail, 'branch-return')) {
            return 'path.branch-return';
        }

        if (str_contains($tail, 'paths.trunk') || str_contains($tail, 'path.trunk')) {
            return 'path.trunk';
        }

        if (str_contains($strang, 'merge') && (str_starts_with($tail, 'extension.') || preg_match('/^extension\d+(?:\.|$)/', $tail) === 1)) {
            return 'path.merge-extension';
        }

            if (str_contains($tail, 'rekey-source') || str_contains($strang, 'rekey-source')) {
                return 'path.rekey-source';
            }

            if (str_contains($tail, 'rekey-target') || str_contains($strang, 'rekey-target')) {
                return 'path.rekey-target';
            }

            if (str_contains($tail, 'paths.merge') || str_contains($strang, 'merge')) {
                return 'path.merge';
            }

        if (str_contains($tail, 'branch-extension') || str_starts_with($tail, 'extension.')) {
            return 'path.branch-extension';
        }

        if (str_contains($tail, 'paths.branch') || str_contains($strang, 'branch')) {
            return 'path.branch';
        }

        return 'path';
    }

    private static function anchor(string $tail): string
    {
        if (preg_match('/(?:^|\.)node\.start(?:\.|$)/', $tail) === 1) {
            return '.anchorStart';
        }

        if (preg_match('/(?:^|\.)node\.end(?:\.|$)/', $tail) === 1) {
            return '.anchorEnd';
        }

        return '';
    }

    private static function labelElement(string $tail): ?string
    {
        if (preg_match('/(?:^|\.)(?:label|start-label|end-label)(?:\.|$)/', $tail) !== 1) {
            return null;
        }

        if (preg_match('/(?:^|\.)label\.(start|end)\.(\d+)(?:\.|$)/', $tail, $matches) === 1) {
            $anchor = $matches[1] === 'start' ? 'Start' : 'End';
            $index = max(1, (int) $matches[2]);

            if (preg_match('/(?:^|\.)connector(?:\.|$)/', $tail) === 1) {
                return 'node' . $anchor . 'Label' . $index . '.connector';
            }

            return 'node' . $anchor . 'Label' . $index;
        }

        if (preg_match('/(?:^|\.)start-label(?:\.|$)/', $tail) === 1) {
            return 'startLabel';
        }

        if (preg_match('/(?:^|\.)end-label(?:\.|$)/', $tail) === 1) {
            return 'endLabel';
        }

        if (preg_match('/(?:^|\.)connector(?:\.|$)/', $tail) === 1) {
            return 'connector';
        }

        if (preg_match('/(?:^|\.)text(?:\.|$)/', $tail) === 1 || preg_match('/(?:^|\.)(?:label|start-label|end-label)(?:\.|$)/', $tail) === 1) {
            return 'label';
        }

        return null;
    }

    private static function arcDirection(string $strang, string $tail, string $role): string
    {
        $isLeft = str_ends_with($strang, '-left');
        $isBranch = str_contains($strang, 'branch') || str_contains($strang, 'rekey-target');
        $isMerge = str_contains($strang, 'merge') || str_contains($strang, 'rekey-source');
        $isReturn = str_contains($tail, 'branch-return');
        $isExtension = str_starts_with($tail, 'extension.') || str_contains($tail, 'merge-extension') || str_contains($tail, 'branch-extension');

        if ($isBranch && ! $isReturn) {
            if ($role === 'in') {
                return $isLeft ? 'east-north' : 'west-north';
            }

            return $isLeft ? 'south-west' : 'south-east';
        }

        if ($isMerge || $isReturn) {
            if ($role === 'out') {
                return $isLeft ? 'south-east' : 'south-west';
            }

            return $isLeft ? 'west-north' : 'east-north';
        }

        if ($isExtension) {
            return $isLeft ? 'west-north' : 'east-north';
        }

        return 'arc';
    }

    /**
     * Extension keys may contain words like "node" as part of the attach group.
     * The last semantic marker decides whether the rendered primitive is a
     * segment or an actual node.
     *
     * @param  list<string>  $segments
     * @param  list<string>  $needles
     */
    private static function lastIndexOfAny(array $segments, array $needles): ?int
    {
        $lastIndex = null;

        foreach ($segments as $index => $segment) {
            if (in_array($segment, $needles, true)) {
                $lastIndex = $index;
            }
        }

        return $lastIndex;
    }
}
