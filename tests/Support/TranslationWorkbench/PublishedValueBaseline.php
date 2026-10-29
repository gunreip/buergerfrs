<?php

namespace Tests\Support\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use PDO;
use RuntimeException;

/** Rehearsal baseline only; a published file value is not a new user revision or approval. */
final class PublishedValueBaseline
{
    public function read(string $sourceRoot, array $files): array
    {
        $reader = new BaselineValueReader;
        $sources = [];
        $excluded = [];
        $values = [];
        foreach ($files as $path => $info) {
            if (! str_starts_with($path, 'lang/')) {
                continue;
            }
            if (! preg_match('~^lang/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)\.php$~D', $path, $match)) {
                $excluded[$path] = ['sha256' => $info['sha256'], 'reason' => 'outside_app_php_locale_group_scope'];

                continue;
            }
            $hash = $info['sha256'];
            $rows = $reader->phpFile($sourceRoot.'/'.$path, $match[1], $match[2], $hash);
            $sources[$path] = ['sha256' => $hash, 'locale' => $match[1], 'group' => $match[2], 'values' => count($rows)];
            foreach ($rows as $row) {
                $identity = json_encode([$row['locale'], $row['key']], JSON_THROW_ON_ERROR);
                if (isset($values[$identity])) {
                    throw new RuntimeException('Duplicate published value identity');
                }
                $values[$identity] = ['locale' => $row['locale'], 'key' => $row['key'], 'value' => $row['value'], 'path' => $path];
            }
        }
        ksort($sources);
        ksort($excluded);
        ksort($values);

        return ['sources' => $sources, 'excluded' => $excluded, 'values' => array_values($values)];
    }

    public function import(PDO $db, array $evidence, string $manifestHash): array
    {
        $payload = json_encode($evidence, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $hash = hash('sha256', $manifestHash.$payload);
        $q = static function ($sql, $params = []) use ($db) {
            $s = $db->prepare($sql);
            $s->execute($params);

            return $s;
        };
        $db->beginTransaction();
        try {
            $db->exec('LOCK TABLE published_value_baselines IN EXCLUSIVE MODE');
            $existing = $q('SELECT totals FROM published_value_baselines WHERE hash=?', [$hash])->fetchColumn();
            if ($existing !== false) {
                $db->rollBack();

                return [...json_decode($existing, true, flags: JSON_THROW_ON_ERROR), 'replayed' => true];
            }
            $keys = $q("SELECT full_name,key_id FROM translation_workbench_key_names WHERE namespace='' AND valid_to IS NULL")->fetchAll(PDO::FETCH_KEY_PAIR);
            $totals = ['linked' => 0, 'blocked_structure' => 0, 'unassigned_key' => 0];
            $counts = [];
            // Ledger inserted after rows using deferred FKs; a failure leaves no partial baseline.
            foreach ($evidence['sources'] as $path => $source) {
                $q('INSERT INTO published_value_sources VALUES (?,?,?,?,?)', [$hash, $path, $source['sha256'], $source['locale'], $source['group']]);
            }
            foreach ($evidence['values'] as $row) {
                $source = $evidence['sources'][$row['path']] ?? null;
                if ($source === null || $row['locale'] !== $source['locale'] || ! str_starts_with($row['key'], $source['group'].'.') || ! is_string($row['value'])) {
                    throw new RuntimeException('Value/source mismatch');
                }
                $state = preg_match('/^[a-zA-Z0-9_-]+(?:\.[a-zA-Z0-9_-]+){2,}$/D', $row['key']) !== 1
                    ? 'blocked_structure' : (isset($keys[$row['key']]) ? 'linked' : 'unassigned_key');
                $q('INSERT INTO published_value_evidence VALUES (?,?,?,?,?,?,?)', [$hash, $row['path'], $row['locale'], $row['key'], $row['value'], $state, $state === 'linked' ? $keys[$row['key']] : null]);
                $totals[$state]++;
                $counts[$row['path']] = ($counts[$row['path']] ?? 0) + 1;
            }
            foreach ($evidence['sources'] as $path => $source) {
                if (($counts[$path] ?? 0) !== $source['values']) {
                    throw new RuntimeException('Incomplete language file');
                }
            }
            $q('INSERT INTO published_value_baselines VALUES (?,?,?::jsonb,?::jsonb)', [$hash, $manifestHash, json_encode($evidence['excluded'], JSON_THROW_ON_ERROR), json_encode($totals, JSON_THROW_ON_ERROR)]);
            $db->commit();

            return [...$totals, 'replayed' => false];
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
