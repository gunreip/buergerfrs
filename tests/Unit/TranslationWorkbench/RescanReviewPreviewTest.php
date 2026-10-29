<?php

use Gunreip\TranslationWorkbench\Livewire\CurrentSourceReview;
use Gunreip\TranslationWorkbench\Support\CurrentScanPreview;
use Gunreip\TranslationWorkbench\Support\RescanReviewPreview;
use Illuminate\Filesystem\Filesystem;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('renders isolated review evidence without mixing its finding IDs with live records', function () {
    $id = str_repeat('a', 64);
    $snapshot = ['source_run' => '20261020-192821-b3d8958008b8', 'generation' => 'isolated-example', 'exported_at' => '2026-10-21',
        'summary' => ['confirmed' => 4, 'preserved_for_review' => 1],
        'files' => [['id' => $id, 'path' => 'views/example.blade.php', 'issues' => 1, 'preserved' => 1]]];
    $detail = ['path' => 'views/example.blade.php', 'issues' => [['line' => 3, 'column' => 5, 'reason' => 'dynamic_or_nonliteral_argument']],
        'captures' => [['source_line' => 3, 'source_column' => 5, 'raw_expression' => '<script>alert(1)</script>',
            'option_source' => ['owner_key' => 'ui.document.types', 'value_expression' => '$document->type',
                'fallback_expression' => '<script>alert(2)</script>', 'locale_expression' => null],
            'option_list_review' => ['state' => 'reviewed_default_source', 'path' => 'Provider.php', 'line' => 12,
                'entries' => [['key' => 'parent', 'value' => '<script>alert(3)</script>', 'line' => 13,
                    'key_dependency' => ['class' => 'Contact', 'constant' => 'PARENT', 'path' => 'Contact.php', 'line' => 9]]]]]],
        'preserved' => [17], 'relocations' => []];
    $reader = Mockery::mock(RescanReviewPreview::class);
    $reader->shouldReceive('load')->andReturn($snapshot);
    $reader->shouldReceive('detail')->with($snapshot, '')->andReturn(null);
    $reader->shouldReceive('detail')->with($snapshot, $id)->andReturn($detail);
    app()->instance(RescanReviewPreview::class, $reader);
    $current = Mockery::mock(CurrentScanPreview::class);
    $current->shouldReceive('load')->andReturn(null);
    $current->shouldReceive('detail')->andReturn(null);
    app()->instance(CurrentScanPreview::class, $current);
    Livewire::test(CurrentSourceReview::class)->assertSee('Isolated rescan review')->assertSee('views/example.blade.php')
        ->set('rescanSelected', $id)->assertSee('#17')->assertSee('must not be used as IDs', false)
        ->assertDontSeeHtml('<script>alert(1)</script>')
        ->assertDontSeeHtml('<script>alert(2)</script>')
        ->assertDontSeeHtml('<script>alert(3)</script>')
        ->assertSee('Default list evidence')->assertSee('Provider.php:12')->assertSee('Contact::PARENT')->assertSee('Contact.php:9')
        ->assertSee('Option group key')->assertSee('ui.document.types')->assertSee('$document->type')
        ->assertSee('Current application locale')->assertSee('the group key is not a scalar translation')
        ->set('rescanSearch', 'not-found')->assertSet('rescanSelected', '')->assertSee('No matching source files.');
});

it('rejects changed evidence, unknown IDs and another generation', function () {
    $previous = storage_path();
    $tmp = sys_get_temp_dir().'/tw-rescan-preview-'.bin2hex(random_bytes(8));
    app()->useStoragePath($tmp);
    $base = $tmp.'/app/private/workbench-previews';
    $run = '20261021-120000-abcdef123456';
    $dir = $base.'/rescan-review-'.$run;
    mkdir($dir, 0700, true);
    try {
        $id = str_repeat('a', 64);
        $detail = json_encode(['id' => $id, 'path' => 'views/example.php'], JSON_THROW_ON_ERROR);
        file_put_contents($dir.'/'.$id.'.json', $detail);
        $manifest = json_encode(['mode' => 'isolated_rescan_review', 'run' => $run, 'generation' => 'one',
            'production_connected' => false, 'workbench_switched' => false,
            'files' => [['id' => $id, 'path' => 'views/example.php', 'sha256' => hash('sha256', $detail)]]], JSON_THROW_ON_ERROR);
        file_put_contents($dir.'/manifest.json', $manifest);
        file_put_contents($base.'/rescan-review.json', json_encode(['run' => $run, 'sha256' => hash('sha256', $manifest)]));
        file_put_contents($base.'/current-generation.json', json_encode(['generation' => ['generation' => 'one']]));
        $reader = new RescanReviewPreview;
        $snapshot = $reader->load();
        expect($snapshot)->not->toBeNull()->and($reader->detail($snapshot, $id)['path'])->toBe('views/example.php')
            ->and($reader->detail($snapshot, '../escape'))->toBeNull()
            ->and($reader->detail($snapshot, str_repeat('b', 64)))->toBeNull();
        file_put_contents($dir.'/'.$id.'.json', '{}');
        expect($reader->detail($snapshot, $id))->toBeNull();
        file_put_contents($base.'/current-generation.json', json_encode(['generation' => ['generation' => 'other']]));
        expect($reader->load())->toBeNull();
        file_put_contents($dir.'/manifest.json', '{}');
        expect($reader->load())->toBeNull();
    } finally {
        app()->useStoragePath($previous);
        (new Filesystem)->deleteDirectory($tmp);
    }
});
