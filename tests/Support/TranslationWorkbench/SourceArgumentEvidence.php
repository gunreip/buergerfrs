<?php

namespace Tests\Support\TranslationWorkbench;

use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use RuntimeException;

/** Static evidence only: expressions are preserved, never evaluated. */
final class SourceArgumentEvidence
{
    public function option(string $expression): array
    {
        $code = '<?php '.$expression.';';
        $ast = (new ParserFactory)->createForHostVersion()->parse($code);
        $call = count($ast) === 1 ? ($ast[0]->expr ?? null) : null;
        if (! $call instanceof Node\Expr\StaticCall || ! $call->class instanceof Node\Name
            || $call->class->toString() !== 'Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption'
            || ! $call->name instanceof Node\Identifier || $call->name->toString() !== 'label'
            || count($call->args) < 3 || count($call->args) > 4) {
            throw new RuntimeException('Unsupported option call');
        }
        foreach ($call->args as $arg) {
            if (! $arg instanceof Node\Arg || $arg->name !== null || $arg->unpack) {
                throw new RuntimeException('Named or unpacked arguments require separate binding');
            }
        }
        if (! $call->args[0]->value instanceof Node\Scalar\String_) {
            throw new RuntimeException('Option owner is not static');
        }

        return ['owner' => $call->args[0]->value->value,
            'identity' => $this->describe($call->args[1]->value, $code),
            'fallback' => $this->describe($call->args[2]->value, $code),
            'locale' => isset($call->args[3]) ? $this->describe($call->args[3]->value, $code) : ['expression' => null, 'literal' => null, 'binding' => 'helper_default']];
    }

    /** Extract observed producer expressions, not an inferred complete runtime vocabulary. */
    public function producers(string $source, string $kind, string $name, ?string $field = null, int $argument = 0): array
    {
        $ast = (new ParserFactory)->createForHostVersion()->parse($source);
        $finder = new NodeFinder;
        $nodes = [];
        if ($kind === 'method_calls') {
            foreach ($finder->findInstanceOf($ast, Node\Expr\MethodCall::class) as $call) {
                if ($call->var instanceof Node\Expr\Variable && $call->var->name === 'this'
                    && $call->name instanceof Node\Identifier && $call->name->toString() === $name) {
                    $arg = $call->args[$argument] ?? null;
                    if (! $arg instanceof Node\Arg || $arg->name !== null || $arg->unpack) {
                        throw new RuntimeException('Unsupported producer argument binding');
                    }
                    $nodes[] = $arg->value;
                }
            }
        } elseif ($kind === 'method_array_field') {
            foreach ($finder->findInstanceOf($ast, Node\Stmt\ClassMethod::class) as $method) {
                if ($method->name->toString() !== $name) {
                    continue;
                }
                foreach ($finder->findInstanceOf($method->stmts ?? [], Node\ArrayItem::class) as $item) {
                    if ($item->key instanceof Node\Scalar\String_ && $item->key->value === $field) {
                        $nodes[] = $item->value;
                    }
                }
            }
        } elseif ($kind === 'constant_values') {
            foreach ($finder->findInstanceOf($ast, Node\Const_::class) as $constant) {
                if ($constant->name->toString() !== $name || ! $constant->value instanceof Node\Expr\Array_) {
                    continue;
                }
                foreach ($constant->value->items as $item) {
                    if ($item === null || $item->unpack) {
                        throw new RuntimeException('Unresolved constant array');
                    }
                    $nodes[] = $item->value;
                }
            }
        } else {
            throw new RuntimeException('Unknown producer query');
        }

        return array_map(fn ($node) => [...$this->describe($node, $source), 'line' => $node->getStartLine()], $nodes);
    }

    private function describe(Node\Expr $node, string $source): array
    {
        return ['expression' => substr($source, $node->getStartFilePos(), $node->getEndFilePos() - $node->getStartFilePos() + 1),
            'literal' => $node instanceof Node\Scalar\String_ ? $node->value : null,
            'binding' => $node instanceof Node\Scalar\String_ ? 'literal' : 'runtime_expression'];
    }
}
