<?php

// app/Models/Country.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'iso2',
    'iso3',
    'name',
    'native_name',
    'phone_code',
    'is_active',
    'sort_order',
])]
class Country extends Model
{
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * Get the nationality relationship rows using this country.
     */
    public function nationalityRows(): HasMany
    {
        return $this->hasMany(PersonNationality::class);
    }

    /**
     * Get addresses assigned to this country.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get insurance providers assigned to this country.
     */
    public function insuranceProviders(): HasMany
    {
        return $this->hasMany(InsuranceProvider::class);
    }

    /**
     * Get people assigned to this country as nationality.
     */
    public function nationalityPeople(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'person_nationalities')
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
}
