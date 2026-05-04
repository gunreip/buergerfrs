<?php

// app/Support/Avatar/UserAvatarStorage.php

namespace App\Support\Avatar;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;

class UserAvatarStorage
{
    private const PROFILE_AVATAR_PATH_KEY = 'profile.avatar_path';

    /**
     * Store a newly uploaded user avatar on the public disk.
     *
     * The old avatar file is deleted when it belongs to the managed avatar
     * directory. The returned path is relative to the public disk.
     */
    public function storeUploadedAvatar(User $user, TemporaryUploadedFile $file): string
    {
        $oldPath = (string) $user->setting(self::PROFILE_AVATAR_PATH_KEY, '');

        $uuid = (string) Str::uuid();
        $extension = $this->extensionFromUpload($file);

        AvatarPath::ensurePublicDiskDirectoryExists($uuid);

        $relativePath = AvatarPath::relativePath($uuid, $extension);

        $written = Storage::disk('public')->put(
            $relativePath,
            $file->get()
        );

        if ($written !== true) {
            throw new RuntimeException("Unable to write avatar file to public disk: {$relativePath}");
        }

        $this->deleteAvatarPath($oldPath);

        return $relativePath;
    }

    /**
     * Delete an avatar file from the public disk when it is inside
     * the managed avatars directory.
     */
    public function deleteAvatarPath(?string $relativePath): void
    {
        $relativePath = trim((string) $relativePath);

        if ($relativePath === '') {
            return;
        }

        if (! Str::startsWith($relativePath, 'avatars/')) {
            return;
        }

        Storage::disk('public')->delete($relativePath);
    }

    /**
     * Build a public URL for a stored avatar path.
     */
    public function url(?string $relativePath): ?string
    {
        $relativePath = trim((string) $relativePath);

        if ($relativePath === '') {
            return null;
        }

        if (! Str::startsWith($relativePath, 'avatars/')) {
            return null;
        }

        if (! Storage::disk('public')->exists($relativePath)) {
            return null;
        }

        return Storage::disk('public')->url($relativePath);
    }

    /**
     * Normalize the uploaded file extension.
     */
    private function extensionFromUpload(TemporaryUploadedFile $file): string
    {
        $extension = strtolower((string) $file->getClientOriginalExtension());

        if ($extension === '') {
            $extension = strtolower((string) $file->extension());
        }

        return match ($extension) {
            'jpg', 'jpeg' => 'jpg',
            'png' => 'png',
            'webp' => 'webp',
            default => 'jpg',
        };
    }
}
