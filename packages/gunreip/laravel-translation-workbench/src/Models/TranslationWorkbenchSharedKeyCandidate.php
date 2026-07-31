<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationWorkbenchSharedKeyCandidate extends Model
{
    protected $table = 'translation_workbench_shared_key_candidates';

    protected $guarded = [];

    protected $casts = [
        'matched_finding_ids' => 'array',
        'meta' => 'array',
        'matched_review_count' => 'integer',
        'matched_finding_count' => 'integer',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function finding(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchFinding::class, 'finding_id');
    }

    public function key(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchKey::class, 'key_id');
    }

    public function matchedKey(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchKey::class, 'matched_key_id');
    }
}
