<?php

use Gunreip\TranslationWorkbench\Support\BaselineFileFreshness;

beforeEach(function () {
    $this->freshnessRoot = sys_get_temp_dir().'/tw-freshness-'.bin2hex(random_bytes(6));
    mkdir($this->freshnessRoot);
    file_put_contents($this->freshnessRoot.'/sample.php', 'captured content');
});

afterEach(function () {
    foreach (glob($this->freshnessRoot.'/*') as $path) {
        unlink($path);
    }
    rmdir($this->freshnessRoot);
});

it('deduplicates source files while retaining changes and missing evidence', function () {
    $source = ['path' => 'sample.php', 'sha256' => hash('sha256', 'captured content')];
    $item = ['blocked_sources' => [$source, $source], 'locales' => []];
    $service = new BaselineFileFreshness;
    $result = $service->inspect($item, $this->freshnessRoot);
    expect($result['matches'])->toBeTrue()->and($result['counts'])->toBe(['unchanged' => 1]);
    file_put_contents($this->freshnessRoot.'/sample.php', 'changed content');
    $result = $service->inspect($item, $this->freshnessRoot);
    expect($result['matches'])->toBeFalse()->and($result['counts'])->toBe(['changed' => 1])
        ->and(file_get_contents($this->freshnessRoot.'/sample.php'))->toBe('changed content');
    unlink($this->freshnessRoot.'/sample.php');
    expect($service->inspect($item, $this->freshnessRoot)['counts'])->toBe(['missing' => 1]);
    $item['blocked_sources'][0]['sha256'] = null;
    $item['blocked_sources'] = [$item['blocked_sources'][0]];
    expect($service->inspect($item, $this->freshnessRoot)['counts'])->toBe(['missing_evidence' => 1]);
});

it('checks language files too and refuses conflicting or absent source evidence', function () {
    $source = ['path' => 'sample.php', 'sha256' => hash('sha256', 'captured content')];
    $service = new BaselineFileFreshness;
    $item = ['locales' => ['en' => ['published' => $source]]];
    expect($service->inspect($item, $this->freshnessRoot)['matches'])->toBeFalse();
    $item['blocked_sources'] = [$source, [...$source, 'sha256' => hash('sha256', 'other')]];
    $result = $service->inspect($item, $this->freshnessRoot);
    expect($result['matches'])->toBeFalse()
        ->and($result['counts'])->toBe(['conflicting_evidence' => 1, 'unchanged' => 1]);
});

it('does not read traversal paths or symlink targets outside the project', function () {
    symlink(dirname($this->freshnessRoot), $this->freshnessRoot.'/outside');
    $item = ['blocked_sources' => array_map(fn ($path) => ['path' => $path, 'sha256' => str_repeat('a', 64)],
        ['../sample.php', '/etc/passwd', 'outside'])];
    $result = (new BaselineFileFreshness)->inspect($item, $this->freshnessRoot);
    expect($result['matches'])->toBeFalse()->and($result['counts'])->toBe(['outside_root' => 3])
        ->and(array_column($result['files'], 'actual_sha256'))->toBe([null, null, null]);
});
