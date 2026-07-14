<?php

use App\Models\Person;
use App\Models\PersonCorrespondence;
use App\Models\PersonDocument;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

test('person correspondence and document lifecycle schema is available', function (): void {
    expect(Schema::hasTable('person_correspondences'))->toBeTrue()
        ->and(Schema::hasColumns('person_correspondences', [
            'person_id',
            'parent_id',
            'status',
            'type',
            'direction',
            'channel',
            'source',
            'priority',
            'subject',
            'summary',
            'document_date',
            'received_at',
            'sent_at',
            'due_at',
            'responded_at',
            'closed_at',
            'created_by_user_id',
            'assigned_to_user_id',
            'closed_by_user_id',
        ]))->toBeTrue()
        ->and(Schema::hasColumns('person_documents', [
            'person_correspondence_id',
            'status',
            'category',
            'source',
            'direction',
            'document_date',
            'received_at',
            'sent_at',
            'valid_from',
            'valid_until',
            'is_current',
            'replaces_document_id',
            'replaced_by_document_id',
            'archived_at',
            'archived_reason',
            'created_by_user_id',
        ]))->toBeTrue();
});

test('person correspondence can own document attachments', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-CORR-001',
        'first_name' => 'Ada',
        'last_name' => 'Lovelace',
    ]);

    $correspondence = PersonCorrespondence::query()->create([
        'person_id' => $person->id,
        'status' => PersonCorrespondence::STATUS_OPEN,
        'type' => PersonCorrespondence::TYPE_REQUEST,
        'direction' => PersonCorrespondence::DIRECTION_INCOMING,
        'channel' => PersonCorrespondence::CHANNEL_EMAIL,
        'source' => PersonCorrespondence::SOURCE_MANUAL,
        'priority' => PersonCorrespondence::PRIORITY_NORMAL,
        'subject' => 'Missing proof request',
        'created_by_user_id' => $user->id,
        'received_at' => now(),
    ]);

    $document = PersonDocument::query()->create([
        'person_id' => $person->id,
        'person_correspondence_id' => $correspondence->id,
        'type' => PersonDocument::TYPE_OTHER,
        'status' => PersonDocument::STATUS_ACTIVE,
        'category' => PersonDocument::CATEGORY_CORRESPONDENCE,
        'source' => PersonDocument::SOURCE_CORRESPONDENCE,
        'direction' => PersonDocument::DIRECTION_INCOMING,
        'title' => 'Incoming email attachment',
        'is_current' => true,
        'created_by_user_id' => $user->id,
    ]);

    expect($person->correspondenceRows()->first()?->is($correspondence))->toBeTrue()
        ->and($correspondence->person->is($person))->toBeTrue()
        ->and($correspondence->documentRows()->first()?->is($document))->toBeTrue()
        ->and($document->personCorrespondence?->is($correspondence))->toBeTrue()
        ->and($document->createdByUser?->is($user))->toBeTrue();
});

test('person documents can model replacements without losing history', function (): void {
    $person = Person::query()->create([
        'person_number' => 'P-DOC-REPLACE-001',
        'first_name' => 'Grace',
        'last_name' => 'Hopper',
    ]);

    $oldDocument = PersonDocument::query()->create([
        'person_id' => $person->id,
        'type' => PersonDocument::TYPE_PASSPORT_COPY,
        'status' => PersonDocument::STATUS_REPLACED,
        'category' => PersonDocument::CATEGORY_IDENTITY,
        'source' => PersonDocument::SOURCE_UPLOAD,
        'direction' => PersonDocument::DIRECTION_NONE,
        'title' => 'Old passport',
        'is_current' => false,
        'valid_until' => now()->subDay()->toDateString(),
    ]);

    $newDocument = PersonDocument::query()->create([
        'person_id' => $person->id,
        'type' => PersonDocument::TYPE_PASSPORT_COPY,
        'status' => PersonDocument::STATUS_ACTIVE,
        'category' => PersonDocument::CATEGORY_IDENTITY,
        'source' => PersonDocument::SOURCE_UPLOAD,
        'direction' => PersonDocument::DIRECTION_NONE,
        'title' => 'New passport',
        'is_current' => true,
        'replaces_document_id' => $oldDocument->id,
        'valid_from' => now()->toDateString(),
    ]);

    $oldDocument->update([
        'replaced_by_document_id' => $newDocument->id,
    ]);

    expect($newDocument->replacesDocument?->is($oldDocument))->toBeTrue()
        ->and($oldDocument->refresh()->replacedByDocument?->is($newDocument))->toBeTrue()
        ->and($person->documentRows()->where('is_current', true)->first()?->is($newDocument))->toBeTrue()
        ->and($person->documentRows()->where('status', PersonDocument::STATUS_REPLACED)->first()?->is($oldDocument))->toBeTrue();
});
