<?php

use Gunreip\TranslationWorkbench\Scanner\SourceAnalysisCache;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->analysisRoot = sys_get_temp_dir().'/tw-analysis-'.bin2hex(random_bytes(8));
    mkdir($this->analysisRoot);
    config(['translation-workbench.analysis_cache.path' => $this->analysisRoot.'/cache']);
});

afterEach(function () {
    File::deleteDirectory($this->analysisRoot);
});

it('reuses extraction across instances but invalidates bytes path and scanner revision', function () {
    $path = base_path('resources/views/example.blade.php');
    $bytes = "{{ __('Save') }}";
    $items = app(TranslationWorkbenchScanner::class)->scanSource($path, $bytes);
    $calls = 0;
    $analyze = function () use (&$calls, $items) {
        $calls++;

        return $items;
    };
    $cache = new SourceAnalysisCache($this->analysisRoot.'/cache', 'v1');
    expect($cache->remember($path, $bytes, $analyze)->all())->toEqual($items->all());
    expect((new SourceAnalysisCache($this->analysisRoot.'/cache', 'v1'))->remember($path, $bytes, $analyze)->all())->toEqual($items->all());
    expect($calls)->toBe(1);
    $cache->remember($path, $bytes.' ', $analyze);
    $cache->remember($path.'2', $bytes, $analyze);
    (new SourceAnalysisCache($this->analysisRoot.'/cache', 'v2'))->remember($path, $bytes.' ', $analyze);
    expect($calls)->toBe(4)->and(glob($this->analysisRoot.'/cache/*.json'))->toHaveCount(2);
});

it('recomputes corrupt expired or unavailable cache and never hides extraction failures', function () {
    $cache = new SourceAnalysisCache($this->analysisRoot.'/cache', 'v1');
    $calls = 0;
    $analyze = function () use (&$calls) {
        $calls++;

        return collect();
    };
    $cache->remember('file', 'bytes', $analyze);
    $file = glob($this->analysisRoot.'/cache/*.json')[0];
    file_put_contents($file, 'broken');
    $cache->remember('file', 'bytes', $analyze);
    touch($file, time() - 604801);
    clearstatcache();
    $cache->remember('file', 'bytes', $analyze);
    file_put_contents($this->analysisRoot.'/not-a-directory', '');
    (new SourceAnalysisCache($this->analysisRoot.'/not-a-directory', 'v1'))->remember('file', 'bytes', $analyze);
    expect($calls)->toBe(4);
    expect(fn () => $cache->remember('file', 'changed', fn () => throw new RuntimeException('Failed extraction')))
        ->toThrow(RuntimeException::class, 'Failed extraction');
});

it('invalidates configured cache on configuration changes and prunes only its expired files', function () {
    $calls = 0;
    $analyze = function () use (&$calls) {
        $calls++;

        return collect();
    };
    SourceAnalysisCache::configured()->remember('file', 'bytes', $analyze);
    SourceAnalysisCache::configured()->remember('file', 'bytes', $analyze);
    expect($calls)->toBe(1);
    config(['translation-workbench.ignored_filename_contains' => ['changed']]);
    SourceAnalysisCache::configured()->remember('file', 'bytes', $analyze);
    expect($calls)->toBe(2);
    $file = glob($this->analysisRoot.'/cache/*.json')[0];
    file_put_contents($this->analysisRoot.'/cache/unrelated', 'keep');
    touch($file, time() - 604801);
    touch($this->analysisRoot.'/cache/unrelated', time() - 604801);
    clearstatcache();
    SourceAnalysisCache::configured();
    expect(is_file($file))->toBeFalse()->and(is_file($this->analysisRoot.'/cache/unrelated'))->toBeTrue();
});

it('keeps cached and uncached manifests identical including additions moves and deletions', function () {
    // Use a project-relative scope as the production scanner does.
    $relative = 'storage/framework/testing/analysis-'.bin2hex(random_bytes(8));
    $directory = base_path($relative);
    File::ensureDirectoryExists($directory);
    try {
        config(['translation-workbench.exclude_paths' => [], 'translation-workbench.ignored_filename_contains' => []]);
        $scanner = app(TranslationWorkbenchScanner::class);
        File::put($directory.'/one.php', "<?php __('Save');");
        File::put($directory.'/empty.php', '<?php // no translation');
        $first = $scanner->scanWithManifest([$relative]);
        expect($scanner->scanWithManifest([$relative]))->toEqual($first);
        File::put($directory.'/one.php', "<?php\n\n__('Save');");
        File::put($directory.'/new.php', "<?php __('Open');");
        File::delete($directory.'/empty.php');
        $cached = $scanner->scanWithManifest([$relative], $first['scanned_paths']);
        $inventory = $scanner->scanInventory([$relative], $first['scanned_paths']);
        expect($inventory)->toEqual(array_diff_key($cached, ['items' => true]));
        config(['translation-workbench.analysis_cache.enabled' => false]);
        $fresh = $scanner->scanWithManifest([$relative], $first['scanned_paths']);
        expect($cached)->toEqual($fresh)
            ->and($cached['missing_paths'])->toContain($relative.'/empty.php')
            ->and($cached['items'])->toHaveCount(2)
            ->and($cached['file_hashes'][$relative.'/one.php'])->not->toBe($first['file_hashes'][$relative.'/one.php']);
    } finally {
        File::deleteDirectory($directory);
    }
});
