<?php

namespace Tests\Support\TranslationWorkbench;

use PDO;
use RuntimeException;

/** Fresh-generation rehearsal only. Records observed links, not value approvals. */
final class ExistingKeyImporter
{
    public function import(PDO $db, array $plan, string $manifest): array
    {
        $payload = json_encode($plan, JSON_THROW_ON_ERROR);
        $hash = hash('sha256', $manifest.$payload);
        $q = static function ($sql, $params = []) use ($db) {
            $s = $db->prepare($sql);
            $s->execute($params);

            return $s;
        };
        $db->beginTransaction();
        try {
            $db->exec('LOCK TABLE existing_key_imports,translation_workbench_keys,translation_workbench_key_names,translation_workbench_key_findings IN EXCLUSIVE MODE');
            if ($q('SELECT 1 FROM existing_key_imports WHERE hash=?', [$hash])->fetchColumn()) {
                $db->rollBack();

                return ['replayed' => true];
            }
            if ((int) $q('SELECT count(*) FROM translation_workbench_keys')->fetchColumn() !== 0) {
                throw new RuntimeException('Expected empty key generation');
            }
            $q('INSERT INTO existing_key_imports VALUES (?,?,?::jsonb)', [$hash, $manifest, $payload]);
            $keys = [];
            $links = 0;
            foreach ($plan['items'] as $item) {
                $f = $q('SELECT f.id,f.raw_source_code_literal,f.suggested_literal,f.raw_literal,f.suggested_key FROM capture_outcomes c JOIN translation_workbench_findings f ON f.id=c.finding_id WHERE c.manifest_sha256=? AND c.capture_id=? AND c.reason=\'key_with_exact_lang_evidence\' AND f.status=\'active\'', [$manifest, $item['capture_id']])->fetch(PDO::FETCH_ASSOC);
                if (! $f || $f['raw_source_code_literal'] !== $item['key'] || $f['suggested_literal'] !== $item['evidence']['value'] || $f['raw_literal'] !== null || $f['suggested_key'] !== null) {
                    throw new RuntimeException('Finding differs from key evidence');
                }
                if ($item['namespace'] !== '' || ($item['state'] === 'eligible') !== (preg_match('/^[a-zA-Z0-9_-]+(?:\\.[a-zA-Z0-9_-]+){2,}$/D', $item['key']) === 1)) {
                    throw new RuntimeException('Key structure mismatch');
                }
                $keyId = null;
                $linkId = null;
                if ($item['state'] === 'eligible') {
                    if (! isset($keys[$item['key']])) {
                        $keys[$item['key']] = $q('INSERT INTO translation_workbench_keys DEFAULT VALUES RETURNING id')->fetchColumn();
                        $q('INSERT INTO translation_workbench_key_names(key_id,namespace,full_name) VALUES (?,?,?)', [$keys[$item['key']], $item['namespace'], $item['key']]);
                    }
                    $keyId = $keys[$item['key']];
                    $linkId = $q('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?) RETURNING id', [$f['id'], $keyId])->fetchColumn();
                    $links++;
                } elseif ($item['state'] !== 'blocked_structure') {
                    throw new RuntimeException('Unknown plan state');
                }
                $q('INSERT INTO existing_key_observations VALUES (?,?,?,?,?,?,?)', [$hash, $manifest, $item['capture_id'], $f['id'], $item['state'], $keyId, $linkId]);
            }
            if (count($keys) !== $plan['eligible_keys'] || $links !== $plan['eligible_findings']) {
                throw new RuntimeException('Plan counts differ');
            }
            if ((int) $q("SELECT count(*) FROM capture_outcomes WHERE manifest_sha256=? AND reason='key_with_exact_lang_evidence'", [$manifest])->fetchColumn() !== count($plan['items'])) {
                throw new RuntimeException('Missing source-key observations');
            }
            $db->commit();

            return ['replayed' => false, 'keys' => count($keys), 'links' => $links];
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
