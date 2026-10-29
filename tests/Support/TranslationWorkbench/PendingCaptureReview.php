<?php

namespace Tests\Support\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Support\CopiedSourceContext;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\ParserFactory;
use RuntimeException;

/** Classifies evidence only; does not resolve or activate findings. */
final class PendingCaptureReview
{
    public function review(array $row, string $source, array $language): array
    {
        if (hash('sha256', $source) !== $row['source_sha256']) {
            throw new RuntimeException('Source evidence changed');
        }
        $raw = $row['raw_expression'];
        $isJs = str_starts_with($raw, '@js(');
        $expression = preg_replace('/^@js\(/', '__(', $raw);
        $ast = (new ParserFactory)->createForHostVersion()->parse('<?php '.$expression.';');
        $call = $ast[0]->expr ?? null;
        $argument = $call->args[0]->value ?? null;
        $result = ['capture_id' => $row['capture_id'], 'source_path' => $row['source_path'], 'source_line' => $row['source_line'],
            'source_sha256' => $row['source_sha256'], 'raw_expression' => $raw, 'original_reason' => $row['reason'],
            'category' => 'runtime_translation_argument', 'decision' => null, 'automatic_import' => false, 'evidence' => []];
        $default = $argument instanceof Node\Expr\Variable && is_string($argument->name)
            ? ($this->defaults($source, $row['source_path'])[$argument->name] ?? null) : null;
        if ($call instanceof Node\Expr\StaticCall && $call->class instanceof Node\Name
            && $call->class->toString() === 'Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption'
            && $call->name instanceof Node\Identifier && $call->name->toString() === 'label' && $argument instanceof Node\Scalar\String_) {
            $result['category'] = 'explicit_option_owner';
            $result['evidence']['owner_key'] = $argument->value;
            $result['evidence']['exact_option_values'] = array_values(array_filter($language, fn ($entry) => str_starts_with($entry['key'], $argument->value.'.options.')));
            $result['next_action'] = 'Keep owner, runtime option identity and fallback separate. Capture an option source; do not import the owner as a scalar translation.';
        } elseif ($isJs) {
            $result['category'] = $default !== null && $default['kind'] === 'translated_argument' ? 'serialized_translated_prop' : 'serialization_only';
            $result['evidence']['prop_default'] = $default;
            $result['next_action'] = 'Do not create another translation task for serialization. A translated prop default has its own source call; overrides require their own evidence.';
        } elseif ($row['reason'] === 'key_like_argument_without_lang_evidence') {
            $result['category'] = 'key_or_literal_review';
            $result['evidence']['argument'] = $argument instanceof Node\Scalar\String_ ? $argument->value : null;
            $result['next_action'] = 'Review literal versus translation key and exact source-locale lookup. Do not guess a replacement key or derive a suggested literal from its name.';
        } elseif ($default !== null) {
            $result['category'] = 'overridable_prop_default';
            $result['evidence']['prop_default'] = $default;
            $result['next_action'] = 'Default is known, but callers may override it. Preserve the dynamic call and default provenance separately; do not treat the default as every runtime value.';
        } else {
            $result['next_action'] = 'Trace the supplied value or finite producer vocabulary. Keep rawLiteral NULL while the actual value is not established.';
        }

        return $result;
    }

    private function defaults(string $source, string $path): array
    {
        $defaults = [];
        preg_match_all('/@props\s*\(/', $source, $matches, PREG_OFFSET_CAPTURE);
        foreach ($matches[0] as [$start, $offset]) {
            $context = (new CopiedSourceContext)->check($path, $source, $offset + strlen($start));
            if ($context['context'] !== 'blade_directive_props') {
                continue;
            }
            $tail = substr($source, $offset + strlen($start));
            $tokens = token_get_all('<?php '.$tail);
            array_shift($tokens);
            $depth = 0;
            $code = '';
            foreach ($tokens as $token) {
                if ($token === ')' && $depth === 0) {
                    break;
                }
                if ($token === '(') {
                    $depth++;
                } elseif ($token === ')') {
                    $depth--;
                }
                $code .= is_array($token) ? $token[1] : $token;
            }
            try {
                $ast = (new ParserFactory)->createForHostVersion()->parse('<?php '.$code.';');
            } catch (Error) {
                continue;
            }
            $array = $ast[0]->expr ?? null;
            if (! $array instanceof Node\Expr\Array_) {
                continue;
            }
            foreach ($array->items as $item) {
                if (! $item?->key instanceof Node\Scalar\String_ || $item->unpack) {
                    continue;
                }
                $value = $item->value;
                if ($value instanceof Node\Scalar\String_) {
                    $defaults[$item->key->value] = ['kind' => 'literal', 'value' => $value->value, 'props_offset' => $offset];
                } elseif ($value instanceof Node\Expr\FuncCall && $value->name instanceof Node\Name && $value->name->toString() === '__'
                    && ($value->args[0]->value ?? null) instanceof Node\Scalar\String_) {
                    $defaults[$item->key->value] = ['kind' => 'translated_argument', 'value' => $value->args[0]->value->value, 'props_offset' => $offset];
                }
            }
        }

        return $defaults;
    }
}
