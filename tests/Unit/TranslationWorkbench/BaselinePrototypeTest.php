<?php

use Gunreip\TranslationWorkbench\Scanner\DiscoveredTranslation;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\File;
use Tests\Support\TranslationWorkbench\BaselinePrototype;
use Tests\TestCase;

uses(TestCase::class);

function baselineItem(string $text = 'Cancel', int $line = 1, string $kind = 'literal', string $path = 'resources/views/example.blade.php'): DiscoveredTranslation
{
    return new DiscoveredTranslation(
        kind: $kind, sourceType: 'blade', sourcePath: $path, sourceLine: $line, functionName: '__',
        rawExpression: '__('.var_export($text, true).')', literalText: $kind === 'literal' ? $text : null,
        literalTextSuggested: 'untrusted guessed label', translationKey: $kind === 'key' ? $text : null,
        translationKeySource: $kind === 'key' ? 'code' : null, existingKey: $kind === 'key' ? $text : null,
        suggestedKey: $kind === 'key' ? $text : 'ui.example.cancel', namespace: 'ui', group: 'example',
        sourceSignature: hash('sha256', $path.':'.$line), fingerprint: hash('sha256', $text),
        sourceFingerprint: 'fixture', expressionFingerprint: hash('sha256', $text), semanticFingerprint: hash('sha256', $text), sourceColumn: 1,
    );
}

function baselineFiles(): array
{
    return ['resources/views/example.blade.php' => hash('sha256', 'test fixture')];
}

it('starts empty and confirms unchanged observations without creating history or keys for literals', function () {
    $db = new BaselinePrototype;
    expect($db->rows('events'))->toBe([]);
    $items = [baselineItem()];
    $db->scan('first', $items, baselineFiles());
    $capture = $db->rows('findings');
    $events = $db->rows('events');
    $db->scan('second', $items, baselineFiles());
    expect($db->rows('findings'))->toBe($capture)->and($db->rows('events'))->toBe($events)
        ->and($db->rows('runs'))->toHaveCount(2)->and($db->rows('observations'))->toHaveCount(2)
        ->and($db->rows('keys'))->toBe([])
        ->and($capture[0]['raw_literal'])->toBe('Cancel')
        ->and($capture[0]['suggested_literal'])->toBeNull()
        ->and($events[0]['actor_type'])->toBe('scanner')
        ->and($events[0]['reason'])->toBe('observed_in_source');
});

it('captures existing keys with proven source-language text and preserves its snapshot', function () {
    $db = new BaselinePrototype;
    $items = [baselineItem('ui.button.cancel', kind: 'key')];
    $evidence = ['ui.button.cancel' => [['value' => 'Cancel', 'locale' => 'en', 'source' => 'lang_file', 'reference' => 'baseline-A:lang/en/ui.php:sha256-example']]];
    $db->scan('first', $items, baselineFiles(), $evidence);
    $capture = $db->rows('findings')[0];
    expect($capture['raw_source_code_literal'])->toBe('ui.button.cancel')->and($capture['raw_literal'])->toBeNull()
        ->and($capture['suggested_literal'])->toBe('Cancel')
        ->and(json_decode($capture['provenance'], true)['evidence'])->toBe($evidence['ui.button.cancel'])
        ->and($db->rows('links'))->toHaveCount(1);
    $evidence['ui.button.cancel'][0]['value'] = 'Changed translation';
    $db->scan('second', $items, baselineFiles(), $evidence);
    expect($db->rows('findings')[0])->toBe($capture)->and($db->rows('events'))->toHaveCount(1);
});

it('does not invent a suggestion from missing, foreign-language, container or conflicting values', function (array $evidence) {
    $db = new BaselinePrototype;
    $db->scan('first', [baselineItem('ui.button.cancel', kind: 'key')], baselineFiles(), ['ui.button.cancel' => $evidence]);
    expect($db->rows('findings')[0]['suggested_literal'])->toBeNull()->and($db->rows('findings')[0]['provenance'])->toBeNull();
})->with([
    'missing' => [[]],
    'other language' => [[['value' => 'Abbrechen', 'locale' => 'de', 'source' => 'lang_file', 'reference' => 'file-hash']]],
    'container' => [[['value' => ['child' => 'Cancel'], 'locale' => 'en', 'source' => 'lang_file', 'reference' => 'file-hash']]],
    'conflict' => [[['value' => 'Cancel', 'locale' => 'en', 'source' => 'lang_file', 'reference' => 'file-hash'], ['value' => 'Abort', 'locale' => 'en', 'source' => 'revision', 'reference' => 'archive-A:revision-3']]],
]);

it('accepts proven empty or key-equal values without treating them as lookup failures', function (string $value) {
    $db = new BaselinePrototype;
    $db->scan('first', [baselineItem('ui.button.cancel', kind: 'key')], baselineFiles(), ['ui.button.cancel' => [['value' => $value, 'locale' => 'en', 'source' => 'lang_file', 'reference' => 'file-hash']]]);
    expect($db->rows('findings')[0]['suggested_literal'])->toBe($value);
})->with(['', 'ui.button.cancel']);

it('versions an unambiguous move and preserves its confirmed assignment', function () {
    $db = new BaselinePrototype;
    $db->scan('first', [baselineItem()], baselineFiles());
    $assignment = $db->assign('assign', 1, 'ui.button.cancel', 7, 'Use the existing action translation');
    $db->scan('move', [baselineItem(line: 5)], baselineFiles());
    $rows = $db->rows('findings');
    expect($rows)->toHaveCount(2)->and($rows[0]['status'])->toBe('obsolete')->and($rows[0]['line'])->toBe(1)
        ->and($rows[1]['line'])->toBe(5)->and($rows[1]['predecessor_id'])->toBe(1)
        ->and($rows[1]['suggested_key'])->toBe($rows[0]['suggested_key'])
        ->and($db->rows('links')[1]['key_id'])->toBe($assignment['key_id'])
        ->and(array_column($db->rows('events'), 'event_type'))->toBe(['finding_captured', 'key_assigned', 'finding_moved']);
});

it('leaves archived findings archived when an occurrence returns', function () {
    $db = new BaselinePrototype;
    $db->scan('first', [baselineItem()], baselineFiles());
    $db->scan('removed', [], baselineFiles());
    $db->scan('returned', [baselineItem()], baselineFiles());
    expect($db->rows('findings')[0]['status'])->toBe('obsolete')
        ->and($db->rows('findings')[1]['predecessor_id'])->toBeNull()
        ->and($db->rows('events'))->toHaveCount(3);
});

it('does not infer absence outside the completed scope or after failure', function () {
    $db = new BaselinePrototype;
    $db->scan('first', [baselineItem()], baselineFiles());
    $db->scan('failed', [], baselineFiles(), complete: false);
    $db->scan('other-scope', [], ['resources/views/other.blade.php' => 'other-hash']);
    expect($db->rows('findings')[0]['status'])->toBe('active')->and($db->rows('events'))->toHaveCount(1)
        ->and($db->rows('runs')[1]['status'])->toBe('failed');
});

it('keeps copied findings distinct while allowing the same key', function () {
    $db = new BaselinePrototype;
    $db->scan('first', [baselineItem('ui.button.cancel', kind: 'key')], baselineFiles());
    $db->scan('copy', [baselineItem('ui.button.cancel', kind: 'key'), baselineItem('ui.button.cancel', 4, 'key')], baselineFiles());
    expect($db->rows('findings'))->toHaveCount(2)->and($db->rows('keys'))->toHaveCount(1)
        ->and($db->rows('links'))->toHaveCount(2)->and($db->rows('findings')[1]['predecessor_id'])->toBeNull();
});

it('rolls back ambiguous moves without choosing a predecessor', function () {
    $db = new BaselinePrototype;
    $db->scan('first', [baselineItem(line: 1), baselineItem(line: 2)], baselineFiles());
    $before = $db->rows('findings');
    expect(fn () => $db->scan('ambiguous', [baselineItem(line: 4), baselineItem(line: 5)], baselineFiles()))->toThrow(RuntimeException::class, 'Ambiguous relocation');
    expect($db->rows('findings'))->toBe($before)->and($db->rows('runs'))->toHaveCount(1)->and($db->rows('events'))->toHaveCount(2);
});

it('records actual work once with actor reason and revision references', function () {
    $db = new BaselinePrototype;
    $db->scan('first', [baselineItem()], baselineFiles());
    $key = $db->assign('choose', 1, 'ui.button.cancel', 7, 'Reuse the action key')['key_id'];
    $first = $db->save('edit-1', $key, 'de', 'Abbrechen', 7, 'Initial German translation');
    expect($db->save('edit-1', $key, 'de', 'Abbrechen', 7, 'Initial German translation'))->toBe($first);
    $db->save('unchanged', $key, 'de', 'Abbrechen', 7, 'No text change');
    $db->assign('same-choice', 1, 'ui.button.cancel', 7, 'Keep selection');
    $second = $db->save('edit-2', $key, 'de', 'Abbruch', 8, 'Correct the intended wording');
    $events = $db->rows('events');
    expect($events)->toHaveCount(4)->and($db->rows('revisions'))->toHaveCount(2)
        ->and($events[3]['actor_id'])->toBe(8)->and($events[3]['reason'])->toBe('Correct the intended wording')
        ->and($events[3]['occurred_at'])->not->toBeEmpty()
        ->and(json_decode($events[3]['details'], true))->toBe(['locale' => 'de', 'previous_revision_id' => $first['revision_id'], 'revision_id' => $second['revision_id']]);
    expect(fn () => $db->save('edit-2', $key, 'de', 'Different', 8, 'Other input'))->toThrow(RuntimeException::class, 'Operation ID reused');
    expect($db->rows('events'))->toBe($events);
});

it('rolls back writes when their history cannot be stored and protects captured fields', function () {
    $db = new BaselinePrototype;
    $db->scan('first', [baselineItem()], baselineFiles());
    $key = $db->assign('choose', 1, 'ui.button.cancel', 7, 'Confirmed context')['key_id'];
    expect(fn () => $db->db->exec("UPDATE findings SET raw_literal='Invented' WHERE id=1"))->toThrow(PDOException::class);
    $db->db->exec("CREATE TRIGGER fail_history BEFORE INSERT ON events WHEN NEW.event_type='translation_saved' BEGIN SELECT RAISE(ABORT,'History unavailable'); END");
    expect(fn () => $db->save('edit', $key, 'de', 'Abbrechen', 7, 'Translate'))->toThrow(PDOException::class);
    expect($db->rows('revisions'))->toBe([])->and($db->rows('events'))->toHaveCount(2)
        ->and(array_column($db->rows('operations'), 'id'))->not->toContain('edit');
});

it('consumes the actual scanner output without keeping its guessed literal suggestion', function () {
    $root = sys_get_temp_dir().'/tw-baseline-'.bin2hex(random_bytes(6));
    $previous = base_path();
    mkdir($root.'/resources/views', 0755, true);
    $source = "{{ __('Hello :name', ['name' => \$name]) }}\n{{ __('ui.button.cancel') }}";
    file_put_contents($root.'/resources/views/example.blade.php', $source);
    try {
        app()->setBasePath($root);
        $items = app(TranslationWorkbenchScanner::class)->scan(['resources/views'])->all();
        $db = new BaselinePrototype;
        $db->scan('first', $items, ['resources/views/example.blade.php' => hash('sha256', $source)]);
        expect($db->rows('findings'))->toHaveCount(2)
            ->and($db->rows('findings')[0]['raw_literal'])->toBe('Hello :name')
            ->and($db->rows('findings')[1]['raw_source_code_literal'])->toBe('ui.button.cancel')
            ->and($db->rows('findings')[1]['suggested_literal'])->toBeNull();
    } finally {
        app()->setBasePath($previous);
        File::deleteDirectory($root);
    }
});

it('captures changed text as a new finding without inheriting an unrelated key assignment', function () {
    $db = new BaselinePrototype;
    $db->scan('first', [baselineItem()], baselineFiles());
    $db->assign('choose', 1, 'ui.button.cancel', 7, 'Cancel action');
    $db->scan('changed', [baselineItem('Save')], baselineFiles());
    $rows = $db->rows('findings');
    expect($rows[0]['raw_literal'])->toBe('Cancel')->and($rows[0]['status'])->toBe('obsolete')
        ->and($rows[1]['raw_literal'])->toBe('Save')->and($rows[1]['predecessor_id'])->toBeNull()
        ->and(array_filter($db->rows('links'), fn ($link) => $link['active'] === 1))->toBe([])
        ->and($db->rows('keys'))->toHaveCount(1);
});

it('does not duplicate a successful scan retry or overwrite its original suggestion', function () {
    $db = new BaselinePrototype;
    $item = baselineItem();
    $first = $db->scan('first', [$item], baselineFiles());
    expect($db->scan('first', [$item], baselineFiles()))->toBe($first);
    $changedRule = new DiscoveredTranslation(...[...get_object_vars($item), 'suggestedKey' => 'ui.changed.rule']);
    $db->scan('second', [$changedRule], baselineFiles());
    expect($db->rows('findings')[0]['suggested_key'])->toBe($item->suggestedKey)
        ->and($db->rows('findings'))->toHaveCount(1)->and($db->rows('events'))->toHaveCount(1)
        ->and($db->rows('runs'))->toHaveCount(2);
});

it('requires an accountable assignment and refuses archived findings', function () {
    $db = new BaselinePrototype;
    $db->scan('first', [baselineItem()], baselineFiles());
    expect(fn () => $db->assign('no-reason', 1, 'ui.button.cancel', 7, ' '))->toThrow(RuntimeException::class, 'Actor and reason');
    $db->scan('removed', [], baselineFiles());
    expect(fn () => $db->assign('archived', 1, 'ui.button.cancel', 7, 'Reuse'))->toThrow(RuntimeException::class, 'Active finding');
    expect($db->rows('keys'))->toBe([])->and($db->rows('links'))->toBe([])->and($db->rows('events'))->toHaveCount(2);
});
