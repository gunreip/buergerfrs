<?php

// Explicitly reviewed file extraction, not automatic cross-file identity guessing.
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTimelineRecorder;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchSourceFile;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Gunreip\TranslationWorkbench\Support\SourcePathSegmentFactory;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

if ($argc !== 1 && ($argc !== 3 || $argv[1] !== '--apply' || ! is_file($argv[2]))) {
    fwrite(STDERR, 'Usage: php '.$argv[0]." [--apply plan.json]\n"); exit(1);
}
$oldPath = 'packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-edit-translation-key.blade.php';
$newPath = 'packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/key-sources.blade.php';
$mapping = [8461 => [7, 'Source heading'], 8462 => [9, 'Source tooltip header'],
    7143 => [161, 'Open in VSC tooltip'], 7144 => [168, 'Open source in VS Code aria-label']];
$build = static function () use ($oldPath, $newPath, $mapping): array {
    $revision = '929f61a7';
    $git = new Process(['git', 'show', $revision.':'.$oldPath], base_path()); $git->mustRun();
    $historical = $git->getOutput(); $oldLines = explode("\n", $historical);
    $newLines = explode("\n", file_get_contents(base_path($newPath)));
    $scanner = app(TranslationWorkbenchScanner::class);
    $oldItems = $scanner->scanFile(base_path($oldPath)); $newItems = $scanner->scanFile(base_path($newPath));
    $source = TranslationWorkbenchSourceFile::where('path', $newPath)->first();
    $actions = [];
    foreach ($mapping as $id => [$line, $reason]) {
        $finding = TranslationWorkbenchFinding::findOrFail($id);
        $item = $newItems->filter(fn ($i) => $i->sourceLine === $line && $i->rawExpression === $finding->raw_expression
            && $i->kind === $finding->kind && $i->translationKey === $finding->found_translation_key)->sole();
        if ($finding->status !== 'active') { throw new RuntimeException('Finding is no longer active: '.$id); }
        if ($source && $finding->source_file_id === $source->id && $finding->source_signature === $item->sourceSignature) { continue; }
        if ($finding->sourceFile->path !== $oldPath || $oldItems->contains(fn ($i) => $i->expressionFingerprint === $finding->expression_fingerprint)
            || ! str_contains($oldLines[$finding->source_line - 1] ?? '', $finding->raw_expression)
            || TranslationWorkbenchFinding::where('source_signature', $item->sourceSignature)->where('status', '!=', 'obsolete')->exists()) {
            throw new RuntimeException('Source extraction evidence changed: '.$id);
        }
        $links = DB::table('translation_workbench_key_findings')->where('finding_id', $id)->orderBy('id')->get()->map(fn ($r) => (array) $r)->all();
        $active = collect($links)->where('status', 'active');
        if ($active->count() !== 1) { throw new RuntimeException('Expected one active assignment: '.$id); }
        $key = DB::table('translation_workbench_keys')->find($active->sole()['key_id']);
        if (! $key || $key->status === 'obsolete') { throw new RuntimeException('Assigned key is not current: '.$id); }
        $actions[] = ['id' => $id, 'reason' => $reason, 'before' => $finding->getAttributes(), 'links' => $links, 'key' => (array) $key,
            'after' => ['source_line' => $line, 'source_column' => $item->sourceColumn,
                'source_signature' => $item->sourceSignature, 'source_fingerprint' => $item->sourceFingerprint,
                'meta' => [...($finding->meta ?? []), 'source_relocation' => ['from_path' => $oldPath, 'to_path' => $newPath,
                    'expression_fingerprint' => $item->expressionFingerprint, 'revision' => $revision, 'reason' => $reason]]],
            'old_context' => array_slice($oldLines, max(0, $finding->source_line - 3), 5),
            'new_context' => array_slice($newLines, max(0, $line - 3), 5)];
    }
    return ['revision' => $revision, 'historical_sha256' => hash('sha256', $historical),
        'files' => [$oldPath => hash_file('sha256', base_path($oldPath)), $newPath => hash_file('sha256', base_path($newPath))],
        'target_source' => $source?->getAttributes(), 'actions' => $actions];
};
if ($argc === 1) {
    $result = DB::transaction(function () use ($build) {
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY'); return $build();
    });
} else {
    $plan = json_decode(file_get_contents($argv[2]), true, 512, JSON_THROW_ON_ERROR);
    $result = DB::transaction(function () use ($build, $plan, $newPath, $oldPath) {
        DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_timeline_events IN SHARE ROW EXCLUSIVE MODE');
        $fresh = $build();
        if ($fresh['actions'] === [] && $fresh['files'] === $plan['files']) { return ['state' => 'already_applied', 'actions' => 0]; }
        if ($fresh !== $plan) { throw new RuntimeException('Relocation plan is stale; no changes applied.'); }
        $timeline = app(TranslationWorkbenchTimelineRecorder::class);
        $source = TranslationWorkbenchSourceFile::where('path', $newPath)->first();
        if (! $source) {
            $source = TranslationWorkbenchSourceFile::create([...app(SourcePathSegmentFactory::class)->fromPath($newPath),
                'path' => $newPath, 'source_type' => 'blade', 'extension' => 'php', 'status' => 'active',
                'first_seen_at' => now(), 'last_seen_at' => now(), 'scan_count' => 0]);
            $timeline->record('source_file_discovered', newValues: $source->getAttributes(),
                context: ['source' => 'reviewed-source-relocation', 'source_path' => $newPath]);
        }
        $results = [];
        foreach ($plan['actions'] as $action) {
            $finding = TranslationWorkbenchFinding::findOrFail($action['id']);
            $after = ['source_file_id' => $source->id, ...$action['after']];
            $before = $finding->only(array_keys($after));
            $finding->forceFill($after)->save();
            $timeline->recordFindingEvent($finding, 'finding_source_relocated', $before, $after,
                ['source' => 'reviewed-source-relocation', 'from_path' => $oldPath, 'to_path' => $newPath,
                    'revision' => $plan['revision'], 'file_hashes' => $plan['files'], 'reason' => $action['reason']]);
            $results[] = ['finding_id' => $finding->id, 'source_file_id' => $source->id, 'line' => $finding->source_line,
                'column' => $finding->source_column, 'key_id' => $action['key']['id']];
        }
        foreach ($plan['files'] as $path => $hash) {
            if (hash_file('sha256', base_path($path)) !== $hash) { throw new RuntimeException('Source changed; relocation rolled back.'); }
        }
        return ['state' => 'applied', 'actions' => $results];
    });
}
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
