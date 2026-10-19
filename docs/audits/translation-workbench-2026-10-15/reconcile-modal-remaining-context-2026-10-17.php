<?php

// Explicit, reviewed historical-context repair. Default is a read-only plan; --apply requires its exact JSON file.
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTimelineRecorder;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Scanner\TranslationFingerprintFactory;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

$path = 'packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-edit-translation-key.blade.php';
$revision = '929f61a7';
// The contexts were compared against the historical file, not inferred from the new line ordering.
$mapping = [
    7038 => [104, 'Dynamic-confirmed badge'],
    7117 => [112, 'Dynamic-multi badge'],
    7119 => [183, 'Current-key heading'],
    7120 => [185, 'Current-key tooltip header'],
    7150 => [499, 'Current-key match selector'],
    7123 => [200, 'Existing-key heading'],
    7124 => [202, 'Existing-key tooltip header'],
    7157 => [560, 'Existing-key segment statistic'],
    7127 => [217, 'Found-key heading'],
    7128 => [219, 'Found-key tooltip header'],
    7134 => [246, 'Copy suggested key tooltip'],
    7135 => [256, 'Copy suggested key aria-label'],
    7137 => [264, 'Transform suggested key tooltip'],
    7138 => [274, 'Transform suggested key aria-label'],
    7165 => [327, 'Delete segment fallback'],
    7168 => [341, 'Restore segment fallback'],
    8819 => [351, 'Remove hash suffix aria-label'],
    8820 => [354, 'Remove hash suffix button caption'],
    7171 => [366, 'Scalar/container conflict heading'],
    7172 => [368, 'Scalar/container conflict tooltip header'],
    7145 => [477, 'Literal segment usage heading'],
    7146 => [479, 'Literal segment usage tooltip header'],
    7181 => [629, 'Segments heading'],
    7182 => [631, 'Segments tooltip header'],
    8245 => [194, 'CurrentTranslationKey missing fallback'],
    8246 => [228, 'FoundTranslationKey missing fallback'],
    8247 => [283, 'EffectiveSuggestedKey missing fallback'],
];

$build = static function () use ($path, $revision, $mapping): array {
    $process = new Process(['git', 'show', $revision.':'.$path], base_path());
    $process->mustRun();
    $historical = $process->getOutput();
    $oldLines = explode("\n", $historical);
    $current = file_get_contents(base_path($path));
    $newLines = explode("\n", $current);
    $items = app(TranslationWorkbenchScanner::class)->scanFile(base_path($path));
    $actions = [];
    foreach ($mapping as $id => [$line, $reason]) {
        $finding = TranslationWorkbenchFinding::findOrFail($id);
        $source = DB::table('translation_workbench_source_files')->find($finding->source_file_id);
        if ($source->path !== $path || $finding->status !== 'active') {
            throw new RuntimeException('Finding no longer active in the reviewed file: '.$id);
        }
        $matches = $items->filter(fn ($item) => $item->sourceLine === $line
            && $item->rawExpression === $finding->raw_expression
            && $item->kind === $finding->kind && $item->suggestedKey === $finding->suggested_key);
        if ($matches->count() !== 1) {
            throw new RuntimeException('Current expression no longer matches reviewed context: '.$id);
        }
        $item = $matches->sole();
        if ($finding->source_signature === $item->sourceSignature) {
            continue; // Repeat execution never updates timestamps or adds history.
        }
        $signature = app(TranslationFingerprintFactory::class)->sourceSignature($path, $finding->source_line,
            $finding->function_name, $finding->kind, $finding->suggested_key, $finding->raw_expression,
            $finding->literal_text, $finding->found_translation_key);
        if ($signature !== $finding->source_signature
            || ! str_contains($oldLines[$finding->source_line - 1] ?? '', $finding->raw_expression)) {
            throw new RuntimeException('Historical finding no longer matches the recorded source: '.$id);
        }
        $actions[] = [
            'id' => $id, 'reason' => $reason, 'before' => $finding->getAttributes(),
            'after' => ['source_line' => $line, 'source_column' => $item->sourceColumn, 'source_signature' => $item->sourceSignature, 'source_fingerprint' => $item->sourceFingerprint],
            'old_line_text' => $oldLines[$finding->source_line - 1],
            'new_line_text' => $newLines[$line - 1],
            'old_context' => array_slice($oldLines, max(0, $finding->source_line - 3), 5),
            'current_context' => array_slice($newLines, max(0, $line - 3), 5),
        ];
    }

    return ['path' => $path, 'revision' => $revision, 'historical_sha256' => hash('sha256', $historical),
        'current_sha256' => hash('sha256', $current), 'actions' => $actions];
};

if ($argc !== 1 && ($argc !== 3 || $argv[1] !== '--apply' || ! is_file($argv[2]))) {
    fwrite(STDERR, "Usage: php ".$argv[0]." [--apply plan.json]\n");
    exit(1);
}

if ($argc === 1) {
    $result = DB::transaction(function () use ($build) {
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
        return $build();
    });
} else {
    $plan = json_decode(file_get_contents($argv[2] ?? ''), true, 512, JSON_THROW_ON_ERROR);
    $result = DB::transaction(function () use ($build, $plan, $path) {
        DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_timeline_events IN SHARE ROW EXCLUSIVE MODE');
        $fresh = $build();
        if ($fresh['actions'] === [] && $fresh['current_sha256'] === $plan['current_sha256']) {
            return ['state' => 'already_applied', 'actions' => 0];
        }
        if ($fresh !== $plan) {
            throw new RuntimeException('Reviewed context plan is stale; no changes applied.');
        }
        $results = [];
        foreach ($plan['actions'] as $action) {
            $finding = TranslationWorkbenchFinding::findOrFail($action['id']);
            $before = $finding->only(array_keys($action['after']));
            $finding->forceFill($action['after'])->save();
            app(TranslationWorkbenchTimelineRecorder::class)->recordFindingEvent($finding, 'finding_position_reconciled',
                $before, $action['after'], ['source' => 'reviewed-historical-context', 'revision' => $plan['revision'],
                    'historical_sha256' => $plan['historical_sha256'], 'current_sha256' => $plan['current_sha256'], 'reason' => $action['reason']]);
            $results[] = ['id' => $finding->id, 'old_line' => $before['source_line'], 'new_line' => $finding->source_line];
        }
        if (hash_file('sha256', base_path($path)) !== $plan['current_sha256']) {
            throw new RuntimeException('Source changed during repair; all changes rolled back.');
        }
        return ['state' => 'applied', 'actions' => $results];
    });
}
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
