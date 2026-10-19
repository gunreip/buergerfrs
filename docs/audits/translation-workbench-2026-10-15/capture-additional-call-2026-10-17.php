<?php

// Reviewed, narrowly scoped use of the regular scanner and FoundationSyncer.
// Default: read-only plan. Apply: --apply /absolute/path/to/plan.json.
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFoundationSyncer;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOccurrenceMatcher;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\DB;

if ($argc !== 1 && ($argc !== 3 || $argv[1] !== '--apply' || ! is_file($argv[2]))) {
    fwrite(STDERR, "Usage: php ".$argv[0]." [--apply plan.json]\n");
    exit(1);
}
$path = 'packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-edit-translation-key.blade.php';
$signature = '0e4b8b1553463564086ec8f9890919e5bd14a1cbf6086dd3d51f4956a0308cc4';
$build = static function () use ($path, $signature): array {
    $items = app(TranslationWorkbenchScanner::class)->scanFile(base_path($path));
    $item = $items->where('sourceSignature', $signature)->sole();
    $stored = DB::table('translation_workbench_findings as f')
        ->join('translation_workbench_source_files as s', 's.id', '=', 'f.source_file_id')
        ->where('s.path', $path)->orderBy('f.id')->get(['f.*', 's.path as source_path']);
    $row = collect(app(TranslationWorkbenchOccurrenceMatcher::class)->compare($items, $stored))
        ->where('source_signature', $signature)->sole();
    $owners = DB::table('translation_workbench_keys')->where('translation_key', $item->translationKey)
        ->where('status', 'open')->where('review_status', 'reviewed')->get();
    if ($owners->count() !== 1 || $owners->sole()->id !== 473 || ! in_array($row['state'], ['new', 'unchanged'], true)) {
        throw new RuntimeException('Reviewed occurrence or owner is no longer unambiguous.');
    }
    return ['path' => $path, 'sha256' => hash_file('sha256', base_path($path)), 'occurrence' => $row,
        'owner' => (array) $owners->sole(), 'file_findings' => $stored->map(fn ($r) => (array) $r)->all()];
};
$hashTable = static function (string $table, ?int $excludeId = null): string {
    $hash = hash_init('sha256');
    foreach (DB::table($table)->when($excludeId !== null, fn ($q) => $q->where('id', '!=', $excludeId))->orderBy('id')->cursor() as $row) {
        hash_update($hash, json_encode($row, JSON_THROW_ON_ERROR));
    }
    return hash_final($hash);
};
if ($argc === 1) {
    $result = DB::transaction(function () use ($build) {
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
        return $build();
    });
} else {
    $plan = json_decode(file_get_contents($argv[2]), true, 512, JSON_THROW_ON_ERROR);
    $result = DB::transaction(function () use ($build, $hashTable, $plan, $path, $signature) {
        DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_key_values, translation_workbench_lang_values, translation_workbench_timeline_events IN SHARE ROW EXCLUSIVE MODE');
        $fresh = $build();
        if ($fresh['occurrence']['state'] === 'unchanged' && $fresh['sha256'] === $plan['sha256']) {
            $id = $fresh['occurrence']['finding_ids'][0];
            $links = DB::table('translation_workbench_key_findings')->where('finding_id', $id)->where('status', 'active')->get();
            if ($links->count() !== 1 || $links->sole()->key_id !== $fresh['owner']['id']) {
                throw new RuntimeException('Existing occurrence has a different assignment.');
            }
            return ['state' => 'already_applied', 'finding_id' => $id, 'actions' => 0];
        }
        if ($fresh !== $plan || $fresh['occurrence']['state'] !== 'new') {
            throw new RuntimeException('Plan is stale; no changes applied.');
        }
        $before = [];
        foreach (['findings', 'keys', 'key_findings', 'key_values', 'lang_values'] as $table) {
            $before[$table] = $hashTable('translation_workbench_'.$table);
        }
        $items = app(TranslationWorkbenchScanner::class)->scanFile(base_path($path))->where('sourceSignature', $signature)->values();
        $summary = app(TranslationWorkbenchFoundationSyncer::class)->sync($items);
        $finding = DB::table('translation_workbench_findings')->where('source_signature', $signature)->where('status', 'active')->sole();
        $link = DB::table('translation_workbench_key_findings')->where('finding_id', $finding->id)->where('status', 'active')->sole();
        if ($summary['findings_created'] !== 1 || $summary['keys_created'] !== 0 || $summary['relations_created'] !== 1
            || $link->key_id !== $plan['owner']['id']) {
            throw new RuntimeException('Unexpected capture result; all changes rolled back.');
        }
        foreach ($before as $table => $hash) {
            $exclude = match ($table) { 'findings' => $finding->id, 'key_findings' => $link->id, default => null };
            if ($hash !== $hashTable('translation_workbench_'.$table, $exclude)) {
                throw new RuntimeException('Existing '.$table.' changed; all changes rolled back.');
            }
        }
        if (hash_file('sha256', base_path($path)) !== $plan['sha256']) {
            throw new RuntimeException('Source changed during capture; all changes rolled back.');
        }
        return ['state' => 'applied', 'finding_id' => $finding->id, 'key_id' => $link->key_id, 'link_id' => $link->id,
            'line' => $finding->source_line, 'column' => $finding->source_column,
            'existing_records_unchanged' => true, 'summary' => $summary];
    });
}
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
