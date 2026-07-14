<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationWorkbenchDuplicateCandidate extends Model
{
    protected $table = 'translation_workbench_duplicate_candidates';

    protected $guarded = [];

    protected $casts = [
        'matched_entry_ids' => 'array',
        'meta' => 'array',
        'group_size' => 'integer',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchEntry::class, 'entry_id');
    }
}
