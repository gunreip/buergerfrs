<?php

namespace Tests\Support\TranslationWorkbench;

use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use RuntimeException;

/** Captures a specific authored array, never executes PHP or asserts runtime completeness. */
final class StaticOptionListEvidence
{
    public function property(string $source, string $class, string $property, array $constantSources = []): array
    {
        $finder = new NodeFinder;
        $classes = $finder->find($this->parse($source), fn ($node) => $node instanceof Node\Stmt\Class_ && (string) $node->namespacedName === $class);
        if (count($classes) !== 1) {
            throw new RuntimeException('Expected one exact producer class');
        }
        $defaults = [];
        foreach (array_values($classes)[0]->getProperties() as $declaration) {
            foreach ($declaration->props as $item) {
                if ($item->name->toString() === $property) {
                    $defaults[] = $item->default;
                }
            }
        }
        if (count($defaults) !== 1 || ! $defaults[0] instanceof Node\Expr\Array_) {
            throw new RuntimeException('Expected one explicit array default');
        }

        return $this->describe($source, $defaults[0], $this->constants($constantSources));
    }

    public function config(string $source, array $path): array
    {
        $returns = array_values(array_filter($this->parse($source), fn ($node) => $node instanceof Node\Stmt\Return_));
        if (count($returns) !== 1) {
            throw new RuntimeException('Expected one top-level config return');
        }
        $node = $returns[0]->expr;
        foreach ($path as $segment) {
            if (! $node instanceof Node\Expr\Array_) {
                throw new RuntimeException('Config path is not a static array');
            }
            $matches = [];
            foreach ($node->items as $item) {
                if ($item === null || $item->unpack || ! $item->key instanceof Node\Scalar\String_) {
                    throw new RuntimeException('Ambiguous config path');
                }
                if ($item->key->value === $segment) {
                    $matches[] = $item->value;
                }
            }
            if (count($matches) !== 1) {
                throw new RuntimeException('Missing or duplicate config key');
            }
            $node = $matches[0];
        }
        if (! $node instanceof Node\Expr\Array_) {
            throw new RuntimeException('Expected option array');
        }

        return $this->describe($source, $node);
    }

    private function parse(string $source): array
    {
        $traverser = new NodeTraverser(new NameResolver);

        return $traverser->traverse((new ParserFactory)->createForHostVersion()->parse($source) ?? []);
    }

    /** Only explicit public string constants in unconditional classes; no autoload or evaluation. */
    private function constants(array $sources): array
    {
        $index = [];
        $seen = [];
        foreach ($sources as $path => $source) {
            $nodes = [];
            foreach ($this->parse($source) as $node) {
                array_push($nodes, ...($node instanceof Node\Stmt\Namespace_ ? $node->stmts : [$node]));
            }
            foreach ($nodes as $class) {
                if (! $class instanceof Node\Stmt\Class_ || $class->name === null) {
                    continue;
                }
                $className = strtolower((string) $class->namespacedName);
                if (isset($seen[$className])) {
                    throw new RuntimeException('Ambiguous constant producer class');
                }
                $seen[$className] = true;
                foreach ($class->getConstants() as $declaration) {
                    foreach ($declaration->consts as $constant) {
                        $id = $className.'::'.$constant->name->toString();
                        if (array_key_exists($id, $index)) {
                            throw new RuntimeException('Duplicate constant declaration');
                        }
                        $index[$id] = $declaration->isPublic() && $constant->value instanceof Node\Scalar\String_
                            ? ['value' => $constant->value->value, 'path' => $path, 'source_sha256' => hash('sha256', $source),
                                'class' => (string) $class->namespacedName, 'constant' => $constant->name->toString(), 'line' => $constant->getStartLine()]
                            : null;
                    }
                }
            }
        }

        return $index;
    }

    private function describe(string $source, Node\Expr\Array_ $array, array $constants = []): array
    {
        $entries = [];
        $keys = [];
        $complete = true;
        foreach ($array->items as $item) {
            if ($item === null) {
                throw new RuntimeException('Missing array item');
            }
            $dependency = null;
            if ($item->key instanceof Node\Expr\ClassConstFetch && $item->key->class instanceof Node\Name\FullyQualified
                && $item->key->name instanceof Node\Identifier) {
                $dependency = $constants[strtolower($item->key->class->toString()).'::'.$item->key->name->toString()] ?? null;
            }
            $literal = ! $item->unpack && ! $item->byRef && ($item->key instanceof Node\Scalar\String_ || $dependency !== null) && $item->value instanceof Node\Scalar\String_;
            $key = $literal ? ($dependency['value'] ?? $item->key->value) : null;
            if ($literal && in_array($key, $keys, true)) {
                throw new RuntimeException('Duplicate option key');
            }
            $keys[] = $key;
            $complete = $complete && $literal;
            $entries[] = ['key' => $key, 'value' => $literal ? $item->value->value : null,
                'state' => $literal ? ($dependency !== null ? 'constant_key_pair' : 'literal_pair') : 'requires_dependency_evidence',
                'key_dependency' => $dependency, 'line' => $item->getStartLine(),
                'expression' => substr($source, $item->getStartFilePos(), $item->getEndFilePos() - $item->getStartFilePos() + 1)];
        }

        return ['source_sha256' => hash('sha256', $source), 'line' => $array->getStartLine(),
            'complete_static_default' => $complete, 'runtime_exhaustiveness_proven' => false, 'entries' => $entries];
    }
}
