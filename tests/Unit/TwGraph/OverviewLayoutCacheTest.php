<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutCache;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class);

it('reuses prepared arrays and invalidates content changes even with unchanged size and timestamp', function () {
    $file = tempnam(sys_get_temp_dir(), 'overview-cache-');
    try {
        file_put_contents($file, 'aaaa');
        $mtime = filemtime($file);
        $calls = 0;
        $build = function () use (&$calls) {
            return ['data' => ['build' => ++$calls], 'issues' => []];
        };
        $inputs = ['locale' => 'en', 'authored' => ['length' => '4rem']];
        $first = OverviewLayoutCache::remember([$file], $inputs, $build);
        expect(OverviewLayoutCache::remember([$file], $inputs, $build))->toBe($first);
        touch($file, $mtime + 10);
        expect(OverviewLayoutCache::remember([$file], $inputs, $build))->toBe($first);
        file_put_contents($file, 'bbbb');
        touch($file, $mtime);
        expect(OverviewLayoutCache::remember([$file], $inputs, $build)['data']['build'])->toBe(2);
        $inputs['locale'] = 'de';
        expect(OverviewLayoutCache::remember([$file], $inputs, $build)['data']['build'])->toBe(3);
        $inputs['authored']['length'] = '8rem';
        expect(OverviewLayoutCache::remember([$file], $inputs, $build)['data']['build'])->toBe(4);
        Cache::flush();
        expect(OverviewLayoutCache::remember([$file], $inputs, $build)['data']['build'])->toBe(5);
    } finally {
        unlink($file);
    }
});

it('fingerprints the complete dependency list and bypasses missing files', function () {
    $first = tempnam(sys_get_temp_dir(), 'overview-cache-');
    $second = tempnam(sys_get_temp_dir(), 'overview-cache-');
    try {
        file_put_contents($first, 'same');
        file_put_contents($second, 'same');
        $hash = OverviewLayoutCache::fingerprint([$first, $second], []);
        expect(OverviewLayoutCache::fingerprint([$second, $first], []))->toBe($hash)
            ->and(OverviewLayoutCache::fingerprint([$first], []))->not->toBe($hash)
            ->and(OverviewLayoutCache::fingerprint([$second], []))->not->toBe($hash);
        file_put_contents($second, 'compiler changed');
        expect(OverviewLayoutCache::fingerprint([$first, $second], []))->not->toBe($hash);
        expect(OverviewLayoutCache::fingerprint([$first.'.missing'], []))->toBeNull();
    } finally {
        unlink($first);
        unlink($second);
    }
});

it('does not retain mismatch results', function () {
    $calls = 0;
    $build = function () use (&$calls) {
        return ['data' => ++$calls, 'issues' => ['mismatch']];
    };
    OverviewLayoutCache::remember([__FILE__], ['test' => 'mismatch'], $build);
    OverviewLayoutCache::remember([__FILE__], ['test' => 'mismatch'], $build);
    expect($calls)->toBe(2);
});

it('returns identical complete layouts on cold and warm loads', function () {
    $cold = OverviewLayoutOverrides::load();
    $warm = OverviewLayoutOverrides::load();
    expect($warm)->toBe($cold)->and($warm['issues'])->toBe([]);
});
