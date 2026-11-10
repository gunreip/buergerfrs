<?php

use Gunreip\TranslationWorkbench\Generation\CurrentLanguageEvidence;

beforeEach(function () {
    $this->root = sys_get_temp_dir().'/current-evidence-'.bin2hex(random_bytes(8));
    mkdir($this->root.'/lang/en', 0700, true);
    $this->path = $this->root.'/lang/en/ui.php';
    $this->row = ['full_name' => 'ui.states.all', 'value' => 'All', 'path' => 'lang/en/ui.php', 'sha256' => 'historical-hash'];
});
afterEach(function () {
    if (is_file($this->path)) unlink($this->path);
    rmdir($this->root.'/lang/en'); rmdir($this->root.'/lang'); rmdir($this->root);
});
it('accepts an unchanged entry in a changed file and records the current bytes hash', function () {
    $bytes = "<?php return ['states' => ['all' => 'All'], 'other' => 'New'];";
    file_put_contents($this->path, $bytes);
    $result = (new CurrentLanguageEvidence)->resolve([$this->row], $this->root);
    expect($result['ui.states.all']['value'])->toBe('All')
        ->and($result['ui.states.all']['provenance']['sha256'])->toBe(hash('sha256', $bytes));
});
it('rejects missing changed dynamic and ambiguous values', function () {
    $reader = new CurrentLanguageEvidence;
    expect($reader->resolve([$this->row], $this->root))->toBe([]);
    foreach ([
        "<?php return ['states' => ['all' => 'Everything']];",
        "<?php return ['states' => []];",
        "<?php return ['states' => ['all' => strtoupper('all')]];",
        "<?php return ['states' => ['all' => 'All', 'all' => 'All']];",
        "<?php return [",
    ] as $bytes) {
        file_put_contents($this->path, $bytes);
        expect($reader->resolve([$this->row], $this->root))->toBe([]);
    }
});
it('deduplicates the same verified file entry but rejects escaping paths', function () {
    file_put_contents($this->path, "<?php return ['states' => ['all' => 'All']];");
    $reader = new CurrentLanguageEvidence;
    expect($reader->resolve([$this->row, $this->row], $this->root))->toHaveKey('ui.states.all')
        ->and($reader->resolve([array_replace($this->row, ['path' => '../ui.php'])], $this->root))->toBe([])
        ->and($reader->resolve([array_replace($this->row, ['path' => $this->path])], $this->root))->toBe([]);
});

it('rejects the same key verified in different files', function () {
    mkdir($this->root.'/other');
    $other = $this->root.'/other/ui.php';
    $bytes = "<?php return ['states' => ['all' => 'All']];";
    file_put_contents($this->path, $bytes);
    file_put_contents($other, $bytes);
    try {
        expect((new CurrentLanguageEvidence)->resolve([$this->row, array_replace($this->row, ['path' => 'other/ui.php'])], $this->root))->toBe([]);
    } finally {
        unlink($other);
        rmdir($this->root.'/other');
    }
});
