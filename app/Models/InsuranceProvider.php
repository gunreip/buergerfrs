<?php

// app/Models/InsuranceProvider.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'country_id',
    'type',
    'name',
    'short_name',
    'code',
    'website',
    'phone',
    'email',
    'is_active',
    'sort_order',
])]
class InsuranceProvider extends Model
{
    public const TYPE_HEALTH = 'health';
    public const TYPE_OTHER = 'other';

    public const TYPES = [
        self::TYPE_HEALTH,
        self::TYPE_OTHER,
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

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

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function personHealthInsurances(): HasMany
    {
        return $this->hasMany(PersonHealthInsurance::class);
    }
}
