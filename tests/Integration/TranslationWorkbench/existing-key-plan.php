<?php

use Tests\Support\TranslationWorkbench\ExistingKeyPlan;

require dirname(__DIR__, 3).'/vendor/autoload.php';
$count = 0;
$check = static function ($ok) use (&$count): void {
    if (! $ok) {
        throw new RuntimeException('Key plan assertion failed: '.($count + 1));
    }
    $count++;
};
$language = static fn ($key) => ['key' => $key, 'locale' => 'en', 'value' => 'Same text', 'path' => 'lang/en/ui.php', 'sha256' => 'saved'];
$capture = static fn ($id, $key) => ['capture_id' => $id, 'reason' => 'key_with_exact_lang_evidence', 'found_translation_key' => $key,
    'source_context' => ['state' => 'confirmed_call'], 'raw_source_code_literal' => $key, 'raw_literal' => null, 'suggested_key' => null,
    'suggested_literal' => 'Same text', 'provenance' => ['suggested_literal' => ['path' => 'lang/en/ui.php', 'sha256' => 'saved', 'locale' => 'en', 'key' => $key]],
    'source_path' => 'view.blade.php', 'source_line' => 1, 'source_sha256' => 'source'];
$p = new ExistingKeyPlan;
$rows = [$capture('a', 'ui.button.save'), $capture('b', 'ui.button.save'), $capture('c', 'ui.other.save'), $capture('d', 'ui.type')];
$values = [$language('ui.button.save'), $language('ui.other.save'), $language('ui.type')];
$plan = $p->build($rows, $values);
$check($plan['key_count'] === 3 && $plan['eligible_keys'] === 2 && $plan['eligible_findings'] === 3);
$check(count($plan['items']) === 4 && $plan['blocked_keys'] === ['ui.type']);
$literal = [...$capture('e', 'ui.button.save'), 'reason' => 'literal_candidate', 'raw_literal' => 'Same text', 'suggested_key' => 'ui.suggested.save'];
$check(count($p->build([...$rows, $literal], $values)['items']) === 4);
$check($rows[0]['suggested_key'] === null && $rows[3]['found_translation_key'] === 'ui.type');
$cases = [[$rows, [...$values, $values[0]]], [[...$rows, $rows[0]], $values], [$rows, []]];
foreach (['suggested_literal' => 'Changed', 'raw_source_code_literal' => 'ui.other.save', 'suggested_key' => 'ui.changed.key'] as $field => $value) {
    $changed = $rows;
    $changed[0][$field] = $value;
    $cases[] = [$changed, $values];
}
foreach ($cases as [$c,$v]) {
    $blocked = false;
    try {
        $p->build($c, $v);
    } catch (RuntimeException) {
        $blocked = true;
    } $check($blocked);
}
echo $count." key-plan assertions passed\n";
