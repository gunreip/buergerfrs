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
        $start = '/^[\t ]*' . preg_quote('{{-- ' . $marker . ':start --}}', '/') . '[\t ]*$/m';
        $end = '/^[\t ]*' . preg_quote('{{-- ' . $marker . ':end --}}', '/') . '[\t ]*$/m';

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
}
