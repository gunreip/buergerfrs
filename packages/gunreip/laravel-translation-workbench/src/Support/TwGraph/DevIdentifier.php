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

        if (str_contains($id, 'strang.')) {
            return self::compact(ElementIdentifier::normalize($id));
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

        return self::compact(self::canonical($label));
    }

    private static function compact(string $id): string
    {
        $tokens = array_values(array_filter(explode('.', trim($id, '.')), static fn(string $token): bool => $token !== ''));

        if (($tokens[0] ?? null) !== 'strang') {
            return $id;
        }

        array_shift($tokens);

        $head = self::compactHead($tokens);
        $tail = self::compactTail($head['tail'], $head['kind'], $head['side'], $head['role']);
        $parts = array_values(array_filter([
            $head['kind'],
            $head['side'],
            $head['role'],
            $head['index'],
            ...$tail,
        ], static fn(?string $part): bool => filled($part)));

        return implode('.', $parts);
    }

    /**
     * @param  list<string>  $tokens
     * @return array{kind: string, side: string, role: string, index: string, tail: list<string>}
     */
    private static function compactHead(array $tokens): array
    {
        if (($tokens[0] ?? null) === 'trunk') {
            return [
                'kind' => 'trunk',
                'side' => 'center',
                'role' => '',
                'index' => ctype_digit((string) ($tokens[1] ?? '')) ? (string) $tokens[1] : '1',
                'tail' => array_slice($tokens, ctype_digit((string) ($tokens[1] ?? '')) ? 2 : 1),
            ];
        }

        $side = in_array((string) ($tokens[0] ?? ''), ['left', 'right'], true) ? (string) $tokens[0] : '';
        $index = ctype_digit((string) ($tokens[1] ?? '')) ? (string) $tokens[1] : '1';
        $kind = (string) ($tokens[2] ?? 'element');
        $role = '';
        $tailStart = 3;

        if ($kind === 'rekey' && in_array((string) ($tokens[3] ?? ''), ['source', 'target'], true)) {
            $role = (string) $tokens[3];
            $tailStart = 4;
        }

        return [
            'kind' => $kind,
            'side' => $side,
            'role' => $role,
            'index' => $index,
            'tail' => array_slice($tokens, $tailStart),
        ];
    }

    /**
     * @param  list<string>  $tokens
     * @return list<string>
     */
    private static function compactTail(array $tokens, string $kind, string $side, string $rekeyRole): array
    {
        $result = [];

        for ($index = 0; $index < count($tokens); $index++) {
            $token = $tokens[$index];

            if (in_array($token, ['main', 'path', 'paths', 'segment', 'bounds', 'text'], true)) {
                continue;
            }

            if (in_array($token, ['before', 'after'], true)) {
                continue;
            }

            if ($token === $kind || str_replace('.', '-', $kind) === $token) {
                continue;
            }

            if (in_array($token, ['extension', 'return'], true)) {
                $chapterNumber = ctype_digit((string) ($tokens[$index + 1] ?? '')) ? (string) $tokens[$index + 1] : '1';
                $result[] = $token . '-' . $chapterNumber;

                if (ctype_digit((string) ($tokens[$index + 1] ?? ''))) {
                    $index++;
                }

                continue;
            }

            if ($token === 'step') {
                $stepNumber = ctype_digit((string) ($tokens[$index + 1] ?? '')) ? (string) $tokens[$index + 1] : '1';
                $result[] = 'step-' . $stepNumber;

                if (ctype_digit((string) ($tokens[$index + 1] ?? ''))) {
                    $index++;
                }

                continue;
            }

            if ($token === 'stem' && str_starts_with((string) end($result), 'step-')) {
                continue;
            }

            if ($token === 'arc') {
                $arcRole = (string) ($tokens[$index + 1] ?? '');

                if (in_array($arcRole, ['in', 'out'], true)) {
                    $result[] = 'arc-' . self::compactArcDirection($kind, $side, $arcRole, $rekeyRole) . '-' . ($arcRole === 'out' ? '2' : '1');
                    $index++;

                    continue;
                }

                $result[] = 'arc-1';

                continue;
            }

            if ($token === 'label') {
                $anchor = (string) ($tokens[$index + 1] ?? '');
                $labelNumber = (string) ($tokens[$index + 2] ?? '');

                if (in_array($anchor, ['start', 'end'], true) && ctype_digit($labelNumber)) {
                    $result[] = 'anchorNode-' . $anchor;
                    $result[] = 'label-' . $labelNumber;
                    $index += 2;

                    continue;
                }

                $result[] = 'label';

                continue;
            }

            if (preg_match('/^arc(\d+)-(.+)$/', $token, $matches) === 1) {
                $result[] = 'arc-' . $matches[2] . '-' . $matches[1];

                continue;
            }

            if (preg_match('/^(bridge|stem)(\d+)$/', $token, $matches) === 1) {
                $result[] = $matches[1] . '-' . $matches[2];

                continue;
            }

            if ($token === 'node') {
                $next = (string) ($tokens[$index + 1] ?? '');

                if (in_array($next, ['start', 'end'], true)) {
                    $result[] = 'anchorNode-' . $next;
                    $index++;

                    continue;
                }

                $result[] = 'anchorNode';

                continue;
            }

            if (preg_match('/^node(?:Start|End)Label(\d+)$/', $token, $matches) === 1) {
                $result[] = 'label-' . $matches[1];

                continue;
            }

            if (ctype_digit($token)) {
                continue;
            }

            $result[] = $token;
        }

        return $result;
    }

    private static function compactArcDirection(string $kind, string $side, string $arcRole, string $rekeyRole): string
    {
        if ($kind === 'branch' || ($kind === 'rekey' && $rekeyRole === 'target')) {
            return $arcRole === 'in'
                ? ($side === 'left' ? 'east-north' : 'west-north')
                : ($side === 'left' ? 'south-west' : 'south-east');
        }

        return $arcRole === 'out'
            ? ($side === 'left' ? 'south-east' : 'south-west')
            : ($side === 'left' ? 'west-north' : 'east-north');
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
