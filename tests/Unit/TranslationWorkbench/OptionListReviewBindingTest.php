<?php

use Tests\Support\TranslationWorkbench\OptionListReviewBinding;

function optionBindingFixture(): array
{
    $source = 'option-call';
    $capture = ['source_path' => 'view.php', 'source_sha256' => hash('sha256', $source), 'source_offset' => 0,
        'raw_expression' => $source, 'option_source' => ['owner_key' => 'ui.options.example']];
    $files = ['view.php' => $source, 'provider.php' => 'provider', 'constant.php' => 'constant'];
    $inventory = ['files' => array_map(fn ($bytes) => ['sha256' => hash('sha256', $bytes)], $files),
        'option_lists' => [['path' => 'provider.php', 'class' => 'Provider', 'property' => 'options', 'line' => 4,
            'source_sha256' => hash('sha256', 'provider'), 'complete_static_default' => true,
            'entries' => [['key' => 'a', 'value' => 'A', 'key_dependency' => ['path' => 'constant.php', 'source_sha256' => hash('sha256', 'constant')]]]]]];
    $bindings = ['view.php' => ['ui.options.example' => ['class' => 'Provider', 'property' => 'options']]];

    return [$capture, $inventory, $bindings, $files];
}

it('attaches only the explicitly reviewed source list', function () {
    [$capture, $inventory, $bindings, $files] = optionBindingFixture();
    $binder = new OptionListReviewBinding;
    $read = fn ($path) => $files[$path] ?? false;
    $result = $binder->bind($capture, $inventory, $bindings, $read);
    expect($result['state'])->toBe('reviewed_default_source')
        ->and($result['entries'][0]['value'])->toBe('A')
        ->and($result['runtime_exhaustiveness_proven'])->toBeFalse();
    $capture['source_path'] = 'another-view.php';
    expect($binder->bind($capture, $inventory, $bindings, $read))->toBeNull();
});

it('withholds values if the source provider or constant changed', function (string $path) {
    [$capture, $inventory, $bindings, $files] = optionBindingFixture();
    $files[$path] .= 'changed';
    $result = (new OptionListReviewBinding)->bind($capture, $inventory, $bindings, fn ($path) => $files[$path] ?? false);
    expect($result)->toBe(['state' => 'source_changed', 'entries' => []]);
})->with(['view.php', 'provider.php', 'constant.php']);

it('withholds ambiguous lists and mismatched expression positions', function () {
    [$capture, $inventory, $bindings, $files] = optionBindingFixture();
    $binder = new OptionListReviewBinding;
    $read = fn ($path) => $files[$path] ?? false;
    $inventory['option_lists'][] = $inventory['option_lists'][0];
    expect($binder->bind($capture, $inventory, $bindings, $read)['state'])->toBe('ambiguous_list');
    $capture['source_offset'] = 1;
    expect($binder->bind($capture, $inventory, $bindings, $read)['state'])->toBe('source_changed');
});
