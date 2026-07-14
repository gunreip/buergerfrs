<?php

// app/Models/Address.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'country_id',
    'postal_code_id',
    'locality_id',
    'street_id',
    'postal_code',
    'city',
    'street',
    'house_number',
    'address_line_2',
    'latitude',
    'longitude',
    'raw_input',
    'verified_at',
    'verified_by_user_id',
    'notes',
])]
class Address extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the country assigned to this address.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the normalized postal code reference assigned to this address.
     */
    public function postalCode(): BelongsTo
    {
        return $this->belongsTo(AddressPostalCode::class, 'postal_code_id');
    }

    /**
     * Get the normalized locality reference assigned to this address.
     */
    public function locality(): BelongsTo
    {
        return $this->belongsTo(AddressLocality::class, 'locality_id');
    }

    /**
     * Get the normalized street reference assigned to this address.
     */
    public function streetReference(): BelongsTo
    {
        return $this->belongsTo(AddressStreet::class, 'street_id');
    }

    /**
     * Get the user who verified this address.
     */
    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    /**
     * Get the person/address relationship rows.
     */
    public function personAddressRows(): HasMany
    {
        return $this->hasMany(PersonAddress::class);
    }

    /**
     * Get people assigned to this address.
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'person_addresses')
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
}
