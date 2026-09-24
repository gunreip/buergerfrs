<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph\Documentation;

use Illuminate\Support\Facades\View;
use LogicException;
use RuntimeException;

/** Raw, unescaped example code from one authored Blade view. */
final class ExampleSource
{
    public function __construct(
        private readonly string $source,
        private readonly string $name = 'source',
    ) {}

    public static function fromView(string $view): self
    {
        $source = file_get_contents(View::getFinder()->find($view));

        if ($source === false) {
            throw new RuntimeException("Unable to read documentation view [{$view}].");
        }

        return new self($source, $view);
    }

    public function example(string $marker): string
    {
        $source = str_replace(["\r\n", "\r"], "\n", $this->source);
        $start = '/^[\t ]*'.preg_quote('{{-- '.$marker.':start --}}', '/').'[\t ]*$/m';
        $end = '/^[\t ]*'.preg_quote('{{-- '.$marker.':end --}}', '/').'[\t ]*$/m';

        if (preg_match_all($start, $source, $starts, PREG_OFFSET_CAPTURE) !== 1
            || preg_match_all($end, $source, $ends, PREG_OFFSET_CAPTURE) !== 1) {
            throw new LogicException("Example [{$marker}] in [{$this->name}] requires exactly one start and end marker.");
        }

        $from = $starts[0][0][1] + strlen($starts[0][0][0]) + 1;
        $to = $ends[0][0][1];

        if ($to < $from) {
            throw new LogicException("Example [{$marker}] in [{$this->name}] has an end marker before its start marker.");
        }

        $lines = explode("\n", rtrim(substr($source, $from, $to - $from)));
        $content = array_filter($lines, static fn (string $line): bool => trim($line) !== '');

        if ($content === []) {
            return '';
        }

        $indent = min(array_map(
            static fn (string $line): int => strlen($line) - strlen(ltrim($line)),
            $content,
        ));

        return implode("\n", array_map(
            static fn (string $line): string => substr($line, $indent),
            $lines,
        ));
    }

    /**
     * Compare authored props, without evaluating Blade/PHP or copying their values.
     * Markers must contain exactly one opening tag of the requested component.
     * Identifier-only changes are excluded unless the caller opts in.
     */
    public function changedProps(string $marker, string $baseline, string $component, array $ignore = ['id', 'graph-id']): string
    {
        $current = $this->attributes($marker, $component);
        $original = $this->attributes($baseline, $component);
        $changes = [];

        foreach ($current as $name => $attribute) {
            if (! in_array(ltrim($name, ':'), $ignore, true) && ($original[$name] ?? null) !== $attribute) {
                $changes[] = $attribute;
            }
        }
        foreach (array_diff_key($original, $current) as $name => $attribute) {
            if (! in_array(ltrim($name, ':'), $ignore, true)) {
                $changes[] = '{{-- '.$name.' is omitted in this example; the component fallback applies. --}}';
            }
        }

        return implode("\n", $changes);
    }

    private function attributes(string $marker, string $component): array
    {
        $source = preg_replace('/\{\{--.*?--\}\}/s', '', $this->example($marker));
        // A quoted Blade prop may contain PHP arrays, arrows and greater-than signs.
        $quoted = '"[^"]*"|\'[^\']*\'';
        $pattern = '~<'.preg_quote($component, '~').'(?=[\\s/>])((?:'.$quoted.'|[^\'">])*)>~s';
        if (preg_match_all($pattern, $source, $matches) !== 1) {
            throw new LogicException("Example [{$marker}] in [{$this->name}] must contain exactly one [{$component}] component.");
        }

        $text = rtrim(trim($matches[1][0]), '/');
        $attributes = [];
        $offset = 0;
        $pattern = '~\\G\\s*([:@A-Za-z_][:@A-Za-z0-9_.-]*)(?:\\s*=\\s*('.$quoted.'))?~s';
        while ($offset < strlen(rtrim($text))) {
            if (! preg_match($pattern, $text, $match, 0, $offset)) {
                throw new LogicException("Unsupported attribute syntax in example [{$marker}] in [{$this->name}].");
            }
            $name = $match[1];
            if (array_key_exists($name, $attributes)) {
                throw new LogicException("Duplicate attribute [{$name}] in example [{$marker}] in [{$this->name}].");
            }
            $attributes[$name] = $name.(isset($match[2]) ? '='.$match[2] : '');
            $offset += strlen($match[0]);
        }

        return $attributes;
    }
}
