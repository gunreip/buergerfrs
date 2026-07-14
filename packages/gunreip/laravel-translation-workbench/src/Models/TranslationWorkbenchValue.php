<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationWorkbenchValue extends Model
{
    protected $table = 'translation_workbench_values';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchEntry::class, 'entry_id');
    }
}
