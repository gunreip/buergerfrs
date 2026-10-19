<?php

// Read-only audit of the reviewed transition candidates. No render/provider invocation.
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchOptionViewBinding;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptions;
use Illuminate\Support\Facades\DB;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$plan = json_decode(file_get_contents(__DIR__.'/option-transition-plan.json'), true, flags: JSON_THROW_ON_ERROR);
foreach ($plan['file_hashes'] as $path => $hash) {
    if (! is_file($root.'/'.$path) || hash_file('sha256', $root.'/'.$path) !== $hash) {
        throw new RuntimeException('Refresh the transition plan; changed file: '.$path);
    }
}
$pdo = DB::connection()->getPdo();
if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
    throw new RuntimeException('PostgreSQL READ ONLY transaction required.');
}
$normalize = static function (array $values): array { ksort($values); return $values; };
$pdo->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
try {
    $rows = [];
    $files = [];
    foreach ($plan['observations'] as $entry) {
        foreach ($entry['linked_sources'] as $sourceEvidence) {
            if ($sourceEvidence['status'] !== 'active' || $sourceEvidence['identity_review'] !== 'same_option_context_changed_expression') {
                continue;
            }
            $observation = DB::table('translation_workbench_option_discoveries')->find($entry['observation_id']);
            $source = DB::table('translation_workbench_dynamic_sources')->find($sourceEvidence['id']);
            if (! $observation || ! $source || $source->status !== 'active'
                || $source->finding_id !== $sourceEvidence['finding_id'] || $source->key_id !== $sourceEvidence['key_id']
                || $source->option_discovery_id !== $observation->id) {
                throw new RuntimeException('Refresh transition plan; source changed: '.$sourceEvidence['id']);
            }
            $payloads = [];
            foreach (explode(', ', $observation->source_reference ?? '') as $reference) {
                $binding = (new TranslationWorkbenchOptionViewBinding)->inspect($reference, $entry['source_path'], $entry['options_variable']);
                if ($binding['status'] !== 'static_view_path') {
                    continue;
                }
                [$path, $method] = array_pad(explode(':', $reference, 2), 2, null);
                $files[$path] = hash_file('sha256', $root.'/'.$path);
                $code = file_get_contents($root.'/'.$path);
                $values = null;
                $basis = null;
                // Only the two previously inspected config readers; do not execute providers.
                $configLeaf = match ($method) {
                    'roleUserManagementBadgeColors' => 'colors',
                    'roleUserManagementBadgeVariants' => 'variants',
                    default => null,
                };
                if ($path === 'app/Livewire/Admin/RoleList.php' && $configLeaf !== null) {
                    $basis = 'config:buergerfrs-icons.categories.role_user_management.badge.'.$configLeaf;
                    $values = config(substr($basis, 7));
                } elseif ($method === null && preg_match('/namespace\s+([^;]+);/', $code, $namespace)
                    && preg_match('/\bclass\s+(\w+)/', $code, $class)) {
                    $reflection = new ReflectionClass(trim($namespace[1]).'\\'.$class[1]);
                    $values = $reflection->getDefaultProperties()[$entry['options_variable']] ?? null;
                    $basis = 'declared_property_default';
                }
                if (is_array($values)) {
                    $payloads[] = ['reference' => $reference, 'basis' => $basis, 'values' => $normalize($values)];
                }
            }
            $stored = $normalize(json_decode($observation->options ?? '{}', true, flags: JSON_THROW_ON_ERROR));
            $sourceValues = DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $source->id)->get();
            $activeValues = $normalize($sourceValues->where('status', 'active')->pluck('native_label', 'value_key')->all());
            $candidates = DB::table('translation_workbench_dynamic_source_candidates')
                ->where('dynamic_source_id', $source->id)
                ->orWhere('candidate_reference', 'dynamic_source:'.$source->id)
                ->orWhere('meta->related_source_id', $source->id)->get();
            $current = count($payloads) === 1 ? $payloads[0]['values'] : null;
            $targetFinding = $entry['current_calls'][0]['finding_ids'][0];
            $resolver = app(TranslationWorkbenchDynamicOptions::class);
            $editorRows = $resolver->rows($source->key_id, $targetFinding, ['source' => 'en', 'active' => 'de']);
            $rows[] = ['source_id' => $source->id, 'observation_id' => $observation->id, 'key_id' => $source->key_id,
                'finding_id' => $source->finding_id, 'target_finding_ids' => $entry['current_calls'][0]['finding_ids'],
                'current_payloads' => $payloads, 'observation_values' => $stored, 'active_source_values' => $activeValues,
                'comparison' => $current === null ? 'unresolved_current_payload' : ($stored === $current && $activeValues === $current ? 'identical' : 'different'),
                'source_value_statuses' => $sourceValues->countBy('status')->all(),
                'editor_baseline_en_de' => $editorRows,
                'editor_linked_runtime_source_ids' => $resolver->linkedRuntimeSourceIdsForFinding((object) ['id' => $targetFinding, 'key_id' => $source->key_id]),
                'candidate_links' => $candidates->map(fn ($row) => [
                    'id' => $row->id, 'dynamic_source_id' => $row->dynamic_source_id, 'finding_id' => $row->finding_id,
                    'reference' => $row->candidate_reference, 'status' => $row->status, 'review_status' => $row->review_status,
                ])->all(),
                'stored_translation_count_for_key' => DB::table('translation_workbench_dynamic_key_values')->where('key_id', $source->key_id)->count()];
        }
    }
    $report = ['captured_at' => date(DATE_ATOM), 'mode' => 'read_only_payload_and_direct_link_audit',
        'transaction' => 'REPEATABLE READ READ ONLY', 'plan_sha256' => hash_file('sha256', __DIR__.'/option-transition-plan.json'),
        'source_hashes' => $files, 'summary' => collect($rows)->countBy('comparison')->all(), 'sources' => $rows];
} finally {
    $pdo->exec('ROLLBACK');
}
file_put_contents(__DIR__.'/option-payload-review.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL);
echo json_encode($report['summary']).PHP_EOL;
