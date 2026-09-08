<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryCache;

it('reads cache before seed and reports the selected source', function (): void {
    $directory = sys_get_temp_dir() . '/tw-graph-cache-test-' . uniqid();
    mkdir($directory, 0755, true);
    $cachePath = $directory . '/cache.json';
    $seedPath = $directory . '/seed.json';

    file_put_contents($seedPath, json_encode(['source' => 'seed']));

    $cache = new GeometryCache($cachePath, $seedPath);

    expect($cache->readSource())->toBe('seed')
        ->and($cache->read())->toBe(['source' => 'seed']);

    file_put_contents($cachePath, json_encode(['source' => 'cache']));

    expect($cache->readSource())->toBe('cache')
        ->and($cache->read())->toBe(['source' => 'cache']);
});

it('returns empty arrays for missing or invalid geometry json', function (): void {
    $directory = sys_get_temp_dir() . '/tw-graph-cache-test-' . uniqid();
    mkdir($directory, 0755, true);
    $cachePath = $directory . '/cache.json';

    $cache = new GeometryCache($cachePath);

    expect($cache->readSource())->toBe('empty')
        ->and($cache->read())->toBe([]);

    file_put_contents($cachePath, '{invalid json');

    expect($cache->readSource())->toBe('cache')
        ->and($cache->read())->toBe([]);
});

it('writes pretty geometry json to a writable cache path', function (): void {
    $directory = sys_get_temp_dir() . '/tw-graph-cache-test-' . uniqid();
    $cachePath = $directory . '/nested/cache.json';
    $cache = new GeometryCache($cachePath);

    expect($cache->write(['geometry' => ['direction' => 'bottom-top']]))->toBeTrue()
        ->and($cache->readSource())->toBe('cache')
        ->and($cache->read())->toBe(['geometry' => ['direction' => 'bottom-top']])
        ->and(file_get_contents($cachePath))->toContain(PHP_EOL);
});

it('exposes configured cache and seed paths', function (): void {
    $cache = new GeometryCache('/tmp/tw-graph-cache.json', '/tmp/tw-graph-seed.json');

    expect($cache->path())->toBe('/tmp/tw-graph-cache.json')
        ->and($cache->seedPath())->toBe('/tmp/tw-graph-seed.json');
});

it('reports cache paths as writable when the parent directory is writable', function (): void {
    $directory = sys_get_temp_dir() . '/tw-graph-cache-test-' . uniqid();
    mkdir($directory, 0755, true);
    $cache = new GeometryCache($directory . '/cache.json');

    expect($cache->canWrite())->toBeTrue();
});
