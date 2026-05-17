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
        'classification',
        'source',
        'suggested_key',
        'native_text',
        'first_seen_at',
        'last_seen_at',
        'obsolete_at',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'obsolete_at' => 'datetime',
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
