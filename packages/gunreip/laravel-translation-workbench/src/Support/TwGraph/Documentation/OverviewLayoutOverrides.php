<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\Documentation;

use Throwable;

/** Explicit, tab-local layout overrides, resolved before any graph component renders. */
final class OverviewLayoutOverrides
{
    public static function directory(): string
    {
        return dirname(__DIR__, 4).'/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/overview/data';
    }

    public static function load(): array
    {
        $files = array_values(array_filter(
            glob(self::directory().'/*.php') ?: [],
            static fn (string $file): bool => basename($file) !== 'template.php',
        ));
        sort($files, SORT_STRING);
        $base = OverviewStructure::data();
        $authored = [];
        $issues = [];
        foreach ($files as $file) {
            try {
                // Evaluate every request: translations and other runtime values are inputs too.
                $authored[basename($file)] = (static fn (string $path) => require $path)($file);
            } catch (Throwable $error) {
                $issues[] = ['file' => basename($file), 'path' => '', 'message' => __('Cannot load layout overrides: :message', ['message' => $error->getMessage()])];
            }
        }
        $build = static function () use ($base, $authored, $files, $issues): array {
            $tree = new OverviewOverrideTree($base);
            $overrides = [];
            foreach ($authored as $name => $values) {
                try {
                    $scoped = $tree->compile($name, $values);
                    $overrides[$name] = $scoped['values'];
                    $issues = [...$issues, ...$scoped['issues']];
                } catch (Throwable $error) {
                    $issues[] = ['file' => $name, 'path' => '', 'message' => __('Cannot load layout overrides: :message', ['message' => $error->getMessage()])];
                }
            }
            $result = self::apply($base, $overrides);
            $result['issues'] = [...$issues, ...$result['issues']];
            $result['files'] = $files;

            return $result;
        };
        // Never cache a failed include. Fixing the file must immediately clear its diagnostic.
        if ($issues !== []) {
            return $build();
        }

        return OverviewLayoutCache::remember(
            [...$files, ...(glob(__DIR__.'/Overview*.php') ?: [])],
            ['base' => $base, 'authored' => $authored, 'locale' => app()->getLocale(), 'fallback' => config('app.fallback_locale')],
            $build,
        );
    }

    /** Author-facing files accept only the named section hierarchy. */
    public static function scope(string $file, mixed $values): array
    {
        return (new OverviewOverrideTree(OverviewStructure::data()))->compile($file, $values);
    }

    /**
     * Replace existing leaves or supported inherited layout leaves; integer keys are never reindexed.
     * Conflicting leaves keep the base value, so file order cannot silently decide layout.
     */
    public static function apply(array $base, array $overrides): array
    {
        $issues = [];
        $changes = [];
        $visit = function (mixed $value, mixed $original, array $path, string $file) use (&$visit, &$issues, &$changes, $base): void {
            $displayPath = implode('.', $path);
            if (get_debug_type($value) !== get_debug_type($original)) {
                $issues[] = ['file' => $file, 'path' => $displayPath, 'message' => __('Expected :type.', ['type' => get_debug_type($original)])];

                return;
            }
            if (is_array($value)) {
                $original = array_replace_recursive(OverviewEntryLayout::inheritedKeys($base, $path, $original), $original);
                foreach ($value as $key => $child) {
                    if (! array_key_exists($key, $original)) {
                        $issues[] = ['file' => $file, 'path' => implode('.', [...$path, $key]), 'message' => __('Unknown layout key.')];

                        continue;
                    }
                    $visit($child, $original[$key], [...$path, $key], $file);
                }

                return;
            }

            $token = json_encode($path, JSON_THROW_ON_ERROR);
            if (isset($changes[$token])) {
                $changes[$token]['conflict'] = true;
                $issues[] = ['file' => $file, 'path' => $displayPath, 'message' => __('Already overridden in :file; the base value is retained.', ['file' => $changes[$token]['file']])];

                return;
            }
            $changes[$token] = compact('path', 'value', 'file') + ['conflict' => false];
        };

        foreach ($overrides as $file => $values) {
            $visit($values, $base, [], (string) $file);
        }
        $data = $base;
        foreach ($changes as $change) {
            if ($change['conflict']) {
                continue;
            }
            $target = &$data;
            foreach ($change['path'] as $key) {
                $target = &$target[$key];
            }
            $target = $change['value'];
            unset($target);
        }

        return compact('data', 'issues');
    }
}
