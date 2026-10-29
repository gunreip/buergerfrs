<?php

use Gunreip\TranslationWorkbench\Support\BaselineCorrectionPreflight;

function correctionPreflightFixture(): array
{
    $root = sys_get_temp_dir().'/joint-preflight-'.bin2hex(random_bytes(8));
    mkdir($root, 0700);
    $source = "{{ __('ui.type') }}";
    $language = "<?php return ['type' => 'Type'];";
    file_put_contents($root.'/source.blade.php', $source);
    file_put_contents($root.'/language.php', $language);
    $published = ['full_name' => 'ui.type', 'locale' => 'en', 'path' => 'language.php',
        'value' => 'Type', 'state' => 'blocked_structure', 'sha256' => hash('sha256', $language)];
    $snapshot = ['baseline_hash' => 'baseline', 'items' => ['ui.type' => [
        'locales' => ['en' => ['published' => $published]],
        'blocked_sources' => [['finding_id' => 42, 'path' => 'source.blade.php', 'line' => 1,
            'column' => 4, 'expression' => "__('ui.type')", 'sha256' => hash('sha256', $source)]],
    ]]];
    $evidence = ['baseline_hash' => 'baseline', 'source' => 'ui.type', 'target' => 'ui.field.type',
        'sources' => [['id' => 42, 'source_path' => 'source.blade.php', 'source_line' => 1, 'source_column' => 4,
            'raw_expression' => "__('ui.type')", 'raw_source_code_literal' => 'ui.type', 'status' => 'active']],
        'published_values' => [$published]];

    return [$root, $snapshot, $evidence];
}

function removeCorrectionPreflightFixture(string $root): void
{
    unlink($root.'/source.blade.php');
    unlink($root.'/language.php');
    rmdir($root);
}

it('binds both checked contexts deterministically without changing the files', function () {
    [$root, $snapshot, $evidence] = correctionPreflightFixture();
    $before = [hash_file('sha256', $root.'/source.blade.php'), hash_file('sha256', $root.'/language.php')];
    $reads = 0;
    $reader = function () use (&$reads, &$evidence): array {
        $reads++;

        return ['evidence' => $evidence, 'fingerprint' => hash('sha256', json_encode($evidence, JSON_THROW_ON_ERROR))];
    };
    try {
        $service = new BaselineCorrectionPreflight;
        $result = $service->inspect($snapshot, 'ui.type', 'ui.field.type', $root, $reader);
        expect($result['status'])->toBe('verified')->and($reads)->toBe(2)
            ->and($result['source_occurrences'])->toBe(1)->and($result['source_files'])->toBe(1)
            ->and($result['published_values'])->toBe(1)
            ->and($service->inspect($snapshot, 'ui.type', 'ui.field.type', $root, $reader)['fingerprint'])->toBe($result['fingerprint']);
        $evidence['target'] = 'ui.other.type';
        expect($service->inspect($snapshot, 'ui.type', 'ui.other.type', $root, $reader)['fingerprint'])->not->toBe($result['fingerprint'])
            ->and([hash_file('sha256', $root.'/source.blade.php'), hash_file('sha256', $root.'/language.php')])->toBe($before);
    } finally {
        removeCorrectionPreflightFixture($root);
    }
});

it('rejects a change between the file and database checks', function (string $change) {
    [$root, $snapshot, $evidence] = correctionPreflightFixture();
    $reads = 0;
    $reader = function () use ($root, $change, &$evidence, &$reads): array {
        $reads++;
        if ($change === 'source') {
            file_put_contents($root.'/source.blade.php', 'changed source');
        } elseif ($change === 'language') {
            file_put_contents($root.'/language.php', 'changed language');
        } elseif ($change === 'database' && $reads === 2) {
            $evidence['sources'][0]['capture_id'] = 'new capture evidence';
        } elseif ($change === 'target') {
            $evidence['target'] = 'ui.other.type';
        } elseif ($change === 'baseline') {
            $evidence['baseline_hash'] = 'another generation';
        }

        return ['evidence' => $evidence, 'fingerprint' => hash('sha256', json_encode($evidence, JSON_THROW_ON_ERROR))];
    };
    try {
        expect(fn () => (new BaselineCorrectionPreflight)->inspect($snapshot, 'ui.type', 'ui.field.type', $root, $reader))
            ->toThrow(RuntimeException::class);
    } finally {
        removeCorrectionPreflightFixture($root);
    }
})->with(['source', 'language', 'database', 'target', 'baseline']);

it('does not query the database when the source proposal is already blocked', function () {
    [$root, $snapshot] = correctionPreflightFixture();
    file_put_contents($root.'/source.blade.php', 'changed before checking');
    $reads = 0;
    try {
        expect(fn () => (new BaselineCorrectionPreflight)->inspect($snapshot, 'ui.type', 'ui.field.type', $root,
            function () use (&$reads): array {
                $reads++;

                return [];
            }))->toThrow(RuntimeException::class);
        expect($reads)->toBe(0);
    } finally {
        removeCorrectionPreflightFixture($root);
    }
});
