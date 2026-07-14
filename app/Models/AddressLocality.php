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
    'postal_code_id',
    'name',
    'normalized_name',
    'is_verified',
    'source',
])]
class AddressLocality extends Model
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
        $query->orderBy('name');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function postalCode(): BelongsTo
    {
        return $this->belongsTo(AddressPostalCode::class, 'postal_code_id');
    }

    public function streets(): HasMany
    {
        return $this->hasMany(AddressStreet::class, 'locality_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'locality_id');
    }
}
