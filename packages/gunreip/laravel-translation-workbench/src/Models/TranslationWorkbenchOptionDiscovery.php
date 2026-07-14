<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationWorkbenchOptionDiscovery extends Model
{
    protected $table = 'translation_workbench_option_discoveries';

    protected $guarded = [];

    protected $casts = [
        'options' => 'array',
        'meta' => 'array',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'options_count' => 'integer',
    ];

    public function matchedEntry(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchEntry::class, 'matched_entry_id');
    }
}
