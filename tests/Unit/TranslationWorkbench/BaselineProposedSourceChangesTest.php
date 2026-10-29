<?php

use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchWorkflow;
use Gunreip\TranslationWorkbench\Support\BaselineProposedSourceChanges;
use Gunreip\TranslationWorkbench\Support\BaselineReaderPreview;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

function proposedSourceFixture(string $path, string $bytes): array
{
    return ['schema' => 1, 'mode' => 'isolated_baseline_preview', 'exported_at' => '2026-10-20 UTC',
        'chains' => ['en' => ['en']], 'items' => ['ui.type' => [
            'key' => 'ui.type', 'key_id' => null,
            'locales' => ['en' => ['editing' => null, 'published' => [
                'value' => 'Type', 'state' => 'blocked_structure', 'path' => 'lang/en/ui.php',
                'sha256' => str_repeat('a', 64),
            ]]],
            'blocked_sources' => [['finding_id' => 42, 'path' => $path, 'line' => 1, 'column' => 4,
                'expression' => "__('ui.type', ['other' => 'ui.type'])", 'sha256' => hash('sha256', $bytes)]],
        ]]];
}

it('previews an explicit target using current captured bytes and rejects changed or ambiguous evidence', function () {
    $directory = sys_get_temp_dir().'/proposed-source-'.bin2hex(random_bytes(8));
    mkdir($directory, 0700);
    $path = $directory.'/example.blade.php';
    $bytes = "{{ __('ui.type', ['other' => 'ui.type']) }}";
    file_put_contents($path, $bytes);
    $data = proposedSourceFixture('example.blade.php', $bytes);
    $service = new BaselineProposedSourceChanges;
    try {
        $preview = $service->preview($data, 'ui.type', 'ui.field.type', 'example.blade.php', $directory);
        expect($preview['status'])->toBe('preview')
            ->and($preview['edits'][0]['after'])->toBe("'ui.field.type'")
            ->and($preview['after_sha256'])->toBe(hash('sha256', "{{ __('ui.field.type', ['other' => 'ui.type']) }}"))
            ->and($preview)->not->toHaveKey('proposed_bytes')
            ->and(file_get_contents($path))->toBe($bytes);
        expect($service->preview($data, 'ui.type', 'bad', 'example.blade.php', $directory)['status'])->toBe('proposal_required')
            ->and($service->preview($data, 'ui.type', 'ui.field.type', '../other.php', $directory)['status'])->toBe('select_file');
        $duplicate = $data;
        $duplicate['items']['ui.type']['blocked_sources'][] = $duplicate['items']['ui.type']['blocked_sources'][0];
        expect($service->preview($duplicate, 'ui.type', 'ui.field.type', 'example.blade.php', $directory)['status'])->toBe('source_not_verified');
        $shifted = $data;
        $shifted['items']['ui.type']['blocked_sources'][0]['column']++;
        expect($service->preview($shifted, 'ui.type', 'ui.field.type', 'example.blade.php', $directory)['status'])->toBe('source_not_verified');
        $outside = $data;
        $outside['items']['ui.type']['blocked_sources'][0]['path'] = '../outside.php';
        expect($service->preview($outside, 'ui.type', 'ui.field.type', '../outside.php', $directory)['status'])->toBe('source_not_verified');
        file_put_contents($path, $bytes.' changed');
        expect($service->preview($data, 'ui.type', 'ui.field.type', 'example.blade.php', $directory)['status'])->toBe('source_not_verified')
            ->and(file_get_contents($path))->toBe($bytes.' changed');
    } finally {
        unlink($path);
        rmdir($directory);
    }
});

it('shows the authored proposal separately and clears the source selection after editing the key', function () {
    $directory = storage_path('framework/testing/proposed-source-'.bin2hex(random_bytes(8)));
    mkdir($directory, 0700, true);
    $path = $directory.'/example.blade.php';
    $relative = substr($path, strlen(base_path()) + 1);
    $bytes = "{{ __('ui.type', ['other' => 'ui.type']) }}";
    file_put_contents($path, $bytes);
    file_put_contents($directory.'/language.php', "<?php return ['type' => 'Type'];");
    $data = proposedSourceFixture($relative, $bytes);
    $data['items']['ui.type']['locales']['en']['published']['path'] = dirname($relative).'/language.php';
    $data['items']['ui.type']['locales']['en']['published']['sha256'] = hash_file('sha256', $directory.'/language.php');
    $reader = Mockery::mock(BaselineReaderPreview::class)->makePartial();
    $reader->shouldReceive('load')->andReturnUsing(function () use (&$data) {
        return $data;
    });
    app()->instance(BaselineReaderPreview::class, $reader);
    try {
        $component = Livewire::test(TranslationWorkbenchWorkflow::class)
            ->set('activeTab', 'baseline-preview')->set('baselineChain', 'en')->set('baselineFilter', 'blocked_structure')
            ->set('baselineProposedKey', 'ui.field.type')->call('checkBaselineProposal')
            ->assertSee('Source changes for your proposed key')
            ->set('baselineProposedSourceFile', $relative)
            ->assertSee('Proposed key arguments')->assertSee("'ui.field.type'")
            ->call('checkBaselineProposedChanges')->assertSee('Complete source preview verified')
            ->assertSee('Proposal fingerprint')->assertSet('baselineProposedChanges.finding_count', 1)
            ->set('baselineProposedKey', 'ui.other.type')
            ->assertSet('baselineProposedSourceFile', '')
            ->assertSet('baselineProposedChanges', null)->assertDontSee('Proposal fingerprint')
            ->assertDontSee('Proposed key arguments')->assertDontSee('Source changes for your proposed key')
            ->call('checkBaselineProposal')->set('baselineProposedSourceFile', $relative)
            ->assertSee("'ui.other.type'")
            ->set('baselineFilter', 'all')->assertSet('baselineProposedSourceFile', '');
        $component->set('baselineProposedKey', 'ui.field.type')->call('checkBaselineProposedChanges')
            ->assertSee('Proposal fingerprint');
        $data['exported_at'] = 'changed snapshot';
        $component->call('$refresh')->assertSet('baselineProposedChanges', null)
            ->assertDontSee('Proposal fingerprint');
        expect(file_get_contents($path))->toBe($bytes);
    } finally {
        unlink($path);
        unlink($directory.'/language.php');
        rmdir($directory);
    }
});

it('requires every source and language file and binds the complete proposal to its evidence', function () {
    $directory = sys_get_temp_dir().'/proposal-set-'.bin2hex(random_bytes(8));
    mkdir($directory, 0700);
    $bytes = "{{ __('ui.type', ['other' => 'ui.type']) }}";
    $language = "<?php return ['type' => 'Type'];";
    foreach (['first.blade.php' => $bytes, 'second.blade.php' => $bytes, 'language.php' => $language] as $path => $content) {
        file_put_contents($directory.'/'.$path, $content);
    }
    $snapshot = proposedSourceFixture('first.blade.php', $bytes);
    $item = &$snapshot['items']['ui.type'];
    $item['blocked_sources'][] = [...$item['blocked_sources'][0], 'finding_id' => 43, 'path' => 'second.blade.php'];
    $item['locales']['en']['published']['path'] = 'language.php';
    $item['locales']['en']['published']['sha256'] = hash('sha256', $language);
    $reader = new BaselineProposedSourceChanges;
    try {
        $result = $reader->previewAll($snapshot, 'ui.type', 'ui.field.type', $directory);
        expect($result['status'])->toBe('preview')->and($result['finding_count'])->toBe(2)
            ->and($result['files'])->toHaveCount(2)->and($result['fingerprint'])->toMatch('/^[a-f0-9]{64}$/')
            ->and($reader->previewAll($snapshot, 'ui.type', 'ui.field.type', $directory)['fingerprint'])->toBe($result['fingerprint'])
            ->and($reader->previewAll($snapshot, 'ui.type', 'ui.other.type', $directory)['fingerprint'])->not->toBe($result['fingerprint']);
        $newSnapshot = $snapshot;
        $newSnapshot['exported_at'] = 'new capture';
        expect($reader->previewAll($newSnapshot, 'ui.type', 'ui.field.type', $directory)['fingerprint'])->not->toBe($result['fingerprint']);
        $badPosition = $snapshot;
        $badPosition['items']['ui.type']['blocked_sources'][1]['column']++;
        $partial = $reader->previewAll($badPosition, 'ui.type', 'ui.field.type', $directory);
        expect($partial['status'])->toBe('files_not_verified')->and($partial['fingerprint'])->toBeNull()
            ->and($partial['finding_count'])->toBe(1)
            ->and($partial['issues'][0]['path'])->toBe('second.blade.php');
        file_put_contents($directory.'/language.php', $language.' changed');
        $changed = $reader->previewAll($snapshot, 'ui.type', 'ui.field.type', $directory);
        expect($changed['status'])->toBe('files_not_verified')->and($changed['fingerprint'])->toBeNull()
            ->and($changed['issues'][0]['kind'])->toBe('language')
            ->and($changed['issues'][0]['status'])->toBe('changed');
        file_put_contents($directory.'/language.php', $language);
        unlink($directory.'/second.blade.php');
        $missing = $reader->previewAll($snapshot, 'ui.type', 'ui.field.type', $directory);
        expect($missing['status'])->toBe('files_not_verified')->and($missing['fingerprint'])->toBeNull()
            ->and($missing['issues'][0]['status'])->toBe('missing');
        $item['blocked_sources'][1]['finding_id'] = 42;
        expect($reader->previewAll($snapshot, 'ui.type', 'ui.field.type', $directory)['status'])->toBe('source_evidence_incomplete');
        $item['blocked_sources'] = [];
        expect($reader->previewAll($snapshot, 'ui.type', 'ui.field.type', $directory)['fingerprint'])->toBeNull()
            ->and($reader->previewAll($snapshot, 'ui.type', 'ui.field.type', $directory)['status'])->toBe('source_evidence_incomplete')
            ->and(file_get_contents($directory.'/first.blade.php'))->toBe($bytes)
            ->and(file_get_contents($directory.'/language.php'))->toBe($language);
    } finally {
        foreach (['first.blade.php', 'second.blade.php', 'language.php'] as $path) {
            if (is_file($directory.'/'.$path)) {
                unlink($directory.'/'.$path);
            }
        }
        rmdir($directory);
    }
});
