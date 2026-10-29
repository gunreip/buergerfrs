<?php

use Tests\Support\TranslationWorkbench\StaticOptionListEvidence;

it('keeps exact class property context and literal bytes without claiming runtime completeness', function () {
    $source = '<?php namespace Demo; class Other { public $options = ["x" => "Wrong"]; } class Subject { public $options = ["a.b" => " A  label ", "c" => "Other"]; }';
    $result = (new StaticOptionListEvidence)->property($source, 'Demo\Subject', 'options');
    expect(array_column($result['entries'], 'value'))->toBe([' A  label ', 'Other'])
        ->and($result['complete_static_default'])->toBeTrue()
        ->and($result['runtime_exhaustiveness_proven'])->toBeFalse()
        ->and($result['source_sha256'])->toBe(hash('sha256', $source));
});

it('retains unknown constants and producers without execution or guessing', function () {
    $source = '<?php class Subject { public $options = [Unknown::KEY => "Label"]; }';
    $result = (new StaticOptionListEvidence)->property($source, 'Subject', 'options');
    expect($result['complete_static_default'])->toBeFalse()
        ->and($result['entries'][0]['key'])->toBeNull()
        ->and($result['entries'][0]['expression'])->toBe('Unknown::KEY => "Label"');
    $result = (new StaticOptionListEvidence)->config('<?php return ["options" => ["x" => danger()]];', ['options']);
    expect($result['entries'][0]['state'])->toBe('requires_dependency_evidence');
});

it('selects the exact nested config list', function () {
    $result = (new StaticOptionListEvidence)->config('<?php return ["other" => ["x" => "Wrong"], "badge" => ["colors" => ["red" => "Red"]]];', ['badge', 'colors']);
    expect(array_column($result['entries'], 'value'))->toBe(['Red']);
});

it('rejects duplicate keys rather than silently overwriting evidence', function () {
    (new StaticOptionListEvidence)->config('<?php return ["red" => "One", "red" => "Two"];', []);
})->throws(RuntimeException::class, 'Duplicate option key');

it('rejects config spreads that could override the selected list', function () {
    (new StaticOptionListEvidence)->config('<?php return ["colors" => [], ...unknown()];', ['colors']);
})->throws(RuntimeException::class, 'Ambiguous config path');

it('resolves an imported constant from supplied source and retains its evidence', function () {
    $source = '<?php namespace View; use Model\Contact as C; class Subject { public $options = [C::PARENT => "Parent"]; }';
    $dependency = '<?php namespace Model; class Contact { public const PARENT = "parent"; }';
    $result = (new StaticOptionListEvidence)->property($source, 'View\Subject', 'options', ['Contact.php' => $dependency]);
    expect($result['complete_static_default'])->toBeTrue()
        ->and($result['runtime_exhaustiveness_proven'])->toBeFalse()
        ->and($result['entries'][0]['key'])->toBe('parent')
        ->and($result['entries'][0]['state'])->toBe('constant_key_pair')
        ->and($result['entries'][0]['key_dependency'])->toBe([
            'value' => 'parent', 'path' => 'Contact.php', 'source_sha256' => hash('sha256', $dependency),
            'class' => 'Model\Contact', 'constant' => 'PARENT', 'line' => 1,
        ]);
    $changed = (new StaticOptionListEvidence)->property($source, 'View\Subject', 'options', ['Contact.php' => str_replace('"parent"', '"guardian"', $dependency)]);
    expect($changed['entries'][0]['key'])->toBe('guardian')
        ->and($changed['entries'][0]['key_dependency']['source_sha256'])->not->toBe(hash('sha256', $dependency));
});

it('leaves unsupported constant definitions unresolved', function (string $dependency) {
    $source = '<?php class Subject { public $options = [\Model\Contact::PARENT => "Parent"]; }';
    $result = (new StaticOptionListEvidence)->property($source, 'Subject', 'options', ['Contact.php' => $dependency]);
    expect($result['complete_static_default'])->toBeFalse()
        ->and($result['entries'][0]['key'])->toBeNull();
})->with([
    '<?php namespace Model; class Contact { private const PARENT = "parent"; }',
    '<?php namespace Model; class Contact { public const PARENT = Other::PARENT; }',
    '<?php namespace Model; if (true) { class Contact { public const PARENT = "parent"; } }',
    '<?php namespace Model; class Contact extends Other {}',
]);

it('rejects collisions between constant and literal keys', function () {
    $source = '<?php class Subject { public $options = [\Model\Contact::PARENT => "Parent", "parent" => "Duplicate"]; }';
    (new StaticOptionListEvidence)->property($source, 'Subject', 'options', [
        'Contact.php' => '<?php namespace Model; class Contact { public const PARENT = "parent"; }',
    ]);
})->throws(RuntimeException::class, 'Duplicate option key');

it('rejects competing definitions of a dependency class', function () {
    $dependency = '<?php namespace Model; class Contact { public const PARENT = "parent"; }';
    (new StaticOptionListEvidence)->property('<?php class Subject { public $options = []; }', 'Subject', 'options', [
        'first.php' => $dependency, 'second.php' => $dependency,
    ]);
})->throws(RuntimeException::class, 'Ambiguous constant producer class');
