<?php

use Illuminate\Filesystem\Filesystem;
use Tests\Support\TranslationWorkbench\CurrentScanTransferInput;

beforeEach(function () {
    $this->directory = sys_get_temp_dir().'/tw-transfer-input-'.bin2hex(random_bytes(8));
    mkdir($this->directory.'/lang/en', 0700, true);
    mkdir($this->directory.'/views', 0700, true);
    mkdir($this->directory.'/scan', 0700, true);
    file_put_contents($this->directory.'/lang/en/ui.php', "<?php return ['button'=>['save'=>'Save'], 'unused'=>['value'=>'Unused']];");
    file_put_contents($this->directory.'/views/example.php', "<?php __('ui.button.save');");
    $languageHash = hash_file('sha256', $this->directory.'/lang/en/ui.php');
    $sourceHash = hash_file('sha256', $this->directory.'/views/example.php');
    $row = ['source_path' => 'views/example.php', 'source_sha256' => $sourceHash, 'source_line' => 1, 'source_column' => 7, 'source_offset' => 6,
        'raw_expression' => "__('ui.button.save')", 'raw_source_code_literal' => 'ui.button.save', 'raw_literal' => null,
        'suggested_literal' => 'Save', 'suggested_key' => null, 'found_translation_key' => 'ui.button.save',
        'source_context' => ['state' => 'confirmed_call'], 'status' => 'pending_review', 'reason' => 'key_with_exact_lang_evidence',
        'provenance' => ['suggested_literal' => ['value' => 'Save', 'locale' => 'en', 'path' => 'lang/en/ui.php', 'sha256' => $languageHash]]];
    $row['capture_id'] = hash('sha256', json_encode(['views/example.php', $sourceHash, 6], JSON_THROW_ON_ERROR));
    $this->original = $row;
    file_put_contents($this->directory.'/scan/candidates.jsonl', json_encode($row, JSON_THROW_ON_ERROR)."\n");
    $scan = ['mode' => 'current_source_scan', 'state' => 'complete', 'production_connected' => false, 'source_locale' => 'en', 'language_issues' => [],
        'candidates_sha256' => hash_file('sha256', $this->directory.'/scan/candidates.jsonl'), 'scope' => ['paths' => ['views']],
        'file_hashes' => ['views/example.php' => $sourceHash, 'lang/en/ui.php' => $languageHash], 'counts' => ['reusable_key' => 1]];
    file_put_contents($this->directory.'/scan/manifest.json', json_encode($scan, JSON_THROW_ON_ERROR));
    $this->hash = hash_file('sha256', $this->directory.'/scan/manifest.json');
});

afterEach(function () {
    (new Filesystem)->deleteDirectory($this->directory);
});

it('preserves raw findings and transfers only current key values', function () {
    $result = (new CurrentScanTransferInput)->prepare($this->directory, $this->directory.'/scan', $this->hash, $this->directory.'/out');
    expect($result['key_plan']['eligible_keys'])->toBe(1)->and($result['omitted_values'])->toBe(1)
        ->and(array_column($result['published']['values'], 'key'))->toBe(['ui.button.save']);
    $captured = json_decode(trim(file_get_contents($this->directory.'/out/candidates.jsonl')), true, flags: JSON_THROW_ON_ERROR);
    foreach (['raw_expression', 'raw_source_code_literal', 'raw_literal', 'suggested_literal', 'suggested_key', 'source_line', 'source_column'] as $field) {
        expect($captured[$field])->toBe($this->original[$field]);
    }
    expect($captured['provenance']['current_scan_capture_id'])->toBe($this->original['capture_id'])
        ->and(file_get_contents($this->directory.'/out/source/lang/en/ui.php'))->toBe(file_get_contents($this->directory.'/lang/en/ui.php'));
});

it('rejects changed current source before any database import', function () {
    file_put_contents($this->directory.'/views/example.php', '<?php // changed');
    expect(fn () => (new CurrentScanTransferInput)->prepare($this->directory, $this->directory.'/scan', $this->hash, $this->directory.'/out'))
        ->toThrow(RuntimeException::class, 'Source changed since scan');
});

it('rejects changed scan evidence', function () {
    expect(fn () => (new CurrentScanTransferInput)->prepare($this->directory, $this->directory.'/scan', str_repeat('0', 64), $this->directory.'/out'))
        ->toThrow(RuntimeException::class, 'Current scan manifest changed');
});
