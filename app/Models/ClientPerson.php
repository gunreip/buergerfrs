<?php

// app/Models/ClientPerson.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable([
    'client_id',
    'person_id',
    'relationship_type',
    'status',
    'is_primary',
    'starts_at',
    'ends_at',
    'verified_at',
    'verified_by_user_id',
    'created_by_user_id',
    'notes',
])]
class ClientPerson extends Pivot
{
    protected $table = 'client_person';
    public $incrementing = true;

    protected $primaryKey = 'id';

    public const RELATIONSHIP_MEMBER = 'member';
    public const RELATIONSHIP_EMPLOYEE = 'employee';
    public const RELATIONSHIP_OWNER = 'owner';
    public const RELATIONSHIP_MANAGER = 'manager';
    public const RELATIONSHIP_CONTACT_PERSON = 'contact_person';
    public const RELATIONSHIP_REPRESENTATIVE = 'representative';
    public const RELATIONSHIP_BENEFICIARY = 'beneficiary';
    public const RELATIONSHIP_EXTERNAL_PARTNER = 'external_partner';

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ENDED = 'ended';

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
     * Get the assigned client.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the assigned person.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Get the user who verified this relationship.
     */
    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    /**
     * Get the user who created this relationship.
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Check whether this relationship is verified.
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Check whether this relationship is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
