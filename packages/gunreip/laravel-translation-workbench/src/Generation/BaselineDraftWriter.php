<?php

namespace Gunreip\TranslationWorkbench\Generation;

use PDO;
use RuntimeException;

/** Shared revision writer; its caller owns the idempotent transaction. */
final class BaselineDraftWriter
{
    public function __construct(private PDO $db) {}

    public function save(string $operation, int $key, string $locale, string $value, ?int $expectedRevision, int $actor, string $reason, ?string $optionIdentity = null, int $eventIndex = 0, ?string $reasonLocale = null): array
    {
        if (! $this->db->inTransaction()) {
            throw new RuntimeException('Draft writer requires a transaction');
        }
        if ($optionIdentity === '') {
            throw new RuntimeException('Option identity must not be empty');
        }
        $kind = $optionIdentity === null ? 'scalar' : 'option';
        // Lock the parent even before the locale slot exists.
        $this->query('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$key]);
        $this->query('INSERT INTO translation_workbench_value_slots(key_id,locale,kind,option_identity) VALUES (?,?,?,?) ON CONFLICT DO NOTHING', [$key, $locale, $kind, $optionIdentity]);
        $slot = $this->query('SELECT * FROM translation_workbench_value_slots WHERE key_id=? AND locale=? AND kind=? AND option_identity IS NOT DISTINCT FROM ? FOR UPDATE', [$key, $locale, $kind, $optionIdentity])->fetch(PDO::FETCH_ASSOC);
        $current = $slot['current_revision_id'] === null ? null : (int) $slot['current_revision_id'];
        if ($current !== $expectedRevision) {
            throw new RuntimeException('Revision context changed');
        }
        $revision = $this->query('SELECT * FROM translation_workbench_value_revisions WHERE id=?', [$current])->fetch(PDO::FETCH_ASSOC);
        if ($revision && $revision['value'] === $value) {
            return ['revision' => $current];
        }
        $id = (int) $this->query('INSERT INTO translation_workbench_value_revisions(slot_id,revision_number,value,actor_id,reason) VALUES (?,?,?,?,?) RETURNING id', [$slot['id'], ($revision['revision_number'] ?? 0) + 1, $value, $actor, $reason])->fetchColumn();
        $this->query('UPDATE translation_workbench_value_slots SET current_revision_id=? WHERE id=?', [$id, $slot['id']]);
        $this->query('INSERT INTO translation_workbench_events(operation_id,event_index,event_type,actor_id,reason,details) VALUES (?,?,?,?,?,?)',
            [$operation, $eventIndex, 'value_changed', $actor, $reason, json_encode(['revision' => $id, 'reason_locale' => $reasonLocale], JSON_THROW_ON_ERROR)]);

        return ['revision' => $id];
    }

    private function query(string $sql, array $params): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement;
    }
}
