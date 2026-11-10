<?php

use Gunreip\TranslationWorkbench\Generation\SourceOccurrenceCheck;

it('distinguishes position evidence without assigning a successor', function () {
    $check = new SourceOccurrenceCheck;
    $finding = ['raw_expression' => "__('Reason')", 'source_line' => 1, 'source_column' => 1];
    expect($check->compare($finding, "__('Reason')\n__('Reason')")['state'])->toBe('position_confirmed');
    $moved = $check->compare($finding, "prefix\n  __('Reason')");
    expect($moved['state'])->toBe('possible_move')
        ->and($moved['positions'])->toBe([['line' => 2, 'column' => 3]]);
    expect($check->compare($finding, "prefix\n__('Reason')\n__('Reason')")['state'])->toBe('ambiguous_matches')
        ->and($check->compare($finding, "__('Other')")['state'])->toBe('expression_missing');
    // Even a sole match inside a comment is only a candidate, never confirmed as a move.
    expect($check->compare($finding, "// __('Reason')")['state'])->toBe('possible_move');
});

it('handles CRLF byte positions and missing expressions', function () {
    $check = new SourceOccurrenceCheck;
    expect($check->compare(['raw_expression' => 'abc', 'source_line' => 2, 'source_column' => 3], "x\r\n  abc")['state'])->toBe('position_confirmed')
        ->and($check->compare(['raw_expression' => '', 'source_line' => 0, 'source_column' => 0], 'abc')['state'])->toBe('expression_missing');
});

it('checks a source replacement group including shared files and unavailable sources without writes', function () {
    $root = sys_get_temp_dir().'/source-group-'.bin2hex(random_bytes(6));
    mkdir($root);
    $bytes = "first\nsecond";
    file_put_contents($root.'/example.php', $bytes);
    try {
        $base = ['path' => 'example.php', 'source_line' => 1, 'source_column' => 1];
        $results = (new SourceOccurrenceCheck)->inspectGroup([
            [...$base, 'id' => 1, 'raw_expression' => 'first'],
            [...$base, 'id' => 2, 'raw_expression' => 'second'],
            [...$base, 'id' => 3, 'raw_expression' => 'missing'],
            [...$base, 'id' => 4, 'path' => 'absent.php', 'raw_expression' => 'first'],
        ], $root);
        $comparison = (new SourceOccurrenceCheck)->inspect([...$base, 'raw_expression' => 'second'], $root);
        expect($comparison['contexts'][1]['lines'][1]['match'])->toBe('second')
            ->and($comparison['contexts'][1]['lines'][1]['focus'])->toBeTrue();
        expect(array_column($results, 'state'))->toBe([
            'position_confirmed', 'possible_move', 'expression_missing', 'source_unavailable',
        ])->and(file_get_contents($root.'/example.php'))->toBe($bytes);
    } finally {
        unlink($root.'/example.php');
        rmdir($root);
    }
});
