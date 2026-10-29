<?php

use Tests\Support\TranslationWorkbench\ReviewCaseLedger;

beforeEach(function () {
    $this->db = new PDO('sqlite::memory:', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $this->db->exec('PRAGMA foreign_keys=ON');
    $this->db->exec(file_get_contents(__DIR__.'/../../../packages/gunreip/laravel-translation-workbench/database/generations/v2/review-cases.sql'));
    $this->ledger = new ReviewCaseLedger($this->db);
});

it('reuses cases and evidence across scans while retaining separate source identities', function () {
    $id = $this->ledger->observe('archive:table:1', 'scan1', ['value' => 'A', 'locale' => 'en'], 'different', 'Compare', true);
    $this->ledger->observe('archive:table:1', 'scan2', ['locale' => 'en', 'value' => 'A'], 'different', 'Compare', true);
    $this->ledger->observe('archive:table:1', 'scan2', ['value' => 'A', 'locale' => 'en'], 'different', 'Compare', true);
    $this->ledger->observe('archive:table:2', 'scan2', ['value' => 'A', 'locale' => 'en'], 'different', 'Compare', true);
    expect($this->ledger->cases())->toHaveCount(2)
        ->and($this->db->query('SELECT count(*) FROM workbench_review_versions')->fetchColumn())->toBe(2)
        ->and($this->db->query('SELECT count(*) FROM workbench_review_observations')->fetchColumn())->toBe(3)
        ->and($this->ledger->cases()[0]['id'])->toBe($id);
});

it('keeps decisions for unchanged evidence and requires review when evidence changes', function () {
    $id = $this->ledger->observe('subject', 'run1', ['value' => 'A'], 'different', 'Compare', true);
    $version = $this->ledger->cases()[0]['version'];
    $this->ledger->decide($id, $version, 'retain_published', 'reviewer:1', 'Published wording is correct');
    $this->ledger->observe('subject', 'run2', ['value' => 'A'], 'different', 'Compare', true);
    expect($this->ledger->cases()[0]['state'])->toBe('decided');
    $this->ledger->observe('subject', 'run3', ['value' => 'B'], 'different', 'Compare', true);
    $this->ledger->observe('subject', 'run1', ['value' => 'A'], 'different', 'Compare', true);
    expect($this->ledger->cases()[0]['state'])->toBe('needs_review')
        ->and($this->ledger->cases()[0]['decision'])->toBeNull()
        ->and($this->db->query('SELECT count(*) FROM workbench_review_decisions')->fetchColumn())->toBe(1);
    expect(fn () => $this->ledger->decide($id, $version, 'archive_only', 'reviewer:1', 'Stale screen'))->toThrow(RuntimeException::class, 'Evidence changed');
});

it('rejects changed run replays without altering the stored state', function () {
    $this->ledger->observe('subject', 'run1', ['value' => 'A'], 'different', 'Compare', true);
    expect(fn () => $this->ledger->observe('subject', 'run1', ['value' => 'B'], 'different', 'Compare', true))
        ->toThrow(RuntimeException::class, 'Run replay');
    expect(json_decode($this->ledger->cases()[0]['evidence'], true))->toBe(['value' => 'A'])
        ->and($this->db->inTransaction())->toBeFalse();
});

it('records non actionable evidence separately and requires accountable decisions', function () {
    $id = $this->ledger->observe('subject', 'run1', [], 'equal', 'No automatic import', false);
    expect($this->ledger->cases()[0]['state'])->toBe('recorded');
    $version = $this->ledger->cases()[0]['version'];
    expect(fn () => $this->ledger->decide($id, $version, 'archive_only', '', ''))->toThrow(RuntimeException::class);
    $this->ledger->decide($id, $version, 'clarify', 'reviewer:1', 'Source context needed');
    expect($this->ledger->cases()[0]['state'])->toBe('clarification');
    expect(fn () => $this->ledger->decide($id, $version, 'archive_only', 'reviewer:2', 'Replace decision'))->toThrow(RuntimeException::class, 'Decision changed');
    expect($this->ledger->cases()[0]['decision'])->toBe('clarify');
    $this->ledger->decide($id, $version, 'archive_only', 'reviewer:2', 'Clarification completed', 1);
    expect($this->ledger->cases()[0]['state'])->toBe('decided')
        ->and($this->db->query('SELECT count(*) FROM workbench_review_decisions')->fetchColumn())->toBe(2);
});
