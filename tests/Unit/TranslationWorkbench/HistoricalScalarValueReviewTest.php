<?php

use Tests\Support\TranslationWorkbench\HistoricalScalarValueReview;

it('compares exact identities without fallback or automatic decisions', function (array $changes, string $comparison) {
    $row = array_replace([
        'source_id' => 42, 'key' => 'ui.button.save', 'locale' => 'de',
        'namespace' => '', 'value_type' => 'text', 'value' => 'Speichern', 'status' => 'obsolete',
    ], $changes);
    $published = [json_encode(['de', 'ui.button.save']) => ['value' => 'Speichern', 'path' => 'lang/de/ui.php']];
    $result = (new HistoricalScalarValueReview)->classify($row, $published);
    expect($result['comparison'])->toBe($comparison)
        ->and($result['decision'])->toBeNull()
        ->and($result['status'])->toBe('obsolete')
        ->and($result['source_id'])->toBe(42)
        ->and($result)->not->toHaveKey('value');
})->with([
    'identical obsolete evidence is not approval' => [[], 'exact_published_match'],
    'changed' => [['value' => 'Sichern'], 'different_from_published'],
    'no trimming' => [['value' => 'Speichern '], 'different_from_published'],
    'no locale fallback' => [['locale' => 'de-at'], 'no_exact_published_evidence'],
    'no text based key match' => [['key' => 'ui.action.save'], 'no_exact_published_evidence'],
    'namespace is file segment' => [['namespace' => 'ui'], 'exact_published_match'],
    'namespace disagrees with key' => [['namespace' => 'package'], 'namespace_key_mismatch'],
    'package namespace excluded' => [['key' => 'package::ui.button.save'], 'namespace_outside_scope'],
    'missing identity' => [['key' => null], 'identity_unresolved'],
    'null is not empty string' => [['value' => null], 'no_scalar_value'],
    'structured value' => [['value_type' => 'array'], 'non_scalar_type'],
]);

it('preserves empty and zero strings as exact evidence', function (string $value) {
    $result = (new HistoricalScalarValueReview)->classify(
        ['key' => 'ui.value.zero', 'locale' => 'en', 'value' => $value],
        [json_encode(['en', 'ui.value.zero']) => ['value' => $value, 'path' => 'lang/en/ui.php']],
    );
    expect($result['comparison'])->toBe('exact_published_match')
        ->and($result['value_sha256'])->toBe(hash('sha256', $value))
        ->and($result['published_sha256'])->toBe($result['value_sha256']);
})->with(['', '0']);
