<?php

// app/Models/PersonLanguage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'person_id',
    'language_id',
    'proficiency',
    'is_native',
    'is_primary',
    'preferred_for_communication',
    'can_speak',
    'can_read',
    'can_write',
    'starts_at',
    'ends_at',
    'verified_at',
    'verified_by_user_id',
    'notes',
])]
class PersonLanguage extends Model
{
    public const PROFICIENCY_NATIVE = 'native';

    public const PROFICIENCY_FLUENT = 'fluent';

    public const PROFICIENCY_INTERMEDIATE = 'intermediate';

    public const PROFICIENCY_BASIC = 'basic';

    public const PROFICIENCY_UNKNOWN = 'unknown';

    public const PROFICIENCIES = [
        self::PROFICIENCY_NATIVE,
        self::PROFICIENCY_FLUENT,
        self::PROFICIENCY_INTERMEDIATE,
        self::PROFICIENCY_BASIC,
        self::PROFICIENCY_UNKNOWN,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_native' => 'boolean',
            'is_primary' => 'boolean',
            'preferred_for_communication' => 'boolean',
            'can_speak' => 'boolean',
            'can_read' => 'boolean',
            'can_write' => 'boolean',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the person assigned to this language row.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Get the language assigned to this person.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the user who verified this language row.
     */
    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
