<?php

// app/Support/Documents/PersonDocumentPath.php

namespace App\Support\Documents;

use Illuminate\Support\Facades\File;
use InvalidArgumentException;

class PersonDocumentPath
{
    public static function relativePath(string $uuid, string $extension): string
    {
        $uuid = self::normalizeUuid($uuid);
        $extension = self::normalizeExtension($extension);

        return self::relativeDirectory($uuid).'/'.$uuid.'.'.$extension;
    }

    public static function relativeDirectory(string $uuid): string
    {
        $uuid = self::normalizeUuid($uuid);
        $prefix = str_replace('-', '', $uuid);

        return 'person-documents/'.substr($prefix, 0, 2).'/'.substr($prefix, 2, 2).'/'.substr($prefix, 4, 2);
    }

    public static function ensureLocalDiskDirectoryExists(string $uuid): void
    {
        $directory = storage_path('app/private/'.self::relativeDirectory($uuid));

        if (! File::exists($directory)) {
            File::ensureDirectoryExists($directory);
        }
    }

    private static function normalizeUuid(string $uuid): string
    {
        $uuid = strtolower(trim($uuid));

        if ($uuid === '') {
            throw new InvalidArgumentException('Person document UUID must not be empty.');
        }

        if (! preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $uuid)) {
            throw new InvalidArgumentException('Person document UUID is not valid.');
        }

        return $uuid;
    }

    private static function normalizeExtension(string $extension): string
    {
        $extension = strtolower(trim($extension));
        $extension = ltrim($extension, '.');

        if ($extension === '') {
            throw new InvalidArgumentException('Person document extension must not be empty.');
        }

        return $extension;
    }
}
