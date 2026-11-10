<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Illuminate\Cache\DatabaseStore;
use Illuminate\Support\Facades\Cache;

final class ScanCacheMaintenance
{
    private function entries(): ?\Illuminate\Database\Query\Builder
    {
        $store = Cache::store()->getStore();
        if (!$store instanceof DatabaseStore) return null;
        $prefix = $store->getPrefix().'workbench-ui-scan:';
        $table = config('cache.stores.'.config('cache.default').'.table', 'cache');
        return $store->getConnection()->table($table)
            ->whereRaw('substr("key", 1, ?) = ?', [strlen($prefix), $prefix]);
    }

    public function summary(): array
    {
        $query = $this->entries();
        if ($query === null) return ['supported' => false];
        $now = time();
        return ['supported' => true,
            'entries' => (clone $query)->count(),
            'expired' => (clone $query)->where('expiration', '<=', $now)->count(),
            'bytes' => (int) (clone $query)->selectRaw('COALESCE(SUM(length(value)),0) AS bytes')->value('bytes'),
        ];
    }

    public function prune(): int
    {
        return $this->entries()?->where('expiration', '<=', time())->delete() ?? 0;
    }
}
