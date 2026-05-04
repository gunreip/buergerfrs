<?php

// app/Support/Avatar/AvatarPath.php

namespace App\Support\Avatar;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class AvatarPath
{
    public static function relativePath(string $uuid, string $ext): string
    {
        $uuid = self::normalizeUuid($uuid);
        $ext = self::normalizeExtension($ext);

        return self::relativeDirectory($uuid) . '/' . $uuid . '.' . $ext;
    }

    public static function relativeDirectory(string $uuid): string
    {
        $uuid = self::normalizeUuid($uuid);

        $prefix = str_replace('-', '', $uuid);

        return 'avatars/' . substr($prefix, 0, 2) . '/' . substr($prefix, 2, 2) . '/' . substr($prefix, 4, 2);
    }

    public static function publicUrl(string $uuid, string $ext): string
    {
        return Storage::disk('public')->url(self::relativePath($uuid, $ext));
    }

    public static function publicDiskFilePath(string $uuid, string $ext): string
    {
        return storage_path('app/public/' . self::relativePath($uuid, $ext));
    }

    public static function ensurePublicDiskDirectoryExists(string $uuid): void
    {
        $directory = storage_path('app/public/' . self::relativeDirectory($uuid));

        if (!File::exists($directory)) {
            File::ensureDirectoryExists($directory);
        }
    }

    private static function normalizeUuid(string $uuid): string
    {
        $uuid = strtolower(trim($uuid));

        if ($uuid === '') {
            throw new InvalidArgumentException('Avatar UUID must not be empty.');
        }

        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $uuid)) {
            throw new InvalidArgumentException('Avatar UUID is not valid.');
        }

        return $uuid;
    }

    private static function normalizeExtension(string $ext): string
    {
        $ext = strtolower(trim($ext));
        $ext = ltrim($ext, '.');

        if ($ext === '') {
            throw new InvalidArgumentException('Avatar extension must not be empty.');
        }

        return $ext;
    }
}
