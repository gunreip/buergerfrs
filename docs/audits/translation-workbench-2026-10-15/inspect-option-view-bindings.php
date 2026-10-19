<?php

// Source-file inspection only. No provider invocation, scan synchronization or DB access.
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$history = json_decode(file_get_contents(__DIR__.'/historical-value-evidence.json'), true, flags: JSON_THROW_ON_ERROR);
$inspector = new Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchOptionViewBinding;
$rows = [];
foreach ($history['unassigned_entries'] as $group) {
    $references = [];
    foreach ($group['values'] as $value) {
        foreach (explode(', ', $value['observation']['source_reference']) as $source) {
            $references[$source] = true;
        }
    }
    $metadata = $group['values'][0]['observation']['meta'];
    $metadata = is_string($metadata) ? json_decode($metadata, true, flags: JSON_THROW_ON_ERROR) : $metadata;
    $variable = $metadata['options_variable'] ?? null;
    foreach (array_keys($references) as $source) {
        $rows[] = ['entry_id' => $group['entry']['id'], 'source' => $source, 'target' => $group['entry']['source_path'],
            ...$inspector->inspect($source, $group['entry']['source_path'], $variable)];
    }
}
file_put_contents(__DIR__.'/option-view-bindings.json', json_encode(['captured_at' => date(DATE_ATOM), 'bindings' => $rows], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
echo json_encode(array_count_values(array_column($rows, 'status'))).PHP_EOL;
