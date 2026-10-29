<?php

use Gunreip\TranslationWorkbench\Livewire\CurrentSourceReview;
use Gunreip\TranslationWorkbench\Support\CurrentScanPreview;
use Illuminate\Filesystem\Filesystem;
use Livewire\Livewire;
use Tests\Support\TranslationWorkbench\CurrentScanDisposition;
use Tests\TestCase;

uses(TestCase::class);

it('plans reuse only from exact current source evidence', function (array $changes, array $values, string $expected) {
    $row = array_replace(['status' => 'pending_review', 'source_context' => ['state' => 'confirmed_call'],
        'reason' => 'key_with_exact_lang_evidence', 'found_translation_key' => 'ui.button.save'], $changes);
    expect((new CurrentScanDisposition)->classify($row, $values, 'en'))->toBe($expected);
})->with([
    'exact source locale' => [[], ['ui.button.save' => ['en' => ['value' => 'Save']]], 'reusable_key'],
    'no source fallback' => [[], ['ui.button.save' => ['de' => ['value' => 'Save']]], 'key_review'],
    'same text other key' => [[], ['ui.other.save' => ['en' => ['value' => 'Save']]], 'key_review'],
    'literal remains separate' => [['reason' => 'literal_candidate', 'found_translation_key' => null], ['ui.button.save' => ['en' => ['value' => 'Save']]], 'literal_new'],
    'short key needs UI review' => [['found_translation_key' => 'ui.type'], ['ui.type' => ['en' => ['value' => 'Type']]], 'key_review'],
    'comment excluded' => [['status' => 'excluded_comment'], [], 'excluded'],
    'unknown context' => [['source_context' => ['state' => 'unresolved']], [], 'expression_review'],
    'dynamic argument' => [['reason' => 'dynamic_or_nonliteral_argument', 'found_translation_key' => null], [], 'expression_review'],
]);

it('shows current problems and exact source locations without historical records or writes', function () {
    $id = str_repeat('a', 64);
    $snapshot = ['recorded_at' => '2026-10-20T19:00:00+00:00', 'files' => 1, 'source_locale' => 'en', 'counts' => ['key_review' => 1],
        'scope' => ['paths' => ['resources/views'], 'exclude_paths' => [], 'exclude_directory_names' => ['_old', '-old']],
        'language_issues' => [], 'groups' => [['id' => $id, 'key' => 'ui.type', 'title' => 'ui.type', 'state' => 'key_review', 'findings' => 1, 'locales' => ['en']]]];
    $detail = ['id' => $id, 'title' => 'ui.type', 'state' => 'key_review', 'findings' => [['source_path' => 'resources/views/example.blade.php', 'source_line' => 12, 'source_column' => 4,
        'raw_expression' => "__('ui.type')", 'reason' => 'key_with_exact_lang_evidence', 'suggested_key' => null]],
        'values' => ['en' => ['path' => 'lang/en/ui.php', 'value' => '<script>Type</script>']]];
    $reader = Mockery::mock(CurrentScanPreview::class);
    $reader->shouldReceive('load')->andReturn($snapshot);
    $reader->shouldReceive('detail')->with($snapshot, '')->andReturn(null);
    $reader->shouldReceive('detail')->with($snapshot, $id)->andReturn($detail);
    app()->instance(CurrentScanPreview::class, $reader);
    Livewire::test(CurrentSourceReview::class)->assertSee('ui.type')
        ->set('selected', $id)->assertSee('resources/views/example.blade.php:12:4')
        ->assertSee('lang/en/ui.php')->assertDontSeeHtml('<script>Type</script>')
        ->set('filter', 'reusable_key')->assertSee('No matching current findings.');
});

it('loads only the requested verified scan indexes and refuses changed evidence', function () {
    $original = storage_path();
    $temporary = sys_get_temp_dir().'/tw-current-reader-'.bin2hex(random_bytes(8));
    $run = '20261020-190000-abcdef123456';
    $base = $temporary.'/app/private/workbench-previews';
    $dir = $base.'/current-scan-'.$run;
    mkdir($dir, 0700, true);
    app()->useStoragePath($temporary);
    try {
        $indexes = [];
        foreach (['key_review', 'expression_review', 'literal_new', 'reusable_key'] as $state) {
            $bytes = json_encode([['id' => str_repeat('a', 64), 'state' => $state]], JSON_THROW_ON_ERROR);
            file_put_contents($dir.'/index-'.$state.'.json', $bytes);
            $indexes[$state] = ['groups' => 1, 'sha256' => hash('sha256', $bytes)];
        }
        $manifest = json_encode(['state' => 'complete', 'mode' => 'current_source_scan', 'run' => $run, 'production_connected' => false, 'indexes' => $indexes], JSON_THROW_ON_ERROR);
        file_put_contents($dir.'/manifest.json', $manifest);
        file_put_contents($base.'/current-scan.json', json_encode(['run' => $run, 'sha256' => hash('sha256', $manifest)], JSON_THROW_ON_ERROR));
        $reader = new CurrentScanPreview;
        expect(array_column($reader->load()['groups'], 'state'))->toBe(['key_review', 'expression_review'])
            ->and(array_column($reader->load('literal_new')['groups'], 'state'))->toBe(['literal_new']);
        file_put_contents($dir.'/index-literal_new.json', '[]');
        expect($reader->load('literal_new'))->toBeNull()
            ->and($reader->load())->not->toBeNull()
            ->and($reader->detail($reader->load(), '../../escape'))->toBeNull();
    } finally {
        app()->useStoragePath($original);
        (new Filesystem)->deleteDirectory($temporary);
    }
});
