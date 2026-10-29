<?php

namespace Tests\Support\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Generation\RevisionReviewContext;
use PDO;
use RuntimeException;

/** Rehearsal read model. Editing/approval never silently replaces published file evidence. */
final class BaselineTranslationReader
{
    public function __construct(private PDO $db) {}

    public function read(string $baselineHash, string $key, array $localeChain): array
    {
        if ($this->db->inTransaction()) {
            throw new RuntimeException('Reader requires its own snapshot transaction');
        }
        $this->db->beginTransaction();
        try {
            $this->db->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ, READ ONLY');
            $result = $this->readInSnapshot($baselineHash, $key, $localeChain);
            $this->db->commit();

            return $result;
        } catch (\Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    /** Shared read path for callers that already own a coherent read-only snapshot. */
    public function readInSnapshot(string $baselineHash, string $key, array $localeChain): array
    {
        if (! $this->db->inTransaction()) {
            throw new RuntimeException('Active snapshot transaction required');
        }
        if ($localeChain === [] || count(array_unique($localeChain)) !== count($localeChain)) {
            throw new RuntimeException('Explicit non-empty unique locale chain required');
        }
        foreach ($localeChain as $locale) {
            if (! is_string($locale) || ! preg_match('/^[a-zA-Z0-9_-]+$/D', $locale)) {
                throw new RuntimeException('Invalid locale');
            }
        }

        if (! $this->query('SELECT 1 FROM published_value_baselines WHERE hash=?', [$baselineHash])->fetchColumn()) {
            throw new RuntimeException('Unknown published baseline');
        }
        $currentName = $this->query("SELECT id,key_id FROM translation_workbench_key_names WHERE namespace='' AND full_name=? AND valid_to IS NULL", [$key])->fetch(PDO::FETCH_ASSOC);
        $locales = [];
        $effective = null;
        foreach ($localeChain as $position => $locale) {
            $published = $this->query('SELECT v.value,v.state,v.key_id,s.path,s.sha256 FROM published_value_evidence v JOIN published_value_sources s ON s.baseline_hash=v.baseline_hash AND s.path=v.path WHERE v.baseline_hash=? AND v.locale=? AND v.full_name=?', [$baselineHash, $locale, $key])->fetch(PDO::FETCH_ASSOC) ?: null;
            $editing = null;
            if ($currentName) {
                $revision = $this->query("SELECT current_revision_id FROM translation_workbench_value_slots WHERE key_id=? AND locale=? AND kind='scalar'", [$currentName['key_id'], $locale])->fetchColumn();
                if ($revision !== false && $revision !== null) {
                    $snapshot = RevisionReviewContext::snapshot($this->db, (int) $revision);
                    $review = $this->query('SELECT id,decision,context_fingerprint FROM translation_workbench_reviews WHERE id=?', [$snapshot['latest_decision']])->fetch(PDO::FETCH_ASSOC) ?: null;
                    $current = $snapshot['context']['current_revision_id'] === (int) $revision
                        && $snapshot['context']['usages'] !== [] && $review !== null
                        && $review['context_fingerprint'] === $snapshot['context_fingerprint'];
                    $editing = ['revision_id' => (int) $revision, 'value' => $snapshot['context']['value'],
                        'review_id' => $review['id'] ?? null, 'recorded_decision' => $review['decision'] ?? null,
                        'approval' => $review === null ? 'unreviewed' : ($current ? $review['decision'] : 'stale')];
                }
            }
            $locales[$locale] = ['published' => $published, 'editing' => $editing];
            // Missing is distinct from an explicitly published empty string or "0".
            if ($effective === null && $published !== null) {
                $effective = ['value' => $published['value'], 'locale' => $locale, 'fallback' => $position > 0,
                    'source' => 'published_file_baseline', 'path' => $published['path'], 'sha256' => $published['sha256']];
            }
        }

        return ['key' => $key, 'requested_locale' => $localeChain[0], 'locale_chain' => $localeChain,
            'key_id' => $currentName ? (int) $currentName['key_id'] : null,
            'effective_published' => $effective, 'locales' => $locales];
    }

    private function query(string $sql, array $params): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement;
    }
}
