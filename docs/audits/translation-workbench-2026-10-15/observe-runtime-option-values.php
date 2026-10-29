<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Tests\Support\TranslationWorkbench\RuntimeOptionObservation;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
if (DB::connection()->getDriverName() !== 'pgsql') {
    throw new RuntimeException('PostgreSQL read-only transaction required');
}
$paths = ['app/Models/PersonDocumentType.php', 'app/Models/PersonDocument.php',
    'app/Livewire/Management/People/EditPerson.php', 'app/Livewire/Admin/UserList.php'];
$hashes = [];
foreach ($paths as $path) {
    $hashes[$path] = hash_file('sha256', $root.'/'.$path);
}
$result = DB::transaction(function () use ($hashes) {
    DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');

    return ['mode' => 'read_only_runtime_option_observation', 'observed_at' => gmdate(DATE_ATOM),
        'database_read_only' => true, 'source_hashes' => $hashes,
        'groups' => (new RuntimeOptionObservation)->collect(DB::connection()->getPdo())];
});
foreach ($hashes as $path => $hash) {
    if (hash_file('sha256', $root.'/'.$path) !== $hash) {
        throw new RuntimeException('Source changed during observation');
    }
}
$base = storage_path('app/private/workbench-previews');
$run = gmdate('Ymd-His').'-'.bin2hex(random_bytes(6));
$bytes = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
file_put_contents($base.'/runtime-options-'.$run.'.json', $bytes);
chmod($base.'/runtime-options-'.$run.'.json', 0644);
$pointer = $base.'/runtime-options-'.$run.'.tmp';
file_put_contents($pointer, json_encode(['run' => $run, 'sha256' => hash('sha256', $bytes)], JSON_THROW_ON_ERROR));
chmod($pointer, 0644);
rename($pointer, $base.'/runtime-options.json');
echo json_encode(['run' => $run, 'counts' => array_map(fn ($group) => $group['count'], $result['groups']), 'database_read_only' => true], JSON_THROW_ON_ERROR).PHP_EOL;
