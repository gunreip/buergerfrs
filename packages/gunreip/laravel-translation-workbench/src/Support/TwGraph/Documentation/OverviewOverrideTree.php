<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\Documentation;

/** Author-facing hierarchy. Render paths are an internal detail, never override aliases. */
final class OverviewOverrideTree
{
    private array $schema = [];

    private array $targets = [];

    private array $nodes = [];

    public function __construct(array $base)
    {
        $canvas = $base['canvas'];
        unset($canvas['graphId']);
        $this->map(['global', 'canvas'], $canvas, ['canvas']);
        $root = $base['root'];
        unset($root['id']);
        $this->map(['global', 'root'], $root, ['root']);
        $this->map(['global', 'labelDefaults'], $base['tabLayout'], ['tabLayout']);

        // Derive ownership from the actual tab attachment, rather than duplicate extension numbers.
        foreach ($base['tabs'] as $name => $tab) {
            preg_match('/strang\.merge-(left|right)(?:\.extension\.(\d+))?\.node\.1$/', $tab['attachTo'], $match);
            $this->connection($base, $name, $match[1], isset($match[2]) ? (int) $match[2] : null);
            $label = $base['tabLayout'];
            $label['text'] = $tab['text'];
            $this->map([$name, 'global', 'label'], $label, ['tabs', $name]);
        }
        foreach ($base['merges'] as $side => $merge) {
            foreach ($merge['extensionEndLabels'] ?? [] as $index => $label) {
                $name = $index === 5 ? 'overview' : 'inventory';
                $this->connection($base, $name, $side, $index);
                $this->map([$name, 'global', 'label'], $label, ['merges', $side, 'extensionEndLabels', $index]);
            }
        }
        foreach (['canvas' => 'canvasTabs', 'primitives' => 'primitivesTabs', 'segments' => 'segmentsTabs', 'parts' => 'partsTabs', 'paths' => 'pathsTabs', 'strang-trunk' => 'strangTrunkTabs', 'strang-merge' => 'strangMergeTabs', 'strang-branch' => 'strangBranchTabs', 'strang-rekey' => 'strangRekeyTabs', 'flow' => 'flowTabs', 'deep-reference' => 'deepReference'] as $name => $treeKey) {
            $tree = $base[$treeKey];
            $this->nodes[json_encode([$name])] = true;
            $shared = array_replace($tree['levels']['subtabs'], $tree['levels']['subsubtabs']);
            foreach ([$name => [$name], 'children' => [$name, 'children']] as $scope => $scopePath) {
                $this->nodes[json_encode($scopePath)] = true;
                foreach ($shared as $field => $value) {
                    // Root color already controls its connection; keep that target.
                    if ($scope === $name && in_array($field, ['color', 'label'], true)) {
                        continue;
                    }
                    $destination = $scope === $name && in_array($field, ['width', 'align', 'direction', 'nodeEnd', 'nodeEndDot'], true)
                        ? ['tabs', $name, $field]
                        : [$treeKey, 'levels', array_key_exists($field, $tree['levels']['subtabs']) ? 'subtabs' : 'subsubtabs', $field];
                    $this->map([...$scopePath, 'global', $field], $value, $destination);
                }
            }
            foreach ($tree['children'] as $key => $group) {
                $path = [$name, 'children', $key];
                $target = [$treeKey, 'children', $key];
                $layout = OverviewEntryLayout::group($tree, $group);
                unset($layout['nodes']);
                $layout['text'] = $group['text'];
                $this->nodes[json_encode($path)] = true;
                $this->map([...$path, 'global'], $layout, $target);
                $this->map([...$path, 'global', 'label', 'text'], $group['text'], [...$target, 'text']);
                foreach (['width', 'align'] as $field) {
                    $this->map([...$path, 'global', $field], $layout['label'][$field], [...$target, 'label', $field]);
                }
                $nodes = $tree['levels']['subsubtabs'];
                unset($nodes['endCap']);
                $nodes['color'] = '';
                $nodes['direction'] = $layout['direction'];
                $this->nodes[json_encode([...$path, 'children'])] = 'connector';
                $this->map([...$path, 'children', 'global'], $nodes, [...$target, 'nodes']);
                foreach (['width', 'align', 'side'] as $field) {
                    $this->map([...$path, 'children', 'global', 'label', $field], $nodes[$field], [...$target, 'nodes', $field]);
                }
                foreach ($nodes as $field => $value) {
                    if (! array_key_exists($field, $layout) && ! in_array($field, ['width', 'align'], true)) {
                        $this->map([...$path, 'global', $field], $value, [...$target, 'nodes', $field]);
                    }
                }
                foreach ($group['children'] as $childKey => $child) {
                    $leaf = OverviewEntryLayout::node($nodes, $layout, $child);
                    $leaf['text'] = $child['text'];
                    $this->nodes[json_encode([...$path, 'children', $childKey])] = 'connector';
                    $this->map([...$path, 'children', $childKey, 'global', 'label', 'nodeEnd'], true, [...$target, 'children', $childKey, 'labelNodeEnd']);
                    $this->map([...$path, 'children', $childKey, 'global'], $leaf, [...$target, 'children', $childKey]);
                    foreach (['width', 'align', 'side', 'text'] as $field) {
                        $this->map([...$path, 'children', $childKey, 'global', 'label', $field], $leaf[$field], [...$target, 'children', $childKey, $field]);
                    }
                }
            }
        }
        foreach (array_keys($this->schema) as $name) {
            if ($name !== 'global') {
                $this->nodes[json_encode([$name])] ??= true;
            }
        }
        $this->addLocalScopes();
    }

    /** Local props target only the current element, never descendant defaults. */
    private function addLocalScopes(): void
    {
        foreach ($this->targets as $encoded => $target) {
            $path = json_decode($encoded, true);
            $global = array_search('global', $path, true);
            if ($global === false || $global === 0 || $path[$global - 1] === 'children') {
                continue;
            }
            // These global-only settings describe lower levels, not this element.
            if (in_array('levels', $target, true) || in_array('nodes', $target, true)) {
                continue;
            }
            $prototype = $this->schema;
            foreach ($path as $key) {
                $prototype = $prototype[$key];
            }
            array_splice($path, $global, 1);
            $this->map($path, $prototype, $target);
        }
    }

    private function connection(array $base, string $name, string $side, ?int $index): void
    {
        $merge = $base['merges'][$side];
        $path = [$name, 'global'];
        $target = ['merges', $side];
        if ($index === null) {
            $this->map([...$path, 'connection', 'arcRadius'], '', [...$target, 'arcRadius']);
            $this->map([...$path, 'color'], $merge['color'], [...$target, 'color']);
            $this->map([...$path, 'connection', 'startLength'], $merge['startLength'], [...$target, 'startLength']);
            $this->map([...$path, 'connection', 'stemLength'], $merge['stemLengths'][1], [...$target, 'stemLengths', 1]);
            $this->map([...$path, 'connection', 'bridgeLength'], $merge['bridgeLength'], [...$target, 'bridgeLength']);
        } else {
            $this->map([...$path, 'color'], '', [...$target, 'extensionColors', $index]);
            $this->map([...$path, 'connection', 'stemLength'], $merge['extensionStemLength'], [...$target, 'extensionStemLengths', $index]);
            $this->map([...$path, 'connection', 'bridgeLength'], $merge['extensionBridgeLength'], [...$target, 'extensionBridgeContinuations', $index]);
            // Prototype only; absent values must retain the graph's radius fallback.
            $this->map([...$path, 'connection', 'arcRadius'], '', [...$target, 'extensionArcRadiuss', $index]);
        }
    }

    private function map(array $path, mixed $prototype, array $target): void
    {
        if (is_array($prototype)) {
            $cursor = &$this->schema;
            foreach ($path as $key) {
                $cursor = &$cursor[$key];
            }
            $cursor ??= [];
            unset($cursor);
            foreach ($prototype as $key => $value) {
                $this->map([...$path, $key], $value, [...$target, $key]);
            }

            return;
        }
        $cursor = &$this->schema;
        foreach ($path as $key) {
            $cursor = &$cursor[$key];
        }
        $cursor = $prototype;
        $this->targets[json_encode($path)] = $target;
    }

    public function schema(): array
    {
        return $this->schema;
    }

    /** Descend by level, independently of array order; explicit local values win. */
    private function inherit(mixed $value, array $schema, array $path, array $inherited = [], ?string $connectorSide = null): mixed
    {
        if (! is_array($value)) {
            return $value;
        }
        if (! isset($this->nodes[json_encode($path)])) {
            return $value;
        }
        $scope = $value;
        $scopeSchema = $schema;
        $value = $scope['global'] ?? [];
        if (! is_array($value)) {
            // Keep malformed global blocks intact for diagnostics at the authored path.
            $value = [];
        }
        $schema = $scopeSchema['global'];
        // Center is a branch placement, not a connector-label side. Preserve the
        // nearest applicable authored side when crossing into connector scopes.
        $localSide = is_array($value['label'] ?? null)
            ? ($value['label']['side'] ?? $value['side'] ?? null)
            : ($value['side'] ?? null);
        if (in_array($localSide, ['left', 'right'], true)) {
            $connectorSide = $localSide;
        }
        if ($this->nodes[json_encode($path)] === 'connector') {
            if (($inherited['side'] ?? null) === 'center') {
                $inherited['side'] = $connectorSide ?? $schema['side'];
            }
            if (($inherited['label']['side'] ?? null) === 'center') {
                $inherited['label']['side'] = $connectorSide ?? $schema['side'];
            }
        }
        $value = $this->normalizeLabel($value, $schema);
        $own = [];
        foreach ($value as $key => $item) {
            if (array_key_exists($key, $schema) && $key !== 'text') {
                // Type errors must be reported only where authored, never propagated.
                if (get_debug_type($item) === get_debug_type($schema[$key])) {
                    $own[$key] = is_array($item) ? $this->project($item, $schema[$key]) : $item;
                }
            }
        }
        $effective = array_replace_recursive($inherited, $own);
        // An entry's text is content, never a style to copy to descendants.
        unset($effective['label']['text']);
        if (isset($own['label']['text'])) {
            $value['label']['text'] = $own['label']['text'];
        }
        foreach ($effective as $key => $item) {
            if (array_key_exists($key, $schema)) {
                if (is_array($item) && is_array($schema[$key])) {
                    $item = $this->project($item, $schema[$key]);
                }
                if (! array_key_exists($key, $value)) {
                    $value[$key] = $item;
                } elseif (is_array($value[$key]) && is_array($item)) {
                    $value[$key] = array_replace_recursive($item, $value[$key]);
                }
            }
        }
        if (! array_key_exists('global', $scope) || is_array($scope['global'])) {
            $scope['global'] = $value;
        }
        foreach ($scopeSchema as $key => $prototype) {
            if (isset($this->nodes[json_encode([...$path, $key])])) {
                $scope[$key] = $this->inherit($scope[$key] ?? [], $prototype, [...$path, $key], $effective, $connectorSide);
            }
        }

        // Apply local values after global resolution; they never enter $effective.
        $localSchema = array_diff_key($scopeSchema, ['global' => true, 'children' => true]);
        $local = array_intersect_key($scope, $localSchema);
        if (end($path) !== 'children') {
            $local = $this->normalizeLabel($local, $localSchema);
        }
        $rest = array_diff_key($scope, ['global' => true, 'children' => true], $localSchema);

        return ['global' => $scope['global'], ...$local, ...$rest,
            ...(array_key_exists('children', $scope) ? ['children' => $scope['children']] : [])];
    }

    private function normalizeLabel(array $value, array $schema): array
    {
        foreach (['width', 'align', 'side', 'text'] as $field) {
            $labelValue = is_array($value['label'] ?? null) ? ($value['label'][$field] ?? null) : null;
            if (array_key_exists($field, $schema) && $labelValue !== null && get_debug_type($labelValue) === get_debug_type($schema[$field])) {
                $value[$field] = $labelValue;
            } elseif ((! array_key_exists('label', $value) || is_array($value['label'])) && isset($schema['label'][$field]) && array_key_exists($field, $value) && get_debug_type($value[$field]) === get_debug_type($schema['label'][$field])) {
                $value['label'][$field] = $value[$field];
            }
        }

        return $value;
    }

    private function project(array $value, array $schema): array
    {
        $result = [];
        foreach ($value as $key => $item) {
            if (! array_key_exists($key, $schema) || get_debug_type($item) !== get_debug_type($schema[$key])) {
                continue;
            }
            $result[$key] = is_array($item) && is_array($schema[$key]) ? $this->project($item, $schema[$key]) : $item;
        }

        return $result;
    }

    /** Validate authored paths and compile only supplied leaves; never materialize defaults. */
    public function compile(string $file, mixed $values): array
    {
        $issues = [];
        $compiled = [];
        $allowedRoot = $file === 'main-tabs.php' ? 'global' : pathinfo($file, PATHINFO_FILENAME);
        $visit = function (mixed $value, mixed $prototype, array $path) use (&$visit, &$issues, &$compiled, $file): void {
            if (get_debug_type($value) !== get_debug_type($prototype)) {
                $issues[] = ['file' => $file, 'path' => implode('.', $path), 'message' => __('Expected :type.', ['type' => get_debug_type($prototype)])];

                return;
            }
            if (is_array($value)) {
                foreach ($value as $key => $child) {
                    if (! array_key_exists($key, $prototype)) {
                        $issues[] = ['file' => $file, 'path' => implode('.', [...$path, $key]), 'message' => __('Unknown layout key.')];

                        continue;
                    }
                    $visit($child, $prototype[$key], [...$path, $key]);
                }

                return;
            }
            $parent = array_slice($path, 0, -1);
            $owner = end($parent) === 'label' ? array_slice($parent, 0, -1) : $parent;
            if (end($owner) === 'global') {
                array_pop($owner);
            }
            if (end($path) === 'side' && ($this->nodes[json_encode($owner)] ?? null) === 'connector'
                && ! in_array($value, ['left', 'right'], true)) {
                $issues[] = ['file' => $file, 'path' => implode('.', $path), 'message' => __('Connector labels require side left or right; center is available for branches.')];

                return;
            }
            $cursor = &$compiled;
            foreach ($this->targets[json_encode($path)] as $key) {
                $cursor = &$cursor[$key];
            }
            $cursor = $value;
        };
        if (! is_array($values)) {
            $issues[] = ['file' => $file, 'path' => '', 'message' => __('Expected array.')];
        } else {
            foreach ($values as $root => $value) {
                if ($root !== $allowedRoot || ! array_key_exists($root, $this->schema)) {
                    $issues[] = ['file' => $file, 'path' => (string) $root, 'message' => __('This file only accepts :section.', ['section' => $allowedRoot])];

                    continue;
                }
                $visit($this->inherit($value, $this->schema[$root], [$root]), $this->schema[$root], [$root]);
            }
        }

        return ['values' => $compiled, 'issues' => $issues];
    }
}
