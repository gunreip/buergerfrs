<?php

use Tests\Support\TranslationWorkbench\PendingCaptureReview;

require dirname(__DIR__, 3).'/vendor/autoload.php';
$review = new PendingCaptureReview;
$count = 0;
$check = static function (bool $ok) use (&$count): void {
    if (! $ok) {
        throw new RuntimeException('Review assertion failed: '.($count + 1));
    }
    $count++;
};
$row = static fn ($source, $expression, $reason = 'dynamic_or_nonliteral_argument') => [
    'capture_id' => 'fixture', 'source_path' => 'fixture.blade.php', 'source_line' => 1,
    'source_sha256' => hash('sha256', $source), 'raw_expression' => $expression, 'reason' => $reason,
];
$expression = '@js($value)';
$result = $review->review($row($expression, $expression), $expression, []);
$check($result['category'] === 'serialization_only' && $result['decision'] === null && ! $result['automatic_import']);
$source = <<<'SOURCE'
@props(['label' => __('ui.button.label')])
@js($label)
SOURCE;
$result = $review->review($row($source, '@js($label)'), $source, []);
$check($result['category'] === 'serialized_translated_prop' && $result['evidence']['prop_default']['value'] === 'ui.button.label');
$source = <<<'SOURCE'
@props(['text' => 'Default text'])
{{ __($text) }}
SOURCE;
$result = $review->review($row($source, '__($text)'), $source, []);
$check($result['category'] === 'overridable_prop_default' && $result['evidence']['prop_default']['value'] === 'Default text');
$source = <<<'SOURCE'
{{-- @props(['text' => 'Old default']) --}}
{{ __($text) }}
SOURCE;
$result = $review->review($row($source, '__($text)'), $source, []);
$check($result['category'] === 'runtime_translation_argument');
$expression = "__('Copied.')";
$result = $review->review($row($expression, $expression, 'key_like_argument_without_lang_evidence'), $expression, []);
$check($result['category'] === 'key_or_literal_review' && $result['decision'] === null);
$expression = '\\Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption::label(\'a.b\', $id, $label)';
$values = [['key' => 'a.b.options.x', 'value' => 'X'], ['key' => 'a.bc.options.x', 'value' => 'Wrong parent']];
$result = $review->review($row($expression, $expression, 'special_call_requires_context'), $expression, $values);
$check($result['category'] === 'explicit_option_owner' && count($result['evidence']['exact_option_values']) === 1);
$check($result['evidence']['owner_key'] === 'a.b' && ! $result['automatic_import']);
$rejected = false;
try {
    $review->review($row($expression, $expression), $expression.' ', []);
} catch (RuntimeException) {
    $rejected = true;
}
$check($rejected);
echo $count." review assertions passed\n";
