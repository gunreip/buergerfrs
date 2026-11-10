<?php

namespace Gunreip\TranslationWorkbench\Generation;

use PDO;

/** Read-only equivalence of reviewed usages across receipted source replacements. */
final class ReviewUsageContinuity
{
    // Inputs: review_usages (jsonb), review_key (bigint). Missing or additional usages
    // are not equivalent. Only the original successor link created in the same
    // transaction as retirement is accepted, never a later reassignment.
    public const RESOLVED = <<<'SQL'
        (WITH RECURSIVE usage_walk AS (
            SELECT l.id AS link, f.id AS finding
            FROM jsonb_array_elements(review_usages) u
            JOIN translation_workbench_key_findings l ON l.id=(u->>'link')::bigint
                AND l.finding_id=(u->>'finding')::bigint AND l.key_id=review_key
            JOIN translation_workbench_findings f ON f.id=l.finding_id
            UNION ALL
            SELECT next_link.id, successor.id
            FROM usage_walk w
            JOIN translation_workbench_key_findings old_link ON old_link.id=w.link
            JOIN translation_workbench_findings predecessor ON predecessor.id=w.finding
                AND predecessor.status='obsolete' AND predecessor.retired_at=old_link.valid_to
            JOIN translation_workbench_findings successor ON successor.predecessor_id=predecessor.id
                AND successor.id>predecessor.id AND successor.source_file_id=predecessor.source_file_id
            JOIN translation_workbench_operations receipt ON receipt.id=successor.captured_scan_id
                AND ((receipt.kind='source_replacement_applied'
                    AND receipt.result->'successors' @> jsonb_build_array(jsonb_build_object('predecessor',predecessor.id,'successor',successor.id)))
                  OR (receipt.kind='complete_rescan'
                    AND successor.raw_expression=predecessor.raw_expression
                    AND successor.raw_source_code_literal IS NOT DISTINCT FROM predecessor.raw_source_code_literal
                    AND successor.raw_literal IS NOT DISTINCT FROM predecessor.raw_literal
                    AND receipt.result->'moved' @> jsonb_build_array(jsonb_build_object('predecessor',predecessor.id,'successor',successor.id))))
            JOIN translation_workbench_key_findings next_link ON next_link.finding_id=successor.id
                AND next_link.key_id=old_link.key_id AND next_link.valid_from=old_link.valid_to
                AND next_link.id=(SELECT min(first_link.id) FROM translation_workbench_key_findings first_link WHERE first_link.finding_id=successor.id)
            WHERE old_link.valid_to IS NOT NULL
        )
        SELECT CASE WHEN count(*)=jsonb_array_length(review_usages)
            THEN COALESCE(jsonb_agg(jsonb_build_object('link',w.link,'finding',w.finding) ORDER BY w.link),'[]'::jsonb)
            ELSE NULL END
        FROM usage_walk w
        JOIN translation_workbench_key_findings l ON l.id=w.link AND l.valid_to IS NULL
        JOIN translation_workbench_findings f ON f.id=w.finding AND f.status='active')
        SQL;

    public static function contextMatches(PDO $db, array $expected, array $current): bool
    {
        $usages = $expected['usages'];
        unset($expected['usages']);
        $actualUsages = $current['usages'];
        unset($current['usages']);
        if ($expected != $current) return false;
        if ($usages == $actualUsages) return true;
        $query = $db->prepare('SELECT '.self::RESOLVED.' FROM (SELECT ?::jsonb AS review_usages, ?::bigint AS review_key) inputs');
        $query->execute([json_encode($usages, JSON_THROW_ON_ERROR), $expected['key_id']]);
        $resolved = $query->fetchColumn();

        return $resolved !== null && $resolved !== false
            && json_decode($resolved, true, flags: JSON_THROW_ON_ERROR) == $actualUsages;
    }
}
