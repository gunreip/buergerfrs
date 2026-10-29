<?php

namespace Tests\Support\TranslationWorkbench;

use PDO;
use RuntimeException;
use Throwable;

/** Evidence projection only; never creates findings, keys or decisions. */
final class SourceRoleImporter
{
    public function import(PDO $db, string $candidatePath, string $candidateHash, string $manifestHash, string $reviewJson, string $reviewHash, ?callable $fault = null): array
    {
        if (hash_file('sha256', $candidatePath) !== $candidateHash || hash('sha256', $reviewJson) !== $reviewHash) {
            throw new RuntimeException('Role input checksum mismatch');
        }
        $review = json_decode($reviewJson, true, flags: JSON_THROW_ON_ERROR);
        if ($review['candidate_file_sha256'] !== $candidateHash || $review['capture_manifest_sha256'] !== $manifestHash) {
            throw new RuntimeException('Role review belongs to another capture');
        }
        $reviews = [];
        foreach ($review['items'] as $item) {
            if (isset($reviews[$item['capture_id']])) {
                throw new RuntimeException('Duplicate role review');
            }
            $reviews[$item['capture_id']] = $item;
        }
        $db->beginTransaction();
        try {
            $db->exec('LOCK TABLE capture_source_roles IN EXCLUSIVE MODE');
            $query = $db->prepare('SELECT * FROM capture_outcomes WHERE manifest_sha256=?');
            $query->execute([$manifestHash]);
            $outcomes = array_column($query->fetchAll(PDO::FETCH_ASSOC), null, 'capture_id');
            $query = $db->prepare('SELECT * FROM capture_source_roles WHERE manifest_sha256=?');
            $query->execute([$manifestHash]);
            $existing = array_column($query->fetchAll(PDO::FETCH_ASSOC), null, 'capture_id');
            $replayed = $existing !== [];
            $insert = $db->prepare('INSERT INTO capture_source_roles VALUES (?,?,?,?,?,?,?)');
            $classifier = new CaptureSourceRole;
            $counts = [];
            $seen = [];
            $stream = fopen($candidatePath, 'r');
            try {
                while (($line = fgets($stream)) !== false) {
                    $capture = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
                    $id = $capture['capture_id'];
                    if (isset($seen[$id]) || ! isset($outcomes[$id])) {
                        throw new RuntimeException('Duplicate or missing capture outcome');
                    }
                    $seen[$id] = true;
                    $role = $classifier->classify($capture, $reviews[$id] ?? null);
                    unset($reviews[$id]);
                    $outcome = $outcomes[$id];
                    if ($outcome['disposition'] !== $role['state']) {
                        throw new RuntimeException('Capture disposition differs from role');
                    }
                    if ($replayed) {
                        $saved = $existing[$id] ?? null;
                        if (! $saved || $saved['review_sha256'] !== $reviewHash || $saved['role'] !== $role['role']
                            || $saved['state'] !== $role['state'] || $saved['finding_id'] !== $outcome['finding_id']
                            || json_decode($saved['evidence'], true, flags: JSON_THROW_ON_ERROR) != $role['evidence']) {
                            throw new RuntimeException('Existing role projection differs');
                        }
                    } else {
                        $insert->execute([$manifestHash, $id, $reviewHash, $role['role'], $role['state'],
                            json_encode($role['evidence'], JSON_THROW_ON_ERROR), $outcome['finding_id']]);
                    }
                    $key = $role['role'].':'.$role['state'];
                    $counts[$key] = ($counts[$key] ?? 0) + 1;
                    if ($fault) {
                        $fault(count($seen));
                    }
                }
            } finally {
                fclose($stream);
            }
            if ($reviews !== [] || $seen === [] || count($seen) !== count($outcomes)
                || ($replayed && count($existing) !== count($seen))
                || hash_file('sha256', $candidatePath) !== $candidateHash) {
                throw new RuntimeException('Incomplete or changed role projection');
            }
            $db->commit();

            return ['replayed' => $replayed, 'counts' => $counts];
        } catch (Throwable $error) {
            $db->rollBack();
            throw $error;
        }
    }
}
