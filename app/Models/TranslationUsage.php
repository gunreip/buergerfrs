<?php

// app/Models/TranslationUsage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationUsage extends Model
{
    protected $fillable = [
        'translation_key_id',
        'fingerprint',
        'file',
        'line',
        'function',
        'classification',
        'reason',
        'raw',
        'original_raw',
    ];

    public function translationKey(): BelongsTo
    {
        return $this->belongsTo(TranslationKey::class);
    }
}
