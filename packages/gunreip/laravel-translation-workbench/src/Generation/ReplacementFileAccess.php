<?php

namespace Gunreip\TranslationWorkbench\Generation;

use RuntimeException;

/** Files inherit the shared-write policy of their containing directory. */
final class ReplacementFileAccess
{
    public static function temporary(string $target, string $prefix): string
    {
        $directory = dirname($target);
        clearstatcache(true, $directory);
        if (! is_dir($directory) || ! is_writable($directory)) {
            throw new RuntimeException('Cannot replace file: the PHP process needs write access to the containing directory: '.$directory);
        }
        $temp = @tempnam($directory, $prefix);
        if ($temp === false) {
            throw new RuntimeException('Cannot create a temporary replacement file in: '.$directory);
        }
        if (realpath(dirname($temp)) !== realpath($directory)) {
            @unlink($temp);
            throw new RuntimeException('Temporary file was created outside the source directory. Check directory permissions: '.$directory);
        }

        return $temp;
    }

    public static function permissions(string $temp, string $target): void
    {
        clearstatcache(true, $target);
        $mode = is_file($target) ? (fileperms($target) & 0777) : 0644;
        // tempnam starts with 0600. Restore the directory's shared read/write mask,
        // including inherited named-user ACLs, without adding executable permissions.
        $mode |= fileperms(dirname($target)) & 0060;
        if (! @chmod($temp, $mode)) {
            throw new RuntimeException('Cannot set permissions on the temporary replacement file for: '.$target);
        }
    }
}
