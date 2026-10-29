<?php

use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Tests\Support\TranslationWorkbench\BaselinePrototype;

beforeEach(function () {
    $this->directory = sys_get_temp_dir().'/tw-evidence-'.bin2hex(random_bytes(8));
    mkdir($this->directory);
    $this->reader = new BaselineValueReader;
});

afterEach(function () {
    foreach (glob($this->directory.'/*') as $file) {
        unlink($file);
    }
    rmdir($this->directory);
});

it('reads nested source values without evaluating PHP and retains their hash provenance', function () {
    $file = $this->directory.'/ui.php';
    $content = "<?php return ['button' => ['cancel' => 'Cancel :name', 'empty' => '', 'unicode' => 'Änderung']];";
    file_put_contents($file, $content);
    $rows = $this->reader->phpFile($file, 'en', 'ui', hash('sha256', $content));
    expect(array_column($rows, 'key'))->toBe(['ui.button.cancel', 'ui.button.empty', 'ui.button.unicode'])
        ->and(array_column($rows, 'value'))->toBe(['Cancel :name', '', 'Änderung'])
        ->and($rows[0]['reference'])->toBe($file.'#sha256='.hash('sha256', $content));
    file_put_contents($file, $content.' ');
    expect(fn () => $this->reader->phpFile($file, 'en', 'ui', hash('sha256', $content)))->toThrow(RuntimeException::class, 'hash mismatch');
});

it('rejects executable source instead of running it', function () {
    $marker = $this->directory.'/executed';
    $file = $this->directory.'/ui.php';
    $content = '<?php file_put_contents('.var_export($marker, true).", 'bad'); return ['key'=>'value'];";
    file_put_contents($file, $content);
    expect(fn () => $this->reader->phpFile($file, 'en', 'ui', hash('sha256', $content)))->toThrow(RuntimeException::class)
        ->and(file_exists($marker))->toBeFalse();
});

it('rejects unsupported and ambiguous PHP payloads without silently losing values', function (string $content) {
    $file = $this->directory.'/ui.php';
    file_put_contents($file, $content);
    expect(fn () => $this->reader->phpFile($file, 'en', 'ui', hash('sha256', $content)))->toThrow(RuntimeException::class);
})->with([
    "<?php return ['x'=>'first','x'=>'second'];",
    "<?php return ['x.y'=>'first','x'=>['y'=>'second']];",
    "<?php return ['x'=>strtoupper('value')];",
    "<?php return ['x'=>false];",
]);

it('preserves conflicting archive drafts and does not inherit old timeline or approvals', function () {
    $lang = $this->directory.'/ui.php';
    $content = "<?php return ['button'=>['cancel'=>'Cancel','save'=>'Save']];";
    file_put_contents($lang, $content);
    $archive = $this->directory.'/archive.json';
    $snapshot = json_encode(['archive_id' => 'old-baseline-A', 'values' => [
        ['table' => 'translation_workbench_lang_values', 'id' => 12, 'key' => 'ui.button.cancel', 'locale' => 'en', 'value' => 'Abort', 'reviewed_at' => '2020-01-01', 'timeline_id' => 998],
        ['table' => 'translation_values', 'id' => 18, 'key' => 'ui.button.save', 'locale' => 'en', 'value' => 'Save'],
        ['table' => 'translation_values', 'id' => 19, 'key' => 'ui.button.save', 'locale' => 'de', 'value' => 'Speichern'],
    ]], JSON_THROW_ON_ERROR);
    file_put_contents($archive, $snapshot);
    $rows = [...$this->reader->phpFile($lang, 'en', 'ui', hash('sha256', $content)), ...$this->reader->archiveJson($archive, hash('sha256', $snapshot))];
    $db = new BaselinePrototype;
    $plan = $db->captureValues('baseline', $rows, 7, 'Verified initial snapshot');
    expect($plan[0]['state'])->toBe('conflict')->and($plan[0]['value'])->toBeNull()
        ->and(array_column($plan[0]['candidates'], 'value'))->toBe(['Cancel', 'Abort'])
        ->and($plan[1]['state'])->toBe('consistent')->and($plan[1]['candidates'])->toHaveCount(2)
        ->and($db->rows('baseline_observations'))->toHaveCount(5)
        ->and(array_column($db->rows('revisions'), 'value'))->toBe(['Save', 'Speichern'])
        ->and(array_column($db->rows('events'), 'event_type'))->toBe(['baseline_values_captured'])
        ->and($db->rows('events')[0]['actor_id'])->toBe(7);
    expect($db->captureValues('baseline', $rows, 7, 'Verified initial snapshot'))->toBe($plan)
        ->and($db->rows('events'))->toHaveCount(1);
    expect(fn () => $db->captureValues('repeat-other-id', $rows, 7, 'Again'))->toThrow(RuntimeException::class, 'empty value baseline');
});

it('rolls back baseline rows and revisions when the work record fails', function () {
    $db = new BaselinePrototype;
    $db->db->exec("CREATE TRIGGER fail_history BEFORE INSERT ON events BEGIN SELECT RAISE(ABORT,'no history'); END");
    expect(fn () => $db->captureValues('baseline', [['key' => 'ui.button.cancel', 'locale' => 'en', 'value' => 'Cancel', 'source' => 'lang_file', 'reference' => 'fixture#hash']], 7, 'Initial capture'))->toThrow(PDOException::class);
    expect($db->rows('baseline_observations'))->toBe([])->and($db->rows('keys'))->toBe([])->and($db->rows('revisions'))->toBe([]);
});

it('round trips option keys without relaxing ordinary publication array keys', function () {
    $source = "<?php return ['choice' => ['options' => [1 => 'One', '01' => 'Leading zero', 'a%2Eb' => 'Dot', 'a%252Eb' => 'Percent']]];";
    expect(fn () => $this->reader->treeFromBytes($source))->toThrow(RuntimeException::class);
    $tree = $this->reader->treeFromBytes($source, allowOptionKeys: true);
    expect($tree['choice']['options'])->toHaveCount(4)
        ->and($this->reader->treeFromBytes('<?php return '.var_export($tree, true).';', allowOptionKeys: true))->toBe($tree);
    foreach ([
        "<?php return ['choice' => [1 => 'One']];",
        "<?php return ['options' => [1 => 'One', '1' => 'Duplicate']];",
        "<?php return ['options' => ['a.b' => 'Unencoded dot']];",
        "<?php return ['options' => ['one' => strtoupper('one')]];",
    ] as $invalid) {
        expect(fn () => $this->reader->treeFromBytes($invalid, allowOptionKeys: true))->toThrow(RuntimeException::class);
    }
});
