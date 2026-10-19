<?php

namespace Tests\Support\TranslationWorkbench;

use PhpParser\Node;
use PhpParser\ParserFactory;
use RuntimeException;

/** Test-only evidence reader. PHP arrays are parsed, never required/evaluated. */
final class BaselineValueReader
{
    public function phpFile(string $path, string $locale, string $group, string $expectedHash): array
    {
        $source = $this->verifiedBytes($path, $expectedHash);
        $statements = (new ParserFactory)->createForHostVersion()->parse($source);
        if (count($statements ?? []) !== 1 || ! $statements[0] instanceof Node\Stmt\Return_ || ! $statements[0]->expr instanceof Node\Expr\Array_) {
            throw new RuntimeException('Only a static returned translation array is supported');
        }
        $rows = [];
        $this->flatten($statements[0]->expr, $group, $locale, $rows, 'lang_file', $path.'#sha256='.$expectedHash);

        return array_values($rows);
    }

    /** Explicit archive snapshot, not an arbitrary live database query. */
    public function archiveJson(string $path, string $expectedHash): array
    {
        $data = json_decode($this->verifiedBytes($path, $expectedHash), true, flags: JSON_THROW_ON_ERROR);
        if (! is_array($data) || ! is_string($data['archive_id'] ?? null) || trim($data['archive_id']) === '' || ! is_array($data['values'] ?? null)) {
            throw new RuntimeException('Archive identity and values required');
        }
        $rows = [];
        $identities = [];
        foreach ($data['values'] as $row) {
            foreach (['table', 'id', 'key', 'locale', 'value'] as $field) {
                if (! array_key_exists($field, $row) || (! is_string($row[$field]) && ! ($field === 'id' && is_int($row[$field])))) {
                    throw new RuntimeException('Invalid archive value');
                }
            }
            $identity = $row['table'].':'.$row['id'];
            if (isset($identities[$identity])) {
                throw new RuntimeException('Duplicate archive identity');
            }
            $identities[$identity] = true;
            $rows[] = $this->row($row['key'], $row['locale'], $row['value'], 'database_archive', $path.'#sha256='.$expectedHash.'#'.$data['archive_id'].':'.$identity);
        }

        return $rows;
    }

    public function plan(array $rows): array
    {
        $groups = [];
        foreach ($rows as $row) {
            $row = $this->row($row['key'], $row['locale'], $row['value'], $row['source'], $row['reference']);
            $identity = json_encode([$row['key'], $row['locale']], JSON_THROW_ON_ERROR);
            $groups[$identity][] = $row;
        }
        $result = [];
        foreach ($groups as $candidates) {
            $values = array_values(array_unique(array_column($candidates, 'value'), SORT_STRING));
            $result[] = ['key' => $candidates[0]['key'], 'locale' => $candidates[0]['locale'],
                'state' => count($values) === 1 ? 'consistent' : 'conflict',
                'value' => count($values) === 1 ? $values[0] : null, 'candidates' => $candidates];
        }

        return $result;
    }

    private function flatten(Node\Expr\Array_ $node, string $prefix, string $locale, array &$rows, string $source, string $reference): void
    {
        $keys = [];
        foreach ($node->items as $item) {
            if ($item === null || $item->unpack || $item->byRef || ! $item->key instanceof Node\Scalar\String_) {
                throw new RuntimeException('Only explicit string keys are supported');
            }
            $key = $item->key->value;
            if (isset($keys[$key])) {
                throw new RuntimeException('Duplicate PHP array key');
            }
            $keys[$key] = true;
            $name = $prefix.'.'.$key;
            if ($item->value instanceof Node\Expr\Array_) {
                $this->flatten($item->value, $name, $locale, $rows, $source, $reference);
            } elseif ($item->value instanceof Node\Scalar\String_) {
                if (array_key_exists($name, $rows)) {
                    throw new RuntimeException('Ambiguous flattened translation key');
                }
                $rows[$name] = $this->row($name, $locale, $item->value->value, $source, $reference);
            } else {
                throw new RuntimeException('Only literal string values or nested arrays are supported');
            }
        }
    }

    private function row(string $key, string $locale, string $value, string $source, string $reference): array
    {
        if (trim($key) === '' || trim($locale) === '' || trim($reference) === '' || ! in_array($source, ['lang_file', 'database_archive'], true)) {
            throw new RuntimeException('Complete source evidence required');
        }

        return compact('key', 'locale', 'value', 'source', 'reference');
    }

    private function verifiedBytes(string $path, string $hash): string
    {
        $bytes = file_get_contents($path);
        if ($bytes === false || ! hash_equals(hash('sha256', $bytes), $hash)) {
            throw new RuntimeException('Baseline source hash mismatch');
        }

        return $bytes;
    }
}
