<?php

use Gunreip\TranslationWorkbench\Generation\TranslationKeySegments;

it('offers configured roots even without stored translations and only direct children thereafter', function () {
    $segments = new TranslationKeySegments(['ui', 'admin', 'unused']);
    $keys = [
        ['full_name' => 'ui.button.save'],
        ['full_name' => 'ui.button.cancel'],
        ['full_name' => 'ui.label.name'],
        ['full_name' => 'unconfigured.button.save'],
        ['full_name' => 'ui.private.save', 'namespace' => 'package'],
    ];
    expect($segments->options([]))->toBe(['ui', 'admin', 'unused']);
    expect($segments->options($keys))->toBe(['ui', 'admin', 'unused']);
    expect($segments->options($keys, 'ui'))->toBe(['button', 'label']);
    expect($segments->options($keys, 'ui.button'))->toBe(['cancel', 'save']);
    expect($segments->options($keys, 'unused'))->toBe([]);
    expect($segments->options($keys, 'unconfigured'))->toBe([]);
});

it('checks assigned key groups without restricting freely authored later segments', function () {
    $segments = new TranslationKeySegments(['ui', 'admin']);
    expect($segments->allows('ui.new_section.Custom_key-2'))->toBeTrue();
    expect($segments->allows('admin.any.new.path.save'))->toBeTrue();
    foreach (['packages.example.save', 'ui.Save', 'ui.Button.save', 'ui..save', 'package::ui.button.save'] as $key) {
        expect($segments->allows($key))->toBeFalse();
    }
});
