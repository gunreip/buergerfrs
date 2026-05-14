<?php

// app/Models/PersonDocument.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'person_id',
    'person_identifier_id',
    'issuing_country_id',
    'type',
    'title',
    'document_number',
    'issuing_authority',
    'issued_at',
    'expires_at',
    'file_disk',
    'file_path',
    'original_filename',
    'mime_type',
    'file_size',
    'verified_at',
    'verified_by_user_id',
    'notes',
])]
class PersonDocument extends Model
{
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
            'issued_at' => 'date',
            'expires_at' => 'date',
            'file_size' => 'integer',
            'verified_at' => 'datetime',
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
     * Get the user who verified this document.
     */
    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
