<?php

namespace Gunreip\TranslationWorkbench\Generation;

use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use RuntimeException;

/** Format static translation arrays consistently, preserving comments and values. Never execute PHP. */
final class TranslationFileEditor
{
    public function render(?string $original, array $tree): string
    {
        $reader = new BaselineValueReader;
        if ($original !== null) {
            $reader->treeFromBytes($original, true);
        }
        $parser = (new ParserFactory)->createForHostVersion();
        $new = $original === null
            ? [new Node\Stmt\Return_($this->expression($tree))]
            : $parser->parse($original);
        if ($original !== null) {
            $this->retainTrailingComments($new, $parser->getTokens());
        }
        foreach ($new as $statement) {
            if ($statement instanceof Node\Stmt\Return_) {
                $this->update($statement->expr, $tree);
            }
        }
        $printer = new class(['indent' => '    ']) extends Standard
        {
            protected function pExpr_Array(Node\Expr\Array_ $node): string
            {
                return $node->items === [] ? '[]' : '['.$this->pCommaSeparatedMultiline($node->items, true).$this->nl.']';
            }
        };
        $bytes = $printer->prettyPrintFile($new)."\n";
        if ($reader->treeFromBytes($bytes, true) !== $tree) {
            throw new RuntimeException('Rendered translation file does not match the planned values');
        }

        return $bytes;
    }

    /** The parser may leave comments before a closing array bracket unattached. */
    private function retainTrailingComments(array $statements, array $tokens): void
    {
        $nodes = (new NodeFinder)->find($statements, static fn (Node $node) => true);
        $attached = [];
        foreach ($nodes as $node) {
            foreach ($node->getComments() as $comment) {
                $attached[$comment->getStartFilePos()] = true;
            }
        }
        foreach ($tokens as $token) {
            if (! in_array($token->id, [T_COMMENT, T_DOC_COMMENT], true) || isset($attached[$token->pos])) {
                continue;
            }
            $previous = null;
            foreach ($nodes as $node) {
                if ($node instanceof Node\ArrayItem && $node->getEndFilePos() < $token->pos
                    && ($previous === null || $node->getEndFilePos() > $previous->getEndFilePos())) {
                    $previous = $node;
                }
            }
            // Preserve an otherwise unattached comment above its preceding entry.
            $target = $previous ?? $statements[0];
            $target->setAttribute('comments', [...$target->getComments(), new \PhpParser\Comment($token->text)]);
        }
    }

    private function update(Node\Expr\Array_ $node, array $tree): void
    {
        $existing = [];
        foreach ($node->items as $item) {
            $key = $item->key->value;
            $existing[$key] = true;
            if (! array_key_exists($key, $tree)) {
                throw new RuntimeException('Publication cannot remove unrelated entries');
            }
            if (is_array($tree[$key]) && $item->value instanceof Node\Expr\Array_) {
                $this->update($item->value, $tree[$key]);
            } elseif ($item->value instanceof Node\Scalar\String_ && $item->value->value !== $tree[$key]) {
                $item->value = new Node\Scalar\String_($tree[$key], ['kind' => $item->value->getAttribute('kind')]);
            }
        }
        foreach ($tree as $key => $value) {
            if (! isset($existing[$key])) {
                $node->items[] = new Node\ArrayItem($this->expression($value), is_int($key) ? new Node\Scalar\Int_($key) : new Node\Scalar\String_($key));
            }
        }
    }

    private function expression(array|string $value): Node\Expr
    {
        if (is_string($value)) {
            return new Node\Scalar\String_($value);
        }
        $node = new Node\Expr\Array_([], ['kind' => Node\Expr\Array_::KIND_SHORT]);
        $this->update($node, $value);

        return $node;
    }
}
