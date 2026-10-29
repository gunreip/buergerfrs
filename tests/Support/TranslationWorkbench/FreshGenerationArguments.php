<?php

namespace Tests\Support\TranslationWorkbench;

use PDO;
use RuntimeException;

/** Validates unresolved source evidence without turning observations into decisions. */
final class FreshGenerationArguments
{
    public function verify(PDO $db, string $root, string $backup, callable $check, callable $reject): array
    {
        $audit = $root.'/docs/audits/translation-workbench-2026-10-15/';
        $read = static fn (string $path): array => json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
        $context = $read($audit.'copied-source-context-2026-10-19.json');
        $candidatePath = dirname($root.'/'.$context['manifest']).'/candidates.jsonl';
        $roleReport = $read($audit.'source-roles-2026-10-19.json');
        $reviewJson = file_get_contents($audit.'pending-capture-review-2026-10-19.json');
        $reviewHash = $roleReport['review_sha256'];
        $fingerprint = static function () use ($db): array {
            $result = [];
            foreach (['findings', 'keys', 'key_names', 'key_findings', 'value_revisions', 'reviews', 'events'] as $table) {
                $result[$table] = $db->query("SELECT md5(coalesce(string_agg(row_to_json(t)::text,chr(10) ORDER BY row_to_json(t)::text),'')) FROM translation_workbench_$table t")->fetchColumn();
            }

            return $result;
        };
        $before = $fingerprint();
        $roles = new SourceRoleImporter;
        $import = fn (?callable $fault = null): array => $roles->import($db, $candidatePath, $context['candidate_file_sha256'],
            $context['manifest_sha256'], $reviewJson, $reviewHash, $fault);
        $reject(fn () => $import(static function (int $n): void {
            if ($n === 2) {
                throw new RuntimeException('Injected role import failure');
            }
        }), 'Interrupted role import succeeded');
        $check((int) $db->query('SELECT count(*) FROM capture_source_roles')->fetchColumn() === 0, 'Partial role import survived');
        $roleResult = $import();
        $check($roleResult['counts'] == $roleReport['counts'], 'Fresh generation role counts differ');
        $check($import()['replayed'], 'Role retry not recognized');
        $reject(fn () => $roles->import($db, $candidatePath, $context['candidate_file_sha256'], $context['manifest_sha256'],
            $reviewJson."\n", hash('sha256', $reviewJson."\n")), 'Changed role review replaced immutable evidence');
        $check((int) $db->query("SELECT count(*) FROM capture_source_roles WHERE role IN ('value_usage','option_source') AND finding_id IS NOT NULL")->fetchColumn() === 0,
            'Usage or option became scalar finding');
        $json = file_get_contents($audit.'source-arguments-2026-10-19.json');
        $expectedHash = $read($audit.'source-argument-persistence-2026-10-19.json')['argument_report_sha256'];
        $input = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        if (hash('sha256', $json) !== $expectedHash || $input['review_sha256'] !== $reviewHash
            || $input['capture_manifest_sha256'] !== $context['manifest_sha256']) {
            throw new RuntimeException('Argument provenance differs');
        }
        $verifySource = static function (string $path, string $hash) use ($backup): void {
            $resolved = realpath($backup.'/source/'.$path);
            if (! $resolved || ! str_starts_with($resolved, realpath($backup.'/source').'/') || hash_file('sha256', $resolved) !== $hash) {
                throw new RuntimeException('Argument source evidence changed');
            }
        };
        foreach ($input['items'] as $item) {
            $verifySource($item['source_path'], $item['source_sha256']);
            if (isset($item['helper'])) {
                $verifySource($item['helper']['path'], $item['helper']['sha256']);
            }
        }
        foreach ($input['producers'] as $producer) {
            $verifySource($producer['path'], $producer['sha256']);
        }
        $arguments = new SourceArgumentImporter;
        $result = $arguments->import($db, $json, $expectedHash);
        $check($arguments->import($db, $json, $expectedHash)['replayed'], 'Argument retry not recognized');
        $check($result['items'] === count($input['items']) && $result['producers'] === count($input['producers']),
            'Argument coverage differs');
        $saved = $db->query('SELECT capture_id,evidence FROM source_argument_evidence')->fetchAll(PDO::FETCH_KEY_PAIR);
        foreach ($input['items'] as $item) {
            $check(json_decode($saved[$item['capture_id']], true, flags: JSON_THROW_ON_ERROR) == $item, 'Argument evidence roundtrip differs');
        }
        $saved = $db->query('SELECT name,evidence FROM source_argument_producers')->fetchAll(PDO::FETCH_KEY_PAIR);
        foreach ($input['producers'] as $name => $producer) {
            $check(json_decode($saved[$name], true, flags: JSON_THROW_ON_ERROR) == $producer, 'Producer evidence roundtrip differs');
        }
        $badReview = $input;
        $badReview['review_sha256'] = str_repeat('0', 64);
        $missing = $input;
        $removed = array_pop($missing['items']);
        $missing['counts'][$removed['evidence_kind']]--;
        $decided = $input;
        $decided['items'][count($decided['items']) - 1]['decision'] = 'approved';
        $usage = $input;
        $usage['items'][0]['capture_id'] = $db->query("SELECT capture_id FROM capture_source_roles WHERE role='value_usage' LIMIT 1")->fetchColumn();
        foreach ([$badReview, $missing, $decided, $usage] as $bad) {
            $bytes = json_encode($bad, JSON_THROW_ON_ERROR);
            $reject(fn () => $arguments->import($db, $bytes, hash('sha256', $bytes)), 'Invalid argument evidence accepted');
            $check((int) $db->query('SELECT count(*) FROM source_argument_batches')->fetchColumn() === 1
                && (int) $db->query('SELECT count(*) FROM source_argument_evidence')->fetchColumn() === count($input['items']),
                'Rejected arguments left partial writes');
        }
        $check($fingerprint() === $before, 'Evidence import changed findings, assignments, values or history');

        return ['roles' => $roleResult['counts'], 'arguments' => $input['counts'], 'producers' => $result['producers'],
            'review_sha256' => $reviewHash, 'argument_report_sha256' => $expectedHash];
    }
}
