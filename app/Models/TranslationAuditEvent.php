<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationAuditEvent extends Model
{
    protected $guarded = [];

    protected $casts = [
        'context' => 'array',
    ];

    public function translationKey(): BelongsTo
    {
        return $this->belongsTo(TranslationKey::class);
    }

    public function translationUsage(): BelongsTo
    {
        return $this->belongsTo(TranslationUsage::class);
    }
}
