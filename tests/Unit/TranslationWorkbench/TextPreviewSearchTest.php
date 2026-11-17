<?php

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('renders shared search controls inside the input and preserves its binding', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('<x-translation-workbench::ui.common.search-input wire:model.live="reviewSearch" :case-sensitive="true" case-action="toggleCase" word-action="toggleWord" />');
    expect($html)->toContain('wire:model.live="reviewSearch"', 'wire:click="toggleCase"', 'wire:click="toggleWord"', 'aria-pressed="true"', 'data-flux-clear-button');
});

it('applies matching modes without changing exact ID or language filters', function () {
    [$sql, $params] = \Gunreip\TranslationWorkbench\Generation\TranslationTaskFilter::build('Finding', '42', 'de', 'p.', true, true);
    expect($sql)->toBe('p.key_id=? AND p.full_name ~ ? AND p.locale=?');
    expect($params)->toBe(['42', '(?<![[:alnum:]])Finding(?![[:alnum:]])', 'de']);
});

it('highlights only the requested case and complete words or key segments', function () {
    $html = Blade::render('<x-translation-workbench::ui.common.text-preview text="finding Findings ui.Finding_label/Finding-end" search="Finding" :case-sensitive="true" :whole-word="true" :length="120" />');
    expect(substr_count($html, '<mark class="highlight">Finding</mark>'))->toBe(2);
    expect($html)->toContain('finding Findings ui.<mark');
});

it('looks past partial matches when choosing the visible search excerpt', function () {
    $html = Blade::render('<x-translation-workbench::ui.common.text-preview :text="$text" search="Finding" :whole-word="true" :length="25" :tooltip="false" />', [
        'text' => 'Findings '.str_repeat('before ', 30).'finding done',
    ]);
    expect($html)->toContain('<mark class="highlight">finding</mark>')->not->toContain('Findings');
});

it('treats punctuation as literal search text and does not search escaped HTML entities', function () {
    $html = Blade::render('<x-ui.text.highlight value="&amp; [Finding]" search="[Finding]" :whole-word="true" />');
    expect($html)->toContain('<mark class="highlight">[Finding]</mark>');
    expect(Blade::render('<x-ui.text.highlight :value="$value" search="amp" />', ['value' => '&']))->not->toContain('<mark');
});

it('builds bound PostgreSQL search patterns for each search mode', function () {
    $search = \Gunreip\TranslationWorkbench\Generation\FindingSearch::class;
    expect($search::condition('Finding'))->toBe(['ILIKE', '%Finding%']);
    expect($search::condition('Finding', true))->toBe(['LIKE', '%Finding%']);
    expect($search::condition('Finding', false, true))->toBe(['~*', '(?<![[:alnum:]])Finding(?![[:alnum:]])']);
    expect($search::condition('ui.[Finding]', true, true))->toBe(['~', '(?<![[:alnum:]])ui\\.\\[Finding\\](?![[:alnum:]])']);
    expect($search::condition('a_b%'))->toBe(['ILIKE', '%a\\_b\\%%']);
});

it('keeps a search match visible when the middle of a long value would be omitted', function () {
    $html = Blade::render('<x-translation-workbench::ui.common.text-preview :text="$text" search="finding" :length="30" side="center" :tooltip="false" />', [
        'text' => str_repeat('before/', 20).'Finding'.str_repeat('/after', 20),
    ]);

    expect($html)->toContain('<mark class="highlight">Finding</mark>')
        ->toContain('...')->not->toContain(str_repeat('before/', 20));
});

it('does not shorten a value when showing the entire search match saves too little space', function () {
    $text = 'prefix '.str_repeat('Finding', 8).' suffix';
    $html = Blade::render('<x-translation-workbench::ui.common.text-preview :text="$text" :search="$search" :length="20" />', [
        'text' => $text, 'search' => str_repeat('Finding', 8),
    ]);

    expect($html)->toContain('prefix <mark class="highlight">')
        ->toContain('</mark> suffix')->not->toContain('data-flux-tooltip');
});

it('highlights both the shortened preview and its safely escaped full text', function () {
    $html = Blade::render('<x-translation-workbench::ui.common.text-preview :text="$text" search="Finding" :length="30" />', [
        'text' => '<script>'.str_repeat('before ', 20).'Finding'.str_repeat(' after', 20).'</script>',
    ]);

    expect(substr_count($html, '<mark class="highlight">Finding</mark>'))->toBe(2);
    expect($html)->toContain('&lt;script&gt;')->not->toContain('<script>');
});
