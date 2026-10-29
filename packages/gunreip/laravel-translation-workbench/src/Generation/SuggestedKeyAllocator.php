<?php

namespace Gunreip\TranslationWorkbench\Generation;

use PDO;
use RuntimeException;

/** Reserve names across all finding lineages, including archived roots. */
final class SuggestedKeyAllocator
{
    private array $used;

    public function __construct(PDO $db)
    {
        $this->used = array_fill_keys($db->query('SELECT DISTINCT suggested_key FROM translation_workbench_findings WHERE suggested_key IS NOT NULL')->fetchAll(PDO::FETCH_COLUMN), true);
    }

    public function allocate(string $base): string
    {
        if (trim($base) === '') {
            throw new RuntimeException('A new finding requires a suggested key.');
        }
        $candidate = $base;
        for ($ordinal = 2; isset($this->used[$candidate]); $ordinal++) {
            $candidate = $base.'_'.$ordinal;
        }
        $this->used[$candidate] = true;
        return $candidate;
    }
}
