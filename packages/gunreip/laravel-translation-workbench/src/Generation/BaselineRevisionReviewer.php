<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Illuminate\Support\Str;
use PDO;
use RuntimeException;

/** Revision approval bound to the saved value, key name and active source links. */
final class BaselineRevisionReviewer
{
    public function __construct(private PDO $db) {}

    public function decide(string $operation, int $revision, string $expectedFingerprint, bool $approve, int $actor, string $reason, ?string $reasonLocale = null): array
    {
        return (new BaselineOperation($this->db))->run($operation, 'review', [$revision, $expectedFingerprint, $approve, $actor, $reason, $reasonLocale], function () use ($operation, $revision, $expectedFingerprint, $approve, $actor, $reason, $reasonLocale) {
            return $this->applyDecision($operation, $revision, $expectedFingerprint, $approve, $actor, $reason, $reasonLocale);
        });
    }

    public function decideMany(string $operation, array $revisions, bool $approve, int $actor, string $reason, ?string $reasonLocale = null): array
    {
        if ($revisions === []) {
            throw new RuntimeException('Select at least one saved revision.');
        }
        ksort($revisions, SORT_NUMERIC);

        return (new BaselineOperation($this->db))->run($operation, 'review_multiple', [$revisions, $approve, $actor, $reason, $reasonLocale], function () use ($operation, $revisions, $approve, $actor, $reason, $reasonLocale) {
            $marks = implode(',', array_fill(0, count($revisions), '?'));
            $keys = $this->query("SELECT DISTINCT s.key_id FROM translation_workbench_value_revisions r JOIN translation_workbench_value_slots s ON s.id=r.slot_id WHERE r.id IN ($marks) ORDER BY s.key_id", array_keys($revisions))->fetchAll(PDO::FETCH_COLUMN);
            foreach ($keys as $key) {
                $this->query('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$key]);
            }
            $result = [];
            foreach ($revisions as $revision => $fingerprint) {
                $result[$revision] = $this->decide((string) Str::uuid(), (int) $revision, $fingerprint, $approve, $actor, $reason, $reasonLocale);
            }

            return $result;
        });
    }

    private function applyDecision(string $operation, int $revision, string $expectedFingerprint, bool $approve, int $actor, string $reason, ?string $reasonLocale = null): array
    {
        if ($reasonLocale !== null && !preg_match('/^[a-z]{2,3}(?:[-_][a-zA-Z0-9]{2,8})*$/D', $reasonLocale)) {
            throw new RuntimeException('Invalid reason language.');
        }
        if ($actor < 1 || trim($reason) === '') {
            throw new RuntimeException('Actor and reason required');
        }
        $key = $this->query('SELECT s.key_id FROM translation_workbench_value_revisions r JOIN translation_workbench_value_slots s ON s.id=r.slot_id WHERE r.id=?', [$revision])->fetchColumn();
        if ($key === false) {
            throw new RuntimeException('Unknown revision');
        }
        $this->query('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$key]);
        $review = RevisionReviewContext::snapshot($this->db, $revision);
        if (! hash_equals($review['fingerprint'], $expectedFingerprint)) {
            throw new RuntimeException('Review context changed');
        }
        $context = $review['context'];
        if ($approve && ($context['current_revision_id'] !== $revision || $context['usages'] === [])) {
            throw new RuntimeException('Approval needs current revision and active usage');
        }
        $decision = $approve ? 'approved' : 'revoked';
        $last = $this->query('SELECT * FROM translation_workbench_reviews WHERE id=?', [$review['latest_decision']])->fetch(PDO::FETCH_ASSOC);
        if ($last && $last['decision'] === $decision && $last['context_fingerprint'] === $review['context_fingerprint']) {
            return ['review' => (int) $last['id']];
        }
        $id = (int) $this->query('INSERT INTO translation_workbench_reviews(operation_id,revision_id,key_name_id,decision,context_fingerprint,context_snapshot,actor_id,reason) VALUES (?,?,?,?,?,?,?,?) RETURNING id',
            [$operation, $revision, $context['key_name_id'], $decision, $review['context_fingerprint'], json_encode($context, JSON_THROW_ON_ERROR), $actor, $reason])->fetchColumn();
        $this->event($operation, $approve ? 'revision_approved' : 'revision_approval_revoked', $actor, $reason, ['review' => $id, 'revision' => $revision, 'reason_locale' => $reasonLocale]);

        return ['review' => $id];
    }

    /** Diagnostic only, not authorization for a future file write. */
    public function approvalIsCurrent(int $revision): bool
    {
        $snapshot = RevisionReviewContext::snapshot($this->db, $revision);
        $last = $this->query('SELECT * FROM translation_workbench_reviews WHERE id=?', [$snapshot['latest_decision']])->fetch(PDO::FETCH_ASSOC);

        return $snapshot['context']['current_revision_id'] === $revision
            && $snapshot['context']['usages'] !== [] && $last
            && $last['decision'] === 'approved'
            && $last['context_fingerprint'] === $snapshot['context_fingerprint'];
    }

    private function event(string $operation, string $type, int $actor, string $reason, array $details): void
    {
        $this->query('INSERT INTO translation_workbench_events(operation_id,event_type,actor_id,reason,details) VALUES (?,?,?,?,?)', [$operation, $type, $actor, $reason, json_encode($details, JSON_THROW_ON_ERROR)]);
    }

    private function query(string $sql, array $params): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement;
    }
}
