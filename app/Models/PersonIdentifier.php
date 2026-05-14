<?php

// app/Models/PersonIdentifier.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'person_id',
    'issuing_country_id',
    'type',
    'value',
    'value_hash',
    'issuing_authority',
    'issued_at',
    'expires_at',
    'is_primary',
    'verified_at',
    'verified_by_user_id',
    'notes',
])]
class PersonIdentifier extends Model
{
    public const TYPE_NATIONAL_ID = 'national_id';

    public const TYPE_PASSPORT = 'passport';

    public const TYPE_TAX_ID = 'tax_id';

    public const TYPE_TAX_NUMBER = 'tax_number';

    public const TYPE_SOCIAL_SECURITY_NUMBER = 'social_security_number';

    public const TYPE_PENSION_INSURANCE_NUMBER = 'pension_insurance_number';

    public const TYPE_HEALTH_INSURANCE_NUMBER = 'health_insurance_number';

    public const TYPE_RESIDENCE_PERMIT_NUMBER = 'residence_permit_number';

    public const TYPE_OTHER = 'other';

    public const TYPES = [
        self::TYPE_NATIONAL_ID,
        self::TYPE_PASSPORT,
        self::TYPE_TAX_ID,
        self::TYPE_TAX_NUMBER,
        self::TYPE_SOCIAL_SECURITY_NUMBER,
        self::TYPE_PENSION_INSURANCE_NUMBER,
        self::TYPE_HEALTH_INSURANCE_NUMBER,
        self::TYPE_RESIDENCE_PERMIT_NUMBER,
        self::TYPE_OTHER,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
            'is_primary' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the person assigned to this identifier.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Get the country or jurisdiction that issued this identifier.
     */
    public function issuingCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'issuing_country_id');
    }

    /**
     * Get the user who verified this identifier.
     */
    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    /**
     * Get documents linked to this identifier.
     */
    public function documentRows(): HasMany
    {
        return $this->hasMany(PersonDocument::class);
    }
}
