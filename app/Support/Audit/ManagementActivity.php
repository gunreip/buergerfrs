<?php

// app/Support/Audit/ManagementActivity.php

namespace App\Support\Audit;

use App\Models\Person;
use App\Models\PersonDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ManagementActivity
{
    private const LOG_NAME = 'management';

    public function personCreated(Person $person, User $user, bool $generatedPasswordLogged, ?string $sourceComponent = null): void
    {
        $this->log(
            event: 'management.person.created',
            description: __('Person created'),
            subject: $person,
            properties: [
                'person' => [
                    'id' => $person->id,
                    'person_number' => $person->person_number,
                    'first_name' => $person->first_name,
                    'last_name' => $person->last_name,
                    'date_of_birth' => $person->date_of_birth?->toDateString(),
                ],
                'user' => [
                    'id' => $user->id,
                    'person_id' => $user->person_id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'generated_password_logged' => $generatedPasswordLogged,
            ],
            sourceComponent: $sourceComponent,
        );
    }

    public function personUpdated(Person $person, array $before, array $after, ?string $sourceComponent = null): void
    {
        $this->log(
            event: 'management.person.updated',
            description: __('Person updated'),
            subject: $person,
            properties: [
                'person' => [
                    'id' => $person->id,
                    'person_number' => $person->person_number,
                    'first_name' => $person->first_name,
                    'last_name' => $person->last_name,
                    'date_of_birth' => $person->date_of_birth?->toDateString(),
                ],
                'before' => $before,
                'after' => $after,
            ],
            sourceComponent: $sourceComponent,
        );
    }

    public function personDocumentsAdded(Person $person, iterable $documents, ?string $sourceComponent = null): void
    {
        $documentRows = collect($documents)
            ->filter(fn(mixed $document): bool => $document instanceof PersonDocument)
            ->map(fn(PersonDocument $document): array => [
                'id' => $document->id,
                'type' => $this->logString($document->type),
                'category' => $this->logString($document->category),
                'status' => $this->logString($document->status),
                'source' => $this->logString($document->source),
                'title' => $this->logString($document->title),
                'document_number' => $this->logString($document->document_number),
                'file_disk' => $this->logString($document->file_disk),
                'file_path' => $this->logString($document->file_path),
                'original_filename' => $this->logString($document->original_filename),
            ])
            ->values();

        $this->log(
            event: 'management.person.document.added',
            description: $documentRows->count() === 1 ? __('Person document added') : __('Person documents added'),
            subject: $person,
            properties: [
                'person' => [
                    'id' => $person->id,
                    'person_number' => $person->person_number,
                    'first_name' => $person->first_name,
                    'last_name' => $person->last_name,
                    'date_of_birth' => $person->date_of_birth?->toDateString(),
                ],
                'documents_count' => $documentRows->count(),
                'documents' => $documentRows->all(),
            ],
            sourceComponent: $sourceComponent,
        );
    }

    private function log(string $event, string $description, ?Model $subject, array $properties, ?string $sourceComponent = null): void
    {
        activity(self::LOG_NAME)
            ->event($event)
            ->causedBy(auth()->user())
            ->performedOn($subject)
            ->withProperties(array_merge($properties, [
                'source' => [
                    'route' => request()?->route()?->getName(),
                    'url' => request()?->headers->get('referer') ?: request()?->fullUrl(),
                    'component' => $sourceComponent ?? static::class,
                ],
            ]))
            ->log($description);
    }

    private function logString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return mb_convert_encoding((string) $value, 'UTF-8', 'UTF-8');
    }
}
