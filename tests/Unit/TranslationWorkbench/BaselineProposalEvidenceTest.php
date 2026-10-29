<?php

use Gunreip\TranslationWorkbench\Support\BaselineProposalEvidence;

function databaseProposalFixture(): array
{
    $published = ['full_name' => 'ui.type', 'locale' => 'en', 'path' => 'lang/en/ui.php', 'value' => 'Type', 'state' => 'blocked_structure'];
    $snapshot = ['baseline_hash' => 'baseline', 'items' => ['ui.type' => [
        'blocked_sources' => [['finding_id' => 42, 'path' => 'example.blade.php', 'line' => 3, 'column' => 4, 'expression' => "__('ui.type')"]],
        'locales' => ['en' => ['published' => $published]],
    ]]];
    $evidence = ['baseline_hash' => 'baseline', 'source' => 'ui.type', 'target' => 'ui.field.type',
        'sources' => [['id' => 42, 'source_path' => 'example.blade.php', 'source_line' => 3, 'source_column' => 4,
            'raw_expression' => "__('ui.type')", 'raw_source_code_literal' => 'ui.type', 'status' => 'active']],
        'published_values' => [$published]];

    return [$snapshot, $evidence];
}

it('compares exact finding identities positions expressions and published evidence', function () {
    [$snapshot, $evidence] = databaseProposalFixture();
    $fingerprint = hash('sha256', json_encode($evidence, JSON_THROW_ON_ERROR));
    $result = (new BaselineProposalEvidence)->compare($snapshot, 'ui.type', 'ui.field.type', ['evidence' => $evidence, 'fingerprint' => $fingerprint]);
    expect($result['status'])->toBe('matched')->and($result['finding_count'])->toBe(1)
        ->and($result['published_count'])->toBe(1)->and($result['database_fingerprint'])->toBe($fingerprint);
});

it('rejects divergent database evidence even when its own fingerprint is valid', function (string $change) {
    [$snapshot, $evidence] = databaseProposalFixture();
    switch ($change) {
        case 'baseline': $evidence['baseline_hash'] = 'another-generation';
            break;
        case 'target': $evidence['target'] = 'ui.other.type';
            break;
        case 'missing': $evidence['sources'] = [];
            break;
        case 'additional': $evidence['sources'][] = [...$evidence['sources'][0], 'id' => 43];
            break;
        case 'inactive': $evidence['sources'][0]['status'] = 'obsolete';
            break;
        case 'path': $evidence['sources'][0]['source_path'] = 'other.blade.php';
            break;
        case 'position': $evidence['sources'][0]['source_column']++;
            break;
        case 'expression': $evidence['sources'][0]['raw_expression'] = '__("ui.type")';
            break;
        case 'value': $evidence['published_values'][0]['value'] = 'Changed';
            break;
        case 'duplicate': $snapshot['items']['ui.type']['blocked_sources'][] = $snapshot['items']['ui.type']['blocked_sources'][0];
            break;
    }
    $context = ['evidence' => $evidence, 'fingerprint' => hash('sha256', json_encode($evidence, JSON_THROW_ON_ERROR))];
    expect(fn () => (new BaselineProposalEvidence)->compare($snapshot, 'ui.type', 'ui.field.type', $context))->toThrow(RuntimeException::class);
})->with(['baseline', 'target', 'missing', 'additional', 'inactive', 'path', 'position', 'expression', 'value', 'duplicate']);

it('rejects a tampered database context fingerprint', function () {
    [$snapshot, $evidence] = databaseProposalFixture();
    expect(fn () => (new BaselineProposalEvidence)->compare($snapshot, 'ui.type', 'ui.field.type',
        ['evidence' => $evidence, 'fingerprint' => str_repeat('0', 64)]))->toThrow(RuntimeException::class);
});
