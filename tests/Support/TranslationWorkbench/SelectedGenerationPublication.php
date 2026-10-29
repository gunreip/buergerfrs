<?php

namespace Tests\Support\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Generation\BaselineDraftWriter;
use Gunreip\TranslationWorkbench\Generation\BaselineOperation;
use Gunreip\TranslationWorkbench\Generation\BaselineRevisionReviewer;
use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Gunreip\TranslationWorkbench\Generation\RevisionReviewContext;
use PDO;

/** Exercises the existing writers on the freshly transferred, isolated generation. */
final class SelectedGenerationPublication
{
    public function verify(PDO $db, string $cluster, int $key, callable $check, callable $reject): void
    {
        $id = static fn (int $n): string => '00000000-0000-4000-8000-'.str_pad((string) $n, 12, '0', STR_PAD_LEFT);
        $evidence = static fn (): string => $db->query("SELECT md5(coalesce(jsonb_agg(to_jsonb(f) ORDER BY id)::text,'')) FROM translation_workbench_findings f")->fetchColumn();
        $originalFindings = $evidence();
        $importer = new CorrectedValueDraftImporter($db);
        $preview = $importer->preview($id(202), 'en');
        $draft = $importer->import($id(203), $id(202), 'en', $preview['fingerprint'], 999999, 'Import evidence into fresh generation');
        $check($importer->import($id(203), $id(202), 'en', $preview['fingerprint'], 999999, 'Import evidence into fresh generation') == $draft,
            'Fresh generation draft retry changed identity');
        $origin = $db->query('SELECT evidence::text FROM baseline_value_draft_imports')->fetchColumn();
        $reviewer = new BaselineRevisionReviewer($db);
        $publisher = new PostgresPublisher($cluster, $db);
        $check(str_ends_with($publisher->directory, '-'.$db->query('SELECT current_schema()')->fetchColumn()),
            'Fresh generation shares publication files with restored baseline');
        $reject(fn () => $publisher->prepare($id(204), [$draft['revision']], 999999, 'Unreviewed export'),
            'Fresh generation exported unreviewed draft');
        $reviewer->decide($id(205), $draft['revision'], RevisionReviewContext::snapshot($db, $draft['revision'])['fingerprint'],
            true, 999999, 'Review imported evidence');
        $value = $preview['evidence']['value'].' [isolated edited revision]';
        $save = fn (string $operation, int $expected): array => (new BaselineOperation($db))->run($operation, 'save',
            [$key, 'en', $value, $expected, 999999, 'Edit isolated draft'],
            fn () => (new BaselineDraftWriter($db))->save($operation, $key, 'en', $value, $expected, 999999, 'Edit isolated draft'));
        $edited = $save($id(206), $draft['revision']);
        $check($save($id(206), $draft['revision']) == $edited, 'Edit retry created another revision');
        $check($edited['revision'] !== $draft['revision'], 'Editing overwrote imported revision');
        $reject(fn () => $save($id(207), $draft['revision']), 'Stale edit overwrote current revision');
        $check(! $reviewer->approvalIsCurrent($draft['revision']) && ! $reviewer->approvalIsCurrent($edited['revision']),
            'Editing retained approval');
        $reject(fn () => $publisher->prepare($id(208), [$edited['revision']], 999999, 'Edited unreviewed export'),
            'Edited value inherited publication permission');
        $reviewer->decide($id(209), $edited['revision'], RevisionReviewContext::snapshot($db, $edited['revision'])['fingerprint'],
            true, 999999, 'Review edited value');
        $plan = $publisher->prepare($id(210), [$edited['revision']], 999999, 'Publish fresh generation to temporary files');
        $path = $publisher->directory.'/en/rehearsal.php';
        $check(! file_exists($path), 'Preparation wrote fresh generation file');
        $reject(fn () => $publisher->publish($id(210), str_repeat('0', 64)), 'Stale fresh generation publication accepted');
        $receipt = $publisher->publish($id(210), $plan['fingerprint']);
        $check($receipt['kind'] === 'completed', 'Fresh generation publication incomplete');
        $check($publisher->publish($id(210), $plan['fingerprint']) === $receipt, 'Publication retry changed receipt');
        $reader = new PublishedRevisionReader($db, $publisher->directory);
        $readback = $reader->read($id(210), $edited['revision']);
        $check($readback['value'] === $value && $readback['key'] === 'rehearsal.correction.type' && $readback['locale'] === 'en',
            'Fresh generation readback differs from edited revision');
        $check((new BaselineValueReader)->treeFromBytes(file_get_contents($path)) === ['correction' => ['type' => $value]],
            'Fresh generation export is not a nested language array');
        $bytes = file_get_contents($path);
        try {
            file_put_contents($path, $bytes."\n// drift\n");
            $reject(fn () => $reader->read($id(210), $edited['revision']), 'Fresh generation readback ignored drift');
        } finally {
            file_put_contents($path, $bytes);
        }
        $check($reader->read($id(210), $edited['revision']) === $readback, 'Restored export no longer readable');
        $check($evidence() === $originalFindings, 'Editing or publication changed immutable findings');
        $check($db->query('SELECT evidence::text FROM baseline_value_draft_imports')->fetchColumn() === $origin,
            'Editing or publication changed imported provenance');
        $check((int) $db->query('SELECT count(*) FROM translation_workbench_value_revisions')->fetchColumn() === 2,
            'Unexpected revisions in fresh generation');
    }
}
