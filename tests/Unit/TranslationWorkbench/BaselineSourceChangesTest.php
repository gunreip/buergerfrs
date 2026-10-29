<?php

use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchWorkflow;
use Gunreip\TranslationWorkbench\Support\BaselineSourceChanges;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

function sourceChangeUiFixture(): array
{
    return ['schema' => 1, 'mode' => 'isolated_source_change_preview', 'recorded_at' => '2026-10-20',
        'input_dump_sha256' => str_repeat('a', 64), 'source_key' => 'ui.type', 'synthetic_target' => 'sample.new.type',
        'files' => [['path' => 'sample.blade.php', 'edits' => [['finding' => 42, 'line' => 7, 'column' => 3, 'before' => "'ui.type'", 'after' => "'sample.new.type'"]]]],
        'movements' => [['path' => 'sample.blade.php', 'before_line' => 7, 'before_column' => 18, 'after_line' => 7, 'after_column' => 36, 'expression' => "__('Unchanged')"]]];
}

it('selects recorded entries only and never resolves a submitted path', function () {
    $service = new BaselineSourceChanges;
    expect($service->select(sourceChangeUiFixture(), '../../.env')['file'])->toBeNull()
        ->and($service->select(sourceChangeUiFixture(), '')['file'])->toBeNull()
        ->and($service->select(sourceChangeUiFixture(), 'sample.blade.php')['file']['edits'][0]['finding'])->toBe(42);
});

it('renders changes and movements using the selected snapshot file', function () {
    $service = Mockery::mock(BaselineSourceChanges::class)->makePartial();
    $service->shouldReceive('load')->andReturn(sourceChangeUiFixture());
    app()->instance(BaselineSourceChanges::class, $service);
    Livewire::test(TranslationWorkbenchWorkflow::class)->set('activeTab', 'baseline-preview')
        ->assertSee('Recorded source changes')->assertSee('Select a file contained in the recorded preview.')
        ->assertDontSee('Complete source file comparison')
        ->call('checkBaselineSourceSet')->assertSee('Complete source file comparison')
        ->assertSee('Missing checksum evidence')
        ->set('baselineSourceFile', 'sample.blade.php')->assertSee('#42')->assertSee('Unchanged calls with shifted positions')
        ->assertSee('Current source file')->assertSee('Check again')
        ->assertSee('The snapshot lacks sufficient checksum evidence for this comparison.')
        ->assertSee('Original line / byte column')->set('baselineSourceFile', '../../.env')
        ->assertDontSee('#42')->assertSee('Select a file contained in the recorded preview.');
});

it('checks every recorded file and never treats partial or ambiguous evidence as complete', function () {
    $directory = sys_get_temp_dir().'/source-set-'.bin2hex(random_bytes(8));
    mkdir($directory, 0700);
    $reader = new BaselineSourceChanges;
    $before = hash('sha256', 'original');
    $after = hash('sha256', 'proposed');
    $files = [];
    try {
        foreach (['a.php' => 'original', 'b.php' => 'proposed', 'c.php' => 'edited'] as $path => $bytes) {
            file_put_contents($directory.'/'.$path, $bytes);
            $files[] = ['path' => $path, 'before_sha256' => $before, 'after_sha256' => $after];
        }
        $result = $reader->currentFiles(['files' => $files], $directory);
        expect($result['total'])->toBe(3)
            ->and($result['counts'])->toBe(['matches_before' => 1, 'matches_after' => 1, 'diverged' => 1])
            ->and($result['all_match_before'])->toBeFalse();
        file_put_contents($directory.'/b.php', 'original');
        file_put_contents($directory.'/c.php', 'original');
        expect($reader->currentFiles(['files' => $files], $directory)['all_match_before'])->toBeTrue();
        unlink($directory.'/c.php');
        expect($reader->currentFiles(['files' => $files], $directory)['counts']['missing'])->toBe(1)
            ->and($reader->currentFiles(['files' => $files], $directory)['all_match_before'])->toBeFalse();
        $files[] = $files[0];
        $duplicate = $reader->currentFiles(['files' => $files], $directory);
        expect($duplicate['total'])->toBe(3)
            ->and($duplicate['counts']['conflicting_evidence'])->toBe(1)
            ->and($duplicate['all_match_before'])->toBeFalse()
            ->and($reader->currentFiles(null, $directory)['all_match_before'])->toBeFalse()
            ->and($reader->currentFiles(['files' => []], $directory)['all_match_before'])->toBeFalse()
            ->and(file_get_contents($directory.'/a.php'))->toBe('original');
    } finally {
        foreach (['a.php', 'b.php', 'c.php'] as $path) {
            if (is_file($directory.'/'.$path)) {
                unlink($directory.'/'.$path);
            }
        }
        rmdir($directory);
    }
});

it('handles an unavailable snapshot without breaking the page', function () {
    $service = Mockery::mock(BaselineSourceChanges::class)->makePartial();
    $service->shouldReceive('load')->andReturn(null);
    app()->instance(BaselineSourceChanges::class, $service);
    Livewire::test(TranslationWorkbenchWorkflow::class)->set('activeTab', 'baseline-preview')
        ->assertSee('No readable source-change preview is available.');
});

it('validates the exported snapshot and tolerates missing or unreadable files', function () {
    $original = storage_path();
    $directory = sys_get_temp_dir().'/source-preview-'.bin2hex(random_bytes(8));
    $folder = $directory.'/app/private/workbench-previews';
    mkdir($folder, 0755, true);
    $path = $folder.'/source-changes.json';
    app()->useStoragePath($directory);
    try {
        $reader = new BaselineSourceChanges;
        expect($reader->load())->toBeNull();
        file_put_contents($path, '{invalid');
        expect($reader->load())->toBeNull();
        $fixture = sourceChangeUiFixture();
        file_put_contents($path, json_encode($fixture));
        expect($reader->load())->toBe($fixture);
        $fixture['files'][0]['edits'][0]['line'] = null;
        file_put_contents($path, json_encode($fixture));
        expect($reader->load())->toBeNull();
        file_put_contents($path, json_encode(sourceChangeUiFixture()));
        chmod($path, 0000);
        clearstatcache(true, $path);
        if (! is_readable($path)) {
            expect($reader->load())->toBeNull();
        }
    } finally {
        app()->useStoragePath($original);
        chmod($path, 0644);
        unlink($path);
        rmdir($folder);
        rmdir(dirname($folder));
        rmdir($directory.'/app');
        rmdir($directory);
    }
});

it('distinguishes the original proposed and diverged current file without writing it', function () {
    $directory = sys_get_temp_dir().'/source-current-'.bin2hex(random_bytes(8));
    mkdir($directory, 0700);
    $path = $directory.'/sample.blade.php';
    $data = sourceChangeUiFixture();
    $data['files'][0]['before_sha256'] = hash('sha256', 'original');
    $data['files'][0]['after_sha256'] = hash('sha256', 'proposed');
    $reader = new BaselineSourceChanges;
    try {
        expect($reader->currentFile($data, '../../.env', $directory))->toBeNull();
        expect($reader->currentFile($data, 'sample.blade.php', $directory)['status'])->toBe('missing');
        foreach (['original' => 'matches_before', 'proposed' => 'matches_after', 'other' => 'diverged'] as $bytes => $status) {
            file_put_contents($path, $bytes);
            expect($reader->currentFile($data, 'sample.blade.php', $directory)['status'])->toBe($status)
                ->and(file_get_contents($path))->toBe($bytes);
        }
        $invalid = $data;
        unset($invalid['files'][0]['after_sha256']);
        expect($reader->currentFile($invalid, 'sample.blade.php', $directory)['status'])->toBe('missing_evidence');
        $outside = $data;
        $outside['files'][0]['path'] = '../outside.php';
        expect($reader->currentFile($outside, '../outside.php', $directory)['status'])->toBe('outside_root');
        chmod($path, 0000);
        clearstatcache(true, $path);
        if (! is_readable($path)) {
            expect($reader->currentFile($data, 'sample.blade.php', $directory)['status'])->toBe('unreadable');
        }
    } finally {
        chmod($path, 0644);
        unlink($path);
        rmdir($directory);
    }
});
