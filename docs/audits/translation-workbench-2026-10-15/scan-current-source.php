<?php

// Fresh read-only source scan. No providers, .env, production database or source writes.
use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Gunreip\TranslationWorkbench\Generation\CopiedSourceCapture;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\ParserFactory;
use Tests\Support\TranslationWorkbench\CurrentScanDisposition;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$configPath = 'packages/gunreip/laravel-translation-workbench/config/translation-workbench.php';
$configHash = hash_file('sha256', $root.'/'.$configPath);
// Read only literal scan-scope arrays from the saved config AST. Never execute it.
$ast = (new ParserFactory)->createForHostVersion()->parse(file_get_contents($root.'/'.$configPath));
if (count($ast) !== 1 || ! $ast[0] instanceof Node\Stmt\Return_ || ! $ast[0]->expr instanceof Node\Expr\Array_) {
    throw new RuntimeException('Unsupported scan configuration');
}
$keys = ['paths', 'exclude_paths', 'exclude_directory_names', 'file_patterns', 'ignored_filename_contains', 'translation_functions'];
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

$sourceLocale = null;
foreach ($ast[0]->expr->items as $item) {
    if ($item?->key instanceof Node\Scalar\String_ && $item->key->value === 'source_locale' && $item->value instanceof Node\Scalar\String_) {
        $sourceLocale = $item->value->value;
    }
}
if ($sourceLocale === null) {
    throw new RuntimeException('Source locale must be explicit');
}
foreach ($scope['paths'] as $path) {
    if (! file_exists($root.'/'.$path)) {
        throw new RuntimeException('Missing scan root: '.$path);
    }
}
$app = new Application($root);
$app->instance('config', new Repository(['translation-workbench' => $scope]));
$app->instance('files', new Filesystem);
Facade::setFacadeApplication($app);
$scanner = $app->make(TranslationWorkbenchScanner::class);
$adapter = new CopiedSourceCapture;
$disposition = new CurrentScanDisposition;
$reader = new BaselineValueReader;
$hashes = [$configPath => $configHash];
foreach (array_merge(glob($root.'/packages/gunreip/laravel-translation-workbench/src/Scanner/*.php'),
    [$root.'/packages/gunreip/laravel-translation-workbench/src/Generation/CopiedSourceCapture.php', $root.'/tests/Support/TranslationWorkbench/CurrentScanDisposition.php',
        $root.'/packages/gunreip/laravel-translation-workbench/src/Generation/BaselineValueReader.php', $root.'/packages/gunreip/laravel-translation-workbench/src/Support/CopiedSourceContext.php']) as $file) {
    $hashes[substr($file, strlen($root) + 1)] = hash_file('sha256', $file);
}
$langFiles = glob($root.'/lang/*/*.php');
sort($langFiles);
$values = [];
$lang = [];
$languageIssues = [];
foreach ($langFiles as $file) {
    $path = substr($file, strlen($root) + 1);
    $hashes[$path] = hash_file('sha256', $file);
    $locale = basename(dirname($file));
    $group = basename($file, '.php');
    try {
        $parsed = $reader->phpFile($file, $locale, $group, $hashes[$path]);
        foreach ($parsed as $row) {
            if (isset($values[$row['key']][$locale])) {
                throw new RuntimeException('Duplicate language identity');
            }
            $values[$row['key']][$locale] = ['value' => $row['value'], 'path' => $path, 'sha256' => $hashes[$path]];
            if ($locale === $sourceLocale) {
                $lang[$row['key']] = ['value' => $row['value'], 'provenance' => ['path' => $path, 'sha256' => $hashes[$path], 'locale' => $locale]];
            }
        }
    } catch (RuntimeException|Error $error) {
        $languageIssues[] = ['path' => $path, 'reason' => $error->getMessage()];
    }
}
$run = gmdate('Ymd-His').'-'.bin2hex(random_bytes(6));
$directory = $root.'/storage/app/private/workbench-previews/current-scan-'.$run;
mkdir($directory, 0755, true);
$stream = fopen($directory.'/candidates.jsonl', 'x');
$groups = [];
$counts = [];
$files = $scanner->scannableFiles();
$paths = array_map(fn ($f) => $f->getPathname(), $files);
sort($paths);
foreach ($paths as $absolute) {
    $path = substr($absolute, strlen($root) + 1);
    $bytes = file_get_contents($absolute);
    $hash = hash('sha256', $bytes);
    $hashes[$path] = $hash;
    foreach ($scanner->scanFile($absolute) as $item) {
        $row = $adapter->capture($item, $bytes, $hash, $lang);
        $row['capture_id'] = hash('sha256', json_encode([$path, $hash, $row['source_offset']], JSON_THROW_ON_ERROR));
        $row['disposition'] = $disposition->classify($row, $values, $sourceLocale);
        fwrite($stream, json_encode($row, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n");
        $counts[$row['disposition']] = ($counts[$row['disposition']] ?? 0) + 1;
        if ($row['disposition'] === 'excluded') {
            continue;
        }
        $key = $row['found_translation_key'];
        $id = hash('sha256', $key === null ? 'source:'.$row['capture_id'] : 'key:'.$key);
        if (! isset($groups[$id])) {
            $groups[$id] = ['id' => $id, 'key' => $key, 'title' => $key ?? $row['raw_literal'] ?? $row['raw_expression'] ?? $path,
                'state' => $row['disposition'], 'findings' => [], 'values' => $key === null ? [] : ($values[$key] ?? [])];
        }
        $groups[$id]['findings'][] = $row;
    }
}
fclose($stream);
// Reject a mixed source state, including newly added/deleted eligible files.
$endPaths = array_map(fn ($f) => $f->getPathname(), $scanner->scannableFiles());
sort($endPaths);
$endLangFiles = glob($root.'/lang/*/*.php');
sort($endLangFiles);
if ($paths !== $endPaths || $langFiles !== $endLangFiles) {
    throw new RuntimeException('File inventory changed during scan');
}
foreach ($hashes as $path => $hash) {
    if (! is_file($root.'/'.$path) || hash_file('sha256', $root.'/'.$path) !== $hash) {
        throw new RuntimeException('File changed during scan: '.$path);
    }
}
$summary = [];
foreach ($groups as $id => $group) {
    $json = json_encode($group, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    file_put_contents($directory.'/'.$id.'.json', $json);
    chmod($directory.'/'.$id.'.json', 0644);
    $summary[] = ['id' => $id, 'key' => $group['key'], 'title' => $group['title'], 'state' => $group['state'],
        'findings' => count($group['findings']), 'locales' => array_keys($group['values']), 'sha256' => hash('sha256', $json)];
}
usort($summary, fn ($a, $b) => strcmp($a['title'], $b['title']));
$indexes = [];
foreach (['reusable_key', 'literal_new', 'key_review', 'expression_review'] as $state) {
    $rows = array_values(array_filter($summary, fn ($row) => $row['state'] === $state));
    $indexJson = json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    file_put_contents($directory.'/index-'.$state.'.json', $indexJson);
    chmod($directory.'/index-'.$state.'.json', 0644);
    $indexes[$state] = ['groups' => count($rows), 'sha256' => hash('sha256', $indexJson)];
}
$report = ['mode' => 'current_source_scan', 'state' => 'complete', 'run' => $run, 'recorded_at' => gmdate(DATE_ATOM),
    'source_locale' => $sourceLocale, 'production_connected' => false, 'active_imports' => 0,
    'scope' => $scope, 'files' => count($paths), 'language_files' => count($langFiles), 'language_issues' => $languageIssues,
    'counts' => $counts, 'indexes' => $indexes, 'file_hashes' => $hashes,
    'candidates_sha256' => hash_file('sha256', $directory.'/candidates.jsonl'),
    'limits' => 'Configured translation calls only; static app PHP locale files. No legacy database import, JSON/vendor translation resolution or runtime execution.'];
$json = json_encode($report, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
file_put_contents($directory.'/manifest.json', $json);
chmod($directory.'/manifest.json', 0644);
$pointer = $root.'/storage/app/private/workbench-previews/current-scan.json';
file_put_contents($pointer.'.tmp', json_encode(['run' => $run, 'sha256' => hash('sha256', $json)], JSON_THROW_ON_ERROR));
chmod($pointer.'.tmp', 0644);
rename($pointer.'.tmp', $pointer);
echo json_encode(array_diff_key($report, ['groups' => true, 'file_hashes' => true]), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
