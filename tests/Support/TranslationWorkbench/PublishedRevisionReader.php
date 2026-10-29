<?php

namespace Tests\Support\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use PDO;
use RuntimeException;
use Throwable;

/** Read-only verification of a completed rehearsal export, not a replacement baseline. */
final class PublishedRevisionReader
{
    public function __construct(private PDO $db, private string $directory) {}

    public function read(string $run, int $revision): array
    {
        $this->db->beginTransaction();
        try {
            $this->db->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ, READ ONLY');
            $record = $this->query("SELECT r.plan,r.plan_hash,o.snapshot FROM translation_workbench_publication_runs r
                JOIN translation_workbench_publication_outcomes o ON o.run_id=r.id AND o.kind='completed' WHERE r.id=?", [$run])->fetch(PDO::FETCH_ASSOC);
            if (! $record || ! hash_equals($record['plan_hash'], hash('sha256', $record['plan']))) {
                throw new RuntimeException('No verified completed publication');
            }
            $plan = json_decode($record['plan'], true, flags: JSON_THROW_ON_ERROR);
            $receipt = json_decode($record['snapshot'], true, flags: JSON_THROW_ON_ERROR);
            $contexts = array_values(array_filter($plan['contexts'], fn ($c) => (int) $c['context']['revision_id'] === $revision));
            if (count($contexts) !== 1) {
                throw new RuntimeException('Revision is not uniquely included in publication');
            }
            $context = $contexts[0]['context'];
            if ($context['namespace'] !== '' || $context['kind'] !== 'scalar') {
                throw new RuntimeException('This verification reader requires a scalar unnamespaced value');
            }
            [$group, $nested] = explode('.', $context['full_name'], 2);
            $relative = $context['locale'].'/'.$group.'.php';
            if (! preg_match('~^[A-Za-z0-9_-]+/[A-Za-z0-9_-]+\.php$~D', $relative)) {
                throw new RuntimeException('Unsafe publication path');
            }
            $path = $this->directory.'/'.$relative;
            if (is_link($this->directory) || is_link(dirname($path)) || is_link($path)) {
                throw new RuntimeException('Symlink publication path');
            }
            $expected = $plan['files'][$relative]['after'] ?? null;
            if (! is_string($expected) || ($receipt[$relative]['current'] ?? null) !== $expected
                || ($receipt[$relative]['after'] ?? null) !== $expected) {
                throw new RuntimeException('Publication receipt differs from plan');
            }
            $bytes = is_file($path) ? file_get_contents($path) : false;
            if ($bytes === false || ! hash_equals($expected, hash('sha256', $bytes))) {
                throw new RuntimeException('Published file is missing or changed');
            }
            $value = (new BaselineValueReader)->treeFromBytes($bytes);
            foreach (explode('.', $nested) as $part) {
                if (! is_array($value) || ! array_key_exists($part, $value)) {
                    throw new RuntimeException('Published entry missing');
                }
                $value = $value[$part];
            }
            $stored = $this->query('SELECT value FROM translation_workbench_value_revisions WHERE id=?', [$revision])->fetchColumn();
            if (! is_string($value) || $value !== $context['value'] || $value !== $stored) {
                throw new RuntimeException('Published value differs from immutable revision');
            }
            $origin = $this->query('SELECT evidence FROM baseline_value_draft_imports WHERE revision_id=?', [$revision])->fetchColumn();
            $result = ['run' => $run, 'revision' => $revision, 'key' => $context['full_name'], 'locale' => $context['locale'],
                'value' => $value, 'path' => $relative, 'sha256' => $expected,
                'origin' => $origin === false ? null : json_decode($origin, true, flags: JSON_THROW_ON_ERROR)];
            $this->db->commit();

            return $result;
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    private function query(string $sql, array $params): \PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement;
    }
}
