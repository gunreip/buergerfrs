<?php

// app/Support/Audit/ManagementActivity.php

namespace App\Support\Audit;

use App\Models\Person;
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
}
