<?php

use Gunreip\TranslationWorkbench\Support\RuntimeOptionPreview;
use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

uses(TestCase::class);

it('reads only verified observations and rejects changed source evidence', function () {
    $previous = storage_path();
    $tmp = sys_get_temp_dir().'/runtime-options-'.bin2hex(random_bytes(8));
    app()->useStoragePath($tmp);
    $base = $tmp.'/app/private/workbench-previews';
    mkdir($base, 0700, true);
    $run = '20261021-120000-abcdef123456';
    $data = ['mode' => 'read_only_runtime_option_observation', 'database_read_only' => true,
        'source_hashes' => ['app/Models/PersonDocumentType.php' => hash_file('sha256', base_path('app/Models/PersonDocumentType.php'))], 'groups' => []];
    $write = function ($data) use ($base, $run) {
        $bytes = json_encode($data);
        file_put_contents($base.'/runtime-options-'.$run.'.json', $bytes);
        file_put_contents($base.'/runtime-options.json', json_encode(['run' => $run, 'sha256' => hash('sha256', $bytes)]));
    };
    try {
        $write($data);
        expect((new RuntimeOptionPreview)->load())->toBe($data);
        file_put_contents($base.'/runtime-options-'.$run.'.json', '{}');
        expect((new RuntimeOptionPreview)->load())->toBeNull();
        $data['source_hashes']['app/Models/PersonDocumentType.php'] = str_repeat('0', 64);
        $write($data);
        expect((new RuntimeOptionPreview)->load())->toBeNull();
    } finally {
        app()->useStoragePath($previous);
        (new Filesystem)->deleteDirectory($tmp);
    }
});

it('shows raw observations safely without presenting a translation decision', function () {
    $html = view('translation-workbench::livewire.translation-workbench.tabs.source-usage.observed-values', ['runtimeOptions' => [
        'observed_at' => '2026-10-21', 'groups' => ['role_categories' => ['count' => 3, 'table' => 'roles', 'columns' => 'category',
            'selection' => 'is_assignable = true', 'rows' => [['value' => null], ['value' => ''], ['value' => '<script>alert(1)</script>']]]],
    ]])->render();
    expect($html)->toContain('Observed database option values', 'NULL', 'Empty string', 'not translations or DatabaseValue decisions')
        ->not->toContain('<script>alert(1)</script>');
});
