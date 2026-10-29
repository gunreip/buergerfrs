<?php

// Isolated scanner rehearsal: no application bootstrap, providers, .env or database.
use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Gunreip\TranslationWorkbench\Generation\CopiedSourceCapture;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use PhpParser\Node;
use PhpParser\ParserFactory;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$result = json_decode(file_get_contents(__DIR__.'/backup-restore-result-2026-10-19.json'), true, flags: JSON_THROW_ON_ERROR);
$manifestPath = $root.'/'.$result['backup_manifest'];
if (hash_file('sha256', $manifestPath) !== $result['backup_manifest_sha256']) {
    throw new RuntimeException('Backup manifest changed');
}
$manifest = json_decode(file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);
$copy = dirname($manifestPath).'/source';
$configPath = 'packages/gunreip/laravel-translation-workbench/config/translation-workbench.php';
$read = static function (string $path) use ($copy, $manifest): string {
    if (! isset($manifest['source_files'][$path]) || str_contains($path, '..') || is_link($copy.'/'.$path)
        || ! str_starts_with((string) realpath($copy.'/'.$path), realpath($copy).DIRECTORY_SEPARATOR)) {
        throw new RuntimeException('Not a saved source file: '.$path);
    }
    $bytes = file_get_contents($copy.'/'.$path);
    if (hash('sha256', $bytes) !== $manifest['source_files'][$path]['sha256']) {
        throw new RuntimeException('Saved source changed: '.$path);
    }

    return $bytes;
};
// Read only literal scan-scope arrays from the saved config AST. Never execute it.
$ast = (new ParserFactory)->createForHostVersion()->parse($read($configPath));
if (count($ast) !== 1 || ! $ast[0] instanceof Node\Stmt\Return_ || ! $ast[0]->expr instanceof Node\Expr\Array_) {
    throw new RuntimeException('Unsupported scan configuration');
}
$keys = ['paths', 'exclude_paths', 'exclude_directory_names', 'file_patterns', 'ignored_filename_contains'];
$scope = [];
foreach ($ast[0]->expr->items as $item) {
    if (! $item?->key instanceof Node\Scalar\String_ || ! in_array($item->key->value, $keys, true)) {
        continue;
    }
    if (! $item->value instanceof Node\Expr\Array_ || isset($scope[$item->key->value])) {
        throw new RuntimeException('Nonliteral or duplicate scan scope');
    }
    $scope[$item->key->value] = [];
    foreach ($item->value->items as $value) {
        if (! $value?->value instanceof Node\Scalar\String_ || $value->unpack) {
            throw new RuntimeException('Nonliteral scan scope item');
        }
        $scope[$item->key->value][] = $value->value->value;
    }
}
if (count($scope) !== count($keys)) {
    throw new RuntimeException('Incomplete scan scope');
}
foreach ($scope['paths'] as $path) {
    if (! is_dir($copy.'/'.$path) && ! is_file($copy.'/'.$path)) {
        throw new RuntimeException('Missing saved scan root: '.$path);
    }
}
$app = new Application($copy);
$app->instance('config', new Repository(['translation-workbench' => $scope]));
$app->instance('files', new Filesystem);
Facade::setFacadeApplication($app);
$scanner = $app->make(TranslationWorkbenchScanner::class);
$adapter = new CopiedSourceCapture;
$reader = new BaselineValueReader;
// Explicit rehearsal locale only; no inferred regional/package fallback.
$locale = 'en';
$lang = [];
$langFiles = [];
$langPending = [];
foreach ($manifest['source_files'] as $path => $info) {
    if (! preg_match('~^lang/'.preg_quote($locale, '~').'/([^/]+)\.php$~D', $path, $match)) {
        continue;
    }
    $read($path);
    $langFiles[$path] = $info['sha256'];
    try {
        foreach ($reader->phpFile($copy.'/'.$path, $locale, $match[1], $info['sha256']) as $row) {
            if (isset($lang[$row['key']])) {
                throw new RuntimeException('Ambiguous language key');
            }
            $lang[$row['key']] = ['value' => $row['value'], 'provenance' => [
                'path' => $path, 'sha256' => $info['sha256'], 'locale' => $locale, 'key' => $row['key'],
            ]];
        }
    } catch (RuntimeException $e) {
        $langPending[$path] = $e->getMessage();
    }
}
$outDir = dirname($manifestPath).'/capture-'.bin2hex(random_bytes(6));
mkdir($outDir, 0700);
$stream = fopen($outDir.'/candidates.jsonl', 'x');
$report = ['mode' => 'copied_source_candidates', 'active_generation_created' => false, 'production_connected' => false,
    'backup_manifest_sha256' => $result['backup_manifest_sha256'], 'scope' => $scope,
    'scope_config_sha256' => $manifest['source_files'][$configPath]['sha256'], 'locale' => $locale,
    'language_files' => $langFiles, 'language_pending' => $langPending,
    'files' => [], 'counts' => [], 'context_counts' => [], 'source_context_validation_complete' => false,
    'scanner_code_sha256' => [], 'state' => 'incomplete'];
foreach (glob($root.'/packages/gunreip/laravel-translation-workbench/src/Scanner/*.php') as $file) {
    $report['scanner_code_sha256'][substr($file, strlen($root) + 1)] = hash_file('sha256', $file);
}
$report['adapter_sha256'] = hash_file('sha256', $root.'/packages/gunreip/laravel-translation-workbench/src/Generation/CopiedSourceCapture.php');
$report['support_code_sha256'] = [];
foreach (['packages/gunreip/laravel-translation-workbench/src/Generation/CopiedSourceCapture.php', 'packages/gunreip/laravel-translation-workbench/src/Support/CopiedSourceContext.php', 'packages/gunreip/laravel-translation-workbench/src/Generation/BaselineValueReader.php',
    'docs/audits/translation-workbench-2026-10-15/capture-copied-source.php', 'composer.lock'] as $path) {
    $report['support_code_sha256'][$path] = hash_file('sha256', $root.'/'.$path);
}
try {
    $files = $scanner->scannableFiles();
    usort($files, fn ($a, $b) => strcmp($a->getPathname(), $b->getPathname()));
    foreach ($files as $file) {
        $path = substr($file->getPathname(), strlen($copy) + 1);
        $bytes = $read($path);
        $hash = hash('sha256', $bytes);
        $report['files'][$path] = ['sha256' => $hash, 'candidates' => 0];
        foreach ($scanner->scanFile($file->getPathname()) as $item) {
            $row = $adapter->capture($item, $bytes, $hash, $lang);
            $row['capture_id'] = hash('sha256', json_encode([$result['backup_manifest_sha256'], $path, $hash, $row['source_offset']], JSON_THROW_ON_ERROR));
            fwrite($stream, json_encode($row, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n");
            $report['context_counts'][$row['source_context']['state']] = ($report['context_counts'][$row['source_context']['state']] ?? 0) + 1;
            $report['counts'][$row['reason']] = ($report['counts'][$row['reason']] ?? 0) + 1;
            $report['files'][$path]['candidates']++;
        }
        $read($path);
    }
    foreach ($report['files'] as $path => $info) {
        $read($path);
    }
    foreach ($langFiles as $path => $hash) {
        $read($path);
    }
    foreach ([...$report['scanner_code_sha256'], ...$report['support_code_sha256']] as $path => $hash) {
        if (hash_file('sha256', $root.'/'.$path) !== $hash) {
            throw new RuntimeException('Capture implementation changed during run: '.$path);
        }
    }
    $report['source_context_validation_complete'] = ($report['context_counts']['unresolved'] ?? 0) === 0;
    $report['state'] = 'candidates_captured';
} finally {
    fclose($stream);
    $report['candidate_file_sha256'] = hash_file('sha256', $outDir.'/candidates.jsonl');
    file_put_contents($outDir.'/manifest.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n");
}
echo json_encode(['manifest' => substr($outDir, strlen($root) + 1).'/manifest.json',
    'state' => $report['state'], 'files' => count($report['files']), 'counts' => $report['counts']], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
