<?php
use Gunreip\TranslationWorkbench\Generation\ScanResultFreshness;
use Tests\TestCase;
uses(TestCase::class);
it('detects added changed and deleted files while ignoring inventory order', function () {
    $old = ['hashes' => ['a.php' => 'aaa', 'b.php' => 'bbb'], 'directories' => ['app'], 'missing' => []];
    $current = ['file_hashes' => ['b.php' => 'bbb', 'a.php' => 'aaa'], 'scanned_directories' => ['app'], 'missing_paths' => []];
    expect(ScanResultFreshness::matches($old, $current))->toBeTrue();
    foreach ([['a.php' => 'new', 'b.php' => 'bbb'], ['a.php' => 'aaa'], ['a.php' => 'aaa', 'b.php' => 'bbb', 'c.php' => 'ccc']] as $hashes) {
        expect(ScanResultFreshness::matches($old, array_replace($current, ['file_hashes' => $hashes])))->toBeFalse();
    }
    expect(ScanResultFreshness::matches($old, array_replace($current, ['missing_paths' => ['gone.php']])))->toBeFalse()
        ->and(ScanResultFreshness::matches([], $current))->toBeFalse();
});
it('invalidates reuse when scan exclusions change', function () {
    $before = ScanResultFreshness::configuration();
    config(['translation-workbench.exclude_paths' => ['changed-scope']]);
    expect(ScanResultFreshness::configuration())->not->toBe($before);
});
