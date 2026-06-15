<?php

// app/Models/TranslationKey.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationKey extends Model
{
    protected $fillable = [
        'fingerprint',
        'key',
        'namespace',
        'group',
        'status',
        'workflow_status',
        'classification',
        'source',
        'suggested_key',
        'native_text',
        'first_seen_at',
        'last_seen_at',
        'obsolete_at',
        'reviewed_at',
        'reviewed_by_user_id',
        'review_note',
        'needs_new_key_marked_at',
        'needs_new_key_marked_by_user_id',
        'needs_new_key_note',
        'needs_new_key_resolved_at',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'obsolete_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'needs_new_key_marked_at' => 'datetime',
        'needs_new_key_resolved_at' => 'datetime',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(TranslationValue::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(TranslationUsage::class);
    }
}
