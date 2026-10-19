<?php

// Read-only classification. Equal key text is never a consolidation decision.
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation;

try {
    $result = DB::transaction(function () {
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
        $links = DB::table('translation_workbench_key_findings as l')->join('translation_workbench_findings as f', 'f.id', '=', 'l.finding_id')
            ->where('l.status', 'active')->where('f.status', 'active')->orderBy('l.key_id')->get(['l.*']);
        $groups = $links->groupBy('finding_id')->filter(fn ($rows) => $rows->count() > 1);
        $keyIds = $groups->flatten(1)->pluck('key_id')->unique();
        $keys = DB::table('translation_workbench_keys')->whereIn('id', $keyIds)->orderBy('id')->get()->keyBy('id');
        $reviews = DB::table('translation_workbench_reviews')->whereIn('key_id', $keyIds)->orderBy('id')->get()->groupBy('key_id');
        $findings = DB::table('translation_workbench_findings as f')->join('translation_workbench_source_files as s', 's.id', '=', 'f.source_file_id')
            ->whereIn('f.id', $groups->keys())->orderBy('f.id')->get(['f.id', 'f.source_line', 'f.literal_text', 'f.suggested_key', 's.path'])->keyBy('id');
        $report = app(TranslationWorkbenchSourceReconciliation::class)->plan($findings->pluck('path')->unique()->values()->all());
        $rows = collect($report['rows'])->filter(fn ($r) => count($r['finding_ids']) === 1)->keyBy(fn ($r) => $r['finding_ids'][0]);
        $cases = $groups->map(function ($links, $findingId) use ($keys, $reviews, $findings, $rows) {
            $owners = $links->map(function ($link) use ($keys, $reviews) {
                $key = $keys[$link->key_id]; $history = $reviews->get($key->id) ?? collect();
                return ['key_id' => $key->id, 'translation_key' => $key->translation_key, 'suggested_key' => $key->suggested_key,
                    'status' => $key->status, 'review_status' => $key->review_status, 'reviewed_at' => $key->reviewed_at,
                    'link_id' => $link->id, 'review_count' => $history->count(),
                    'reviews' => $history->map(fn ($r) => ['id' => $r->id, 'type' => $r->review_type, 'decision' => $r->decision, 'reviewed_at' => $r->reviewed_at])->values()->all()];
            });
            $same = $owners->pluck('translation_key')->unique()->count() === 1;
            return ['finding' => (array) $findings[$findingId], 'source_state' => $rows->get($findingId)['state'] ?? 'unresolved',
                'classification' => ! $same ? 'different_translation_keys' : ($owners->every(fn ($k) => $k['review_status'] === 'reviewed') ? 'multiple_reviewed_records' : 'reviewed_and_pending_records'),
                'owners' => $owners->all()];
        })->values();
        $texts = $keys->pluck('translation_key')->filter()->unique();
        return ['read_only' => true, 'multiple_findings' => $cases->count(), 'classification_counts' => $cases->countBy('classification')->all(),
            'cases' => $cases->all(), 'stored_language_value_counts' => DB::table('translation_workbench_lang_values')->whereIn('translation_key', $texts)
                ->selectRaw('translation_key, status, count(*) as count')->groupBy('translation_key', 'status')->orderBy('translation_key')->get()->all()];
    });
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL;
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
