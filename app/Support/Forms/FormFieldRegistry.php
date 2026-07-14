<?php

// app/Support/Forms/FormFieldRegistry.php

namespace App\Support\Forms;

class FormFieldRegistry
{
    public function forms(): array
    {
        return config('buergerfrs-forms', []);
    }

    public function form(string $form): array
    {
        $node = data_get($this->forms(), $form);

        if (! is_array($node)) {
            return [];
        }

        return [
            ...$node,
            'fields' => $this->collectFields($node, $form),
        ];
    }

    public function fields(string $form): array
    {
        return $this->form($form)['fields'] ?? [];
    }

    public function field(string $form, string $field): array
    {
        return $this->fields($form)[$field] ?? [];
    }

    public function hasField(string $form, string $field): bool
    {
        return array_key_exists($field, $this->fields($form));
    }

    public function isRequired(string $form, string $field): bool
    {
        return (bool) ($this->field($form, $field)['required'] ?? false);
    }

    public function isStatusRelevant(string $form, string $field): bool
    {
        return (bool) ($this->field($form, $field)['status_relevant'] ?? true);
    }

    public function tab(string $form, string $field): ?string
    {
        $tab = $this->field($form, $field)['tab'] ?? null;

        return is_string($tab) && $tab !== '' ? $tab : null;
    }

    public function requiredFields(string $form): array
    {
        return array_keys(array_filter(
            $this->fields($form),
            fn (array $field): bool => (bool) ($field['required'] ?? false),
        ));
    }

    public function fieldsForTab(string $form, string $tab): array
    {
        return array_filter(
            $this->fields($form),
            fn (array $field): bool => ($field['tab'] ?? null) === $tab,
        );
    }

    public function statusRelevantFieldsForTab(string $form, string $tab): array
    {
        return array_filter(
            $this->fieldsForTab($form, $tab),
            fn (array $field): bool => (bool) ($field['status_relevant'] ?? true),
        );
    }

    public function requiredFieldsForTab(string $form, string $tab): array
    {
        return array_keys(array_filter(
            $this->fieldsForTab($form, $tab),
            fn (array $field): bool => (bool) ($field['required'] ?? false),
        ));
    }

    public function tabs(string $form): array
    {
        $tabs = [];

        foreach ($this->fields($form) as $field) {
            $tab = $field['tab'] ?? null;

            if (! is_string($tab) || $tab === '') {
                continue;
            }

            if (! in_array($tab, $tabs, true)) {
                $tabs[] = $tab;
            }
        }

        return $tabs;
    }

    private function collectFields(array $node, string $form): array
    {
        $fields = [];
        $this->collectFieldsFromNode($node, $form, $fields);

        return $fields;
    }

    private function collectFieldsFromNode(array $node, string $namespace, array &$fields): void
    {
        foreach ($node['fields'] ?? [] as $field => $meta) {
            $fields[$field] = $this->normalizeFieldMeta($meta, $namespace);
        }

        foreach ($node as $key => $child) {
            if (in_array($key, ['scope', 'fields'], true) || ! is_array($child)) {
                continue;
            }

            $this->collectFieldsFromNode($child, "{$namespace}.{$key}", $fields);
        }
    }

    private function normalizeFieldMeta(mixed $meta, string $namespace): array
    {
        if (is_bool($meta)) {
            return [
                'required' => $meta,
                'status_relevant' => true,
                'tab' => $this->tabFromNamespace($namespace),
            ];
        }

        if (! is_array($meta)) {
            return [
                'required' => false,
                'status_relevant' => true,
                'tab' => $this->tabFromNamespace($namespace),
            ];
        }

        return [
            'required' => (bool) ($meta['required'] ?? false),
            'status_relevant' => (bool) ($meta['status_relevant'] ?? true),
            'tab' => $meta['tab'] ?? $this->tabFromNamespace($namespace),
        ];
    }

    private function tabFromNamespace(string $namespace): ?string
    {
        $parts = explode('.', $namespace);
        $sectionsIndex = array_search('sections', $parts, true);

        if ($sectionsIndex === false) {
            return null;
        }

        $tab = $parts[$sectionsIndex + 1] ?? null;

        return is_string($tab) && $tab !== '' ? $tab : null;
    }
}
