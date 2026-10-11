<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

/** Explicit provenance from geometry producers, scoped to one canvas. */
final class CalculatedLengths
{
    private array $lengths = [];

    public function record(string $id, string $component, string $owner, string $property, array $inputs, string $reason, ?string $default = null): void
    {
        array_walk_recursive($inputs, static function (&$input): void {
            if (is_string($input) && str_contains($input, 'calc(')) {
                $resolved = BoundsRegistry::evaluateRemExpression($input);
                $input = $resolved === null ? \Illuminate\Support\Str::limit($input, 160) : round($resolved, 4).'rem';
            }
        });
        $default ??= $this->lengths[$id]['default'] ?? null;
        $this->lengths[$id] = compact('component', 'owner', 'property', 'inputs', 'reason', 'default') + ['kind' => 'calculated'];
    }

    public function recordProp(string $id, string $component, string $owner, string $property, bool $explicit, ?string $default = null): void
    {
        // A child may supply the default without replacing the parent's calculation.
        if (isset($this->lengths[$id]) && $default !== null) {
            $this->lengths[$id]['default'] ??= $default;
        }
        // Preserve provenance already supplied by a parent producer.
        $this->lengths[$id] ??= [
            'component' => $component, 'owner' => $owner, 'property' => $property,
            'default' => $default, 'inputs' => [], 'kind' => $explicit ? 'prop' : 'default',
            'reason' => $explicit ? 'Length supplied by the component prop.' : 'Length uses the component default.',
        ];
    }

    public function get(string $id): ?array
    {
        return $this->lengths[$id] ?? null;
    }
}
