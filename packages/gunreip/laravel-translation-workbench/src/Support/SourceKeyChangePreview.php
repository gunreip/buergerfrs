<?php

namespace Gunreip\TranslationWorkbench\Support;

use PhpParser\Node;
use PhpParser\ParserFactory;
use RuntimeException;

/** Builds proposed bytes only. Never writes files or changes finding evidence. */
final class SourceKeyChangePreview
{
    public function preview(string $bytes, string $hash, string $path, array $findings, string $source, string $target): array
    {
        if (! hash_equals($hash, hash('sha256', $bytes)) || $source === $target
            || ! preg_match('/^[A-Za-z0-9_-]+(?:\.[A-Za-z0-9_-]+){2,}$/D', $target)) {
            throw new RuntimeException('Source hash or target is invalid');
        }
        $lines = explode("\n", $bytes);
        $edits = [];
        $contexts = new CopiedSourceContext;
        foreach ($findings as $finding) {
            $line = (int) $finding['source_line'];
            $column = (int) $finding['source_column'];
            if ($line < 1 || $column < 1 || ! isset($lines[$line - 1]) || $column > strlen($lines[$line - 1]) + 1) {
                throw new RuntimeException('Invalid captured position');
            }
            $offset = strlen(implode("\n", array_slice($lines, 0, $line - 1))) + ($line > 1 ? 1 : 0) + $column - 1;
            $expression = $finding['raw_expression'];
            if (! is_string($expression) || $expression === '' || substr($bytes, $offset, strlen($expression)) !== $expression
                || $contexts->check($path, $bytes, $offset)['state'] !== 'confirmed_call') {
                throw new RuntimeException('Captured source expression no longer matches');
            }
            $normalized = preg_replace('/^@(lang|js)\s*(?=\()/', '__', $expression);
            $delta = strlen($expression) - strlen($normalized);
            $nodes = (new ParserFactory)->createForHostVersion()->parse('<?php '.$normalized.';');
            $call = count($nodes ?? []) === 1 && $nodes[0] instanceof Node\Stmt\Expression ? $nodes[0]->expr : null;
            $argument = ($call instanceof Node\Expr\FuncCall || $call instanceof Node\Expr\StaticCall) ? ($call->args[0] ?? null) : null;
            if (! $argument instanceof Node\Arg || $argument->unpack || ! $argument->value instanceof Node\Scalar\String_
                || $argument->value->value !== $source) {
                throw new RuntimeException('Expected exact static translation key');
            }
            $start = $offset + $argument->value->getStartFilePos() - 6 + $delta;
            $length = $argument->value->getEndFilePos() - $argument->value->getStartFilePos() + 1;
            $old = substr($bytes, $start, $length);
            if (! in_array($old[0] ?? '', ["'", '"'], true) || substr($old, -1) !== $old[0]) {
                throw new RuntimeException('Unsupported key quoting');
            }
            $edits[] = ['finding' => (int) $finding['id'], 'line' => $line, 'column' => $column, 'offset' => $start, 'length' => $length,
                'before' => $old, 'after' => $old[0].$target.$old[0]];
        }
        usort($edits, fn ($a, $b) => $a['offset'] <=> $b['offset']);
        $end = -1;
        foreach ($edits as $edit) {
            if ($edit['offset'] < $end) {
                throw new RuntimeException('Overlapping or duplicate finding positions');
            }
            $end = $edit['offset'] + $edit['length'];
        }
        $proposed = $bytes;
        foreach (array_reverse($edits) as $edit) {
            $proposed = substr_replace($proposed, $edit['after'], $edit['offset'], $edit['length']);
        }

        return ['path' => $path, 'before_sha256' => $hash, 'after_sha256' => hash('sha256', $proposed), 'edits' => $edits, 'proposed_bytes' => $proposed];
    }
}
