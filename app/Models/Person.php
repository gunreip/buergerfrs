<?php

// app/Models/Person.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'person_number',
    'is_test_data',
    'salutation',
    'name_title',
    'gender',
    'marital_status',
    'first_name',
    'middle_name',
    'preferred_name',
    'last_name',
    'birth_name',
    'avatar_path',
    'date_of_birth',
    'birth_country_id',
    'birth_place_text',
    'phone',
    'mobile',
    'email_private',
    'email_work',
])]

class Person extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_test_data' => 'boolean',
        ];
    }

    /**
     * Get the country where this person was born.
     */
    public function birthCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'birth_country_id');
    }

    /**
     * Get the nationality relationship rows.
     */
    public function nationalityRows(): HasMany
    {
        return $this->hasMany(PersonNationality::class);
    }

    /**
     * Get the countries assigned as nationalities.
     */
    public function nationalities(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'person_nationalities')
            ->using(PersonNationality::class)
            ->withPivot([
                'id',
                'is_primary',
                'starts_at',
                'ends_at',
                'verified_at',
                'verified_by_user_id',
                'notes',
            ])
            ->withTimestamps();
    }

    /**
     * Get the language relationship rows.
     */
    public function languageRows(): HasMany
    {
        return $this->hasMany(PersonLanguage::class);
    }

    /**
     * Get the languages assigned to this person.
     */
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'person_languages')
            ->using(PersonLanguage::class)
            ->withPivot([
                'id',
                'proficiency',
                'is_native',
                'is_primary',
                'preferred_for_communication',
                'can_speak',
                'can_read',
                'can_write',
                'starts_at',
                'ends_at',
                'verified_at',
                'verified_by_user_id',
                'notes',
            ])
            ->withTimestamps();
    }

    /**
     * Get the person/address relationship rows.
     */
    public function addressRows(): HasMany
    {
        return $this->hasMany(PersonAddress::class);
    }

    /**
     * Get the addresses assigned to this person.
     */
    public function addresses(): BelongsToMany
    {
        return $this->belongsToMany(Address::class, 'person_addresses')
            ->using(PersonAddress::class)
            ->withPivot([
                'id',
                'type',
                'is_primary',
                'starts_at',
                'ends_at',
                'verified_at',
                'verified_by_user_id',
                'notes',
            ])
            ->withTimestamps();
    }

    /**
     * Get the person identifier rows.
     */
    public function identifierRows(): HasMany
    {
        return $this->hasMany(PersonIdentifier::class);
    }

    /**
     * Get the contact rows owned by this person.
     */
    public function contactRows(): HasMany
    {
        return $this->hasMany(PersonContact::class);
    }

    /**
     * Get contact rows where this person is referenced as the related person.
     */
    public function relatedContactRows(): HasMany
    {
        return $this->hasMany(PersonContact::class, 'related_person_id');
    }

    /**
     * Get the person document rows.
     */
    public function documentRows(): HasMany
    {
        return $this->hasMany(PersonDocument::class);
    }

    /**
     * Get the correspondence rows owned by this person.
     */
    public function correspondenceRows(): HasMany
    {
        return $this->hasMany(PersonCorrespondence::class);
    }

    /**
     * Get the health insurance rows assigned to this person.
     */
    public function healthInsuranceRows(): HasMany
    {
        return $this->hasMany(PersonHealthInsurance::class);
    }

    /**
     * Get the person/client relationship rows.
     */
    public function clientPeople(): HasMany
    {
        return $this->hasMany(ClientPerson::class);
    }

    /**
     * Get the clients assigned to this person.
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_person')
            ->using(ClientPerson::class)
            ->withPivot([
                'id',
                'relationship_type',
                'status',
                'is_primary',
                'starts_at',
                'ends_at',
                'verified_at',
                'verified_by_user_id',
                'created_by_user_id',
                'notes',
            ])
            ->withTimestamps();
    }

    /**
     * Get the login user assigned to this person.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * Get the person's display name.
     */
    public function displayName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
