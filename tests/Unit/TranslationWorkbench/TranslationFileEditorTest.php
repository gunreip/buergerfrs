<?php

use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Gunreip\TranslationWorkbench\Generation\TranslationFileEditor;

it('normalizes formatting while preserving comments and unrelated values', function () {
    $source = <<<'SOURCE'
<?php
// Translation header
return [
    'button' => [
        // Translator hint
        'save' => 'Old',
        'cancel'  =>  'Keep', // intentionally formatted
    ],
];
// Footer comment
SOURCE;
    $tree = (new BaselineValueReader)->treeFromBytes($source, true);
    $tree['button']['save'] = 'New';
    $bytes = (new TranslationFileEditor)->render($source, $tree);
    expect((new BaselineValueReader)->treeFromBytes($bytes, true))->toBe($tree);
    foreach (['Translation header', 'Translator hint', 'intentionally formatted', 'Footer comment'] as $comment) {
        expect($bytes)->toContain($comment);
    }
    expect($bytes)->toContain("        'cancel' => 'Keep',");
    expect((new TranslationFileEditor)->render($bytes, $tree))->toBe($bytes);
});

it('adds nested entries and numeric option values without losing zero or empty values', function () {
    $source = "<?php return ['label' => ['keep' => 'Unchanged']];";
    $tree = ['label' => ['keep' => 'Unchanged', 'new' => "Quotes ' and \\ and :name"], 'choice' => ['options' => [0 => '0', 7 => '']]];
    $bytes = (new TranslationFileEditor)->render($source, $tree);
    expect((new BaselineValueReader)->treeFromBytes($bytes, true))->toBe($tree);
});

it('does not execute dynamic files or remove unrelated entries', function () {
    expect(fn () => (new TranslationFileEditor)->render('<?php return loadTranslations();', []))->toThrow(RuntimeException::class)
        ->and(fn () => (new TranslationFileEditor)->render("<?php return ['keep' => 'Value'];", []))->toThrow(RuntimeException::class);
});

it('renders new and legacy arrays in the same multiline short syntax', function () {
    $tree = ['button' => ['save' => 'Save', 'empty' => []]];
    $expected = "<?php\n\nreturn [\n    'button' => [\n        'save' => 'Save',\n        'empty' => [],\n    ],\n];\n";
    $editor = new TranslationFileEditor;
    expect($editor->render(null, $tree))->toBe($expected)
        ->and($editor->render("<?php return array('button' => array('save' => 'Save', 'empty' => array()));", $tree))->toBe($expected);
});

it('preserves multiline strings and literal escape sequences during formatting', function () {
    $tree = ['text' => ['value' => "First\nSecond\t'quoted' \\ :name", 'zero' => '0', 'empty' => '']];
    $editor = new TranslationFileEditor;
    $bytes = $editor->render(null, $tree);
    expect((new BaselineValueReader)->treeFromBytes($bytes, true))->toBe($tree)
        ->and($editor->render($bytes, $tree))->toBe($bytes);
});
