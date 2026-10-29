<?php

use Tests\Support\TranslationWorkbench\SourceArgumentEvidence;

require dirname(__DIR__, 3).'/vendor/autoload.php';
$reader = new SourceArgumentEvidence;
$count = 0;
$check = static function (bool $ok) use (&$count): void {
    if (! $ok) {
        throw new RuntimeException('Argument assertion failed: '.($count + 1));
    }
    $count++;
};
$call = '\\Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption::label';
$option = $reader->option($call."('a.b', (string) \$id, \$labels[\$id] ?? \$id)");
$check($option['owner'] === 'a.b');
$check($option['identity'] === ['expression' => '(string) $id', 'literal' => null, 'binding' => 'runtime_expression']);
$check($option['fallback']['expression'] === '$labels[$id] ?? $id' && $option['fallback']['literal'] === null);
$check($option['locale']['binding'] === 'helper_default');
$option = $reader->option($call."('a.b', 'a.b %', 'Text', 'de-AT')");
$check($option['identity']['literal'] === 'a.b %' && $option['fallback']['literal'] === 'Text' && $option['locale']['literal'] === 'de-AT');
$option = $reader->option($call."('a.b', '', '')");
$check($option['identity']['literal'] === '' && $option['fallback']['literal'] === '');
foreach ([$call."('a.b', ...\$args)", $call."(key: 'a.b', value: 'x', fallback: 'y')", $call."(\$key, 'x', 'y')", "Other::label('a.b', 'x', 'y')", $call."('a.b', 'x', 'y'); system('false')"] as $expression) {
    $blocked = false;
    try {
        $reader->option($expression);
    } catch (RuntimeException) {
        $blocked = true;
    }
    $check($blocked);
}
$source = <<<'SOURCE'
<?php
class Example {
    const LABELS = [1 => 'One', 2 => 'Two'];
    function produce() {
        // $this->conflict('Not code');
        $this->conflict('Fixed message');
        $this->conflict('Run '.$id.' changed');
        $other->conflict('Another receiver');
    }
    function badges() { return [['label' => 'Active'], ['label' => $unknown]]; }
}
SOURCE;
$values = $reader->producers($source, 'method_calls', 'conflict');
$check(count($values) === 2 && $values[0]['literal'] === 'Fixed message');
$check($values[1]['literal'] === null && $values[1]['expression'] === "'Run '.\$id.' changed'");
$check($values[0]['line'] === 6);
$values = $reader->producers($source, 'method_array_field', 'badges', 'label');
$check(count($values) === 2 && $values[0]['literal'] === 'Active' && $values[1]['literal'] === null);
$values = $reader->producers($source, 'constant_values', 'LABELS');
$check(array_column($values, 'literal') === ['One', 'Two']);
echo $count." argument assertions passed\n";
