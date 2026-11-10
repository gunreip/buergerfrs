<?php

use Gunreip\TranslationWorkbench\Generation\ScanCacheMaintenance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

it('prunes only expired scan entries in the current database cache prefix', function () {
    config([
        'database.connections.scan_cleanup_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
        'cache.default' => 'scan_cleanup_test',
        'cache.stores.scan_cleanup_test' => ['driver' => 'database', 'connection' => 'scan_cleanup_test', 'table' => 'cache', 'prefix' => 'test_%_'],
    ]);
    Schema::connection('scan_cleanup_test')->create('cache', function ($table) {
        $table->string('key')->primary(); $table->text('value'); $table->integer('expiration');
    });
    $db = DB::connection('scan_cleanup_test')->table('cache');
    foreach ([
        ['test_%_workbench-ui-scan:old:plan', time() - 1],
        ['test_%_workbench-ui-scan:old:progress', time() - 1],
        ['test_%_workbench-ui-scan:running:plan', time() + 7200],
        ['test_%_unrelated', time() - 1],
        ['other_workbench-ui-scan:old:plan', time() - 1],
        ['test_AB_workbench-ui-scan:old:plan', time() - 1],
    ] as [$key, $expiration]) $db->insert(compact('key', 'expiration') + ['value' => '12345']);
    $maintenance = new ScanCacheMaintenance;
    expect($maintenance->summary())->toMatchArray(['supported' => true, 'entries' => 3, 'expired' => 2, 'bytes' => 15])
        ->and($maintenance->prune())->toBe(2)
        ->and($db->count())->toBe(4)
        ->and($maintenance->prune())->toBe(0);
});
