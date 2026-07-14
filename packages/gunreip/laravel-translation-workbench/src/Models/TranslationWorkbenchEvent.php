<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationWorkbenchEvent extends Model
{
    protected $table = 'translation_workbench_events';

    protected $guarded = [];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'context' => 'array',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchEntry::class, 'entry_id');
    }
}
