<?php

namespace Gunreip\TranslationWorkbench\Generation;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use RuntimeException;

/** Preserve comments and formatting around unchanged translation entries. Never execute PHP. */
final class TranslationFileEditor
{
    public function render(?string $original, array $tree): string
    {
        if ($original === null) {
            return "<?php\n\nreturn ".var_export($tree, true).";\n";
        }
        $reader = new BaselineValueReader;
        if ($reader->treeFromBytes($original, true) === $tree) {
            return $original;
        }
        $parser = (new ParserFactory)->createForHostVersion();
        $old = $parser->parse($original);
        $tokens = $parser->getTokens();
        $new = (new NodeTraverser(new CloningVisitor))->traverse($old);
        foreach ($new as $statement) {
            if ($statement instanceof Node\Stmt\Return_) {
                $this->update($statement->expr, $tree);
            }
        }
        $bytes = (new Standard)->printFormatPreserving($new, $old, $tokens);
        if ($reader->treeFromBytes($bytes, true) !== $tree) {
            throw new RuntimeException('Rendered translation file does not match the planned values');
        }

        return $bytes;
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
