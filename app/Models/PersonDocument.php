<?php

// app/Models/PersonDocument.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'person_id',
    'person_correspondence_id',
    'person_identifier_id',
    'issuing_country_id',
    'type',
    'status',
    'category',
    'source',
    'direction',
    'title',
    'document_number',
    'issuing_authority',
    'document_date',
    'received_at',
    'sent_at',
    'issued_at',
    'expires_at',
    'valid_from',
    'valid_until',
    'is_current',
    'replaces_document_id',
    'replaced_by_document_id',
    'file_disk',
    'file_path',
    'original_filename',
    'mime_type',
    'file_size',
    'verified_at',
    'verified_by_user_id',
    'created_by_user_id',
    'archived_at',
    'archived_reason',
    'notes',
])]
class PersonDocument extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_REPLACED = 'replaced';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUS_DRAFT = 'draft';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_EXPIRED,
        self::STATUS_REPLACED,
        self::STATUS_ARCHIVED,
        self::STATUS_DRAFT,
    ];

    public const CATEGORY_IDENTITY = 'identity';

    public const CATEGORY_INSURANCE = 'insurance';

    public const CATEGORY_TAX = 'tax';

    public const CATEGORY_RESIDENCE = 'residence';

    public const CATEGORY_CORRESPONDENCE = 'correspondence';

    public const CATEGORY_MEDICAL = 'medical';

    public const CATEGORY_OTHER = 'other';

    public const CATEGORIES = [
        self::CATEGORY_IDENTITY,
        self::CATEGORY_INSURANCE,
        self::CATEGORY_TAX,
        self::CATEGORY_RESIDENCE,
        self::CATEGORY_CORRESPONDENCE,
        self::CATEGORY_MEDICAL,
        self::CATEGORY_OTHER,
    ];

    public const SOURCE_UPLOAD = 'upload';

    public const SOURCE_SCAN = 'scan';

    public const SOURCE_GENERATED = 'generated';

    public const SOURCE_CORRESPONDENCE = 'correspondence';

    public const SOURCE_IMPORTED = 'imported';

    public const SOURCE_MANUAL = 'manual';

    public const SOURCES = [
        self::SOURCE_UPLOAD,
        self::SOURCE_SCAN,
        self::SOURCE_GENERATED,
        self::SOURCE_CORRESPONDENCE,
        self::SOURCE_IMPORTED,
        self::SOURCE_MANUAL,
    ];

    public const DIRECTION_NONE = 'none';

    public const DIRECTION_INCOMING = 'incoming';

    public const DIRECTION_OUTGOING = 'outgoing';

    public const DIRECTION_INTERNAL = 'internal';

    public const DIRECTIONS = [
        self::DIRECTION_NONE,
        self::DIRECTION_INCOMING,
        self::DIRECTION_OUTGOING,
        self::DIRECTION_INTERNAL,
    ];

    public const TYPE_ID_CARD_COPY = 'id_card_copy';

    public const TYPE_PASSPORT_COPY = 'passport_copy';

    public const TYPE_RESIDENCE_PERMIT_COPY = 'residence_permit_copy';

    public const TYPE_HEALTH_INSURANCE_PROOF = 'health_insurance_proof';

    public const TYPE_TAX_DOCUMENT = 'tax_document';

    public const TYPE_OTHER = 'other';

    public const TYPES = [
        self::TYPE_ID_CARD_COPY,
        self::TYPE_PASSPORT_COPY,
        self::TYPE_RESIDENCE_PERMIT_COPY,
        self::TYPE_HEALTH_INSURANCE_PROOF,
        self::TYPE_TAX_DOCUMENT,
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
            'document_date' => 'date',
            'issued_at' => 'date',
            'expires_at' => 'date',
            'received_at' => 'datetime',
            'sent_at' => 'datetime',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_current' => 'boolean',
            'file_size' => 'integer',
            'verified_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    /**
     * Get the person assigned to this document.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Get the correspondence this document belongs to, when it is correspondence-related.
     */
    public function personCorrespondence(): BelongsTo
    {
        return $this->belongsTo(PersonCorrespondence::class);
    }

    /**
     * Get the identifier optionally linked to this document.
     */
    public function personIdentifier(): BelongsTo
    {
        return $this->belongsTo(PersonIdentifier::class);
    }

    /**
     * Get the country or jurisdiction that issued this document.
     */
    public function issuingCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'issuing_country_id');
    }

    /**
     * Get the previous document replaced by this row.
     */
    public function replacesDocument(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replaces_document_id');
    }

    /**
     * Get the newer document that replaced this row.
     */
    public function replacedByDocument(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replaced_by_document_id');
    }

    /**
     * Get the user who verified this document.
     */
    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    /**
     * Get the user who originally added this document row.
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
