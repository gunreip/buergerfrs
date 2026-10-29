<?php

use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchWorkflow;
use Gunreip\TranslationWorkbench\Support\BaselineRehearsalReport;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

function rehearsalReportFixture(): array
{
    return ['state' => 'isolated_correction_application_verified', 'production_connected' => false,
        'cluster_stopped' => true, 'assertions' => 103, 'synthetic_target' => 'rehearsal.correction.type',
        'input_dump_sha256' => str_repeat('a', 64), 'correction_applied' => 'isolated_database_only',
        'explicit_draft_import_verified' => true, 'explicit_review_cycle_verified' => true,
        'imported_draft_file_export_verified' => true, 'publication_readback_verified' => true];
}

it('rejects invalid or nonisolated reports', function () {
    $service = new BaselineRehearsalReport;
    expect($service->validate(rehearsalReportFixture()))->not->toBeNull();
    foreach ([null, [], ['production_connected' => true], ['cluster_stopped' => false], ['assertions' => '103'], ['input_dump_sha256' => 'invalid']] as $change) {
        expect($service->validate($change === null || $change === [] ? $change : array_replace(rehearsalReportFixture(), $change)))->toBeNull();
    }
});

it('shows recorded progress without treating missing steps as successful', function () {
    $service = Mockery::mock(BaselineRehearsalReport::class)->makePartial();
    $service->shouldReceive('load')->andReturn(array_diff_key(rehearsalReportFixture(), ['publication_readback_verified' => true]));
    app()->instance(BaselineRehearsalReport::class, $service);
    Livewire::test(TranslationWorkbenchWorkflow::class)->set('activeTab', 'baseline-preview')
        ->assertSee('Recorded isolated workflow rehearsal')->assertSee('rehearsal.correction.type')
        ->assertSee('Not verified in this report')->assertSee('Verified in rehearsal')
        ->assertSee('Install a separate versioned generation and detect changed constraints, indexes and triggers')
        ->assertSee('Transfer selected evidence with new finding identities and without old reviews or history')
        ->assertSee('Preserve all evidenced key names and reject conflicts outside the selected transfer')
        ->assertSee('Apply and replay the confirmed correction in the newly populated generation')
        ->assertSee('Edit, review, publish and read back a revision in the newly populated generation')
        ->assertSee('Import the full verified static capture into a fresh generation; preserve pending and excluded evidence')
        ->assertSee('Preserve source roles and unresolved arguments without creating translation decisions')
        ->assertSee('Preserve published language values and verify exact readback and explicit fallback without creating drafts')
        ->assertSee('Verify the explicit database, schema and baseline; reject missing or shadowed tables')
        ->assertSee('Compare snapshot occurrences and published evidence with the isolated database')
        ->assertSee('Bind source and database checks to one proposal')
        ->assertSee('Recheck the confirmed proposal before writing and reject changed files')
        ->assertSee('Production write actions are not connected to this preview.');
});

it('shows a missing report without starting a rehearsal', function () {
    $service = Mockery::mock(BaselineRehearsalReport::class)->makePartial();
    $service->shouldReceive('load')->andReturn(null);
    app()->instance(BaselineRehearsalReport::class, $service);
    Livewire::test(TranslationWorkbenchWorkflow::class)->set('activeTab', 'baseline-preview')
        ->assertSee('No valid isolated workflow report is available.');
});

it('reads the web snapshot and tolerates missing malformed or unreadable reports', function () {
    $original = storage_path();
    $directory = sys_get_temp_dir().'/workbench-report-'.bin2hex(random_bytes(8));
    $folder = $directory.'/app/private/workbench-previews';
    mkdir($folder, 0755, true);
    $path = $folder.'/workflow-rehearsal.json';
    app()->useStoragePath($directory);
    try {
        $reader = new BaselineRehearsalReport;
        expect($reader->load())->toBeNull();
        file_put_contents($path, '{invalid');
        expect($reader->load())->toBeNull();
        file_put_contents($path, json_encode(rehearsalReportFixture(), JSON_THROW_ON_ERROR));
        chmod($path, 0644);
        expect($reader->load())->toBe(rehearsalReportFixture());
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
