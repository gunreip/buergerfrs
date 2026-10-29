<?php

use Tests\Support\TranslationWorkbench\CaptureSourceRole;

require dirname(__DIR__, 3).'/vendor/autoload.php';
$model = new CaptureSourceRole;
$count = 0;
$check = static function (bool $ok) use (&$count): void {
    if (! $ok) {
        throw new RuntimeException('Role assertion failed: '.($count + 1));
    }
    $count++;
};
$row = ['capture_id' => 'one', 'source_path' => 'fixture.blade.php', 'source_sha256' => 'hash',
    'raw_expression' => '@js($text)', 'reason' => 'dynamic_or_nonliteral_argument', 'source_context' => ['state' => 'confirmed_call']];
$review = [...$row, 'original_reason' => $row['reason'], 'decision' => null, 'automatic_import' => false,
    'category' => 'serialized_translated_prop', 'evidence' => ['prop_default' => ['value' => 'Existing translation']]];
$before = [$row, $review];
$result = $model->classify($row, $review);
$check($result['role'] === 'value_usage' && $result['state'] === 'pending');
$check([$row, $review] === $before);
foreach (['serialization_only' => 'value_usage', 'explicit_option_owner' => 'option_source',
    'runtime_translation_argument' => 'translation_source', 'overridable_prop_default' => 'translation_source',
    'key_or_literal_review' => 'translation_source'] as $category => $role) {
    $check($model->classify($row, [...$review, 'category' => $category])['role'] === $role);
}
$check($model->classify([...$row, 'reason' => 'literal_candidate'], null)['state'] === 'captured');
$check($model->classify([...$row, 'reason' => 'commented_source'], null)['role'] === 'excluded');
foreach ([[$row, null], [$row, [...$review, 'source_sha256' => 'changed']],
    [$row, [...$review, 'capture_id' => 'another']], [$row, [...$review, 'category' => 'unknown']],
    [$row, [...$review, 'decision' => 'approved']],
    [[...$row, 'source_context' => ['state' => 'unresolved']], $review]] as [$capture, $evidence]) {
    $blocked = false;
    try {
        $model->classify($capture, $evidence);
    } catch (RuntimeException) {
        $blocked = true;
    }
    $check($blocked);
}
echo $count." role assertions passed\n";
