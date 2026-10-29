<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Scanner\DiscoveredTranslation;
use Gunreip\TranslationWorkbench\Scanner\SuggestedKeyFactory;
use Gunreip\TranslationWorkbench\Support\CopiedSourceContext;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\ParserFactory;
use RuntimeException;

/** Source capture adapter. No persistence and no evaluation of scanned PHP. */
final class CopiedSourceCapture
{
    private ?CopiedSourceContext $contexts = null;

    public function capture(DiscoveredTranslation $item, string $bytes, string $expectedHash, array $langValues): array
    {
        if (hash('sha256', $bytes) !== $expectedHash) {
            throw new RuntimeException('Source hash changed');
        }
        $offset = $item->meta['source_offset'] ?? null;
        if (! is_int($offset) || $offset < 0 || $offset >= strlen($bytes)) {
            throw new RuntimeException('Missing exact source offset');
        }
        $line = substr_count(substr($bytes, 0, $offset), "\n") + 1;
        $newline = strrpos(substr($bytes, 0, $offset), "\n");
        $column = $offset - ($newline === false ? -1 : $newline);
        if ($line !== $item->sourceLine || $column !== $item->sourceColumn) {
            throw new RuntimeException('Scanner position differs from bytes');
        }
        $raw = $this->expression(substr($bytes, $offset));
        $row = ['source_path' => $item->sourcePath, 'source_sha256' => $expectedHash,
            'source_line' => $line, 'source_column' => $column, 'source_offset' => $offset,
            'column_unit' => 'bytes', 'raw_expression' => $raw,
            'raw_source_code_literal' => null, 'raw_literal' => null,
            'suggested_literal' => null, 'suggested_key' => (new SuggestedKeyFactory)->forDynamicExpressionAtSource($raw, $item->sourcePath)->key, 'found_translation_key' => null,
            'scanner_kind' => $item->kind, 'status' => 'pending_review', 'reason' => null,
            'provenance' => ['source' => 'copied_source', 'scanner' => $item->meta['scanner'] ?? null],
        ];
        $context = ($this->contexts ??= new CopiedSourceContext)->check($item->sourcePath, $bytes, $offset);
        $row['source_context'] = $context;
        if ($context['state'] !== 'confirmed_call') {
            $row['status'] = str_starts_with($context['state'], 'excluded_') ? $context['state'] : 'pending_review';
            $row['reason'] = $context['state'] === 'excluded_comment' ? 'commented_source' : 'context_'.$context['state'];

            return $row;
        }
        if ($raw === null) {
            $row['reason'] = 'incomplete_expression';

            return $row;
        }
        // Parse only the captured expression. Blade directives are syntactic wrappers.
        $parseable = preg_replace('/^@(lang|js)\s*\(/', '__(', $raw);
        try {
            $stmts = (new ParserFactory)->createForHostVersion()->parse('<?php '.$parseable.';');
            $expr = count($stmts ?? []) === 1 && $stmts[0] instanceof Node\Stmt\Expression ? $stmts[0]->expr : null;
            $arg = ($expr instanceof Node\Expr\FuncCall || $expr instanceof Node\Expr\StaticCall) ? ($expr->args[0] ?? null) : null;
        } catch (Error) {
            $arg = null;
        }
        if (! $arg instanceof Node\Arg || ! $arg->value instanceof Node\Scalar\String_ || $arg->unpack) {
            $row['reason'] = 'dynamic_or_nonliteral_argument';

            return $row;
        }
        $value = $arg->value->value;
        $row['raw_source_code_literal'] = $value;
        $row['suggested_key'] = $item->kind === 'literal'
            ? (new SuggestedKeyFactory)->forLiteral($value, $item->sourcePath)->key
            : (new SuggestedKeyFactory)->forExistingKeyAtSource($value, $item->sourcePath)->key;
        if ($item->kind === 'literal') {
            $row['raw_literal'] = $value;
            $row['reason'] = 'literal_candidate';
        } elseif ($item->kind === 'key') {
            $row['found_translation_key'] = $value;
            if (isset($langValues[$value])) {
                $row['suggested_literal'] = $langValues[$value]['value'];
                $row['provenance']['suggested_literal'] = $langValues[$value]['provenance'];
                $row['reason'] = 'key_with_exact_lang_evidence';
            } else {
                $row['reason'] = 'key_like_argument_without_lang_evidence';
            }
        } else {
            $row['reason'] = 'special_call_requires_context';
            if ($expr instanceof Node\Expr\StaticCall && $expr->class instanceof Node\Name
                && $expr->class->toString() === 'Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption'
                && $expr->name instanceof Node\Identifier && $expr->name->toString() === 'label'
                && count($expr->args) >= 3 && count($expr->args) <= 4
                && ! array_filter($expr->args, fn ($argument) => ! $argument instanceof Node\Arg || $argument->unpack || $argument->name !== null)) {
                // Preserve authored arguments; never execute producers or turn an owner into a scalar key.
                $argumentSource = static fn (Node\Arg $argument): string => substr(
                    $parseable, $argument->value->getStartFilePos() - 6,
                    $argument->value->getEndFilePos() - $argument->value->getStartFilePos() + 1,
                );
                $row['option_source'] = [
                    'owner_key' => $value,
                    'value_expression' => $argumentSource($expr->args[1]),
                    'fallback_expression' => $argumentSource($expr->args[2]),
                    'locale_expression' => isset($expr->args[3]) ? $argumentSource($expr->args[3]) : null,
                    'fallback_literal' => $expr->args[2]->value instanceof Node\Scalar\String_ ? $expr->args[2]->value->value : null,
                    'state' => 'requires_option_value_evidence',
                ];
            }
        }

        // Confirmed source context is not an assignment, approval or active database capture.
        return $row;
    }

    private function expression(string $tail): ?string
    {
        $tokens = token_get_all('<?php '.$tail);
        array_shift($tokens);
        $depth = 0;
        $opened = false;
        $text = '';
        foreach ($tokens as $token) {
            $part = is_array($token) ? $token[1] : $token;
            $text .= $part;
            if ($token === '(') {
                $opened = true;
                $depth++;
            } elseif ($token === ')' && $opened && --$depth === 0) {
                return $text;
            }
        }

        return null;
    }
}
