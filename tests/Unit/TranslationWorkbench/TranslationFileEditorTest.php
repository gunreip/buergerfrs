<?php

use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Gunreip\TranslationWorkbench\Generation\TranslationFileEditor;

it('changes only the selected literal while preserving comments and formatting', function () {
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
    expect((new TranslationFileEditor)->render($source, $tree))->toBe(str_replace("'Old'", "'New'", $source));
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
