<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'country_id',
    'postal_code',
    'normalized_postal_code',
    'is_verified',
    'source',
])]
class AddressPostalCode extends Model
{
    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
        ];
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('postal_code');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function localities(): HasMany
    {
        return $this->hasMany(AddressLocality::class, 'postal_code_id');
    }

    public function streets(): HasMany
    {
        return $this->hasMany(AddressStreet::class, 'postal_code_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'postal_code_id');
    }
}
