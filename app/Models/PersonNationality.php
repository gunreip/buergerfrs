<?php

// app/Models/PersonNationality.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'person_id',
    'country_id',
    'is_primary',
    'starts_at',
    'ends_at',
    'verified_at',
    'verified_by_user_id',
    'notes',
])]
class PersonNationality extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the person assigned to this nationality row.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Get the country used as nationality.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the user who verified this nationality row.
     */
    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
