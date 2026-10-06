<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewOverrideTree;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure;
use Tests\TestCase;

uses(TestCase::class);

it('excludes the reference template from loaded overrides and displayed source files', function () {
    $loaded = OverviewLayoutOverrides::load();
    expect($loaded['issues'])->toBe([]);
    expect(array_map('basename', $loaded['files']))->not->toContain('template.php');
});

it('documents every supported field for each representative level with valid types', function () {
    $template = require OverviewLayoutOverrides::directory().'/template.php';
    $schema = (new OverviewOverrideTree(OverviewStructure::data()))->schema();
    foreach ($template as $key => $values) {
        $file = $key === 'global' ? 'main-tabs.php' : $key.'.php';
        expect(OverviewLayoutOverrides::scope($file, [$key => $values])['issues'])->toBe([]);
    }
    $schema['deep-reference']['children'] = array_diff_key($schema['deep-reference']['children'], ['parts' => true]);
    $schema['deep-reference']['children']['strang']['children'] = array_filter(
        $schema['deep-reference']['children']['strang']['children'],
        static fn ($value, $key) => ! is_array($value) || in_array($key, ['global', 'flow-while'], true),
        ARRAY_FILTER_USE_BOTH,
    );
    // Parts here demonstrates only the alternative main connection; its subtree uses the full example above.
    $schema['parts'] = array_intersect_key($schema['parts'], $template['parts']);
    $schema['parts']['global'] = array_intersect_key($schema['parts']['global'], $template['parts']['global']);
    // Optional label-local marker override is documented on the leaf example.
    $shape = function (array $values) use (&$shape): array {
        $result = [];
        foreach ($values as $key => $value) {
            $result[$key] = is_array($value) ? $shape($value) : get_debug_type($value);
        }
        ksort($result);

        return $result;
    };
    expect($shape($template))->toBe($shape(array_intersect_key($schema, $template)));
});
