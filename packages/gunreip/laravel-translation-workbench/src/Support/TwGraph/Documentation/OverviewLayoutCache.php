<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\Documentation;

use Closure;
use Illuminate\Support\Facades\Cache;
use Throwable;

/** Caches prepared arrays only; HTML, anchors and geometry are always rendered anew. */
final class OverviewLayoutCache
{
    public static function remember(array $files, array $inputs, Closure $build): array
    {
        $fingerprint = self::fingerprint($files, $inputs);
        if ($fingerprint === null) {
            return $build();
        }
        $key = 'tw-graph:overview:layout:'.$fingerprint;
        try {
            $cached = Cache::get($key);
            if (is_array($cached)) {
                return $cached;
            }
        } catch (Throwable $error) {
            report($error);

            return $build();
        }
        $result = $build();
        if (($result['issues'] ?? []) === []) {
            try {
                // Old fingerprints expire; a normal project cache clear also clears these entries.
                Cache::put($key, $result, now()->addHour());
            } catch (Throwable $error) {
                report($error);
            }
        }

        return $result;
    }

    public static function fingerprint(array $files, array $inputs): ?string
    {
        $hashes = [];
        foreach ($files as $file) {
            if (! is_file($file) || ! is_readable($file)) {
                return null;
            }
            $hash = hash_file('sha256', $file);
            if ($hash === false) {
                return null;
            }
            $hashes[$file] = $hash;
        }
        ksort($hashes);

        return hash('sha256', serialize([$hashes, $inputs]));
    }
}
