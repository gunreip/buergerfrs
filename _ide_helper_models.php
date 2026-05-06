<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string|null $client_number
 * @property string $name
 * @property string|null $legal_name
 * @property string|null $type
 * @property string $status
 * @property string|null $description
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientPerson> $clientPeople
 * @property-read int|null $client_people_count
 * @property-read \App\Models\ClientPerson|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $people
 * @property-read int|null $people_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereClientNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereLegalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedAt($value)
 */
	class Client extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $client_id
 * @property int $person_id
 * @property string $relationship_type
 * @property string $status
 * @property bool $is_primary
 * @property \Carbon\CarbonImmutable|null $starts_at
 * @property \Carbon\CarbonImmutable|null $ends_at
 * @property \Carbon\CarbonImmutable|null $verified_at
 * @property int|null $verified_by_user_id
 * @property int|null $created_by_user_id
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\User|null $createdByUser
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\User|null $verifiedByUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson whereCreatedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson whereRelationshipType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientPerson whereVerifiedByUserId($value)
 */
	class ClientPerson extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $person_number
 * @property string $first_name
 * @property string $last_name
 * @property \Carbon\CarbonImmutable|null $date_of_birth
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientPerson> $clientPeople
 * @property-read int|null $client_people_count
 * @property-read \App\Models\ClientPerson|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Client> $clients
 * @property-read int|null $clients_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person wherePersonNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereUpdatedAt($value)
 */
	class Person extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property array<array-key, mixed>|null $settings
 * @property int|null $person_id
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Person|null $person
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $teams
 * @property-read int|null $teams_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, ?string $guard = null, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User team($teams, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, ?string $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTeam($teams)
 */
	class User extends \Eloquent {}
}

