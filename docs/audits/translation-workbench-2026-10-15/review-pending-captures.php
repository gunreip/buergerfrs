<?php

// Offline review of saved candidates; no bootstrap, database or source modifications.
use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Tests\Support\TranslationWorkbench\PendingCaptureReview;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$read = static fn (string $p): array => json_decode(file_get_contents($p), true, flags: JSON_THROW_ON_ERROR);
$context = $read(__DIR__.'/copied-source-context-2026-10-19.json');
$manifestPath = $root.'/'.$context['manifest'];
if (hash_file('sha256', $manifestPath) !== $context['manifest_sha256']) {
    throw new RuntimeException('Capture manifest changed');
}
$manifest = $read($manifestPath);
$backup = dirname($manifestPath, 2);
$candidatePath = dirname($manifestPath).'/candidates.jsonl';
if (hash_file('sha256', $candidatePath) !== $context['candidate_file_sha256']) {
    throw new RuntimeException('Capture payload changed');
}
$language = [];
$reader = new BaselineValueReader;
foreach ($manifest['language_files'] as $path => $hash) {
    $group = pathinfo($path, PATHINFO_FILENAME);
    foreach ($reader->phpFile($backup.'/source/'.$path, $manifest['locale'], $group, $hash) as $entry) {
        $language[] = ['key' => $entry['key'], 'value' => $entry['value'], 'locale' => $manifest['locale'], 'path' => $path, 'sha256' => $hash];
    }
}
$reviewer = new PendingCaptureReview;
$items = [];
$counts = [];
foreach (file($candidatePath, FILE_IGNORE_NEW_LINES) as $line) {
    $row = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
    if (! in_array($row['reason'], ['dynamic_or_nonliteral_argument', 'special_call_requires_context', 'key_like_argument_without_lang_evidence'], true)) {
        continue;
    }
    $source = file_get_contents($backup.'/source/'.$row['source_path']);
    $item = $reviewer->review($row, $source, $language);
    $lines = explode("\n", $source);
    $item['source_excerpt'] = array_slice($lines, max(0, $row['source_line'] - 3), 5);
    $items[] = $item;
    $counts[$item['category']] = ($counts[$item['category']] ?? 0) + 1;
}
if (count($items) !== 72 || count(array_unique(array_column($items, 'capture_id'))) !== 72) {
    throw new RuntimeException('Incomplete or duplicate review coverage');
}
$result = ['mode' => 'offline_pending_review', 'capture_manifest_sha256' => $context['manifest_sha256'],
    'candidate_file_sha256' => $context['candidate_file_sha256'], 'production_connected' => false,
    'reviewer_sha256' => hash_file('sha256', $root.'/tests/Support/TranslationWorkbench/PendingCaptureReview.php'),
    'automatic_decisions' => 0, 'counts' => $counts, 'items' => $items];
$out = $argv[1] ?? __DIR__.'/pending-capture-review-2026-10-19.json';
$handle = fopen($out, 'x');
if (! $handle) {
    throw new RuntimeException('Review output already exists');
}
fwrite($handle, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n");
fclose($handle);
echo json_encode(['items' => count($items), 'counts' => $counts], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
