<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Support\CopiedSourceContext;
use PhpParser\Node;
use PhpParser\ParserFactory;
use RuntimeException;

/** Exact, read-only replacement plan. No broad literal matching or source evaluation. */
final class SourceReplacementPreview
{
    public function inspect(array $context, string $sourceLocale, string $root, string $languageRoot): array
    {
        $finding = $context['finding'];
        if ($finding['status'] !== 'active' || ! $finding['key_id']) {
            throw new RuntimeException('An active finding with an explicit key assignment is required.');
        }
        $key = $finding['full_name'];
        if (($finding['namespace'] ?? '') !== '' || ! preg_match('/^[A-Za-z_][A-Za-z0-9_-]*(\.[A-Za-z_][A-Za-z0-9_-]*){2,}$/D', $key)) {
            throw new RuntimeException('Source replacement requires a valid unnamespaced array key.');
        }
        $path = $this->path($root, $finding['path']);
        $bytes = file_get_contents($path);
        if ($bytes === false) {
            throw new RuntimeException('Source file cannot be read.');
        }
        $lines = explode("\n", $bytes);
        $line = (int) $finding['source_line'];
        $column = (int) $finding['source_column'];
        if ($line < 1 || $column < 1 || ! isset($lines[$line - 1]) || $column > strlen($lines[$line - 1]) + 1) {
            throw new RuntimeException('Source position changed. Run a fresh scan before continuing.');
        }
        $offset = array_sum(array_map('strlen', array_slice($lines, 0, $line - 1))) + $line - 1 + $column - 1;
        $before = $finding['raw_expression'];
        if ($before === '' || substr($bytes, $offset, strlen($before)) !== $before) {
            throw new RuntimeException('Source occurrence changed or moved. Run a fresh scan before continuing.');
        }
        $sourceContext = (new CopiedSourceContext)->check($finding['path'], $bytes, $offset);
        if ($sourceContext['state'] !== 'confirmed_call') {
            throw new RuntimeException('The selected occurrence is not a confirmed active translation call.');
        }
        $parseable = preg_replace('/^@lang\s*\(/', '__(', $before);
        $statements = (new ParserFactory)->createForHostVersion()->parse('<?php '.$parseable.';');
        $call = count($statements ?? []) === 1 && $statements[0] instanceof Node\Stmt\Expression ? $statements[0]->expr : null;
        $supported = $call instanceof Node\Expr\FuncCall && $call->name instanceof Node\Name
            && in_array($call->name->toString(), ['__', 'trans', 'trans_choice'], true);
        $supported = $supported || ($call instanceof Node\Expr\StaticCall && $call->class instanceof Node\Name
            && in_array($call->class->toString(), ['Lang', 'Illuminate\\Support\\Facades\\Lang'], true)
            && $call->name instanceof Node\Identifier && in_array($call->name->toString(), ['get', 'choice'], true));
        $argument = $supported ? ($call->args[0] ?? null) : null;
        if (! $argument instanceof Node\Arg || ! $argument->value instanceof Node\Scalar\String_ || $argument->unpack || $argument->name !== null) {
            throw new RuntimeException('Only supported calls with a static first string argument can be previewed. Dynamic values require a separate decision.');
        }
        $start = $argument->value->getStartFilePos() - 6 + strlen($before) - strlen($parseable);
        $length = $argument->value->getEndFilePos() - $argument->value->getStartFilePos() + 1;
        $after = substr_replace($before, "'".$key."'", $start, $length);
        $already = $argument->value->value === $key;
        if ($already) {
            $after = $before;
        }
        $result = ['finding' => (int) $finding['id'], 'key' => $key, 'key_id' => (int) $finding['key_id'], 'link_id' => (int) $finding['link_id'],
            'path' => $finding['path'], 'line' => $line, 'column' => $column, 'source_sha256' => hash('sha256', $bytes),
            'edit' => ['offset' => $offset + $start, 'length' => $length, 'replacement' => "'".$key."'"],
            'before' => $before, 'after' => $after, 'source_locale' => $sourceLocale, 'already_set' => $already,
            'ready' => false, 'blocker' => null, 'language_path' => null, 'language_sha256' => null, 'published_value' => null];
        if ($already) {
            $result['blocker'] = 'The selected source occurrence already uses its assigned key.';
        } elseif (! ($context['review']['approved'] ?? false) || ($context['review']['context']['locale'] ?? null) !== $sourceLocale) {
            $result['blocker'] = 'Save and approve a revision in the source language before replacing the literal.';
        } else {
            if (! preg_match('/^[A-Za-z0-9_-]+$/D', $sourceLocale)) {
                throw new RuntimeException('Invalid source locale.');
            }
            [$group, $nested] = explode('.', $key, 2);
            $relative = $sourceLocale.'/'.$group.'.php';
            $result['language_path'] = $relative;
            try {
                $languagePath = $this->path($languageRoot, $relative);
                $languageBytes = file_get_contents($languagePath);
                if ($languageBytes === false) {
                    throw new RuntimeException('Language file cannot be read.');
                }
                $value = (new BaselineValueReader)->treeFromBytes($languageBytes, true);
                foreach (explode('.', $nested) as $part) {
                    $value = is_array($value) && array_key_exists($part, $value) ? $value[$part] : null;
                }
                $result['language_sha256'] = hash('sha256', $languageBytes);
                $result['published_value'] = is_string($value) ? $value : null;
                if (! is_string($value) || $value !== $context['revision']['value']) {
                    $result['blocker'] = 'Publish the approved source-language revision first. The file does not contain that value.';
                } else {
                    $result['ready'] = true;
                }
            } catch (RuntimeException $error) {
                $result['blocker'] = $error->getMessage();
            }
        }
        clearstatcache(true, $path);
        if (hash_file('sha256', $path) !== $result['source_sha256']) {
            throw new RuntimeException('Source file changed during preview. Reopen the preview.');
        }
        if ($result['language_sha256'] !== null && hash_file('sha256', $languagePath) !== $result['language_sha256']) {
            throw new RuntimeException('Language file changed during preview. Reopen the preview.');
        }
        $result['fingerprint'] = hash('sha256', json_encode($result, JSON_THROW_ON_ERROR));

        return $result;
    }

    public function path(string $root, string $relative): string
    {
        if ($relative === '' || str_contains($relative, "\0") || str_starts_with($relative, '/') || str_contains($relative, '\\')
            || array_intersect(explode('/', $relative), ['', '.', '..']) || ! is_dir($root) || is_link($root)) {
            throw new RuntimeException('Unsafe source or language path.');
        }
        $path = realpath($root);
        foreach (explode('/', $relative) as $part) {
            $path .= '/'.$part;
            if (is_link($path)) {
                throw new RuntimeException('Symlink paths are not supported for source replacement.');
            }
        }
        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException('Source or language file is missing or unreadable.');
        }

        return $path;
    }
}
