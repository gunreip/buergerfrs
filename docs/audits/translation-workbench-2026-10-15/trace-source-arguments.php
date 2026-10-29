<?php

use Tests\Support\TranslationWorkbench\SourceArgumentEvidence;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$read = static fn ($path) => json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
$context = $read(__DIR__.'/copied-source-context-2026-10-19.json');
$manifestPath = $root.'/'.$context['manifest'];
if (hash_file('sha256', $manifestPath) !== $context['manifest_sha256']) {
    throw new RuntimeException('Capture manifest changed');
}
$manifest = $read($manifestPath);
$backup = dirname($manifestPath, 2);
$backupManifest = $read($backup.'/manifest.json');
if (hash_file('sha256', $backup.'/manifest.json') !== $manifest['backup_manifest_sha256']) {
    throw new RuntimeException('Backup changed');
}
$reviewPath = __DIR__.'/pending-capture-review-2026-10-19.json';
$review = $read($reviewPath);
if ($review['capture_manifest_sha256'] !== $context['manifest_sha256'] || $review['candidate_file_sha256'] !== $context['candidate_file_sha256']) {
    throw new RuntimeException('Review mismatch');
}
$readSource = static function ($path) use ($backup, $backupManifest): string {
    $source = file_get_contents($backup.'/source/'.$path);
    if (hash('sha256', $source) !== $backupManifest['source_files'][$path]['sha256']) {
        throw new RuntimeException('Source drift: '.$path);
    }

    return $source;
};
$prefix = 'packages/gunreip/laravel-translation-workbench/src/';
$helper = $prefix.'Foundation/TranslationWorkbenchOption.php';
$extractor = new SourceArgumentEvidence;
$definitions = [
    'settings_summary' => [$prefix.'Livewire/TranslationWorkbenchSettings.php', 'method_array_field', 'configSummary', 'label', 0],
    'settings_title' => [$prefix.'Livewire/TranslationWorkbenchSettings.php', 'method_calls', 'settingsGroup', null, 0],
    'settings_description' => [$prefix.'Livewire/TranslationWorkbenchSettings.php', 'method_calls', 'settingsGroup', null, 1],
    'context_badges' => [$prefix.'Livewire/TranslationWorkbenchEntries.php', 'method_array_field', 'lastEditedTranslationContextBadges', 'label', 0],
    'critical_datasets' => [$prefix.'Livewire/TwGraphDataDrivenDatasets.php', 'constant_values', 'CRITICAL_DATASETS', null, 0],
    'publication_messages' => [$prefix.'Foundation/TranslationWorkbenchPublicationRunner.php', 'method_calls', 'conflict', null, 0],
];
$producers = [];
foreach ($definitions as $name => [$path, $kind, $member, $field, $argument]) {
    $source = $readSource($path);
    $values = $extractor->producers($source, $kind, $member, $field, $argument);
    if ($values === []) {
        throw new RuntimeException('Producer query returned no evidence');
    }
    $producers[$name] = ['path' => $path, 'sha256' => hash('sha256', $source), 'member' => $member, 'observations' => $values,
        'runtime_exhaustiveness_proven' => false];
}
$items = [];
$counts = [];
foreach ($review['items'] as $item) {
    if (in_array($item['category'], ['serialization_only', 'serialized_translated_prop'], true)) {
        continue;
    }
    $source = $readSource($item['source_path']);
    if (hash('sha256', $source) !== $item['source_sha256']) {
        throw new RuntimeException('Review source mismatch');
    }
    $result = ['capture_id' => $item['capture_id'], 'source_path' => $item['source_path'], 'source_sha256' => $item['source_sha256'],
        'source_line' => $item['source_line'], 'raw_expression' => $item['raw_expression'], 'decision' => null, 'finding_fields_changed' => false];
    if ($item['category'] === 'explicit_option_owner') {
        $result['evidence_kind'] = 'option_arguments';
        $result['arguments'] = $extractor->option($item['raw_expression']);
        if ($result['arguments']['owner'] !== $item['evidence']['owner_key']) {
            throw new RuntimeException('Owner mismatch');
        }
        $result['helper'] = ['path' => $helper, 'sha256' => hash('sha256', $readSource($helper))];
    } elseif ($item['category'] === 'overridable_prop_default') {
        $result['evidence_kind'] = 'overridable_default';
        $result['default'] = $item['evidence']['prop_default'];
    } elseif ($item['category'] === 'key_or_literal_review') {
        $result['evidence_kind'] = 'literal_key_decision_required';
        $result['argument'] = $item['evidence']['argument'];
    } else {
        $producer = match ($item['raw_expression']) {
            "__(\$badge['label'] ?? '')" => 'context_badges',
            "__(\$summary['label'])" => 'settings_summary',
            "__(\$group['title'])" => 'settings_title',
            "__(\$group['description'])" => 'settings_description',
            '__($criticalDescription)' => 'critical_datasets',
            '__($message)' => 'publication_messages',
            default => null,
        };
        $result['evidence_kind'] = $producer ? 'producer_observations' : 'runtime_value_required';
        $result['producer'] = $producer;
        // Manual, snapshot-bound interpretation; no automatic taint/data-flow proof claimed.
        $result['binding_review'] = $producer ? 'Manually traced in the saved source; producer expressions extracted by AST.' : match (true) {
            str_contains($item['raw_expression'], '$details[') => 'Reason supplied through the component details prop; arbitrary callers may supply values.',
            str_contains($item['raw_expression'], 'headline()') => 'Display text is generated from a field name or document attribute; no fixed literal at this call.',
            default => 'Status/review value supplied by the finding, link, key or review record; no complete value domain proven.',
        };
    }
    $counts[$result['evidence_kind']] = ($counts[$result['evidence_kind']] ?? 0) + 1;
    $items[] = $result;
}
if (count($items) !== 59 || count(array_unique(array_column($items, 'capture_id'))) !== 59) {
    throw new RuntimeException('Incomplete trace coverage');
}
$result = ['mode' => 'offline_source_argument_evidence', 'production_connected' => false, 'database_writes' => 0,
    'review_sha256' => hash_file('sha256', $reviewPath), 'capture_manifest_sha256' => $context['manifest_sha256'],
    'implementation_sha256' => hash_file('sha256', $root.'/tests/Support/TranslationWorkbench/SourceArgumentEvidence.php'),
    'runner_sha256' => hash_file('sha256', __FILE__), 'counts' => $counts, 'producers' => $producers, 'items' => $items];
$out = $argv[1] ?? __DIR__.'/source-arguments-2026-10-19.json';
$handle = fopen($out, 'x');
if (! $handle) {
    throw new RuntimeException('Output already exists');
}
fwrite($handle, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n");
fclose($handle);
echo json_encode($counts, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
