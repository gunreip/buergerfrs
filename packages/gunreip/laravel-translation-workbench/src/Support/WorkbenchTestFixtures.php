<?php

namespace Gunreip\TranslationWorkbench\Support;

use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PDO;

/** Synthetic sources only; real findings, translations and history are never copied. */
final class WorkbenchTestFixtures
{
    public function seed(PDO $db, string $root, int $actor): void
    {
        $q = static function (string $sql, array $params = []) use ($db) {
            $statement = $db->prepare($sql);
            $statement->execute($params);

            return $statement;
        };
        File::ensureDirectoryExists($root.'/fixtures');
        File::ensureDirectoryExists($root.'/lang/en');
        $scan = (string) Str::uuid();
        $q("INSERT INTO translation_workbench_scan_runs VALUES (?,'completed','{}',now())", [$scan]);
        $editor = new GenerationEditor($db, $root, $root.'/lang');
        $fixtureGroup = \Gunreip\TranslationWorkbench\Generation\TranslationKeySegments::configured()->options([])[0]
            ?? throw new \RuntimeException('Configure at least one first translation-key segment before creating test fixtures.');
        $catalog = [];
        $allocator = new \Gunreip\TranslationWorkbench\Generation\SuggestedKeyAllocator($db);
        $create = function (string $state, int $variant, ?int $predecessor = null) use ($q, $scan, $root, $allocator): int {
            $literal = match ($variant) {
                1 => 'Save changes',
                2 => 'Hello :name — review your changes',
                default => "First line\nSecond line with a longer explanation\nThird line",
            };
            $expression = '__('.var_export($literal, true).')';
            $path = 'fixtures/'.$state.'-'.$variant.'.php';
            file_put_contents($root.'/'.$path, "<?php\necho ".$expression.";\n");
            $file = $q('INSERT INTO translation_workbench_source_files(path) VALUES (?) RETURNING id', [$path])->fetchColumn();

            $suggested = $predecessor === null
                ? $allocator->allocate((new \Gunreip\TranslationWorkbench\Scanner\SuggestedKeyFactory)->forLiteral($literal, $path)->key)
                : $q('SELECT suggested_key FROM translation_workbench_findings WHERE id=?', [$predecessor])->fetchColumn();
            return (int) $q('INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_key,provenance,predecessor_id) VALUES (?,?,2,6,?,?,?,?,?::jsonb,?) RETURNING id',
                [$file, $scan, $expression, $literal, $literal, $suggested, json_encode(['ui_test' => true, 'case' => $state, 'variant' => $variant]), $predecessor])->fetchColumn();
        };
        foreach (['unassigned', 'assigned', 'shared', 'archived', 'moved'] as $state) {
            for ($variant = 1; $variant <= 3; $variant++) {
                $finding = $create($state, $variant);
                $catalog[] = ['id' => $finding, 'state' => $state, 'variant' => $variant];
                if ($state === 'unassigned') {
                    continue;
                }
                // Provide key-selection contexts without creating any translation values.
                $name = $fixtureGroup.'.test_'.$state.'_'.$variant.'.actions.'.($variant === 2 ? 'a_long_translation_key_for_testing_the_select_option_layout' : 'save');
                $assignment = $editor->assign($finding, 0, null, $actor, 'UI fixture: assign a context-specific key.', $name);
                $key = $assignment['key'];
                $link = $assignment['link'];
                if ($state === 'shared') {
                    foreach (['second', 'third'] as $suffix) {
                        $additional = $create('shared-'.$suffix, $variant);
                        $editor->assign($additional, $key, null, $actor, 'UI fixture: reuse a shared key.');
                    }
                }
                if (in_array($state, ['archived', 'moved'], true)) {
                    $q("UPDATE translation_workbench_findings SET status='obsolete', retired_at=now(), retired_reason=? WHERE id=?",
                        [$state === 'moved' ? 'Unique same-file movement' : 'Absent from completed scan inventory', $finding]);
                    if ($state === 'moved') {
                        $successor = $create('successor', $variant, $finding);
                        $editor->assign($successor, $key, null, $actor, 'UI fixture: retain the key after a source move.');
                    }

                    continue;
                }
                // Drafts, reviews and publications are created by the user through the UI.

            }
        }
        $manifest = hash('sha256', 'workbench-ui-source-cases');
        $q('INSERT INTO capture_imports VALUES (?,?,?,?::jsonb)', [$manifest, $manifest, $scan, '{}']);
        $cases = [];
        for ($i = 1; $i <= 9; $i++) {
            $id = 'source-case-'.$i;
            $path = 'fixtures/dynamic-'.$i.'.php';
            $expression = '__($option->label)';
            file_put_contents($root.'/'.$path, "<?php\necho ".$expression.";\n");
            $cases[$id] = ['capture_id' => $id, 'manifest_sha256' => $manifest, 'source_path' => $path,
                'source_sha256' => hash_file('sha256', $root.'/'.$path), 'source_line' => 2, 'source_column' => 6,
                'raw_expression' => $expression, 'reason' => 'dynamic_argument', 'state' => 'pending'];
            $q("INSERT INTO capture_outcomes VALUES (?,?,'pending','dynamic_argument',NULL)", [$manifest, $id]);
        }
        $snapshot = ['database' => $db->query('SELECT current_database()')->fetchColumn(),
            'generation' => ['schema' => 'current_generation_v2', 'generation' => $db->query('SELECT generation FROM workbench_generation')->fetchColumn()], 'cases' => $cases];
        file_put_contents($root.'/source-cases.json', json_encode($snapshot, JSON_THROW_ON_ERROR));
        $decisions = new GenerationCaseDecisions($root, $root.'/source-cases.json', $db);
        foreach ($cases as $id => $case) {
            $i = (int) substr($id, strlen('source-case-'));
            if ($i > 3) {
                $decisions->save($db, $case, $i <= 6 ? 'translate' : 'database_value', 'UI fixture: explicit source usage decision.', $actor, 0);
            }
        }
        $groups = [];
        foreach (['document_types', 'document_categories', 'role_categories'] as $group) {
            $groups[$group] = ['table' => 'fixture_'.$group, 'columns' => 'value, label', 'selection' => 'Synthetic UI examples', 'count' => 3,
                'rows' => [['value' => 'example', 'source_label' => 'Example'], ['value' => '', 'source_label' => null], ['value' => 'long-value-for-layout-testing', 'source_label' => "First line\nSecond line"]]];
        }
        file_put_contents($root.'/runtime-options.json', json_encode(['observed_at' => now()->toIso8601String(), 'groups' => $groups], JSON_THROW_ON_ERROR));
        file_put_contents($root.'/cases.json', json_encode($catalog, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }
}
