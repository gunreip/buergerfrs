<?php

// Diagnostic only: does not scan sources or invoke option providers.
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$pdo = Illuminate\Support\Facades\DB::connection()->getPdo();
if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
    throw new RuntimeException('This audit requires PostgreSQL READ ONLY transactions.');
}
$pdo->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
try {
    $rows = $pdo->query("SELECT o.id, o.status, o.source_path, o.source_line, o.scope, o.source_type,
        o.options_variable, o.key_variable, o.label_variable, o.matched_entry_id,
        o.meta->'foundation_assignment' AS foundation_assignment,
        o.meta->'foundation_assignment' IS NOT NULL AS has_assignment,
        (SELECT count(*) FROM translation_workbench_dynamic_sources d WHERE d.option_discovery_id=o.id) AS linked_sources,
        (SELECT count(*) FROM translation_workbench_dynamic_sources d WHERE d.option_discovery_id=o.id AND d.status='active') AS active_linked_sources
        FROM translation_workbench_option_discoveries o ORDER BY o.id")->fetchAll(PDO::FETCH_ASSOC);
    $counts = ['total' => count($rows), 'historical_active' => 0, 'historical_obsolete' => 0, 'assigned' => 0, 'historical_with_active_sources' => 0];
    foreach ($rows as $row) {
        if ($row['has_assignment']) {
            $counts['assigned']++;
        } else {
            $counts[$row['status'] === 'obsolete' ? 'historical_obsolete' : 'historical_active']++;
            $counts['historical_with_active_sources'] += (int) ($row['active_linked_sources'] > 0);
        }
    }
    $report = ['captured_at' => date(DATE_ATOM), 'transaction' => 'REPEATABLE READ READ ONLY', 'counts' => $counts, 'observations' => $rows];
} finally {
    $pdo->exec('ROLLBACK');
}
file_put_contents(__DIR__.'/option-observation-inventory.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
echo json_encode($counts).PHP_EOL;
