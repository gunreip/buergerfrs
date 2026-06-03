<?php

// app/Models/TranslationValue.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationValue extends Model
{
    protected $fillable = [
        'translation_key_id',
        'locale',
        'value',
        'status',
        'source',
        'reviewed_at',
        'reviewed_by_user_id',
        'is_base_duplicate',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'is_base_duplicate' => 'boolean',
    ];

    public function translationKey(): BelongsTo
    {
        return $this->belongsTo(TranslationKey::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
