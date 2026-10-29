<?php

namespace Gunreip\TranslationWorkbench\Generation;

use PDO;

/** Evidence-based assignment rules; text equality never assigns a key. */
final class RescanCandidateRules
{
    public function existingKey(PDO $db, array $capture): array
    {
        if ($capture['raw_literal'] !== null) {
            return ['state' => 'literal', 'key_id' => null];
        }
        $key = $capture['found_translation_key'];
        if (! is_string($key) || preg_match('/^[a-zA-Z0-9_-]+(?:\.[a-zA-Z0-9_-]+){2,}$/D', $key) !== 1) {
            return ['state' => 'key_structure_review', 'key_id' => null];
        }
        $proof = $capture['provenance']['suggested_literal'] ?? [];
        if ($capture['reason'] !== 'key_with_exact_lang_evidence' || $capture['raw_source_code_literal'] !== $key
            || ($proof['key'] ?? null) !== $key || ($proof['locale'] ?? null) !== 'en'
            || ! isset($proof['path'], $proof['sha256']) || ! is_file(base_path($proof['path']))
            || hash_file('sha256', base_path($proof['path'])) !== $proof['sha256']) {
            return ['state' => 'key_evidence_review', 'key_id' => null];
        }
        $q = $db->prepare("SELECT n.key_id FROM translation_workbench_key_names n
            JOIN published_value_evidence e ON e.key_id=n.key_id AND e.full_name=n.full_name
            JOIN published_value_sources s ON s.baseline_hash=e.baseline_hash AND s.path=e.path AND s.locale=e.locale
            WHERE n.valid_to IS NULL AND n.namespace='' AND n.full_name=? AND e.locale='en'
                AND e.state='linked' AND e.value=? AND e.path=? AND s.sha256=?");
        $q->execute([$key, $capture['suggested_literal'], $proof['path'], $proof['sha256']]);
        $ids = $q->fetchAll(PDO::FETCH_COLUMN);
        if (count($ids) !== 1) {
            return ['state' => 'key_assignment_review', 'key_id' => null];
        }

        return ['state' => 'existing_key_observed', 'key_id' => (int) $ids[0]];
    }

    /** Derived from active raw literals only; no normalization, fallback or stored stale flags. */
    public function sharedCandidates(PDO $db): array
    {
        $rows = $db->query("SELECT id,raw_literal,suggested_key FROM translation_workbench_findings
            WHERE status='active' AND raw_literal IS NOT NULL AND suggested_key IS NOT NULL ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
        $groups = [];
        foreach ($rows as $row) {
            // Encoded identity keeps whitespace, case and numeric-looking strings distinct.
            $identity = base64_encode($row['raw_literal']);
            $groups[$identity]['literal'] = $row['raw_literal'];
            $groups[$identity]['findings'][] = (int) $row['id'];
            $groups[$identity]['suggested_keys'][$row['suggested_key']] = true;
        }
        $result = [];
        foreach ($groups as $group) {
            if (count($group['findings']) >= 2 && count($group['suggested_keys']) >= 2) {
                $result[] = ['literal' => $group['literal'], 'findings' => $group['findings'], 'suggested_keys' => array_keys($group['suggested_keys'])];
            }
        }

        return $result;
    }
}
