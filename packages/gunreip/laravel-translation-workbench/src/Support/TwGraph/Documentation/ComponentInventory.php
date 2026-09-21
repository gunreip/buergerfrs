<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph\Documentation;

/** Static Blade call paths. Never renders inspected components or executes their PHP. */
final class ComponentInventory
{
    public const LAYERS = ['strang', 'paths', 'parts', 'segments', 'primitives'];
    public array $components = [];

    public function __construct(?string $directory = null)
    {
        $directory ??= dirname(__DIR__, 4).'/resources/views/components/ui/tw-graph';
        foreach (self::LAYERS as $layer) {
            if (!is_dir($directory.'/'.$layer)) {
                continue;
            }
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory.'/'.$layer, \FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $file) {
                if (!str_ends_with($file->getFilename(), '.blade.php')) {
                    continue;
                }
                $name = str_replace('/', '.', substr($file->getPathname(), strlen($directory) + 1, -10));
                $raw = file_get_contents($file->getPathname());
                $source = preg_replace_callback('/\{\{--.*?--\}\}|<!--.*?-->|@php\b.*?@endphp|<\?php\b.*?\?>/s', fn ($m) => str_repeat("\n", substr_count($m[0], "\n")), $raw);
                $calls = [];
                $conditions = [];
                preg_match_all('/@(if|elseif|else|endif|unless|endunless|isset|endisset|empty|endempty|foreach|endforeach|forelse|endforelse|for|endfor|while|endwhile)\b|<x-translation-workbench::ui\.tw-graph\.([\w.-]+)(?=[\s\/>])/', $source, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
                foreach ($matches as $match) {
                    $directive = $match[1][0] ?? '';
                    $line = substr_count(substr($source, 0, $match[0][1]), "\n") + 1;
                    if ($directive !== '') {
                        if (str_starts_with($directive, 'end')) {
                            array_pop($conditions);
                        } elseif (in_array($directive, ['else', 'elseif'], true) || ($directive === 'empty' && str_starts_with(end($conditions) ?: '', '@forelse'))) {
                            array_pop($conditions);
                            $conditions[] = '@'.$directive.' (line '.$line.')';
                        } else {
                            $conditions[] = '@'.$directive.' (line '.$line.')';
                        }
                    } else {
                        $target = $match[2][0];
                        if (in_array(explode('.', $target)[0], self::LAYERS, true)) {
                            $calls[$target.'|'.implode(',', $conditions)] = ['name' => $target, 'caller' => $name, 'line' => $line, 'conditions' => $conditions];
                        }
                    }
                }
                $this->components[$name] = ['name' => $name, 'layer' => $layer, 'archive' => str_contains($name, '._old.'), 'source' => $raw, 'path' => $file->getPathname(), 'calls' => array_values($calls)];
            }
        }
        ksort($this->components);
    }

    public function roots(bool $archive = false): array
    {
        $used = [];
        foreach ($this->components as $component) {
            if ($component['archive'] === $archive) {
                foreach ($component['calls'] as $call) {
                    $used[$call['name']] = true;
                }
            }
        }
        return array_keys(array_filter($this->components, fn ($c) => $c['archive'] === $archive && ($c['layer'] === 'strang' || !isset($used[$c['name']]))));
    }

    public function rows(bool $archive = false, string $root = ''): array
    {
        $roots = $this->roots($archive);
        if ($root !== '') {
            $roots = in_array($root, $roots, true) ? [$root] : [];
        }
        $rows = [];
        foreach ($roots as $name) {
            $this->walk(['name' => $name, 'conditions' => [], 'caller' => null, 'line' => 1], [], $rows);
        }
        return $rows;
    }

    private function walk(array $entry, array $path, array &$rows): void
    {
        $cycle = in_array($entry['name'], array_column($path, 'name'), true);
        $path[] = $entry;
        $component = $this->components[$entry['name']] ?? null;
        if ($cycle || $component === null || $component['calls'] === []) {
            $cells = array_fill_keys(self::LAYERS, []);
            foreach ($path as $step) {
                $cells[explode('.', $step['name'])[0]][] = $step;
            }
            $rows[] = ['cells' => $cells, 'chain' => implode(' → ', array_column($path, 'name')), 'note' => $cycle ? 'Recursive call' : ($component === null ? 'Unresolved component' : '')];
            return;
        }
        foreach ($component['calls'] as $call) {
            $this->walk($call, $path, $rows);
        }
    }
}
