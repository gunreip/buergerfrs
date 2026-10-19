<?php

// Production preflight only. Database transaction explicitly prohibits writes.
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOptionSourceTransition;
use Illuminate\Support\Facades\DB;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$payload = json_decode(file_get_contents(__DIR__.'/option-payload-review.json'), true, flags: JSON_THROW_ON_ERROR);
if ($payload['plan_sha256'] !== hash_file('sha256', __DIR__.'/option-transition-plan.json')) {
    throw new RuntimeException('Payload review does not match the current transition plan.');
}
$plan = json_decode(file_get_contents(__DIR__.'/option-transition-plan.json'), true, flags: JSON_THROW_ON_ERROR);
foreach ($payload['source_hashes'] as $path => $hash) {
    if (hash_file('sha256', $root.'/'.$path) !== $hash) {
        throw new RuntimeException('Changed option source: '.$path);
    }
}
$requests = [];
foreach ($payload['sources'] as $source) {
    if ($source['comparison'] !== 'identical' || count($source['target_finding_ids']) !== 1) {
        throw new RuntimeException('Source requires review: '.$source['source_id']);
    }
    $observation = collect($plan['observations'])->firstWhere('observation_id', $source['observation_id']);
    $requests[] = ['source_id' => $source['source_id'], 'old_finding_id' => $source['finding_id'],
        'target_finding_id' => $source['target_finding_ids'][0], 'key_id' => $source['key_id'],
        'options' => $source['current_payloads'][0]['values'], 'file_hash' => $plan['file_hashes'][$observation['source_path']]];
}
if (DB::connection()->getDriverName() !== 'pgsql') {
    throw new RuntimeException('PostgreSQL READ ONLY transaction required.');
}
$result = DB::transaction(function () use ($requests) {
    DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    return app(TranslationWorkbenchOptionSourceTransition::class)->process($requests);
});
file_put_contents(__DIR__.'/option-source-transition-preflight.json', json_encode(['mode' => 'read_only', 'requests' => $requests, 'result' => $result], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
echo json_encode(array_count_values(array_column($result, 'status'))).PHP_EOL;
