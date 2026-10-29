<?php

namespace Tests\Support\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Support\BaselineNameInventory;
use PDO;
use RuntimeException;

/** Published file evidence in the complete fresh generation; never imports approvals. */
final class FreshGenerationPublishedValues
{
    public function verify(PDO $db, string $backup, array $manifest, string $manifestHash, callable $check, callable $reject): array
    {
        $fingerprint = static function () use ($db): array {
            $tables = $db->query('SELECT tablename FROM pg_tables WHERE schemaname=current_schema() ORDER BY tablename')->fetchAll(PDO::FETCH_COLUMN);
            $result = [];
            foreach ($tables as $table) {
                if (in_array($table, ['published_value_baselines', 'published_value_sources', 'published_value_evidence', 'baseline_name_inventories'], true)) {
                    continue;
                }
                $result[$table] = $db->query('SELECT md5(coalesce(string_agg(row_to_json(t)::text,chr(10) ORDER BY row_to_json(t)::text),\'\')) FROM "'.str_replace('"', '""', $table).'" t')->fetchColumn();
            }

            return $result;
        };
        $before = $fingerprint();
        $service = new PublishedValueBaseline;
        $evidence = $service->read($backup.'/source', $manifest['source_files']);
        $bad = $evidence;
        $bad['values'][count($bad['values']) - 1]['locale'] = 'invented';
        $reject(fn () => $service->import($db, $bad, $manifestHash), 'Published locale mismatch accepted');
        foreach (['published_value_baselines', 'published_value_sources', 'published_value_evidence'] as $table) {
            $check((int) $db->query("SELECT count(*) FROM $table")->fetchColumn() === 0, 'Failed value import left partial data');
        }
        $totals = $service->import($db, $evidence, $manifestHash);
        $check(! $totals['replayed'] && $service->import($db, $evidence, $manifestHash)['replayed'], 'Published baseline replay failed');
        $check((int) $db->query('SELECT count(*) FROM published_value_baselines')->fetchColumn() === 1
            && (int) $db->query('SELECT count(*) FROM published_value_evidence')->fetchColumn() === count($evidence['values']),
            'Published evidence lost or duplicated');
        $hash = $db->query('SELECT hash FROM published_value_baselines')->fetchColumn();
        $reader = new BaselineTranslationReader($db);
        $expected = [];
        $locales = [];
        $db->beginTransaction();
        try {
            $db->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ, READ ONLY');
            foreach ($evidence['values'] as $row) {
                $state = $reader->readInSnapshot($hash, $row['key'], [$row['locale']]);
                $value = $state['locales'][$row['locale']];
                if ($value['editing'] !== null || $value['published']['value'] !== $row['value']
                    || $value['published']['path'] !== $row['path']
                    || $value['published']['sha256'] !== $evidence['sources'][$row['path']]['sha256']
                    || $state['effective_published']['fallback'] !== false) {
                    throw new RuntimeException('Exact published readback differs: '.$row['key'].' / '.$row['locale']);
                }
                $expected[$row['key']][$row['locale']] = $row['value'];
                $locales[$row['locale']] = ($locales[$row['locale']] ?? 0) + 1;
            }
            $fallbacks = 0;
            foreach ($expected as $key => $values) {
                $state = $reader->readInSnapshot($hash, $key, ['de-at', 'de', 'en']);
                $chosen = null;
                foreach (['de-at', 'de', 'en'] as $locale) {
                    if (! array_key_exists($locale, $values)) {
                        if ($state['locales'][$locale]['published'] !== null) {
                            throw new RuntimeException('Missing locale evidence was fabricated');
                        }
                    } elseif ($chosen === null) {
                        $chosen = $locale;
                    }
                }
                $effective = $state['effective_published'];
                if (($chosen === null && $effective !== null) || ($chosen !== null
                    && ($effective['value'] !== $values[$chosen] || $effective['locale'] !== $chosen
                        || $effective['fallback'] !== ($chosen !== 'de-at')))) {
                    throw new RuntimeException('Explicit fallback chain differs: '.$key);
                }
                $fallbacks += (int) ($effective['fallback'] ?? false);
            }
            $db->commit();
        } catch (\Throwable $error) {
            $db->rollBack();
            throw $error;
        }
        $check(array_sum($locales) === count($evidence['values']), 'Readback missed published values');
        $check($fallbacks > 0, 'No real missing-locale fallback exercised');
        $check((int) $db->query("SELECT count(*) FROM published_value_evidence v JOIN translation_workbench_key_names n ON n.key_id=v.key_id AND n.valid_to IS NULL WHERE v.full_name<>n.full_name OR n.namespace<>''")->fetchColumn() === 0,
            'Published evidence linked to another key');
        $check((int) $db->query("SELECT count(*) FROM published_value_evidence WHERE state<>'linked' AND key_id IS NOT NULL")->fetchColumn() === 0,
            'Blocked or unassigned value became assigned');
        $schema = $db->query('SELECT current_schema()')->fetchColumn();
        $names = new BaselineNameInventory;
        $db->beginTransaction();
        try {
            $inventory = $names->capture($db, $schema, $hash);
            $names->store($db, $schema, $hash, $hash, $inventory);
            $check($names->read($db, $schema, $hash) === $inventory, 'Full generation name inventory differs');
            $db->commit();
        } catch (\Throwable $error) {
            $db->rollBack();
            throw $error;
        }
        foreach (['published_value_baselines', 'published_value_sources', 'published_value_evidence', 'baseline_name_inventories'] as $table) {
            $reject(fn () => $db->exec("DELETE FROM $table"), 'Immutable published evidence could be deleted');
        }
        $check($fingerprint() === $before, 'Published baseline changed findings, assignments, decisions or history');

        return ['files' => count($evidence['sources']), 'excluded_files' => count($evidence['excluded']),
            'values' => count($evidence['values']), 'locales' => $locales, 'totals' => $totals,
            'fallback_keys' => $fallbacks, 'baseline_hash' => $hash, 'reserved_names' => $inventory['count']];
    }
}
