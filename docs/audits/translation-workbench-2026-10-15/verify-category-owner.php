<?php
// Independent post-commit verification; PostgreSQL READ ONLY, no application writers.
use Illuminate\Support\Facades\DB;
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$require = function (bool $ok, string $message): void { if (! $ok) { throw new RuntimeException($message); } };
try {
    $report = json_decode(file_get_contents(__DIR__.'/category-owner-applied.json'), true, flags: JSON_THROW_ON_ERROR);
    $before = json_decode(file_get_contents($report['backup']), true, flags: JSON_THROW_ON_ERROR);
    $require(DB::connection()->getDriverName() === 'pgsql', 'PostgreSQL required');
    $result = DB::transaction(function () use ($report, $before, $require) {
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
        foreach ($report['retained_tables'] as $table) {
            $current = DB::table('translation_workbench_'.$table)->orderBy('id')->get()->mapWithKeys(fn ($r) => [$r->id => (array) $r])->all();
            $require($current === $before[$table], 'Retained table changed: '.$table);
        }
        $old = DB::table('translation_workbench_findings')->find(2145);
        $new = DB::table('translation_workbench_findings')->find($report['new_finding_id']);
        $require($old->status === 'obsolete' && $new->status === 'active', 'Unexpected finding state');
        $require(DB::table('translation_workbench_key_findings')->where('finding_id', 2145)->where('status', 'active')->count() === 0, 'Old active links remain');
        $links = DB::table('translation_workbench_key_findings')->where('finding_id', $new->id)->where('status', 'active')->pluck('key_id')->all();
        $require($links === [3267], 'Wrong current owner');
        $require(DB::table('translation_workbench_keys')->where('id', 3188)->value('status') === 'obsolete', 'Old key not historical');
        $require((array) DB::table('translation_workbench_keys')->find(3267) === $before['keys'][3267], 'Current key changed');
        $require(hash_file('sha256', base_path($report['source_path'])) === $report['file_hash'], 'Source changed');
        return ['mode' => 'read_only_verified', 'new_finding_id' => $new->id, 'owner_key_id' => 3267,
            'retained_tables' => $report['retained_tables'], 'source_hash_verified' => true];
    });
    file_put_contents(__DIR__.'/category-owner-verified.json', json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode($result).PHP_EOL;
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
