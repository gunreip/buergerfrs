<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Gunreip\TranslationWorkbench\Support\TwGraph\ElementIdentifier;
use Illuminate\Support\Collection;

final class LayoutCorrectionConfig
{
    /**
     * Read deliberate data-driven graph-family layout corrections from package config.
     *
     * Corrections are deltas that adjust the currently resolved value. This keeps
     * central defaults, data-driven defaults and calculated compensation as the
     * source of truth; this config only records deliberate final nudges.
     *
     * @return array<int, array{target: string, prop: string, delta: mixed, value: mixed, reason: string}>
     */
    public static function forDataDriven(): array
    {
        $config = (array) config('tw-graph-data-driven-layout-corrections', []);

        return collect([
            ...self::normalizeFlatCorrections((array) data_get($config, 'corrections', [])),
            ...self::normalizeTrunkCorrections(self::trunkConfig($config)),
        ])
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private static function trunkConfig(array $config): array
    {
        $dotted = $config['strang.trunk'] ?? null;

        if (is_array($dotted)) {
            return $dotted;
        }

        $nested = data_get($config, 'strang.trunk');

        return is_array($nested) ? $nested : [];
    }

    /**
     * Kept as a compatibility wrapper. The correction source is intentionally
     * graph-family based and must not branch by timeline-chain graph id.
     *
     * @return array<int, array{target: string, prop: string, delta: mixed, value: mixed, reason: string}>
     */
    public static function forGraph(string $graphId): array
    {
        return self::forDataDriven();
    }

    /**
     * @param  array<int|string, mixed>  $pathLengths
     * @param  array<int, array{target: string, prop: string, delta: mixed, value: mixed, reason: string}>  $corrections
     * @return array{path_lengths: array<int|string, mixed>, applied: array<int, array<string, mixed>>}
     */
    public static function applyToTrunkPathLengths(array $pathLengths, array $corrections, string $defaultLength): array
    {
        $applied = [];

        foreach ($corrections as $correction) {
            if (! self::targetsTrunkPathLength($correction)) {
                continue;
            }

            $pathNumber = self::trunkPathNumber((string) $correction['target']);

            if ($pathNumber === null) {
                continue;
            }

            $currentEntry = $pathLengths[$pathNumber] ?? null;
            $currentLength = self::entryLength($currentEntry, $defaultLength);
            $effectiveLength = self::correctedLength($currentLength, $correction, $defaultLength);

            if ($effectiveLength === null) {
                continue;
            }

            $pathLengths[$pathNumber] = self::entryWithLength($currentEntry, $effectiveLength);
            $applied[] = self::appliedCorrection($correction, $currentLength, $effectiveLength);
        }

        return [
            'path_lengths' => $pathLengths,
            'applied' => $applied,
        ];
    }

    /**
     * @param  array<int, array{target: string, prop: string, delta: mixed, value: mixed, reason: string}>  $corrections
     */
    public static function maxTrunkPathNumber(array $corrections): int
    {
        return collect($corrections)
            ->map(static fn(array $correction): ?int => self::trunkPathNumber((string) $correction['target']))
            ->filter()
            ->max() ?? 0;
    }

    /**
     * @param  array<int, array<mixed>>  $entries
     * @return array<int, array{target: string, prop: string, delta: mixed, value: mixed, reason: string}>
     */
    private static function normalizeFlatCorrections(array $entries): array
    {
        return collect($entries)
            ->filter(static fn(mixed $entry): bool => is_array($entry))
            ->map(static fn(array $entry): array => [
                'target' => ElementIdentifier::normalize(data_get($entry, 'target', '')),
                'prop' => self::previewPropName((string) data_get($entry, 'prop', '')),
                'delta' => data_get($entry, 'delta'),
                'value' => data_get($entry, 'value'),
                'reason' => trim((string) data_get($entry, 'reason', '')),
            ])
            ->filter(static fn(array $entry): bool => $entry['target'] !== ''
                && $entry['prop'] !== ''
                && ($entry['delta'] !== null || $entry['value'] !== null))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $trunkConfig
     * @return array<int, array{target: string, prop: string, delta: mixed, value: mixed, reason: string}>
     */
    private static function normalizeTrunkCorrections(array $trunkConfig): array
    {
        $corrections = [];

        foreach (['stem', 'path'] as $key) {
            foreach ((array) data_get($trunkConfig, $key, []) as $pathNumber => $delta) {
                if ($delta === null || $delta === '') {
                    continue;
                }

                $corrections[] = [
                    'target' => ElementIdentifier::normalize('strang.trunk.1.main.path.trunk.path' . (string) $pathNumber),
                    'prop' => 'length',
                    'delta' => $delta,
                    'value' => null,
                    'reason' => 'Configured data-driven trunk path delta.',
                ];
            }
        }

        return $corrections;
    }

    /**
     * @param  array<int, array<string, mixed>>  $previews
     * @param  array<int, array{target: string, prop: string, delta: mixed, value: mixed, reason: string}>  $corrections
     * @return array<int, array<string, mixed>>
     */
    public static function applyToPreviews(array $previews, array $corrections): array
    {
        if ($corrections === []) {
            return $previews;
        }

        return collect($previews)
            ->map(static function (array $preview) use ($corrections): array {
                $previewId = self::previewId($preview);

                if ($previewId === '') {
                    return $preview;
                }

                foreach ($corrections as $correction) {
                    if (! self::targetsPreview((string) $correction['target'], $previewId)) {
                        continue;
                    }

                    $prop = (string) $correction['prop'];
                    $currentValue = data_get($preview, $prop, self::fallbackForPreviewProp($prop));
                    $effectiveValue = self::correctedLength($currentValue, $correction, self::fallbackForPreviewProp($prop));

                    if ($effectiveValue === null) {
                        continue;
                    }

                    data_set($preview, $prop, $effectiveValue);
                    $preview['layout'] = [
                        ...((array) ($preview['layout'] ?? [])),
                        'appliedCorrections' => [
                            ...((array) data_get($preview, 'layout.appliedCorrections', [])),
                            self::appliedCorrection($correction, $currentValue, $effectiveValue),
                        ],
                    ];
                }

                return $preview;
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  ...$previewGroups
     * @return array<int, array<string, mixed>>
     */
    public static function appliedCorrections(array ...$previewGroups): array
    {
        return collect($previewGroups)
            ->flatMap(static fn(array $previewGroup): Collection => collect($previewGroup))
            ->flatMap(static fn(array $preview): array => (array) data_get($preview, 'layout.appliedCorrections', []))
            ->values()
            ->all();
    }

    private static function targetsPreview(string $target, string $previewId): bool
    {
        return ElementIdentifier::startsWith($target, $previewId);
    }

    /**
     * @param  array{target: string, prop: string, delta: mixed, value: mixed, reason: string}  $correction
     */
    private static function targetsTrunkPathLength(array $correction): bool
    {
        if (! in_array($correction['prop'], ['length', 'path_length', 'stem_length'], true)) {
            return false;
        }

        return self::trunkPathNumber((string) $correction['target']) !== null;
    }

    private static function trunkPathNumber(string $target): ?int
    {
        $target = ElementIdentifier::normalize($target);

        if (preg_match('/^strang\.trunk(?:\.1)?\.main\.stem(\d+)$/', $target, $matches) === 1) {
            return max(1, (int) $matches[1]);
        }

        return null;
    }

    private static function entryLength(mixed $entry, string $defaultLength): string
    {
        if (is_array($entry)) {
            return (string) (data_get($entry, 'length', data_get($entry, 0)) ?: $defaultLength);
        }

        return filled($entry) ? (string) $entry : $defaultLength;
    }

    private static function entryWithLength(mixed $entry, string $length): mixed
    {
        if (! is_array($entry)) {
            return $length;
        }

        if (array_key_exists('length', $entry) || ! array_key_exists(0, $entry)) {
            $entry['length'] = $length;

            return $entry;
        }

        $entry[0] = $length;

        return $entry;
    }

    /**
     * @param  array{target: string, prop: string, delta: mixed, value: mixed, reason: string}  $correction
     */
    private static function correctedLength(mixed $currentValue, array $correction, string $fallback): ?string
    {
        if ($correction['value'] !== null) {
            return (string) $correction['value'];
        }

        if ($correction['delta'] === null || $correction['delta'] === '') {
            return null;
        }

        $current = self::remNumber($currentValue, $fallback);
        $delta = self::remNumber($correction['delta'], '0rem');

        return self::formatRem($current + $delta);
    }

    private static function remNumber(mixed $value, string $fallback): float
    {
        $candidate = filled($value) ? (string) $value : $fallback;

        if (preg_match('/-?\d+(?:\.\d+)?/', $candidate, $matches) !== 1) {
            return 0.0;
        }

        return (float) $matches[0];
    }

    private static function formatRem(float $value): string
    {
        $formatted = rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.');

        return ($formatted === '' ? '0' : $formatted) . 'rem';
    }

    /**
     * @param  array{target: string, prop: string, delta: mixed, value: mixed, reason: string}  $correction
     * @return array<string, mixed>
     */
    private static function appliedCorrection(array $correction, mixed $baseValue, mixed $effectiveValue): array
    {
        return [
            'target' => $correction['target'],
            'prop' => $correction['prop'],
            'delta' => $correction['delta'],
            'value' => $correction['value'],
            'baseValue' => $baseValue,
            'effectiveValue' => $effectiveValue,
            'reason' => $correction['reason'],
        ];
    }

    private static function fallbackForPreviewProp(string $prop): string
    {
        return match ($prop) {
            'bridge_length',
            'extension_bridge_length',
            'extension_bridge_continuations' => \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::dataDrivenString(
                'bridge_length',
                \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::dataDrivenString('line_length', '4rem'),
            ),
            'stem_length',
            'entry_stem_length',
            'end_length',
            'extension_stem_length',
            'extension_stem_lengths',
            'extension_stem_continuations',
            'stem_continuation' => \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::dataDrivenString(
                'stem_length',
                \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::dataDrivenString('line_length', '4rem'),
            ),
            default => \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::dataDrivenString('line_length', '4rem'),
        };
    }

    /**
     * @param  array<string, mixed>  $preview
     */
    private static function previewId(array $preview): string
    {
        $id = trim((string) ($preview['id'] ?? ''));

        if ($id !== '') {
            return ElementIdentifier::normalize($id);
        }

        $component = trim((string) ($preview['component'] ?? ''));
        $side = trim((string) ($preview['side'] ?? ''));
        $counter = (int) ($preview['component_counter'] ?? 1);

        if ($component === '' || $side === '') {
            return '';
        }

        return match (true) {
            str_contains($component, 'merge') => ElementIdentifier::normalize('strang.merge-' . $side . '.' . $counter),
            str_contains($component, 'branch') => ElementIdentifier::normalize('strang.branch-' . $side . '.' . $counter),
            str_contains($component, 'rekey-source') => ElementIdentifier::normalize('strang.rekey-source-' . $side . '.' . $counter),
            str_contains($component, 'rekey-target') => ElementIdentifier::normalize('strang.rekey-target-' . $side . '.' . $counter),
            default => '',
        };
    }

    private static function previewPropName(string $prop): string
    {
        $prop = trim($prop);

        return match ($prop) {
            'bridgeLength', 'bridge-length' => 'bridge_length',
            'stemLength', 'stem-length' => 'stem_length',
            'entryStemLength', 'entry-stem-length' => 'entry_stem_length',
            'endLength', 'end-length' => 'end_length',
            'pathLength', 'path-length' => 'path_length',
            default => $prop,
        };
    }
}
