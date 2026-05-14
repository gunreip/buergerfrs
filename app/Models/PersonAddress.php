<?php

// app/Models/PersonAddress.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'person_id',
    'address_id',
    'type',
    'is_primary',
    'starts_at',
    'ends_at',
    'verified_at',
    'verified_by_user_id',
    'notes',
])]
class PersonAddress extends Model
{
    public const TYPE_HOME = 'home';

    public const TYPE_PRIMARY = 'primary';

    public const TYPE_SECONDARY = 'secondary';

    public const TYPE_MAILING = 'mailing';

    public const TYPE_WORK = 'work';

    public const TYPE_OTHER = 'other';

    public const TYPES = [
        self::TYPE_HOME,
        self::TYPE_PRIMARY,
        self::TYPE_SECONDARY,
        self::TYPE_MAILING,
        self::TYPE_WORK,
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
            'is_primary' => 'boolean',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the person assigned to this address row.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Get the address assigned to this person row.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the user who verified this person/address row.
     */
    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
