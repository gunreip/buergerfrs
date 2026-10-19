<?php

// Read-only inventory of historical observations and actual classifier/resolver selections.
use Gunreip\TranslationWorkbench\Console\ClassifyDynamicValues;
use Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector;
use Illuminate\Support\Facades\DB;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    if (DB::connection()->getDriverName() !== 'pgsql') { throw new RuntimeException('PostgreSQL required'); }
    $report = DB::transaction(function () {
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
        $observations = DB::table('translation_workbench_option_discoveries')->where('status', '<>', 'obsolete')->whereNull('meta->foundation_assignment')->orderBy('id')->get();
        $classifier = app(ClassifyDynamicValues::class);
        $resolver = app(ResolveUnknownDynamicSources::class);
        $invoke = fn ($object, $method, ...$args) => (new ReflectionMethod($object, $method))->invoke($object, ...$args);
        $classifierHits = []; $resolverHits = [];
        $classificationRows = $invoke($classifier, 'dynamicFindingRows');
        $resolverRows = $invoke($resolver, 'unknownSources');
        $keyAssignments = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicKeyAssignment::class);
        foreach ($classificationRows as $finding) {
            foreach ($invoke($classifier, 'matchingOptionDiscoveries', $finding) as $discovery) {
                $classifierHits[$discovery->id][] = ['finding_id' => $finding->finding_id, 'key_id' => $finding->key_id, 'path' => $finding->source_path];
            }
        }
        foreach ($resolverRows as $source) {
            $match = $invoke($resolver, 'bestDiscoveryForSource', $source);
            if ($match) { $resolverHits[$match->id][] = ['source_id' => $source->id, 'finding_id' => $source->finding_id, 'key_id' => $source->key_id]; }
        }
        $collector = app(TranslationWorkbenchDynamicOptionCollector::class);
        $arrays = $invoke($collector, 'hardcodedPublicArrayOptions');
        $groups = $arrays->groupBy('name')->map(fn ($items) => [
            'source_file' => $items->pluck('source_file')->implode(', '), 'options' => $items->count() === 1 ? $items->sole()['options'] : [],
            'source_candidates' => $items->values()->all(), 'source_resolution' => $items->count() === 1 ? 'single_candidate' : 'ambiguous_source',
        ]);
        $manifest = app(TranslationWorkbenchScanner::class)->scanWithManifest($observations->pluck('source_path')->unique()->all());
        $current = $invoke($collector, 'discoveries', $manifest['scanned_paths'], $groups);
        $rows = [];
        foreach ($observations as $o) {
            $matches = $current->filter(fn ($r) => $r['source_path'] === $o->source_path && $r['options_variable'] === $o->options_variable
                && $r['key_variable'] === $o->key_variable && $r['label_variable'] === $o->label_variable)->values();
            $linked = DB::table('translation_workbench_dynamic_sources')->where('option_discovery_id', $o->id)->get(['id', 'finding_id', 'key_id', 'status']);
            $calls = $manifest['items']->filter(function ($item) use ($o) {
                $context = $item->meta['dynamic_option_context'] ?? [];
                return $item->sourcePath === $o->source_path
                    && ($item->meta['source_context'] ?? null) !== 'commented_out'
                    && ($item->meta['translation_relevant'] ?? true) !== false
                    && ($context['options_variable'] ?? null) === $o->options_variable
                    && ($context['key_variable'] ?? null) === $o->key_variable
                    && ($context['label_variable'] ?? null) === $o->label_variable;
            })->map(function ($item) {
                $findings = DB::table('translation_workbench_findings as f')
                    ->join('translation_workbench_source_files as s', 's.id', '=', 'f.source_file_id')
                    ->where('s.path', $item->sourcePath)->where('s.status', 'active')->where('f.status', 'active')
                    ->where('f.raw_expression', $item->rawExpression)->get(['f.id', 'f.source_line', 'f.meta']);
                return ['line' => $item->sourceLine, 'expression' => $item->rawExpression,
                    'owner_key' => $item->meta['option_owner_key'] ?? null,
                    'context' => $item->meta['dynamic_option_context'],
                    'stored_findings' => $findings->map(function ($f) {
                        $meta = json_decode($f->meta ?? '{}', true);
                        return ['id' => $f->id, 'line' => $f->source_line,
                            'context' => $meta['dynamic_option_context'] ?? null,
                            'keys' => DB::table('translation_workbench_key_findings as l')
                                ->join('translation_workbench_keys as k', 'k.id', '=', 'l.key_id')
                                ->where('l.finding_id', $f->id)->where('l.status', 'active')->where('k.status', '<>', 'obsolete')
                                ->get(['k.id', 'k.translation_key'])->all()];
                    })->all()];
            })->values();
            $rows[] = ['id' => $o->id, 'path' => $o->source_path, 'variable' => $o->options_variable,
                'historical_label_usage' => $o->label_usage,
                'historical_options_count' => count(json_decode($o->options ?? '[]', true)),
                'scanned' => in_array($o->source_path, $manifest['scanned_paths'], true),
                'current_observations' => $matches->map(fn ($r) => ['line' => $r['source_line'], 'label_usage' => $r['label_usage'],
                    'resolution' => $r['source_resolution'], 'options_count' => count($r['options']),
                    'source_reference' => $r['source_reference']])->all(),
                'current_translation_calls' => $calls->all(),
                'classifier_consumers' => $classifierHits[$o->id] ?? [], 'resolver_consumers' => $resolverHits[$o->id] ?? [], 'linked_sources' => $linked->all()];
        }
        return ['mode' => 'read_only_candidate_selection_not_command_execution',
            'command_preflight_conflicts' => [
                'classify_dynamic_values' => $keyAssignments->conflicts($classificationRows->pluck('finding_id')->all())->all(),
                'resolve_unknown_dynamic_sources' => $keyAssignments->conflicts($resolverRows->pluck('finding_id')->all())->all(),
            ],
            'file_hashes' => $manifest['file_hashes'], 'observations' => $rows,
            'summary' => ['historical_observations' => count($rows), 'with_classifier_consumers' => collect($rows)->filter(fn ($r) => $r['classifier_consumers'] !== [])->count(),
                'with_resolver_consumers' => collect($rows)->filter(fn ($r) => $r['resolver_consumers'] !== [])->count(),
                'currently_recognized' => collect($rows)->filter(fn ($r) => $r['current_observations'] !== [])->count()]];
    });
    file_put_contents(__DIR__.'/remaining-option-consumers.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode($report['summary']).PHP_EOL;
} catch (Throwable $error) { fwrite(STDERR, $error->getMessage().PHP_EOL); exit(1); }
