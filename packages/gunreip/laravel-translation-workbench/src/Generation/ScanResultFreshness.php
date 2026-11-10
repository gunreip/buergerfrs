<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;

class ScanResultFreshness
{
    public static function configuration(): string
    {
        $config = (array) config('translation-workbench');
        unset($config['analysis_cache'], $config['scan_result_max_age_minutes']);
        return hash('sha256', json_encode($config, JSON_THROW_ON_ERROR));
    }

    public function inspect(): array
    {
        $db = app(GenerationDatabase::class)->connection();
        $row = $db->query("SELECT s.id,o.completed_at,s.scope_snapshot FROM translation_workbench_scan_runs s
            JOIN translation_workbench_operations o ON o.id=s.id
            WHERE s.status='completed' AND o.kind='complete_rescan' AND o.completed_at IS NOT NULL
            ORDER BY o.completed_at DESC,s.id DESC LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return ['reusable' => false, 'reason' => __('No completed scan is available.')];
        $snapshot = json_decode($row['scope_snapshot'], true, flags: JSON_THROW_ON_ERROR);
        $expires = strtotime($row['completed_at']) + max(1, (int) config('translation-workbench.scan_result_max_age_minutes', 120)) * 60;
        $base = ['run' => $row['id'], 'expires' => $expires];
        if (time() >= $expires) return $base + ['reusable' => false, 'reason' => __('The last scan has reached the configured age limit.')];
        if (($snapshot['configuration'] ?? null) !== self::configuration()) return $base + ['reusable' => false, 'reason' => __('The scan configuration changed or was not recorded by the last scan.')];
        $inventory = $snapshot['inventory'] ?? [];
        $known = array_unique([...array_keys($inventory['hashes'] ?? []), ...($inventory['missing'] ?? [])]);
        $current = app(TranslationWorkbenchScanner::class)->scanInventory(null, $known);
        return $base + ['reusable' => self::matches($inventory, $current), 'reason' => self::matches($inventory, $current)
            ? __('The source files and scan configuration are unchanged.')
            : __('Source files were added, changed or removed, or could not be checked.')];
    }

    public static function matches(array $previous, array $current): bool
    {
        foreach (['hashes' => 'file_hashes', 'directories' => 'scanned_directories', 'missing' => 'missing_paths'] as $old => $new) {
            if (!isset($previous[$old], $current[$new])) return false;
            $a = $previous[$old];
            $b = $current[$new];
            if ($old === 'hashes') {
                ksort($a);
                ksort($b);
            } else {
                sort($a);
                sort($b);
            }
            if ($a !== $b) return false;
        }
        return true;
    }
}
