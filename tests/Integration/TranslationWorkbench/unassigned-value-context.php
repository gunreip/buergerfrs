<?php

use Tests\Support\TranslationWorkbench\UnassignedValueContext;

require dirname(__DIR__, 3).'/vendor/autoload.php';
$service = new UnassignedValueContext;
$source = ['capture_id' => 'a', 'source_path' => 'view.blade.php', 'source_line' => 12, 'source_sha256' => 'hash',
    'arguments' => ['owner' => 'ui.document.kind', 'identity' => ['expression' => '$kind'], 'fallback' => ['expression' => '$label']]];
$count = 0;
$check = static function ($ok) use (&$count): void {
    if (! $ok) {
        throw new RuntimeException('Context assertion failed: '.($count + 1));
    } $count++;
};
$r = $service->classify('ui.document.kind.options.invoice', [$source]);
$check($r['kind'] === 'option_source_evidenced' && $r['identity'] === 'invoice' && ! $r['runtime_identity_proven']);
$check($r['sources'][0]['path'] === 'view.blade.php' && $r['sources'][0]['fallback_expression'] === '$label');
$r = $service->classify('ui.document.kind.options.a%2Eb', [$source]);
$check($r['identity'] === 'a.b');
foreach (['ui.document.kinds.options.invoice', 'ui.document.kind.options.a.b', 'ui.document.kind.options.', 'ui.document.kind.options.a%2eb', 'ui.document.kind.options.%ZZ'] as $key) {
    $check($service->classify($key, [$source])['kind'] === 'source_assignment_open');
}
$r = $service->classify('ui.document.kind.options.invoice', [$source, [...$source, 'capture_id' => 'b']]);
$check(count($r['sources']) === 2);
$check($service->classify('ui.document.kind.options.invoice', [])['kind'] === 'source_assignment_open');
echo $count." context assertions passed\n";
