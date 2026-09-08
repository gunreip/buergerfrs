<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph\Documentation;

use Illuminate\Support\Str;

class DocumentationViewResolver
{
    private const PACKAGE_VIEW_ROOT = 'packages/gunreip/laravel-translation-workbench/resources/views';

    /**
     * Resolve the highest ordinal Blade view in a documentation section.
     *
     * Special wrapper files such as 00-canvas-open/00-canvas-close are ignored
     * deliberately. They are structural helpers, not authoring steps.
     */
    public static function latestOrdinalView(string $directoryView): ?string
    {
        $directory = self::directoryFromViewName($directoryView);

        if (! is_dir($directory)) {
            return null;
        }

        $files = glob($directory . DIRECTORY_SEPARATOR . '[0-9][0-9]-*.blade.php') ?: [];
        $files = array_values(array_filter($files, function (string $file): bool {
            $basename = basename($file, '.blade.php');

            return ! in_array($basename, ['00-canvas-open', '00-canvas-close'], true);
        }));

        if ($files === []) {
            return null;
        }

        rsort($files, SORT_NATURAL);

        return $directoryView . '.' . Str::beforeLast(basename($files[0]), '.blade.php');
    }

    /**
     * Resolve the latest authored step across ordered documentation sections.
     *
     * Section order wins first, then the file ordinal inside that section. This
     * lets the master graph follow the newest development step without rendering
     * every earlier section as a separate graph.
     *
     * @param  array<int, string>  $directoryViews
     */
    public static function latestOrdinalViewAcrossSections(array $directoryViews): ?string
    {
        $directoryViews = array_values(array_filter($directoryViews, 'is_string'));

        for ($index = count($directoryViews) - 1; $index >= 0; $index--) {
            $latestView = self::latestOrdinalView($directoryViews[$index]);

            if ($latestView !== null) {
                return $latestView;
            }
        }

        return null;
    }

    private static function directoryFromViewName(string $directoryView): string
    {
        $view = Str::after($directoryView, 'translation-workbench::');
        $path = str_replace('.', DIRECTORY_SEPARATOR, $view);

        return base_path(self::PACKAGE_VIEW_ROOT . DIRECTORY_SEPARATOR . $path);
    }
}
