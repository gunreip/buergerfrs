<?php

// tests/Feature/View/Components/Ui/Text/HighlightTest.php

use Illuminate\Support\Facades\Blade;

test('it highlights matching text case insensitive', function () {
    $html = Blade::render('<x-ui.text.highlight value="Germany" search="man" />');

    expect($html)->toContain('Ger<mark class="highlight">man</mark>y');
});

test('it keeps the original match casing', function () {
    $html = Blade::render('<x-ui.text.highlight value="GERMANY" search="man" />');

    expect($html)->toContain('GER<mark class="highlight">MAN</mark>Y');
});

test('it escapes html before highlighting', function () {
    $html = Blade::render('<x-ui.text.highlight value="<script>Germany</script>" search="Germany" />');

    expect($html)
        ->not->toContain('<script>')
        ->toContain('&lt;script&gt;<mark class="highlight">Germany</mark>&lt;/script&gt;');
});

test('it returns escaped text without marks when search is empty', function () {
    $html = Blade::render('<x-ui.text.highlight value="<strong>Germany</strong>" search="" />');

    expect($html)
        ->not->toContain('<strong>')
        ->not->toContain('<mark')
        ->toContain('&lt;strong&gt;Germany&lt;/strong&gt;');
});

test('it supports a custom mark class', function () {
    $html = Blade::render('<x-ui.text.highlight value="Germany" search="Germany" mark-class="rounded bg-yellow-200" />');

    expect($html)->toContain('<mark class="rounded bg-yellow-200">Germany</mark>');
});

test('it highlights case insensitive by default', function () {
    $html = Blade::render('<x-ui.text.highlight value="AD" search="ad" />');

    expect($html)->toContain('<mark class="highlight">AD</mark>');
});

test('it can highlight case sensitive', function () {
    $html = Blade::render('<x-ui.text.highlight value="AD" search="ad" case-sensitive />');

    expect($html)
        ->not->toContain('<mark')
        ->toContain('AD');
});
