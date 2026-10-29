<?php

namespace Tests\Support\TranslationWorkbench;

use PDO;
use RuntimeException;

/** Reads raw source values only; never calls models, collectors or translation providers. */
final class RuntimeOptionObservation
{
    public function collect(PDO $db): array
    {
        if (! $db->inTransaction()) {
            throw new RuntimeException('A consistent read transaction is required');
        }
        if ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql'
            && $db->query('SHOW transaction_read_only')->fetchColumn() !== 'on') {
            throw new RuntimeException('A read-only transaction is required');
        }
        $queries = [
            'document_types' => ['table' => 'person_document_types', 'columns' => 'code, label',
                'selection' => 'is_active = true; sort_order, label, code',
                'sql' => 'SELECT code AS value, label AS source_label FROM person_document_types WHERE is_active = true ORDER BY sort_order, label, code'],
            'document_categories' => ['table' => 'person_documents', 'columns' => 'category',
                'selection' => 'category IS NOT NULL AND category <> empty string; distinct',
                'sql' => "SELECT DISTINCT category AS value FROM person_documents WHERE category IS NOT NULL AND category <> '' ORDER BY category"],
            'role_categories' => ['table' => 'roles', 'columns' => 'category',
                'selection' => 'is_assignable = true; distinct raw category (including NULL/empty)',
                'sql' => 'SELECT DISTINCT category AS value FROM roles WHERE is_assignable = true ORDER BY category'],
        ];
        $groups = [];
        foreach ($queries as $id => $definition) {
            $rows = $db->query($definition['sql'])->fetchAll(PDO::FETCH_ASSOC);
            $groups[$id] = [...$definition, 'rows' => $rows, 'count' => count($rows),
                'state' => $rows === [] ? 'observed_empty' : 'observed',
                'translation_decision' => null, 'runtime_exhaustiveness_proven' => false];
        }

        return $groups;
    }
}
