<?php

// app/Models/PersonContact.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'person_id',
    'related_person_id',
    'type',
    'relationship',
    'name',
    'phone',
    'email',
    'is_primary',
    'is_emergency_contact',
    'is_authorized_representative',
    'verified_at',
    'verified_by_user_id',
    'notes',
])]
class PersonContact extends Model
{
    public const TYPE_EMERGENCY = 'emergency';

    public const TYPE_FAMILY = 'family';

    public const TYPE_CAREGIVER = 'caregiver';

    public const TYPE_GUARDIAN = 'guardian';

    public const TYPE_AUTHORIZED_REPRESENTATIVE = 'authorized_representative';

    public const TYPE_OTHER = 'other';

    public const TYPES = [
        self::TYPE_EMERGENCY,
        self::TYPE_FAMILY,
        self::TYPE_CAREGIVER,
        self::TYPE_GUARDIAN,
        self::TYPE_AUTHORIZED_REPRESENTATIVE,
        self::TYPE_OTHER,
    ];

    public const RELATIONSHIP_PARENT = 'parent';

    public const RELATIONSHIP_CHILD = 'child';

    public const RELATIONSHIP_SPOUSE = 'spouse';

    public const RELATIONSHIP_PARTNER = 'partner';

    public const RELATIONSHIP_SIBLING = 'sibling';

    public const RELATIONSHIP_GUARDIAN = 'guardian';

    public const RELATIONSHIP_CAREGIVER = 'caregiver';

    public const RELATIONSHIP_FRIEND = 'friend';

    public const RELATIONSHIP_OTHER = 'other';

    public const RELATIONSHIPS = [
        self::RELATIONSHIP_PARENT,
        self::RELATIONSHIP_CHILD,
        self::RELATIONSHIP_SPOUSE,
        self::RELATIONSHIP_PARTNER,
        self::RELATIONSHIP_SIBLING,
        self::RELATIONSHIP_GUARDIAN,
        self::RELATIONSHIP_CAREGIVER,
        self::RELATIONSHIP_FRIEND,
        self::RELATIONSHIP_OTHER,
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
            'is_emergency_contact' => 'boolean',
            'is_authorized_representative' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the person this contact belongs to.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Get the related person, when this contact points to an existing person record.
     */
    public function relatedPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'related_person_id');
    }

    /**
     * Get the user who verified this contact.
     */
    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
