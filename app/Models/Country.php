<?php

// app/Models/Country.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'iso2',
    'iso3',
    'iso_numeric',
    'name',
    'official_name',
    'common_name',
    'native_name',
    'phone_code',
    'capital',
    'continent_code',
    'region',
    'subregion',
    'latitude',
    'longitude',
    'emoji_flag',
    'tld',
    'is_active',
    'is_independent',
    'is_eu_member',
    'is_eea_member',
    'is_schengen_member',
    'postal_code_required',
    'postal_code_regex',
    'address_format_key',
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
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->orderBy('iso2');
    }

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
            'is_independent' => 'boolean',
            'is_eu_member' => 'boolean',
            'is_eea_member' => 'boolean',
            'is_schengen_member' => 'boolean',
            'postal_code_required' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get localized names for this country.
     */
    public function names(): HasMany
    {
        return $this->hasMany(CountryName::class);
    }

    /**
     * Get subdivisions assigned to this country.
     */
    public function subdivisions(): HasMany
    {
        return $this->hasMany(CountrySubdivision::class);
    }

    /**
     * Get address formats assigned to this country.
     */
    public function addressFormats(): HasMany
    {
        return $this->hasMany(AddressFormat::class);
    }

    /**
     * Get the address format assigned to this country.
     */
    public function addressFormat(): HasOne
    {
        return $this->hasOne(AddressFormat::class);
    }

    /**
     * Get locales assigned to this country.
     */
    public function locales(): HasMany
    {
        return $this->hasMany(Locale::class);
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
     * Get postal code references assigned to this country.
     */
    public function addressPostalCodes(): HasMany
    {
        return $this->hasMany(AddressPostalCode::class);
    }

    /**
     * Get locality references assigned to this country.
     */
    public function addressLocalities(): HasMany
    {
        return $this->hasMany(AddressLocality::class);
    }

    /**
     * Get street references assigned to this country.
     */
    public function addressStreets(): HasMany
    {
        return $this->hasMany(AddressStreet::class);
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
