<?php

// app/Models/PersonCorrespondence.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'person_id',
    'parent_id',
    'status',
    'type',
    'direction',
    'channel',
    'source',
    'priority',
    'subject',
    'summary',
    'external_reference',
    'document_date',
    'received_at',
    'sent_at',
    'due_at',
    'responded_at',
    'closed_at',
    'created_by_user_id',
    'assigned_to_user_id',
    'closed_by_user_id',
    'notes',
])]
class PersonCorrespondence extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_OPEN = 'open';

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_OPEN,
        self::STATUS_PENDING,
        self::STATUS_COMPLETED,
        self::STATUS_ARCHIVED,
    ];

    public const TYPE_GENERAL = 'general';

    public const TYPE_REQUEST = 'request';

    public const TYPE_NOTICE = 'notice';

    public const TYPE_APPLICATION = 'application';

    public const TYPE_DECISION = 'decision';

    public const TYPE_REMINDER = 'reminder';

    public const TYPE_OTHER = 'other';

    public const TYPES = [
        self::TYPE_GENERAL,
        self::TYPE_REQUEST,
        self::TYPE_NOTICE,
        self::TYPE_APPLICATION,
        self::TYPE_DECISION,
        self::TYPE_REMINDER,
        self::TYPE_OTHER,
    ];

    public const DIRECTION_INCOMING = 'incoming';

    public const DIRECTION_OUTGOING = 'outgoing';

    public const DIRECTION_INTERNAL = 'internal';

    public const DIRECTIONS = [
        self::DIRECTION_INCOMING,
        self::DIRECTION_OUTGOING,
        self::DIRECTION_INTERNAL,
    ];

    public const CHANNEL_LETTER = 'letter';

    public const CHANNEL_EMAIL = 'email';

    public const CHANNEL_PHONE = 'phone';

    public const CHANNEL_IN_PERSON = 'in_person';

    public const CHANNEL_APP = 'app';

    public const CHANNEL_OTHER = 'other';

    public const CHANNELS = [
        self::CHANNEL_LETTER,
        self::CHANNEL_EMAIL,
        self::CHANNEL_PHONE,
        self::CHANNEL_IN_PERSON,
        self::CHANNEL_APP,
        self::CHANNEL_OTHER,
    ];

    public const SOURCE_MANUAL = 'manual';

    public const SOURCE_GENERATED = 'generated';

    public const SOURCE_IMPORTED = 'imported';

    public const SOURCE_SCAN = 'scan';

    public const SOURCES = [
        self::SOURCE_MANUAL,
        self::SOURCE_GENERATED,
        self::SOURCE_IMPORTED,
        self::SOURCE_SCAN,
    ];

    public const PRIORITY_LOW = 'low';

    public const PRIORITY_NORMAL = 'normal';

    public const PRIORITY_HIGH = 'high';

    public const PRIORITY_URGENT = 'urgent';

    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_NORMAL,
        self::PRIORITY_HIGH,
        self::PRIORITY_URGENT,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'received_at' => 'datetime',
            'sent_at' => 'datetime',
            'due_at' => 'datetime',
            'responded_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function documentRows(): HasMany
    {
        return $this->hasMany(PersonDocument::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function assignedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }
}
