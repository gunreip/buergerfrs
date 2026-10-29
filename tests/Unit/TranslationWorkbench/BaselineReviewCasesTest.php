<?php

use Gunreip\TranslationWorkbench\Support\BaselineReviewCases;
use Tests\TestCase;

uses(TestCase::class);

function reviewCasePreviewFixture(): array
{
    $backup = str_repeat('a', 64);
    $subject = 'historical:'.$backup.':translation_values:12';
    $e = ['source_table' => 'translation_values', 'source_id' => 12, 'key' => 'ui.button.save',
        'locale' => 'de', 'status' => 'obsolete', 'comparison' => 'different_from_published',
        'value_sha256' => hash('sha256', '<script>old</script>'), 'published_sha256' => hash('sha256', 'Neu'), 'published_path' => 'lang/de/ui.php'];
    ksort($e);
    $action = 'Compare before deciding';
    $version = hash('sha256', json_encode([json_encode($e, JSON_THROW_ON_ERROR), 'different_from_published', $action, true], JSON_THROW_ON_ERROR));

    return ['mode' => 'isolated_review_preview', 'cluster_stopped' => true, 'backup_manifest_sha256' => $backup,
        'dump_sha256' => str_repeat('b', 64), 'open_cases' => 1, 'total_cases' => 2, 'rows' => [[
            'id' => hash('sha256', $subject), 'version' => $version, 'subject' => $subject, 'state' => 'needs_review',
            'reason' => 'different_from_published', 'required_action' => $action, 'evidence' => $e,
            'historical_value' => '<script>old</script>', 'published_value' => 'Neu',
        ]]];
}

it('validates source identities, versions and exact original values', function () {
    $data = reviewCasePreviewFixture();
    expect((new BaselineReviewCases)->validate($data))->toBe($data);
});

it('rejects inconsistent snapshots instead of showing misleading review evidence', function (string $change) {
    $data = reviewCasePreviewFixture();
    match ($change) {
        'text' => $data['rows'][0]['historical_value'] = 'Changed',
        'published' => $data['rows'][0]['published_value'] = null,
        'version' => $data['rows'][0]['version'] = str_repeat('c', 64),
        'action' => $data['rows'][0]['required_action'] = 'Automatically approve',
        'backup' => $data['backup_manifest_sha256'] = str_repeat('c', 64),
        'duplicates' => $data['rows'][] = $data['rows'][0],
        'count' => $data['open_cases'] = 2,
        'state' => $data['rows'][0]['state'] = 'decided',
    };
    expect((new BaselineReviewCases)->validate($data))->toBeNull();
})->with(['text', 'published', 'version', 'action', 'backup', 'duplicates', 'count', 'state']);
