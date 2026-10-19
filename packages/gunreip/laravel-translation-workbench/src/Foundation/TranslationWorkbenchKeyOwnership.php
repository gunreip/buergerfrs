<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/** Exact key identity; obsolete history is not a competing active owner. */
class TranslationWorkbenchKeyOwnership
{
    public static function activeKeys(): Builder
    {
        return DB::table('translation_workbench_keys as ownership_keys')->where('ownership_keys.status', '<>', 'obsolete');
    }

    public static function counts(): Builder
    {
        return self::activeKeys()->whereNotNull('ownership_keys.translation_key')
            ->select('ownership_keys.translation_key')->selectRaw('COUNT(*) as owner_count')
            ->groupBy('ownership_keys.translation_key');
    }

    public static function duplicateKeys(): Builder
    {
        return self::activeKeys()->whereNotNull('ownership_keys.translation_key')
            ->select('ownership_keys.translation_key')->groupBy('ownership_keys.translation_key')
            ->havingRaw('COUNT(*) > 1');
    }

    public static function ids(string $translationKey): array
    {
        return self::activeKeys()->where('ownership_keys.translation_key', $translationKey)
            ->orderBy('ownership_keys.id')->pluck('ownership_keys.id')->map(fn ($id) => (int) $id)->all();
    }
}
