<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\TestFailureDetails;
use Tests\TestCase;

uses(TestCase::class);

it('summarizes failures and errors without successful checks or stack traces', function () {
    $checks = [
        ['name' => 'Successful check', 'status' => 'passed'],
        ['name' => 'Pest group', 'status' => 'failed', 'parsed_output' => [
            'failures' => [['test' => 'DefaultsTest', 'file' => 'test.php', 'line' => 12, 'message' => "Missing key\nStack trace:\n#0 irrelevant"]],
            'error_details' => [['test' => 'ViewTest', 'message' => '<script>example</script>']],
        ]],
        ['name' => 'Git whitespace', 'status' => 'failed', 'output' => "file.php:2 trailing whitespace\nextra output"],
    ];
    $summary = TestFailureDetails::summary($checks);
    expect($summary)->toContain('DefaultsTest', 'ViewTest', 'test.php:12', 'Missing key', 'Git whitespace', 'trailing whitespace')
        ->not->toContain('Successful check', 'Stack trace', 'irrelevant', 'extra output');
    $html = view('translation-workbench::pages.tw-graph.partials.fail-summary', compact('checks'))->render();
    expect($html)->toContain('Fail Summary', 'Copy fail summary', 'navigator.clipboard.writeText', 'x-ref="failSummary"', '&lt;script&gt;example&lt;/script&gt;')
        ->not->toContain('<script>example</script>', 'Successful check');
});

it('shows an empty summary without a copy button when no checks failed', function () {
    $html = view('translation-workbench::pages.tw-graph.partials.fail-summary', ['checks' => [['status' => 'passed']]])->render();
    expect($html)->toContain('No failed checks in this report.')
        ->not->toContain('Copy fail summary');
});

it('extracts each failed test from Artisan console output instead of the initial PASS line', function () {
    $output = "\033[32m PASS \033[0m Tests\\PassingTest\n  ✓ passed test\n"
        ."  FAILED  Tests\\BoundsTest > it checks controls\n  Expected: long view output\n  To contain: Collisions only\n  at tests/BoundsTest.php:16\n"
        ."  FAILED  Tests\\RekeyTest > it checks labels\n  Failed asserting that two arrays are identical.\n  at tests/RekeyTest.php:95\n"
        ."  Tests: 2 failed, 1 passed\n";
    $check = ['name' => 'Assembly', 'status' => 'failed', 'output' => $output, 'parsed_output' => ['tests' => 3]];
    $details = TestFailureDetails::fromCheck($check);
    expect($details)->toHaveCount(2)
        ->and($details[0]['location'])->toBe('tests/BoundsTest.php:16')
        ->and($details[0]['message'])->toBe('To contain: Collisions only');
    expect(TestFailureDetails::summary([$check]))->toContain('BoundsTest', 'RekeyTest', 'two arrays are identical')
        ->not->toContain('PassingTest', 'PASS', 'Expected: long view output');
});
