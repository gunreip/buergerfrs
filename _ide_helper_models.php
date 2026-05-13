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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FallbackReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FallbackReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FallbackReport query()
 */
	class FallbackReport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $country_id
 * @property string $type
 * @property string $name
 * @property string|null $short_name
 * @property string|null $code
 * @property string|null $website
 * @property string|null $phone
 * @property string|null $email
 * @property bool $is_active
 * @property int $sort_order
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Country|null $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PersonHealthInsurance> $personHealthInsurances
 * @property-read int|null $person_health_insurances_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InsuranceProvider whereWebsite($value)
 */
	class InsuranceProvider extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $person_id
 * @property int $address_id
 * @property string $type
 * @property bool $is_primary
 * @property \Carbon\CarbonImmutable|null $starts_at
 * @property \Carbon\CarbonImmutable|null $ends_at
 * @property \Carbon\CarbonImmutable|null $verified_at
 * @property int|null $verified_by_user_id
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Address $address
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\User|null $verifiedByUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonAddress whereVerifiedByUserId($value)
 */
	class PersonAddress extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $person_id
 * @property int|null $related_person_id
 * @property string $type
 * @property string|null $relationship
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $email
 * @property bool $is_primary
 * @property bool $is_emergency_contact
 * @property bool $is_authorized_representative
 * @property \Carbon\CarbonImmutable|null $verified_at
 * @property int|null $verified_by_user_id
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\Person|null $relatedPerson
 * @property-read \App\Models\User|null $verifiedByUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereIsAuthorizedRepresentative($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereIsEmergencyContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereRelatedPersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereRelationship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonContact whereVerifiedByUserId($value)
 */
	class PersonContact extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $person_id
 * @property int|null $person_identifier_id
 * @property int|null $issuing_country_id
 * @property string $type
 * @property string|null $title
 * @property string|null $document_number
 * @property string|null $issuing_authority
 * @property \Carbon\CarbonImmutable|null $issued_at
 * @property \Carbon\CarbonImmutable|null $expires_at
 * @property string|null $file_disk
 * @property string|null $file_path
 * @property string|null $original_filename
 * @property string|null $mime_type
 * @property int|null $file_size
 * @property \Carbon\CarbonImmutable|null $verified_at
 * @property int|null $verified_by_user_id
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Country|null $issuingCountry
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\PersonIdentifier|null $personIdentifier
 * @property-read \App\Models\User|null $verifiedByUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereDocumentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereFileDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereIssuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereIssuingAuthority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereIssuingCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereOriginalFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument wherePersonIdentifierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonDocument whereVerifiedByUserId($value)
 */
	class PersonDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $person_id
 * @property int|null $insurance_provider_id
 * @property string|null $insurance_number
 * @property \Carbon\CarbonImmutable|null $starts_at
 * @property \Carbon\CarbonImmutable|null $ends_at
 * @property bool $is_primary
 * @property \Carbon\CarbonImmutable|null $verified_at
 * @property int|null $verified_by_user_id
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\InsuranceProvider|null $insuranceProvider
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\User|null $verifiedByUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance whereInsuranceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance whereInsuranceProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonHealthInsurance whereVerifiedByUserId($value)
 */
	class PersonHealthInsurance extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $person_id
 * @property int|null $issuing_country_id
 * @property string $type
 * @property string $value
 * @property string|null $value_hash
 * @property string|null $issuing_authority
 * @property \Carbon\CarbonImmutable|null $issued_at
 * @property \Carbon\CarbonImmutable|null $expires_at
 * @property bool $is_primary
 * @property \Carbon\CarbonImmutable|null $verified_at
 * @property int|null $verified_by_user_id
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PersonDocument> $documentRows
 * @property-read int|null $document_rows_count
 * @property-read \App\Models\Country|null $issuingCountry
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\User|null $verifiedByUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereIssuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereIssuingAuthority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereIssuingCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereValueHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonIdentifier whereVerifiedByUserId($value)
 */
	class PersonIdentifier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $person_id
 * @property int $language_id
 * @property string $proficiency
 * @property bool $is_native
 * @property bool $is_primary
 * @property bool $preferred_for_communication
 * @property \Carbon\CarbonImmutable|null $starts_at
 * @property \Carbon\CarbonImmutable|null $ends_at
 * @property \Carbon\CarbonImmutable|null $verified_at
 * @property int|null $verified_by_user_id
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Language $language
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\User|null $verifiedByUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage whereIsNative($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage wherePreferredForCommunication($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage whereProficiency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonLanguage whereVerifiedByUserId($value)
 */
	class PersonLanguage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $person_id
 * @property int $country_id
 * @property bool $is_primary
 * @property \Carbon\CarbonImmutable|null $starts_at
 * @property \Carbon\CarbonImmutable|null $ends_at
 * @property \Carbon\CarbonImmutable|null $verified_at
 * @property int|null $verified_by_user_id
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Country $country
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\User|null $verifiedByUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonNationality whereVerifiedByUserId($value)
 */
	class PersonNationality extends \Eloquent {}
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

