<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationWorkbenchOccurrence extends Model
{
    protected $table = 'translation_workbench_occurrences';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'scan_count' => 'integer',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchEntry::class, 'entry_id');
    }
}
