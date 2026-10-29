<?php

use Tests\Support\TranslationWorkbench\RuntimeOptionObservation;

it('observes only eligible raw values without translating or merging null and empty categories', function () {
    $db = new PDO('sqlite::memory:');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('CREATE TABLE person_document_types (code TEXT, label TEXT, is_active BOOLEAN, sort_order INT);
        CREATE TABLE person_documents (category TEXT);
        CREATE TABLE roles (category TEXT, is_assignable BOOLEAN);
        INSERT INTO person_document_types VALUES ("id", "Original label", 1, 1), ("old", "Hidden", 0, 2);
        INSERT INTO person_documents VALUES ("a"), ("a"), (""), (NULL);
        INSERT INTO roles VALUES (NULL, 1), ("", 1), ("staff", 1), ("hidden", 0);');
    $db->beginTransaction();
    $groups = (new RuntimeOptionObservation)->collect($db);
    expect($groups['document_types']['rows'])->toBe([['value' => 'id', 'source_label' => 'Original label']])
        ->and($groups['document_categories']['rows'])->toBe([['value' => 'a']])
        ->and(array_column($groups['role_categories']['rows'], 'value'))->toBe([null, '', 'staff'])
        ->and($groups['document_types']['translation_decision'])->toBeNull()
        ->and($groups['document_types']['runtime_exhaustiveness_proven'])->toBeFalse();
    $db->rollBack();
    $db->exec('DELETE FROM person_document_types');
    $db->beginTransaction();
    expect((new RuntimeOptionObservation)->collect($db)['document_types']['state'])->toBe('observed_empty');
    $db->rollBack();
});

it('does not turn an unavailable table into an empty successful observation', function () {
    $db = new PDO('sqlite::memory:');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->beginTransaction();
    (new RuntimeOptionObservation)->collect($db);
})->throws(PDOException::class);

it('requires an explicit snapshot transaction', function () {
    (new RuntimeOptionObservation)->collect(new PDO('sqlite::memory:'));
})->throws(RuntimeException::class, 'consistent read transaction');
